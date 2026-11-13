import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/providers/address_book_provider.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/location_suggestion_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/screens/address/add_update_address.dart';
import 'package:laundry_customer/screens/address/current_location_card.dart';
import 'package:laundry_customer/screens/address/location_picker_screen.dart';

String readableAddress(Address address) => [
      address.area?.toString(),
      address.addressLine,
      address.addressLine2,
    ].whereType<String>().where((s) => s.trim().isNotEmpty).toSet().join('، ');

Address? selectedOwnedAddress(List<Address> addresses, String id) {
  for (final address in addresses) {
    if (address.id != null && address.id.toString() == id) return address;
  }
  return null;
}

class ManageAddressScreen extends ConsumerStatefulWidget {
  const ManageAddressScreen({super.key, this.choose = false});
  final bool choose;
  @override
  ConsumerState<ManageAddressScreen> createState() =>
      _ManageAddressScreenState();
}

class _ManageAddressScreenState extends ConsumerState<ManageAddressScreen> {
  String _pendingId = '';
  @override
  void initState() {
    super.initState();
    _pendingId = ref.read(addressIDProvider);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;
      ref.read(addressBookProvider.notifier).refresh();
      ref.read(locationSuggestionProvider.notifier).suggest();
    });
  }

  Future<void> _add() async {
    final saved = await Navigator.push<Address>(
        context, MaterialPageRoute(builder: (_) => const AddOrEditAddress()));
    if (mounted && saved != null && widget.choose) {
      setState(() => _pendingId = saved.id.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    final book = ref.watch(addressBookProvider);
    final selected = selectedOwnedAddress(book.addresses, _pendingId);
    return Directionality(
        textDirection: TextDirection.rtl,
        child: Scaffold(
          backgroundColor: const Color(0xFFFAF7F2),
          appBar: AppBar(
              title: Text(widget.choose ? 'اختر عنوان التوصيل' : 'عناويني'),
              backgroundColor: const Color(0xFF028090),
              foregroundColor: Colors.white,
              actions: [
                IconButton(
                    tooltip: 'تحديث العناوين',
                    onPressed: book.busy || book.loading
                        ? null
                        : () =>
                            ref.read(addressBookProvider.notifier).refresh(),
                    icon: const Icon(Icons.refresh))
              ]),
          body: SafeArea(
              bottom: false,
              child: RefreshIndicator(
                onRefresh: () =>
                    ref.read(addressBookProvider.notifier).refresh(),
                child: ListView(
                    padding: const EdgeInsets.all(16),
                    physics: const AlwaysScrollableScrollPhysics(),
                    children: [
                      CurrentLocationCard(onSaved: (address) {
                        if (widget.choose)
                          setState(() => _pendingId = address.id.toString());
                      }),
                      const SizedBox(height: 24),
                      Row(children: [
                        const Expanded(
                            child: Text('العناوين المحفوظة',
                                style: TextStyle(
                                    fontSize: 19,
                                    fontWeight: FontWeight.bold))),
                        Text('${book.addresses.length}',
                            style: const TextStyle(color: Color(0xFF64748B)))
                      ]),
                      const SizedBox(height: 12),
                      if (book.loading || book.busy)
                        const Padding(
                            padding: EdgeInsets.all(16),
                            child: LinearProgressIndicator(
                                color: Color(0xFF028090))),
                      if (book.error != null)
                        Padding(
                            padding: const EdgeInsets.all(12),
                            child: Text(book.error!,
                                style:
                                    const TextStyle(color: Color(0xFFB91C1C)))),
                      if (!book.loading && book.addresses.isEmpty)
                        const Padding(
                            padding: EdgeInsets.all(24),
                            child: Column(children: [
                              Icon(Icons.location_off_outlined,
                                  size: 48, color: Color(0xFF94A3B8)),
                              SizedBox(height: 12),
                              Text('لا توجد عناوين محفوظة. أضف عنوانك الأول.')
                            ])),
                      for (final address in book.addresses)
                        Padding(
                            padding: const EdgeInsets.only(bottom: 12),
                            child: AddressCard(
                                address: address,
                                isSelected:
                                    widget.choose && selected?.id == address.id,
                                onSelect:
                                    widget.choose && !book.busy && !book.loading
                                        ? () => setState(() =>
                                            _pendingId = address.id.toString())
                                        : null)),
                      const SizedBox(height: 12),
                    ]),
              )),
          bottomNavigationBar: SafeArea(
              top: false,
              child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                      mainAxisSize: MainAxisSize.min,
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        if (widget.choose)
                          FilledButton(
                              onPressed: selected == null ||
                                      book.loading ||
                                      book.busy ||
                                      book.error != null
                                  ? null
                                  : () {
                                      ref
                                          .read(addressIDProvider.notifier)
                                          .state = selected.id.toString();
                                      Navigator.pop(context, selected);
                                    },
                              style: FilledButton.styleFrom(
                                  backgroundColor: const Color(0xFF028090),
                                  minimumSize: const Size.fromHeight(48)),
                              child: const Text('استخدم هذا العنوان')),
                        const SizedBox(height: 8),
                        OutlinedButton.icon(
                            onPressed: book.loading || book.busy
                                ? null
                                : _add,
                            icon: const Icon(Icons.add_location_alt_outlined),
                            label: const Text('إضافة عنوان جديد')),
                      ]))),
        ));
  }
}

class AddressCard extends ConsumerWidget {
  const AddressCard(
      {super.key,
      required this.address,
      this.isSelected = false,
      this.onSelect});
  final Address address;
  final bool isSelected;
  final VoidCallback? onSelect;

  Future<void> _delete(BuildContext context, WidgetRef ref) async {
    final confirmed = await showDialog<bool>(
        context: context,
        builder: (context) => Directionality(
            textDirection: TextDirection.rtl,
            child: AlertDialog(
              title: const Text('حذف العنوان؟'),
              content: Text(address.isDefault
                  ? 'هذا عنوانك الأساسي. بعد الحذف يمكنك اختيار عنوان أساسي آخر.'
                  : 'سيُحذف العنوان من قائمة عناوينك المحفوظة.'),
              actions: [
                TextButton(
                    onPressed: () => Navigator.pop(context, false),
                    child: const Text('إلغاء')),
                FilledButton(
                    onPressed: () => Navigator.pop(context, true),
                    child: const Text('حذف العنوان'))
              ],
            )));
    if (confirmed != true || !context.mounted) return;
    final success = await ref
        .read(addressBookProvider.notifier)
        .mutate(address, delete: true);
    if (!context.mounted) return;
    if (success) {
      if (ref.read(addressIDProvider) == address.id.toString())
        ref.read(addressIDProvider.notifier).state = '';
      ref.invalidate(addresListProvider);
    }
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(success
            ? 'تم حذف العنوان'
            : ref.read(addressBookProvider).error ??
                'تعذر الحذف. حاول مجددًا.')));
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final busy = ref.watch(addressBookProvider).busy;
    final point = addressLocation(address);
    return Card(
        color: Colors.white,
        elevation: 0,
        shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(18),
            side: BorderSide(
                color: isSelected
                    ? const Color(0xFF028090)
                    : const Color(0xFFE2E8F0),
                width: isSelected ? 2 : 1)),
        child: InkWell(
            borderRadius: BorderRadius.circular(18),
            onTap: onSelect,
            child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      Row(children: [
                        Icon(
                            isSelected
                                ? Icons.check_circle
                                : Icons.location_on_outlined,
                            color: const Color(0xFF028090)),
                        const SizedBox(width: 8),
                        Expanded(
                            child: Text(
                                address.addressName?.toString() ??
                                    'عنوان محفوظ',
                                style: const TextStyle(
                                    fontSize: 17,
                                    fontWeight: FontWeight.bold))),
                        if (address.isDefault)
                          const Chip(
                              label: Text('الأساسي'),
                              backgroundColor: Color(0xFFEFFAF8)),
                      ]),
                      const SizedBox(height: 8),
                      Text(readableAddress(address),
                          style: const TextStyle(
                              color: Color(0xFF475569), height: 1.5)),
                      if (isSelected)
                        const Padding(
                            padding: EdgeInsets.only(top: 8),
                            child: Text('العنوان المحدد',
                                style: TextStyle(
                                    color: Color(0xFF028090),
                                    fontWeight: FontWeight.bold))),
                      const SizedBox(height: 8),
                      Wrap(spacing: 4, children: [
                        TextButton.icon(
                            style: TextButton.styleFrom(
                                foregroundColor: const Color(0xFF028090)),
                            onPressed: busy
                                ? null
                                : () => Navigator.push<Address>(
                                    context,
                                    MaterialPageRoute(
                                        builder: (_) => AddOrEditAddress(
                                            address: address))),
                            icon: const Icon(Icons.edit_outlined, size: 18),
                            label: const Text('تعديل')),
                        TextButton.icon(
                            style: TextButton.styleFrom(
                                foregroundColor: const Color(0xFF028090)),
                            onPressed: busy || point == null
                                ? null
                                : () => Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                        builder: (_) => LocationPickerScreen(
                                            initial: point, readOnly: true))),
                            icon: const Icon(Icons.map_outlined, size: 18),
                            label: const Text('عرض الموقع')),
                        if (!address.isDefault)
                          TextButton(
                              style: TextButton.styleFrom(
                                  foregroundColor: const Color(0xFF028090)),
                              onPressed: busy
                                  ? null
                                  : () async {
                                      final success = await ref
                                          .read(addressBookProvider.notifier)
                                          .mutate(address);
                                      if (!context.mounted) return;
                                      if (success)
                                        ref.invalidate(addresListProvider);
                                      ScaffoldMessenger.of(context)
                                          .showSnackBar(SnackBar(
                                              content: Text(success
                                                  ? 'تم تعيين العنوان الأساسي'
                                                  : ref
                                                          .read(
                                                              addressBookProvider)
                                                          .error ??
                                                      'تعذر التعديل')));
                                    },
                              child: const Text('تعيين كأساسي')),
                        TextButton.icon(
                            onPressed:
                                busy ? null : () => _delete(context, ref),
                            icon: const Icon(Icons.delete_outline,
                                size: 18, color: Color(0xFFB91C1C)),
                            label: const Text('حذف',
                                style: TextStyle(color: Color(0xFFB91C1C)))),
                      ]),
                    ]))));
  }
}

class AddressCardv2 extends ConsumerWidget {
  const AddressCardv2(
      {super.key, required this.address, required this.isSelected});
  final Address address;
  final bool isSelected;
  @override
  Widget build(BuildContext context, WidgetRef ref) =>
      AddressCard(address: address, isSelected: isSelected);
}
