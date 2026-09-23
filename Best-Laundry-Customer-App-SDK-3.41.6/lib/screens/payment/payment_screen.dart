import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/screens/order/payment_method_card.dart';
import 'package:laundry_customer/screens/payment/payment_controller.dart';
import 'package:laundry_customer/screens/payment/payment_section.dart';
import 'package:laundry_customer/screens/payment/schedule_picker_widget.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class CheckOutScreen extends ConsumerStatefulWidget {
  const CheckOutScreen({super.key});
  @override
  ConsumerState<ConsumerStatefulWidget> createState() => _CheckOutScreenState();
}

class _CheckOutScreenState extends ConsumerState<CheckOutScreen> {
  final PaymentController pay = PaymentController();
  final TextEditingController _instruction = TextEditingController();
  PaymentType selectedPaymentType = PaymentType.cash;

  @override
  void initState() {
    super.initState();
  }

  InputDecoration _dropDec(String hint) => InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
        filled: true, fillColor: const Color(0xFFF8FAFC),
        contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 14.h),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFF00A896), width: 1.4)),
      );

  @override
  Widget build(BuildContext context) {
    ref.watch(addressIDProvider);
    ref.watch(dateProvider('Pick Up'));
    ref.watch(dateProvider('Delivery'));
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: Column(
        children: [
          Container(
            padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 16.h),
            decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.vertical(bottom: Radius.circular(24))),
            child: SafeArea(
              bottom: false,
              child: Row(children: [
                InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: Colors.white24)), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Colors.white))),
                SizedBox(width: 12.w),
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(S.of(context).shpngndpymnt, style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')), Text('اختر الموعد والعنوان وطريقة الدفع', style: TextStyle(fontSize: 11.sp, color: Colors.white.withOpacity(0.92)))])),
                Container(width: 36.w, height: 36.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), shape: BoxShape.circle), child: Icon(Icons.local_shipping_rounded, size: 18.sp, color: Colors.white)),
              ]),
            ),
          ),
          Expanded(
            child: ListView(
              padding: EdgeInsets.fromLTRB(16.w, 16.h, 16.w, 16.h),
              children: [
                // جدول الشحن
                Container(
                  padding: EdgeInsets.all(16.w),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))]),
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Row(children: [Container(width: 32.w, height: 32.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(10.r)), child: Icon(Icons.calendar_today_rounded, size: 16.sp, color: const Color(0xFF028090))), SizedBox(width: 8.w), Text(S.of(context).shpngschdl, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'))]),
                    SizedBox(height: 14.h),
                    Row(children: [Expanded(child: ShedulePicker(image: 'assets/images/pickup-car.png', title: S.of(context).pickupat)), SizedBox(width: 10.w), Expanded(child: ShedulePicker(image: 'assets/images/pick-up-truck.png', title: S.of(context).dlvryat))]),
                  ]),
                ),
                SizedBox(height: 14.h),
                // العنوان
                Container(
                  padding: EdgeInsets.all(16.w),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))]),
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                      Row(children: [Container(width: 32.w, height: 32.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(10.r)), child: Icon(Icons.location_on_rounded, size: 16.sp, color: const Color(0xFF028090))), SizedBox(width: 8.w), Text(S.of(context).adrs, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'))]),
                      InkWell(
                        borderRadius: BorderRadius.circular(20.r),
                        onTap: () => context.nav.pushNamed(Routes.manageAddressScreen),
                        child: Container(padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h), decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFF00A896).withOpacity(0.18))), child: Text(S.of(context).mngaddrs, style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))),
                      ),
                    ]),
                    SizedBox(height: 12.h),
                    ref.watch(addresListProvider).map(
                          initial: (_) => const SizedBox(),
                          loading: (_) => const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                          loaded: (_) => _.data.data!.addresses!.isEmpty
                              ? SizedBox(
                                  width: double.infinity, height: 44.h,
                                  child: OutlinedButton.icon(onPressed: () => context.nav.pushNamed(Routes.addOrUpdateAddressScreen), icon: Icon(Icons.add_location_alt_rounded, size: 16.sp, color: const Color(0xFF028090)), label: Text(S.of(context).adadres, style: TextStyle(fontWeight: FontWeight.w800, color: const Color(0xFF028090))), style: OutlinedButton.styleFrom(side: const BorderSide(color: Color(0xFF00A896)), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r)))),
                                )
                              : DropdownButtonFormField(
                                  decoration: _dropDec(S.of(context).chsadrs),
                                  isExpanded: true,
                                  onChanged: (v) => ref.read(addressIDProvider.notifier).state = v.toString(),
                                  items: _.data.data!.addresses!.map((e) => DropdownMenuItem(value: e.id.toString(), child: Text(e.addressLine ?? '', style: TextStyle(fontSize: 12.sp), overflow: TextOverflow.ellipsis))).toList(),
                                ),
                          error: (_) => Container(padding: EdgeInsets.all(10.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(10.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)))),
                        ),
                  ]),
                ),
                SizedBox(height: 14.h),
                // تعليمات
                Container(
                  padding: EdgeInsets.all(16.w),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))]),
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Row(children: [Container(width: 32.w, height: 32.h, decoration: BoxDecoration(color: const Color(0xFFFFF7ED), borderRadius: BorderRadius.circular(10.r)), child: Icon(Icons.notes_rounded, size: 16.sp, color: const Color(0xFFF59E0B))), SizedBox(width: 8.w), Text(S.of(context).instrctn, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'))]),
                    SizedBox(height: 12.h),
                    TextField(controller: _instruction, maxLines: 3, decoration: InputDecoration(hintText: S.of(context).adinstrctnop, hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)), filled: true, fillColor: const Color(0xFFF8FAFC), contentPadding: EdgeInsets.all(14.w), border: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))), enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))), focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFF00A896))))),
                  ]),
                ),
                SizedBox(height: 14.h),
                // طرق الدفع - الدفع عند الاستلام فقط
                Container(
                  padding: EdgeInsets.all(16.w),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))]),
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Row(children: [Container(width: 32.w, height: 32.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(10.r)), child: Icon(Icons.payments_rounded, size: 16.sp, color: const Color(0xFF028090))), SizedBox(width: 8.w), Text(S.of(context).pymntmthd, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'))]),
                    SizedBox(height: 12.h),
                    PaymentMethodCard(onTap: () => setState(() => selectedPaymentType = PaymentType.cash), imageLocation: 'assets/images/logo_cod.png', title: S.of(context).cshondlvry, subtitle: S.of(context).pywhndlvry, isSelected: selectedPaymentType == PaymentType.cash),
                  ]),
                ),
                SizedBox(height: 14.h),
                PaymentSection(instruction: _instruction, selectedPaymentType: selectedPaymentType),
                SizedBox(height: 24.h),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

enum PaymentType { cash, onlinePayment }
