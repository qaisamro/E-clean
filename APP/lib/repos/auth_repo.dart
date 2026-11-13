// ignore_for_file: non_constant_identifier_names

import 'dart:io';

import 'package:dio/dio.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/forgot_password_otp_submit_model/data.dart';
import 'package:laundry_customer/models/auth_flow_model.dart';
import 'package:laundry_customer/models/forgot_password_otp_submit_model/forgot_password_otp_submit_model.dart';
import 'package:laundry_customer/models/login_model/login_model.dart';
import 'package:laundry_customer/models/register_model/access.dart';
import 'package:laundry_customer/offline_data/login_data.dart';
import 'package:laundry_customer/services/api_service.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
// import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/cupertino.dart';

abstract class IAuthRepo {
  Future<LoginModel> login({
    required String contact,
    required String password,
    required String device_key,
  }); //
  Future<PasswordRecoveryFlow> forgotPassword({required String contact}); //
  Future<void> logout({
    required String device_key,
  });
  Future<void> deleteAccount();
  /// Returns the full registration flow response. [RegisterFlowModel.access]
  /// is only set when the server reported `next_step: ready`.
  Future<RegisterFlowModel> register(Map<String, dynamic> udata); //
  Future<void> resendOTP(String contact);
  /// Verifies the registration OTP and returns what is still outstanding.
  Future<VerifyOtpFlowModel> signUpVerification({
    required String contact,
    required String otp,
  });
  /// Public join-request status check by tracking token.
  Future<JoinRequestStatus> joinRequestStatus({
    required String trackingToken,
  });
  /// Recovery step 1: request a recovery OTP for the given contact.
  Future<void> requestJoinRequestRecovery({required String contact});
  /// Recovery step 2: verify the recovery OTP and get a fresh tracking token.
  Future<JoinRequestRecovery> verifyJoinRequestRecovery({
    required String contact,
    required String otp,
  });
  Future<ForgotPasswordOtpSubmitModel> forgotPasswordVerification({
    //
    required String contact,
    required String otp,
  });
  Future<void> resetPassword({
    //
    required String password,
    required String password_confirmation,
    required String token,
  });
  /// Tells the server this device's FCM key so the app can receive push
  /// notifications even for accounts created before keys were stored.
  Future<void> syncDeviceKey();
  Future<void> addProfilePhoto({required File profile_photo});
}

class AuthRepo extends IAuthRepo {
  final Dio _dio = getDio();

  @override
  Future<LoginModel> login({
    required String contact,
    required String password,
    required String device_key,
  }) async {
    final token = await FirebaseMessaging.instance.getToken();
    String deviceType = 'android';
    if (Platform.isIOS) {
      deviceType = 'ios';
    }
    try {
      final Response response = await _dio.post(
        '/login',
        data: FormData.fromMap(
          {
            'contact': contact,
            'password': password,
            'device_key': token,
            'device_type': deviceType
          },
        ),
      );
      return LoginModel.fromMap(response.data as Map<String, dynamic>);
    } on DioError catch (e) {
      // A 403 here is a documented *gate* (verify_otp / await_approval /
      // rejected), not a transport failure. Parse it into the model so the UI
      // can route the user to the right screen with the server's message.
      final dynamic body = e.response?.data;
      if (e.response?.statusCode == 403 && body is Map) {
        return LoginModel.fromMap(Map<String, dynamic>.from(body));
      }
      rethrow;
    }
  }

  @override
  Future<PasswordRecoveryFlow> forgotPassword({required String contact}) async {
    final Map<String, dynamic> data = {};
    data["contact"] = contact;
    final Response response = await _dio.post(
      '/forgot-password',
      data: FormData.fromMap(data),
    );
    return PasswordRecoveryFlow.fromMap(Map<String, dynamic>.from(response.data as Map));
  }

  @override
  Future<ForgotPasswordOtpSubmitModel> forgotPasswordVerification({
    required String contact,
    required String otp,
  }) async {
    final Response response = await _dio.post(
      '/forgot-password/otp/verify',
      data: FormData.fromMap({'contact': contact, 'otp': otp}),
    );
    return ForgotPasswordOtpSubmitModel.fromMap(
      response.data as Map<String, dynamic>,
    );
  }

  @override
  Future<void> resetPassword({
    required String password,
    required String password_confirmation,
    required String token,
  }) async {
    await _dio.post(
      '/reset-password',
      data: FormData.fromMap({
        'password': password,
        'password_confirmation': password_confirmation,
        'token': token
      }),
    );
  }

  @override
  Future<void> logout({
    required String device_key,
  }) async {
    await _dio.post(
      '/logout',
      data: FormData.fromMap({'device_key': device_key}),
    );
  }

  @override
  Future<void> deleteAccount() async {
    await _dio.delete('/customer/account');
  }

  @override
  Future<void> addProfilePhoto({required File profile_photo}) async {
    final ext = profile_photo.path.split('.').last;
    debugPrint("${DateTime.now().millisecondsSinceEpoch.toString()}.$ext");
    await _dio.post(
      '/users/profile-photo/update',
      data: FormData.fromMap({
        'profile_photo': await MultipartFile.fromFile(
          profile_photo.path,
          filename: "${DateTime.now().millisecondsSinceEpoch.toString()}.$ext",
        ),
      }),
    );
  }

  @override
  Future<RegisterFlowModel> register(Map<String, dynamic> udata) async {
    try {
      // Always attach the device key at registration so push notifications
      // reach the account from the very first moment.
      final data = Map<String, dynamic>.from(udata);
      final token = await FirebaseMessaging.instance.getToken();
      String deviceType = 'android';
      if (Platform.isIOS) {
        deviceType = 'ios';
      }
      data['device_key'] = token;
      data['device_type'] = deviceType;
      final Response response = await _dio.post(
        '/register',
        data: FormData.fromMap(data),
      );
      return RegisterFlowModel.fromMap(response.data as Map<String, dynamic>);
    } on DioError catch (e) {
      // HTTP 503 with data.next_step = retry_later is a documented flow state
      // (join-request store temporarily unavailable), not a crash.
      final dynamic body = e.response?.data;
      if (body is Map &&
          (Map<String, dynamic>.from(body)['data'] as Map?)
                  ?.containsKey('next_step') ==
              true) {
        return RegisterFlowModel.fromMap(Map<String, dynamic>.from(body));
      }
      rethrow;
    }
  }

  @override
  Future<void> resendOTP(String contact) async {
    await _dio.post(
      '/resend/otp',
      data: FormData.fromMap({
        'contact': contact,
      }),
    );
  }

  @override
  Future<VerifyOtpFlowModel> signUpVerification({
    required String contact,
    required String otp,
  }) async {
    final Response response = await _dio.post(
      '/contact/verify',
      data: FormData.fromMap({
        'contact': contact,
        'otp': otp,
      }),
    );
    return VerifyOtpFlowModel.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<JoinRequestStatus> joinRequestStatus({
    required String trackingToken,
  }) async {
    final Response response = await _dio.post(
      '/customer/join-request-status',
      data: FormData.fromMap({'tracking_token': trackingToken}),
    );
    return JoinRequestStatus.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<void> requestJoinRequestRecovery({required String contact}) async {
    // The endpoint always answers with the same generic message; an unknown
    // contact is indistinguishable from a known one by design.
    await _dio.post(
      '/customer/join-request-recovery',
      data: FormData.fromMap({'contact': contact}),
    );
  }

  @override
  Future<JoinRequestRecovery> verifyJoinRequestRecovery({
    required String contact,
    required String otp,
  }) async {
    final Response response = await _dio.post(
      '/customer/join-request-recovery/verify',
      data: FormData.fromMap({'contact': contact, 'otp': otp}),
    );
    return JoinRequestRecovery.fromMap(response.data as Map<String, dynamic>);
  }

  @override
  Future<void> syncDeviceKey() async {
    try {
      final token = await FirebaseMessaging.instance.getToken();
      String deviceType = 'android';
      if (Platform.isIOS) {
        deviceType = 'ios';
      }
      await _dio.post(
        '/users/device-key',
        data: FormData.fromMap({'device_key': token, 'device_type': deviceType}),
      );
    } catch (_) {
      // Non-fatal; the next app launch will retry.
    }
  }
}

class OfflineAuthRepo extends IAuthRepo {
  @override
  Future<LoginModel> login({
    required String contact,
    required String password,
    required String device_key,
  }) async {
    await Future.delayed(apiDataDuration);

    return LoginModel.fromMap(LoginOfflineData.loginData);
  }

  @override
  Future<PasswordRecoveryFlow> forgotPassword({required String contact}) async {
    await Future.delayed(apiDataDuration);
    return const PasswordRecoveryFlow(requiresOtp: true, otp: '1234');
  }

  @override
  Future<ForgotPasswordOtpSubmitModel> forgotPasswordVerification({
    required String contact,
    required String otp,
  }) async {
    await Future.delayed(apiDataDuration);
    return ForgotPasswordOtpSubmitModel(
      message: 'Success',
      data: Data(token: 'gotToken'),
    );
  }

  @override
  Future<void> resetPassword({
    required String password,
    required String password_confirmation,
    required String token,
  }) async {
    await Future.delayed(apiDataDuration);
  }

  @override
  Future<void> logout({
    required String device_key,
  }) async {
    await Future.delayed(apiDataDuration);
  }

  @override
  Future<void> deleteAccount() async {
    await Future.delayed(apiDataDuration);
  }

  @override
  Future<void> addProfilePhoto({required File profile_photo}) async {
    await Future.delayed(apiDataDuration);
  }

  @override
  Future<RegisterFlowModel> register(Map<String, dynamic> udata) async {
    await Future.delayed(apiDataDuration);

    return RegisterFlowModel(
      message: 'Registration successfully complete',
      otpRequired: false,
      approvalRequired: false,
      approvalStatus: 'approved',
      nextStep: AuthNextStep.ready,
      access: Access(
        authType: 'Bearer',
        token: 'offline-token',
        expiresAt: DateTime.now()
            .add(const Duration(days: 30))
            .toIso8601String(),
      ),
    );
  }

  @override
  Future<void> resendOTP(String contact) async {
    await Future.delayed(apiDataDuration);
  }

  @override
  Future<void> syncDeviceKey() async {
    await Future.delayed(apiDataDuration);
  }

  @override
  Future<VerifyOtpFlowModel> signUpVerification({
    required String contact,
    required String otp,
  }) async {
    await Future.delayed(apiDataDuration);

    return VerifyOtpFlowModel(
      message: 'Mobile verification complete',
      otpVerified: true,
      nextStep: AuthNextStep.ready,
      approvalStatus: 'approved',
      access: Access(
        authType: 'Bearer',
        token: 'offline-token',
        expiresAt: DateTime.now()
            .add(const Duration(days: 30))
            .toIso8601String(),
      ),
    );
  }

  @override
  Future<JoinRequestStatus> joinRequestStatus({
    required String trackingToken,
  }) async {
    await Future.delayed(apiDataDuration);

    return JoinRequestStatus(
      message: 'حالة الطلب.',
      approvalStatus: 'pending',
      statusMessage: 'الحساب قيد المراجعة من قبل الإدارة.',
    );
  }

  @override
  Future<void> requestJoinRequestRecovery({required String contact}) async {
    await Future.delayed(apiDataDuration);
  }

  @override
  Future<JoinRequestRecovery> verifyJoinRequestRecovery({
    required String contact,
    required String otp,
  }) async {
    await Future.delayed(apiDataDuration);

    return JoinRequestRecovery(
      message: 'تم إنشاء رمز متابعة جديد.',
      trackingToken: 'offline-tracking-token-abcdef0123456789',
      approvalStatus: 'pending',
    );
  }
}
