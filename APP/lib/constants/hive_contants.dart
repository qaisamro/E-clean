class AppHSC {
  AppHSC._();
  //Box Names
  static const String appSettingsBox = 'laundry_appSettingsBox';
  static const String authBox = 'laundry_authBox';
  static const String userBox = 'laundry_userBox';
  static const String cartBox = 'laundry_cartBox';
  static const String messageBox = 'laundry_messageBox';

  //Variable Names
  //Auth Variable Names
  static const String authTokenType = 'auth_type';
  static const String authToken = 'token';

  //Customer join request (admin approval) - never logged, never in a URL.
  static const String trackingToken = 'join_request_tracking_token';
  static const String pendingContact = 'join_request_pending_contact';

  //Settings Variable Names
  static const String hasSeenSplashScreen = 'hasSeenSplashScreen';
  static const String appLocal = 'appLocal';
}
