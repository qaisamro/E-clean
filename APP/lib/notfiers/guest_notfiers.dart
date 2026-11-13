// ignore_for_file: public_member_api_docs, sort_constructors_first
import 'package:flutter/material.dart';
import 'package:laundry_customer/services/safe_api_notifier.dart';
import 'package:laundry_customer/models/all_service_model/all_service_model.dart';
import 'package:laundry_customer/models/products_model/products_model.dart';
import 'package:laundry_customer/models/offers_model/offers_model.dart';
import 'package:laundry_customer/models/promotions_model/promotions_model.dart';
import 'package:laundry_customer/models/wide_banners_model/wide_banners_model.dart';
import 'package:laundry_customer/models/variations_model/variations_model.dart';
import 'package:laundry_customer/repos/guest_repo.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/services/network_exceptions.dart';

class AllPromotionsNotifier extends SafeApiNotifier<PromotionsModel> {
  AllPromotionsNotifier(this.repo) : super(const ApiState.initial()) {
    getAllpromotions();
  }

  final IGuestRepo repo;

  Future<void> getAllpromotions() async {
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(
        data: await repo.getPromotions(),
      ));
    } catch (e) {
      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }
}

class WideBannersNotifier extends SafeApiNotifier<WideBannersModel> {
  WideBannersNotifier(this.repo) : super(const ApiState.initial()) {
    getWideBanners();
  }

  final IGuestRepo repo;

  Future<void> getWideBanners() async {
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(
        data: await repo.getWideBanners(),
      ));
    } catch (e) {
      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }
}

class AllServicerNotifier extends SafeApiNotifier<AllServiceModel> {
  AllServicerNotifier(this.repo) : super(const ApiState.initial()) {
    getAllServices();
  }

  final IGuestRepo repo;

  Future<void> getAllServices() async {
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(
        data: await repo.getServices(),
      ));
    } catch (e) {
      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }
}

class ServiceVariationsNotifier extends SafeApiNotifier<VariationsModel> {
  ServiceVariationsNotifier(this.repo, this.id)
      : super(const ApiState.initial()) {
    getAllVariations();
  }

  final IGuestRepo repo;
  final String id;

  Future<void> getAllVariations() async {
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(
        data: await repo.getVariations(id),
      ));
    } catch (e) {
      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }
}

class ProductsNotifier extends SafeApiNotifier<ProductsModel> {
  ProductsNotifier(this.repo, this.data) : super(const ApiState.initial()) {
    getAllVariations();
  }

  final IGuestRepo repo;
  final ProducServiceVariavtionDataModel data;

  Future<void> getAllVariations() async {
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(
        data: await repo.getProducts(data.servieID, data.variationID),
      ));
    } catch (e, stackTrace) {
      debugPrint("getprstacktrace $stackTrace");
      debugPrint("getpro $e");

      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }
}

class OffersNotifier extends SafeApiNotifier<OffersModel> {
  OffersNotifier(this.repo) : super(const ApiState.initial()) {
    getOffers();
  }
  final IGuestRepo repo;
  Future<void> getOffers() async {
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(data: await repo.getOffers()));
    } catch (e) {
      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }
}

class ProducServiceVariavtionDataModel {
  String servieID;
  String variationID;
  ProducServiceVariavtionDataModel({
    required this.servieID,
    required this.variationID,
  });

  ProducServiceVariavtionDataModel copyWith({
    String? servieID,
    String? variationID,
  }) {
    return ProducServiceVariavtionDataModel(
      servieID: servieID ?? this.servieID,
      variationID: variationID ?? this.variationID,
    );
  }
}
