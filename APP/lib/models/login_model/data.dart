import 'dart:convert';

import 'package:laundry_customer/models/auth_flow_model.dart';
import 'package:laundry_customer/models/login_model/access.dart';
import 'package:laundry_customer/models/login_model/user.dart';

class Data {
  User? user;
  Access? access;

  /// Gate fields returned by the login endpoint when the account may not
  /// sign in yet (HTTP 403). Present only on error-ish responses.
  bool? otpRequired;
  bool? phoneVerified;
  bool? approvalRequired;
  String? approvalStatus;
  AuthNextStep nextStep;
  String? gateMessage;

  Data({
    this.user,
    this.access,
    this.otpRequired,
    this.phoneVerified,
    this.approvalRequired,
    this.approvalStatus,
    this.nextStep = AuthNextStep.unknown,
    this.gateMessage,
  });

  @override
  String toString() =>
      'Data(user: $user, access: $access, nextStep: ${nextStep.value})';

  /// True when the server answered with a gate instead of a session.
  bool get isGate => access == null && nextStep != AuthNextStep.unknown;

  factory Data.fromMap(Map<String, dynamic> data) => Data(
        user: data['user'] == null
            ? null
            : User.fromMap(data['user'] as Map<String, dynamic>),
        access: data['access'] == null
            ? null
            : Access.fromMap(data['access'] as Map<String, dynamic>),
        otpRequired: data['otp_required'] is bool
            ? data['otp_required'] as bool
            : null,
        phoneVerified: data['phone_verified'] is bool
            ? data['phone_verified'] as bool
            : null,
        approvalRequired: data['approval_required'] is bool
            ? data['approval_required'] as bool
            : null,
        approvalStatus: data['approval_status']?.toString(),
        nextStep: AuthNextStep.from(data['next_step']),
        gateMessage: data['message']?.toString(),
      );

  Map<String, dynamic> toMap() => {
        'user': user?.toMap(),
        'access': access?.toMap(),
        'otp_required': otpRequired,
        'phone_verified': phoneVerified,
        'approval_required': approvalRequired,
        'approval_status': approvalStatus,
        'next_step': nextStep.value,
        'message': gateMessage,
      };

  /// `dart:convert`
  ///
  /// Parses the string and returns the resulting Json object as [Data].
  factory Data.fromJson(String data) {
    return Data.fromMap(json.decode(data) as Map<String, dynamic>);
  }

  /// `dart:convert`
  ///
  /// Converts [Data] to a JSON string.
  String toJson() => json.encode(toMap());
}
