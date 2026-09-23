import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/order_update_provider.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:upgrader/upgrader.dart';

class HomeScreenWrapper extends ConsumerWidget {
  HomeScreenWrapper({super.key, this.color = const Color(0xFFEAF6F8), required this.child});
  final Color color;
  final Widget child;
  final Box cartsBox = Hive.box(AppHSC.cartBox);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    // الترتيب البصري من اليمين لليسار كما في HTML: حسابي | إشعار | (وسط) | طلبي | عربة
    // لكن PageView ترتيبه: 0=عربة, 1=طلبي, 2=إشعار, 3=حسابي, 4=هوم
    final bottomItems = [
      {'icon': Icons.person_outline_rounded, 'title': 'حسابي', 'page': 3},
      {'icon': Icons.notifications_none_rounded, 'title': 'إشعار', 'page': 2},
      {'icon': Icons.receipt_long_outlined, 'title': 'طلبي', 'page': 1},
      {'icon': Icons.shopping_cart_outlined, 'title': 'عربة التسوق', 'page': 0},
    ];

    AppGFunctions.changeStatusBarColor(color: Colors.transparent);
    final rawActiveIndex = ref.watch(homeScreenIndexProvider);
    final pageController = ref.watch(homeScreenPageControllerProvider);
    ref.watch(addresListProvider);
    final bool showFab = MediaQuery.of(context).viewInsets.bottom == 0.0;

    Widget buildItem(int index) {
      final int pageForItem = bottomItems[index]['page'] as int;
      final bool isActive = rawActiveIndex == pageForItem;
      return Expanded(
        child: InkWell(
          borderRadius: BorderRadius.circular(12.r),
          onTap: () {
            pageController.animateToPage(pageForItem, duration: transissionDuration, curve: Curves.easeInOut);
            ref.read(homeScreenIndexProvider.notifier).state = pageForItem;
          },
          child: ValueListenableBuilder(
            valueListenable: cartsBox.listenable(),
            builder: (context, Box box, _) {
              final badge = CartHelper.getTotalItemCount();
              return Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Stack(
                    clipBehavior: Clip.none,
                    children: [
                      Icon(bottomItems[index]['icon'] as IconData, size: 24.sp, color: isActive ? Color(0xFF156172) : const Color(0xFF1E293B)),
                      if (index == 3 && badge > 0)
                        Positioned(
                          right: -8.w,
                          top: -8.h,
                          child: Container(
                            padding: EdgeInsets.symmetric(horizontal: 5.w, vertical: 2.h),
                            decoration: BoxDecoration(color: const Color(0xFFF59E0B), borderRadius: BorderRadius.circular(10.r), border: Border.all(color: Colors.white, width: 1.5)),
                            child: Text("$badge", style: TextStyle(color: Colors.white, fontSize: 9.sp, fontWeight: FontWeight.w900)),
                          ),
                        ),
                      if (index == 1 && !isActive)
                        Positioned(right: -1.w, top: -1.h, child: Container(width: 7.w, height: 7.h, decoration: BoxDecoration(color: const Color(0xFFE63946), shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 1.2)))),
                    ],
                  ),
                  SizedBox(height: 4.h),
                  Text(bottomItems[index]['title'] as String, style: TextStyle(fontSize: 10.sp, fontWeight: isActive ? FontWeight.w900 : FontWeight.w700, color: isActive ? Color(0xFF156172) : const Color(0xFF1E293B), height: 1)),
                ],
              );
            },
          ),
        ),
      );
    }

    return Scaffold(
      extendBody: true,
      body: UpgradeAlert(
        showIgnore: false,
        showLater: false,
        upgrader: Upgrader(durationUntilAlertAgain: const Duration(minutes: 10)),
        child: Container(width: 375.w, height: 812.h, color: color, child: child),
      ),
      floatingActionButton: Visibility(
        visible: showFab,
        child: Container(
          width: 58.w,
          height: 58.h,
          decoration: BoxDecoration(
            gradient: const LinearGradient(colors: [Color(0xFF156172), Color(0xFF1b758a)], begin: Alignment.topLeft, end: Alignment.bottomRight),
            borderRadius: BorderRadius.circular(16.r),
            boxShadow: [BoxShadow(color: Color(0xFF1b758a).withOpacity(0.42), blurRadius: 18, offset: const Offset(0, 8))],
            border: Border.all(color: Color(0xFFEAF6F8), width: 4),
          ),
          child: FloatingActionButton(
            backgroundColor: Colors.transparent,
            elevation: 0,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r)),
            onPressed: () {
              pageController.animateToPage(4, duration: transissionDuration, curve: Curves.easeInOut);
              ref.read(homeScreenIndexProvider.notifier).state = 4;
              if (ref.read(orderIdProvider) != '') {
                CartHelper.clearAll();
                ref.invalidate(vendorCartsProvider);
                ref.read(orderIdProvider.notifier).state = '';
              }
            },
            child: Container(
              width: 36.w,
              height: 36.h,
              decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle, boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.12), blurRadius: 8, offset: const Offset(0, 2))]),
              padding: EdgeInsets.all(5.w),
              child: ClipOval(child: Image.asset('assets/images/app_icon.png', fit: BoxFit.cover, errorBuilder: (_, __, ___) => Icon(Icons.waves_rounded, color: Color(0xFF156172), size: 20.sp))),
            ),
          ),
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
      bottomNavigationBar: Container(
        margin: EdgeInsets.fromLTRB(12.w, 0, 12.w, 12.h),
        height: 68.h,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24.r),
          border: Border.all(color: const Color(0xFFE2E8F0), width: 1.2),
          boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.12), blurRadius: 22, offset: const Offset(0, -4)), BoxShadow(color: Colors.black.withOpacity(0.07), blurRadius: 10, offset: const Offset(0, 2))],
        ),
        child: Row(
          children: [
            buildItem(0),
            buildItem(1),
            SizedBox(width: 58.w),
            buildItem(2),
            buildItem(3),
          ],
        ),
      ),
    );
  }
}




