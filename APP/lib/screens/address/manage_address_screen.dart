import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class ManageAddressScreen extends ConsumerStatefulWidget {
  const ManageAddressScreen({super.key});
  @override
  ConsumerState<ConsumerStatefulWidget> createState() => _ManageAddressScreenState();
}

class _ManageAddressScreenState extends ConsumerState<ManageAddressScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: Column(
        children: [
          Container(
            padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 18.h),
            decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.vertical(bottom: Radius.circular(24))),
            child: SafeArea(
              bottom: false,
              child: Row(children: [
                InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: Colors.white24)), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Colors.white))),
                SizedBox(width: 12.w),
                Expanded(child: Text(S.of(context).mngadrs, style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo'))),
                Container(padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h), decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r)), child: Row(children: [Icon(Icons.location_on_rounded, size: 14.sp, color: const Color(0xFF028090)), SizedBox(width: 4.w), Consumer(builder: (context, ref, _) => Text('${ref.watch(addresListProvider).maybeMap(loaded: (s) => s.data.data!.addresses!.length, orElse: () => 0)} عنوان', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090))))])),
              ]),
            ),
          ),
          Expanded(
            child: ref.watch(addresListProvider).map(
                  initial: (_) => const SizedBox(),
                  loading: (_) => const Center(child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090))),
                  loaded: (_) {
                    final list = _.data.data!.addresses!;
                    if (list.isEmpty) {
                      return Center(
                        child: Container(
                          margin: EdgeInsets.all(20.w), padding: EdgeInsets.all(24.w),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9))),
                          child: Column(mainAxisSize: MainAxisSize.min, children: [
                            Container(width: 72.w, height: 72.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), shape: BoxShape.circle), child: Icon(Icons.location_off_rounded, size: 28.sp, color: const Color(0xFF94A3B8))),
                            SizedBox(height: 14.h),
                            Text('لا يوجد عناوين', style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))),
                            SizedBox(height: 6.h),
                            Text('أضف عنوانك الأول للتوصيل السريع', style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B))),
                          ]),
                        ),
                      );
                    }
                    return ListView.builder(
                      padding: EdgeInsets.fromLTRB(16.w, 16.h, 16.w, 100.h),
                      itemCount: list.length,
                      itemBuilder: (context, i) => Padding(padding: EdgeInsets.only(bottom: 12.h), child: AddressCard(address: list[i])),
                    );
                  },
                  error: (_) => Center(child: Container(margin: EdgeInsets.all(20.w), padding: EdgeInsets.all(16.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center))),
                ),
          ),
        ],
      ),
      bottomNavigationBar: Container(
        padding: EdgeInsets.fromLTRB(20.w, 12.h, 20.w, 12.h + MediaQuery.of(context).viewPadding.bottom),
        decoration: BoxDecoration(color: Colors.white, border: Border(top: BorderSide(color: const Color(0xFFE2E8F0))), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 12, offset: const Offset(0, -4))]),
        child: SizedBox(
          height: 48.h, width: double.infinity,
          child: DecoratedBox(
            decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.28), blurRadius: 12, offset: const Offset(0, 6))]),
            child: ElevatedButton.icon(
              onPressed: () => context.nav.pushNamed(Routes.addOrUpdateAddressScreen),
              icon: Icon(Icons.add_location_alt_rounded, size: 18.sp, color: Colors.white),
              label: Text(S.of(context).adadres, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
              style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))),
            ),
          ),
        ),
      ),
    );
  }
}

class AddressCard extends ConsumerStatefulWidget {
  const AddressCard({super.key, required this.address});
  final Address address;
  @override
  ConsumerState<ConsumerStatefulWidget> createState() => _AddressCardState();
}

class _AddressCardState extends ConsumerState<AddressCard> {
  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.all(16.w),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))]),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(width: 44.w, height: 44.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFF00A896).withOpacity(0.14))), child: Icon(Icons.location_on_rounded, size: 20.sp, color: const Color(0xFF028090))),
          SizedBox(width: 12.w),
          Expanded(
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(widget.address.addressLine.toString(), style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'), maxLines: 1, overflow: TextOverflow.ellipsis),
              SizedBox(height: 4.h),
              Text(processAddess(), style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B), height: 1.3), maxLines: 2, overflow: TextOverflow.ellipsis),
            ]),
          ),
          SizedBox(width: 8.w),
          InkWell(
            borderRadius: BorderRadius.circular(10.r),
            onTap: () => context.nav.pushNamed(Routes.addOrUpdateAddressScreen, arguments: widget.address),
            child: Container(width: 36.w, height: 36.h, decoration: BoxDecoration(color: const Color(0xFFF8FAFC), borderRadius: BorderRadius.circular(10.r), border: Border.all(color: const Color(0xFFE2E8F0))), child: Icon(Icons.edit_outlined, size: 16.sp, color: const Color(0xFF028090))),
          ),
        ],
      ),
    );
  }

  String processAddess() {
    if (widget.address.addressLine2 == null) {
      return "${widget.address.addressName}, ${widget.address.area}, ${widget.address.postCode}";
    } else {
      return "${widget.address.addressName}, ${widget.address.area}, ${widget.address.addressLine2 ?? ''}, ${widget.address.postCode}";
    }
  }
}

class AddressCardv2 extends ConsumerWidget {
  const AddressCardv2({super.key, required this.address, required this.isSelected});
  final Address address;
  final bool isSelected;
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Container(
      margin: EdgeInsets.only(top: 12.h),
      padding: EdgeInsets.all(16.w),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16.r), border: Border.all(color: isSelected ? const Color(0xFF00A896) : const Color(0xFFE2E8F0), width: isSelected ? 1.4 : 1), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.05), blurRadius: 10)]),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Container(width: 36.w, height: 36.h, decoration: BoxDecoration(color: isSelected ? const Color(0xFF00A896) : const Color(0xFFF1F5F9), shape: BoxShape.circle), child: Icon(Icons.location_on_rounded, size: 16.sp, color: isSelected ? Colors.white : const Color(0xFF64748B))),
        SizedBox(width: 10.w),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          if (address.addressName != null) Text(address.addressName.toString(), style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))),
          SizedBox(height: 2.h),
          Text(AppGFunctions.processAdAddess(address), style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B)), maxLines: 2, overflow: TextOverflow.ellipsis),
        ])),
        if (isSelected) Container(width: 22.w, height: 22.h, decoration: const BoxDecoration(color: Color(0xFF00A896), shape: BoxShape.circle), child: Icon(Icons.check_rounded, size: 14.sp, color: Colors.white)),
      ]),
    );
  }
}
