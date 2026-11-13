import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/models/addres_list_model/addres_list_model.dart';
import 'package:laundry_customer/repos/address_repo.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/services/network_exceptions.dart';

class AddressListNotifier extends StateNotifier<ApiState<AddresListModel>> {
  AddressListNotifier(this._repo) : super(const ApiState.initial()) {
    getAddress();
  }
  final IAddressRepo _repo;
  Future<void> getAddress() async {
    // Guests hold no token — the call would 401 and the interceptor would
    // wipe a session that never existed. Skip the noise entirely.
    final String token =
        Hive.box(AppHSC.authBox).get(AppHSC.authToken)?.toString() ?? '';
    if (token.isEmpty) {
      state = const ApiState.initial();
      return;
    }
    state = const ApiState.loading();
    try {
      final data = await _repo.getAddresses();
      if (!mounted) return;
      state = ApiState.loaded(data: data);
    } catch (e) {
      if (!mounted) return;
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class AddAddressNotifier extends StateNotifier<ApiState<String>> {
  AddAddressNotifier(this._repo) : super(const ApiState.initial());
  final IAddressRepo _repo;
  Future<void> addAddress({required Map<String, dynamic> address}) async {
    state = const ApiState.loading();
    try {
      final response = await _repo.addAddress(address);
      if (!mounted) return;
      final Map<String, dynamic>? responseData =
          response?.data as Map<String, dynamic>?;

      final message = responseData?['message'] as String;
      debugPrint("message: ${responseData?['message']}");
      state = ApiState.loaded(data: message);
    } catch (e) {
      if (!mounted) return;
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class UpdateAddressNotifier extends StateNotifier<ApiState<String>> {
  UpdateAddressNotifier(this._repo) : super(const ApiState.initial());
  final IAddressRepo _repo;
  Future<void> updateAddress({
    required Map<String, dynamic> address,
    required String addressID,
  }) async {
    state = const ApiState.loading();
    try {
      await _repo.updateAddress(address, addressID);
      if (!mounted) return;
      state = const ApiState.loaded(data: 'Successs');
    } catch (e) {
      if (!mounted) return;
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}
