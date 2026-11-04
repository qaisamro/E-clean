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
    final bottomItems = [
      {
        'icon': Icons.person_outline_rounded,
        'activeIcon': Icons.person_rounded,
        'title': 'حسابي',
        'page': 3,
      },
      {
        'icon': Icons.notifications_none_rounded,
        'activeIcon': Icons.notifications_rounded,
        'title': 'إشعاراتي',
        'page': 2,
      },
      {
        'icon': Icons.receipt_long_outlined,
        'activeIcon': Icons.receipt_long_rounded,
        'title': 'طلباتي',
        'page': 1,
      },
      {
        'icon': Icons.shopping_bag_outlined,
        'activeIcon': Icons.shopping_bag_rounded,
        'title': 'السلة',
        'page': 0,
      },
    ];

AppGFunctions.changeStatusBarColor(color: Colors.transparent);
    final rawActiveIndex = ref.watch(homeScreenIndexProvider);
    final pageController = ref.watch(homeScreenPageControllerProvider);
    ref.watch(addresListProvider);
    final isHomeActive = rawActiveIndex == 4;

    Widget buildItem(int index) {
      final int pageForItem = bottomItems[index]['page'] as int;
      final bool isActive = rawActiveIndex == pageForItem;
      final IconData icon = (isActive
          ? bottomItems[index]['activeIcon']
          : bottomItems[index]['icon']) as IconData;

      return Expanded(
        child: InkWell(
          borderRadius: BorderRadius.circular(16.r),
          onTap: () {
            pageController.animateToPage(
              pageForItem,
              duration: transissionDuration,
              curve: Curves.easeInOut,
            );
            ref.read(homeScreenIndexProvider.notifier).state = pageForItem;
          },
          child: ValueListenableBuilder(
            valueListenable: cartsBox.listenable(),
            builder: (context, Box box, _) {
              final badge = CartHelper.getTotalItemCount();
              return AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding: EdgeInsets.symmetric(vertical: 4.h),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Stack(
                      clipBehavior: Clip.none,
                      children: [
                        Container(
                          padding: EdgeInsets.symmetric(
                            horizontal: 10.w,
                            vertical: 4.h,
                          ),
                          decoration: BoxDecoration(
                            color: isActive
                                ? const Color(0xFFEAF6F8)
                                : Colors.transparent,
                            borderRadius: BorderRadius.circular(14.r),
                          ),
                          child: Icon(
                            icon,
                            size: 22.sp,
                            color: isActive
                                ? const Color(0xFF156172)
                                : const Color(0xFF64748B),
                          ),
                        ),
                        // Cart Badge
                        if (index == 3 && badge > 0)
                          Positioned(
                            right: 0,
                            top: -4.h,
                            child: Container(
                              padding: EdgeInsets.symmetric(
                                horizontal: 5.w,
                                vertical: 1.5.h,
                              ),
                              decoration: BoxDecoration(
                                gradient: const LinearGradient(
                                  colors: [Color(0xFFFF9800), Color(0xFFFF5722)],
                                ),
                                borderRadius: BorderRadius.circular(10.r),
                                border: Border.all(color: Colors.white, width: 1.5),
                                boxShadow: [
                                  BoxShadow(
                                    color: const Color(0xFFFF5722).withOpacity(0.3),
                                    blurRadius: 4,
                                    offset: const Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: Text(
                                "$badge",
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 8.5.sp,
                                  fontWeight: FontWeight.w900,
                                  fontFamily: 'Cairo',
                                ),
                              ),
                            ),
                          ),
                        // Notifications Notification Dot
                        if (index == 1 && !isActive)
                          Positioned(
                            right: 4.w,
                            top: 0,
                            child: Container(
                              width: 7.w,
                              height: 7.h,
                              decoration: BoxDecoration(
                                color: const Color(0xFFFF3B30),
                                shape: BoxShape.circle,
                                border: Border.all(color: Colors.white, width: 1.2),
                              ),
                            ),
                          ),
                      ],
                    ),
                    SizedBox(height: 2.h),
                    Text(
                      bottomItems[index]['title'] as String,
                      style: TextStyle(
                        fontSize: 10.sp,
                        fontWeight: isActive ? FontWeight.w900 : FontWeight.w600,
                        color: isActive
                            ? const Color(0xFF156172)
                            : const Color(0xFF64748B),
                        fontFamily: 'Cairo',
                        height: 1.1,
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ),
      );
    }

Widget buildNewOrderItem() {
      return Expanded(
        child: InkWell(
          borderRadius: BorderRadius.circular(16.r),
          onTap: () {
            pageController.animateToPage(
              4,
              duration: transissionDuration,
              curve: Curves.easeInOut,
            );
            ref.read(homeScreenIndexProvider.notifier).state = 4;
            if (ref.read(orderIdProvider) != '') {
              CartHelper.clearAll();
              ref.invalidate(vendorCartsProvider);
              ref.read(orderIdProvider.notifier).state = '';
            }
          },
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 200),
            padding: EdgeInsets.symmetric(vertical: 4.h),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  width: 42.w,
                  height: 42.h,
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFF156172), Color(0xFF1b758a)],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFF156172).withOpacity(0.3),
                        blurRadius: 8,
                        offset: const Offset(0, 3),
                      ),
                    ],
                  ),
                  child: Icon(
                    isHomeActive
                        ? Icons.add_circle_rounded
                        : Icons.add_rounded,
                    color: Colors.white,
                    size: 24.sp,
                  ),
                ),
                SizedBox(height: 2.h),
                Text(
                  'طلب جديد',
                  style: TextStyle(
                    fontSize: 10.sp,
                    fontWeight: isHomeActive ? FontWeight.w900 : FontWeight.w700,
                    color: isHomeActive
                        ? const Color(0xFF156172)
                        : const Color(0xFF64748B),
                    fontFamily: 'Cairo',
                    height: 1.1,
                  ),
                ),
              ],
            ),
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
      bottomNavigationBar: Container(
        margin: EdgeInsets.fromLTRB(16.w, 0, 16.w, 14.h),
        height: 68.h,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(24.r),
          border: Border.all(
            color: const Color(0xFFE2E8F0).withOpacity(0.9),
            width: 1.2,
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF0C2238).withOpacity(0.09),
              blurRadius: 22,
              offset: const Offset(0, 6),
            ),
            BoxShadow(
              color: Colors.black.withOpacity(0.03),
              blurRadius: 8,
              offset: const Offset(0, -2),
            ),
          ],
        ),
        child: Row(
          children: [
            buildItem(0),
            buildItem(1),
            buildNewOrderItem(),
            buildItem(2),
            buildItem(3),
          ],
        ),
      ),
    );
  }
}




