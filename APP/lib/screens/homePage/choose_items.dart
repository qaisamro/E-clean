import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/products_model/product.dart';
import 'package:laundry_customer/models/products_model/sub_product.dart';
import 'package:laundry_customer/notfiers/guest_notfiers.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/screens/homePage/subProductBottomSheet/sub_product_bottom_sheet.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/cart_single_vendor_guard.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/buttons/cart_item_inc_dec_button.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';
import 'package:laundry_customer/widgets/product_price_block.dart';

/// Total value of the given cart items, including each item's sub-product
/// price. Shared helper used by the cart, the checkout sheet and this screen.
double calculateTotal(List<CarItemHiveModel> cartItems) {
  double total = 0;
  for (final item in cartItems) {
    final double extra = item.subProduct?.price?.toDouble() ?? 0;
    total += item.productsQTY * (item.unitPrice + extra);
  }
  return total;
}

/// Choose-items screen for a service.
///
/// Rebuilt to the same behaviour as the previous implementation: it lists the
/// service's products (respecting the selected variation), lets the customer
/// pick variants via the shared sub-product bottom sheet and keeps every
/// action inside the Hive-backed multi-vendor cart.
class ChooseItems extends ConsumerStatefulWidget {
  const ChooseItems({
    super.key,
    required this.service,
    this.vendorId,
    this.vendorName,
  });

  final Service service;
  final String? vendorId;
  final String? vendorName;

  @override
  ConsumerState<ChooseItems> createState() => _ChooseItemsState();
}

class _ChooseItemsState extends ConsumerState<ChooseItems> {
  final Box _settingsBox = Hive.box(AppHSC.appSettingsBox);

  String get _vendorId {
    if (widget.vendorId != null && widget.vendorId!.isNotEmpty) {
      return widget.vendorId!;
    }
    return ref.read(activeVendorIdProvider) ?? 'default';
  }

  @override
  void initState() {
    super.initState();
    // Scope every product request to this service (+ selected variation).
    Future.microtask(() {
      ref.read(productsFilterProvider.notifier).state =
          ProducServiceVariavtionDataModel(
        servieID: (widget.service.id ?? '').toString(),
        variationID: ref.read(productsFilterProvider).variationID,
      );
      ref.invalidate(productsProvider);
    });
  }

  @override
  Widget build(BuildContext context) {
    final String vendorId = _vendorId;
    ref.watch(vendorCartsProvider); // rebuild on cart mutations

    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0.5,
        centerTitle: true,
        iconTheme: const IconThemeData(color: Color(0xFF028090)),
        title: Text(
          widget.service.name ?? 'الخدمة',
          style: TextStyle(
              fontSize: 15.sp,
              fontWeight: FontWeight.w900,
              color: const Color(0xFF0B1E2E),
              fontFamily: 'Cairo'),
        ),
      ),
      body: Column(
        children: [
          Expanded(
            child: Consumer(builder: (context, ref, _) {
              return ref.watch(productsProvider).map(
                    initial: (_) => const SizedBox.shrink(),
                    loading: (_) => const Center(
                        child: LoadingWidget(size: 110, message: 'جاري تحميل العناصر...')),
                    loaded: (loaded) {
                      final products = loaded.data.data?.products ?? [];
                      if (products.isEmpty) {
                        return const Center(child: Text('لا توجد عناصر لهذه الخدمة'));
                      }
                      return ListView.separated(
                        padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 90.h),
                        itemCount: products.length,
                        separatorBuilder: (_, __) => AppSpacerH(10.h),
                        itemBuilder: (context, index) =>
                            _productCard(products[index], vendorId),
                      );
                    },
                    error: (err) => Center(child: Text(err.error)),
                  );
            }),
          ),
          _bottomBar(vendorId),
        ],
      ),
    );
  }

  Widget _productCard(Product product, String vendorId) {
    final bool hasVariants =
        (product.sbproducts?.isNotEmpty ?? false) || product.variant != null;

    return Container(
      padding: EdgeInsets.all(12.w),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18.r),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(14.r),
            child: SizedBox(
              width: 76.w,
              height: 76.w,
              child: (product.imagePath?.isNotEmpty ?? false)
                  ? AppNetworkImage(product.imagePath!,
                      fit: BoxFit.cover,
                      width: 76.w,
                      height: 76.w,
                      placeholder: 'assets/images/app_icon.png')
                  : Container(
                      color: const Color(0xFFEAF6F8),
                      child: const Icon(Icons.local_laundry_service_rounded,
                          color: Color(0xFF156172)),
                    ),
            ),
          ),
          AppSpacerW(12.w),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(product.name ?? '',
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                        fontSize: 13.sp,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF0B1E2E))),
                AppSpacerH(4.h),
                ProductPriceBlock(
                  currency: _settingsBox.get('currency')?.toString() ?? '\$',
                  currentPrice:
                      (product.currentPrice ?? product.oldPrice ?? 0).toDouble(),
                  oldPrice: product.oldPrice?.toDouble(),
                ),
                if (widget.vendorName != null) ...[
                  AppSpacerH(2.h),
                  Text(widget.vendorName!,
                      style: TextStyle(
                          fontSize: 10.5.sp, color: const Color(0xFF64748B))),
                ],
                AppSpacerH(8.h),
                if (hasVariants)
                  SizedBox(
                    height: 36.h,
                    width: 120.w,
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF028090),
                        elevation: 0,
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(10.r)),
                      ),
                      onPressed: () => _openVariantSheet(product, vendorId),
                      child: const Text('اختر الخيار',
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 12,
                              fontWeight: FontWeight.w800)),
                    ),
                  )
                else
                  _addButton(product, vendorId, null),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _addButton(Product product, String vendorId, SubProduct? sub) {
    final existing = CartHelper.findCartItem(
      vendorId: vendorId,
      productId: product.id ?? sub?.id ?? 0,
      subProductId: sub?.id,
    );

    if (existing != null) {
      return IncDecButtonWithValueV2(
        height: 36.h,
        width: 120.w,
        value: existing.productsQTY,
        onDec: () {
          CartHelper.decrement(
            vendorId: vendorId,
            productId: existing.productsId,
            subProductId: existing.subProduct?.id ?? existing.subproductsId,
          );
          ref.refreshVendorCarts();
        },
        onInc: () {
          CartHelper.increment(
            vendorId: vendorId,
            productId: existing.productsId,
            subProductId: existing.subProduct?.id ?? existing.subproductsId,
          );
          ref.refreshVendorCarts();
        },
      );
    }

    return SizedBox(
      height: 36.h,
      width: 120.w,
      child: ElevatedButton(
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.white,
          elevation: 0,
          side: const BorderSide(color: Color(0xFF028090)),
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(10.r)),
        ),
        onPressed: () async {
          final double unit = sub != null
              ? (sub.price ?? 0).toDouble()
              : (product.currentPrice ?? product.oldPrice ?? 0).toDouble();
          final allowed = await ensureSingleVendorOrConfirmClear(
            context: context,
            ref: ref,
            vendorId: vendorId,
          );
          if (!allowed) return;
          CartHelper.addItem(
            vendorId,
            CarItemHiveModel(
              productsId: product.id ?? sub?.id ?? 0,
              subproductsId: sub?.id,
              productsQTY: 1,
              unitPrice: unit,
              productsName: sub?.name ?? product.name ?? '',
              productsImage: product.imagePath ?? '',
              serviceName: widget.service.name ?? '',
              subProduct: sub,
              vendorId: vendorId,
              vendorName: widget.vendorName,
            ),
          );
          ref.refreshVendorCarts();
        },
        child: const Text('أضف',
            style: TextStyle(
                color: Color(0xFF028090),
                fontSize: 12,
                fontWeight: FontWeight.w800)),
      ),
    );
  }

  Future<void> _openVariantSheet(Product product, String vendorId) async {
    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => SubPrductBottomSheet(
        product: product,
        vendorId: vendorId,
        vendorName: widget.vendorName,
      ),
    );
    if (!mounted) return;
    ref.refreshVendorCarts();
  }

  Widget _bottomBar(String vendorId) {
    final items = CartHelper.getCartForVendor(vendorId);
    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.cartBox).listenable(),
      builder: (context, Box box, _) {
        final double total = calculateTotal(items);
        return Container(
          padding: EdgeInsets.all(16.w),
          decoration: BoxDecoration(
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                  color: const Color(0xFF0C2238).withOpacity(0.08),
                  blurRadius: 16,
                  offset: const Offset(0, -4))
            ],
          ),
          child: SafeArea(
            top: false,
            child: Row(
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text('${items.length} عنصر في السلة',
                          style: TextStyle(
                              fontSize: 11.sp,
                              color: const Color(0xFF64748B))),
                      AppSpacerH(2.h),
                      Text(
                          '${_settingsBox.get('currency') ?? '\$'} ${total.toStringAsFixed(2)}',
                          style: TextStyle(
                              fontSize: 15.sp,
                              fontWeight: FontWeight.w900,
                              color: const Color(0xFF0B1E2E))),
                    ],
                  ),
                ),
                SizedBox(
                  height: 46.h,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF028090),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14.r)),
                    ),
                    onPressed: items.isEmpty ? null : () => Navigator.pop(context),
                    child: const Text('متابعة',
                        style: TextStyle(
                            color: Colors.white, fontWeight: FontWeight.w800)),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
