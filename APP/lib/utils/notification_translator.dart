final Map<String, String> _notificationTranslateMap = {
  'New Order': 'طلب جديد',
  'New Order has been placed': 'تم تقديم طلب جديد',
  'Coupon Notification': 'إشعار كوبون',
  'Get Product discount Coupon': 'احصل على كوبون خصم على منتجاتنا',
  'Coupon Discount': 'خصم كوبون',
  'Order Confirmed': 'تم تأكيد الطلب',
  'Your order has been confirmed': 'تم تأكيد طلبك',
  'Order Picked': 'تم استلام الطلب',
  'Your order has been picked by delivery boy': 'قام سائق التوصيل باستلام طلبك',
  'Order Processing': 'طلب قيد التجهيز',
  'Your order has been processed': 'جارٍ تجهيز طلبك',
  'Order Cancelled': 'تم إلغاء الطلب',
  'Your order has been cancelled': 'تم إلغاء طلبك',
  'Order Delivered': 'تم تسليم الطلب',
  'Your order has been delivered successfully': 'تم تسليم طلبك بنجاح',
  'Driver Assigned': 'تم تعيين سائق',
  'Delivery boy has been assigned to your order': 'تم تعيين سائق توصيل لطلبك',
  'Order Status Update': 'تحديث حالة الطلب',
  'Order status update': 'تحديث حالة الطلب',
  'Order Status Updated': 'تحديث حالة الطلب',
  'Assign Order': 'طلب جديد',
  'Order Accepted': 'تم قبول الطلب',
  'Your order has been accepted': 'تم قبول طلبك',
  'Pending': 'قيد الانتظار',
  'Order confirmed': 'تم تأكيد الطلب',
  'Picked your order': 'تم استلام طلبك',
  'Picked up': 'تم الاستلام',
  'Processing': 'قيد المعالجة',
  'On Going': 'جارٍ التنفيذ',
  'Cancelled': 'ملغى',
  'Delivered': 'تم التسليم',
  'Paid': 'مدفوع',
  'Unpaid': 'غير مدفوع',
  'Cash on Delivery': 'الدفع عند الاستلام',
  'Online Payment': 'الدفع الإلكتروني',
};

final RegExp _arabicRegex = RegExp(r'[\u0600-\u06FF]');
final RegExp _helloRegex =
    RegExp(r'^Hello (.+?)\. Your order status is (.+?)\. OrderID: (.+)$');
final RegExp _assignRegex =
    RegExp(r'^You have received a (pick-up|delivery) request\. Order ID: (LM\S+)$');

/// تحويل نص الإشعار من الإنجليزية إلى العربية (يترك النص العربي كما هو)
String translateNotificationText(String? text) {
  final String input = text?.trim() ?? '';
  if (input.isEmpty) return text ?? '';
  if (_arabicRegex.hasMatch(input)) return input;

  final String? direct = _notificationTranslateMap[input];
  if (direct != null) return direct;

  final Match? hello = _helloRegex.firstMatch(input);
  if (hello != null) {
    final String status =
        _notificationTranslateMap[hello.group(2)!] ?? hello.group(2)!;
    return 'مرحبًا ${hello.group(1)}، تم تحديث حالة طلبك إلى $status. رقم الطلب: ${hello.group(3)}';
  }

  final Match? assign = _assignRegex.firstMatch(input);
  if (assign != null) {
    final String type = assign.group(1) == 'pick-up' ? 'استلام' : 'تسليم';
    return 'تم تكليفك بطلب $type. رقم الطلب: ${assign.group(2)}';
  }

  return input;
}
