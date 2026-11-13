import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/providers/address_session_provider.dart';
import 'package:laundry_customer/utils/cart_helper.dart';

/// Provider that exposes Map<vendorId, List<CartItem>>.
/// Rebuilds whenever the auth session changes, so an account switch picks
/// up the newly-scoped cart automatically (no cross-account leakage).
/// The UI also listens to the Hive box via ValueListenableBuilder for
/// immediate updates after mutations.
final vendorCartsProvider =
    StateProvider<Map<String, List<CarItemHiveModel>>>((ref) {
  ref.watch(addressSessionProvider);
  return CartHelper.getAllVendorCarts();
});

/// Total item count across all vendors.
final cartTotalCountProvider = Provider<int>((ref) {
  // Watch vendorCartsProvider to rebuild
  ref.watch(vendorCartsProvider);
  return CartHelper.getTotalItemCount();
});

/// Provider for a specific vendor's cart.
final vendorCartProvider =
    Provider.family<List<CarItemHiveModel>, String>((ref, vendorId) {
  ref.watch(vendorCartsProvider);
  return CartHelper.getCartForVendor(vendorId);
});

/// Currently selected vendor for checkout / scoping.
/// When user taps a store card, this should be set.
final activeVendorIdProvider = StateProvider<String?>((ref) => null);

/// Helper to refresh carts after mutation.
void refreshCarts(WidgetRef ref) {
  ref.read(vendorCartsProvider.notifier).state = CartHelper.getAllVendorCarts();
}

// Extension for WidgetRef to easily call
extension CartRefExtension on WidgetRef {
  void refreshVendorCarts() {
    read(vendorCartsProvider.notifier).state = CartHelper.getAllVendorCarts();
  }
}
