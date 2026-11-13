import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';

/// A single bookable time slot with three instantly readable states:
///
/// * available — white card, teal accent, «متاح»
/// * selected  — filled teal gradient with a white check, «المحدد»
/// * booked    — muted terracotta card, strikethrough time, «محجوز»
///               (clearly different from every actionable state)
///
/// Booked slots are never tappable and never look like an option.
class TimeSlotTile extends StatelessWidget {
  const TimeSlotTile({
    super.key,
    required this.title,
    required this.available,
    this.selected = false,
    this.reason,
    this.onTap,
  });

  final String title;
  final bool available;
  final bool selected;
  final String? reason;
  final VoidCallback? onTap;

  static const Color bookedBg = Color(0xFFF9E9E5);
  static const Color bookedBorder = Color(0xFFE3B6AB);
  static const Color bookedInk = Color(0xFF9A5B4F);
  static const Color bookedCaption = Color(0xFFB0492F);
  static const Color bookedTileBg = Color(0xFFEDD3CB);

  static const Color availableBorder = Color(0xFF0F766E);
  static const Color availableInk = Color(0xFF0F2E35);
  static const Color teal = Color(0xFF0F766E);
  static const Color tealMid = Color(0xFF14B8A6);

  @override
  Widget build(BuildContext context) {
    final bool interactive = available && onTap != null;

    final Color bg = selected
        ? Colors.transparent
        : available
            ? Colors.white
            : bookedBg;
    final Color border = selected
        ? Colors.transparent
        : available
            ? availableBorder.withOpacity(0.35)
            : bookedBorder;
    final Color iconTileBg = selected
        ? Colors.white.withOpacity(0.22)
        : available
            ? const Color(0xFFEAF6F1)
            : bookedTileBg;
    final Color iconColor = selected
        ? Colors.white
        : available
            ? teal
            : bookedInk;
    final Color ink = selected
        ? Colors.white
        : available
            ? availableInk
            : bookedInk;
    final Color caption = selected
        ? Colors.white.withOpacity(0.92)
        : available
            ? teal
            : bookedCaption;

    final IconData icon = selected
        ? Icons.check_circle_rounded
        : available
            ? Icons.access_time_filled_rounded
            : Icons.event_busy_rounded;

    final String captionText = selected
        ? 'المحدد'
        : available
            ? 'متاح'
            : (reason?.trim().isNotEmpty == true ? reason!.trim() : 'محجوز');

    final tile = Container(
      padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 8.h),
      decoration: BoxDecoration(
        color: bg,
        gradient: selected
            ? const LinearGradient(
                colors: [Color(0xFF0F766E), Color(0xFF14B8A6)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              )
            : null,
        borderRadius: BorderRadius.circular(16.r),
        border: Border.all(color: border, width: selected ? 0 : 1.2),
        boxShadow: selected
            ? [
                BoxShadow(
                  color: teal.withOpacity(0.38),
                  blurRadius: 14,
                  offset: const Offset(0, 6),
                ),
              ]
            : available
                ? [
                    BoxShadow(
                      color: const Color(0xFF0B1E2E).withOpacity(0.05),
                      blurRadius: 8,
                      offset: const Offset(0, 4),
                    ),
                  ]
                : const [],
      ),
      child: Row(
        children: [
          Container(
            width: 34.w,
            height: 34.h,
            decoration: BoxDecoration(
              color: iconTileBg,
              borderRadius: BorderRadius.circular(10.r),
            ),
            child: Icon(icon, size: 17.sp, color: iconColor),
          ),
          SizedBox(width: 8.w),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  title,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontSize: 11.5.sp,
                    fontWeight: FontWeight.w800,
                    color: ink,
                    height: 1.25,
                    decoration: available
                        ? null
                        : TextDecoration.lineThrough,
                    decorationColor: bookedCaption,
                    decorationThickness: 1.6,
                    fontFamily: 'Cairo',
                  ),
                ),
                Text(
                  captionText,
                  style: TextStyle(
                    fontSize: 9.5.sp,
                    fontWeight: FontWeight.w800,
                    color: caption,
                    fontFamily: 'Cairo',
                    letterSpacing: 0.2,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );

    if (!interactive) return tile;
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(16.r),
        onTap: onTap,
        child: tile,
      ),
    );
  }
}

/// Small legend used above time-slot grids so the meaning of the colours
/// is obvious at a glance: teal = available, clay = booked, filled = picked.
class TimeSlotLegend extends StatelessWidget {
  const TimeSlotLegend({super.key});

  @override
  Widget build(BuildContext context) {
    Widget item(Color dot, String label, {bool outline = false}) => Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 11.w,
              height: 11.h,
              decoration: BoxDecoration(
                color: outline ? Colors.white : dot,
                shape: BoxShape.circle,
                border: Border.all(
                  color: dot,
                  width: 1.4,
                ),
              ),
            ),
            SizedBox(width: 5.w),
            Text(
              label,
              style: TextStyle(
                fontSize: 10.sp,
                fontWeight: FontWeight.w700,
                color: const Color(0xFF475569),
                fontFamily: 'Cairo',
              ),
            ),
          ],
        );

    return Row(
      children: [
        item(TimeSlotTile.teal, 'متاح', outline: true),
        SizedBox(width: 14.w),
        item(TimeSlotTile.bookedCaption, 'محجوز'),
        SizedBox(width: 14.w),
        item(const Color(0xFF14B8A6), 'المحدد'),
      ],
    );
  }
}
