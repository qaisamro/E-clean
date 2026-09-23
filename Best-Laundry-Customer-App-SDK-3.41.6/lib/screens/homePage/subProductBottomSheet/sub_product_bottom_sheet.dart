// ignore_for_file: public_member_api_docs, sort_constructors_first
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/app_text_decor.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/products_model/product.dart';
import 'package:laundry_customer/models/products_model/sub_product.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/screens/homePage/choose_items.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/widgets/buttons/cart_item_inc_dec_button.dart';
import 'package:laundry_customer/widgets/buttons/full_width_button.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class SubPrductBottomSheet extends ConsumerStatefulWidget {
  const SubPrductBottomSheet({
    super.key,
    required this.product,
    this.vendorId,
    this.vendorName,
  });
  final Product product;
  final String? vendorId;
  final String? vendorName;

  @override
  ConsumerState<SubPrductBottomSheet> createState() =>
      _SubPrductBottomSheetState();
}

class _SubPrductBottomSheetState extends ConsumerState<SubPrductBottomSheet> {
  final settingsBox = Hive.box(AppHSC.appSettingsBox);
  bool inCart = false;

  String get effectiveVendorId {
    if (widget.vendorId != null && widget.vendorId!.isNotEmpty) {
      return widget.vendorId!;
    }
    return ref.read(activeVendorIdProvider) ?? 'default';
  }

  @override
  Widget build(BuildContext context) {
    final String vId = effectiveVendorId;
    final String? vName = widget.vendorName;
    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.cartBox).listenable(),
      builder: (
        BuildContext context,
        Box cartsBox,
        Widget? child,
      ) {
        return Stack(
          children: [
            Container(
              width: MediaQuery.of(context).size.width,
              decoration: BoxDecoration(
                color: AppColors.white,
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(8),
                  topRight: Radius.circular(8),
                ),
                boxShadow: [
                  BoxShadow(
                    color: AppColors.black.withOpacity(0.1),
                    blurRadius: 24,
                    spreadRadius: 16,
                  ),
                ],
              ),
              child: Padding(
                padding: EdgeInsets.symmetric(
                  horizontal: 16.h,
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    AppSpacerH(16.h),
                    Text(
                      S.of(context).slctvarient,
                      style: AppTextDecor.osRegular14Navy,
                    ),
                    AppSpacerH(8.h),
                    Text(
                      widget.product.name!,
                      style: AppTextDecor.osBold24black,
                    ),
                    if (vName != null)
                      Padding(
                        padding: EdgeInsets.only(top: 4.h),
                        child: Text(
                          vName,
                          style: AppTextDecor.osRegular12black.copyWith(
                            color: AppColors.primary,
                            fontSize: 11.sp,
                          ),
                        ),
                      ),
                    AppSpacerH(16.h),
                    SizedBox(
                      width: MediaQuery.of(
                        context,
                      ).size.width,
                      height: 270.h,
                      child: ListView.builder(
                        padding: EdgeInsets.zero,
                        shrinkWrap: true,
                        itemCount: widget.product.sbproducts!.length,
                        itemBuilder: (
                          context,
                          index,
                        ) {
                          final length = widget.product.sbproducts!.length;
                          return buildSubPoduct(
                            context,
                            length,
                            widget.product.sbproducts![index],
                            vId,
                            vName,
                          );
                        },
                      ),
                    ),
                    AppSpacerH(8.h),
                    ValueListenableBuilder(
                      valueListenable: Hive.box(
                        AppHSC.cartBox,
                      ).listenable(),
                      builder: (context, Box cartBox, child) {
                        final total = CartHelper.calculateTotalForVendor(vId);
                        // Fallback to global if scoped empty and default vendor was used
                        final displayTotal = total > 0
                            ? total
                            : calculateTotal(CartHelper.getAllItems());
                        return Text(
                          "${settingsBox.get('currency') ?? '\$'}${displayTotal.toStringAsFixed(2)}",
                          style: AppTextDecor.osBold24black.copyWith(
                            color: AppColors.purple,
                          ),
                        );
                      },
                    ),
                    AppSpacerH(16.h),
                    AppTextButton(
                      onTap: () {
                        context.nav.pop(context);
                      },
                      title: S.of(context).cnfirm,
                      buttonColor: AppColors.purple,
                    ),
                    AppSpacerH(16.h),
                  ],
                ),
              ),
            ),
            Positioned(
              top: 0,
              left: 150,
              right: 150,
              child: Container(
                height: 4.h,
                decoration: const BoxDecoration(
                  color: AppColors.gray,
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(10),
                    bottomRight: Radius.circular(10),
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
  }

  Container buildSubPoduct(
    BuildContext context,
    int length,
    SubProduct subProduct,
    String vendorId,
    String? vendorName,
  ) {
    return Container(
      margin: const EdgeInsets.only(
        bottom: 4,
      ),
      width: MediaQuery.of(
        context,
      ).size.width,
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(
          8,
        ),
        border: Border.all(
          color: AppColors.gray,
        ),
      ),
      child: Padding(
        padding: EdgeInsets.symmetric(
          horizontal: 16.h,
          vertical: 14.h,
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  length != 0 ? subProduct.name ?? '' : "Default",
                  style: AppTextDecor.osBold14black,
                ),
                Text(
                  "${settingsBox.get('currency')} ${subProduct.price}",
                  style: AppTextDecor.osSemiBold12black.copyWith(
                    color: AppColors.purple,
                  ),
                ),
              ],
            ),
            ValueListenableBuilder(
              valueListenable: Hive.box(
                AppHSC.cartBox,
              ).listenable(),
              builder: (
                context,
                Box cartbox,
                Widget? child,
              ) {
                final existing = CartHelper.findCartItem(
                  vendorId: vendorId,
                  productId: subProduct.id ?? 0,
                  subProductId: subProduct.id,
                );
                final bool inCart = existing != null;

                if (inCart) {
                  return Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      IncDecButtonWithValueV2(
                        height: 36.h,
                        width: 120.w,
                        value: existing.productsQTY,
                        onDec: () {
                          CartHelper.decrement(
                            vendorId: vendorId,
                            productId: existing.productsId,
                            subProductId: existing.subProduct?.id ??
                                existing.subproductsId,
                          );
                          ref.refreshVendorCarts();
                        },
                        onInc: () {
                          CartHelper.increment(
                            vendorId: vendorId,
                            productId: existing.productsId,
                            subProductId: existing.subProduct?.id ??
                                existing.subproductsId,
                          );
                          ref.refreshVendorCarts();
                        },
                      ),
                    ],
                  );
                } else {
                  return Row(
                    children: [
                      AppTextButton(
                        title: S.of(context).additm,
                        width: 120.w,
                        height: 36.h,
                        onTap: () {
                          final newItem = CarItemHiveModel(
                            productsId: subProduct.id ?? 0,
                            subproductsId: subProduct.id,
                            productsName: subProduct.name ?? '',
                            productsImage: widget.product.imagePath!,
                            productsQTY: 1,
                            unitPrice: (subProduct.price ?? 0).toDouble(),
                            serviceName: widget.product.service!.name!,
                            subProduct: subProduct,
                            vendorId: vendorId,
                            vendorName: vendorName,
                          );
                          CartHelper.addItem(vendorId, newItem);
                          ref.refreshVendorCarts();
                        },
                      ),
                    ],
                  );
                }
              },
            ),
          ],
        ),
      ),
    );
  }
}
