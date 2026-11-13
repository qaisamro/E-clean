import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/all_service_model/all_service_model.dart';
import 'package:laundry_customer/models/products_model/products_model.dart';
import 'package:laundry_customer/models/offers_model/offers_model.dart';
import 'package:laundry_customer/models/promotions_model/promotions_model.dart';
import 'package:laundry_customer/models/wide_banners_model/wide_banners_model.dart';
import 'package:laundry_customer/models/variations_model/variations_model.dart';
import 'package:laundry_customer/offline_data/guest_data.dart';
import 'package:laundry_customer/offline_data/products_data.dart';
import 'package:laundry_customer/services/api_service.dart';

abstract class IGuestRepo {
  Future<PromotionsModel> getPromotions();
  Future<WideBannersModel> getWideBanners();
  Future<AllServiceModel> getServices();
  Future<OffersModel> getOffers();
  Future<VariationsModel> getVariations(String id);
  Future<ProductsModel> getProducts(String servieID, String variationID);
}

class GuestRepo implements IGuestRepo {
  GuestRepo({Dio? client, this.cityId}) : _dio = client ?? getDio();
  final Dio _dio;
  final String? cityId;
  Map<String, dynamic> get scope => {if (cityId != null) 'city_id': cityId};
  @override
  Future<PromotionsModel> getPromotions() async {
    final Response reponse =
        await _dio.get('/promotions', queryParameters: scope);
    return PromotionsModel.fromMap(reponse.data as Map<String, dynamic>);
  }

  @override
  Future<WideBannersModel> getWideBanners() async {
    final Response reponse =
        await _dio.get('/banners/wide', queryParameters: scope);
    return WideBannersModel.fromMap(reponse.data as Map<String, dynamic>);
  }

  @override
  Future<AllServiceModel> getServices() async {
    final Response reponse =
        await _dio.get('/services', queryParameters: scope);
    return AllServiceModel.fromMap(reponse.data as Map<String, dynamic>);
  }

  @override
  Future<OffersModel> getOffers() async {
    final Response reponse = await _dio.get('/offers', queryParameters: scope);
    return OffersModel.fromMap(reponse.data as Map<String, dynamic>);
  }

  @override
  Future<VariationsModel> getVariations(String id) async {
    final Response reponse = await _dio.get('/variants?service_id=$id');
    debugPrint("varient data ${reponse.data}");
    return VariationsModel.fromMap(reponse.data as Map<String, dynamic>);
  }

  @override
  Future<ProductsModel> getProducts(String servieID, String variationID) async {
    final Response reponse = await _dio.get('/products', queryParameters: {
      ...scope,
      'service_id': servieID,
      'variant_id': variationID,
      'search': ''
    });
    return ProductsModel.fromMap(reponse.data as Map<String, dynamic>);
  }
}

class OfflineGuestRepo implements IGuestRepo {
  @override
  Future<PromotionsModel> getPromotions() async {
    await Future.delayed(apiDataDuration);
    return PromotionsModel.fromMap(OfflineGuestData.promotionsData);
  }

  @override
  Future<WideBannersModel> getWideBanners() async {
    await Future.delayed(apiDataDuration);
    return WideBannersModel.fromMap({
      "message": "offline",
      "data": {"banners": []}
    });
  }

  @override
  Future<AllServiceModel> getServices() async {
    await Future.delayed(apiDataDuration);
    return AllServiceModel.fromMap(OfflineGuestData.servicesData);
  }

  @override
  Future<OffersModel> getOffers() async {
    await Future.delayed(apiDataDuration);
    return OffersModel();
  }

  @override
  Future<VariationsModel> getVariations(String id) async {
    await Future.delayed(apiDataDuration);
    return VariationsModel.fromMap(OfflineGuestData.variationData);
  }

  @override
  Future<ProductsModel> getProducts(String servieID, String variationID) async {
    await Future.delayed(apiDataDuration);
    return ProductsModel.fromMap(ProductsDat.allProducts);
  }
}
