import 'package:dio/dio.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:flutter/foundation.dart';
import 'package:hive_flutter/hive_flutter.dart';

/// Adds the bearer token to every request and interprets the auth gates.
///
/// The Laravel backend protects the customer API group with the
/// `ensure.customer.approved` middleware, which answers with a **flat** body:
///
/// ```json
/// {"message": "...", "otp_required": true, "next_step": "verify_otp"}
/// {"message": "...", "approval_status": "pending", "next_step": "await_approval"}
/// {"message": "...", "approval_status": "rejected", "next_step": "rejected"}
/// {"message": "...", "next_step": "retry_later"}   // 503
/// ```
///
/// A 401 only means "this token is no longer accepted"; the session is not
/// cleared blindly, so a transient failure cannot silently log the user out.
class ElTanvirInterceptors extends Interceptor {
  Box? authBox;
  Box? userBox;

  Box get _authBox => authBox ??= Hive.box(AppHSC.authBox);

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    //These boxes open when the app opens (see main.dart), so reading them here
    //is safe even for requests fired before the first frame.
    final Box box = _authBox;
    final dynamic token = box.get(AppHSC.authToken);
    if (token != null && token.toString().isNotEmpty) {
      final dynamic tokenType = box.get(AppHSC.authTokenType);
      options.headers['Authorization'] = "${tokenType ?? 'Bearer'} $token";
    }
    options.headers['Accept'] = "application/json";
    options.headers['accept'] = "application/json";

    return super.onRequest(options, handler);
  }

  @override
  void onResponse(Response response, ResponseInterceptorHandler handler) {
    return super.onResponse(response, handler);
  }

  /// Reads the flat `next_step` gate from a 403/503 response body.
  String? _flatNextStep(dynamic data) {
    if (data is Map) {
      final dynamic step = data['next_step'];
      if (step != null && step.toString().isNotEmpty) {
        return step.toString();
      }
    }
    return null;
  }

  void _handleGate(String nextStep, String? message) {
    switch (nextStep) {
      case 'verify_otp':
        ContextLess.nav.pushNamedAndRemoveUntil(
          Routes.signUpOtpScreen,
          (route) => false,
        );
        break;
      case 'await_approval':
      case 'rejected':
        ContextLess.nav.pushNamedAndRemoveUntil(
          Routes.joinRequestWaitingScreen,
          (route) => false,
        );
        break;
      case 'retry_later':
        // Store unavailable: keep the user where they are; the next retry may
        // succeed. Do NOT drop the session.
        debugPrint('Join-request store unavailable (retry_later): $message');
        break;
    }
  }

  @override
  Future<void> onError(DioError err, ErrorInterceptorHandler handler) async {
    final int? status = err.response?.statusCode;
    final dynamic body = err.response?.data;

    if (status == 403 || status == 503) {
      final String? step = _flatNextStep(body);
      if (step != null) {
        _handleGate(step, body is Map ? body['message']?.toString() : null);
        return super.onError(err, handler);
      }
    }

    if (status == 401) {
      // Sanctum rejected this token — it is permanently invalid (revoked /
      // rotated / wiped by the approval gate). Holding onto it just repeats
      // the same toast on every background poll, which is exactly the
      // "مزعج" loop customers reported. Clear it and send the user to login;
      // the next login succeeds normally and the message is never shown again.
      final dynamic token = _authBox.get(AppHSC.authToken);
      if (token != null && token.toString().isNotEmpty) {
        await _authBox.delete(AppHSC.authToken);
        await _authBox.delete(AppHSC.authTokenType);
        ContextLess.nav.pushNamedAndRemoveUntil(
          Routes.loginScreen,
          (route) => false,
        );
      }
      return super.onError(err, handler);
    }

    return super.onError(err, handler);
  }
}
