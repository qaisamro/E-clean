// Integration-focused unit tests for the customer join-request flow.
//
// These tests mirror the exact contract documented by the Laravel backend
// (Web_Admin/docs/CUSTOMER_JOIN_REQUEST_API.md) and validate the model layer
// the Flutter app relies on - no live API is contacted.

import 'package:flutter_test/flutter_test.dart';
import 'package:laundry_customer/models/auth_flow_model.dart';
import 'package:laundry_customer/models/login_model/data.dart' as login_data;
import 'package:laundry_customer/models/login_model/login_model.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart';

/// Exactly the documented register response for state 1
/// (OTP off, approval off).
Map<String, dynamic> _registerState1() => {
      'message': 'Registration successfully complete',
      'data': {
        'user': {'id': 7, 'name': 'Sara'},
        'otp_required': false,
        'approval_required': false,
        'approval_status': 'approved',
        'next_step': 'ready',
        'access': {
          'auth_type': 'Bearer',
          'token': 'tok_state1',
          'expires_at': '2026-12-08 00:00:00',
        },
      },
    };

/// Exactly the documented register response for state 2
/// (OTP on, approval off).
Map<String, dynamic> _registerState2() => {
      'message': 'Registration successfully complete',
      'data': {
        'user': {'id': 8, 'name': 'Omar'},
        'otp_required': true,
        'approval_required': false,
        'approval_status': 'approved',
        'next_step': 'verify_otp',
      },
    };

/// Exactly the documented register response for state 3
/// (OTP off, approval on).
Map<String, dynamic> _registerState3() => {
      'message': 'تم إنشاء حسابك بنجاح، وطلبك قيد المراجعة...',
      'data': {
        'user': {'id': 9, 'name': 'Lina'},
        'otp_required': false,
        'approval_required': true,
        'approval_status': 'pending',
        'next_step': 'await_approval',
        'tracking_token': 'a1b2c3tracking',
      },
    };

/// Exactly the documented register response for state 4
/// (OTP on, approval on).
Map<String, dynamic> _registerState4() => {
      'message': 'تم إنشاء حسابك بنجاح، وطلبك قيد المراجعة...',
      'data': {
        'user': {'id': 10, 'name': 'Khaled'},
        'otp_required': true,
        'approval_required': true,
        'approval_status': 'pending',
        'next_step': 'verify_otp',
        'tracking_token': 'd4e5f6tracking',
      },
    };

/// Registration when the join-request store is unavailable (503).
Map<String, dynamic> _registerRetryLater() => {
      'message': 'تعذر إنشاء طلب الانضمام حاليًا...',
      'data': {'next_step': 'retry_later'},
    };

Map<String, dynamic> _verifyReady() => {
      'message': 'Mobile verification complete',
      'data': {
        'user': {'id': 8, 'name': 'Omar', 'phone_verified': true},
        'otp_verified': true,
        'approval_required': false,
        'approval_status': 'approved',
        'next_step': 'ready',
        'access': {
          'auth_type': 'Bearer',
          'token': 'tok_after_otp',
          'expires_at': '2026-12-08 00:00:00',
        },
      },
    };

Map<String, dynamic> _verifyAwaitingApproval() => {
      'message': 'Mobile verification complete',
      'data': {
        'user': {'id': 10, 'name': 'Khaled', 'phone_verified': true},
        'otp_verified': true,
        'approval_required': true,
        'approval_status': 'pending',
        'next_step': 'await_approval',
      },
    };

Map<String, dynamic> _statusPending() => {
      'message': 'حالة الطلب.',
      'data': {
        'approval_status': 'pending',
        'message': 'الحساب قيد المراجعة من قبل الإدارة.',
      },
    };

Map<String, dynamic> _statusRejected() => {
      'message': 'حالة الطلب.',
      'data': {
        'approval_status': 'rejected',
        'message': 'تم رفض طلب انضمامك.',
      },
    };

Map<String, dynamic> _recoveryNewToken() => {
      'message': 'تم إنشاء رمز متابعة جديد.',
      'data': {'tracking_token': 'newtoken1234567890', 'approval_status': 'pending'},
    };

void main() {
  group('AuthNextStep', () {
    test('maps the documented values', () {
      expect(AuthNextStep.from('verify_otp'), AuthNextStep.verifyOtp);
      expect(AuthNextStep.from('await_approval'), AuthNextStep.awaitApproval);
      expect(AuthNextStep.from('ready'), AuthNextStep.ready);
      expect(AuthNextStep.from('rejected'), AuthNextStep.rejected);
      expect(AuthNextStep.from('retry_later'), AuthNextStep.retryLater);
      expect(AuthNextStep.from(null), AuthNextStep.unknown);
      expect(AuthNextStep.from('nonsense'), AuthNextStep.unknown);
    });
  });

  group('RegisterFlowModel', () {
    test('state 1 (otp off, approval off) yields a token and ready', () {
      final flow = RegisterFlowModel.fromMap(_registerState1());
      expect(flow.nextStep, AuthNextStep.ready);
      expect(flow.hasAccessToken, isTrue);
      expect(flow.access?.token, 'tok_state1');
      expect(flow.trackingToken, isNull);
      expect(flow.user?['name'], 'Sara');
    });

    test('state 2 (otp on, approval off) yields NO token', () {
      final flow = RegisterFlowModel.fromMap(_registerState2());
      expect(flow.nextStep, AuthNextStep.verifyOtp);
      expect(flow.nextStep.isVerifyOtp, isTrue);
      expect(flow.hasAccessToken, isFalse);
      expect(flow.access, isNull);
      expect(flow.otpRequired, isTrue);
    });

    test('state 3 (otp off, approval on) yields tracking token, no access',
        () {
      final flow = RegisterFlowModel.fromMap(_registerState3());
      expect(flow.nextStep, AuthNextStep.awaitApproval);
      expect(flow.nextStep.isAwaitApproval, isTrue);
      expect(flow.hasAccessToken, isFalse);
      expect(flow.trackingToken, 'a1b2c3tracking');
      expect(flow.approvalStatus, 'pending');
    });

    test('state 4 (otp on, approval on) still requires OTP first', () {
      final flow = RegisterFlowModel.fromMap(_registerState4());
      expect(flow.nextStep, AuthNextStep.verifyOtp);
      expect(flow.hasAccessToken, isFalse);
      expect(flow.trackingToken, 'd4e5f6tracking');
      expect(flow.otpRequired, isTrue);
      expect(flow.approvalRequired, isTrue);
    });

    test('503 store unavailable surfaces retry_later', () {
      final flow = RegisterFlowModel.fromMap(_registerRetryLater());
      expect(flow.nextStep, AuthNextStep.retryLater);
      expect(flow.nextStep.isRetryLater, isTrue);
      expect(flow.hasAccessToken, isFalse);
    });

    test('never fabricates an access token', () {
      final flow = RegisterFlowModel.fromMap(_registerState2());
      expect(flow.hasAccessToken, isFalse);
      // A UI must never write an empty token to storage.
      expect(flow.access?.token ?? '', isEmpty);
    });
  });

  group('VerifyOtpFlowModel', () {
    test('ready returns a token', () {
      final v = VerifyOtpFlowModel.fromMap(_verifyReady());
      expect(v.otpVerified, isTrue);
      expect(v.nextStep, AuthNextStep.ready);
      expect(v.hasAccessToken, isTrue);
      expect(v.access?.token, 'tok_after_otp');
    });

    test('await_approval after OTP returns no token', () {
      final v = VerifyOtpFlowModel.fromMap(_verifyAwaitingApproval());
      expect(v.otpVerified, isTrue);
      expect(v.nextStep, AuthNextStep.awaitApproval);
      expect(v.hasAccessToken, isFalse);
      expect(v.approvalStatus, 'pending');
    });
  });

  group('JoinRequestStatus / recovery', () {
    test('pending status keeps the user waiting', () {
      final s = JoinRequestStatus.fromMap(_statusPending());
      expect(s.isPending, isTrue);
      expect(s.isApproved, isFalse);
      expect(s.isRejected, isFalse);
      expect(s.statusMessage, contains('قيد المراجعة'));
    });

    test('rejected status only exposes the generic message', () {
      final s = JoinRequestStatus.fromMap(_statusRejected());
      expect(s.isRejected, isTrue);
      expect(s.isPending, isFalse);
      // The API never sends an internal reason; only the generic message.
      expect(s.statusMessage, 'تم رفض طلب انضمامك.');
      expect(s.statusMessage, isNot(contains('internal')));
    });

    test('recovery verify returns the new tracking token', () {
      final r = JoinRequestRecovery.fromMap(_recoveryNewToken());
      expect(r.trackingToken, 'newtoken1234567890');
      expect(r.approvalStatus, 'pending');
    });
  });

  group('Login gate model', () {
    test('403 pending gate is parsed and exposes next_step', () {
      final model = LoginModel.fromMap({
        'message': 'الحساب قيد المراجعة من قبل الإدارة.',
        'data': {
          'approval_status': 'pending',
          'approval_required': true,
          'next_step': 'await_approval',
          'message': 'الحساب قيد المراجعة من قبل الإدارة.',
        },
      });
      final data = model.data! as login_data.Data;
      expect(data.isGate, isTrue);
      expect(data.access, isNull);
      expect(data.nextStep, AuthNextStep.awaitApproval);
    });

    test('403 verify_otp gate is parsed', () {
      final model = LoginModel.fromMap({
        'message': 'يرجى التحقق من رقم هاتفك عبر رمز التحقق قبل تسجيل الدخول.',
        'data': {
          'otp_required': true,
          'phone_verified': false,
          'approval_required': false,
          'approval_status': 'approved',
          'next_step': 'verify_otp',
        },
      });
      final data = model.data! as login_data.Data;
      expect(data.isGate, isTrue);
      expect(data.nextStep, AuthNextStep.verifyOtp);
      expect(data.access, isNull);
    });

    test('200 login keeps a full session and is not a gate', () {
      final model = LoginModel.fromMap({
        'message': 'Log In Successfull',
        'data': {
          'user': {'id': 1, 'name': 'Existing'},
          'access': {
            'auth_type': 'Bearer',
            'token': 'tok_login',
            'expires_at': '2026-12-08 00:00:00',
          },
          'approval_status': 'approved',
          'approval_required': false,
          'next_step': 'ready',
        },
      });
      final data = model.data! as login_data.Data;
      expect(data.isGate, isFalse);
      expect(data.nextStep, AuthNextStep.ready);
      expect(data.access?.token, 'tok_login');
    });
  });

  group('Order payload (cash only)', () {
    test('checkout serializes payment_type=cash', () {
      // The backend enum only accepts stripe|paypal|paystack|paytab|razorpay|cash.
      // After removing online payment, checkout must send 'cash'.
      final model = OrderPlaceModel(
        pick_date: '2026-11-10',
        pick_hour: '10',
        delivery_date: '2026-11-12',
        delivery_hour: '14',
        products: [
          OrderProductModel(id: '5', quantity: '2', subid: '7'),
        ],        address_id: '3',
        additional_service_id: const [],
        paymentType: 'cash',
        vendorId: '12',
      );

      final map = model.toMap();
      expect(map['payment_type'], 'cash');
      expect(map['products'], isA<List>());
      expect((map['products'] as List).first['id'], '5');
      expect(map['address_id'], '3');
      expect(map['vendor_id'], '12');
      expect(map['pick_date'], '2026-11-10');

      // No online-gateway keys may leak into the request.
      expect(map.containsKey('payment_intent'), isFalse);
      expect(map.containsKey('stripe'), isFalse);
    });

    test('coupon and instruction survive serialization', () {
      final model = OrderPlaceModel(
        pick_date: '2026-11-10',
        pick_hour: '10',
        delivery_date: '2026-11-12',
        delivery_hour: '14',
        products: const [],
        address_id: '3',
        coupon_id: '42',
        instruction: 'اتصل قبل الوصول',
        additional_service_id: const [],
        paymentType: 'cash',
      );
      final map = model.toMap();
      expect(map['coupon_id'], '42');
      expect(map['instruction'], 'اتصل قبل الوصول');
      expect(map['payment_type'], 'cash');
    });
  });
}
