import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/adapters.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/products_model/product.dart';
import 'package:laundry_customer/models/variations_model/variant.dart';
import 'package:laundry_customer/notfiers/guest_notfiers.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/order_update_provider.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/screens/homePage/subProductBottomSheet/sub_product_bottom_sheet.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/buttons/cart_item_inc_dec_button.dart';
import 'package:laundry_customer/widgets/global_functions.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class ChooseItems extends ConsumerWidget {
  const ChooseItems({super.key, required this.service, this.vendorId, this.vendorName});
  final Service service;
  final String? vendorId;
  final String? vendorName;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final finalIndex = ref.watch(itemSelectMenuIndexProvider);
    ref.watch(servicesVariationsProvider(service.id.toString()));
    final productFilter = ref.watch(productsFilterProvider);
    if (productFilter.servieID == '') {
      Future.delayed(buildDuration).then((_) { if (service.id == null) return; ref.read(productsFilterProvider.notifier).state = ProducServiceVariavtionDataModel(servieID: service.id!.toString(), variationID: ''); });
    }
    if (productFilter.variationID == '') {
      ref.watch(servicesVariationsProvider(service.id.toString())).maybeWhen(loaded: (_) {
        Future.delayed(buildDuration).then((_) {
          final vars = _.data?.variants ?? []; vars.sort((a, b) => (a.id ?? 0).compareTo(b.id ?? 0));
          if (vars.isNotEmpty) { ref.read(productsFilterProvider.notifier).state = ProducServiceVariavtionDataModel(servieID: service.id!.toString(), variationID: vars.first.id!.toString()); }
        });
      }, orElse: () {});
    }
    ref.watch(productsProvider);
    final appSettingsBox = Hive.box(AppHSC.appSettingsBox);
    int? minimum = 0; double? dlvrychrg = 0; double? free = 0;
    ref.watch(settingsProvider).whenOrNull(loaded: (d) { minimum = d.data!.minimumCost; dlvrychrg = d.data!.deliveryCost!.toDouble(); free = d.data!.feeCost!.toDouble(); });

    return WillPopScope(
      onWillPop: () { ref.read(productsFilterProvider.notifier).state = ProducServiceVariavtionDataModel(servieID: '', variationID: ''); return Future.value(true); },
      child: Scaffold(
        backgroundColor: const Color(0xFFFAF7F2),
        body: Stack(
          children: [
            CustomScrollView(
              slivers: [
                SliverToBoxAdapter(
                  child: Container(
                    padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 14.h),
                    decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.vertical(bottom: Radius.circular(24))),
                    child: SafeArea(
                      bottom: false,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(children: [
                            InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: Colors.white24)), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Colors.white))),
                            SizedBox(width: 12.w),
                            Expanded(child: Text(getLng(en: service.name, changeLang: service.nameBn.toString()), style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo'), maxLines: 1)),
                            if (vendorName != null) Container(padding: EdgeInsets.symmetric(horizontal: 8.w, vertical: 4.h), decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r)), child: Text(vendorName!, style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))),
                          ]),
                          SizedBox(height: 10.h),
                          Text(service.description?.toString() ?? 'اختر المنتجات والكميات', style: TextStyle(fontSize: 11.5.sp, color: Colors.white.withOpacity(0.92))),
                        ],
                      ),
                    ),
                  ),
                ),
                SliverToBoxAdapter(
                  child: SizedBox(
                    height: 52.h,
                    child: Consumer(builder: (context, ref, _) {
                      return ref.watch(servicesVariationsProvider(service.id.toString())).map(
                            initial: (_) => const SizedBox(), loading: (_) => const SizedBox(),
                            loaded: (_) {
                              final vars = _.data.data!.variants!..sort((a, b) => a.id!.compareTo(b.id!));
                              return ListView.separated(
                                padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 10.h),
                                scrollDirection: Axis.horizontal,
                                itemCount: vars.length,
                                separatorBuilder: (_, __) => SizedBox(width: 8.w),
                                itemBuilder: (context, i) {
                                  final v = vars[i];
                                  final isActive = finalIndex == i;
                                  return InkWell(
                                    borderRadius: BorderRadius.circular(20.r),
                                    onTap: () {
                                      ref.read(productsFilterProvider.notifier).update((s) { s.variationID = v.id!.toString(); return s; });
                                      ref.refresh(productsProvider);
                                      ref.read(itemSelectMenuIndexProvider.notifier).state = i;
                                    },
                                    child: AnimatedContainer(
                                      duration: const Duration(milliseconds: 200),
                                      padding: EdgeInsets.symmetric(horizontal: 16.w),
                                      decoration: BoxDecoration(color: isActive ? const Color(0xFF028090) : Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: isActive ? const Color(0xFF028090) : const Color(0xFFE2E8F0)), boxShadow: [if (isActive) BoxShadow(color: const Color(0xFF028090).withOpacity(0.22), blurRadius: 10, offset: const Offset(0, 4)) else BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.04), blurRadius: 8)]),
                                      child: Center(child: Text(getLng(en: v.name, changeLang: v.nameBn.toString()), style: TextStyle(fontSize: 12.sp, fontWeight: isActive ? FontWeight.w900 : FontWeight.w700, color: isActive ? Colors.white : const Color(0xFF334155)))),
                                    ),
                                  );
                                },
                              );
                            },
                            error: (_) => Center(child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)))),
                          );
                    }),
                  ),
                ),
                SliverPadding(
                  padding: EdgeInsets.fromLTRB(16.w, 4.h, 16.w, 120.h),
                  sliver: Consumer(builder: (context, ref, _) {
                    return ref.watch(productsProvider).map(
                          initial: (_) => const SliverToBoxAdapter(child: SizedBox()),
                          loading: (_) => SliverToBoxAdapter(child: Center(child: Padding(padding: EdgeInsets.only(top: 40), child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090))))),
                          loaded: (_) {
                            if (_.data.data!.products!.isEmpty) return SliverToBoxAdapter(child: Container(margin: EdgeInsets.only(top: 30.h), padding: EdgeInsets.all(24.w), decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r)), child: Column(children: [Icon(Icons.inventory_2_outlined, size: 36.sp, color: const Color(0xFF94A3B8)), SizedBox(height: 10.h), Text('لا توجد منتجات', style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w800, color: const Color(0xFF334155)))])));
                            // Grouping header per store owner
                            final headerName = _.data.data!.products!.isNotEmpty ? (_.data.data!.products!.first.vendorName ?? (vendorName ?? (vendorId != null ? 'متجر المختار' : 'منتجات متعددة'))) : (vendorName ?? (vendorId != null ? 'متجر المختار' : 'منتجات متعددة'));
                            return SliverPadding(
                              padding: EdgeInsets.fromLTRB(16.w, 4.h, 16.w, 120.h),
                              sliver: SliverList(
                                delegate: SliverChildListDelegate([
                                  // Vendor filter chips
                                  Padding(
                                    padding: EdgeInsets.fromLTRB(16.w, 6.h, 16.w, 6.h),
                                    child: Wrap(
                                      spacing: 6.w,
                                      runSpacing: 6.h,
                                      children: [
                                        FilterChip(label: Text('الكل', style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w800)), selected: true, onSelected: (_) {}, backgroundColor: Color(0xFFEAF6F8), selectedColor: Color(0xFF156172), labelStyle: TextStyle(color: Colors.white), showCheckmark: false),
                                        ...({for (final p in _.data.data!.products ?? []) p.vendorName ?? 'متجر المختار'}.toSet()..removeWhere((n) => n.isEmpty)).map((name) => FilterChip(label: Text(name, style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w800)), selected: false, onSelected: (_) {}, backgroundColor: Colors.white, selectedColor: Color(0xFF1b758a), labelStyle: TextStyle(color: Color(0xFF083744)), showCheckmark: false)).toList(),
                                      ],
                                    ),
                                  ),
                                  // Vendor/store owner grouping header
                                  Padding(
                                    padding: EdgeInsets.only(bottom: 10.h, top: 4.h),
                                    child: Container(
                                      padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 10.h),
                                      decoration: BoxDecoration(
                                        gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]),
                                        borderRadius: BorderRadius.circular(14.r),
                                      ),
                                      child: Row(
                                        children: [
                                          Icon(Icons.store_rounded, size: 20.sp, color: Colors.white),
                                          SizedBox(width: 8.w),
                                          Expanded(
                                            child: Text(
                                              headerName,
                                              style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo'),
                                              maxLines: 1,
                                              overflow: TextOverflow.ellipsis,
                                            ),
                                          ),
                                          Text('مجموعة منفصلة • $headerName', style: TextStyle(fontSize: 9.sp, color: Colors.white70)),
                                        ],
                                      ),
                                    ),
                                  ),
                                  ..._.data.data!.products!.asMap().entries.map((entry) {
                                    return Padding(padding: EdgeInsets.only(bottom: 12.h), child: ChooseItemCard(product: entry.value, vendorId: vendorId ?? ref.watch(activeVendorIdProvider), vendorName: vendorName));
                                  }).toList(),
                                ]),
                              ),
                            );
                          },
                          error: (_) => SliverToBoxAdapter(child: Center(child: Text(_.error))),
                        );
                  }),
                ),
              ],
            ),
            Positioned(
              bottom: 0, left: 0, right: 0,
              child: Builder(builder: (context) {
                final scopingVendorId = vendorId ?? ref.watch(activeVendorIdProvider);
                return ValueListenableBuilder(
                  valueListenable: Hive.box(AppHSC.cartBox).listenable(),
                  builder: (context, Box cartBox, _) {
                    final cartItems = scopingVendorId != null ? CartHelper.getCartForVendor(scopingVendorId) : CartHelper.getAllItems();
                    if (cartItems.isEmpty) return const SizedBox.shrink();
                    return Container(
                      padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 12.h + MediaQuery.of(context).viewPadding.bottom),
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(20.r)), border: Border(top: BorderSide(color: const Color(0xFFE2E8F0))), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.10), blurRadius: 18, offset: const Offset(0, -6))]),
                      child: Row(
                        children: [
                          Expanded(
                            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                              Text(S.of(context).ttl, style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))),
                              Text('${appSettingsBox.get('currency') ?? '\$'}${calculateTotal(cartItems).toStringAsFixed(2)}', style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))),
                              if (AppGFunctions.calculateTotal(cartItems).toInt() < free!) Text(' ${S.of(context).dlvrychrg} ${appSettingsBox.get('currency') ?? '\$'}${AppGFunctions.convertToFixedTwo(dlvrychrg!)}', style: TextStyle(fontSize: 10.sp, color: const Color(0xFF94A3B8))) else Text('${S.of(context).dlvrychrg} 0.00', style: TextStyle(fontSize: 10.sp, color: const Color(0xFF94A3B8))),
                            ]),
                          ),
                          Column(children: [
                            if (calculateTotal(cartItems) < minimum!) Text('${S.of(context).mnmmordramnt} ${AppGFunctions.convertToFixedTwo(minimum!)}', style: TextStyle(fontSize: 10.sp, color: const Color(0xFFE63946), fontWeight: FontWeight.w700)),
                            SizedBox(height: 4.h),
                            ref.read(orderIdProvider) != ''
                                ? SizedBox(height: 42.h, width: 140.w, child: ElevatedButton(onPressed: () { context.nav.pop(); ref.read(homeScreenIndexProvider.notifier).state = 0; ref.read(homeScreenPageControllerProvider).animateToPage(0, duration: transissionDuration, curve: Curves.easeInOut); }, style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF028090), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))), child: const Text('تم', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w900))))
                                : SizedBox(
                                    height: 42.h, width: 140.w,
                                    child: DecoratedBox(
                                      decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(12.r)),
                                      child: ElevatedButton(
                                        onPressed: () {
                                          final authBox = Hive.box(AppHSC.authBox);
                                          if (authBox.get(AppHSC.authToken) == null || authBox.get(AppHSC.authToken) == '') { context.nav.pushNamed(Routes.loginScreen); return; }
                                          if (cartItems.isEmpty) { EasyLoading.showError(S.of(context).noitmcrt); return; }
                                          if (calculateTotal(cartItems) >= minimum!) {
                                            if (scopingVendorId != null) ref.read(activeVendorIdProvider.notifier).state = scopingVendorId;
                                            ref.refresh(addresListProvider);
                                            context.nav.pushNamed(Routes.checkOutScreen);
                                          } else { EasyLoading.showError('${S.of(context).mnmmordramnt} ${AppGFunctions.convertToFixedTwo(minimum!)}'); }
                                        },
                                        style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))),
                                        child: Text(S.of(context).ordrnow, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900)),
                                      ),
                                    ),
                                  ),
                          ]),
                        ],
                      ),
                    );
                  },
                );
              }),
            ),
          ],
        ),
      ),
    );
  }
}

double calculateTotal(List<CarItemHiveModel> cartItems) {
  double a = 0;
  for (final e in cartItems) { if (e.subProduct != null) { a += e.productsQTY * (e.unitPrice + e.subProduct!.price!); } else { a += e.productsQTY * e.unitPrice; } }
  return a;
}

class ChooseItemCard extends ConsumerStatefulWidget {
  const ChooseItemCard({super.key, required this.product, this.vendorId, this.vendorName});
  final Product product;
  final String? vendorId;
  final String? vendorName;
  @override
  ConsumerState<ChooseItemCard> createState() => _ChooseItemCardState();
}

class _ChooseItemCardState extends ConsumerState<ChooseItemCard> {
  String get effectiveVendorId => (widget.vendorId != null && widget.vendorId!.isNotEmpty) ? widget.vendorId! : (ref.read(activeVendorIdProvider) ?? 'default');
  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.appSettingsBox).listenable(),
      builder: (context, Box settingsBox, _) {
        return ValueListenableBuilder(
          valueListenable: Hive.box(AppHSC.cartBox).listenable(),
          builder: (context, Box cartsBox, _) {
            final vId = effectiveVendorId;
            final existing = CartHelper.findCartItem(vendorId: vId, productId: widget.product.id!);
            final inCart = existing != null;
            return Container(
              padding: EdgeInsets.all(12.w),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 12, offset: const Offset(0, 4))]),
              child: Row(children: [
                ClipRRect(borderRadius: BorderRadius.circular(14.r), child: AppNetworkImage(widget.product.imagePath, height: 74.h, width: 74.w, fit: BoxFit.cover)),
                SizedBox(width: 12.w),
                Expanded(
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Row(children: [
                      Expanded(child: Text(getLng(en: widget.product.name, changeLang: widget.product.nameBn.toString()), style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'), maxLines: 1, overflow: TextOverflow.ellipsis)),
                      if (widget.product.discountPercentage != null && (widget.product.discountPercentage as num) < 0) Container(padding: EdgeInsets.symmetric(horizontal: 6.w, vertical: 2.h), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(8.r)), child: Text('${widget.product.discountPercentage}%', style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w900, color: const Color(0xFFE63946)))),
                    ]),
                    SizedBox(height: 2.h),
                    if (true) Row(children: [Icon(Icons.store_rounded, size: 10.sp, color: Color(0xFF156172)), SizedBox(width: 4.w), Expanded(child: Text('محل: ${widget.product.vendorName ?? widget.vendorName ?? (widget.vendorId != null ? 'متجر المختار' : 'متاجر متعددة')}', style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w600, color: Color(0xFF156172)), maxLines: 1, overflow: TextOverflow.ellipsis))]),
                    SizedBox(height: 2.h),
                    Text(widget.product.description ?? '', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B)), maxLines: 1, overflow: TextOverflow.ellipsis),
                    SizedBox(height: 8.h),
                    Row(children: [
                      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        if (widget.product.oldPrice != null) Text('${settingsBox.get('currency') ?? '\$'}${widget.product.oldPrice}/${S.of(context).item}', style: TextStyle(fontSize: 10.sp, color: const Color(0xFFE63946), decoration: TextDecoration.lineThrough)),
                        Text('${settingsBox.get('currency') ?? '\$'}${AppGFunctions.convertToFixedTwo(widget.product.currentPrice!)}/${S.of(context).item}', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090))),
                      ])),
                      if (widget.product.sbproducts!.isNotEmpty) ...[
                        if (!inCart) SizedBox(height: 32.h, width: 108.w, child: OutlinedButton(onPressed: () => showModalBottomSheet(backgroundColor: Colors.transparent, context: context, isScrollControlled: true, builder: (_) => SubPrductBottomSheet(product: widget.product, vendorId: vId, vendorName: widget.vendorName)), style: OutlinedButton.styleFrom(side: const BorderSide(color: Color(0xFF00A896)), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10.r))), child: Text(S.of(context).showmore, style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))) ) else Builder(builder: (context) { final d = CartHelper.findCartItem(vendorId: vId, productId: widget.product.id!)!; return IncDecButtonWithValueV2(height: 32.h, width: 108.w, value: d.productsQTY, onDec: () { CartHelper.decrement(vendorId: vId, productId: widget.product.id!, subProductId: d.subProduct?.id ?? d.subproductsId); ref.refreshVendorCarts(); }, onInc: () { CartHelper.increment(vendorId: vId, productId: widget.product.id!, subProductId: d.subProduct?.id ?? d.subproductsId); ref.refreshVendorCarts(); }); }),
                      ] else ...[
                        inCart
                            ? Builder(builder: (context) { final d = CartHelper.findCartItem(vendorId: vId, productId: widget.product.id!)!; return IncDecButtonWithValueV2(height: 32.h, width: 108.w, value: d.productsQTY, onDec: () { CartHelper.decrement(vendorId: vId, productId: widget.product.id!, subProductId: d.subProduct?.id); ref.refreshVendorCarts(); }, onInc: () { CartHelper.increment(vendorId: vId, productId: widget.product.id!, subProductId: d.subProduct?.id); ref.refreshVendorCarts(); }); })
                            : SizedBox(height: 32.h, width: 88.w, child: DecoratedBox(decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(10.r)), child: ElevatedButton(onPressed: () { final n = CarItemHiveModel(productsId: widget.product.id!, productsName: widget.product.name!, productsImage: widget.product.imagePath!, productsQTY: 1, unitPrice: widget.product.currentPrice!, serviceName: widget.product.service!.name!, vendorId: vId, vendorName: widget.vendorName); CartHelper.addItem(vId, n); ref.refreshVendorCarts(); }, style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10.r))), child: Text(S.of(context).additm, style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w900, color: Colors.white))))),
                      ],
                    ]),
                  ]),
                ),
              ]),
            );
          },
        );
      },
    );
  }
}

class IncDecButton extends StatelessWidget {
  const IncDecButton({super.key, required this.ontap, required this.icon});
  final Function() ontap;
  final IconData icon;
  @override
  Widget build(BuildContext context) {
    return GestureDetector(onTap: ontap, child: Container(width: 36.w, height: 36.h, decoration: BoxDecoration(borderRadius: BorderRadius.circular(12), color: const Color(0xFFEFFAF8)), child: Center(child: Icon(icon, color: const Color(0xFF028090)))));
  }
}

