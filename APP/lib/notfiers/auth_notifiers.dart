import 'dart:async';
import 'dart:io';

import 'package:laundry_customer/models/auth_flow_model.dart';
import 'package:laundry_customer/models/forgot_password_otp_submit_model/forgot_password_otp_submit_model.dart';
import 'package:laundry_customer/models/login_model/login_model.dart';
import 'package:laundry_customer/repos/auth_repo.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/services/network_exceptions.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

class LoginNotifier extends StateNotifier<ApiState<LoginModel>> {
  LoginNotifier(this.repo, this.device_key) : super(const ApiState.initial());

  final IAuthRepo repo;
  // ignore: non_constant_identifier_names
  final String device_key;

  Future<void> login(String contact, String password) async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.login(
          contact: contact,
          password: password,
          device_key: device_key,
        ),
      );
    } catch (e) {
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class ForgotPassNotifier extends StateNotifier<ApiState<PasswordRecoveryFlow>> {
  ForgotPassNotifier(this.repo) : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> forgotPassword(String contact) async {
    state = const ApiState.loading();
    try {
      final result = await repo.forgotPassword(contact: contact);
      state = ApiState.loaded(
        data: result,
      );
    } catch (e) {
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class ForgotPassOtpVerficationNotifier
    extends StateNotifier<ApiState<ForgotPasswordOtpSubmitModel>> {
  ForgotPassOtpVerficationNotifier(this.repo) : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> verifyOtp(String contact, String otp) async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.forgotPasswordVerification(contact: contact, otp: otp),
      );
    } catch (e) {
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class ForgotPassResetPassNotifier extends StateNotifier<ApiState<String>> {
  ForgotPassResetPassNotifier(this.repo) : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> resetPassword(
    String password,
    String passwordConfirmation,
    String token,
  ) async {
    state = const ApiState.loading();
    try {
      await repo.resetPassword(
        password_confirmation: passwordConfirmation,
        password: password,
        token: token,
      );
      state = const ApiState.loaded(
        data: 'Success',
      );
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class ForgotPassTimerNotifier extends StateNotifier<int> {
  ForgotPassTimerNotifier() : super(0);
  // ignore: unused_field, use_late_for_private_fields_and_variables
  Timer? _timer;

  Future<void> startTimer() async {
    state = 30;
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (state != 0) {
        state = state - 1;
      } else {
        timer.cancel();
      }
    });
  }
}

class LogoutNotifier extends StateNotifier<ApiState<String>> {
  LogoutNotifier(this.repo, this.device_key) : super(const ApiState.initial());

  final IAuthRepo repo;
  // ignore: non_constant_identifier_names
  final String device_key;

  Future<void> logout() async {
    state = const ApiState.loading();
    try {
      await repo.logout(device_key: device_key);
      state = const ApiState.loaded(
        data: 'Success',
      );
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }

  Future<void> deleteAccount() async {
    state = const ApiState.loading();
    try {
      await repo.deleteAccount();
      state = const ApiState.loaded(
        data: 'Success',
      );
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class RegistrationNotifier extends StateNotifier<ApiState<RegisterFlowModel>> {
  RegistrationNotifier(this.repo) : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> register(Map<String, dynamic> data) async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.register(data),
      );
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class RegistrationResendOtpNotifier extends StateNotifier<ApiState<String>> {
  RegistrationResendOtpNotifier(this.repo) : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> resendOtp(String data) async {
    state = const ApiState.loading();
    try {
      await repo.resendOTP(data);
      state = ApiState.loaded(data: 'Success');
    } catch (e) {
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class RegistrationOtpVerificationNotifier
    extends StateNotifier<ApiState<VerifyOtpFlowModel>> {
  RegistrationOtpVerificationNotifier(this.repo)
      : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> verify(String contact, String otp) async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.signUpVerification(contact: contact, otp: otp),
      );
    } catch (e) {
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

/// Status of the customer's join request (public, tracking-token based).
class JoinRequestStatusNotifier
    extends StateNotifier<ApiState<JoinRequestStatus>> {
  JoinRequestStatusNotifier(this.repo) : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> checkStatus(String trackingToken) async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.joinRequestStatus(trackingToken: trackingToken),
      );
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

/// Join-request recovery, step 1: request an OTP for the registered contact.
class JoinRequestRecoveryRequestNotifier
    extends StateNotifier<ApiState<String>> {
  JoinRequestRecoveryRequestNotifier(this.repo)
      : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> request(String contact) async {
    state = const ApiState.loading();
    try {
      await repo.requestJoinRequestRecovery(contact: contact);
      state = const ApiState.loaded(data: 'Success');
    } catch (e) {
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

/// Join-request recovery, step 2: verify the OTP and store a new token.
class JoinRequestRecoveryVerifyNotifier
    extends StateNotifier<ApiState<JoinRequestRecovery>> {
  JoinRequestRecoveryVerifyNotifier(this.repo)
      : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> verify(String contact, String otp) async {
    state = const ApiState.loading();
    try {
      state = ApiState.loaded(
        data: await repo.verifyJoinRequestRecovery(
          contact: contact,
          otp: otp,
        ),
      );
    } catch (e) {
      debugPrint(e.toString());
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}

class ImageUploadNotifier extends StateNotifier<ApiState<String>> {
  ImageUploadNotifier(this.repo) : super(const ApiState.initial());

  final IAuthRepo repo;

  Future<void> addProfilePhoto(File profilePhoto) async {
    state = const ApiState.loading();
    try {
      await repo.addProfilePhoto(profile_photo: profilePhoto);
      state = ApiState.loaded(
        data: 'Success',
      );
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
}
