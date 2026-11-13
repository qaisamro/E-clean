// ignore_for_file: type_annotate_public_apis, avoid_dynamic_calls, duplicate_ignore

import 'dart:io';

import 'package:dio/dio.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter/foundation.dart';
import 'package:freezed_annotation/freezed_annotation.dart';

part 'network_exceptions.freezed.dart';

@freezed
abstract class NetworkExceptions with _$NetworkExceptions {
  const factory NetworkExceptions.firebaseException(String reason) =
      FBaseException;
  const factory NetworkExceptions.requestCancelled() = RequestCancelled;

  const factory NetworkExceptions.unauthorisedRequest() = UnauthorisedRequest;

  const factory NetworkExceptions.badRequest() = BadRequest;

  const factory NetworkExceptions.notFound(String reason) = NotFound;

  const factory NetworkExceptions.methodNotAllowed() = MethodNotAllowed;

  const factory NetworkExceptions.notAcceptable() = NotAcceptable;

  const factory NetworkExceptions.requestTimeout() = RequestTimeout;

  const factory NetworkExceptions.sendTimeout() = SendTimeout;

  const factory NetworkExceptions.conflict() = Conflict;

  const factory NetworkExceptions.internalServerError() = InternalServerError;

  const factory NetworkExceptions.notImplemented() = NotImplemented;

  const factory NetworkExceptions.serviceUnavailable() = ServiceUnavailable;

  const factory NetworkExceptions.noInternetConnection() = NoInternetConnection;

  const factory NetworkExceptions.formatException() = FormatException;

  const factory NetworkExceptions.unableToProcess() = UnableToProcess;

  const factory NetworkExceptions.defaultError(String error) = DefaultError;

  const factory NetworkExceptions.unexpectedError() = UnexpectedError;

  /// Human-friendly Arabic message per error category.
  /// Raw server messages are NEVER shown — categories map to clear text.
  static String getErrorMessage(NetworkExceptions networkExceptions) {
    var errorMessage = "";
    networkExceptions.when(
      notImplemented: () {
        errorMessage =
            "هذه الميزة غير متاحة حالياً — أعد المحاولة لاحقاً";
      },
      requestCancelled: () {
        errorMessage = "تم إلغاء الطلب — أعد المحاولة";
      },
      internalServerError: () {
        errorMessage =
            "حدث خطأ في الخادم — أعد المحاولة بعد قليل";
      },
      notFound: (_) {
        errorMessage = "لم يُعثر على المطلوب — تحقق من الرابط أو حاول لاحقًا";
      },
      serviceUnavailable: () {
        errorMessage =
            "الخدمة غير متاحة حالياً — أعد المحاولة بعد قليل";
      },
      methodNotAllowed: () {
        errorMessage = "هذه العملية غير مسموحة";
      },
      badRequest: () {
        errorMessage = "بيانات غير صحيحة — تحقق منها";
      },
      unauthorisedRequest: () {
        errorMessage =
            "انتهت صلاحية الجلسة — سجّل الدخول من جديد";
      },
      unexpectedError: () {
        errorMessage =
            "حدث خطأ غير متوقع — أعد المحاولة";
      },
      requestTimeout: () {
        errorMessage =
            "انتهت مهلة الاتصال — تحقق من الإنترنت";
      },
      noInternetConnection: () {
        errorMessage =
            "لا يوجد اتصال بالإنترنت — تحقق من الشبكة";
      },
      conflict: () {
        errorMessage =
            "تعذر تنفيذ العملية — هناك تعارض — أعد المحاولة";
      },
      sendTimeout: () {
        errorMessage =
            "انتهت مهلة إرسال البيانات — تحقق من الإنترنت";
      },
      unableToProcess: () {
        errorMessage = "تعذر معالجة البيانات — تحقق منها";
      },
      defaultError: (error) {
        errorMessage = error;
      },
      formatException: () {
        errorMessage =
            "حدث خطأ غير متوقع — أعد المحاولة";
      },
      notAcceptable: () {
        errorMessage = "الطلب غير مقبول — أعد المحاولة";
      },
      firebaseException: (_) {
        errorMessage = "خطأ في الإشعارات — أعد المحاولة";
      },
    );

    return errorMessage;
  }

  static String errorText(error) {
    final network = getDioException(error);
    // kDebugMode prints expand with the raw body so a developer can see
    // exactly which endpoint fails and what the server returns.
    if (kDebugMode) {
      Object? rawBody;
      if (error is DioException) {
        rawBody = error.response?.data;
      }
      debugPrint(
          '[EC-ERROR] type=${network.runtimeType} status=${error is DioException ? error.response?.statusCode : null} raw=$rawBody');
    }
    // 401/403 are not user-visible error events: the interceptor wipes the
    // stale token and navigates to login, or swallows gate failures for
    // pending/rejected customers. Popping a modal here is what customers
    // reported as "تظهر بشكل مزعج غالباً" — silence it.
    if (network is UnauthorisedRequest) {
      return 'انتهت صلاحية الجلسة — سجّل الدخول من جديد';
    }
    return getErrorMessage(network);
  }

  // ignore: prefer_constructors_over_static_methods
  static NetworkExceptions getDioException(error) {
    if (error is Exception) {
      try {
        NetworkExceptions networkExceptions;
        if (error is DioException) {
          switch (error.type) {
            case DioExceptionType.cancel:
              networkExceptions = const NetworkExceptions.requestCancelled();
            case DioExceptionType.connectionTimeout:
              networkExceptions = const NetworkExceptions.requestTimeout();
            case DioExceptionType.unknown:
              networkExceptions =
                  const NetworkExceptions.noInternetConnection();
            case DioExceptionType.receiveTimeout:
              networkExceptions = const NetworkExceptions.sendTimeout();
            case DioExceptionType.badResponse:
              switch (error.response!.statusCode) {
                case 302:
                  networkExceptions =
                      const NetworkExceptions.unauthorisedRequest();
                case 400:
                  networkExceptions = const NetworkExceptions.badRequest();
                case 401:
                  networkExceptions =
                      const NetworkExceptions.unauthorisedRequest();
                case 403:
                  networkExceptions =
                      const NetworkExceptions.defaultError('غير مصرح لك بهذا الإجراء');
                case 404:
                  networkExceptions =
                      const NetworkExceptions.notFound('لم يُعثر على المطلوب');
                case 409:
                  networkExceptions = const NetworkExceptions.conflict();
                case 408:
                  networkExceptions = const NetworkExceptions.requestTimeout();
                case 422:
                  networkExceptions = const NetworkExceptions.badRequest();
                case 429:
                  networkExceptions = const NetworkExceptions.defaultError(
                      'محاولات اتصال كثيرة — انتظر لحظة');
                case 500:
                  networkExceptions =
                      const NetworkExceptions.internalServerError();
                case 502:
                case 503:
                case 504:
                  networkExceptions =
                      const NetworkExceptions.serviceUnavailable();
                default:
                  networkExceptions =
                      const NetworkExceptions.defaultError('تعذر تنفيذ العملية — أعد المحاولة');
              }
            case DioExceptionType.sendTimeout:
              networkExceptions = const NetworkExceptions.sendTimeout();
            default:
              networkExceptions = const NetworkExceptions.unexpectedError();
          }
        } else if (error is SocketException) {
          networkExceptions = const NetworkExceptions.noInternetConnection();
        } else {
          networkExceptions = const NetworkExceptions.unexpectedError();
        }
        return networkExceptions;
        // ignore: unused_catch_clause
      } on FormatException catch (e) {
        // Helper.printError(e.toString());
        return const NetworkExceptions.formatException();
      } catch (_) {
        return const NetworkExceptions.unexpectedError();
      }
    } else {
      if (error.toString().contains("is not a subtype of")) {
        return const NetworkExceptions.unableToProcess();
      } else {
        return const NetworkExceptions.unexpectedError();
      }
    }
  }
}
