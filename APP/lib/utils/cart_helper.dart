import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';

class CartHelper {
  CartHelper._();

  static Box get _cartBox => Hive.box(AppHSC.cartBox);

  /// Legacy (pre-scoping) storage key, kept only to migrate existing carts.
  static const String _legacyVendorMapKey = '__vendorCartMap_v2__';

  /// Scoped storage prefix — every account (and the guest session) keeps its
  /// own isolated cart inside the same box. Switching accounts on a shared
  /// device can never leak items from one account into another.
  static const String _vendorMapPrefix = '__vendorCartMap__v3__';

  /// Stable scope for the currently signed-in account; 'guest' when not.
  static String get _currentScope {
    try {
      final dynamic id = Hive.box(AppHSC.userBox).get('id');
      final int? parsed = id == null ? null : int.tryParse(id.toString());
      if (parsed != null) return 'user_$parsed';
      final String raw = (id?.toString() ?? '').trim();
      if (raw.isNotEmpty) return 'user_$raw';
    } catch (_) {
      // userBox unavailable (very early boot) — treat as guest.
    }
    return 'guest';
  }

  /// Storage key for the multi-vendor map, scoped to the current account.
  /// We keep it as a string key inside the same box to allow ValueListenableBuilder listening.
  static String get _vendorMapKey => '$_vendorMapPrefix$_currentScope';

  // ---------------------------------------------------------------------------
  // Migration & raw access
  // ---------------------------------------------------------------------------

  static void _ensureMigrated() {
    // One-off: adopt the pre-scoping global map into the *current* account.
    // Legacy items cannot follow both accounts, so whichever account has the
    // app open when this runs inherits them.
    try {
      if (_cartBox.containsKey(_legacyVendorMapKey)) {
        if (!_cartBox.containsKey(_vendorMapKey)) {
          _cartBox.put(_vendorMapKey, _cartBox.get(_legacyVendorMapKey));
        }
        _cartBox.delete(_legacyVendorMapKey);
      }
    } catch (_) {}

    if (_cartBox.containsKey(_vendorMapKey)) return;

    // Check if there are legacy integer-indexed items (box entries where value is Map)
    if (_cartBox.isEmpty) {
      // Initialize empty map
      _cartBox.put(_vendorMapKey, <String, dynamic>{});
      return;
    }

    // Detect legacy: if any entry's value is a Map with productsId, it's legacy single list
    final Map<String, List<Map<String, dynamic>>> newMap = {};
    final List<dynamic> keysToDelete = [];

    for (final key in _cartBox.keys.toList()) {
      if (key == _vendorMapKey) continue;
      // Never absorb another account's scoped cart into this one.
      if (key.toString().startsWith(_vendorMapPrefix)) continue;
      final dynamic value = _cartBox.get(key);
      if (value is Map) {
        // Legacy single item stored at auto-increment int key
        try {
          final map = Map<String, dynamic>.from(
            (value as Map).map((k, v) => MapEntry(k.toString(), v)),
          );
          // Validate it looks like a cart item
          if (map.containsKey('productsId') || map.containsKey('productsName')) {
            final item = CarItemHiveModel.fromMap(map);
            final vendorId = item.vendorId ?? 'default';
            newMap.putIfAbsent(vendorId, () => []).add(item.toMap());
            keysToDelete.add(key);
          }
        } catch (_) {
          // ignore non-cart entries
        }
      } else if (value is List) {
        // Already vendor-list style but key is vendorId string
        // Keep as is - migrate to new map structure
        final String vendorId = key.toString();
        final List<Map<String, dynamic>> list = [];
        for (final e in value) {
          if (e is Map) {
            list.add(Map<String, dynamic>.from(
              (e as Map).map((k, v) => MapEntry(k.toString(), v)),
            ));
          }
        }
        // Ensure each item has vendorId set
        for (final m in list) {
          m['vendorId'] ??= vendorId;
        }
        newMap[vendorId] = list;
        keysToDelete.add(key);
      }
    }

    // Delete legacy keys after migration
    for (final k in keysToDelete) {
      _cartBox.delete(k);
    }

    // Save new map
    final encodable = <String, dynamic>{};
    newMap.forEach((k, v) => encodable[k] = v);
    _cartBox.put(_vendorMapKey, encodable);
  }

  static Map<String, dynamic> _rawVendorMap() {
    _ensureMigrated();
    if (_currentScope != 'guest') {
      adoptGuestCart();
    }
    final raw = _cartBox.get(_vendorMapKey);
    if (raw == null) return {};
    if (raw is Map) {
      return Map<String, dynamic>.from(
        raw.map((k, v) => MapEntry(k.toString(), v)),
      );
    }
    return {};
  }

  static void _saveRawVendorMap(Map<String, dynamic> map) {
    _cartBox.put(_vendorMapKey, map);
  }

  // ---------------------------------------------------------------------------
  // Guest -> account adoption
  // ---------------------------------------------------------------------------

  /// Merges items that were added while browsing as a guest into the cart of
  /// the account currently signed in, then drops the guest storage key.
  ///
  /// Items merge by vendor + product + subProduct; quantities are added.
  /// This runs automatically on the first cart read after login (and right
  /// after the profile is fetched), so products never just vanish from the
  /// cart — the guest -> user identity flip used to hide them.
  static void adoptGuestCart() {
    try {
      if (_currentScope == 'guest') return;
      final box = _cartBox;
      const String guestKey = '${_vendorMapPrefix}guest';
      if (!box.containsKey(guestKey)) return;
      final dynamic rawGuest = box.get(guestKey);
      if (rawGuest is! Map) {
        box.delete(guestKey);
        return;
      }
      final dynamic rawCurrent = box.get(_vendorMapKey);
      final Map<String, dynamic> current = rawCurrent is Map
          ? rawCurrent.map((k, v) => MapEntry(k.toString(), v))
          : <String, dynamic>{};

      for (final entry in rawGuest.entries) {
        final String vendorId = entry.key.toString();
        final dynamic gList = entry.value;
        if (gList is! List) continue;
        final List<dynamic> guestList = List.from(gList);
        final dynamic cList = current[vendorId];
        final List<dynamic> currentList = cList is List ? List.from(cList) : <dynamic>[];

        for (final gi in guestList) {
          if (gi is! Map) continue;
          final g = Map<String, dynamic>.from(
              gi.map((k, v) => MapEntry(k.toString(), v)));
          final dynamic gSub = g['subProduct'];
          final gSubId = gSub is Map ? gSub['id'] : g['subproductsId'];
          final prod = g['productsId'];

          bool merged = false;
          for (int i = 0; i < currentList.length; i++) {
            final dynamic c = currentList[i];
            if (c is! Map) continue;
            final u = Map<String, dynamic>.from(
                c.map((k, v) => MapEntry(k.toString(), v)));
            final dynamic uSub = u['subProduct'];
            final uSubId = uSub is Map ? uSub['id'] : u['subproductsId'];
            if (u['productsId'] == prod && uSubId == gSubId) {
              final int uQty =
                  int.tryParse(u['productsQTY']?.toString() ?? '') ?? 0;
              final int gQty =
                  int.tryParse(g['productsQTY']?.toString() ?? '') ?? 0;
              u['productsQTY'] = uQty + gQty;
              u['vendorId'] = vendorId;
              currentList[i] = u;
              merged = true;
              break;
            }
          }
          if (!merged) {
            g['vendorId'] = vendorId;
            currentList.add(g);
          }
        }
        current[vendorId] = currentList;
      }

      box.put(_vendorMapKey, current);
      box.delete(guestKey);
    } catch (_) {
      // Adoption is best-effort; it must never break cart reads.
    }
  }

  // ---------------------------------------------------------------------------
  // Public API - Map<vendorId, List<CartItem>>
  // ---------------------------------------------------------------------------

  /// Returns Map<vendorId, List<CartItem>> for all vendors.
  static Map<String, List<CarItemHiveModel>> getAllVendorCarts() {
    final raw = _rawVendorMap();
    final result = <String, List<CarItemHiveModel>>{};
    raw.forEach((vendorId, listDynamic) {
      final List<CarItemHiveModel> items = [];
      if (listDynamic is List) {
        for (final e in listDynamic) {
          if (e is Map) {
            final m = Map<String, dynamic>.from(
              (e as Map).map((k, v) => MapEntry(k.toString(), v)),
            );
            try {
              items.add(CarItemHiveModel.fromMap(m));
            } catch (_) {}
          }
        }
      }
      result[vendorId] = items;
    });
    return result;
  }

  /// Returns list for specific vendor (empty if none).
  static List<CarItemHiveModel> getCartForVendor(String vendorId) {
    return getAllVendorCarts()[vendorId] ?? [];
  }

  /// Returns flattened list of all items across vendors.
  static List<CarItemHiveModel> getAllItems() {
    final all = getAllVendorCarts();
    return all.values.expand((e) => e).toList();
  }

  static int getTotalItemCount() {
    return getAllItems().fold<int>(0, (sum, e) => sum + e.productsQTY);
  }

  static int getVendorItemCount(String vendorId) {
    return getCartForVendor(vendorId)
        .fold<int>(0, (sum, e) => sum + e.productsQTY);
  }

  static double calculateTotalForVendor(String vendorId) {
    final items = getCartForVendor(vendorId);
    double amount = 0;
    for (final element in items) {
      if (element.subProduct != null) {
        amount += element.productsQTY *
            (element.unitPrice + (element.subProduct!.price?.toDouble() ?? 0));
      } else {
        amount += element.productsQTY * element.unitPrice;
      }
    }
    return amount;
  }

  static double calculateTotalAll() {
    double total = 0;
    for (final vendorId in getAllVendorCarts().keys) {
      total += calculateTotalForVendor(vendorId);
    }
    return total;
  }

  /// Adds item to vendor cart. If same product+subProduct exists, increments qty.
  static void addItem(String vendorId, CarItemHiveModel newItem) {
    final raw = _rawVendorMap();
    final List<dynamic> currentList =
        (raw[vendorId] as List?) ?? <dynamic>[];

    // Normalize newItem vendorId
    final itemToAdd = newItem.copyWith(
      vendorId: vendorId,
      vendorName: newItem.vendorName,
    );

    bool found = false;
    for (int i = 0; i < currentList.length; i++) {
      final existingMap = Map<String, dynamic>.from(
        (currentList[i] as Map).map((k, v) => MapEntry(k.toString(), v)),
      );
      final existing = CarItemHiveModel.fromMap(existingMap);
      // Match by productId + subProduct id (if any)
      final bool sameProduct = existing.productsId == itemToAdd.productsId;
      final bool sameSub = (existing.subProduct?.id == itemToAdd.subProduct?.id) &&
          (existing.subproductsId == itemToAdd.subproductsId);
      // For subProduct-less items, sameSub will be true if both null
      final bool isSame = sameProduct && sameSub;
      if (isSame) {
        final updated = existing.copyWith(
          productsQTY: existing.productsQTY + itemToAdd.productsQTY,
        );
        // Ensure vendorId preserved
        final updatedMap = updated.toMap();
        updatedMap['vendorId'] = vendorId;
        currentList[i] = updatedMap;
        found = true;
        break;
      }
    }
    if (!found) {
      final map = itemToAdd.toMap();
      map['vendorId'] = vendorId;
      currentList.add(map);
    }

    raw[vendorId] = currentList;
    _saveRawVendorMap(raw);
  }

  /// Updates quantity for a specific item in vendor cart.
  /// If qty <=0, removes item.
  static void updateQuantity({
    required String vendorId,
    required int productId,
    int? subProductId,
    required int newQty,
  }) {
    final raw = _rawVendorMap();
    final List<dynamic>? list = raw[vendorId] as List?;
    if (list == null) return;

    for (int i = 0; i < list.length; i++) {
      final m = Map<String, dynamic>.from(
        (list[i] as Map).map((k, v) => MapEntry(k.toString(), v)),
      );
      final item = CarItemHiveModel.fromMap(m);
      final bool matchProduct = item.productsId == productId;
      final bool matchSub = (subProductId == null && item.subProduct == null && item.subproductsId == null) ||
          (item.subProduct?.id == subProductId) ||
          (item.subproductsId == subProductId);
      if (matchProduct && matchSub) {
        if (newQty <= 0) {
          list.removeAt(i);
        } else {
          final updated = item.copyWith(productsQTY: newQty);
          final updatedMap = updated.toMap();
          updatedMap['vendorId'] = vendorId;
          list[i] = updatedMap;
        }
        break;
      }
    }
    raw[vendorId] = list;
    // Remove vendor entry if empty
    if ((raw[vendorId] as List).isEmpty) {
      raw.remove(vendorId);
    }
    _saveRawVendorMap(raw);
  }

  /// Increment by 1
  static void increment({
    required String vendorId,
    required int productId,
    int? subProductId,
  }) {
    final items = getCartForVendor(vendorId);
    for (final it in items) {
      final bool matchProduct = it.productsId == productId;
      final bool matchSub = (subProductId == null && it.subProduct == null && it.subproductsId == null) ||
          (it.subProduct?.id == subProductId) ||
          (it.subproductsId == subProductId);
      if (matchProduct && matchSub) {
        updateQuantity(
          vendorId: vendorId,
          productId: productId,
          subProductId: subProductId,
          newQty: it.productsQTY + 1,
        );
        return;
      }
    }
  }

  /// Decrement by 1 (removes if 1)
  static void decrement({
    required String vendorId,
    required int productId,
    int? subProductId,
  }) {
    final items = getCartForVendor(vendorId);
    for (final it in items) {
      final bool matchProduct = it.productsId == productId;
      final bool matchSub = (subProductId == null && it.subProduct == null && it.subproductsId == null) ||
          (it.subProduct?.id == subProductId) ||
          (it.subproductsId == subProductId);
      if (matchProduct && matchSub) {
        updateQuantity(
          vendorId: vendorId,
          productId: productId,
          subProductId: subProductId,
          newQty: it.productsQTY - 1,
        );
        return;
      }
    }
  }

  static void removeItem({
    required String vendorId,
    required int productId,
    int? subProductId,
  }) {
    updateQuantity(
      vendorId: vendorId,
      productId: productId,
      subProductId: subProductId,
      newQty: 0,
    );
  }

  static void clearVendorCart(String vendorId) {
    final raw = _rawVendorMap();
    raw.remove(vendorId);
    _saveRawVendorMap(raw);
  }

  /// Clears the *current account's* cart only; other accounts on this
  /// device keep theirs. Called after placing an order, or after the
  /// account is deleted.
  static void clearAll() {
    _saveRawVendorMap(<String, dynamic>{});
  }

  /// For backward compat: returns legacy flat list via getAllItems()
  /// but also support old code that did Hive.box(cartBox).length via helper.
  static int legacyBoxLengthForCompatibility() => getAllItems().length;

  // ---------------------------------------------------------------------------
  // Helpers for UI: vendor ids with carts
  // ---------------------------------------------------------------------------
  static List<String> getVendorIdsWithItems() =>
      getAllVendorCarts().keys.toList();

  static bool isProductInCart({
    required String vendorId,
    required int productId,
    int? subProductId,
  }) {
    final items = getCartForVendor(vendorId);
    for (final it in items) {
      final bool matchProduct = it.productsId == productId;
      final bool matchSub = (subProductId == null && it.subProduct == null && it.subproductsId == null) ||
          (it.subProduct?.id == subProductId) ||
          (it.subproductsId == subProductId);
      if (matchProduct && matchSub) return true;
    }
    return false;
  }

  static CarItemHiveModel? findCartItem({
    required String vendorId,
    required int productId,
    int? subProductId,
  }) {
    final items = getCartForVendor(vendorId);
    for (final it in items) {
      final bool matchProduct = it.productsId == productId;
      final bool matchSub = (subProductId == null && it.subProduct == null && it.subproductsId == null) ||
          (it.subProduct?.id == subProductId) ||
          (it.subproductsId == subProductId);
      if (matchProduct && matchSub) return it;
    }
    return null;
  }
}
