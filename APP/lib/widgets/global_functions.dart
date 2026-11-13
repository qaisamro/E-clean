import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';

String getLng({String? en, String? changeLang}) {
  final lang = Hive.box(AppHSC.appSettingsBox)
          .get(AppHSC.appLocal)
          ?.toString() ??
      'ar';
  if (lang == 'en') {
    return en ?? changeLang ?? '';
  } else {
    if (changeLang != null && changeLang != "null") {
      return changeLang;
    }
    return en ?? '';
  }
}
