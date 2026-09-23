import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/providers/order_update_provider.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/screens/cart/my_cart_with_image_card.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class MyCartTab extends ConsumerWidget {
  MyCartTab({super.key});
  final Box appSettingsBox = Hive.box(AppHSC.appSettingsBox);
  final TextEditingController coupon = TextEditingController();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final orderId = ref.watch(orderIdProvider);
    int? minimum = 0; double? dlvrychrg = 0; double? free = 0;
    ref.watch(settingsProvider).whenOrNull(loaded: (d) { minimum = d.data!.minimumCost; dlvrychrg = d.data!.deliveryCost!.toDouble(); free = d.data!.feeCost!.toDouble(); });
    ref.watch(couponProvider); ref.watch(discountAmountProvider); ref.watch(vendorCartsProvider);

    return Container(
      color: const Color(0xFFFAF7F2),
      child: Column(
        children: [
          Container(
            padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 16.h),
            decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.vertical(bottom: Radius.circular(24))),
            child: SafeArea(
              bottom: false,
              child: Row(children: [
                Text(S.of(context).myCart, style: TextStyle(fontSize: 18.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                const Spacer(),
                ValueListenableBuilder(
                  valueListenable: Hive.box(AppHSC.cartBox).listenable(),
                  builder: (context, Box b, _) {
                    final c = CartHelper.getAllItems().length;
                    return Container(padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h), decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(20.r), border: Border.all(color: Colors.white24)), child: Row(children: [Icon(Icons.shopping_cart_rounded, size: 14.sp, color: Colors.white), SizedBox(width: 4.w), Text('$c عنصر', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: Colors.white))]));
                  },
                ),
              ]),
            ),
          ),
          Expanded(
            child: ValueListenableBuilder(
              valueListenable: Hive.box(AppHSC.authBox).listenable(),
              builder: (context, Box authbox, _) {
                if (authbox.get(AppHSC.authToken) == null) return const NotSignedInCart();
                return ValueListenableBuilder(
                  valueListenable: Hive.box(AppHSC.cartBox).listenable(),
                  builder: (context, Box cartBox, _) {
                    final vendorCarts = CartHelper.getAllVendorCarts();
                    final allItems = CartHelper.getAllItems();
                    ref.watch(couponProvider).maybeWhen(
                          orElse: () {},
                          error: (_) { EasyLoading.showError(_); ref.refresh(couponProvider); },
                          loaded: (_) {
                            if (_.data?.coupon?.discount != null) {
                              final sub = calculateTotal(allItems);
                              Future.delayed(buildDuration).then((_) {
                                if (_.data!.coupon!.type!.toLowerCase() == "percent") {
                                  ref.read(discountAmountProvider.notifier).state = sub * (_.data!.coupon!.discount! / 100);
                                } else {
                                  ref.read(discountAmountProvider.notifier).state = _.data!.coupon!.discount!.toDouble();
                                }
                              });
                            }
                          },
                        );
                    if (allItems.isEmpty) {
                      return Center(
                        child: Container(
                          margin: EdgeInsets.all(20.w), padding: EdgeInsets.all(24.w),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9))),
                          child: Column(mainAxisSize: MainAxisSize.min, children: [
                            Container(width: 80.w, height: 80.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), shape: BoxShape.circle), child: Icon(Icons.shopping_cart_outlined, size: 32.sp, color: const Color(0xFF94A3B8))),
                            SizedBox(height: 16.h),
                            Text(S.of(context).noitmcrt, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E)), textAlign: TextAlign.center),
                            SizedBox(height: 6.h),
                            Text('أضف منتجات من الخدمات أو المتاجر', style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B))),
                          ]),
                        ),
                      );
                    }
                    return ListView(
                      padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 140.h),
                      children: [
                        ...vendorCarts.entries.map((e) {
                          final vid = e.key; final items = e.value;
                          final String rawName = items.first.vendorName ?? '';
                          final vName = rawName.isNotEmpty ? rawName : (vid == 'default' ? 'المتجر الرئيسي' : 'متجر #$vid');
                          final vTotal = CartHelper.calculateTotalForVendor(vid);
                          return Container(
                            margin: EdgeInsets.only(bottom: 14.h),
                            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))]),
                            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                              Container(
                                padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 12.h),
                                decoration: BoxDecoration(color: const Color(0xFF028090).withOpacity(0.06), borderRadius: BorderRadius.vertical(top: Radius.circular(20.r))),
                                child: Row(children: [
                                  Container(width: 32.w, height: 32.h, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(10.r), border: Border.all(color: const Color(0xFFE2E8F0))), child: Icon(Icons.store_rounded, size: 16.sp, color: const Color(0xFF028090))),
                                  SizedBox(width: 8.w),
                                  Expanded(child: Text(vName, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E)), maxLines: 1)),
                                  Text('${items.length} عنصر', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))),
                                  SizedBox(width: 8.w),
                                  InkWell(onTap: () { CartHelper.clearVendorCart(vid); ref.refreshVendorCarts(); }, borderRadius: BorderRadius.circular(8.r), child: Container(padding: EdgeInsets.symmetric(horizontal: 8.w, vertical: 4.h), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(8.r)), child: Text(S.of(context).cncl, style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: const Color(0xFFE63946))))),
                                ]),
                              ),
                              ...items.map((it) => MyCartItemImageCard(carItemHiveModel: it, vendorId: vid)),
                              Padding(
                                padding: EdgeInsets.fromLTRB(14.w, 10.h, 14.w, 8.h),
                                child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                                  Text('المجموع الفرعي ($vName)', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))),
                                  Text('${appSettingsBox.get('currency') ?? '\$'}${vTotal.toStringAsFixed(2)}', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))),
                                ]),
                              ),
                              Padding(
                                padding: EdgeInsets.fromLTRB(14.w, 0, 14.w, 14.h),
                                child: SizedBox(
                                  height: 42.h, width: double.infinity,
                                  child: DecoratedBox(
                                    decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(12.r)),
                                    child: ElevatedButton(
                                      onPressed: () {
                                        ref.read(activeVendorIdProvider.notifier).state = vid;
                                        if (calculateTotal(items) >= (minimum ?? 0)) { ref.refresh(addresListProvider); context.nav.pushNamed(Routes.checkOutScreen); } else { EasyLoading.showError('${S.of(context).mnmmordramnt} ${AppGFunctions.convertToFixedTwo(minimum!)}'); }
                                      },
                                      style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))),
                                      child: Text('${S.of(context).chckout} • $vName', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900)),
                                    ),
                                  ),
                                ),
                              ),
                            ]),
                          );
                        }),
                        if (orderId == '')
                          Container(
                            padding: EdgeInsets.all(16.w),
                            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9))),
                            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                              Text('كود الخصم', style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))),
                              SizedBox(height: 10.h),
                              Row(children: [
                                Expanded(child: TextField(controller: coupon, decoration: InputDecoration(hintText: 'أدخل الكود', hintStyle: TextStyle(fontSize: 11.sp, color: const Color(0xFF94A3B8)), filled: true, fillColor: const Color(0xFFF8FAFC), contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 12.h), border: OutlineInputBorder(borderRadius: BorderRadius.circular(12.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))), enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))), focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12.r), borderSide: const BorderSide(color: Color(0xFF00A896)))))),
                                SizedBox(width: 8.w),
                                SizedBox(height: 44.h, width: 86.w, child: DecoratedBox(decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(12.r)), child: ElevatedButton(onPressed: () => ref.read(couponProvider.notifier).applyCoupon(coupon: coupon.text, amount: calculateTotal(allItems).toStringAsFixed(2)), style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))), child: Text('تطبيق', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900))))),
                              ]),
                              ref.watch(couponProvider).maybeWhen(orElse: () => const SizedBox(), loaded: (_) => InkWell(onTap: () { coupon.clear(); ref.refresh(couponProvider); ref.refresh(discountAmountProvider); }, child: Padding(padding: EdgeInsets.only(top: 8.h), child: Row(mainAxisAlignment: MainAxisAlignment.end, children: [Icon(Icons.close_rounded, size: 14.sp, color: const Color(0xFFE63946)), SizedBox(width: 4.w), Text('إزالة الكود ${_.data?.coupon?.code ?? ''}', style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946), fontWeight: FontWeight.w700))])))),
                            ]),
                          ),
                        SizedBox(height: 12.h),
                        Container(
                          padding: EdgeInsets.all(16.w),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9))),
                          child: Column(children: [
                            Text('ملخص الطلب', style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))),
                            SizedBox(height: 12.h),
                            ...vendorCarts.entries.map((e) => Padding(padding: EdgeInsets.only(bottom: 6.h), child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text('المجموع (${e.value.first.vendorName ?? 'متجر'})', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))), Text('${appSettingsBox.get('currency') ?? '\$'}${CartHelper.calculateTotalForVendor(e.key).toStringAsFixed(2)}', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800))]))),
                            Divider(height: 16.h, color: const Color(0xFFF1F5F9)),
                            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text('المجموع الفرعي', style: TextStyle(fontSize: 12.sp, color: const Color(0xFF64748B))), Text('${appSettingsBox.get('currency') ?? '\$'}${calculateTotal(allItems).toStringAsFixed(2)}', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800))]),
                            SizedBox(height: 6.h),
                            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text('تكلفة التوصيل', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))), Text(AppGFunctions.calculateTotal(allItems).toInt() < (free ?? 0) ? '${appSettingsBox.get('currency') ?? '\$'}$dlvrychrg' : '0.00', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w700))]),
                            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text('الخصم', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))), Text('${appSettingsBox.get('currency') ?? '\$'}${ref.watch(discountAmountProvider).toStringAsFixed(2)}', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w700, color: const Color(0xFF00A896)))]),
                            Divider(height: 16.h, color: const Color(0xFFF1F5F9)),
                            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text('الإجمالي', style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))), Text('${appSettingsBox.get('currency') ?? '\$'}${(AppGFunctions.calculateTotal(allItems).toInt() < (free ?? 0) ? (AppGFunctions.calculateTotal(allItems) + (dlvrychrg ?? 0) - ref.watch(discountAmountProvider)).toStringAsFixed(2) : (AppGFunctions.calculateTotal(allItems) - ref.watch(discountAmountProvider)).toStringAsFixed(2))}', style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090)))]),
                          ]),
                        ),
                        SizedBox(height: 16.h),
                        if (vendorCarts.length > 1)
                          SizedBox(width: double.infinity, height: 44.h, child: OutlinedButton(onPressed: () { CartHelper.clearAll(); ref.refreshVendorCarts(); }, style: OutlinedButton.styleFrom(side: const BorderSide(color: Color(0xFFE63946)), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))), child: Text('إفراغ كل العربات', style: TextStyle(color: const Color(0xFFE63946), fontWeight: FontWeight.w800)))),
                        SizedBox(height: 12.h),
                        SizedBox(
                          height: 50.h, width: double.infinity,
                          child: DecoratedBox(
                            decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.28), blurRadius: 12, offset: const Offset(0, 6))]),
                            child: Consumer(builder: (context, ref, _) {
                              return ref.watch(updateOrderProvider).map(
                                    initial: (_) => orderId != '' ? ElevatedButton(onPressed: () => updateOrderProduct(ref: ref, orderId: orderId, cartItems: allItems, context: context), style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))), child: Text(S.of(context).updateproduct, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900))) : ElevatedButton(onPressed: () {
                                      final authBox = Hive.box(AppHSC.authBox);
                                      if (authBox.get(AppHSC.authToken) == null || authBox.get(AppHSC.authToken) == '') { context.nav.pushNamed(Routes.loginScreen); return; }
                                      if (calculateTotal(allItems) >= (minimum ?? 0)) {
                                        if (vendorCarts.keys.isNotEmpty) ref.read(activeVendorIdProvider.notifier).state = vendorCarts.keys.first;
                                        ref.refresh(addresListProvider);
                                        context.nav.pushNamed(Routes.checkOutScreen);
                                      } else { EasyLoading.showError('${S.of(context).mnmmordramnt} ${AppGFunctions.convertToFixedTwo(minimum!)}'); }
                                    }, style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))), child: Text(S.of(context).chckout, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900))),
                                    loading: (_) => const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))),
                                    loaded: (_) => const SizedBox(),
                                    error: (_) => Center(child: Text('error', style: TextStyle(color: const Color(0xFFE63946)))),
                                  );
                            }),
                          ),
                        ),
                        if (orderId != '') ...[SizedBox(height: 12.h), SizedBox(width: double.infinity, height: 44.h, child: OutlinedButton(onPressed: () { ref.read(homeScreenPageControllerProvider).animateToPage(4, duration: transissionDuration, curve: Curves.easeInOut); ref.read(homeScreenIndexProvider.notifier).state = 4; }, style: OutlinedButton.styleFrom(side: const BorderSide(color: Color(0xFF00A896)), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))), child: Text(S.of(context).addmore, style: const TextStyle(color: Color(0xFF028090), fontWeight: FontWeight.w800))))],
                      ],
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  void updateOrderProduct({required WidgetRef ref, required String orderId, required List<CarItemHiveModel> cartItems, required BuildContext context}) {
    final List<OrderProductModel> products = [];
    for (final c in cartItems) { products.add(OrderProductModel(id: c.productsId.toString(), quantity: c.productsQTY.toString())); }
    ref.read(orderRepoProvider).updateOrder(products, orderId).then((_) {
      EasyLoading.showSuccess(S.of(context).orderupdatesuccmes);
      ref.read(orderIdProvider.notifier).state = '';
      CartHelper.clearAll(); ref.refreshVendorCarts();
      ref.read(homeScreenIndexProvider.notifier).state = 1;
      ref.read(homeScreenPageControllerProvider).animateToPage(1, duration: transissionDuration, curve: Curves.easeInOut);
    });
  }

  double calculateTotal(List<CarItemHiveModel> cartItems) {
    double a = 0; for (final e in cartItems) { if (e.subProduct != null) { a += e.productsQTY * (e.unitPrice + (e.subProduct!.price?.toDouble() ?? 0)); } else { a += e.productsQTY * e.unitPrice; } } return a;
  }
}

class NotSignedInCart extends StatelessWidget {
  const NotSignedInCart({super.key});
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Container(
        margin: EdgeInsets.all(20.w), padding: EdgeInsets.all(24.w),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9))),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(width: 80.w, height: 80.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), shape: BoxShape.circle), child: Icon(Icons.shopping_cart_outlined, size: 32.sp, color: const Color(0xFF94A3B8))),
          SizedBox(height: 16.h),
          Text('سجّل دخولك لعرض عربتك', style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E)), textAlign: TextAlign.center),
          SizedBox(height: 6.h),
          Text('ستجد هنا منتجاتك قبل الطلب', style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B)), textAlign: TextAlign.center),
        ]),
      ),
    );
  }
}
