import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/providers/address_book_provider.dart';
import 'package:laundry_customer/providers/location_suggestion_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/screens/address/add_update_address.dart';
import 'package:laundry_customer/screens/address/current_location_card.dart';
import 'package:laundry_customer/screens/address/manage_address_screen.dart';

class DeliveryAddressSection extends ConsumerStatefulWidget {
  const DeliveryAddressSection({super.key});
  @override
  ConsumerState<DeliveryAddressSection> createState() =>
      _DeliveryAddressSectionState();
}

class _DeliveryAddressSectionState
    extends ConsumerState<DeliveryAddressSection> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;
      if (!ref.read(addressBookProvider).loading)
        ref.read(addressBookProvider.notifier).refresh();
      ref.read(locationSuggestionProvider.notifier).suggest();
    });
  }

  void _select(Address address) {
    ref.read(addressIDProvider.notifier).state = address.id.toString();
    ScaffoldMessenger.of(context)
        .showSnackBar(const SnackBar(content: Text('تم اختيار عنوان التوصيل')));
  }

  @override
  Widget build(BuildContext context) {
    final book = ref.watch(addressBookProvider);
    final selected =
        selectedOwnedAddress(book.addresses, ref.watch(addressIDProvider));
    return Directionality(
        textDirection: TextDirection.rtl,
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: const Color(0xFFE2E8F0))),
          child:
              Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
            const Row(children: [
              Icon(Icons.location_on_outlined, color: Color(0xFF028090)),
              SizedBox(width: 8),
              Text('عنوان التوصيل',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold))
            ]),
            const SizedBox(height: 8),
            const Text(
                'حدد موقعك على الخريطة حتى نعرف مكان استلام وتسليم الطلب بدقة.'),
            const SizedBox(height: 16),
            CurrentLocationCard(onSaved: _select),
            const SizedBox(height: 16),
            if (book.loading)
              const LinearProgressIndicator(color: Color(0xFF028090)),
            if (selected != null && book.error == null)
              Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                      color: const Color(0xFFF8FAFC),
                      borderRadius: BorderRadius.circular(14)),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                            'العنوان المحدد: ${selected.addressName ?? 'عنوان محفوظ'}',
                            style:
                                const TextStyle(fontWeight: FontWeight.bold)),
                        const SizedBox(height: 6),
                        Text(readableAddress(selected)),
                      ]))
            else if (!book.loading)
              const Text(
                  'اختر عنوانًا محفوظًا أو أكّد موقعك واحفظه قبل إتمام الطلب.',
                  style: TextStyle(color: Color(0xFF64748B))),
            if (book.error != null)
              Column(children: [
                Text(book.error!,
                    style: const TextStyle(color: Color(0xFFB91C1C))),
                TextButton(
                    onPressed: book.busy
                        ? null
                        : () =>
                            ref.read(addressBookProvider.notifier).refresh(),
                    child: const Text('إعادة المحاولة')),
              ]),
            const SizedBox(height: 12),
            OutlinedButton.icon(
                style: OutlinedButton.styleFrom(
                  foregroundColor: const Color(0xFF028090),
                  side: const BorderSide(color: Color(0xFF028090)),
                ),
                onPressed: book.busy
                    ? null
                    : () => Navigator.push<Address>(
                        context,
                        MaterialPageRoute(
                            builder: (_) =>
                                const ManageAddressScreen(choose: true))),
                icon: const Icon(Icons.bookmark_border),
                label: const Text('اختيار عنوان محفوظ')),
            TextButton.icon(
                style: TextButton.styleFrom(
                  foregroundColor: const Color(0xFF028090),
                ),
                onPressed:
                    book.loading || book.busy
                        ? null
                        : () async {
                            final saved = await Navigator.push<Address>(
                                context,
                                MaterialPageRoute(
                                    builder: (_) => const AddOrEditAddress()));
                            if (mounted && saved != null) _select(saved);
                          },
                icon: const Icon(Icons.add_location_alt_outlined),
                label: const Text('إضافة عنوان جديد')),
          ]),
        ));
  }
}
