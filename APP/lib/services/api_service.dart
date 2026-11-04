import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:laundry_customer/constants/config.dart';
import 'package:laundry_customer/services/interceptor.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

/// Shared [Dio] instance. Reusing a single client enables connection pooling /
/// keep-alive between requests (a fresh `Dio` per call opens a new connection
/// every time) and avoids building interceptors over and over.
final Dio dio = _buildDio();

Dio _buildDio() {
  final Dio dio = Dio(
    BaseOptions(
      baseUrl: AppConfig.baseUrl,
      connectTimeout: const Duration(milliseconds: 30000),
      receiveTimeout: const Duration(milliseconds: 60000),
      headers: {'Accept': 'application/json'},
      followRedirects: false,
    ),
  );

  //Only log in debug builds. In release the logger serialises every
  //request/response body to the platform log, which is wasted work on-device.
  if (kDebugMode) {
    dio.interceptors.add(
      PrettyDioLogger(
        requestHeader: true,
        requestBody: true,
        responseHeader: true,
        maxWidth: 120,
      ),
    );
  }

  //Intercepts all requests and adds the token to the header and Allows Global Logout
  dio.interceptors.add(ElTanvirInterceptors());

  return dio;
}

Dio getDio() => dio;