import 'package:flutter/foundation.dart';

/// Bumped every time a push message arrives while the app is in the
/// foreground. Screens that show server data (e.g. the notifications
/// tab) listen to this and refresh instantly instead of waiting for
/// a manual reopen.
final ValueNotifier<int> pushReceived = ValueNotifier<int>(0);
