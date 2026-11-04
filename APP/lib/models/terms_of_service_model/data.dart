import 'dart:convert';

import 'package:laundry_customer/models/terms_of_service_model/setting.dart';

class Data {
  Setting? setting;
  // Extra contact details served by the backend from the super-admin
  // "صفحات الموقع" section. Null when the admin has not saved them yet
  // (the app then falls back to the AppConfig constants).
  String? phoneNumber;
  String? address;

  Data({this.setting, this.phoneNumber, this.address});

  @override
  String toString() =>
      'Data(setting: $setting, phoneNumber: $phoneNumber, address: $address)';

  factory Data.fromMap(Map<String, dynamic> data) => Data(
        setting: data['setting'] == null
            ? null
            : Setting.fromMap(data['setting'] as Map<String, dynamic>),
        phoneNumber: data['phone_number'] as String?,
        address: data['address'] as String?,
      );

  Map<String, dynamic> toMap() => {
        'setting': setting?.toMap(),
        'phone_number': phoneNumber,
        'address': address,
      };

  /// `dart:convert`
  ///
  /// Parses the string and returns the resulting Json object as [Data].
  factory Data.fromJson(String data) {
    return Data.fromMap(json.decode(data) as Map<String, dynamic>);
  }

  /// `dart:convert`
  ///
  /// Converts [Data] to a JSON string.
  String toJson() => json.encode(toMap());

  Data copyWith({
    Setting? setting,
    String? phoneNumber,
    String? address,
  }) {
    return Data(
      setting: setting ?? this.setting,
      phoneNumber: phoneNumber ?? this.phoneNumber,
      address: address ?? this.address,
    );
  }
}
