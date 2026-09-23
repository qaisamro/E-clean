import 'package:flutter/material.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/config.dart';

/// Rewrites image URLs returned by the API so they always point to the same
/// host/port the app is configured to talk to (see [AppConfig.baseUrl]).
///
/// The API builds absolute URLs from the server's APP_URL (e.g.
/// http://127.0.0.1:8000). On an Android emulator or a physical device
/// `127.0.0.1` refers to the device itself, so those URLs are rewritten to the
/// configured backend host (10.0.2.2 / LAN IP / whatever baseUrl uses).
String resolveApiImageUrl(String? url) {
  if (url == null || url.isEmpty) return '';
  final base = Uri.parse(AppConfig.baseUrl);
  final origin =
      '${base.scheme}://${base.host}${base.hasPort ? ':${base.port}' : ''}';

  if (url.startsWith('http://') || url.startsWith('https://')) {
    final uri = Uri.tryParse(url);
    if (uri != null &&
        (uri.host == '127.0.0.1' || uri.host == 'localhost')) {
      return Uri(
        scheme: base.scheme,
        host: base.host,
        port: base.hasPort ? base.port : (uri.hasPort ? uri.port : null),
        path: uri.path,
        query: uri.hasQuery ? uri.query : null,
      ).toString();
    }
    return url;
  }
  if (url.startsWith('/')) return '$origin$url';
  return url;
}

/// Network image with a local asset fallback (no more red error boxes) and a
/// light loading placeholder.
class AppNetworkImage extends StatelessWidget {
  const AppNetworkImage(
    this.url, {
    super.key,
    this.fit,
    this.width,
    this.height,
    this.placeholder = 'assets/images/app_icon.png',
  });

  final String? url;
  final BoxFit? fit;
  final double? width;
  final double? height;
  final String placeholder;

  @override
  Widget build(BuildContext context) {
    final resolved = resolveApiImageUrl(url);
    if (resolved.isEmpty) return _placeholder(context);
    return Image.network(
      resolved,
      width: width,
      height: height,
      fit: fit,
      loadingBuilder: (context, child, loadingProgress) {
        if (loadingProgress == null) return child;
        return Container(
          color: AppColors.lightgray,
          width: width,
          height: height,
          alignment: Alignment.center,
          child: const SizedBox(
            width: 20,
            height: 20,
            child: CircularProgressIndicator(strokeWidth: 2),
          ),
        );
      },
      errorBuilder: (context, error, stackTrace) => _placeholder(context),
    );
  }

  Widget _placeholder(BuildContext context) {
    return Container(
      color: AppColors.lightgray,
      width: width,
      height: height,
      alignment: Alignment.center,
      child: Image.asset(placeholder, fit: BoxFit.contain),
    );
  }
}
