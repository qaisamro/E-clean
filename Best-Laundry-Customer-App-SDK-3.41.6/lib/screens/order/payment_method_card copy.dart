import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/app_text_decor.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class PaymentGateWayCard extends ConsumerWidget {
  const PaymentGateWayCard({
    super.key,
    required this.imageLocation,
    required this.title,
    this.isSelected = false,
    this.onTap,
  });
  final String imageLocation;
  final String title;
  final bool isSelected;
  final Function()? onTap;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: EdgeInsets.all(10.h),
        margin: EdgeInsets.only(bottom: 10.h),
        decoration: BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.circular(5.h),
          border: Border.all(
            color: isSelected ? AppColors.primary : AppColors.white,
          ),
        ),
        child: Row(
          //crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              decoration: BoxDecoration(
                // shape: BoxShape.circle,
                borderRadius: BorderRadius.circular(6.h),
                color: AppColors.gray.withOpacity( 0.2),
              ),
              child: CachedNetworkImage(
                imageUrl: imageLocation,
                height: 30.h,
                width: 80.w,
                fit: BoxFit.contain,
                errorWidget: (context, url, error) => const Icon(
                  Icons.error,
                ),
              ),
            ),
            const Spacer(),
            Text(
              title,
              style: AppTextDecor.osBold12black,
            ),
            AppSpacerW(15.w),
            if (isSelected)
              Icon(
                Icons.check_circle,
                color: AppColors.primary,
                size: 15.h,
              ),
          ],
        ),
      ),
    );
  }
}
