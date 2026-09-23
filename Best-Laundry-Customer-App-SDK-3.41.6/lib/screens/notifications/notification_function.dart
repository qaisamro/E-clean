import 'package:laundry_customer/services/api_service.dart';

class NotificationFunctions {
  NotificationFunctions._();

  static Future<void> readNotification({required String notificationID}) async {
    await getDio().post(
      '/notification/$notificationID',
      data: {
        'isRead': 1,
      },
    );
  }

  static Future<void> deleteNotification({
    required String notificationID,
  }) async {
    await getDio().delete(
      '/notification/$notificationID',
    );
  }
}
