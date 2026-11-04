import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/app_text_decor.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/buttons/cart_item_inc_dec_button.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class MyCartItemImageCard extends ConsumerStatefulWidget {
  const MyCartItemImageCard({
    super.key,
    required this.carItemHiveModel,
    this.vendorId,
  });
  final CarItemHiveModel carItemHiveModel;
  final String? vendorId;

  @override
  ConsumerState<ConsumerStatefulWidget> createState() =>
      _MyCartItemImageCardState();
}

class _MyCartItemImageCardState extends ConsumerState<MyCartItemImageCard> {
  final Box settingsBox = Hive.box(AppHSC.appSettingsBox);

  String get effectiveVendorId =>
      widget.vendorId ??
      widget.carItemHiveModel.vendorId ??
      CartHelper.getAllVendorCarts().entries
          .firstWhere(
            (e) => e.value.any((it) =>
                it.productsId == widget.carItemHiveModel.productsId &&
                (it.subProduct?.id ==
                        widget.carItemHiveModel.subProduct?.id ||
                    it.subproductsId ==
                        widget.carItemHiveModel.subproductsId)),
            orElse: () => MapEntry(
                widget.carItemHiveModel.vendorId ?? 'default', []),
          )
          .key;

  @override
  Widget build(BuildContext context) {
    // Resolve current quantity from helper (to keep UI in sync when vendor scoped)
    final currentItem = CartHelper.findCartItem(
      vendorId: effectiveVendorId,
      productId: widget.carItemHiveModel.productsId,
      subProductId: widget.carItemHiveModel.subProduct?.id ??
          widget.carItemHiveModel.subproductsId,
    );
    final int displayQty = currentItem?.productsQTY ??
        widget.carItemHiveModel.productsQTY;

    return Padding(
      padding: EdgeInsets.only(top: 10.h),
      child: ValueListenableBuilder(
        valueListenable: Hive.box(AppHSC.cartBox).listenable(),
        builder: (context, Box cartbox, Widget? child) {
          // Re-resolve after box change
          final liveItem = CartHelper.findCartItem(
            vendorId: effectiveVendorId,
            productId: widget.carItemHiveModel.productsId,
            subProductId: widget.carItemHiveModel.subProduct?.id ??
                widget.carItemHiveModel.subproductsId,
          );
          final int liveQty = liveItem?.productsQTY ?? displayQty;

          return Container(
            width: 335.w,
            padding: EdgeInsets.all(10.h),
            decoration: const BoxDecoration(color: AppColors.white),
            child: Row(
              children: [
                SizedBox(
                  height: 80.h,
                  width: 83.w,
                  child: FittedBox(
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(10.w),
                      child: AppNetworkImage(
                        widget.carItemHiveModel.productsImage,
                        height: 80.h,
                        width: 83.w,
                        fit: BoxFit.fill,
                      ),
                    ),
                  ),
                ),
                AppSpacerW(20.w),
                Expanded(
                  child: Column(
                    children: [
                      SizedBox(
                        width: 232.w,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              widget.carItemHiveModel.productsName,
                              style: AppTextDecor.osBold14black,
                            ),
                            Text(
                              widget.carItemHiveModel.serviceName,
                              style: AppTextDecor.osRegular12black,
                            ),
                            if (widget.carItemHiveModel.vendorName != null)
                              Text(
                                widget.carItemHiveModel.vendorName!,
                                style: AppTextDecor.osRegular12black.copyWith(
                                  color: AppColors.primary,
                                  fontSize: 11.sp,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                          ],
                        ),
                      ),
                      Row(
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  '${settingsBox.get('currency') ?? '\$'}${AppGFunctions.convertToFixedTwo(widget.carItemHiveModel.unitPrice)}/${S.of(context).item}',
                                ),
                              ],
                            ),
                          ),
                          IncDecButtonWithValueV2(
                            height: 36.h,
                            width: 120.w,
                            value: liveQty,
                            onDec: () {
                              CartHelper.decrement(
                                vendorId: effectiveVendorId,
                                productId: widget.carItemHiveModel.productsId,
                                subProductId: widget.carItemHiveModel
                                        .subProduct?.id ??
                                    widget.carItemHiveModel.subproductsId,
                              );
                              ref.refreshVendorCarts();
                            },
                            onInc: () {
                              CartHelper.increment(
                                vendorId: effectiveVendorId,
                                productId: widget.carItemHiveModel.productsId,
                                subProductId: widget.carItemHiveModel
                                        .subProduct?.id ??
                                    widget.carItemHiveModel.subproductsId,
                              );
                              ref.refreshVendorCarts();
                            },
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
