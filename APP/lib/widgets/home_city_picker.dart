import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geolocator/geolocator.dart';
import 'package:laundry_customer/providers/city_providers.dart';
import 'package:laundry_customer/services/location_service.dart';
import 'package:url_launcher/url_launcher.dart';

Future<void> showHomeCityPicker(BuildContext context) =>
    showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      useSafeArea: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (_) => const _CitySheet(),
    );

/// One-time, first-launch prompt: explains why we ask for location access and
/// lets the customer activate it (or choose a city manually). When the system
/// location permission is denied forever, the primary button opens the app's
/// settings page instead of asking for it again.
Future<void> showLocationActivationPrompt(BuildContext context) =>
    showModalBottomSheet<void>(
      context: context,
      isScrollControlled: true,
      useSafeArea: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (_) => const _LocationPromptSheet(),
    );

class HomeCityPicker extends ConsumerWidget {
  const HomeCityPicker({super.key});
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final selection = ref.watch(citySelectionProvider);
    return TextButton.icon(
      onPressed: () => showHomeCityPicker(context),
      icon: const Icon(Icons.location_on_rounded, size: 18),
      label: Text(
          selection.city?.name ??
              (selection.loading ? 'تحديد المدينة…' : 'اختر المدينة'),
          style: const TextStyle(
              fontFamily: 'Cairo', fontWeight: FontWeight.w700)),
      style: TextButton.styleFrom(
          backgroundColor: const Color(0xFFEAF6F8),
          foregroundColor: const Color(0xFF156172)),
    );
  }
}

class HomeCityNotice extends ConsumerWidget {
  const HomeCityNotice({super.key});
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final selection = ref.watch(citySelectionProvider);
    return Padding(
        padding: const EdgeInsets.all(24),
        child: Column(children: [
          if (selection.loading) const CircularProgressIndicator(),
          const SizedBox(height: 16),
          Text(
              selection.message ??
                  'اختر مدينتك لعرض المتاجر والخدمات المتاحة فيها.',
              textAlign: TextAlign.center,
              style: const TextStyle(fontFamily: 'Cairo')),
          const SizedBox(height: 12),
          FilledButton(
              onPressed: () => showHomeCityPicker(context),
              child: const Text('اختيار المدينة')),
        ]));
  }
}

class _CitySheet extends ConsumerStatefulWidget {
  const _CitySheet();
  @override
  ConsumerState<_CitySheet> createState() => _CitySheetState();
}

class _CitySheetState extends ConsumerState<_CitySheet> {
  String query = '';
  @override
  Widget build(BuildContext context) {
    final selection = ref.watch(citySelectionProvider);
    final controller = ref.read(citySelectionProvider.notifier);
    final cities = selection.cities
        .where((c) =>
            c.name.contains(query.trim()) ||
            c.districts.any((d) => d.contains(query.trim())))
        .toList();
    return Directionality(
        textDirection: TextDirection.rtl,
        child: Padding(
          padding: EdgeInsets.only(
              left: 20,
              right: 20,
              top: 16,
              bottom: MediaQuery.viewInsetsOf(context).bottom + 12),
          child: SizedBox(
              height: MediaQuery.sizeOf(context).height * .78,
              child: Column(children: [
                Row(children: [
                  const Expanded(
                      child: Text('اختر المدينة',
                          style: TextStyle(
                              fontSize: 20, fontWeight: FontWeight.bold))),
                  IconButton(
                      onPressed: () => Navigator.pop(context),
                      icon: const Icon(Icons.close))
                ]),
                const Text(
                    'يمكنك الاختيار يدويًا، أو السماح بالموقع لتحديد مدينتك. يُرسل الموقع للتحقق من حدود المدينة فقط، ولا يُحفظ في اختيار المدينة على الجهاز.',
                    style: TextStyle(fontSize: 12)),
                TextButton.icon(
                    onPressed: selection.loading || selection.cities.isEmpty
                        ? null
                        : () async {
                            await controller.useCurrentLocation();
                            if (context.mounted &&
                                ref.read(citySelectionProvider).message == null)
                              Navigator.pop(context);
                          },
                    icon: const Icon(Icons.my_location),
                    label: const Text('استخدام موقعي الحالي')),
                if (selection.loading) const LinearProgressIndicator(),
                if (selection.message != null)
                  Text(selection.message!, textAlign: TextAlign.center),
                if (selection.cities.isEmpty && !selection.loading)
                  TextButton(
                      onPressed: controller.initialize,
                      child: const Text('إعادة المحاولة')),
                TextField(
                    onChanged: (v) => setState(() => query = v),
                    decoration: const InputDecoration(
                        hintText: 'ابحث عن مدينة',
                        prefixIcon: Icon(Icons.search))),
                Expanded(
                    child: ListView.builder(
                        itemCount: cities.length,
                        itemBuilder: (_, index) {
                          final city = cities[index];
                          return ListTile(
                              title: Text(city.name),
                              subtitle: Text(city.districts.join('، ')),
                              selected: selection.city?.id == city.id,
                              trailing: selection.city?.id == city.id
                                  ? const Icon(Icons.check_circle)
                                  : null,
                              onTap: () {
                                controller.select(city.id);
                                Navigator.pop(context);
                              });
                        })),
                TextButton(
                    onPressed: () => launchUrl(
                        Uri.parse('https://www.pcbs.gov.ps')),
                    child: const Text('حدود المدن: الجهاز المركزي للإحصاء الفلسطيني',
                        style: TextStyle(fontSize: 11))),
              ])),
        ));
  }
}

class _LocationPromptSheet extends ConsumerStatefulWidget {
  const _LocationPromptSheet();

  @override
  ConsumerState<_LocationPromptSheet> createState() =>
      _LocationPromptSheetState();
}

class _LocationPromptSheetState extends ConsumerState<_LocationPromptSheet> {
  LocationPermission? _permission;

  @override
  void initState() {
    super.initState();
    // Check the current permission once, so we can show the right call to action.
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      try {
        final permission =
            await ref.read(locationServiceProvider).device.permission();
        if (mounted) setState(() => _permission = permission);
      } catch (_) {/* leave _permission as-is */}
    });
  }

  bool get _foreverDenied =>
      _permission == LocationPermission.deniedForever;

  bool get _alreadyGranted =>
      _permission == LocationPermission.whileInUse ||
      _permission == LocationPermission.always;

  String get _primaryLabel =>
      _foreverDenied
          ? 'فتح إعدادات التطبيق لتفعيل الموقع'
          : _alreadyGranted
              ? 'تحديث موقعي الحالي'
              : 'السماح بالموقع';

  Future<void> _handleAllow() async {
    if (_foreverDenied) {
      // The OS will not ask again; send the customer to the app settings.
      await Geolocator.openAppSettings();
      return;
    }
    final notifier = ref.read(citySelectionProvider.notifier);
    await notifier.useCurrentLocation();
    if (!mounted) return;
    final city = ref.read(citySelectionProvider).city;
    if (city != null) {
      Navigator.of(context).pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final selection = ref.watch(citySelectionProvider);
    final loading = selection.loading && selection.cities.isEmpty;
    return Directionality(
      textDirection: TextDirection.rtl,
      child: Padding(
        padding: EdgeInsets.only(
            bottom: MediaQuery.viewInsetsOf(context).bottom,
            left: 24,
            right: 24,
            top: 8),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: const Color(0xFFE2E8F0),
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            SizedBox(height: 18),
            Container(
              width: 88,
              height: 88,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF028090), Color(0xFF00A896)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                shape: BoxShape.circle,
                boxShadow: const [
                  BoxShadow(
                    color: Color(0x3300A896),
                    blurRadius: 24,
                    spreadRadius: 2,
                  ),
                ],
              ),
              child: const Icon(Icons.my_location_rounded,
                  size: 42, color: Colors.white),
            ),
            SizedBox(height: 18),
            Text(
              'فعّل الموقع لعرض مدينتك',
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontFamily: 'Cairo',
                fontSize: 22,
                fontWeight: FontWeight.w900,
                color: Color(0xFF0B1E2E),
              ),
            ),
            SizedBox(height: 8),
            Text(
              'نحتاج موقعك لعرض المتاجر والخدمات والعروض المتاحة في مدينتك مباشرةً على الشاشة الرئيسية. لا يُستخدم موقعك لأي غرض آخر.',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontFamily: 'Cairo',
                fontSize: 13,
                height: 1.7,
                color: const Color(0xFF475569),
              ),
            ),
            SizedBox(height: 18),
            if (loading)
              Padding(
                padding: const EdgeInsets.symmetric(vertical: 4),
                child: Column(children: [
                  const LinearProgressIndicator(color: Color(0xFF028090)),
                  SizedBox(height: 8),
                  Text('جارٍ تحميل المدن…',
                      style: const TextStyle(color: Color(0xFF64748B))),
                ]),
              )
            else ...[
              if (selection.message != null)
                Container(
                  margin: const EdgeInsets.only(bottom: 12),
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFFF7ED),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: const Color(0xFFFDE68A)),
                  ),
                  child: Text(selection.message!,
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                          fontSize: 12, color: Color(0xFF92400E))),
                ),
              SizedBox(
                height: 52,
                child: FilledButton(
                  onPressed: _handleAllow,
                  style: FilledButton.styleFrom(
                    backgroundColor: const Color(0xFF028090),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                  ),
                  child: Text(_primaryLabel,
                      style: const TextStyle(
                          fontFamily: 'Cairo',
                          fontSize: 14,
                          fontWeight: FontWeight.w800,
                          color: Colors.white)),
                ),
              ),
              SizedBox(height: 8),
              SizedBox(
                height: 50,
                child: OutlinedButton(
                  onPressed: () {
                    Navigator.of(context).pop();
                    showHomeCityPicker(context);
                  },
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: Color(0xFFCBD5E1)),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                  ),
                  child: const Text('اختيار المدينة يدويًا',
                      style: TextStyle(
                          fontFamily: 'Cairo',
                          fontSize: 13,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF334155))),
                ),
              ),
            ],
            SizedBox(height: 12),
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: const Text('ليس الآن',
                  style: TextStyle(color: Color(0xFF94A3B8))),
            ),
            SizedBox(height: 8),
          ],
        ),
      ),
    );
  }
}
