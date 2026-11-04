import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/constants/app_colors.dart';

class AppTextDecor {
  AppTextDecor._();
  static const _arabicFallback = <String>[
    'Noto Sans Arabic',
    'Noto Naskh Arabic',
    'Geeza Pro',
    'Segoe UI',
    'Tahoma',
    'Arial',
  ];
  //Open Sans
  //Regular
  static TextStyle osRegular18black = TextStyle(
    color: AppColors.black,
    fontSize: 18.sp,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osRegular14white = TextStyle(
    color: AppColors.black,
    fontSize: 14.sp,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osRegular14black = TextStyle(
    color: AppColors.black,
    fontSize: 14.sp,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osRegular14Navy = TextStyle(
    color: AppColors.navyText,
    fontSize: 14.sp,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osRegular14red = TextStyle(
    color: AppColors.red,
    fontSize: 14.sp,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osRegular12black = TextStyle(
    color: AppColors.black,
    fontSize: 12.sp,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osRegular12navy = TextStyle(
    color: AppColors.navyText,
    fontSize: 12.sp,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osRegular12red = TextStyle(
    color: AppColors.red,
    fontSize: 12.sp,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );

  //Semi Bold
  static TextStyle osSemiBold10black = TextStyle(
    color: AppColors.black,
    fontSize: 10.sp,
    fontWeight: FontWeight.w600,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osSemiBold12black = TextStyle(
    color: AppColors.black,
    fontSize: 12.sp,
    fontWeight: FontWeight.w600,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osSemiBold14black = TextStyle(
    color: AppColors.black,
    fontSize: 14.sp,
    fontWeight: FontWeight.w600,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osSemiBold18black = TextStyle(
    color: AppColors.black,
    fontSize: 18.sp,
    fontWeight: FontWeight.w600,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osSemiBold14navy = TextStyle(
    color: AppColors.navyButton,
    fontSize: 14.sp,
    fontWeight: FontWeight.w600,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osSemiBold20black = TextStyle(
    color: AppColors.black,
    fontSize: 20.sp,
    fontWeight: FontWeight.w600,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );

  //Bold
  static TextStyle osBold30black = TextStyle(
    color: AppColors.black,
    fontSize: 30.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold24white = TextStyle(
    color: AppColors.black,
    fontSize: 24.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold24black = TextStyle(
    color: AppColors.black,
    fontSize: 24.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold20Black = TextStyle(
    color: AppColors.black,
    fontSize: 20.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold20white = TextStyle(
    color: AppColors.black,
    fontSize: 20.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold12gold = TextStyle(
    color: AppColors.black,
    fontSize: 12.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold12black = TextStyle(
    color: AppColors.black,
    fontSize: 12.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold12red = TextStyle(
    color: AppColors.red,
    fontSize: 12.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold14white = TextStyle(
    color: AppColors.black,
    fontSize: 14.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold14black = TextStyle(
    color: AppColors.black,
    fontSize: 14.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold14red = TextStyle(
    color: AppColors.red,
    fontSize: 14.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  static TextStyle osBold14gold = TextStyle(
    color: AppColors.black,
    fontSize: 14.sp,
    fontWeight: FontWeight.bold,
    fontFamily: 'Open Sans',
    fontFamilyFallback: _arabicFallback,
  );
  //Poppins
  //Regular
  static TextStyle popRegular14white = TextStyle(
    color: AppColors.black,
    fontSize: 14.sp,
  );
  static TextStyle formErrorTextStyle = TextStyle(
    color: AppColors.red,
    fontSize: 12.sp,
  );
}
