import 'dart:convert';

class OffersModel {
  String? message;
  OffersData? data;
  OffersModel({this.message, this.data});
  factory OffersModel.fromMap(Map<String, dynamic> data) => OffersModel(
        message: data['message'] as String?,
        data: data['data'] == null
            ? null
            : OffersData.fromMap(data['data'] as Map<String, dynamic>),
      );
  Map<String, dynamic> toMap() => {'message': message, 'data': data?.toMap()};
  factory OffersModel.fromJson(String data) =>
      OffersModel.fromMap(json.decode(data) as Map<String, dynamic>);
  String toJson() => json.encode(toMap());
}

class OffersData {
  List<OfferItem>? offers;
  OffersData({this.offers});
  factory OffersData.fromMap(Map<String, dynamic> data) => OffersData(
        offers: ((data['offers'] ?? data['promotions']) as List<dynamic>?)
            ?.map((e) => OfferItem.fromMap(e as Map<String, dynamic>))
            .toList(),
      );
  Map<String, dynamic> toMap() =>
      {'offers': offers?.map((e) => e.toMap()).toList()};
}

class OfferItem {
  int? id;
  int? vendorId;
  String? vendorName;
  String? title;
  String? description;
  String? discountType;
  double? discountValue;
  String? discountLabel;
  String? imagePath;
  bool? isActive;
  OfferItem(
      {this.id,
      this.vendorId,
      this.vendorName,
      this.title,
      this.description,
      this.discountType,
      this.discountValue,
      this.discountLabel,
      this.imagePath,
      this.isActive});
  factory OfferItem.fromMap(Map<String, dynamic> data) => OfferItem(
        id: data['id'] is int
            ? data['id'] as int
            : int.tryParse('${data['id']}'),
        vendorId: data['vendor_id'] is int
            ? data['vendor_id'] as int
            : int.tryParse('${data['vendor_id']}'),
        vendorName: data['vendor_name'] as String?,
        title: data['title'] as String?,
        description: data['description'] as String?,
        discountType: data['discount_type'] as String?,
        discountValue: (data['discount_value'] as num?)?.toDouble(),
        discountLabel: data['discount_label'] as String?,
        imagePath: data['image_path'] as String?,
        isActive: data['is_active'] as bool?,
      );
  Map<String, dynamic> toMap() => {
        'id': id,
        'vendor_id': vendorId,
        'vendor_name': vendorName,
        'title': title,
        'description': description,
        'discount_type': discountType,
        'discount_value': discountValue,
        'discount_label': discountLabel,
        'image_path': imagePath,
        'is_active': isActive,
      };
}
