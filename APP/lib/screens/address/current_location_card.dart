import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/providers/address_book_provider.dart';
import 'package:laundry_customer/providers/location_suggestion_provider.dart';
import 'package:laundry_customer/screens/address/add_update_address.dart';
import 'package:laundry_customer/screens/address/location_picker_screen.dart';
import 'package:laundry_customer/services/location_service.dart';

class CurrentLocationCard extends ConsumerStatefulWidget {
  const CurrentLocationCard({super.key, required this.onSaved});
  final ValueChanged<Address> onSaved;
  @override
  ConsumerState<CurrentLocationCard> createState() =>
      _CurrentLocationCardState();
}

class _CurrentLocationCardState extends ConsumerState<CurrentLocationCard> {
  bool _opening = false;
  Future<void> _open() async {
    if (_opening) return;
    final book = ref.read(addressBookProvider);
    if (book.loading || book.busy) return;
    setState(() => _opening = true);
    try {
      final point = await Navigator.push<PickedLocation>(
          context,
          MaterialPageRoute(
              builder: (_) => LocationPickerScreen(
                  initial: ref.read(locationSuggestionProvider).point,
                  locateOnOpen: true)));
      if (!mounted || point == null) return;
      final saved = await Navigator.push<Address>(
          context,
          MaterialPageRoute(
              builder: (_) => AddOrEditAddress(initialLocation: point)));
      if (mounted && saved != null) widget.onSaved(saved);
    } finally {
      if (mounted) setState(() => _opening = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final suggestion = ref.watch(locationSuggestionProvider);
    final book = ref.watch(addressBookProvider);
    return Card(
        color: const Color(0xFFEFFAF8),
        elevation: 0,
        margin: EdgeInsets.zero,
        child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Row(children: [
                    Icon(Icons.my_location, color: Color(0xFF028090)),
                    SizedBox(width: 8),
                    Expanded(
                        child: Text('موقعك الحالي',
                            style: TextStyle(fontWeight: FontWeight.bold)))
                  ]),
                  const SizedBox(height: 8),
                  if (suggestion.loading)
                    const LinearProgressIndicator(color: Color(0xFF028090)),
                  Text(suggestion.point != null
                      ? (suggestion.point!.addressLine.isEmpty
                          ? 'موقعك متاح. أكّده على الخريطة وأضف وصف العنوان.'
                          : suggestion.point!.addressLine)
                      : suggestion.message ??
                          'اقتراح اختياري؛ تأكد من الموقع قبل حفظه.'),
                  const SizedBox(height: 8),
                  OutlinedButton.icon(
                      style: OutlinedButton.styleFrom(
                        foregroundColor: const Color(0xFF028090),
                        side: const BorderSide(color: Color(0xFF028090)),
                      ),
                      onPressed:
                          _opening || book.loading || book.busy ? null : _open,
                      icon: const Icon(Icons.my_location),
                      label: Text(_opening
                          ? 'جارٍ فتح الخريطة...'
                          : 'تحديد موقعي الحالي')),
                  const Text('لن يُستخدم هذا الموقع للطلب قبل تأكيده وحفظه.',
                      style: TextStyle(fontSize: 12, color: Color(0xFF64748B))),
                ])));
  }
}
