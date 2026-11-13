import 'package:laundry_customer/services/safe_api_notifier.dart';
import 'package:laundry_customer/models/all_service_model/all_service_model.dart';
import 'package:laundry_customer/models/products_model/products_model.dart';
import 'package:laundry_customer/models/vendor_model/vendors_response_model.dart';
import 'package:laundry_customer/repos/vendor_repo.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/services/network_exceptions.dart';

class VendorsNotifier extends SafeApiNotifier<VendorsResponseModel> {
  VendorsNotifier(this.repo, this.search) : super(const ApiState.initial()) {
    getVendors();
  }

  final IVendorRepo repo;
  String search;

  Future<void> getVendors() async {
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(
        data: await repo.getVendors(search: search),
      ));
    } catch (e) {
      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }

  Future<void> searchVendors(String newSearch) async {
    search = newSearch;
    await getVendors();
  }

  Future<void> refresh() async => getVendors();
}

class VendorServicesNotifier extends SafeApiNotifier<AllServiceModel> {
  VendorServicesNotifier(this.repo, this.vendorId)
      : super(const ApiState.initial()) {
    getVendorServices();
  }

  final IVendorRepo repo;
  final String vendorId;

  Future<void> getVendorServices() async {
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(
        data: await repo.getVendorServices(vendorId),
      ));
    } catch (e) {
      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }
}

class VendorProductsNotifier extends SafeApiNotifier<ProductsModel> {
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
    emit(const ApiState.loading());
    try {
      emit(ApiState.loaded(
        data: await repo.getVendorProducts(
          vendorId: vendorId,
          serviceId: serviceId,
          variantId: variantId,
        ),
      ));
    } catch (e) {
      // Avoid throwing stacktrace as in guest notifier bug; handle gracefully
      emit(ApiState.error(error: NetworkExceptions.errorText(e)));
    }
  }
}
