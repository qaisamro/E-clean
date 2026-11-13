import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/utils/map_launcher.dart';

/// Small pill button that opens the given address on Google Maps.
/// Mirrors the «الموقع» button on the store page (white background,
/// teal marker, rounded pill, thin border).
class LocationPillButton extends StatelessWidget {
  const LocationPillButton({
    super.key,
    this.latitude,
    this.longitude,
    this.fallbackQuery,
    this.compact = false,
  });

  final dynamic latitude;
  final dynamic longitude;
  final String? fallbackQuery;

  /// When true the icon is used without the text label (tight rows).
  final bool compact;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: const Color(0xFFFFFFFF),
      borderRadius: BorderRadius.circular(20.r),
      child: InkWell(
        borderRadius: BorderRadius.circular(20.r),
        onTap: () => openAddressOnMap(
          latitude: latitude,
          longitude: longitude,
          fallbackQuery: fallbackQuery,
        ),
        child: Container(
          height: compact ? 32.h : 34.h,
          padding: EdgeInsets.symmetric(horizontal: compact ? 8.w : 12.w, vertical: 0),
          decoration: BoxDecoration(
            color: const Color(0xFFFFFFFF),
            borderRadius: BorderRadius.circular(20.r),
            border: Border.all(color: const Color(0xFFE2E8F0)),
          ),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(Icons.location_on_outlined,
                  size: 15.sp, color: const Color(0xFF028090)),
              if (!compact) ...[
                SizedBox(width: 5.w),
                Text('الموقع',
                    style: TextStyle(
                        fontSize: 11.sp,
                        fontWeight: FontWeight.w800,
                        color: const Color(0xFF0B1E2E),
                        fontFamily: 'Cairo')),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
