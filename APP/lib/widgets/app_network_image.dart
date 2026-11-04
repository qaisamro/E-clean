import 'package:cached_network_image/cached_network_image.dart';
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
    // Callers inside flexible layouts pass `double.infinity` (or NaN when an
    // unbounded box is measured). Those must never reach the decoder: the
    // memCache math below would round Infinity and throw, which surfaced as an
    // error box with no image instead of the picture.
    final double? boxW = _finite(width);
    final double? boxH = _finite(height);
    // Decode only as many pixels as the widget actually needs. Decoding the
    // full source resolution for a small thumb is the main memory/raster cost
    // when scrolling vendor and product grids.
    final double dpr = MediaQuery.devicePixelRatioOf(context);
    final int? decW = _decodeSize(boxW, dpr);
    final int? decH = _decodeSize(boxH, dpr);
    return CachedNetworkImage(
      imageUrl: resolved,
      width: boxW,
      height: boxH,
      fit: fit,
      memCacheWidth: decW,
      memCacheHeight: decH,
      maxWidthDiskCache: 1200,
      maxHeightDiskCache: 1200,
      imageBuilder: (context, imageProvider) => Image(
        image: imageProvider,
        width: boxW,
        height: boxH,
        fit: fit,
      ),
      placeholder: (context, url) => _loader(context),
      errorWidget: (context, url, error) => _placeholder(context),
    );
  }

  static double? _finite(double? value) {
    if (value == null) return null;
    if (value.isNaN || value.isInfinite || value <= 0) return null;
    return value;
  }

  static int? _decodeSize(double? value, double dpr) {
    final size = _finite(value);
    if (size == null) return null;
    final px = (size * dpr).round();
    if (px.isNaN || px.isInfinite || px <= 0) return null;
    return px;
  }

  Widget _loader(BuildContext context) {
    return Container(
      color: AppColors.lightgray,
      width: _finite(width),
      height: _finite(height),
      alignment: Alignment.center,
      child: const SizedBox(
        width: 20,
        height: 20,
        child: CircularProgressIndicator(strokeWidth: 2),
      ),
    );
  }

  Widget _placeholder(BuildContext context) {
    return Container(
      color: AppColors.lightgray,
      width: _finite(width),
      height: _finite(height),
      alignment: Alignment.center,
      child: Image.asset(placeholder, fit: BoxFit.contain),
    );
  }
}
