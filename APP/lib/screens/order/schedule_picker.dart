import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/app_string_const.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/schedule_model.dart';
import 'package:laundry_customer/models/schedules_model/schedule.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';
import 'package:laundry_customer/widgets/nav_bar.dart';
import 'package:laundry_customer/widgets/screen_wrapper.dart';
import 'package:table_calendar/table_calendar.dart';

class SchedulerPicker extends ConsumerStatefulWidget {
  const SchedulerPicker({
    super.key,
  });

  @override
  ConsumerState<ConsumerStatefulWidget> createState() =>
      _SchedulerPickerState();
}

class _SchedulerPickerState extends ConsumerState<SchedulerPicker> {
  @override
  Widget build(
    BuildContext context,
  ) {
    final dateSelect = ref.watch(dateProvider(AppStrConst.pickup));

    if (dateSelect == null) {
      Future.delayed(buildDuration).then((value) {
        ref.watch(dateProvider(AppStrConst.pickup).notifier).state =
            DateUtils.dateOnly(DateTime.now());
      });
    }
    ref.watch(pickUpScheduleProvider);
    return ScreenWrapper(
      padding: EdgeInsets.zero,
      child: Container(
        height: 812.h,
        width: 375.w,
        color: AppColors.grayBG,
        child: Column(
          children: [
            Column(
              children: [
                Container(
                  color: AppColors.primary,
                  height: 108.h,
                  width: 375.w,
                  child: Padding(
                    padding: EdgeInsets.symmetric(horizontal: 20.h),
                    child: Column(
                      children: [
                        AppSpacerH(44.h),
                        AppNavbar(
                          backgroundColor: AppColors.primary,
                          titleColor: AppColors.white,
                          title:
                              '${AppStrConst.pickup == "Pick Up" ? S.of(context).pckup : ""} ${S.of(context).schdl}',
                          onBack: () {
                            context.nav.pop();
                          },
                        ),
                      ],
                    ),
                  ),
                ),
                Container(
                  margin: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 0),
                  decoration: BoxDecoration(
                    color: AppColors.white,
                    borderRadius: BorderRadius.circular(20.r),
                    border: Border.all(color: const Color(0xFFE2E8F0)),
                    boxShadow: [
                      BoxShadow(
                          color: const Color(0xFF0B1E2E).withOpacity(0.06),
                          blurRadius: 14,
                          offset: const Offset(0, 6)),
                    ],
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(20.r),
                    child: Padding(
                      padding:
                          EdgeInsets.symmetric(horizontal: 8.h, vertical: 6.h),
                      child: TableCalendar(
                        rowHeight: 50.h,
                        currentDay: DateTime.now(),
                        firstDay: DateTime.now(),
                        lastDay: DateTime.utc(2030, 3, 14),
                        calendarStyle: CalendarStyle(
                          todayDecoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12.r),
                            color:
                                const Color(0xFF028090).withOpacity(0.25),
                          ),
                          todayTextStyle: const TextStyle(
                              color: Color(0xFF0B1E2E),
                              fontWeight: FontWeight.w800),
                          selectedDecoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12.r),
                            gradient: const LinearGradient(colors: [
                              Color(0xFF028090),
                              Color(0xFF00A896)
                            ]),
                          ),
                        ),
                      focusedDay: dateSelect ?? DateTime.now(),
                      headerStyle: const HeaderStyle(
                        formatButtonVisible: false,
                        titleCentered: true,
                      ),
                      onDaySelected: (DateTime selectDay, DateTime focusDay) {
                        if (selectDay.isBefore(
                          DateUtils.dateOnly(DateTime.now()),
                        )) {
                          EasyLoading.showError(
                            'لا يمكن اختيار تاريخ سابق',
                          );
                          return;
                        }
                        ref
                            .watch(dateProvider(AppStrConst.pickup).notifier)
                            .state = selectDay;
                        //Any new pickup date invalidates the previously
                        //picked slot and the whole delivery plan.
                        ref
                            .watch(
                              scheduleProvider(AppStrConst.pickup).notifier,
                            )
                            .state = null;
                        ref
                            .watch(
                              dateProvider(AppStrConst.delivery).notifier,
                            )
                            .state = null;
                        ref
                            .watch(
                              scheduleProvider(AppStrConst.delivery).notifier,
                            )
                            .state = null;

                        debugPrint("this is the date $selectDay");
                      },
                      selectedDayPredicate: (DateTime date) {
                        return isSameDay(dateSelect, date);
                      },
                      ),
                    ),
                  ),
                ),
              ],
            ),
            Expanded(
              child: ref.watch(pickUpScheduleProvider).map(
                    initial: (_) => const SizedBox(),
                    loading: (_) => const LoadingWidget(),
                    loaded: (_) {
                      final List<Schedule> schedules =
                          _.data.data!.schedules ?? [];

                      if (schedules.isEmpty) {
                        return Center(
                          child: Container(
                            margin: EdgeInsets.all(20.w),
                            padding: EdgeInsets.symmetric(
                                horizontal: 20.w, vertical: 28.h),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(20.r),
                              border: Border.all(
                                  color: const Color(0xFFE2E8F0)),
                            ),
                            child: Column(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Container(
                                  width: 52.w,
                                  height: 52.h,
                                  decoration: BoxDecoration(
                                    color: const Color(0xFF028090)
                                        .withOpacity(0.1),
                                    shape: BoxShape.circle,
                                  ),
                                  child: Icon(
                                      Icons.event_busy_rounded,
                                      size: 24.sp,
                                      color: const Color(0xFF028090)),
                                ),
                                SizedBox(height: 12.h),
                                const Text(
                                  'لا يوجد جدول متاح في هذا اليوم، اختر يوماً آخر',
                                  textAlign: TextAlign.center,
                                  style:
                                      TextStyle(color: Color(0xFF64748B)),
                                ),
                              ],
                            ),
                          ),
                        );
                      }

                      return ValueListenableBuilder(
                        valueListenable:
                            Hive.box(AppHSC.appSettingsBox).listenable(),
                        builder: (
                          BuildContext context,
                          Box settingsBox,
                          Widget? child,
                        ) {
                          final selectedLocal =
                              settingsBox.get(AppHSC.appLocal);
                          return Column(
                            children: [
                              Padding(
                                padding: EdgeInsets.fromLTRB(
                                    18.w, 14.h, 18.w, 2.h),
                                child: Row(
                                  children: [
                                    Container(
                                      width: 32.w,
                                      height: 32.h,
                                      decoration: BoxDecoration(
                                        gradient: const LinearGradient(
                                            colors: [
                                              Color(0xFF028090),
                                              Color(0xFF00A896)
                                            ]),
                                        borderRadius:
                                            BorderRadius.circular(10.r),
                                      ),
                                      child: Icon(
                                          Icons.schedule_rounded,
                                          size: 16.sp,
                                          color: Colors.white),
                                    ),
                                    SizedBox(width: 10.w),
                                    Expanded(
                                      child: Text(
                                        'اختر موعد الاستلام (${schedules.length})',
                                        style: TextStyle(
                                            fontSize: 13.sp,
                                            fontWeight: FontWeight.w900,
                                            color:
                                                const Color(0xFF0B1E2E),
                                            fontFamily: 'Cairo'),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              Expanded(
                                child: GridView.count(
                                  crossAxisCount: 2,
                                  childAspectRatio: 2.35,
                                  padding: EdgeInsets.fromLTRB(
                                      16.w, 8.h, 16.w, 20.h),
                                  mainAxisSpacing: 10.h,
                                  crossAxisSpacing: 10.w,
                                  children: [
                                    ...schedules.map(
                                      (e) {
                                        final isBooked = e.isBooked;
                                        final title = (selectedLocal ==
                                                    null ||
                                                selectedLocal == '' ||
                                                selectedLocal == 'en')
                                            ? e.title ?? ""
                                            : "${e.title!.split("-").last}- ${e.title!.split("-").first}";
                                        return Opacity(
                                          opacity: isBooked ? 0.6 : 1,
                                          child: InkWell(
                                            borderRadius:
                                                BorderRadius.circular(18.r),
                                            onTap: isBooked
                                                ? null
                                                : () {
                                                    ref
                                                        .watch(
                                                          scheduleProvider(
                                                            AppStrConst.pickup,
                                                          ).notifier,
                                                        )
                                                        .state = ScheduleModel(
                                                      schedule: e,
                                                      dateTime: dateSelect ??
                                                          DateUtils.dateOnly(
                                                            DateTime.now(),
                                                          ),
                                                    );

                                                    ref
                                                        .watch(
                                                          dateProvider(
                                                                  'Delivery')
                                                              .notifier,
                                                        )
                                                        .state = null;
                                                    ref
                                                        .watch(
                                                          scheduleProvider(
                                                                  'Delivery')
                                                              .notifier,
                                                        )
                                                        .state = null;

                                                    context.nav.pop();
                                                  },
                                            child: Container(
                                              padding:
                                                  EdgeInsets.symmetric(
                                                      horizontal: 10.w,
                                                      vertical: 8.h),
                                              decoration: BoxDecoration(
                                                color: Colors.white,
                                                borderRadius:
                                                    BorderRadius.circular(
                                                        18.r),
                                                border: Border.all(
                                                    color: isBooked
                                                        ? const Color(
                                                            0xFFE2E8F0)
                                                        : const Color(
                                                                0xFF028090)
                                                            .withOpacity(
                                                                0.4)),
                                                boxShadow: [
                                                  BoxShadow(
                                                      color: const Color(
                                                              0xFF0B1E2E)
                                                          .withOpacity(0.05),
                                                      blurRadius: 10,
                                                      offset: const Offset(
                                                          0, 4)),
                                                ],
                                              ),
                                              child: Row(
                                                children: [
                                                  Container(
                                                    width: 34.w,
                                                    height: 34.h,
                                                    decoration: BoxDecoration(
                                                      color: (isBooked
                                                              ? const Color(
                                                                  0xFF64748B)
                                                              : const Color(
                                                                  0xFF028090))
                                                          .withOpacity(0.12),
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                              10.r),
                                                    ),
                                                    child: Icon(
                                                        Icons.access_time_rounded,
                                                        size: 17.sp,
                                                        color: isBooked
                                                            ? const Color(
                                                                0xFF64748B)
                                                            : const Color(
                                                                0xFF028090)),
                                                  ),
                                                  SizedBox(width: 8.w),
                                                  Expanded(
                                                    child: Column(
                                                      crossAxisAlignment:
                                                          CrossAxisAlignment
                                                              .start,
                                                      mainAxisAlignment:
                                                          MainAxisAlignment
                                                              .center,
                                                      children: [
                                                        Text(
                                                          title,
                                                          maxLines: 1,
                                                          overflow: TextOverflow
                                                              .ellipsis,
                                                          style: TextStyle(
                                                            fontSize: 11.5.sp,
                                                            fontWeight:
                                                                FontWeight.w800,
                                                            color: const Color(
                                                                0xFF0B1E2E),
                                                            decoration: isBooked
                                                                ? TextDecoration
                                                                    .lineThrough
                                                                : null,
                                                          ),
                                                        ),
                                                        Text(
                                                          isBooked
                                                              ? 'محجوز'
                                                              : 'متاح',
                                                          style: TextStyle(
                                                            fontSize: 9.5.sp,
                                                            fontWeight:
                                                                FontWeight.w700,
                                                            color: isBooked
                                                                ? const Color(
                                                                    0xFF94A3B8)
                                                                : const Color(
                                                                    0xFF00A896),
                                                          ),
                                                        ),
                                                      ],
                                                    ),
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ),
                                        );
                                      },
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          );
                        },
                      );
                    },
                    error: (_) => ErrorTextWidget(error: _.error),
                  ),
            ),
          ],
        ),
      ),
    );
  }
}
