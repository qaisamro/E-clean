

import 'dart:convert';

import 'package:laundry_customer/models/master_model/payment_gateway.dart';

class Data {
  final String? currency;
  final String? currencyPosition;
  final int? deliveryCost;
  final int? feeCost;
  final int? minimumCost;
  final List<PaymentGateway>? paymentGateway;
  final List<String>? postCode;
  final String? androidUrl;
  final String? iosUrl;

  const Data({
    this.currency,
    this.currencyPosition,
    this.deliveryCost,
    this.feeCost,
    this.minimumCost,
    this.paymentGateway,
    this.postCode,
    this.androidUrl,
    this.iosUrl,
  });

  factory Data.fromMap(Map<String, dynamic> map) {
    return Data(
      currency: map['currency'] as String?,
      currencyPosition: map['currency_position'] as String?,
      deliveryCost: map['delivery_cost'] as int?,
      feeCost: map['fee_cost'] as int?,
      minimumCost: map['minimum_cost'] as int?,
      paymentGateway: (map['payment_gateway'] as List<dynamic>?)
          ?.map((e) => PaymentGateway.fromMap(e as Map<String, dynamic>))
          .toList(),
      postCode: (map['post_code'] as List<dynamic>?)
          ?.map((e) => e.toString())
          .toList(),
      androidUrl: map['android_url'] as String?,
      iosUrl: map['ios_url'] as String?,
    );
  }

  Map<String, dynamic> toMap() {
    return {
      'currency': currency,
      'currency_position': currencyPosition,
      'delivery_cost': deliveryCost,
      'fee_cost': feeCost,
      'minimum_cost': minimumCost,
      'payment_gateway': paymentGateway?.map((e) => e.toMap()).toList(),
      'post_code': postCode,
      'android_url': androidUrl,
      'ios_url': iosUrl,
    };
  }

  String toJson() => json.encode(toMap());

  Data copyWith({
    String? currency,
    String? currencyPosition,
    int? deliveryCost,
    int? feeCost,
    int? minimumCost,
    List<PaymentGateway>? paymentGateway,
    List<String>? postCode,
    String? androidUrl,
    String? iosUrl,
  }) {
    return Data(
      currency: currency ?? this.currency,
      currencyPosition: currencyPosition ?? this.currencyPosition,
      deliveryCost: deliveryCost ?? this.deliveryCost,
      feeCost: feeCost ?? this.feeCost,
      minimumCost: minimumCost ?? this.minimumCost,
      paymentGateway: paymentGateway ?? this.paymentGateway,
      postCode: postCode ?? this.postCode,
      androidUrl: androidUrl ?? this.androidUrl,
      iosUrl: iosUrl ?? this.iosUrl,
    );
  }

  @override
  String toString() {
    return 'Data(currency: $currency, currencyPosition: $currencyPosition, deliveryCost: $deliveryCost, feeCost: $feeCost, minimumCost: $minimumCost, paymentGateway: $paymentGateway, postCode: $postCode, androidUrl: $androidUrl, iosUrl: $iosUrl)';
  }
}
