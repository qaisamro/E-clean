import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/models/notifications_model/notification.dart' as nt;
import 'package:laundry_customer/providers/notification_providers.dart';
import 'package:laundry_customer/screens/notifications/notification_function.dart';
import 'package:laundry_customer/utils/notification_translator.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class MyNotificationsTab extends ConsumerStatefulWidget {
  MyNotificationsTab({super.key});

  @override
  ConsumerState<MyNotificationsTab> createState() => _MyNotificationsTabState();
}

class _MyNotificationsTabState extends ConsumerState<MyNotificationsTab> {
  List<nt.Notification> notifications = [];

  @override
  Widget build(BuildContext context) {
    ref.watch(notificationListProvider).maybeWhen(
          loaded: (_) => notifications = List.of(_.data?.notification ?? []),
          orElse: () {},
        );
    return Container(
      color: const Color(0xFFFAF7F2),
      child: CustomScrollView(
        slivers: [
          SliverToBoxAdapter(
            child: Container(
              padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 18.h),
              decoration: const BoxDecoration(
                gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft),
                borderRadius: BorderRadius.vertical(bottom: Radius.circular(24)),
              ),
              child: SafeArea(
                bottom: false,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    SizedBox(height: 8.h),
                    Row(children: [
                      Text('الإشعارات', style: TextStyle(fontSize: 18.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                      const Spacer(),
                      Container(
                        padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h),
                        decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(20.r), border: Border.all(color: Colors.white24)),
                        child: Row(children: [
                          Icon(Icons.notifications_rounded, size: 14.sp, color: Colors.white),
                          SizedBox(width: 4.w),
                          Text('${notifications.length}', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: Colors.white)),
                        ]),
                      ),
                    ]),
                    SizedBox(height: 6.h),
                    Text(notifications.isEmpty ? 'لا يوجد إشعارات حالياً' : 'اسحب لليمين للقراءة ولليسار للحذف', style: TextStyle(fontSize: 11.5.sp, color: Colors.white.withOpacity(0.92))),
                  ],
                ),
              ),
            ),
          ),
          SliverPadding(
            padding: EdgeInsets.fromLTRB(16.w, 16.h, 16.w, 100.h),
            sliver: ValueListenableBuilder(
              valueListenable: Hive.box(AppHSC.authBox).listenable(),
              builder: (context, Box authbox, _) {
                final bool hasToken = authbox.get('token') != null && authbox.get('token') != '';
                if (!hasToken || notifications.isEmpty) {
                  return SliverToBoxAdapter(child: const NoNotificationWidget());
                }
                return SliverList.builder(
                  itemCount: notifications.length,
                  itemBuilder: (context, index) {
                    final n = notifications[index];
                    final bool isRead = n.isRead == 1;
                    return Dismissible(
                      key: ValueKey(n.id ?? index),
                      background: Container(
                        margin: EdgeInsets.only(bottom: 12.h),
                        decoration: BoxDecoration(color: const Color(0xFF00A896), borderRadius: BorderRadius.circular(20.r)),
                        alignment: Alignment.centerRight,
                        padding: EdgeInsets.only(right: 20.w),
                        child: Row(mainAxisSize: MainAxisSize.min, children: [Icon(Icons.mark_email_read_rounded, color: Colors.white, size: 18.sp), SizedBox(width: 6.w), Text('قراءة', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 12.sp))]),
                      ),
                      secondaryBackground: Container(
                        margin: EdgeInsets.only(bottom: 12.h),
                        decoration: BoxDecoration(color: const Color(0xFFE63946), borderRadius: BorderRadius.circular(20.r)),
                        alignment: Alignment.centerLeft,
                        padding: EdgeInsets.only(left: 20.w),
                        child: Row(mainAxisSize: MainAxisSize.min, children: [Text('حذف', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 12.sp)), SizedBox(width: 6.w), Icon(Icons.delete_rounded, color: Colors.white, size: 18.sp)]),
                      ),
                      onDismissed: (dir) {
                        setState(() => notifications.removeWhere((e) => e.id == n.id));
                        if (dir == DismissDirection.endToStart) {
                          NotificationFunctions.deleteNotification(notificationID: n.id.toString()).then((_) {
                            if (mounted) ref.refresh(notificationListProvider);
                          });
                        } else {
                          NotificationFunctions.readNotification(notificationID: n.id.toString()).then((_) {
                            if (mounted) ref.refresh(notificationListProvider);
                          });
                        }
                      },
                      child: Container(
                        margin: EdgeInsets.only(bottom: 12.h),
                        padding: EdgeInsets.all(16.w),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20.r),
                          border: Border.all(color: isRead ? const Color(0xFFE2E8F0).withOpacity(0.9) : const Color(0xFF00A896).withOpacity(0.22), width: isRead ? 1 : 1.2),
                          boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))],
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              width: 40.w, height: 40.h,
                              decoration: BoxDecoration(color: isRead ? const Color(0xFFF1F5F9) : const Color(0xFFEFFAF8), shape: BoxShape.circle, border: Border.all(color: isRead ? const Color(0xFFE2E8F0) : const Color(0xFF00A896).withOpacity(0.18))),
                              child: Icon(isRead ? Icons.notifications_none_rounded : Icons.notifications_active_rounded, size: 18.sp, color: isRead ? const Color(0xFF64748B) : const Color(0xFF028090)),
                            ),
                            SizedBox(width: 12.w),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(children: [
                                    Expanded(child: Text(translateNotificationText(n.title), style: TextStyle(fontSize: 13.sp, fontWeight: isRead ? FontWeight.w700 : FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'))),
                                    if (!isRead) Container(width: 8.w, height: 8.h, decoration: const BoxDecoration(color: Color(0xFF00A896), shape: BoxShape.circle)),
                                  ]),
                                  SizedBox(height: 4.h),
                                  Text(translateNotificationText(n.message), style: TextStyle(fontSize: 11.5.sp, height: 1.4, color: const Color(0xFF475569), fontWeight: isRead ? FontWeight.w500 : FontWeight.w600)),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

class NoNotificationWidget extends StatelessWidget {
  const NoNotificationWidget({super.key});
  @override
  Widget build(BuildContext context) {
    return Container(
      margin: EdgeInsets.only(top: 40.h),
      padding: EdgeInsets.all(24.w),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.05), blurRadius: 18)]),
      child: Column(
        children: [
          Container(width: 80.w, height: 80.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), shape: BoxShape.circle), child: Icon(Icons.notifications_none_rounded, size: 36.sp, color: const Color(0xFF94A3B8))),
          SizedBox(height: 16.h),
          Text('لا يوجد إشعارات', style: TextStyle(fontSize: 15.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
          SizedBox(height: 6.h),
          Text('ستظهر هنا تنبيهات طلباتك وعروض المتاجر', style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B)), textAlign: TextAlign.center),
        ],
      ),
    );
  }
}
