import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/constants/app_string_const.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/models/schedule_model.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';

class ShedulePicker extends ConsumerStatefulWidget {
  const ShedulePicker({
    super.key,
    required this.image,
    required this.title,
  });
  final String image;
  final String title;

  @override
  ConsumerState<ConsumerStatefulWidget> createState() => _ShedulePickerState();
}

class _ShedulePickerState extends ConsumerState<ShedulePicker>
    with AutomaticKeepAliveClientMixin {
  static const List<String> _arMonths = [
    'يناير',
    'فبراير',
    'مارس',
    'أبريل',
    'مايو',
    'يونيو',
    'يوليو',
    'أغسطس',
    'سبتمبر',
    'أكتوبر',
    'نوفمبر',
    'ديسمبر',
  ];

  static const List<String> _arDays = [
    'الإثنين',
    'الثلاثاء',
    'الأربعاء',
    'الخميس',
    'الجمعة',
    'السبت',
    'الأحد',
  ];

  String _formatDate(DateTime date) =>
      '${_arDays[date.weekday - 1]}، ${date.day} ${_arMonths[date.month - 1]} ${date.year}';

  @override
  Widget build(BuildContext context) {
    super.build(context);
    String modifiedtitle = "";
    if (widget.title == AppStrConst.pickup ||
        widget.title == S.of(context).pickupat) {
      modifiedtitle = AppStrConst.pickup;
    } else {
      modifiedtitle = AppStrConst.delivery;
    }
    final ScheduleModel? data = ref.watch(
      scheduleProvider(modifiedtitle),
    );
    final DateTime? selectedDate = ref.watch(dateProvider(modifiedtitle));
    final DateTime shownDate = data?.dateTime ?? selectedDate ?? DateTime.now();
    final bool isPickup = modifiedtitle == AppStrConst.pickup;
    final Color accent =
        isPickup ? const Color(0xFF028090) : const Color(0xFF00A896);
    final bool hasDate = data != null || selectedDate != null;
    final bool isComplete =
        data != null && (data.schedule.hour ?? data.schedule.title) != null;
    final String slotText = data != null
        ? data.schedule.hour ?? data.schedule.title ?? ''
        : '';
    return GestureDetector(
      onTap: () {
        if (widget.title == AppStrConst.pickup ||
            widget.title == S.of(context).pickupat) {
          context.nav.pushNamed(Routes.schedulePickerScreen);
        } else {
          context.nav.pushNamed(Routes.deilverySchedulePickerScreen);
        }
      },
      child: Container(
        padding: EdgeInsets.all(12.w),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20.r),
          border: Border.all(
              color: isComplete
                  ? accent.withOpacity(0.55)
                  : const Color(0xFFE2E8F0),
              width: isComplete ? 1.4 : 1),
          boxShadow: [
            BoxShadow(
                color: const Color(0xFF0B1E2E).withOpacity(0.06),
                blurRadius: 12,
                offset: const Offset(0, 5)),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 42.w,
                  height: 42.h,
                  decoration: BoxDecoration(
                    color: accent.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(13.r),
                  ),
                  child: Padding(
                    padding: EdgeInsets.all(7.w),
                    child: Image.asset(widget.image, fit: BoxFit.contain),
                  ),
                ),
                SizedBox(width: 8.w),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      RichText(
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        text: TextSpan(
                          style: TextStyle(
                              fontSize: 11.5.sp,
                              fontWeight: FontWeight.w900,
                              color: const Color(0xFF0B1E2E),
                              fontFamily: 'Cairo',
                              height: 1.3),
                          children: [
                            TextSpan(text: widget.title),
                            TextSpan(
                                text: ' *',
                                style: TextStyle(
                                    color: const Color(0xFFE63946),
                                    fontWeight: FontWeight.w900)),
                          ],
                        ),
                      ),
                      SizedBox(height: 4.h),
                      Container(
                        padding: EdgeInsets.symmetric(
                            horizontal: 6.w, vertical: 2.h),
                        decoration: BoxDecoration(
                          color: isComplete
                              ? accent.withOpacity(0.12)
                              : const Color(0xFFF1F5F9),
                          borderRadius: BorderRadius.circular(20.r),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                                isComplete
                                    ? Icons.check_circle_rounded
                                    : Icons.pending_outlined,
                                size: 10.sp,
                                color: isComplete
                                    ? accent
                                    : const Color(0xFF94A3B8)),
                            SizedBox(width: 2.w),
                            Flexible(
                              child: Text(
                                isComplete ? 'تم الاختيار' : 'بانتظار الاختيار',
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: TextStyle(
                                    fontSize: 8.5.sp,
                                    fontWeight: FontWeight.w800,
                                    color: isComplete
                                        ? accent
                                        : const Color(0xFF94A3B8)),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            SizedBox(height: 10.h),
            Container(
              padding:
                  EdgeInsets.symmetric(horizontal: 9.w, vertical: 7.h),
              decoration: BoxDecoration(
                color: const Color(0xFFF8FAFC),
                borderRadius: BorderRadius.circular(12.r),
              ),
              child: Row(
                children: [
                  Icon(Icons.calendar_today_rounded,
                      size: 14.sp, color: accent),
                  SizedBox(width: 6.w),
                  Expanded(
                    child: Text(
                      hasDate ? _formatDate(shownDate) : S.of(context).unslctd,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                          fontSize: 10.5.sp,
                          fontWeight: FontWeight.w700,
                          color: hasDate
                              ? const Color(0xFF0B1E2E)
                              : const Color(0xFF94A3B8)),
                    ),
                  ),
                ],
              ),
            ),
            SizedBox(height: 7.h),
            Container(
              width: double.infinity,
              padding:
                  EdgeInsets.symmetric(horizontal: 9.w, vertical: 7.h),
              decoration: BoxDecoration(
                gradient: isComplete
                    ? LinearGradient(
                        colors: [accent, accent.withOpacity(0.75)])
                    : null,
                color: isComplete ? null : accent.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12.r),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                      isComplete
                          ? Icons.access_time_rounded
                          : Icons.add_circle_outline_rounded,
                      size: 14.sp,
                      color: isComplete ? Colors.white : accent),
                  SizedBox(width: 5.w),
                  Flexible(
                    child: Text(
                      isComplete ? slotText : 'اختر الموعد',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                          fontSize: 10.5.sp,
                          fontWeight: FontWeight.w900,
                          color: isComplete ? Colors.white : accent),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  bool get wantKeepAlive => true;
}
