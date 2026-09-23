import 'dart:convert';

import 'package:laundry_customer/models/vendor_model/vendor.dart';

class VendorsResponseModel {
  String? message;
  VendorsData? data;

  VendorsResponseModel({this.message, this.data});

  factory VendorsResponseModel.fromMap(Map<String, dynamic> map) {
    // Handle multiple response shapes:
    // 1. { message, data: { vendors: [...] } }
    // 2. { message, data: [...] }
    // 3. { vendors: [...] }
    // 4. { data: { data: [...] } } (paginated)
    // 5. Direct list wrapper

    if (map.containsKey('data')) {
      final dynamic dataField = map['data'];
      if (dataField is List) {
        // data is directly a list
        return VendorsResponseModel(
          message: map['message'] as String?,
          data: VendorsData(
            vendors: dataField
                .map((e) => Vendor.fromMap(e as Map<String, dynamic>))
                .toList(),
          ),
        );
      } else if (dataField is Map<String, dynamic>) {
        // check if data contains vendors, data, or is itself vendor map
        if (dataField.containsKey('vendors')) {
          return VendorsResponseModel(
            message: map['message'] as String?,
            data: VendorsData.fromMap(dataField),
          );
        } else if (dataField.containsKey('data') && dataField['data'] is List) {
          // paginated: data.data
          final list = dataField['data'] as List;
          return VendorsResponseModel(
            message: map['message'] as String?,
            data: VendorsData(
              vendors: list
                  .map((e) => Vendor.fromMap(e as Map<String, dynamic>))
                  .toList(),
            ),
          );
        } else if (dataField.containsKey('stores') && dataField['stores'] is List) {
          final list = dataField['stores'] as List;
          return VendorsResponseModel(
            message: map['message'] as String?,
            data: VendorsData(
              vendors: list
                  .map((e) => Vendor.fromMap(e as Map<String, dynamic>))
                  .toList(),
            ),
          );
        } else {
          // try to parse as VendorsData directly
          return VendorsResponseModel(
            message: map['message'] as String?,
            data: VendorsData.fromMap(dataField),
          );
        }
      }
    }

    // Fallback: check top-level vendors/stores
    if (map.containsKey('vendors') && map['vendors'] is List) {
      return VendorsResponseModel(
        message: map['message'] as String?,
        data: VendorsData(
          vendors: (map['vendors'] as List)
              .map((e) => Vendor.fromMap(e as Map<String, dynamic>))
              .toList(),
        ),
      );
    }
    if (map.containsKey('stores') && map['stores'] is List) {
      return VendorsResponseModel(
        message: map['message'] as String?,
        data: VendorsData(
          vendors: (map['stores'] as List)
              .map((e) => Vendor.fromMap(e as Map<String, dynamic>))
              .toList(),
        ),
      );
    }

    // Last resort: parse as VendorsData
    return VendorsResponseModel(
      message: map['message'] as String?,
      data: VendorsData.fromMap(map),
    );
  }

  Map<String, dynamic> toMap() => {
        'message': message,
        'data': data?.toMap(),
      };

  factory VendorsResponseModel.fromJson(String source) =>
      VendorsResponseModel.fromMap(json.decode(source) as Map<String, dynamic>);

  String toJson() => json.encode(toMap());
}

class VendorsData {
  List<Vendor>? vendors;

  VendorsData({this.vendors});

  factory VendorsData.fromMap(Map<String, dynamic> map) {
    List<dynamic>? rawList;
    // Handle paginated vendors: { vendors: { data: [...] } } or { vendors: { current_page, data: [...] } }
    if (map['vendors'] is Map<String, dynamic> && map['vendors']['data'] is List) {
      rawList = map['vendors']['data'] as List<dynamic>;
    } else if (map['data'] is Map<String, dynamic> && map['data']['data'] is List) {
      rawList = map['data']['data'] as List<dynamic>;
    } else {
      // Find the list in map
      for (final key in ['vendors', 'stores', 'data', 'items', 'result']) {
        if (map[key] is List) {
          rawList = map[key] as List<dynamic>;
          break;
        }
      }
    }
    // If no list found, check if map itself looks like vendor list wrapper fails,
    // treat as empty
    if (rawList == null) {
      // Check if map contains vendor-like keys (id, name) -> single vendor?
      if (map.containsKey('id') && map.containsKey('name')) {
        return VendorsData(
          vendors: [Vendor.fromMap(map)],
        );
      }
      return VendorsData(vendors: []);
    }
    return VendorsData(
      vendors: rawList
          .map((e) => Vendor.fromMap(e as Map<String, dynamic>))
          .toList(),
    );
  }

  Map<String, dynamic> toMap() => {
        'vendors': vendors?.map((e) => e.toMap()).toList(),
      };

  factory VendorsData.fromJson(String source) =>
      VendorsData.fromMap(json.decode(source) as Map<String, dynamic>);

  String toJson() => json.encode(toMap());
}
