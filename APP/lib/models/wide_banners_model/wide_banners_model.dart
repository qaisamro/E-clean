import 'dart:convert';

import 'package:laundry_customer/models/wide_banners_model/data.dart';

class WideBannersModel {
  String? message;
  Data? data;

  WideBannersModel({this.message, this.data});

  @override
  String toString() => 'WideBannersModel(message: $message, data: $data)';

  factory WideBannersModel.fromMap(Map<String, dynamic> data) {
    return WideBannersModel(
      message: data['message'] as String?,
      data: data['data'] == null
          ? null
          : Data.fromMap(data['data'] as Map<String, dynamic>),
    );
  }

  Map<String, dynamic> toMap() => {
        'message': message,
        'data': data?.toMap(),
      };

  /// `dart:convert`
  ///
  /// Parses the string and returns the resulting Json object as [WideBannersModel].
  factory WideBannersModel.fromJson(String data) {
    return WideBannersModel.fromMap(json.decode(data) as Map<String, dynamic>);
  }

  /// `dart:convert`
  ///
  /// Converts [WideBannersModel] to a JSON string.
  String toJson() => json.encode(toMap());

  WideBannersModel copyWith({
    String? message,
    Data? data,
  }) {
    return WideBannersModel(
      message: message ?? this.message,
      data: data ?? this.data,
    );
  }
}