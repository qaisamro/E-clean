import 'dart:convert';

import 'package:laundry_customer/models/register_model/access.dart';

class PasswordRecoveryFlow {
  const PasswordRecoveryFlow({required this.requiresOtp, this.otp = '', this.message = ''});
  final bool requiresOtp;
  final String otp;
  final String message;

  factory PasswordRecoveryFlow.fromMap(Map<String, dynamic> body) {
    final data = body['data'] is Map ? body['data'] as Map : const {};
    final flag = data['otp_required'];
    return PasswordRecoveryFlow(
      // Older servers omit the flag and continue to use the existing OTP flow.
      requiresOtp: data['next_step'] != 'contact_support' &&
          flag != false && flag != 0 && flag != '0',
      otp: (data['otp'] ?? body['otp'])?.toString() ?? '',
      message: body['message']?.toString() ?? '',
    );
  }
}

/// The exact `next_step` values documented by the Laravel backend
/// (`Web_Admin/docs/CUSTOMER_JOIN_REQUEST_API.md`).
///
/// `verifyOtp`      -> the customer must verify a phone code.
/// `awaitApproval`  -> a join request is pending admin approval.
/// `ready`          -> nothing outstanding, an access token may be issued.
/// `rejected`       -> the join request was rejected.
/// `retryLater`     -> the join-request store is temporarily unavailable (503).
enum AuthNextStep {
  verifyOtp('verify_otp'),
  awaitApproval('await_approval'),
  ready('ready'),
  rejected('rejected'),
  retryLater('retry_later'),
  unknown('');

  const AuthNextStep(this.value);

  final String value;

  static AuthNextStep from(dynamic raw) {
    final String? v = raw?.toString();
    for (final step in AuthNextStep.values) {
      if (step.value == v) return step;
    }
    return AuthNextStep.unknown;
  }

  bool get isVerifyOtp => this == AuthNextStep.verifyOtp;
  bool get isAwaitApproval => this == AuthNextStep.awaitApproval;
  bool get isReady => this == AuthNextStep.ready;
  bool get isRejected => this == AuthNextStep.rejected;
  bool get isRetryLater => this == AuthNextStep.retryLater;
}

/// Result of `POST /api/register`.
///
/// Mirrors the documented contract: `access` is only returned when
/// [nextStep] is [AuthNextStep.ready] and `tracking_token` only when
/// `approval_required` is true. Nothing is assumed locally.
class RegisterFlowModel {
  RegisterFlowModel({
    this.message,
    this.otpRequired,
    this.approvalRequired,
    this.approvalStatus,
    this.nextStep = AuthNextStep.unknown,
    this.trackingToken,
    this.access,
    this.otp,
    this.serverMessage,
    this.user,
  });

  final String? message;
  final bool? otpRequired;
  final bool? approvalRequired;
  final String? approvalStatus;
  final AuthNextStep nextStep;
  final String? trackingToken;
  final Access? access;
  /// Only present in local/test environments for whitelisted contacts.
  final String? otp;
  /// Server-configurable status message (pending / rejected / approved).
  final String? serverMessage;
  /// The created `user` resource (kept for the local user box, as before).
  final Map<String, dynamic>? user;

  bool get hasAccessToken => access != null && (access?.token ?? '').isNotEmpty;

  factory RegisterFlowModel.fromMap(Map<String, dynamic> map) {
    final dynamic d = map['data'];
    final Map<String, dynamic> data =
        d is Map ? Map<String, dynamic>.from(d) : <String, dynamic>{};
    final dynamic accessRaw = data['access'];
    return RegisterFlowModel(
      message: map['message']?.toString(),
      otpRequired: _asBool(data['otp_required']),
      approvalRequired: _asBool(data['approval_required']),
      approvalStatus: data['approval_status']?.toString(),
      nextStep: AuthNextStep.from(data['next_step']),
      trackingToken: data['tracking_token']?.toString(),
      access: accessRaw is Map
          ? Access.fromMap(Map<String, dynamic>.from(accessRaw))
          : null,
      otp: data['otp']?.toString(),
      serverMessage: data['message']?.toString(),
      user: data['user'] is Map
          ? Map<String, dynamic>.from(data['user'] as Map)
          : null,
    );
  }

  Map<String, dynamic> toMap() => {
        'message': message,
        'data': {
          'otp_required': otpRequired,
          'approval_required': approvalRequired,
          'approval_status': approvalStatus,
          'next_step': nextStep.value,
          'tracking_token': trackingToken,
          'access': access?.toMap(),
          'otp': otp,
          'message': serverMessage,
          'user': user,
        },
      };
}

/// Result of `POST /api/contact/verify` (OTP verification) and of the
/// login gate. `access` only arrives when nothing else is outstanding.
class VerifyOtpFlowModel {
  VerifyOtpFlowModel({
    this.message,
    this.otpVerified,
    this.nextStep = AuthNextStep.unknown,
    this.access,
    this.trackingToken,
    this.approvalStatus,
    this.approvalRequired,
  });

  final String? message;
  final bool? otpVerified;
  final AuthNextStep nextStep;
  final Access? access;
  final String? trackingToken;
  final String? approvalStatus;
  final bool? approvalRequired;

  bool get hasAccessToken => access != null && (access?.token ?? '').isNotEmpty;

  factory VerifyOtpFlowModel.fromMap(Map<String, dynamic> map) {
    final dynamic d = map['data'];
    final Map<String, dynamic> data =
        d is Map ? Map<String, dynamic>.from(d) : <String, dynamic>{};
    final dynamic accessRaw = data['access'];
    return VerifyOtpFlowModel(
      message: map['message']?.toString(),
      otpVerified: _asBool(data['otp_verified']),
      nextStep: AuthNextStep.from(data['next_step']),
      access: accessRaw is Map
          ? Access.fromMap(Map<String, dynamic>.from(accessRaw))
          : null,
      trackingToken: data['tracking_token']?.toString(),
      approvalStatus: data['approval_status']?.toString(),
      approvalRequired: _asBool(data['approval_required']),
    );
  }
}

/// Result of `POST /api/customer/join-request-status` (public, token-based)
/// and of `GET /api/customer/join-request-status` (authenticated).
class JoinRequestStatus {
  JoinRequestStatus({
    this.message,
    this.approvalStatus,
    this.nextStep = AuthNextStep.unknown,
    this.statusMessage,
  });

  final String? message;
  final String? approvalStatus;
  final AuthNextStep nextStep;
  /// The generic, server-configurable Arabic message. Never a raw reason.
  final String? statusMessage;

  bool get isPending => approvalStatus == 'pending';
  bool get isApproved => approvalStatus == 'approved';
  bool get isRejected => approvalStatus == 'rejected';

  factory JoinRequestStatus.fromMap(Map<String, dynamic> map) {
    final dynamic d = map['data'];
    final Map<String, dynamic> data =
        d is Map ? Map<String, dynamic>.from(d) : <String, dynamic>{};
    return JoinRequestStatus(
      message: map['message']?.toString(),
      approvalStatus: data['approval_status']?.toString(),
      nextStep: AuthNextStep.from(data['next_step']),
      statusMessage: data['message']?.toString(),
    );
  }
}

/// Result of `POST /api/customer/join-request-recovery/verify`.
class JoinRequestRecovery {
  JoinRequestRecovery({this.message, this.trackingToken, this.approvalStatus});

  final String? message;
  final String? trackingToken;
  final String? approvalStatus;

  factory JoinRequestRecovery.fromMap(Map<String, dynamic> map) {
    final dynamic d = map['data'];
    final Map<String, dynamic> data =
        d is Map ? Map<String, dynamic>.from(d) : <String, dynamic>{};
    return JoinRequestRecovery(
      message: map['message']?.toString(),
      trackingToken: data['tracking_token']?.toString(),
      approvalStatus: data['approval_status']?.toString(),
    );
  }
}

bool? _asBool(dynamic value) {
  if (value is bool) return value;
  if (value is num) return value != 0;
  if (value is String) {
    if (value == '1' || value.toLowerCase() == 'true') return true;
    if (value == '0' || value.toLowerCase() == 'false') return false;
  }
  return null;
}

/// Encodes a map to JSON (helper used by tests / debugging).
String encodeFlowJson(Map<String, dynamic> map) => json.encode(map);
