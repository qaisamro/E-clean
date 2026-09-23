import 'dart:convert';

class PaymentGateway {
  String? image;
  String? identifier;

  PaymentGateway({this.image, this.identifier});

  factory PaymentGateway.fromMap(Map<String, dynamic> data) {
    return PaymentGateway(
      image: data['image'] as String?,
      identifier: data['identifier'] as String?,
    );
  }

  Map<String, dynamic> toMap() => {
        'image': image,
        'identifier': identifier,
      };

  /// `dart:convert`
  ///
  /// Parses the string and returns the resulting Json object as [PaymentGateway].
  factory PaymentGateway.fromJson(String data) {
    return PaymentGateway.fromMap(json.decode(data) as Map<String, dynamic>);
  }

  /// `dart:convert`
  ///
  /// Converts [PaymentGateway] to a JSON string.
  String toJson() => json.encode(toMap());

  PaymentGateway copyWith({
    String? image,
    String? identifier,
  }) {
    return PaymentGateway(
      image: image ?? this.image,
      identifier: identifier ?? this.identifier,
    );
  }
}
