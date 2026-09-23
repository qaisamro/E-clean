import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/all_service_model/all_service_model.dart';
import 'package:laundry_customer/models/products_model/products_model.dart';
import 'package:laundry_customer/models/vendor_model/vendors_response_model.dart';
import 'package:laundry_customer/notfiers/vendor_notifiers.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/repos/vendor_repo.dart';
import 'package:laundry_customer/services/api_state.dart';

final vendorRepoProvider = Provider<IVendorRepo>((ref) {
  return ref.watch(isAppLive) ? VendorRepo() : OfflineVendorRepo();
});

final vendorSearchQueryProvider = StateProvider<String>((ref) => '');

final vendorsProvider = StateNotifierProvider<VendorsNotifier,
    ApiState<VendorsResponseModel>>((ref) {
  final search = ref.watch(vendorSearchQueryProvider);
  return VendorsNotifier(
    ref.watch(vendorRepoProvider),
    search,
  );
});

// Vendor-specific services
final vendorServicesProvider = StateNotifierProvider.family<
    VendorServicesNotifier, ApiState<AllServiceModel>, String>(
  (ref, vendorId) {
    return VendorServicesNotifier(
      ref.watch(vendorRepoProvider),
      vendorId,
    );
  },
);

// For vendor-specific product filtering
class VendorProductFilter {
  final String vendorId;
  final String serviceId;
  final String variantId;
  VendorProductFilter({
    required this.vendorId,
    required this.serviceId,
    required this.variantId,
  });

  VendorProductFilter copyWith({
    String? vendorId,
    String? serviceId,
    String? variantId,
  }) {
    return VendorProductFilter(
      vendorId: vendorId ?? this.vendorId,
      serviceId: serviceId ?? this.serviceId,
      variantId: variantId ?? this.variantId,
    );
  }
}

final vendorProductFilterProvider =
    StateProvider.family<VendorProductFilter, String>((ref, vendorId) {
  return VendorProductFilter(vendorId: vendorId, serviceId: '', variantId: '');
});

final vendorProductsProvider = StateNotifierProvider.family<
    VendorProductsNotifier, ApiState<ProductsModel>, VendorProductFilter>(
  (ref, filter) {
    return VendorProductsNotifier(
      ref.watch(vendorRepoProvider),
      filter.vendorId,
      filter.serviceId,
      filter.variantId,
    );
  },
);

// Currently selected vendor for cart scoping
final selectedVendorIdProvider = StateProvider<String?>((ref) => null);
final selectedVendorProvider = StateProvider<dynamic>((ref) => null);
