class AppConfig {
  AppConfig._();

  // USB/debug and release builds use production by default.
  // Local development override: --dart-define=API_BASE_URL=http://localhost:8000/api
  static const String _defaultApiBase =
      'https://elitecleanps.com/api';
  static const String baseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: _defaultApiBase,
  );

  //Online card payments were removed from the app. Orders are placed as
  //cash-on-delivery only (see PaymentType in payment_screen.dart).

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
