import 'dart:convert';

import 'package:flutter/cupertino.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/add_order_model/add_order_model.dart';
import 'package:laundry_customer/models/all_orders_model/all_orders_model.dart';
import 'package:laundry_customer/models/coupon_response_model/coupon_response_model.dart';
import 'package:laundry_customer/models/order_details_model/order_details_model.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart';
import 'package:laundry_customer/models/schedules_model/schedules_model.dart';
import 'package:laundry_customer/repos/order_repo.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/services/network_exceptions.dart';

class AllOrdersNotifier extends StateNotifier<ApiState<AllOrdersModel>> {
  AllOrdersNotifier(this._repo, this._filter)
      : super(const ApiState.initial()) {
    getAllOrders();
  }
  final IOrderRepo _repo;
  final String _filter;
  Future<void> getAllOrders() async {
    state = const ApiState.loading();
    try {
      final data = await _repo.getAllOrders(_filter);
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e, stackTrace) {
      if (!mounted) return;
      debugPrint("error is $e");
      debugPrint("stacktrace is $stackTrace");
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }

  /// Refreshes the list silently: the current data stays on screen while the
  /// new page is fetched, so a status change swaps in without a loading flash
  /// or a full app reload. When nothing changed the state is kept as-is so the
  /// list is not rebuilt every poll.
  Future<void> silentRefresh() async {
    try {
      final data = await _repo.getAllOrders(_filter);
      if (!mounted) return;
      final current = state.mapOrNull(loaded: (s) => s.data);
      if (current != null &&
          jsonEncode(current.toMap()) == jsonEncode(data.toMap())) {
        return;
      }
      state = ApiState.loaded(data: data);
    } catch (e) {
      debugPrint("silent refresh error is $e");
    }
  }
}

class OrderDetailsNotifier extends StateNotifier<ApiState<OrderDetailsModel>> {
  OrderDetailsNotifier(this._repo, this._id) : super(const ApiState.initial()) {
    getOrderDetails();
  }
  final IOrderRepo _repo;
  final String _id;
  Future<void> getOrderDetails() async {
    state = const ApiState.loading();
    try {
      final data = await _repo.getOrderDetails(_id);
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }

  /// Keeps the details page in sync with server-side status changes without
  /// rebuilding into a loading state. When the payload is unchanged the state
  /// is kept, avoiding needless rebuilds every poll.
  Future<void> silentRefresh() async {
    try {
      final data = await _repo.getOrderDetails(_id);
      if (!mounted) return;
      final current = state.mapOrNull(loaded: (s) => s.data);
      if (current != null &&
          jsonEncode(current.toMap()) == jsonEncode(data.toMap())) {
        return;
      }
      state = ApiState.loaded(data: data);
    } catch (e) {
      debugPrint("silent refresh error is $e");
    }
  }
}

class PlaceOrderNotifier extends StateNotifier<ApiState<AddOrderModel>> {
  PlaceOrderNotifier(
    this._repo,
  ) : super(const ApiState.initial());
  final IOrderRepo _repo;

  Future<void> addOrder(OrderPlaceModel orderPlaceModel) async {
    state = const ApiState.loading();
    try {
      final data = await _repo.addOrder(orderPlaceModel);
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class UpdateOrderNotifier extends StateNotifier<ApiState<AddOrderModel>> {
  UpdateOrderNotifier(this._repo) : super(const ApiState.initial());
  final IOrderRepo _repo;

  Future<void> updateOrder(
    List<OrderProductModel> orderPlaceModel,
    String orderID,
  ) async {
    state = const ApiState.loading();
    try {
      final data = await _repo.updateOrder(orderPlaceModel, orderID);
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

/// Cancel own order while it is pending confirmation.
class CancelOrderNotifier extends StateNotifier<ApiState<AddOrderModel>> {
  CancelOrderNotifier(this._repo) : super(const ApiState.initial());
  final IOrderRepo _repo;

  Future<void> cancelOrder(String orderID) async {
    state = const ApiState.loading();
    try {
      final data = await _repo.cancelOrder(orderID);
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

/// Edit delivery details of own order while it is pending confirmation.
class EditOrderDeliveryNotifier extends StateNotifier<ApiState<AddOrderModel>> {
  EditOrderDeliveryNotifier(this._repo) : super(const ApiState.initial());
  final IOrderRepo _repo;

  Future<void> editOrderDelivery({
    required String orderID,
    String? instruction,
    String? deliveryDate,
    String? deliveryHour,
    int? addressId,
  }) async {
    state = const ApiState.loading();
    try {
      final data = await _repo.editOrderDelivery(
        orderID: orderID,
        instruction: instruction,
        deliveryDate: deliveryDate,
        deliveryHour: deliveryHour,
        addressId: addressId,
      );
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class PickUpScheduleNotifier extends StateNotifier<ApiState<SchedulesModel>> {
  PickUpScheduleNotifier(this._repo, this._date)
      : super(const ApiState.initial()) {
    getPickUpSchedules();
  }
  final IOrderRepo _repo;
  final DateTime _date;
  Future<void> getPickUpSchedules() async {
    state = const ApiState.loading();
    try {
      final data = await _repo.getPickUpSchedules(_date);
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class DeliveryScheduleNotifier extends StateNotifier<ApiState<SchedulesModel>> {
  DeliveryScheduleNotifier(this._repo, this._date)
      : super(const ApiState.initial()) {
    getDeliverySchedules();
  }
  final IOrderRepo _repo;
  final DateTime _date;
  Future<void> getDeliverySchedules() async {
    state = const ApiState.loading();
    try {
      final data = await _repo.getDeliverySchedules(_date);
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class CouponNotifier extends StateNotifier<ApiState<CouponResponseModel>> {
  CouponNotifier(this._repo) : super(const ApiState.initial());
  final IOrderRepo _repo;

  Future<void> applyCoupon({
    required String coupon,
    required String amount,
  }) async {
    state = const ApiState.loading();
    try {
      final data = await _repo.applyCoupon(coupon: coupon, amount: amount);
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}
