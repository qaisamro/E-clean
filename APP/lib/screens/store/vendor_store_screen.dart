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
import 'package:laundry_customer/models/products_model/product.dart';
import 'package:laundry_customer/models/products_model/sub_product.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/models/variations_model/variant.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/order_update_provider.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/buttons/cart_item_inc_dec_button.dart';
import 'package:laundry_customer/widgets/buttons/full_width_button.dart';
import 'package:laundry_customer/widgets/global_functions.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class VendorStoreScreen extends ConsumerStatefulWidget {
  const VendorStoreScreen({super.key, required this.vendor});
  final Vendor vendor;
  @override
  ConsumerState<VendorStoreScreen> createState() => _VendorStoreScreenState();
}

class _VendorStoreScreenState extends ConsumerState<VendorStoreScreen> {
  String? selectedServiceId;
  int selectedVariantIndex = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(activeVendorIdProvider.notifier).state = widget.vendor.id.toString();
      ref.read(selectedVendorProvider.notifier).state = widget.vendor;
    });
  }

  @override
  Widget build(BuildContext context) {
    final vendorId = widget.vendor.id.toString();
    final vendorServicesAsync = ref.watch(vendorServicesProvider(vendorId));
    final appSettingsBox = Hive.box(AppHSC.appSettingsBox);
    int? minimum = 0; double? dlvrychrg = 0; double? free = 0;
    ref.watch(settingsProvider).whenOrNull(loaded: (d) { minimum = d.data!.minimumCost; dlvrychrg = d.data!.deliveryCost!.toDouble(); free = d.data!.feeCost!.toDouble(); });
    ref.watch(vendorCartsProvider);
    final vendorCartItems = CartHelper.getCartForVendor(vendorId);

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: Stack(
        children: [
          CustomScrollView(
            slivers: [
              SliverToBoxAdapter(
                child: Container(
                  padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 16.h),
                  decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.vertical(bottom: Radius.circular(24))),
                  child: SafeArea(
                    bottom: false,
                    child: Column(
                      children: [
                        Row(children: [
                          InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: Colors.white24)), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Colors.white))),
                          SizedBox(width: 12.w),
                          Expanded(child: Text(widget.vendor.displayName, style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo'), maxLines: 1)),
                        ]),
                        SizedBox(height: 14.h),
                        Container(
                          padding: EdgeInsets.all(12.w),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 14)]),
                          child: Row(children: [
                            ClipRRect(borderRadius: BorderRadius.circular(14.r), child: AppNetworkImage(widget.vendor.displayImage, width: 56.w, height: 56.h, fit: BoxFit.cover)),
                            SizedBox(width: 12.w),
                            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                              Text(widget.vendor.displayName, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E)), maxLines: 1),
                              SizedBox(height: 4.h),
                              Row(children: [Icon(Icons.location_on_rounded, size: 13.sp, color: const Color(0xFF00A896)), SizedBox(width: 4.w), Expanded(child: Text(widget.vendor.displayAddress.isNotEmpty ? widget.vendor.displayAddress : 'لا يوجد عنوان', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B)), maxLines: 1))]),
                            ])),
                          ]),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: vendorServicesAsync.map(
                  initial: (_) => const SizedBox(), loading: (_) => Padding(padding: EdgeInsets.only(top: 20), child: Center(child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))), 
                  loaded: (_) {
                    final services = _.data.data?.services ?? [];
                    if (services.isEmpty) return Padding(padding: EdgeInsets.only(top: 30.h), child: Center(child: Text(S.of(context).nosrvcavlbl, style: TextStyle(color: const Color(0xFF64748B)))));
                    if (selectedServiceId == null && services.isNotEmpty) {
                      selectedServiceId = services.first.id.toString();
                      Future.microtask(() => ref.read(vendorProductFilterProvider(vendorId).notifier).state = VendorProductFilter(vendorId: vendorId, serviceId: selectedServiceId!, variantId: ''));
                    }
                    return Column(children: [
                      SizedBox(
                        height: 102.h,
                        child: ListView.separated(
                          padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 10.h),
                          scrollDirection: Axis.horizontal,
                          itemCount: services.length,
                          separatorBuilder: (_, __) => SizedBox(width: 10.w),
                          itemBuilder: (context, i) {
                            final svc = services[i];
                            final isSel = selectedServiceId == svc.id.toString();
                            return InkWell(
                              borderRadius: BorderRadius.circular(16.r),
                              onTap: () {
                                setState(() { selectedServiceId = svc.id.toString(); selectedVariantIndex = 0; });
                                ref.invalidate(servicesVariationsProvider(svc.id.toString()));
                                ref.read(vendorProductFilterProvider(vendorId).notifier).state = VendorProductFilter(vendorId: vendorId, serviceId: svc.id.toString(), variantId: '');
                              },
                              child: AnimatedContainer(
                                duration: const Duration(milliseconds: 200),
                                width: 88.w,
                                padding: EdgeInsets.all(8.w),
                                decoration: BoxDecoration(color: isSel ? const Color(0xFF028090) : Colors.white, borderRadius: BorderRadius.circular(16.r), border: Border.all(color: isSel ? const Color(0xFF028090) : const Color(0xFFE2E8F0)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(isSel ? 0.12 : 0.05), blurRadius: 10)]),
                                child: Column(mainAxisSize: MainAxisSize.min, mainAxisAlignment: MainAxisAlignment.center, children: [
                                  ClipRRect(borderRadius: BorderRadius.circular(10.r), child: AppNetworkImage(svc.imagePath, height: 42.h, width: 42.w, fit: BoxFit.cover)),
                                  SizedBox(height: 6.h),
                                  Flexible(child: Text(getLng(en: svc.name, changeLang: svc.nameBn.toString()), style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w800, color: isSel ? Colors.white : const Color(0xFF334155)), maxLines: 1, textAlign: TextAlign.center, overflow: TextOverflow.ellipsis)),
                                ]),
                              ),
                            );
                          },
                        ),
                      ),
                      if (selectedServiceId != null)
                        Consumer(builder: (context, ref, _) {
                          final varAsync = ref.watch(servicesVariationsProvider(selectedServiceId!));
                          return varAsync.map(
                            initial: (_) => const SizedBox(height: 42), loading: (_) => SizedBox(height: 42.h, child: const Center(child: SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090))))),
                            loaded: (vd) {
                              final vars = vd.data.data?.variants ?? [];
                              if (vars.isEmpty) return const SizedBox();
                              vars.sort((a, b) => a.id!.compareTo(b.id!));
                              final cur = ref.watch(vendorProductFilterProvider(vendorId));
                              if (cur.variantId.isEmpty) Future.microtask(() => ref.read(vendorProductFilterProvider(vendorId).notifier).state = cur.copyWith(variantId: vars.first.id.toString()));
                              return SizedBox(
                                height: 48.h,
                                child: ListView.builder(
                                  scrollDirection: Axis.horizontal, padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 4.h),
                                  itemCount: vars.length,
                                  itemBuilder: (context, idx) {
                                    final Variant v = vars[idx];
                                    final isActive = selectedVariantIndex == idx;
                                    return Padding(
                                      padding: EdgeInsets.only(right: 8.w),
                                      child: InkWell(
                                        borderRadius: BorderRadius.circular(20.r),
                                        onTap: () { setState(() => selectedVariantIndex = idx); ref.read(vendorProductFilterProvider(vendorId).notifier).state = ref.read(vendorProductFilterProvider(vendorId)).copyWith(variantId: v.id.toString()); },
                                        child: Container(padding: EdgeInsets.symmetric(horizontal: 16.w), decoration: BoxDecoration(color: isActive ? const Color(0xFF028090) : Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: isActive ? const Color(0xFF028090) : const Color(0xFFE2E8F0))), child: Center(child: Text(getLng(en: v.name, changeLang: v.nameBn.toString()), style: TextStyle(fontSize: 12.sp, fontWeight: isActive ? FontWeight.w900 : FontWeight.w700, color: isActive ? Colors.white : const Color(0xFF334155))))),
                                      ),
                                    );
                                  },
                                ),
                              );
                            },
                            error: (_) => Center(child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)))),
                          );
                        }),
                      SizedBox(height: 8.h),
                    ]);
                  },
                  error: (_) => Center(child: Text(_.error)),
                ),
              ),
              if (selectedServiceId != null)
                Consumer(builder: (context, ref, _) {
                  final filter = ref.watch(vendorProductFilterProvider(vendorId));
                  if (filter.serviceId.isEmpty) return SliverToBoxAdapter(child: Padding(padding: EdgeInsets.only(top: 30.h), child: Center(child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))));
                  final prodAsync = ref.watch(vendorProductsProvider(filter));
                  return prodAsync.map(
                    initial: (_) => SliverToBoxAdapter(child: Padding(padding: EdgeInsets.only(top: 30.h), child: Center(child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090))))),
                    loading: (_) => SliverToBoxAdapter(child: Padding(padding: EdgeInsets.only(top: 30.h), child: Center(child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090))))),
                    loaded: (pd) {
                      final prods = pd.data.data?.products ?? [];
                      if (prods.isEmpty) return SliverToBoxAdapter(child: Padding(padding: EdgeInsets.only(top: 40.h), child: Center(child: Text('لا توجد منتجات', style: TextStyle(color: const Color(0xFF64748B))))));
                      return SliverPadding(
                        padding: EdgeInsets.fromLTRB(16.w, 4.h, 16.w, 120.h),
                        sliver: SliverList.builder(itemCount: prods.length, itemBuilder: (context, i) => Padding(padding: EdgeInsets.only(bottom: 12.h), child: _VendorProductCard(product: prods[i], vendor: widget.vendor))),
                      );
                    },
                    error: (e) => SliverToBoxAdapter(child: Center(child: Text(e.error))),
                  );
                }),
            ],
          ),
          if (vendorCartItems.isNotEmpty)
            Positioned(
              bottom: 0, left: 0, right: 0,
              child: Container(
                padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 12.h + MediaQuery.of(context).viewPadding.bottom),
                decoration: BoxDecoration(color: Colors.white, borderRadius: const BorderRadius.vertical(top: Radius.circular(20)), border: Border(top: BorderSide(color: const Color(0xFFE2E8F0))), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.10), blurRadius: 18, offset: const Offset(0, -6))]),
                child: Row(crossAxisAlignment: CrossAxisAlignment.center, children: [
                  Expanded(child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
                    Text(S.of(context).ttl, style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))),
                    Text('${appSettingsBox.get('currency') ?? '\$'}${CartHelper.calculateTotalForVendor(vendorId).toStringAsFixed(2)}', style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))),
                    Flexible(child: Text('${vendorCartItems.length} عناصر • ${widget.vendor.displayName}', style: TextStyle(fontSize: 10.sp, color: const Color(0xFF94A3B8)), maxLines: 1, overflow: TextOverflow.ellipsis)),
                  ])),
                  SizedBox(width: 12.w),
                  SizedBox(
                    height: 44.h, width: 140.w,
                    child: DecoratedBox(
                      decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(12.r)),
                      child: ElevatedButton(
                        onPressed: () {
                          final authBox = Hive.box(AppHSC.authBox);
                          if (authBox.get(AppHSC.authToken) == null || authBox.get(AppHSC.authToken) == '') { context.nav.pushNamed(Routes.loginScreen); return; }
                          if (CartHelper.calculateTotalForVendor(vendorId) >= (minimum ?? 0)) { ref.read(activeVendorIdProvider.notifier).state = vendorId; context.nav.pushNamed(Routes.checkOutScreen); } else { EasyLoading.showError('${S.of(context).mnmmordramnt} ${AppGFunctions.convertToFixedTwo(minimum!)}'); }
                        },
                        style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))),
                        child: Text(S.of(context).ordrnow, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900)),
                      ),
                    ),
                  ),
                ]),
              ),
            ),
        ],
      ),
    );
  }
}

class _VendorProductCard extends ConsumerStatefulWidget {
  const _VendorProductCard({required this.product, required this.vendor});
  final Product product;
  final Vendor vendor;
  @override
  ConsumerState<_VendorProductCard> createState() => _VendorProductCardState();
}

class _VendorProductCardState extends ConsumerState<_VendorProductCard> {
  @override
  Widget build(BuildContext context) {
    final vendorId = widget.vendor.id.toString();
    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.cartBox).listenable(),
      builder: (context, Box cartBox, _) {
        final items = CartHelper.getCartForVendor(vendorId);
        bool inCart = false; CarItemHiveModel? cartItem;
        for (final it in items) { if (it.productsId == widget.product.id) { inCart = true; cartItem = it; break; } if (it.subProduct != null && widget.product.sbproducts != null && widget.product.sbproducts!.any((sp) => sp.id == it.subProduct!.id || sp.id == it.productsId)) { inCart = true; cartItem = it; break; } }
        final exact = CartHelper.findCartItem(vendorId: vendorId, productId: widget.product.id!);
        final displayInCart = exact != null || inCart;
        final effective = exact ?? cartItem;
        return Container(
          padding: EdgeInsets.all(12.w),
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 12, offset: const Offset(0, 4))]),
          child: Row(children: [
            ClipRRect(borderRadius: BorderRadius.circular(14.r), child: AppNetworkImage(widget.product.imagePath, height: 72.h, width: 72.w, fit: BoxFit.cover)),
            SizedBox(width: 12.w),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Row(children: [Expanded(child: Text(getLng(en: widget.product.name, changeLang: widget.product.nameBn.toString()), style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'), maxLines: 1, overflow: TextOverflow.ellipsis)), if (widget.product.discountPercentage != null && (widget.product.discountPercentage as num) < 0) Container(padding: EdgeInsets.symmetric(horizontal: 6.w, vertical: 2.h), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(8.r)), child: Text('${widget.product.discountPercentage}%', style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w900, color: const Color(0xFFE63946))))]),
              SizedBox(height: 2.h),
              Text(widget.product.description ?? '', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B)), maxLines: 1, overflow: TextOverflow.ellipsis),
              SizedBox(height: 8.h),
              Row(children: [
                Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  if (widget.product.oldPrice != null) Text('${Hive.box(AppHSC.appSettingsBox).get('currency') ?? '\$'}${widget.product.oldPrice}/${S.of(context).item}', style: TextStyle(fontSize: 10.sp, color: const Color(0xFFE63946), decoration: TextDecoration.lineThrough)),
                  Text('${Hive.box(AppHSC.appSettingsBox).get('currency') ?? '\$'}${AppGFunctions.convertToFixedTwo(widget.product.currentPrice!)}/${S.of(context).item}', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090))),
                ])),
                if (widget.product.sbproducts != null && widget.product.sbproducts!.isNotEmpty) ...[
                  if (!displayInCart) SizedBox(height: 32.h, width: 108.w, child: OutlinedButton(onPressed: () => showModalBottomSheet(backgroundColor: Colors.transparent, context: context, isScrollControlled: true, builder: (_) => _VendorSubProductSheet(product: widget.product, vendor: widget.vendor)), style: OutlinedButton.styleFrom(side: const BorderSide(color: Color(0xFF00A896)), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10.r))), child: Text(S.of(context).showmore, style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))) ) else IncDecButtonWithValueV2(height: 32.h, width: 108.w, value: effective?.productsQTY ?? 1, onDec: () { if (effective == null) return; CartHelper.decrement(vendorId: vendorId, productId: effective.productsId, subProductId: effective.subProduct?.id ?? effective.subproductsId); ref.refreshVendorCarts(); }, onInc: () { if (effective == null) return; CartHelper.increment(vendorId: vendorId, productId: effective.productsId, subProductId: effective.subProduct?.id ?? effective.subproductsId); ref.refreshVendorCarts(); }),
                ] else ...[
                  displayInCart ? IncDecButtonWithValueV2(height: 32.h, width: 108.w, value: effective!.productsQTY, onDec: () { CartHelper.decrement(vendorId: vendorId, productId: effective.productsId, subProductId: effective.subProduct?.id); ref.refreshVendorCarts(); }, onInc: () { CartHelper.increment(vendorId: vendorId, productId: effective.productsId, subProductId: effective.subProduct?.id); ref.refreshVendorCarts(); }) : SizedBox(height: 32.h, width: 86.w, child: DecoratedBox(decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(10.r)), child: ElevatedButton(onPressed: () { final n = CarItemHiveModel(productsId: widget.product.id!, productsName: widget.product.name!, productsImage: widget.product.imagePath!, productsQTY: 1, unitPrice: widget.product.currentPrice!, serviceName: widget.product.service?.name ?? '', vendorId: vendorId, vendorName: widget.vendor.displayName); CartHelper.addItem(vendorId, n); ref.refreshVendorCarts(); }, style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10.r))), child: Text(S.of(context).additm, style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w900, color: Colors.white))))),
                ],
              ]),
            ])),
          ]),
        );
      },
    );
  }
}

class _VendorSubProductSheet extends ConsumerStatefulWidget {
  const _VendorSubProductSheet({required this.product, required this.vendor});
  final Product product; final Vendor vendor;
  @override
  ConsumerState<_VendorSubProductSheet> createState() => _VendorSubProductSheetState();
}

class _VendorSubProductSheetState extends ConsumerState<_VendorSubProductSheet> {
  @override
  Widget build(BuildContext context) {
    final vendorId = widget.vendor.id.toString();
    final settingsBox = Hive.box(AppHSC.appSettingsBox);
    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.cartBox).listenable(),
      builder: (context, Box cartBox, _) {
        return Container(
          decoration: const BoxDecoration(color: Colors.white, borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
          padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 16.h + MediaQuery.of(context).viewPadding.bottom),
          child: Column(mainAxisSize: MainAxisSize.min, crossAxisAlignment: CrossAxisAlignment.start, children: [
            Center(child: Container(width: 40.w, height: 4.h, decoration: BoxDecoration(color: const Color(0xFFE2E8F0), borderRadius: BorderRadius.circular(2.r)))),
            SizedBox(height: 14.h),
            Text(S.of(context).slctvarient, style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))),
            Text(widget.product.name ?? '', style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
            SizedBox(height: 12.h),
            SizedBox(
              height: 280.h,
              child: ListView.builder(
                itemCount: widget.product.sbproducts!.length,
                itemBuilder: (context, i) {
                  final sp = widget.product.sbproducts![i];
                  final cartItem = CartHelper.findCartItem(vendorId: vendorId, productId: sp.id ?? 0, subProductId: sp.id);
                  final alt = CartHelper.getCartForVendor(vendorId).where((e) => e.subProduct?.id == sp.id || e.productsId == sp.id).firstOrNull;
                  final eff = cartItem ?? alt; final inCart = eff != null;
                  return Container(
                    margin: EdgeInsets.only(bottom: 8.h), padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 12.h),
                    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(14.r), border: Border.all(color: const Color(0xFFE2E8F0))),
                    child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                      Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(sp.name ?? 'Default', style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w800, color: const Color(0xFF0B1E2E))), Text("${settingsBox.get('currency') ?? '\$'} ${sp.price}", style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))]),
                      inCart ? IncDecButtonWithValueV2(height: 32.h, width: 110.w, value: eff!.productsQTY, onDec: () { CartHelper.decrement(vendorId: vendorId, productId: eff.productsId, subProductId: eff.subProduct?.id ?? eff.subproductsId); ref.refreshVendorCarts(); }, onInc: () { CartHelper.increment(vendorId: vendorId, productId: eff.productsId, subProductId: eff.subProduct?.id ?? eff.subproductsId); ref.refreshVendorCarts(); }) : SizedBox(height: 32.h, width: 96.w, child: DecoratedBox(decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(10.r)), child: ElevatedButton(onPressed: () { final n = CarItemHiveModel(productsId: sp.id ?? 0, subproductsId: sp.id, productsName: sp.name ?? widget.product.name ?? '', productsImage: widget.product.imagePath ?? '', productsQTY: 1, unitPrice: (sp.price ?? 0).toDouble(), serviceName: widget.product.service?.name ?? '', subProduct: sp, vendorId: vendorId, vendorName: widget.vendor.displayName); CartHelper.addItem(vendorId, n); ref.refreshVendorCarts(); }, style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10.r))), child: Text(S.of(context).additm, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900))))),
                    ]),
                  );
                },
              ),
            ),
            SizedBox(height: 12.h),
            ValueListenableBuilder(valueListenable: Hive.box(AppHSC.cartBox).listenable(), builder: (context, Box b, _) => Text("${settingsBox.get('currency') ?? '\$'}${CartHelper.calculateTotalForVendor(vendorId).toStringAsFixed(2)}", style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090)))),
            SizedBox(height: 12.h),
            SizedBox(width: double.infinity, height: 48.h, child: DecoratedBox(decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(14.r)), child: ElevatedButton(onPressed: () => context.nav.pop(), style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14.r))), child: Text(S.of(context).cnfirm, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900))))),
          ]),
        );
      },
    );
  }
}

extension _FirstOrNull<E> on Iterable<E> { E? get firstOrNull => isEmpty ? null : first; }
