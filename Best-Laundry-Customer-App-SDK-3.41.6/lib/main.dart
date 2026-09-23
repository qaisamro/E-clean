import 'dart:io';

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
// import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_stripe/flutter_stripe.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/config.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/firebase_options.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/notification_translator.dart';
import 'package:laundry_customer/utils/routes.dart';

// @pragma('vm:entry-point')
Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  );
  await setupFlutterNotifications();
  showFlutterNotification(message);
  debugPrint('Handling a background message ${message.messageId}');
}

Future<void> _firebaseMessagingForgroundHandler() async {
  FirebaseMessaging.onMessage.listen((message) {
    debugPrint(message.data.toString());
    debugPrint(message.toString());
    debugPrint('Handling a ForeGround message ${message.messageId}');
    debugPrint('Handling a ForeGround message ${message.notification}');
    showFlutterNotification(message);
  });
}

void handleMessage(RemoteMessage? message) {
  if (message == null) return;
  if (message.data['type'] == 'Conversetion') {
    // ContextLess.navigatorkey.currentState!
    //     .pushNamedAndRemoveUntil(Routes.homeScreen, (route) => false);
    // ContextLess.navigatorkey.currentState!.pushNamed(
    //   Routes.messageScreen,
    //   arguments: MessageScreenArgument(
    //     orderId: int.parse(message.data['orderId'].toString()),
    //     senderId: int.parse(message.data['receiverId'].toString()),
    //     receiverId: int.parse(message.data['senderId'].toString()),
    //   ),
    // );
  }
}

/// Create a [AndroidNotificationChannel] for heads up notifications
late AndroidNotificationChannel channel;

bool isFlutterLocalNotificationsInitialized = false;

Future<void> setupFlutterNotifications() async {
  if (isFlutterLocalNotificationsInitialized) {
    return;
  }
  channel = const AndroidNotificationChannel(
    'high_importance_channel', // id
    'High Importance Notifications', // title
    description:
        'This channel is used for important notifications.', // description
    importance: Importance.high,
  );

  flutterLocalNotificationsPlugin = FlutterLocalNotificationsPlugin();
  await flutterLocalNotificationsPlugin
      .resolvePlatformSpecificImplementation<
          AndroidFlutterLocalNotificationsPlugin>()
      ?.createNotificationChannel(channel);
  await flutterLocalNotificationsPlugin
      .resolvePlatformSpecificImplementation<
          IOSFlutterLocalNotificationsPlugin>()
      ?.requestPermissions(
        alert: true,
        badge: true,
      );

  const InitializationSettings initializationSettings = InitializationSettings(
    android: AndroidInitializationSettings('@drawable/ic_stat_launcher'),
    iOS: DarwinInitializationSettings(),
  );
  await flutterLocalNotificationsPlugin.initialize(
    initializationSettings,
    onDidReceiveBackgroundNotificationResponse: onDidReceiveLocalNotification,
    onDidReceiveNotificationResponse: onSelectNotification,
  );

  await FirebaseMessaging.instance.setForegroundNotificationPresentationOptions(
    alert: true,
    badge: true,
    sound: true,
  );

  isFlutterLocalNotificationsInitialized = true;
}

Future<void> onSelectNotification(
  NotificationResponse notificationResponse,
) async {
  // final List<String> parts = notificationResponse.payload!.split('_');
  // final int orderId = int.parse(parts[0]);
  // final int senderId = int.parse(parts[1]);
  // final int receiverId = int.parse(parts[2]);
  // ContextLess.navigatorkey.currentState!.pushNamedAndRemoveUntil(
  //   Routes.messageScreen,
  //   arguments: MessageScreenArgument(
  //     orderId: orderId,
  //     senderId: receiverId,
  //     receiverId: senderId,
  //   ),
  //   (route) => true,
  // );
}

Future<void> onDidReceiveLocalNotification(
  NotificationResponse notificationResponse,
) async {
  // final List<String> parts = notificationResponse.payload!.split('_');
  // final int orderId = int.parse(parts[0]);
  // final int senderId = int.parse(parts[1]);
  // final int receiverId = int.parse(parts[2]);
  // ContextLess.navigatorkey.currentState!.pushNamedAndRemoveUntil(
  //   Routes.messageScreen,
  //   arguments: MessageScreenArgument(
  //     orderId: orderId,
  //     senderId: receiverId,
  //     receiverId: senderId,
  //   ),
  //   (route) => true,
  // );
}

void showFlutterNotification(RemoteMessage message) {
  final String combinedPayload =
      '${message.data['orderId']}_${message.data['senderId']}_${message.data['receiverId']}';
  final RemoteNotification? notification = message.notification;
  final AndroidNotification? android = message.notification?.android;
  final AppleNotification? iOS = message.notification?.apple;
  if (notification != null && (android != null || iOS != null) && !kIsWeb) {
    flutterLocalNotificationsPlugin.show(
      notification.hashCode,
      translateNotificationText(notification.title),
      translateNotificationText(notification.body),
      NotificationDetails(
        android: AndroidNotificationDetails(
          channel.id,
          channel.name,
          channelDescription: channel.description,
          icon: '@drawable/ic_stat_launcher',
        ),
        iOS: const DarwinNotificationDetails(
          presentAlert: true,
          presentBadge: true,
          presentSound: true,
        ),
      ),
      payload: combinedPayload,
    );
  }
}

/// Initialize the [FlutterLocalNotificationsPlugin] package.
late FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin;

Future<void> _initFirebaseMessaging() async {
  if (kIsWeb) return;
  try {
    if (defaultTargetPlatform == TargetPlatform.android) {
      await Firebase.initializeApp(
        options: DefaultFirebaseOptions.currentPlatform,
      ).timeout(const Duration(seconds: 6));
    } else if (defaultTargetPlatform == TargetPlatform.iOS) {
      await Firebase.initializeApp().timeout(const Duration(seconds: 6));
    }
    await setupFlutterNotifications().timeout(const Duration(seconds: 5));
    // Background message handler
    FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);
    // Foreground message handler
    _firebaseMessagingForgroundHandler();

    final token = await FirebaseMessaging.instance
        .getToken()
        .timeout(const Duration(seconds: 5), onTimeout: () => null);
    debugPrint('Token : $token');
  } catch (e) {
    debugPrint('Firebase/messaging init skipped (non-fatal): $e');
  }
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  if (!kIsWeb) {
    Stripe.publishableKey = AppConfig.publicKey;
  }
  await _initFirebaseMessaging();
  // await oneSignalHandler();
  await Hive.initFlutter();
  await Hive.openBox(AppHSC.appSettingsBox);
  await Hive.openBox(AppHSC.authBox);
  await Hive.openBox(AppHSC.userBox);
  await Hive.openBox(AppHSC.cartBox);
  // Default language is Arabic - force to ar for first launch and migrate existing en to ar
  final appSettingsBox = Hive.box(AppHSC.appSettingsBox);
  final currentLang = appSettingsBox.get(AppHSC.appLocal);
  if (currentLang == null ||
      (currentLang as String).isEmpty ||
      currentLang == 'en') {
    await appSettingsBox.put(AppHSC.appLocal, 'ar');
  }
  HttpOverrides.global = MyHttpOverrides();
  runApp(
    const ProviderScope(
      child: MyApp(),
    ),
  );
}

class MyHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)
      ..badCertificateCallback =
          (X509Certificate cert, String host, int port) => true;
  }
}

class MyApp extends ConsumerStatefulWidget {
  const MyApp({super.key});

  @override
  ConsumerState<MyApp> createState() => _MyAppState();
}

class _MyAppState extends ConsumerState<MyApp> with WidgetsBindingObserver {
  Future<void> launchApp() async {
    if (kIsWeb) return;
    try {
      final RemoteMessage? initialMessage =
          await FirebaseMessaging.instance.getInitialMessage();
      handleMessage(initialMessage);
    } catch (e) {
      debugPrint('launchApp skipped (non-fatal): $e');
    }
  }

  @override
  void initState() {
    launchApp();
    // ref.read(socketProvider).initSocketConnection();
    WidgetsBinding.instance.addObserver(this);
    super.initState();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      // ref.read(socketProvider).initSocketConnection();
      if (kDebugMode) {
        print('App is resumed');
      }
    } else if (state == AppLifecycleState.paused) {
      // ref.read(socketProvider).socket!.dispose();
      if (kDebugMode) {
        print('app is paused');
      }
    } else if (state == AppLifecycleState.inactive) {
      // ref.read(socketProvider).valueSet(orderID: 0, show: true);
      if (kDebugMode) {
        print('inactive');
      }
    } else if (state == AppLifecycleState.detached) {
      if (kDebugMode) {
        print('app is detached');
      }
    }
    super.didChangeAppLifecycleState(state);
  }

  @override
  Widget build(BuildContext context) {
    final playerID = ref.watch(onesignalDeviceIDProvider);
    if (playerID == '') {
      // getPlayerID(ref);
    }

    final bool isWideScreen =
        kIsWeb ||
        defaultTargetPlatform == TargetPlatform.windows ||
        defaultTargetPlatform == TargetPlatform.linux ||
        defaultTargetPlatform == TargetPlatform.macOS;
    final winSize = MediaQuery.of(context).size;

    Widget buildApp() {
      return ScreenUtilInit(
        designSize: const Size(375, 812), // XD Design Size
        minTextAdapt: true,
        splitScreenMode: true,
        builder: (context, child) {
          return ValueListenableBuilder(
            valueListenable:
                Hive.box(AppHSC.appSettingsBox).listenable(),
            builder:
                (BuildContext context, Box appSettingsBox, Widget? child) {
              return MaterialApp(
                debugShowCheckedModeBanner: false,
                title: 'Elite Cleaning',

                localizationsDelegates: const [
                  S.delegate,
                  GlobalMaterialLocalizations.delegate,
                  GlobalWidgetsLocalizations.delegate,
                  GlobalCupertinoLocalizations.delegate,
                  FormBuilderLocalizations.delegate,
                ],
                // Arabic-only app: always force 'ar' regardless of saved/device locale
                locale: const Locale('ar'),
                localeResolutionCallback: (deviceLocale, supportedLocales) {
                  appSettingsBox.put(AppHSC.appLocal, 'ar');
                  return const Locale('ar');
                },
                supportedLocales: S.delegate.supportedLocales,
                theme: ThemeData(
                  fontFamily: "Poppins",
                  fontFamilyFallback: const [
                    'Noto Sans Arabic',
                    'Noto Naskh Arabic',
                    'Geeza Pro',
                    'Segoe UI',
                    'Tahoma',
                    'Arial',
                  ],
                ),
                navigatorKey: ContextLess
                    .navigatorkey, //Setting Global navigator key to navigate from anywhere without Context

                onGenerateRoute: (settings) => generatedRoutes(settings),
                initialRoute: Routes.splash,
                builder: EasyLoading.init(),
              );
            },
          );
        },
      );
    }

    if (!isWideScreen) {
      return buildApp();
    }

    // Wide screens (web/desktop): cap the layout at a phone-like 430px width.
    // Overriding MediaQuery makes ScreenUtil's .w scaling compute against
    // 430px instead of the full window width, so no right-side overflows.
    return ColoredBox(
      color: const Color(0xFF101014),
      child: Center(
        child: SizedBox(
          width: 430,
          height: winSize.height,
          child: MediaQuery(
            data: MediaQuery.of(context).copyWith(
              size: Size(430, winSize.height),
            ),
            child: buildApp(),
          ),
        ),
      ),
    );
  }
}
