import 'package:flutter_test/flutter_test.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/notfiers/auth_notifiers.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/repos/auth_repo.dart';
import 'package:laundry_customer/screens/auth/password_recovery.dart';
import 'package:laundry_customer/models/auth_flow_model.dart';

class DisabledRecoveryRepo extends OfflineAuthRepo {
  @override
  Future<PasswordRecoveryFlow> forgotPassword({required String contact}) async =>
      const PasswordRecoveryFlow(requiresOtp: false, message: 'تواصل مع الدعم لاستعادة حسابك.');
}

class RecoveryObserver extends NavigatorObserver {
  int pushes = 0;
  @override
  void didPush(Route<dynamic> route, Route<dynamic>? previousRoute) { pushes++; }
}

void main() {
  testWidgets('disabled recovery shows support and never opens OTP screen', (tester) async {
    final observer = RecoveryObserver();
    await tester.pumpWidget(ProviderScope(overrides: [
      forgotPassProvider.overrideWith((ref) => ForgotPassNotifier(DisabledRecoveryRepo())..forgotPassword('0599000000')),
    ], child: ScreenUtilInit(designSize: const Size(375, 812), builder: (_, __) => MaterialApp(
      localizationsDelegates: const [S.delegate],
      navigatorObservers: [observer], home: RecoverPasswordStageOne(),
    ))));
    await tester.pumpAndSettle();
    await tester.pump(const Duration(seconds: 3));
    expect(find.text('تواصل مع الدعم لاستعادة حسابك.'), findsOneWidget);
    expect(observer.pushes, 1);
    expect(tester.takeException(), isNull);
    await tester.pumpWidget(const SizedBox());
  });
  test('disabled recovery never routes to OTP, including legacy scalar flags', () {
    for (final flag in [false, 0, '0']) {
      expect(PasswordRecoveryFlow.fromMap({'data': {'otp_required': flag}}).requiresOtp, false);
    }
    final result = PasswordRecoveryFlow.fromMap({
      'message': 'تواصل مع الدعم',
      'data': {'next_step': 'contact_support', 'otp_required': false},
    });
    expect(result.requiresOtp, false);
    expect(result.otp, isEmpty);
    expect(result.message, 'تواصل مع الدعم');
  });
  test('enabled and old-server recovery keep the existing OTP path', () {
    expect(PasswordRecoveryFlow.fromMap({'data': {'otp_required': true}}).requiresOtp, true);
    final old = PasswordRecoveryFlow.fromMap({'data': {'otp': 1234}});
    expect(old.requiresOtp, true);
    expect(old.otp, '1234');
  });
  test('registration without OTP still respects pending approval', () {
    final ready = RegisterFlowModel.fromMap({'data': {'otp_required': false, 'next_step': 'ready'}});
    final pending = RegisterFlowModel.fromMap({'data': {'otp_required': false, 'next_step': 'await_approval'}});
    expect(ready.nextStep, AuthNextStep.ready);
    expect(pending.nextStep, AuthNextStep.awaitApproval);
    expect(pending.hasAccessToken, false);
  });
}
