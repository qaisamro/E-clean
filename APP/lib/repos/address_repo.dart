import 'package:dio/dio.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/addres_list_model/addres_list_model.dart';
import 'package:laundry_customer/offline_data/address_data.dart';
import 'package:laundry_customer/services/api_service.dart';

abstract class IAddressRepo {
  Future<AddresListModel> getAddresses();
  Future<Response?> addAddress(Map<String, dynamic> address);
  Future<void> updateAddress(Map<String, dynamic> address, String addressID);
  Future<void> deleteAddress(String addressID);
  Future<void> setDefault(String addressID);
}

class AddressRepo implements IAddressRepo {
  AddressRepo({Dio? client}) : _dio = client ?? getDio();
  final Dio _dio;
  @override
  Future<AddresListModel> getAddresses() async {
    final Response response = await _dio.get(
      '/addresses',
    );
    return AddresListModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<Response> addAddress(Map<String, dynamic> address) async {
    final response = await _dio.post('/addresses',
        data: FormData.fromMap(_payload(address)));
    final body = response.data;
    if (body is! Map ||
        body['data'] is! Map ||
        body['data']['addresses'] is! List) {
      throw const AddressSaveFailure('لم يتم حفظ العنوان. حاول مرة أخرى.');
    }
    return response;
  }

  @override
  Future<void> updateAddress(
    Map<String, dynamic> address,
    String addressID,
  ) async {
    await _dio.post(
      '/addresses/$addressID',
      data: FormData.fromMap(_payload(address)),
    );
  }

  Map<String, dynamic> _payload(Map<String, dynamic> address) => {
        ...address,
        if (address.containsKey('is_default'))
          'is_default': address['is_default'] == true ? '1' : '0',
      };

  @override
  Future<void> deleteAddress(String addressID) async {
    await _dio.delete('/addresses/$addressID');
  }

  @override
  Future<void> setDefault(String addressID) =>
      updateAddress({'is_default': true}, addressID);
}

class AddressSaveFailure implements Exception {
  const AddressSaveFailure(this.message);
  final String message;
  @override
  String toString() => message;
}

class OfflineAddressRepo implements IAddressRepo {
  @override
  Future<void> deleteAddress(String addressID) async =>
      throw const AddressSaveFailure('الحفظ يحتاج اتصالاً بالخادم');
  @override
  Future<void> setDefault(String addressID) async =>
      throw const AddressSaveFailure('الحفظ يحتاج اتصالاً بالخادم');
  @override
  Future<AddresListModel> getAddresses() async {
    await Future.delayed(apiDataDuration);

    return AddresListModel.fromMap(OfflineAddressData.addresslist);
  }

  @override
  Future<Response?> addAddress(Map<String, dynamic> address) async {
    await Future.delayed(apiDataDuration);

    return null;
  }

  @override
  Future<void> updateAddress(
    Map<String, dynamic> address,
    String addressID,
  ) async {
    await Future.delayed(apiDataDuration);

    return;
  }
}
