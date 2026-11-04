import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';

/// بطاقة المتجر بشكل شبكي عصري (عمودين في كل سطر)
class StoreGridCard extends StatelessWidget {
  const StoreGridCard({super.key, required this.vendor, this.onTap});
  final Vendor vendor;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final hasRealImage = vendor.displayImage.isNotEmpty &&
        !vendor.displayImage.contains('dummy-placeholder');

    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18.r),
          border: Border.all(
            color: const Color(0xFFE2E8F0).withOpacity(0.9),
            width: 1.2,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF0F2E35).withOpacity(0.06),
              blurRadius: 14,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // صورة أو بانر المتجر
            Expanded(
              flex: 5,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  hasRealImage
                      ? AppNetworkImage(
                          vendor.displayImage,
                          width: double.infinity,
                          fit: BoxFit.cover,
                          placeholder: 'assets/images/app_icon.png',
                        )
                      : Container(
                          decoration: const BoxDecoration(
                            gradient: LinearGradient(
                              colors: [Color(0xFF156172), Color(0xFF1b758a)],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                          ),
                          child: Center(
                            child: Container(
                              width: 44.w,
                              height: 44.h,
                              decoration: BoxDecoration(
                                color: Colors.white.withOpacity(0.15),
                                shape: BoxShape.circle,
                                border: Border.all(color: Colors.white24),
                              ),
                              child: Center(
                                child: Text(
                                  vendor.displayName.isNotEmpty
                                      ? vendor.displayName.characters.first
                                          .toUpperCase()
                                      : "M",
                                  style: TextStyle(
                                    fontSize: 20.sp,
                                    fontWeight: FontWeight.w900,
                                    color: Colors.white,
                                    fontFamily: 'Cairo',
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),
],
              ),
            ),

            // تفاصيل المتجر
            Expanded(
              flex: 5,
              child: Padding(
                padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 8.h),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          vendor.displayName,
                          style: TextStyle(
                            fontSize: 12.5.sp,
                            fontWeight: FontWeight.w800,
                            color: const Color(0xFF0A4A54),
                            fontFamily: 'Cairo',
                            height: 1.2,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        SizedBox(height: 3.h),
                        Row(
                          children: [
                            Icon(
                              Icons.location_on_rounded,
                              size: 11.sp,
                              color: const Color(0xFF156172),
                            ),
                            SizedBox(width: 3.w),
                            Expanded(
                              child: Text(
                                vendor.displayAddress.isNotEmpty
                                    ? vendor.displayAddress
                                    : 'مغسلة معتمدة',
                                style: TextStyle(
                                  fontSize: 10.sp,
                                  fontWeight: FontWeight.w500,
                                  color: const Color(0xFF64748B),
                                  fontFamily: 'Cairo',
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),

                    // زر طلب الآن السفلي
                    Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 8.w, vertical: 4.5.h),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEAF6F8),
                        borderRadius: BorderRadius.circular(8.r),
                        border: Border.all(
                          color: const Color(0xFF156172).withOpacity(0.12),
                        ),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            "طلب الآن",
                            style: TextStyle(
                              fontFamily: 'Cairo',
                              fontSize: 10.sp,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF156172),
                            ),
                          ),
                          Icon(
                            Icons.arrow_forward_rounded,
                            size: 12.sp,
                            color: const Color(0xFF156172),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

/// هيكل تحميل بطاقات المتجر الشبكية
class StoreGridCardShimmer extends StatelessWidget {
  const StoreGridCardShimmer({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18.r),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        children: [
          Expanded(
            flex: 5,
            child: Container(color: const Color(0xFFF1F5F9)),
          ),
          Expanded(
            flex: 5,
            child: Padding(
              padding: EdgeInsets.all(10.w),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Container(
                    width: 90.w,
                    height: 12.h,
                    decoration: BoxDecoration(
                      color: const Color(0xFFF1F5F9),
                      borderRadius: BorderRadius.circular(4.r),
                    ),
                  ),
                  Container(
                    width: 70.w,
                    height: 10.h,
                    decoration: BoxDecoration(
                      color: const Color(0xFFF1F5F9),
                      borderRadius: BorderRadius.circular(4.r),
                    ),
                  ),
                  Container(
                    width: double.infinity,
                    height: 22.h,
                    decoration: BoxDecoration(
                      color: const Color(0xFFF1F5F9),
                      borderRadius: BorderRadius.circular(8.r),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// بطاقة المتجر العريضة (للاستخدام البديل إن لزم)
class StoreCard extends StatelessWidget {
  const StoreCard({super.key, required this.vendor, this.onTap});
  final Vendor vendor;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    return StoreGridCard(vendor: vendor, onTap: onTap);
  }
}

class StoreCardShimmer extends StatelessWidget {
  const StoreCardShimmer({super.key});
  @override
  Widget build(BuildContext context) {
    return const StoreGridCardShimmer();
  }
}








