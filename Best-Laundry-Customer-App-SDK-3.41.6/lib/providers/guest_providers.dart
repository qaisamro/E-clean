import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/all_service_model/all_service_model.dart';
import 'package:laundry_customer/models/offers_model/offers_model.dart';
import 'package:laundry_customer/models/products_model/products_model.dart';
import 'package:laundry_customer/models/promotions_model/promotions_model.dart';
import 'package:laundry_customer/models/variations_model/variations_model.dart';
import 'package:laundry_customer/notfiers/guest_notfiers.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/repos/guest_repo.dart';
import 'package:laundry_customer/services/api_state.dart';

final guestRepoProvider = Provider<IGuestRepo>((ref) {
  return ref.watch(isAppLive) ? GuestRepo() : OfflineGuestRepo();
});

final allPromotionsProvider = StateNotifierProvider<AllPromotionsNotifier, ApiState<PromotionsModel>>((ref) {
  return AllPromotionsNotifier(ref.watch(guestRepoProvider));
});

final allServicesProvider = StateNotifierProvider<AllServicerNotifier, ApiState<AllServiceModel>>((ref) {
  return AllServicerNotifier(ref.watch(guestRepoProvider));
});

final servicesVariationsProvider = StateNotifierProvider.family<ServiceVariationsNotifier, ApiState<VariationsModel>, String>((ref, id) {
  return ServiceVariationsNotifier(ref.watch(guestRepoProvider), id);
});

final productsFilterProvider = StateProvider<ProducServiceVariavtionDataModel>((ref) {
  return ProducServiceVariavtionDataModel(servieID: '', variationID: '');
});

final productsProvider = StateNotifierProvider<ProductsNotifier, ApiState<ProductsModel>>((ref) {
  return ProductsNotifier(ref.watch(guestRepoProvider), ref.watch(productsFilterProvider));
});

final offersProvider = StateNotifierProvider<OffersNotifier, ApiState<OffersModel>>((ref) {
  return OffersNotifier(ref.watch(guestRepoProvider));
});
