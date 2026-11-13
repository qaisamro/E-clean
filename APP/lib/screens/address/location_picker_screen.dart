import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geolocator/geolocator.dart';
import 'package:latlong2/latlong.dart';
import 'package:laundry_customer/services/location_service.dart';
import 'package:url_launcher/url_launcher.dart';

class LocationPickerScreen extends ConsumerStatefulWidget {
  const LocationPickerScreen(
      {super.key,
      this.initial,
      this.locateOnOpen = false,
      this.readOnly = false,
      this.tileProvider});
  final PickedLocation? initial;
  final bool locateOnOpen;
  final bool readOnly;
  final TileProvider? tileProvider;
  @override
  ConsumerState<LocationPickerScreen> createState() => _LocationPickerState();
}

class _LocationPickerState extends ConsumerState<LocationPickerScreen> {
  final _map = MapController();
  LatLng? _point;
  bool _locating = false;
  bool _confirming = false;
  bool _tilesFailed = false;
  bool _approximate = false;
  LocationFailure? _failure;
  int _revision = 0;

  @override
  void initState() {
    super.initState();
    final initial = widget.initial;
    if (initial != null && initial.valid) {
      _point = LatLng(initial.latitude, initial.longitude);
      _approximate = initial.approximate;
    }
    if (widget.locateOnOpen && !widget.readOnly) {
      WidgetsBinding.instance.addPostFrameCallback((_) => _locate(false));
    }
  }

  @override
  void dispose() {
    _map.dispose();
    super.dispose();
  }

  void _pick(LatLng point) {
    if (widget.readOnly ||
        _confirming ||
        !validCoordinates(point.latitude, point.longitude)) return;
    _revision++;
    setState(() {
      _point = point;
      _approximate = false;
    });
  }

  Future<void> _locate(bool explicit) async {
    if (_locating || _confirming) return;
    if (explicit &&
        await ref.read(locationServiceProvider).device.permission() ==
            LocationPermission.denied) {
      if (!mounted) return;
      final agreed = await showDialog<bool>(
          context: context,
          builder: (context) => Directionality(
              textDirection: TextDirection.rtl,
              child: AlertDialog(
                title: const Text('تحديد موقعك الحالي'),
                content: const Text(
                    'نستخدم موقعك أثناء اختيار عنوان الاستلام والتوصيل فقط. يمكنك أيضًا اختيار نقطة على الخريطة دون منح الإذن.'),
                actions: [
                  TextButton(
                      onPressed: () => Navigator.pop(context, false),
                      child: const Text('اختيار يدوي')),
                  FilledButton(
                      onPressed: () => Navigator.pop(context, true),
                      child: const Text('السماح بالمتابعة'))
                ],
              )));
      if (agreed != true || !mounted) return;
    }
    if (!mounted) return;
    final revision = ++_revision;
    setState(() {
      _locating = true;
      _failure = null;
    });
    try {
      final location = await ref
          .read(locationServiceProvider)
          .current(requestPermission: explicit);
      if (!mounted || revision != _revision) return;
      setState(() {
        _point = LatLng(location.latitude, location.longitude);
        _approximate = location.approximate;
      });
      _map.move(_point!, 17);
    } on LocationFailure catch (e) {
      if (mounted && revision == _revision) setState(() => _failure = e);
    } catch (_) {
      if (mounted)
        setState(() => _failure =
            const LocationFailure('تعذر تحديد الموقع. يمكنك الاختيار يدويًا.'));
    } finally {
      if (mounted) setState(() => _locating = false);
    }
  }

  Future<void> _confirm() async {
    final point = _point;
    if (point == null || _confirming || _locating) return;
    setState(() => _confirming = true);
    final result = await ref
        .read(locationServiceProvider)
        .describeLocation(point.latitude, point.longitude);
    if (mounted) Navigator.pop(context, result);
  }

  @override
  Widget build(BuildContext context) => Directionality(
        textDirection: TextDirection.rtl,
        child: Scaffold(
          backgroundColor: const Color(0xFFFAF7F2),
          appBar: AppBar(
              title: const Text('حدد موقع العنوان'),
              backgroundColor: const Color(0xFF028090),
              foregroundColor: Colors.white),
          body: SafeArea(
              child: Column(children: [
            Padding(
                padding: const EdgeInsets.all(16),
                child: Text(widget.readOnly
                    ? 'موقع العنوان المحفوظ. لتغييره اختر تعديل العنوان.'
                    : 'حرّك الخريطة أو اضغط عليها لوضع الدبوس، ثم أكّد الموقع. لا يؤثر الموقع على رسوم الطلب.')),
            if (_failure != null)
              Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Column(children: [
                    Text(_failure!.message,
                        style: const TextStyle(color: Color(0xFF9A3412))),
                    if (_failure!.settings)
                      TextButton(
                          onPressed: Geolocator.openLocationSettings,
                          child: const Text('فتح إعدادات الموقع')),
                    if (_failure!.permission)
                      TextButton(
                          onPressed: Geolocator.openAppSettings,
                          child: const Text('فتح إعدادات التطبيق')),
                  ])),
            if (_approximate)
              const Padding(
                  padding: EdgeInsets.all(8),
                  child: Text(
                      'دقة GPS منخفضة. عدّل الدبوس وتأكد من مكان العنوان قبل المتابعة.',
                      style: TextStyle(color: Color(0xFF9A3412)))),
            Expanded(
                child: Stack(children: [
              FlutterMap(
                mapController: _map,
                options: MapOptions(
                  // Overview only: this center is never silently selected/saved.
                  initialCenter: _point ?? const LatLng(31.9, 35.2),
                  initialZoom: _point == null ? 8 : 17,
                  minZoom: 3, maxZoom: 19,
                  onTap: (_, point) {
                    _pick(point);
                    _map.move(point, _map.camera.zoom);
                  },
                  onPositionChanged: (camera, gesture) {
                    if (gesture) _pick(camera.center);
                  },
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
                      if (mounted && !_tilesFailed) {
                        WidgetsBinding.instance.addPostFrameCallback((_) {
                          if (mounted) setState(() => _tilesFailed = true);
                        });
                      }
                    },
                  ),
                  if (_point != null)
                    MarkerLayer(markers: [
                      Marker(
                        point: _point!,
                        width: 52,
                        height: 58,
                        alignment: Alignment.topCenter,
                        child: const Icon(Icons.location_pin,
                            size: 52, color: Color(0xFF028090)),
                      )
                    ]),
                  RichAttributionWidget(attributions: [
                    TextSourceAttribution(
                      'OpenStreetMap contributors',
                      onTap: () => launchUrl(
                          Uri.parse('https://www.openstreetmap.org/copyright')),
                    )
                  ]),
                ],
              ),
              if (_tilesFailed)
                Positioned(
                    top: 8,
                    left: 8,
                    right: 8,
                    child: Material(
                      color: const Color(0xFFFFF7ED),
                      borderRadius: BorderRadius.circular(12),
                      child: Padding(
                          padding: const EdgeInsets.all(10),
                          child: Row(children: [
                            const Expanded(
                                child: Text(
                                    'تعذر تحميل بعض أجزاء الخريطة. تحقق من الإنترنت قبل تأكيد الموقع.')),
                            TextButton(
                                onPressed: () =>
                                    setState(() => _tilesFailed = false),
                                child: const Text('إغلاق')),
                          ])),
                    )),
              if (_point == null)
                const Center(
                    child: IgnorePointer(
                        child: Icon(Icons.add_location_alt_outlined,
                            size: 40, color: Color(0xFF64748B)))),
            ])),
            Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      if (!widget.readOnly)
                        OutlinedButton.icon(
                            onPressed: _locating || _confirming
                                ? null
                                : () => _locate(true),
                            icon: _locating
                                ? const SizedBox(
                                    width: 18,
                                    height: 18,
                                    child: CircularProgressIndicator(
                                        strokeWidth: 2))
                                : const Icon(Icons.my_location),
                            label: Text(_locating
                                ? 'جارٍ تحديد موقعك...'
                                : 'موقعي الحالي')),
                      const SizedBox(height: 8),
                      FilledButton(
                          onPressed: widget.readOnly
                              ? () => Navigator.pop(context)
                              : _point == null || _confirming || _locating
                                  ? null
                                  : _confirm,
                          style: FilledButton.styleFrom(
                              backgroundColor: const Color(0xFF028090),
                              minimumSize: const Size.fromHeight(48)),
                          child: Text(widget.readOnly
                              ? 'إغلاق الخريطة'
                              : _confirming
                                  ? 'جارٍ تأكيد الموقع...'
                                  : 'تأكيد هذا الموقع')),
                    ])),
          ])),
        ),
      );
}
