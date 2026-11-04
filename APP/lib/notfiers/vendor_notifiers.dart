import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/all_service_model/all_service_model.dart';
import 'package:laundry_customer/models/products_model/products_model.dart';
import 'package:laundry_customer/models/vendor_model/vendors_response_model.dart';
import 'package:laundry_customer/repos/vendor_repo.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/services/network_exceptions.dart';

class VendorsNotifier extends StateNotifier<ApiState<VendorsResponseModel>> {
  VendorsNotifier(this.repo, this.search)
      : super(const ApiState.initial()) {
    getVendors();
  }

  final IVendorRepo repo;
  String search;

  Future<void> getVendors() async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.getVendors(search: search),
      );
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }

  Future<void> searchVendors(String newSearch) async {
    search = newSearch;
    await getVendors();
  }

  Future<void> refresh() async => getVendors();
}

class VendorServicesNotifier
    extends StateNotifier<ApiState<AllServiceModel>> {
  VendorServicesNotifier(this.repo, this.vendorId)
      : super(const ApiState.initial()) {
    getVendorServices();
  }

  final IVendorRepo repo;
  final String vendorId;

  Future<void> getVendorServices() async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.getVendorServices(vendorId),
      );
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class VendorProductsNotifier
    extends StateNotifier<ApiState<ProductsModel>> {
  VendorProductsNotifier(
    this.repo,
    this.vendorId,
    this.serviceId,
    this.variantId,
  ) : super(const ApiState.initial()) {
    getProducts();
  }

  final IVendorRepo repo;
  final String vendorId;
  final String serviceId;
  final String variantId;

  Future<void> getProducts() async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.getVendorProducts(
          vendorId: vendorId,
          serviceId: serviceId,
          variantId: variantId,
        ),
      );
    } catch (e, stack) {
      // Avoid throwing stacktrace as in guest notifier bug; handle gracefully
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}
