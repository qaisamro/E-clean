import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';

/// Address state belongs to a session. Tokens are never written to logs.
final addressSessionProvider = StreamProvider<String>((ref) async* {
  final box = Hive.box(AppHSC.authBox);
  yield box.get(AppHSC.authToken)?.toString() ?? '';
  await for (final _ in box.watch(key: AppHSC.authToken)) {
    yield box.get(AppHSC.authToken)?.toString() ?? '';
  }
});
