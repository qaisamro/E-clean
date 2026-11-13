import 'dart:async';

import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geocoding/geocoding.dart';
import 'package:geolocator/geolocator.dart';
import 'package:laundry_customer/services/api_service.dart';

bool validCoordinates(double? latitude, double? longitude) =>
    latitude != null &&
    longitude != null &&
    latitude.isFinite &&
    longitude.isFinite &&
    latitude >= -90 &&
    latitude <= 90 &&
    longitude >= -180 &&
    longitude <= 180;

class PickedLocation {
  const PickedLocation({
    required this.latitude,
    required this.longitude,
    this.addressLine = '',
    this.area = '',
    this.postCode = '',
    this.accuracy,
  });
  final double latitude;
  final double longitude;
  final String addressLine;
  final String area;
  final String postCode;
  final double? accuracy;
  bool get valid => validCoordinates(latitude, longitude);
  bool get approximate => accuracy != null && accuracy! > 100;
}

class LocationFailure implements Exception {
  const LocationFailure(this.message,
      {this.settings = false, this.permission = false});
  final String message;
  final bool settings;
  final bool permission;
  @override
  String toString() => message;
}

abstract class LocationDevice {
  Future<bool> enabled();
  Future<LocationPermission> permission();
  Future<LocationPermission> request();
  Future<Position> position();
}

class GeolocatorDevice implements LocationDevice {
  @override
  Future<bool> enabled() => Geolocator.isLocationServiceEnabled();
  @override
  Future<LocationPermission> permission() => Geolocator.checkPermission();
  @override
  Future<LocationPermission> request() => Geolocator.requestPermission();
  @override
  Future<Position> position() => Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.high,
          timeLimit: Duration(seconds: 20),
        ),
      );
}

typedef DescribePoint = Future<List<Placemark>> Function(double, double);

final locationServiceProvider =
    Provider<LocationService>((ref) => LocationService());

class LocationService {
  LocationService({LocationDevice? device, DescribePoint? describe})
      : device = device ?? GeolocatorDevice(),
        describe = describe ??
            ((lat, lon) => Geocoding().placemarkFromCoordinates(lat, lon));
  final LocationDevice device;
  final DescribePoint describe;

  /// Automatic checkout suggestions never request permission or open settings.
  Future<PickedLocation> current({bool requestPermission = false}) async {
    final point =
        await currentCoordinates(requestPermission: requestPermission);
    final text = await describeLocation(point.latitude, point.longitude);
    return PickedLocation(
        latitude: point.latitude,
        longitude: point.longitude,
        accuracy: point.accuracy,
        addressLine: text.addressLine,
        area: text.area,
        postCode: text.postCode);
  }

  /// City selection needs coordinates only; no device reverse-geocoding request.
  Future<PickedLocation> currentCoordinates(
      {bool requestPermission = false}) async {
    if (!await device.enabled()) {
      throw const LocationFailure(
          'خدمة الموقع متوقفة. فعّل GPS أو اختر نقطة على الخريطة.',
          settings: true);
    }
    var permission = await device.permission();
    if (permission == LocationPermission.denied && requestPermission) {
      permission = await device.request();
    }
    if (permission == LocationPermission.deniedForever) {
      throw const LocationFailure(
          'إذن الموقع مغلق. يمكنك تفعيله من إعدادات التطبيق أو الاختيار يدويًا.',
          permission: true);
    }
    if (permission != LocationPermission.whileInUse &&
        permission != LocationPermission.always) {
      throw const LocationFailure(
          'اختر عنوانًا محفوظًا أو اسمح بالموقع عند الضغط على «موقعي الحالي».');
    }
    try {
      final point =
          await device.position().timeout(const Duration(seconds: 22));
      if (!validCoordinates(point.latitude, point.longitude)) {
        throw const LocationFailure(
            'الموقع المستلم غير صالح. اختر نقطة على الخريطة.');
      }
      return PickedLocation(
          latitude: point.latitude,
          longitude: point.longitude,
          accuracy: point.accuracy);
    } on LocationFailure {
      rethrow;
    } on TimeoutException {
      throw const LocationFailure(
          'تعذر تحديد موقع دقيق في الوقت المتاح. حاول مجددًا أو اختر نقطة يدويًا.');
    } catch (_) {
      throw const LocationFailure(
          'تعذر تحديد موقعك. اختر نقطة يدويًا أو حاول مجددًا.');
    }
  }

  Future<PickedLocation> describeLocation(double lat, double lon) async {
    if (!validCoordinates(lat, lon)) {
      throw const LocationFailure('اختر نقطة صالحة على الخريطة.');
    }
    // Reverse geocoding occurs only on explicit confirmation, never on every pan.
    // Server-first: the precise address does not depend on the on-device
    // geocoder, locale or offline caches, so it always fills the address field
    // when a network connection exists.
    try {
      final response = await getDio().get('/maps/reverse-geocode',
          queryParameters: {'lat': lat, 'lng': lon}).timeout(
        const Duration(seconds: 10),
      );
      final Map<String, dynamic>? body = response.data is Map
          ? Map<String, dynamic>.from(response.data as Map)
          : null;
      final data = body?['data'];
      final String? address =
          data is Map ? (data['address'] as String?)?.trim() : null;
      if (address != null && address.isNotEmpty) {
        return PickedLocation(
          latitude: lat,
          longitude: lon,
          addressLine: address,
          area: address,
        );
      }
    } catch (_) {
      // Fall back to the native on-device geocoder below.
    }
    try {
      final places =
          await describe(lat, lon).timeout(const Duration(seconds: 6));
      if (places.isNotEmpty) {
        final p = places.first;
        final parts = <String>{
          for (final value in [
            p.street,
            p.subLocality,
            p.locality,
            p.administrativeArea
          ])
            if ((value ?? '').trim().isNotEmpty) value!.trim(),
        };
        if (parts.isNotEmpty) {
          return PickedLocation(
            latitude: lat,
            longitude: lon,
            addressLine: parts.join('، '),
            area: (p.locality ??
                    p.subAdministrativeArea ??
                    p.administrativeArea ??
                    '')
                .trim(),
            postCode: p.postalCode ?? '',
          );
        }
      }
    } catch (_) {
      // Keep the chosen point; the customer supplies a readable address.
    }
    return PickedLocation(latitude: lat, longitude: lon);
  }
}

/// Retained for existing call sites; permissions are requested only on a user action.
Future<PickedLocation> resolveCurrentLocation() =>
    LocationService().current(requestPermission: true);
