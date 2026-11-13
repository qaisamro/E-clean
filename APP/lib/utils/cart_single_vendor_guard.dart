import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/widgets/laundry_basket_icon.dart';

/// Single-vendor cart guard with luxury Arabic RTL styling.
///
/// The cart may only hold products from one store at a time. When the user
/// tries to add an item from another store while the cart already contains
/// products from a different store, a beautiful dialog is shown offering to clear
/// the cart and add the new item instead.
Future<bool> ensureSingleVendorOrConfirmClear({
  required BuildContext context,
  required WidgetRef ref,
  required String vendorId,
}) async {
  final carts = CartHelper.getAllVendorCarts();
  final others = carts.entries
      .where((e) => e.key != vendorId && e.value.isNotEmpty)
      .toList();
  if (others.isEmpty) return true;

  final otherItems = others.first.value;
  final String otherName =
      (otherItems.first.vendorName?.trim().isNotEmpty ?? false)
          ? otherItems.first.vendorName!.trim()
          : 'متجر آخر';

  final bool? confirmed = await showDialog<bool>(
    context: context,
    barrierDismissible: true,
    builder: (dialogContext) => Directionality(
      textDirection: TextDirection.rtl,
      child: Dialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(26.r),
        ),
        backgroundColor: Colors.white,
        insetPadding: EdgeInsets.symmetric(horizontal: 20.w, vertical: 24.h),
        child: Padding(
          padding: EdgeInsets.fromLTRB(20.w, 24.h, 20.w, 20.h),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // 1. Animated Badge Icon
              Container(
                width: 64.w,
                height: 64.h,
                decoration: BoxDecoration(
                  color: const Color(0xFFEFFAF8),
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: const Color(0xFF00A896).withValues(alpha: 0.35),
                    width: 1.5,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF028090).withValues(alpha: 0.12),
                      blurRadius: 16,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Center(
                  child: Icon(
                    Icons.storefront_rounded,
                    size: 32.sp,
                    color: const Color(0xFF028090),
                  ),
                ),
              ),
              SizedBox(height: 16.h),

              // 2. Title & Subtitle
              Text(
                'السلة من متجر آخر',
                style: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 18.sp,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF0F172A),
                ),
                textAlign: TextAlign.center,
              ),
              SizedBox(height: 8.h),

              // 3. Current Store Highlight Card
              Container(
                width: double.infinity,
                padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 10.h),
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(16.r),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 36.w,
                      height: 36.h,
                      decoration: BoxDecoration(
                        color: const Color(0xFFEFFAF8),
                        borderRadius: BorderRadius.circular(10.r),
                      ),
                      child: const LaundryBasketIcon(
                        size: 18,
                        color: Color(0xFF028090),
                      ),
                    ),
                    SizedBox(width: 10.w),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'المتجر الحالي بالسلة: $otherName',
                            style: TextStyle(
                              fontFamily: 'Cairo',
                              fontSize: 12.sp,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF0F172A),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          Text(
                            'يحتوي على ${otherItems.length} عنصر',
                            style: TextStyle(
                              fontFamily: 'Cairo',
                              fontSize: 11.sp,
                              color: const Color(0xFF64748B),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              SizedBox(height: 12.h),

              // 4. Explanation text
              Text(
                'يمكن إتمام الطلب من متجر واحد فقط في كل عملية.\n'
                'هل ترغب في إفراغ السلة السابقة للبدء مع هذا المتجر؟',
                style: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 13.sp,
                  height: 1.6,
                  color: const Color(0xFF475569),
                ),
                textAlign: TextAlign.center,
              ),
              SizedBox(height: 22.h),

              // 5. Action Buttons (Arabic RTL order)
              Row(
                children: [
                  // Confirm Clear & Add Button (Primary on right)
                  Expanded(
                    flex: 6,
                    child: SizedBox(
                      height: 46.h,
                      child: DecoratedBox(
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [Color(0xFF028090), Color(0xFF00A896)],
                          ),
                          borderRadius: BorderRadius.circular(14.r),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF00A896)
                                  .withValues(alpha: 0.28),
                              blurRadius: 8,
                              offset: const Offset(0, 3),
                            ),
                          ],
                        ),
                        child: ElevatedButton(
                          onPressed: () =>
                              Navigator.of(dialogContext).pop(true),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.transparent,
                            shadowColor: Colors.transparent,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14.r),
                            ),
                            padding: EdgeInsets.zero,
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.delete_sweep_rounded,
                                size: 18.sp,
                                color: Colors.white,
                              ),
                              SizedBox(width: 6.w),
                              Text(
                                'إفراغ والبدء',
                                style: TextStyle(
                                  fontFamily: 'Cairo',
                                  fontSize: 13.sp,
                                  fontWeight: FontWeight.w900,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                  SizedBox(width: 10.w),

                  // Cancel Button (Secondary on left)
                  Expanded(
                    flex: 4,
                    child: SizedBox(
                      height: 46.h,
                      child: OutlinedButton(
                        onPressed: () => Navigator.of(dialogContext).pop(false),
                        style: OutlinedButton.styleFrom(
                          side: const BorderSide(color: Color(0xFFCBD5E1)),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(14.r),
                          ),
                          padding: EdgeInsets.zero,
                        ),
                        child: Text(
                          'إلغاء',
                          style: TextStyle(
                            fontFamily: 'Cairo',
                            fontSize: 13.sp,
                            fontWeight: FontWeight.w800,
                            color: const Color(0xFF64748B),
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    ),
  );

  if (confirmed == true) {
    CartHelper.clearAll();
    ref.refreshVendorCarts();
    return true;
  }
  return false;
}
