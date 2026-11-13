import 'package:url_launcher/url_launcher.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';

/// Opens the given address on Google Maps — same behavior as the store
/// page's «الموقع» button. Prefers coordinates when available, falls back
/// to a free-text query otherwise. Shows a toast when nothing is known.
Future<void> openAddressOnMap({
  dynamic latitude,
  dynamic longitude,
  String? fallbackQuery,
}) async {
  final double? lat = _toDouble(latitude);
  final double? lng = _toDouble(longitude);

  final List<Uri> attempts = [];

  if (lat != null && lng != null) {
    attempts.add(Uri.parse(
        'https://www.google.com/maps/search/?api=1&query=$lat,$lng'));
  }
  final String query = (fallbackQuery ?? '').trim();
  if (query.isNotEmpty) {
    attempts.add(Uri.parse('https://www.google.com/maps/search/?api=1&query=${Uri.encodeComponent(query)}'));
  }

  for (final uri in attempts) {
    try {
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
        return;
      }
    } catch (_) {
      // try the next candidate
    }
  }

  if (attempts.isEmpty) {
    EasyLoading.showToast('لا يوجد موقع لهذا العنوان');
  } else {
    EasyLoading.showToast('تعذر فتح الخريطة');
  }
}

double? _toDouble(dynamic value) {
  if (value == null) return null;
  if (value is num) return value.toDouble();
  return double.tryParse(value.toString().trim());
}
