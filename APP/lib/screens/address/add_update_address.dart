import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/providers/address_book_provider.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/screens/address/location_picker_screen.dart';
import 'package:laundry_customer/services/location_service.dart';

PickedLocation? addressLocation(Address? address) {
  final lat = double.tryParse(address?.latitude?.toString() ?? '');
  final lon = double.tryParse(address?.longitude?.toString() ?? '');
  if (!validCoordinates(lat, lon)) return null;
  return PickedLocation(
      latitude: lat!,
      longitude: lon!,
      addressLine: address?.addressLine ?? '',
      area: address?.area?.toString() ?? '',
      postCode: address?.postCode ?? '');
}

class AddOrEditAddress extends ConsumerStatefulWidget {
  const AddOrEditAddress({super.key, this.address, this.initialLocation});
  final Address? address;
  final PickedLocation? initialLocation;
  @override
  ConsumerState<AddOrEditAddress> createState() => _AddOrEditAddressState();
}

class _AddOrEditAddressState extends ConsumerState<AddOrEditAddress> {
  final _form = GlobalKey<FormState>();
  late final TextEditingController _name;
  late final TextEditingController _area;
  late final TextEditingController _line;
  PickedLocation? _location;
  bool _default = false;
  bool _saving = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    final address = widget.address;
    final initial = widget.initialLocation;
    _name = TextEditingController(
        text: address?.addressName?.toString() ??
            (initial != null ? 'الموقع الحالي' : ''));
    // Area is NEVER auto-filled — it must be typed by the customer manually.
    _area = TextEditingController(text: address?.area?.toString() ?? '');
    _line = TextEditingController(text: address?.addressLine ?? '');
    _default = address?.isDefault ?? false;
    _location = initial ?? addressLocation(address);
  }

  @override
  void dispose() {
    for (final controller in [_name, _area, _line]) {
      controller.dispose();
    }
    super.dispose();
  }

  Future<void> _chooseLocation() async {
    final picked = await Navigator.push<PickedLocation>(
        context,
        MaterialPageRoute(
            builder: (_) => LocationPickerScreen(
                initial: _location, locateOnOpen: _location == null)));
    if (!mounted || picked == null) return;
    setState(() {
      _location = picked;
      // The area (المنطقة) is deliberately NOT auto-filled here — the
      // customer types it themselves; it stays required.
      _error = null;
    });
  }

  Future<void> _save() async {
    if (_saving || !_form.currentState!.validate()) return;
    if (_location == null || !_location!.valid) {
      setState(() => _error = 'حدد نقطة صالحة على الخريطة قبل حفظ العنوان.');
      return;
    }
    setState(() {
      _saving = true;
      _error = null;
    });
    final fields = <String, dynamic>{
      'address_name': _name.text.trim(),
      'area': _area.text.trim(),
      'address_line': _line.text.trim(),
      'latitude': _location!.latitude.toStringAsFixed(7),
      'longitude': _location!.longitude.toStringAsFixed(7),
      'is_default': _default,
    };
    final notifier = ref.read(addressBookProvider.notifier);
    final saved = await notifier.save(fields, existing: widget.address);
    if (!mounted) return;
    setState(() => _saving = false);
    if (saved != null) {
      ref.invalidate(addresListProvider);
      Navigator.pop(context, saved);
    } else {
      setState(() => _error = ref.read(addressBookProvider).error ??
          'تعذر تأكيد الحفظ. حدّث العناوين قبل المحاولة مجددًا.');
    }
  }

  Widget _field(TextEditingController controller, String label, IconData icon,
          {bool required = false, int lines = 1}) =>
      Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: TextFormField(
            controller: controller,
            maxLines: lines,
            enabled: !_saving,
            maxLength: controller == _name ? 256 : null,
            validator: required
                ? (value) =>
                    (value ?? '').trim().isEmpty ? '$label مطلوب' : null
                : null,
            decoration: InputDecoration(
                labelText: label,
                prefixIcon: Icon(icon, color: const Color(0xFF028090)),
                filled: true,
                fillColor: const Color(0xFFF8FAFC),
                border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14))),
          ));

  @override
  Widget build(BuildContext context) => Directionality(
        textDirection: TextDirection.rtl,
        child: PopScope(
            canPop: !_saving,
            child: Scaffold(
              backgroundColor: const Color(0xFFFAF7F2),
              appBar: AppBar(
                  title: Text(widget.address == null
                      ? 'إضافة عنوان جديد'
                      : 'تعديل العنوان'),
                  backgroundColor: const Color(0xFF028090),
                  foregroundColor: Colors.white),
              body: SafeArea(
                  child: SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Form(
                    key: _form,
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          const Text('مكانك محفوظ، وتوصيلك أدق',
                              style: TextStyle(
                                  fontSize: 22,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xFF0B1E2E))),
                          const SizedBox(height: 8),
                          const Text(
                              'اختر الموقع على الخريطة وأضف وصفًا يساعدنا في الوصول إليك.'),
                          const SizedBox(height: 20),
                          Wrap(
                              spacing: 8,
                              children: ['البيت', 'العمل']
                                  .map((label) => ActionChip(
                                      label: Text(label),
                                      avatar: Icon(
                                          label == 'البيت'
                                              ? Icons.home_outlined
                                              : Icons.work_outline,
                                          size: 18),
                                      onPressed: _saving
                                          ? null
                                          : () => setState(
                                              () => _name.text = label)))
                                  .toList()),
                          const SizedBox(height: 12),
                          _field(_name, 'اسم العنوان', Icons.label_outline,
                              required: true),
                          _field(_area, 'المنطقة', Icons.map_outlined,
                              required: true),
                          _field(_line, 'الشارع ورقم البناية (اختياري)',
                              Icons.signpost_outlined,
                              lines: 2),
                          Card(
                              color: Colors.white,
                              child: Padding(
                                  padding: const EdgeInsets.all(16),
                                  child: Column(children: [
                                    Icon(
                                        _location == null
                                            ? Icons.add_location_alt_outlined
                                            : Icons.location_on,
                                        size: 36,
                                        color: const Color(0xFF028090)),
                                    const SizedBox(height: 8),
                                    Text(_location == null
                                        ? 'لم يُحدّد موقع العنوان بعد'
                                        : 'تم تحديد موقع العنوان على الخريطة'),
                                    const SizedBox(height: 8),
                                    OutlinedButton.icon(
                                        onPressed:
                                            _saving ? null : _chooseLocation,
                                        icon: const Icon(Icons.map_outlined),
                                        label: Text(_location == null
                                            ? 'اختيار الموقع على الخريطة'
                                            : 'عرض الموقع أو تغييره')),
                                  ]))),
                          SwitchListTile.adaptive(
                              value: _default,
                              onChanged: _saving
                                  ? null
                                  : (value) => setState(() => _default = value),
                              activeTrackColor: const Color(0xFF028090),
                              title: const Text('اجعله عنواني الأساسي'),
                              subtitle: const Text(
                                  'يمكنك اختيار عنوان مختلف عند تأكيد الطلب.')),
                          if (_error != null)
                            Padding(
                                padding: const EdgeInsets.all(12),
                                child: Text(_error!,
                                    style: const TextStyle(
                                        color: Color(0xFFB91C1C)),
                                    textAlign: TextAlign.center)),
                          const SizedBox(height: 12),
                          FilledButton.icon(
                              onPressed: _saving ? null : _save,
                              style: FilledButton.styleFrom(
                                  backgroundColor: const Color(0xFF028090),
                                  minimumSize: const Size.fromHeight(52)),
                              icon: _saving
                                  ? const SizedBox(
                                      width: 18,
                                      height: 18,
                                      child: CircularProgressIndicator(
                                          strokeWidth: 2))
                                  : const Icon(Icons.check),
                              label: Text(_saving
                                  ? 'جارٍ حفظ العنوان...'
                                  : 'حفظ العنوان')),
                          const SizedBox(height: 16),
                        ])),
              )),
            )),
      );
}
