import 'package:app_settings/app_settings.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/screens/cart/my_cart_tab.dart';
import 'package:laundry_customer/screens/homePage/home_tab_new.dart';
// import 'package:laundry_customer/screens/homePage/home_tab.dart';
import 'package:laundry_customer/screens/homePage/my_notfications_tab.dart';
import 'package:laundry_customer/screens/homePage/tab_profile_unsigned.dart';
import 'package:laundry_customer/screens/order/my_orders_tab.dart';
import 'package:laundry_customer/widgets/home_screen_wrapper.dart';
import 'package:laundry_customer/widgets/keep_alive_page.dart';
import 'package:permission_handler/permission_handler.dart';

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  @override
  void initState() {
    super.initState();
    requestNotificationPermission();
  }

  Future<void> requestNotificationPermission() async {
    final PermissionStatus status = await Permission.notification.status;
    if (status == PermissionStatus.granted) {
      if (kDebugMode) {
        print('Notification permission is granted');
      }
    } else if (status == PermissionStatus.denied) {
      await Permission.notification.request().then((status) {
        if (status.isDenied) {
          AppSettings.openAppSettings(type: AppSettingsType.notification);
        }
      });
    } else if (status == PermissionStatus.permanentlyDenied) {
      AppSettings.openAppSettings(type: AppSettingsType.notification);
    }
  }

  @override
  Widget build(BuildContext context) {
    ref.watch(settingsProvider);
    return HomeScreenWrapper(
      child: PageView(
        controller: ref.watch(homeScreenPageControllerProvider),
        physics: const NeverScrollableScrollPhysics(),
        children: [
          KeepAlivePage(child: MyCartTab()),
          const KeepAlivePage(child: MyOrdersTab()),
          KeepAlivePage(child: MyNotificationsTab()),
          const KeepAlivePage(child: UsignedUserTab()),
          const KeepAlivePage(child: NewHomeTab()),
        ],
      ),
    );
  }
}
