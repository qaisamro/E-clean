import 'package:dio/dio.dart';
import 'package:laundry_customer/constants/config.dart';
import 'package:laundry_customer/services/interceptor.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

Dio getDio() {
  final Dio dio = Dio();

  //Basic Configuration
  // Dio joins a relative path directly to baseUrl. Keep the trailing slash so
  // `/api` + `services` becomes `/api/services`, not `/apiservices`.
  dio.options.baseUrl = AppConfig.baseUrl.endsWith('/')
      ? AppConfig.baseUrl
      : '${AppConfig.baseUrl}/';
  dio.options.connectTimeout = const Duration(milliseconds: 30000);
  dio.options.receiveTimeout = const Duration(milliseconds: 60000);
  // _dio.options.headers = {'Content-Type': 'application/json'};
  dio.options.headers = {'Accept': 'application/json'};
  dio.options.headers = {'accept': 'application/json'};
  dio.options.followRedirects = false;

  //for Logging the Request And response
  dio.interceptors.add(
    PrettyDioLogger(
      requestHeader: true,
      requestBody: true,
      responseHeader: true,
    ),
  );

  //Intercepts all requests and adds the token to the header and Allows Global Logout
  dio.interceptors.add(ElTanvirInterceptors());

  return dio;
}
