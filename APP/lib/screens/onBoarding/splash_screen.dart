import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/screen_wrapper.dart';
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';

/// Brand splash. It always lands on the home screen - the onboarding /
/// "skip" / "get started" flow was removed at the product owner's request.
class SplashScreen extends ConsumerStatefulWidget {
  const SplashScreen({super.key});

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen> {
  final Box appSettingsBox = Hive.box(AppHSC.appSettingsBox);

  @override
  void initState() {
    super.initState();
    // Kept for backward compatibility with any earlier install that still
    // has this flag; the onboarding screen is never shown anymore.
    appSettingsBox.put(AppHSC.hasSeenSplashScreen, true);
    _syncDeviceKey();
    Future.delayed(const Duration(seconds: 2), () {
      context.nav.pushNamedAndRemoveUntil(
        Routes.homeScreen,
        (route) => false,
      );
    });
  }

  /// Sends the current device's FCM key to the server if the customer is
  /// signed in, so notifications reach them even if their key was never
  /// stored. Runs silently in the background and never blocks navigation.
  Future<void> _syncDeviceKey() async {
    try {
      final authToken = Hive.box(AppHSC.authBox).get(AppHSC.authToken);
      if (authToken == null || authToken.toString().isEmpty) return;
      await ref.read(authRepoProvider).syncDeviceKey();
    } catch (_) {/* best effort only */}
  }

  @override
  Widget build(BuildContext context) {
    return ScreenWrapper(
      child: Center(
        child: Hero(
          tag: 'logo',
          child: Image.asset(
            'assets/images/app_icon.png',
            height: 170.h,
            width: 170.w,
            fit: BoxFit.contain,
          ),
        ),
      ),
    );
  }
}
