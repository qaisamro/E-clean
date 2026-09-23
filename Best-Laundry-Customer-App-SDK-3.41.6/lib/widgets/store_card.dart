import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';

class StoreCard extends StatelessWidget {
  const StoreCard({super.key, required this.vendor, this.onTap});
  final Vendor vendor;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final hasRealImage = vendor.displayImage.isNotEmpty && !vendor.displayImage.contains('dummy-placeholder');
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: double.infinity,
        margin: EdgeInsets.only(bottom: 14.h),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20.r),
          border: Border.all(color: const Color(0xFFEAF6F8).withOpacity(0.85)),
          boxShadow: [BoxShadow(color: const Color(0xFF0C2238).withOpacity(0.07), blurRadius: 18, offset: const Offset(0, 8))],
        ),
        child: Column(
          children: [
            // صورة المتجر الحقيقية - بانر احترافي 16:9
            ClipRRect(
              borderRadius: BorderRadius.vertical(top: Radius.circular(20.r)),
              child: hasRealImage
                  ? AspectRatio(
                      aspectRatio: 16 / 7.2,
                      child: AppNetworkImage(vendor.displayImage, width: double.infinity, fit: BoxFit.cover, placeholder: 'assets/images/app_icon.png'),
                    )
                  : Container(
                      height: 88.h,
                      width: double.infinity,
                      decoration: BoxDecoration(
                        gradient: LinearGradient(colors: [const Color(0xFF156172), const Color(0xFF1b758a)], begin: Alignment.topRight, end: Alignment.bottomLeft),
                      ),
                      child: Center(
                        child: Text(vendor.displayName.isNotEmpty ? vendor.displayName.characters.first.toUpperCase() : "M",
                            style: TextStyle(fontSize: 28.sp, fontWeight: FontWeight.w900, color: Colors.white)),
                      ),
                    ),
            ),
            Padding(
              padding: EdgeInsets.fromLTRB(14.w, 12.h, 14.w, 12.h),
              child: Row(
                children: [
                  Container(
                    width: 42.w,
                    height: 42.h,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12.r),
                      border: Border.all(color: const Color(0xFFEAF6F8).withOpacity(0.85)),
                      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 8, offset: const Offset(0, 2))],
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(12.r),
                      child: hasRealImage
                          ? AppNetworkImage(vendor.displayImage, width: 42.w, height: 42.h, fit: BoxFit.cover, placeholder: 'assets/images/app_icon.png')
                          : Center(child: Icon(Icons.store_rounded, size: 20.sp, color: const Color(0xFF1b758a))),
                    ),
                  ),
                  SizedBox(width: 10.w),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(vendor.displayName, style: TextStyle(fontSize: 13.5.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0A4A54), fontFamily: 'Cairo'), maxLines: 1, overflow: TextOverflow.ellipsis),
                        SizedBox(height: 3.h),
                        Row(
                          children: [
                            Icon(Icons.location_on_rounded, size: 13.sp, color: const Color(0xFF156172)),
                            SizedBox(width: 3.w),
                            Expanded(
                              child: Text(vendor.displayAddress.isNotEmpty ? vendor.displayAddress : 'لا يوجد عنوان',
                                  style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w600, color: const Color(0xFF64748B)), maxLines: 1, overflow: TextOverflow.ellipsis),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  Container(
                    width: 32.w,
                    height: 32.h,
                    decoration: BoxDecoration(color: const Color(0xFFEAF6F8), shape: BoxShape.circle, border: Border.all(color: const Color(0xFF156172).withOpacity(0.18))),
                    child: Icon(Icons.arrow_forward_rounded, size: 16.sp, color: const Color(0xFF1b758a)),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class StoreCardShimmer extends StatelessWidget {
  const StoreCardShimmer({super.key});
  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      height: 148.h,
      margin: EdgeInsets.only(bottom: 14.h),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r)),
      child: Column(
        children: [
          Container(height: 88.h, decoration: BoxDecoration(color: const Color(0xFFF1F5F9), borderRadius: BorderRadius.vertical(top: Radius.circular(20.r)))),
          Padding(
            padding: EdgeInsets.all(14.w),
            child: Row(children: [
              Container(width: 42.w, height: 42.h, decoration: BoxDecoration(color: const Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(12.r))),
              SizedBox(width: 10.w),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Container(width: 110.w, height: 12.h, decoration: BoxDecoration(color: const Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(6.r))),
                SizedBox(height: 6.h),
                Container(width: 150.w, height: 10.h, decoration: BoxDecoration(color: const Color(0xFFF1F5F9), borderRadius: BorderRadius.circular(6.r))),
              ]))
            ]),
          ),
        ],
      ),
    );
  }
}







