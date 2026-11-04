import 'package:flutter/foundation.dart';

class AppConfig {
  AppConfig._();

  // debug  -> local server  (needs: adb reverse tcp:8000 tcp:8000)
  // release -> production
  // Override any time with: --dart-define=API_BASE_URL=https://...
  static const String _defaultApiBase =
      kDebugMode ? 'http://localhost:8000/api' : 'https://elitecleanps.com/api';
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: _defaultApiBase,
  );

  //Stripe Keys For App - Replace With Yours
  static const String secretKey = 'sk_test_dummy';
  static const String publicKey = 'pk_test_dummy';

  //One Signal
  static const String oneSignalAppID =
      '96fa9ec8-39bc-4395-9f3b-2c30fd9fdc3e'; // One Signal App ID

  static const String appName = 'E Clean';

  //Contact US Config
  static const String ctAboutCompany = ''; //Company name And Address (empty = hidden)
  static const String ctWhatsApp = '+97059925567'; // whats app Number with Country Code
  static const String ctPhone = '0592925567'; // Contact Phone Numbers
  static const String ctMail = ''; // Contact Mail (empty = hidden)
}
