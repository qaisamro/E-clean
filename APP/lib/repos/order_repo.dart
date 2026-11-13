import 'package:dio/dio.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/add_order_model/add_order_model.dart';
import 'package:laundry_customer/models/all_orders_model/all_orders_model.dart';
import 'package:laundry_customer/models/coupon_response_model/coupon_response_model.dart';
import 'package:laundry_customer/models/order_details_model/order_details_model.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart';
import 'package:laundry_customer/models/schedules_model/schedules_model.dart';
import 'package:laundry_customer/offline_data/order_data.dart';
import 'package:laundry_customer/services/api_service.dart';
import 'package:flutter/foundation.dart';
import 'package:intl/intl.dart';

abstract class IOrderRepo {
  Future<AllOrdersModel> getAllOrders(String status);
  Future<AddOrderModel> addOrder(OrderPlaceModel orderPlaceModel);
  Future<AddOrderModel> updateOrder(
    List<OrderProductModel> orderProductModel,
    String orderID,
  );
  Future<CouponResponseModel> applyCoupon({
    required String coupon,
    required String amount,
  });
  Future<OrderDetailsModel> getOrderDetails(String id);
  Future<SchedulesModel> getPickUpSchedules(DateTime date);
  Future<SchedulesModel> getDeliverySchedules(DateTime date);
  Future<AddOrderModel> cancelOrder(String orderID);
  Future<AddOrderModel> editOrderDelivery({
    required String orderID,
    String? instruction,
    String? deliveryDate,
    String? deliveryHour,
    int? addressId,
  });
}

class OrderRepo implements IOrderRepo {
  final Dio _dio = getDio();

  @override
  Future<AllOrdersModel> getAllOrders(String status) async {
    Map<String, dynamic> qp = {};

    if (status != '') {
      qp = {'status': status};
    }
    final Response response = await _dio.get('/orders', queryParameters: qp);
    return AllOrdersModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<AddOrderModel> addOrder(OrderPlaceModel orderPlaceModel) async {
    final response = await _dio.post(
      '/orders',
      data: FormData.fromMap(orderPlaceModel.toMap()),
    );
    return AddOrderModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<AddOrderModel> updateOrder(
    List<OrderProductModel> orderProductModel,
    String orderID,
  ) async {
    final List<Map<String, dynamic>> products =
        orderProductModel.map((orderProduct) => orderProduct.toMap()).toList();
    final response = await _dio.put(
      '/orders/$orderID',
      data: {'products': products},
    );
    return AddOrderModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<CouponResponseModel> applyCoupon({
    required String coupon,
    required String amount,
  }) async {
    final response = await _dio.post(
      '/coupons/$coupon/apply',
      data: FormData.fromMap({'amount': amount}),
    );
    return CouponResponseModel.fromMap(response.data as Map<String, dynamic>);
  }

  //TODO get order details

  @override
  Future<OrderDetailsModel> getOrderDetails(String id) async {
    try {
      final Response response = await _dio.get(
        '/orders/$id/details',
      );
      return OrderDetailsModel.fromMap(response.data as Map<String, dynamic>);
    } catch (e) {
      print(e);
      rethrow;
    }
  }

  @override
  Future<SchedulesModel> getPickUpSchedules(DateTime date) async {
    // Force Latin digits: with an Arabic locale iOS formats yyyy-MM-dd
    // using Eastern Arabic numerals (٢٠٢٦-١٠-٠٩) which the API rejects.
    final data = DateFormat('yyyy-MM-dd', 'en').format(date);

    final Response response = await _dio.get(
      '/pick-schedules/$data',
    );
    return SchedulesModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<SchedulesModel> getDeliverySchedules(DateTime date) async {
    // Force Latin digits (see above — iOS Arabic locale issue).
    final data = DateFormat('yyyy-MM-dd', 'en').format(date);
    debugPrint(data);
    final Response response = await _dio.get(
      '/delivery-schedules/$data',
    );
    return SchedulesModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<AddOrderModel> cancelOrder(String orderID) async {
    final Response response = await _dio.post(
      '/orders/$orderID/cancel',
      data: FormData.fromMap({}),
    );
    return AddOrderModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<AddOrderModel> editOrderDelivery({
    required String orderID,
    String? instruction,
    String? deliveryDate,
    String? deliveryHour,
    int? addressId,
  }) async {
    final Map<String, dynamic> body = {};
    if (instruction != null) body['instruction'] = instruction;
    if (deliveryDate != null) body['delivery_date'] = deliveryDate;
    if (deliveryHour != null) body['delivery_hour'] = deliveryHour;
    if (addressId != null) body['address_id'] = addressId;
    final Response response = await _dio.post(
      '/orders/$orderID/customer-edit',
      data: FormData.fromMap(body),
    );
    return AddOrderModel.fromMap(response.data as Map<String, dynamic>);
  }
}

class OfflineOrderRepo implements IOrderRepo {
  @override
  Future<AllOrdersModel> getAllOrders(String status) async {
    await Future.delayed(apiDataDuration);
    return AllOrdersModel.fromMap(OfflineOrderData.orderList);
  }

  @override
  Future<AddOrderModel> addOrder(OrderPlaceModel orderPlaceModel) async {
    await Future.delayed(apiDataDuration);
    return AddOrderModel.fromMap(OfflineOrderData.addOrderData);
  }

  @override
  Future<AddOrderModel> updateOrder(
    List<OrderProductModel> orderProductModel,
    String orderID,
  ) async {
    await Future.delayed(apiDataDuration);
    return AddOrderModel.fromMap(OfflineOrderData.addOrderData);
  }

  @override
  Future<CouponResponseModel> applyCoupon({
    required String coupon,
    required String amount,
  }) async {
    await Future.delayed(apiDataDuration);
    return CouponResponseModel.fromMap(OfflineOrderData.coupondata);
  }

  @override
  Future<OrderDetailsModel> getOrderDetails(String id) async {
    await Future.delayed(apiDataDuration);
    return OrderDetailsModel.fromMap(OfflineOrderData.detailsData);
  }

  @override
  Future<SchedulesModel> getPickUpSchedules(DateTime date) async {
    await Future.delayed(apiDataDuration);
    return SchedulesModel.fromMap(OfflineOrderData.scheduleData);
  }

  @override
  Future<SchedulesModel> getDeliverySchedules(DateTime date) async {
    await Future.delayed(apiDataDuration);
    return SchedulesModel.fromMap(OfflineOrderData.scheduleData);
  }

  @override
  Future<AddOrderModel> cancelOrder(String orderID) async {
    await Future.delayed(apiDataDuration);
    return AddOrderModel.fromMap(OfflineOrderData.addOrderData);
  }

  @override
  Future<AddOrderModel> editOrderDelivery({
    required String orderID,
    String? instruction,
    String? deliveryDate,
    String? deliveryHour,
    int? addressId,
  }) async {
    await Future.delayed(apiDataDuration);
    return AddOrderModel.fromMap(OfflineOrderData.addOrderData);
  }
}
