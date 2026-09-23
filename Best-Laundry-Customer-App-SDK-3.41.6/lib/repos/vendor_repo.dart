import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/all_service_model/all_service_model.dart';
import 'package:laundry_customer/models/products_model/products_model.dart';
import 'package:laundry_customer/models/vendor_model/vendors_response_model.dart';
import 'package:laundry_customer/offline_data/guest_data.dart';
import 'package:laundry_customer/services/api_service.dart';

abstract class IVendorRepo {
  Future<VendorsResponseModel> getVendors({String? search});
  Future<AllServiceModel> getVendorServices(String vendorId);
  Future<ProductsModel> getVendorProducts({
    required String vendorId,
    required String serviceId,
    required String variantId,
  });
}

class VendorRepo implements IVendorRepo {
  final Dio _dio = getDio();

  @override
  Future<VendorsResponseModel> getVendors({String? search}) async {
    final Map<String, dynamic> qp = {};
    if (search != null && search.isNotEmpty) {
      qp['search'] = search;
    }
    final Response response =
        await _dio.get('vendors', queryParameters: qp);
    return VendorsResponseModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<AllServiceModel> getVendorServices(String vendorId) async {
    // Primary endpoint: GET /api/vendors/{id}/services
    // Fallback to filtered services via query param if 404
    try {
        final Response response = await _dio.get('vendors/$vendorId/services');
      // Support both AllServiceModel shape and raw vendors response
      return AllServiceModel.fromMap(response.data as Map<String, dynamic>);
    } on DioException catch (e) {
      if (e.response?.statusCode == 404) {
        // Fallback: try query param approach
        final Response fallback = await _dio.get(
          '/services',
          queryParameters: {'vendor_id': vendorId},
        );
        return AllServiceModel.fromMap(fallback.data as Map<String, dynamic>);
      }
      rethrow;
    }
  }

  @override
  Future<ProductsModel> getVendorProducts({
    required String vendorId,
    required String serviceId,
    required String variantId,
  }) async {
    // Try vendor-scoped products endpoint first
    try {
      final Response response = await _dio.get(
        'vendors/$vendorId/products',
        queryParameters: {
          'service_id': serviceId,
          'variant_id': variantId,
          'search': '',
        },
      );
      return ProductsModel.fromMap(response.data as Map<String, dynamic>);
    } on DioException catch (e) {
      if (e.response?.statusCode == 404) {
        // Fallback to global products with vendor_id filter
        final Response fallback = await _dio.get(
          '/products',
          queryParameters: {
            'service_id': serviceId,
            'variant_id': variantId,
            'vendor_id': vendorId,
            'search': '',
          },
        );
        return ProductsModel.fromMap(fallback.data as Map<String, dynamic>);
      }
      rethrow;
    }
  }
}

class OfflineVendorRepo implements IVendorRepo {
  @override
  Future<VendorsResponseModel> getVendors({String? search}) async {
    await Future.delayed(apiDataDuration);
    // Return mock vendors filtered by search (partial address match)
    final mockVendors = [
      {
        "id": 1,
        "name": "Elite Cleaning - Downtown",
        "address": "شارع الملك فيصل، الرياض",
        "image_path":
            "https://goldstardrycleaners.com:8100/storage/images/services/sAHG0x0rxJBVg7ebYp4HlYEi0PBtulec0PCqjxkw.png",
        "phone": "+966500000001",
      },
      {
        "id": 2,
        "name": "Elite Cleaning - Al Olaya",
        "address": "حي العليا، الرياض",
        "image_path":
            "https://goldstardrycleaners.com:8100/storage/images/services/sAHG0x0rxJBVg7ebYp4HlYEi0PBtulec0PCqjxkw.png",
        "phone": "+966500000002",
      },
      {
        "id": 3,
        "name": "Elite Cleaning - Jeddah",
        "address": "شارع التحلية، جدة",
        "image_path":
            "https://goldstardrycleaners.com:8100/storage/images/services/sAHG0x0rxJBVg7ebYp4HlYEi0PBtulec0PCqjxkw.png",
        "phone": "+966500000003",
      },
      {
        "id": 4,
        "name": "Quick Wash - Dammam",
        "address": "حي الشاطئ، الدمام",
        "image_path":
            "https://goldstardrycleaners.com:8100/storage/images/banners/VKiXHWtro5ZIHS1xkPfEJKBsNVQ8abPPZVXJPGrE.png",
        "phone": "+966500000004",
      },
    ];

    List<Map<String, dynamic>> filtered = mockVendors;
    if (search != null && search.isNotEmpty) {
      final q = search.toLowerCase();
      filtered = mockVendors.where((v) {
        final addr = (v['address'] as String).toLowerCase();
        final name = (v['name'] as String).toLowerCase();
        return addr.contains(q) || name.contains(q);
      }).toList();
    }

    return VendorsResponseModel.fromMap({
      "message": "vendors list",
      "data": {"vendors": filtered}
    });
  }

  @override
  Future<AllServiceModel> getVendorServices(String vendorId) async {
    await Future.delayed(apiDataDuration);
    return AllServiceModel.fromMap(OfflineGuestData.servicesData);
  }

  @override
  Future<ProductsModel> getVendorProducts({
    required String vendorId,
    required String serviceId,
    required String variantId,
  }) async {
    await Future.delayed(apiDataDuration);
    // For offline, return generic products - same as guest data
    // Need to import products_data offline
    try {
      final offline = await _offlineProductsFallback();
      return offline;
    } catch (e) {
      debugPrint('offline products fallback error $e');
      rethrow;
    }
  }

  Future<ProductsModel> _offlineProductsFallback() async {
    // Reuse OfflineGuestData via GuestRepo offline
    // Create minimal products model if not available
    // Import dynamically to avoid circular
    // For now, return empty wrapper with offline guest services mocked as products
    return ProductsModel.fromMap({
      "message": "products list",
      "data": {
        "products": [
          {
            "id": 101,
            "name": "Wash & Fold",
            "name_bn": null,
            "slug": "wash-fold",
            "current_price": 25.0,
            "old_price": null,
            "description": "Standard wash and fold service",
            "image_path":
                "https://goldstardrycleaners.com:8100/storage/images/services/sAHG0x0rxJBVg7ebYp4HlYEi0PBtulec0PCqjxkw.png",
            "discount_percentage": null,
            "sub_products": [],
            "service": {"id": 1, "name": "Laundry"},
            "variant": {"id": 1, "name": "Laundry"}
          },
          {
            "id": 102,
            "name": "Dry Cleaning Suit",
            "name_bn": null,
            "slug": "dry-cleaning-suit",
            "current_price": 45.0,
            "old_price": 50.0,
            "description": "Premium dry cleaning for suits",
            "image_path":
                "https://goldstardrycleaners.com:8100/storage/images/services/sAHG0x0rxJBVg7ebYp4HlYEi0PBtulec0PCqjxkw.png",
            "discount_percentage": 10,
            "sub_products": [
              {"id": 201, "name": "With Iron", "price": 5.0, "description": "Include ironing"},
              {"id": 202, "name": "Without Iron", "price": 0.0, "description": "No ironing"}
            ],
            "service": {"id": 1, "name": "Dry Cleaning"},
            "variant": {"id": 2, "name": "Laundry"}
          }
        ]
      }
    });
  }
}
