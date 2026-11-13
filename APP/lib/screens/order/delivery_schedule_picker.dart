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
import 'package:laundry_customer/widgets/time_slot_tile.dart';

class DeliverySchedulerPicker extends ConsumerStatefulWidget {
  const DeliverySchedulerPicker({
    super.key,
  });

  @override
  ConsumerState<ConsumerStatefulWidget> createState() =>
      _DeliverySchedulerPickerState();
}

class _DeliverySchedulerPickerState
    extends ConsumerState<DeliverySchedulerPicker> {
  @override
  Widget build(
    BuildContext context,
  ) {
    final dateSelect = ref.watch(dateProvider(AppStrConst.delivery));
    final ScheduleModel? pickSchedule = ref.watch(
      scheduleProvider(AppStrConst.pickup),
    );

    if (pickSchedule != null && dateSelect == null) {
      Future.delayed(buildDuration).then((value) {
        final autoDate = pickSchedule.dateTime.add(const Duration(days: 1));

        if (autoDate.weekday == DateTime.sunday) {
          ref.watch(dateProvider(AppStrConst.delivery).notifier).state =
              autoDate.add(const Duration(days: 1));
        } else {
          ref.watch(dateProvider(AppStrConst.delivery).notifier).state =
              autoDate;
        }
      });
    }

    ref.watch(deliveryScheduleProvider);
    return ScreenWrapper(
      padding: EdgeInsets.zero,
      child: Container(
        height: 812.h,
        width: 375.w,
        color: AppColors.grayBG,
        child: Column(
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
                          '${AppStrConst.delivery == "Delivery" ? S.of(context).dlvry : ""} ${S.of(context).schdl}',
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
                  padding: EdgeInsets.symmetric(horizontal: 8.h, vertical: 6.h),
                  child: TableCalendar(
                  rowHeight: 50.h,
                  firstDay: DateTime.now(),
                  lastDay: DateTime.utc(2030, 3, 14),
                  focusedDay: dateSelect ?? DateTime.now(),
                  headerStyle: const HeaderStyle(
                    formatButtonVisible: false,
                    titleCentered: true,
                  ),
                  calendarStyle: CalendarStyle(
                    todayDecoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12.r),
                      color: const Color(0xFF028090).withOpacity(0.25),
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
                  onDaySelected: (DateTime selectDay, DateTime focusDay) {
                    if (selectDay.isBefore(
                      DateUtils.dateOnly(DateTime.now()),
                    )) {
                      EasyLoading.showError('لا يمكن اختيار تاريخ سابق');
                      return;
                    }
                    if (pickSchedule == null) {
                      EasyLoading.showError(
                        S.of(context).plsslctpckupschdl,
                      );
                      return;
                    }
                    if (!selectDay.isAfter(
                      DateUtils.dateOnly(pickSchedule.dateTime),
                    )) {
                      EasyLoading.showError(
                        'موعد التسليم يجب أن يكون بعد موعد الاستلام',
                      );
                      return;
                    }
                    ref
                        .watch(
                          dateProvider(AppStrConst.delivery).notifier,
                        )
                        .state = selectDay;
                    ref
                        .watch(
                          scheduleProvider(AppStrConst.delivery).notifier,
                        )
                        .state = null;
                  },
                  selectedDayPredicate: (DateTime date) {
                    return isSameDay(dateSelect, date);
                  },
                  ),
                ),
              ),
            ),
            Expanded(
              child: ref.watch(deliveryScheduleProvider).map(
                    initial: (_) => const SizedBox(),
                    loading: (_) => const LoadingWidget(),
                    loaded: (_) {
                      List<Schedule> schedules = _.data.data!.schedules ?? [];

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
                                  'لا يوجد جدول تسليم متاح في هذا اليوم، اختر يوماً آخر',
                                  textAlign: TextAlign.center,
                                  style:
                                      TextStyle(color: Color(0xFF64748B)),
                                ),
                              ],
                            ),
                          ),
                        );
                      }

                      final ScheduleModel? pickSchedule = ref.watch(
                        scheduleProvider('Pick Up'),
                      );
                      if (pickSchedule != null &&
                          isSameDay(
                            dateSelect,
                            pickSchedule.dateTime.add(const Duration(days: 1)),
                          )) {
                        final List<Schedule> temp = [];
                        for (final element in _.data.data!.schedules!) {
                          if (int.parse(element.hour!.split('-').first) >=
                              int.parse(
                                pickSchedule.schedule.hour!.split('-').first,
                              )) {
                            temp.add(element);
                          }
                        }
                        schedules = temp;
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
                                        'اختر موعد التسليم (${schedules.length})',
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
                              Padding(
                                padding: EdgeInsets.fromLTRB(
                                    20.w, 2.h, 18.w, 0),
                                child: const TimeSlotLegend(),
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
                                        return TimeSlotTile(
                                          title: title,
                                          available: !isBooked,
                                          onTap: isBooked
                                              ? null
                                              : () {
                                                  debugPrint(
                                                      "this is the date $dateSelect",
                                                    );
                                                  if (pickSchedule != null) {
                                                      ref
                                                          .watch(
                                                            scheduleProvider(
                                                              AppStrConst
                                                                  .delivery,
                                                            ).notifier,
                                                          )
                                                          .state =
                                                          ScheduleModel(
                                                        schedule: e,
                                                        dateTime: dateSelect ??
                                                            DateUtils.dateOnly(
                                                              DateTime.now(),
                                                            ),
                                                      );

                                                      context.nav.pop();
                                                    } else {
                                                      EasyLoading.showError(
                                                        S
                                                                .of(context)
                                                                .plsslctpckupschdl,
                                                      );
                                                    }
                                              },
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
