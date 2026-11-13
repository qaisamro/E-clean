import 'package:laundry_customer/screens/address/delivery_address_section.dart';
import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/screens/order/payment_method_card.dart';
import 'package:laundry_customer/screens/payment/payment_section.dart';
import 'package:laundry_customer/screens/payment/schedule_picker_widget.dart';
import 'package:laundry_customer/screens/payment/vendor_appointment_picker.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';

class CheckOutScreen extends ConsumerStatefulWidget {
  const CheckOutScreen({super.key});
  @override
  ConsumerState<ConsumerStatefulWidget> createState() => _CheckOutScreenState();
}

class _CheckOutScreenState extends ConsumerState<CheckOutScreen> {
  final TextEditingController _instruction = TextEditingController();
  final TextEditingController _couponController = TextEditingController();
  // Online card payment was removed: cash on delivery is the only method.
  PaymentType selectedPaymentType = PaymentType.cash;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(couponProvider).whenOrNull(
        loaded: (data) {
          if (data.data?.coupon?.code != null) {
            _couponController.text = data.data!.coupon!.code!;
          }
        },
      );
    });
  }

  @override
  void dispose() {
    _instruction.dispose();
    _couponController.dispose();
    super.dispose();
  }

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
            decoration: const BoxDecoration(
                gradient: LinearGradient(
                    colors: [Color(0xFF028090), Color(0xFF00A896)],
                    begin: Alignment.topRight,
                    end: Alignment.bottomLeft),
                borderRadius:
                    BorderRadius.vertical(bottom: Radius.circular(24))),
            child: SafeArea(
              bottom: false,
              child: Row(children: [
                InkWell(
                    borderRadius: BorderRadius.circular(12.r),
                    onTap: () => context.nav.pop(),
                    child: Container(
                        width: 38.w,
                        height: 38.h,
                        decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.18),
                            borderRadius: BorderRadius.circular(12.r),
                            border: Border.all(color: Colors.white24)),
                        child: const Icon(Icons.arrow_forward_rounded,
                            size: 18, color: Colors.white))),
                SizedBox(width: 12.w),
                Expanded(
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                      Text(S.of(context).shpngndpymnt,
                          style: TextStyle(
                              fontSize: 16.sp,
                              fontWeight: FontWeight.w900,
                              color: Colors.white,
                              fontFamily: 'Cairo')),
                      Text('اختر الموعد والعنوان وطريقة الدفع',
                          style: TextStyle(
                              fontSize: 11.sp,
                              color: Colors.white.withOpacity(0.92)))
                    ])),
                Container(
                    width: 36.w,
                    height: 36.h,
                    decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.18),
                        shape: BoxShape.circle),
                    child: Icon(Icons.local_shipping_rounded,
                        size: 18.sp, color: Colors.white)),
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
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20.r),
                      border: Border.all(
                          color: const Color(0xFFE2E8F0).withOpacity(0.9)),
                      boxShadow: [
                        BoxShadow(
                            color: const Color(0xFF0B1E2E).withOpacity(0.06),
                            blurRadius: 14,
                            offset: const Offset(0, 6))
                      ]),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: [
                          Container(
                              width: 32.w,
                              height: 32.h,
                              decoration: BoxDecoration(
                                  color: const Color(0xFFEFFAF8),
                                  borderRadius: BorderRadius.circular(10.r)),
                              child: Icon(Icons.calendar_today_rounded,
                                  size: 16.sp, color: const Color(0xFF028090))),
                          SizedBox(width: 8.w),
                          Text(S.of(context).shpngschdl,
                              style: TextStyle(
                                  fontSize: 13.sp,
                                  fontWeight: FontWeight.w900,
                                  color: const Color(0xFF0B1E2E),
                                  fontFamily: 'Cairo'))
                        ]),
                        SizedBox(height: 14.h),
                        VendorAppointmentPicker(
                            legacyPicker: Row(children: [
                          Expanded(
                              child: ShedulePicker(
                                  image: 'assets/images/pickup-car.png',
                                  title: S.of(context).pickupat)),
                          SizedBox(width: 10.w),
                          Expanded(
                              child: ShedulePicker(
                                  image: 'assets/images/pick-up-truck.png',
                                  title: S.of(context).dlvryat))
                        ])),
                      ]),
                ),
                SizedBox(height: 14.h),
                const DeliveryAddressSection(),
                SizedBox(height: 14.h),
                // تعليمات
                Container(
                  padding: EdgeInsets.all(16.w),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20.r),
                      border: Border.all(
                          color: const Color(0xFFE2E8F0).withOpacity(0.9)),
                      boxShadow: [
                        BoxShadow(
                            color: const Color(0xFF0B1E2E).withOpacity(0.06),
                            blurRadius: 14,
                            offset: const Offset(0, 6))
                      ]),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: [
                          Container(
                              width: 32.w,
                              height: 32.h,
                              decoration: BoxDecoration(
                                  color: const Color(0xFFFFF7ED),
                                  borderRadius: BorderRadius.circular(10.r)),
                              child: Icon(Icons.notes_rounded,
                                  size: 16.sp, color: const Color(0xFFF59E0B))),
                          SizedBox(width: 8.w),
                          Text(S.of(context).instrctn,
                              style: TextStyle(
                                  fontSize: 13.sp,
                                  fontWeight: FontWeight.w900,
                                  color: const Color(0xFF0B1E2E),
                                  fontFamily: 'Cairo'))
                        ]),
                        SizedBox(height: 12.h),
                        TextField(
                            controller: _instruction,
                            maxLines: 3,
                            decoration: InputDecoration(
                                hintText: S.of(context).adinstrctnop,
                                hintStyle: TextStyle(
                                    fontSize: 12.sp,
                                    color: const Color(0xFF94A3B8)),
                                filled: true,
                                fillColor: const Color(0xFFF8FAFC),
                                contentPadding: EdgeInsets.all(14.w),
                                border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16.r),
                                    borderSide: const BorderSide(
                                        color: Color(0xFFE2E8F0))),
                                enabledBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16.r),
                                    borderSide: const BorderSide(
                                        color: Color(0xFFE2E8F0))),
                                focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16.r),
                                    borderSide: const BorderSide(
                                        color: Color(0xFF00A896))))),
                      ]),
                ),
                SizedBox(height: 14.h),
                // كود الخصم
                Container(
                  padding: EdgeInsets.all(16.w),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20.r),
                      border: Border.all(
                          color: const Color(0xFFE2E8F0).withOpacity(0.9)),
                      boxShadow: [
                        BoxShadow(
                            color: const Color(0xFF0B1E2E).withOpacity(0.06),
                            blurRadius: 14,
                            offset: const Offset(0, 6))
                      ]),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: [
                          Container(
                              width: 32.w,
                              height: 32.h,
                              decoration: BoxDecoration(
                                  color: const Color(0xFFEFFAF8),
                                  borderRadius: BorderRadius.circular(10.r)),
                              child: Icon(Icons.local_offer_rounded,
                                  size: 16.sp, color: const Color(0xFF028090))),
                          SizedBox(width: 8.w),
                          Text('كود الخصم',
                              style: TextStyle(
                                  fontSize: 13.sp,
                                  fontWeight: FontWeight.w900,
                                  color: const Color(0xFF0B1E2E),
                                  fontFamily: 'Cairo'))
                        ]),
                        SizedBox(height: 12.h),
                        Row(children: [
                          Expanded(
                            child: TextField(
                              controller: _couponController,
                              textDirection: TextDirection.ltr,
                              decoration: InputDecoration(
                                hintText: 'أدخل كود الخصم',
                                hintStyle: TextStyle(
                                    fontSize: 12.sp,
                                    color: const Color(0xFF94A3B8),
                                    fontFamily: 'Cairo'),
                                filled: true,
                                fillColor: const Color(0xFFF8FAFC),
                                contentPadding: EdgeInsets.symmetric(
                                    horizontal: 14.w, vertical: 12.h),
                                border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(14.r),
                                    borderSide: const BorderSide(
                                        color: Color(0xFFE2E8F0))),
                                enabledBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(14.r),
                                    borderSide: const BorderSide(
                                        color: Color(0xFFE2E8F0))),
                                focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(14.r),
                                    borderSide: const BorderSide(
                                        color: Color(0xFF00A896))),
                              ),
                            ),
                          ),
                          SizedBox(width: 8.w),
                          SizedBox(
                            height: 48.h,
                            width: 88.w,
                            child: DecoratedBox(
                              decoration: BoxDecoration(
                                  gradient: const LinearGradient(colors: [
                                    Color(0xFF028090),
                                    Color(0xFF00A896)
                                  ]),
                                  borderRadius: BorderRadius.circular(14.r)),
                              child: ElevatedButton(
                                onPressed: () {
                                  final code = _couponController.text.trim();
                                  if (code.isEmpty) {
                                    EasyLoading.showToast(
                                        'يرجى إدخال كود الخصم');
                                    return;
                                  }
                                  final String? activeVendorId =
                                      ref.read(activeVendorIdProvider);
                                  final List<CarItemHiveModel> cartItems =
                                      activeVendorId != null
                                          ? CartHelper.getCartForVendor(
                                              activeVendorId)
                                          : CartHelper.getAllItems();
                                  double total = 0;
                                  for (final item in cartItems) {
                                    if (item.subProduct != null) {
                                      total += item.productsQTY *
                                          (item.unitPrice +
                                              (item.subProduct!.price ?? 0));
                                    } else {
                                      total +=
                                          item.productsQTY * item.unitPrice;
                                    }
                                  }
                                  ref.read(couponProvider.notifier).applyCoupon(
                                        coupon: code,
                                        amount: total.toStringAsFixed(2),
                                      );
                                },
                                style: ElevatedButton.styleFrom(
                                    backgroundColor: Colors.transparent,
                                    shadowColor: Colors.transparent,
                                    shape: RoundedRectangleBorder(
                                        borderRadius:
                                            BorderRadius.circular(14.r))),
                                child: Text('تطبيق',
                                    style: TextStyle(
                                        color: Colors.white,
                                        fontWeight: FontWeight.w900,
                                        fontSize: 13.sp,
                                        fontFamily: 'Cairo')),
                              ),
                            ),
                          ),
                        ]),
                        ref.watch(couponProvider).maybeWhen(
                              orElse: () => const SizedBox.shrink(),
                              loading: () => Padding(
                                padding: EdgeInsets.only(top: 10.h),
                                child: const Center(
                                  child: SizedBox(
                                    width: 22,
                                    height: 22,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      color: Color(0xFF00A896),
                                    ),
                                  ),
                                ),
                              ),
                              error: (error) => Padding(
                                padding: EdgeInsets.only(top: 10.h),
                                child: Container(
                                  padding: EdgeInsets.symmetric(
                                      horizontal: 12.w, vertical: 8.h),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFFEF2F2),
                                    borderRadius: BorderRadius.circular(12.r),
                                    border: Border.all(
                                        color: const Color(0xFFE63946)
                                            .withOpacity(0.3)),
                                  ),
                                  child: Row(
                                    children: [
                                      Icon(Icons.error_outline_rounded,
                                          size: 16.sp,
                                          color: const Color(0xFFE63946)),
                                      SizedBox(width: 6.w),
                                      Expanded(
                                        child: Text(
                                          error,
                                          style: TextStyle(
                                            fontSize: 12.sp,
                                            color: const Color(0xFFE63946),
                                            fontWeight: FontWeight.w600,
                                            fontFamily: 'Cairo',
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              loaded: (data) {
                                final couponData = data.data?.coupon;
                                final double discount =
                                    ref.watch(discountAmountProvider);
                                return Padding(
                                  padding: EdgeInsets.only(top: 10.h),
                                  child: Container(
                                    padding: EdgeInsets.symmetric(
                                        horizontal: 12.w, vertical: 8.h),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFEFFAF8),
                                      borderRadius: BorderRadius.circular(12.r),
                                      border: Border.all(
                                          color: const Color(0xFF00A896)
                                              .withOpacity(0.3)),
                                    ),
                                    child: Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Row(children: [
                                          Icon(Icons.check_circle_rounded,
                                              size: 16.sp,
                                              color: const Color(0xFF00A896)),
                                          SizedBox(width: 6.w),
                                          Text(
                                            'تم تطبيق: ${couponData?.code ?? ''} (-${discount.toStringAsFixed(2)})',
                                            style: TextStyle(
                                              fontSize: 12.sp,
                                              color: const Color(0xFF028090),
                                              fontWeight: FontWeight.w700,
                                              fontFamily: 'Cairo',
                                            ),
                                          ),
                                        ]),
                                        InkWell(
                                          onTap: () {
                                            _couponController.clear();
                                            ref.refresh(couponProvider);
                                            ref.refresh(discountAmountProvider);
                                          },
                                          child: Row(
                                            children: [
                                              Icon(Icons.close_rounded,
                                                  size: 14.sp,
                                                  color:
                                                      const Color(0xFFE63946)),
                                              SizedBox(width: 2.w),
                                              Text(
                                                'إلغاء',
                                                style: TextStyle(
                                                  fontSize: 11.sp,
                                                  color:
                                                      const Color(0xFFE63946),
                                                  fontWeight: FontWeight.w700,
                                                  fontFamily: 'Cairo',
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                );
                              },
                            ),
                      ]),
                ),
                SizedBox(height: 14.h),
                // كراس الدفع — طريقة الدفع او السداد (ايقونات فقط — بلا صور)
                Container(
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24.r),
                      border: Border.all(
                          color: const Color(0xFF00A896).withOpacity(0.30),
                          width: 1.4),
                      boxShadow: [
                        BoxShadow(
                            color: const Color(0xFF00A896).withOpacity(0.10),
                            blurRadius: 18,
                            offset: const Offset(0, 8))
                      ]),
                  padding: EdgeInsets.all(18.w),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(children: [
                          Container(
                              width: 38.w,
                              height: 38.h,
                              decoration: BoxDecoration(
                                  gradient: const LinearGradient(
                                      colors: [
                                        Color(0xFF028090),
                                        Color(0xFF00A896)
                                      ],
                                      begin: Alignment.topRight,
                                      end: Alignment.bottomLeft),
                                  shape: BoxShape.circle,
                                  boxShadow: [
                                    BoxShadow(
                                        color: const Color(0xFF00A896)
                                            .withOpacity(0.30),
                                        blurRadius: 10,
                                        offset: const Offset(0, 4))
                                  ]),
                              child: const Icon(Icons.payments_rounded,
                                  size: 19, color: Colors.white)),
                          const SizedBox(width: 10),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('طريقة الدفع او السداد',
                                  style: TextStyle(
                                      fontSize: 13.5.sp,
                                      fontWeight: FontWeight.w900,
                                      color: const Color(0xFF0B1E2E),
                                      fontFamily: 'Cairo')),
                              Text('الدفع عند الاستلام حالياً',
                                  style: TextStyle(
                                      fontSize: 10.sp,
                                      fontWeight: FontWeight.w600,
                                      color: const Color(0xFF64748B),
                                      fontFamily: 'Cairo')),
                            ],
                          ),
                        ]),
                        const SizedBox(height: 14),
                        PaymentMethodCard(
                            onTap: () => setState(
                                () => selectedPaymentType = PaymentType.cash),
                            isSelected:
                                selectedPaymentType == PaymentType.cash),
                      ]),
                ),
                SizedBox(height: 14.h),
                PaymentSection(
                    instruction: _instruction,
                    selectedPaymentType: selectedPaymentType),
                SizedBox(height: 24.h),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

enum PaymentType { cash }
