import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';

/// Section headline used across the home screen — the title sits on a
/// rounded teal pill (the identity color of the app) with a soft glow,
/// so sections read at a glance in a busy, colorful feed.
class HomeSectionTitle extends StatelessWidget {
  const HomeSectionTitle({super.key, required this.text, this.fontSize});

  final String text;
  final double? fontSize;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 7.h),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF156172), Color(0xFF1B758A)],
          begin: Alignment.topRight,
          end: Alignment.bottomLeft,
        ),
        borderRadius: BorderRadius.circular(13.r),
        border: Border.all(color: Colors.white.withOpacity(0.22)),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF156172).withOpacity(0.32),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: fontSize ?? 13.sp,
          fontWeight: FontWeight.w900,
          color: Colors.white,
          fontFamily: 'Cairo',
          height: 1.25,
          letterSpacing: 0.15,
        ),
      ),
    );
  }
}
