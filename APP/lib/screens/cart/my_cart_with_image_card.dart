import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';

/// A polished cart line: rounded image, title, service, price + compact
/// quantity stepper. Shared with the modern cart screen design tokens.
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
              orElse: () =>
                  MapEntry(widget.carItemHiveModel.vendorId ?? 'default', []))
          .key;

  @override
  Widget build(BuildContext context) {
    final currency = settingsBox.get('currency')?.toString() ?? '\$';
    final currentItem = CartHelper.findCartItem(
      vendorId: effectiveVendorId,
      productId: widget.carItemHiveModel.productsId,
      subProductId: widget.carItemHiveModel.subProduct?.id ??
          widget.carItemHiveModel.subproductsId,
    );
    final int displayQty = currentItem?.productsQTY ??
        widget.carItemHiveModel.productsQTY;

    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.cartBox).listenable(),
      builder: (context, Box cartbox, Widget? child) {
        final liveItem = CartHelper.findCartItem(
          vendorId: effectiveVendorId,
          productId: widget.carItemHiveModel.productsId,
          subProductId: widget.carItemHiveModel.subProduct?.id ??
              widget.carItemHiveModel.subproductsId,
        );
        final int liveQty = liveItem?.productsQTY ?? displayQty;
        final double lineTotal =
            liveQty * widget.carItemHiveModel.unitPrice;

        return Directionality(
          textDirection: TextDirection.rtl,
          child: Container(
          margin: EdgeInsets.fromLTRB(10.w, 6.h, 10.w, 0),
          padding: EdgeInsets.all(10.w),
          decoration: BoxDecoration(
            color: const Color(0xFFFDFDFE),
            borderRadius: BorderRadius.circular(16.r),
            border: Border.all(color: const Color(0xFFEDF2F5)),
          ),
          child: Row(
            children: [
              // ===== Visual — image on RIGHT in RTL =====
              ClipRRect(
                borderRadius: BorderRadius.circular(14.r),
                child: Container(
                  width: 86.w,
                  height: 86.h,
                  color: const Color(0xFFF1F5F9),
                  child: AppNetworkImage(
                    widget.carItemHiveModel.productsImage,
                    height: 86.h,
                    width: 86.w,
                    fit: BoxFit.cover,
                  ),
                ),
              ),
              SizedBox(width: 12.w),
              // ===== Details =====
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      widget.carItemHiveModel.productsName,
                      style: TextStyle(
                        fontFamily: 'Cairo',
                        fontSize: 13.sp,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xFF0B1E2E),
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      textDirection: TextDirection.rtl,
                    ),
                    SizedBox(height: 2.h),
                    Text(
                      widget.carItemHiveModel.serviceName,
                      style: TextStyle(
                        fontSize: 10.sp,
                        color: const Color(0xFF64748B),
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      textDirection: TextDirection.rtl,
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        // ===== Compact stepper on LEFT (visual left = end in RTL) =====
                        Container(
                          height: 34.h,
                          decoration: BoxDecoration(
                            color: const Color(0xFFEFFAF8),
                            borderRadius: BorderRadius.circular(11.r),
                            border: Border.all(
                              color: const Color(0xFFBFE9E6),
                            ),
                          ),
                          padding: EdgeInsets.symmetric(horizontal: 4.w),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              _StepperButton(
                                icon: Icons.remove_rounded,
                                onTap: () {
                                  CartHelper.decrement(
                                    vendorId: effectiveVendorId,
                                    productId:
                                        widget.carItemHiveModel.productsId,
                                    subProductId: widget
                                            .carItemHiveModel
                                            .subProduct
                                            ?.id ??
                                        widget.carItemHiveModel
                                            .subproductsId,
                                  );
                                  ref.refreshVendorCarts();
                                },
                              ),
                              SizedBox(
                                width: 34.w,
                                child: Text(
                                  '$liveQty',
                                  textAlign: TextAlign.center,
                                  style: TextStyle(
                                    fontSize: 13.sp,
                                    fontWeight: FontWeight.w900,
                                    color: const Color(0xFF028090),
                                  ),
                                ),
                              ),
                              _StepperButton(
                                icon: Icons.add_rounded,
                                onTap: () {
                                  CartHelper.increment(
                                    vendorId: effectiveVendorId,
                                    productId:
                                        widget.carItemHiveModel.productsId,
                                    subProductId: widget
                                            .carItemHiveModel
                                            .subProduct
                                            ?.id ??
                                        widget.carItemHiveModel
                                            .subproductsId,
                                  );
                                  ref.refreshVendorCarts();
                                },
                              ),
                            ],
                          ),
                        ),
                        // ===== Price on RIGHT (visual right = start in RTL) =====
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text(
                              '$currency${AppGFunctions.convertToFixedTwo(widget.carItemHiveModel.unitPrice)}',
                              style: TextStyle(
                                fontSize: 13.sp,
                                fontWeight: FontWeight.w900,
                                color: const Color(0xFF028090),
                              ),
                            ),
                            Text(
                              'الإجمالي: $currency${lineTotal.toStringAsFixed(2)}',
                              style: TextStyle(
                                fontSize: 9.sp,
                                color: const Color(0xFF94A3B8),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ],
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

class _StepperButton extends StatelessWidget {
  const _StepperButton({required this.icon, required this.onTap});
  final IconData icon;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(9.r),
      child: SizedBox(
        width: 30.w,
        height: double.infinity,
        child: Icon(icon, size: 17.sp, color: const Color(0xFF028090)),
      ),
    );
  }
}
