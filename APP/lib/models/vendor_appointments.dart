class AppointmentSlot {
  const AppointmentSlot(
      {required this.date,
      required this.hour,
      required this.title,
      required this.available,
      this.reason});
  final String date;
  final String hour;
  final String title;
  final bool available;
  final String? reason;
  factory AppointmentSlot.fromMap(Map<String, dynamic> map) => AppointmentSlot(
      date: map['date'] as String,
      hour: map['hour'] as String,
      title: map['title'] as String,
      available: map['is_booked'] != true,
      reason: map['reason'] as String?);
  String get key => '$date $hour';
}

class VendorAppointments {
  const VendorAppointments(
      {required this.enabled,
      required this.pickup,
      required this.delivery,
      required this.processingMinutes});
  final bool enabled;
  final int processingMinutes;
  final List<AppointmentSlot> pickup;
  final List<AppointmentSlot> delivery;
  factory VendorAppointments.fromMap(Map<String, dynamic> map) {
    List<AppointmentSlot> slots(String type) => (map['days'] as List? ?? [])
        .expand((day) => (day as Map)[type] as List? ?? [])
        .map(
            (s) => AppointmentSlot.fromMap(Map<String, dynamic>.from(s as Map)))
        .toList();
    return VendorAppointments(
        enabled: map['enabled'] == true,
        processingMinutes: (map['processing_minutes'] as num?)?.toInt() ?? 0,
        pickup: slots('pickup'),
        delivery: slots('delivery'));
  }
}
