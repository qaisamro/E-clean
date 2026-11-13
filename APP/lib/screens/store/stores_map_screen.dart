import 'package:laundry_customer/providers/city_providers.dart';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:latlong2/latlong.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/repos/vendor_repo.dart';
import 'package:laundry_customer/screens/store/vendor_store_screen.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';
import 'package:url_launcher/url_launcher.dart';

final mapVendorsProvider = FutureProvider.autoDispose<List<Vendor>>(
    (ref) => VendorRepo().getMapVendors());

/// Same name/address matching used by the home screen.
bool vendorMatchesArea(Vendor vendor, String area) =>
    area == 'الكل' ||
    (vendor.cityMetadata
        ? vendor.city == area
        : (vendor.displayAddress.toLowerCase().contains(area.toLowerCase()) ||
            vendor.displayName.toLowerCase().contains(area.toLowerCase())));

Route<void> vendorMapRoute(Vendor vendor) =>
    MaterialPageRoute<void>(builder: (_) => VendorStoreScreen(vendor: vendor));

class StoresMapScreen extends ConsumerStatefulWidget {
  const StoresMapScreen({super.key, this.tileProvider});
  final TileProvider? tileProvider;
  @override
  ConsumerState<StoresMapScreen> createState() => _StoresMapState();
}

class _StoresMapState extends ConsumerState<StoresMapScreen> {
  bool _all = false;
  bool _tileError = false;
  Vendor? _selected;

  @override
  Widget build(BuildContext context) {
    final area = ref.watch(selectedVendorAreaProvider);
    final cityId = ref.watch(currentCityIdProvider);
    final data = ref.watch(mapVendorsProvider);
    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: const Color(0xFFFAF7F2),
        appBar: AppBar(title: const Text('خريطة المتاجر')),
        body: SafeArea(
            child: Column(children: [
          Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(children: [
                Expanded(child: Text(_all ? 'جميع المدن' : 'المدينة: $area')),
                if (area != 'الكل')
                  FilterChip(
                    label: const Text('كل المتاجر'),
                    selected: _all,
                    onSelected: (value) => setState(() {
                      _all = value;
                      _selected = null;
                    }),
                  ),
                IconButton(
                    tooltip: 'تحديث المتاجر',
                    icon: const Icon(Icons.refresh),
                    onPressed: () {
                      setState(() => _selected = null);
                      ref.invalidate(mapVendorsProvider);
                    }),
              ])),
          Expanded(
              child: data.when(
            loading: () => const Center(
                child:
                    LoadingWidget(size: 110, message: 'جاري تحميل المتاجر...')),
            error: (_, __) => Center(
                child: Column(mainAxisSize: MainAxisSize.min, children: [
              const Text('تعذر تحميل المتاجر. تحقق من الإنترنت.'),
              TextButton(
                  onPressed: () => ref.invalidate(mapVendorsProvider),
                  child: const Text('إعادة المحاولة')),
            ])),
            data: (vendors) {
              final matches = vendors
                  .where((v) =>
                      _all ||
                      (cityId != null
                          ? v.cityId == cityId
                          : vendorMatchesArea(v, area)))
                  .toList();
              final located = matches.where((v) => v.hasMapLocation).toList();
              if (located.isEmpty)
                return Center(
                    child: Padding(
                        padding: const EdgeInsets.all(24),
                        child: Text(
                            matches.isEmpty
                                ? 'لا توجد متاجر في المدينة المختارة.'
                                : 'لا توجد مواقع متاجر صالحة للعرض حاليًا.',
                            textAlign: TextAlign.center)));
              final points = located
                  .map((v) => LatLng(v.latitude!, v.longitude!))
                  .toList();
              final selected =
                  located.where((v) => v.id == _selected?.id).firstOrNull;
              return Column(children: [
                if (_tileError)
                  const Text('تعذر تحميل بعض أجزاء الخريطة. تحقق من الإنترنت.'),
                Expanded(
                    child: FlutterMap(
                  key: ValueKey(
                      '${_all ? 'all' : area}:${located.map((v) => '${v.id}:${v.latitude}:${v.longitude}').join(';')}'),
                  options: MapOptions(
                    initialCenter: points.first,
                    initialZoom: 15,
                    initialCameraFit: CameraFit.bounds(
                        bounds: LatLngBounds.fromPoints(points),
                        padding: const EdgeInsets.all(65),
                        maxZoom: 16),
                  ),
                  children: [
                    TileLayer(
                      tileProvider: widget.tileProvider,
                      urlTemplate: const String.fromEnvironment('MAP_TILE_URL',
                          defaultValue:
                              'https://tile.openstreetmap.org/{z}/{x}/{y}.png'),
                      userAgentPackageName: 'com.elitecleanps.laundry',
                      maxNativeZoom: 19,
                      panBuffer: 0,
                      errorTileCallback: (_, __, ___) {
                        if (mounted && !_tileError)
                          WidgetsBinding.instance.addPostFrameCallback((_) {
                            if (mounted) setState(() => _tileError = true);
                          });
                      },
                    ),
                    MarkerLayer(
                        markers: located
                            .map((v) => Marker(
                                  point: LatLng(v.latitude!, v.longitude!),
                                  width: 160,
                                  height: 78,
                                  child: Column(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        Material(
                                            color: Colors.white,
                                            borderRadius:
                                                BorderRadius.circular(8),
                                            child: InkWell(
                                                key: ValueKey(
                                                    'vendor-name-${v.id}'),
                                                onTap: () => Navigator.of(context)
                                                    .push(vendorMapRoute(v)),
                                                child: Padding(
                                                    padding:
                                                        const EdgeInsets.all(6),
                                                    child: Text(v.displayName,
                                                        maxLines: 1,
                                                        overflow: TextOverflow
                                                            .ellipsis,
                                                        style: const TextStyle(
                                                            color: Color(
                                                                0xFF028090),
                                                            fontWeight:
                                                                FontWeight
                                                                    .bold))))),
                                        InkWell(
                                            key: ValueKey('vendor-pin-${v.id}'),
                                            onTap: () =>
                                                setState(() => _selected = v),
                                            child: const Icon(Icons.location_on,
                                                size: 34,
                                                color: Color(0xFF028090))),
                                      ]),
                                ))
                            .toList()),
                    RichAttributionWidget(attributions: [
                      TextSourceAttribution('OpenStreetMap contributors',
                          onTap: () => launchUrl(Uri.parse(
                              'https://www.openstreetmap.org/copyright')))
                    ]),
                  ],
                )),
                if (selected != null)
                  ListTile(
                      title: Text(selected.displayName),
                      subtitle: const Text('اضغط لفتح المتجر'),
                      trailing: const Icon(Icons.chevron_left),
                      onTap: () =>
                          Navigator.of(context).push(vendorMapRoute(selected))),
              ]);
            },
          )),
        ])),
      ),
    );
  }
}
