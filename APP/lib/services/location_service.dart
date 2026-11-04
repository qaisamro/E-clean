import 'dart:async';

import 'package:geocoding/geocoding.dart';
import 'package:geolocator/geolocator.dart';

class PickedLocation {
  const PickedLocation({
    required this.latitude,
    required this.longitude,
    required this.addressLine,
    required this.area,
    required this.postCode,
  });

  final double latitude;
  final double longitude;
  final String addressLine;
  final String area;
  final String postCode;
}

class LocationFailure implements Exception {
  const LocationFailure(this.message);

  final String message;

  @override
  String toString() => message;
}

/// Resolves the device position and turns it into a readable address.
/// Throws a [LocationFailure] carrying a user facing message when the
/// location cannot be resolved.
Future<PickedLocation> resolveCurrentLocation() async {
  var serviceEnabled = await Geolocator.isLocationServiceEnabled();
  if (!serviceEnabled) {
    await Geolocator.openLocationSettings();
    serviceEnabled = await Geolocator.isLocationServiceEnabled();
  }
  if (!serviceEnabled) {
    throw const LocationFailure(
        'Please turn on location services to use your current location');
  }

  var permission = await Geolocator.checkPermission();
  if (permission == LocationPermission.denied) {
    permission = await Geolocator.requestPermission();
  }
  if (permission == LocationPermission.deniedForever) {
    await Geolocator.openAppSettings();
    throw const LocationFailure(
        'Location permission is blocked, please enable it from settings');
  }
  if (permission == LocationPermission.denied) {
    throw const LocationFailure(
        'Location permission is required to detect your current location');
  }

  Position position;
  try {
    position = await Geolocator.getCurrentPosition(
      locationSettings: const LocationSettings(
        accuracy: LocationAccuracy.high,
        timeLimit: Duration(seconds: 20),
      ),
    );
  } on TimeoutException {
    position = await Geolocator.getCurrentPosition(
      locationSettings:
          const LocationSettings(accuracy: LocationAccuracy.medium),
    );
  }

  return _describe(position);
}

Future<PickedLocation> _describe(Position position) async {
  final coordinates =
      '${position.latitude.toStringAsFixed(5)}, ${position.longitude.toStringAsFixed(5)}';
  final fallback = PickedLocation(
    latitude: position.latitude,
    longitude: position.longitude,
    addressLine: 'Current location ($coordinates)',
    area: '',
    postCode: '',
  );

  try {
    final placemarks = await Geocoding().placemarkFromCoordinates(
      position.latitude,
      position.longitude,
    );
    if (placemarks.isEmpty) return fallback;

    final place = placemarks.first;
    final parts = <String>{
      if ((place.street ?? '').trim().isNotEmpty) place.street!.trim(),
      if ((place.subLocality ?? '').trim().isNotEmpty)
        place.subLocality!.trim(),
      if ((place.locality ?? '').trim().isNotEmpty) place.locality!.trim(),
      if ((place.administrativeArea ?? '').trim().isNotEmpty)
        place.administrativeArea!.trim(),
    }.toList();

    return PickedLocation(
      latitude: position.latitude,
      longitude: position.longitude,
      addressLine: parts.isEmpty ? fallback.addressLine : parts.join(', '),
      area: _firstOf([
        place.subAdministrativeArea,
        place.administrativeArea,
        place.locality,
        place.subLocality,
      ]),
      postCode: (place.postalCode ?? '').trim(),
    );
  } catch (_) {
    return fallback;
  }
}

String _firstOf(List<String?> values) {
  for (final value in values) {
    final trimmed = (value ?? '').trim();
    if (trimmed.isNotEmpty) return trimmed;
  }
  return '';
}
