import 'dart:convert';

class Schedule {
  String? hour;
  String? title;
  bool isBooked;

  Schedule({this.hour, this.title, this.isBooked = false});

  factory Schedule.fromMap(Map<String, dynamic> data) => Schedule(
        hour: data['hour'] as String?,
        title: data['title'] as String?,
        isBooked: data['is_booked'] == true || data['is_booked'] == 1,
      );

  Map<String, dynamic> toMap() => {
        'hour': hour,
        'title': title,
        'is_booked': isBooked,
      };

  /// `dart:convert`
  ///
  /// Parses the string and returns the resulting Json object as [Schedule].
  factory Schedule.fromJson(String data) {
    return Schedule.fromMap(json.decode(data) as Map<String, dynamic>);
  }

  /// `dart:convert`
  ///
  /// Converts [Schedule] to a JSON string.
  String toJson() => json.encode(toMap());

  Schedule copyWith({
    String? hour,
    String? title,
    bool? isBooked,
  }) {
    return Schedule(
      hour: hour ?? this.hour,
      title: title ?? this.title,
      isBooked: isBooked ?? this.isBooked,
    );
  }
}
