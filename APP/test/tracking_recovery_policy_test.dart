import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:laundry_customer/models/master_model/data.dart';
import 'package:laundry_customer/screens/auth/join_request_waiting_screen.dart';

void main() {
  test('old and disabled policies hide recovery; enabled policy permits it', () {
    expect(Data.fromMap({}).trackingRecoveryAvailable, false);
    expect(Data.fromMap({'tracking_recovery_available': false}).trackingRecoveryAvailable, false);
    expect(Data.fromMap({'tracking_recovery_available': true}).trackingRecoveryAvailable, true);
  });
  for (final available in [false, true]) {
    testWidgets('recovery card follows server availability $available', (tester) async {
      await tester.pumpWidget(ProviderScope(overrides: [
        trackingRecoveryAvailableProvider.overrideWith((ref) async => available),
      ], child: ScreenUtilInit(designSize: const Size(375, 812), builder: (_, __) => MaterialApp(
        home: JoinRequestWaitingScreen(args: JoinRequestWaitingArgs(contact: '0599000000')),
      ))));
      await tester.pumpAndSettle();
      expect(find.text('طلبك قيد المراجعة'), findsOneWidget);
      expect(find.text('هل فقدت رمز المتابعة؟'), available ? findsOneWidget : findsNothing);
      expect(find.text('أدخل رقم جوالك أو بريدك المسجّل وسنرسل لك رمز تحقق لاستعادة رمز المتابعة.'), available ? findsOneWidget : findsNothing);
      expect(tester.takeException(), isNull);
      await tester.pumpWidget(const SizedBox());
    });
  }
}
