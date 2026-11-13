import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/models/auth_flow_model.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

final trackingRecoveryAvailableProvider = FutureProvider.autoDispose<bool>((ref) async {
  final master = await ref.watch(settingsRepoProvider).getMasterData();
  return master.data?.trackingRecoveryAvailable ?? false;
});

/// Arguments for [JoinRequestWaitingScreen].
class JoinRequestWaitingArgs {
  JoinRequestWaitingArgs({
    required this.contact,
    this.trackingToken = '',
  });

  final String contact;
  final String trackingToken;
}

/// Screen shown while a customer's join request awaits admin approval.
///
/// It is driven **entirely** by the server:
/// * status is read from `POST /customer/join-request-status` with the stored
///   `tracking_token`, refreshed every 60 s and on app resume (no tight
///   polling);
/// * `approved` -> the pending state is cleared and the user is sent to login;
/// * `rejected` -> only the generic Arabic message is shown (the backend never
///   exposes the internal reason) and the recovery option stays available;
/// * `404` (invalid / expired / revoked token) -> the token is dropped and
///   recovery is offered;
/// * lost token -> `join-request-recovery` then
///   `join-request-recovery/verify`, after which the new token is stored.
///
/// The tracking token is never logged and never placed in a URL.
class JoinRequestWaitingScreen extends ConsumerStatefulWidget {
  const JoinRequestWaitingScreen({super.key, required this.args});

  final JoinRequestWaitingArgs args;

  @override
  ConsumerState<JoinRequestWaitingScreen> createState() =>
      _JoinRequestWaitingScreenState();
}

class _JoinRequestWaitingScreenState
    extends ConsumerState<JoinRequestWaitingScreen> with WidgetsBindingObserver {
  static const Duration _refreshInterval = Duration(seconds: 60);

  Timer? _timer;
  String _trackingToken = '';
  String _contact = '';
  String? _serverMessage;
  String _approvalStatus = 'pending';
  bool _tokenInvalid = false;

  final TextEditingController _tokenController = TextEditingController();
  final TextEditingController _otpController = TextEditingController();
  final TextEditingController _contactController = TextEditingController();

  bool _recoveryStepTwo = false;

  @override
  void initState() {
    super.initState();
    _trackingToken = widget.args.trackingToken;
    _contact = widget.args.contact;
    _tokenController.text = _trackingToken;
    _contactController.text = _contact;
    WidgetsBinding.instance.addObserver(this);
    _startTimerIfNeeded();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (mounted) _checkStatus();
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    WidgetsBinding.instance.removeObserver(this);
    _tokenController.dispose();
    _otpController.dispose();
    _contactController.dispose();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    // Refresh when the user comes back to the app instead of hammering the
    // rate-limited endpoint while it is in the background.
    if (state == AppLifecycleState.resumed) _checkStatus();
  }

  void _startTimerIfNeeded() {
    _timer?.cancel();
    if (_trackingToken.isEmpty) return;
    _timer = Timer.periodic(_refreshInterval, (_) => _checkStatus());
  }

  Future<void> _persistToken(String token) async {
    final Box authBox = Hive.box(AppHSC.authBox);
    await authBox.put(AppHSC.trackingToken, token);
    setState(() => _trackingToken = token);
  }

  Future<void> _checkStatus() async {
    ref.invalidate(trackingRecoveryAvailableProvider);
    if (_trackingToken.isEmpty) {
      setState(() => _tokenInvalid = true);
      return;
    }
    await ref
        .read(joinRequestStatusProvider.notifier)
        .checkStatus(_trackingToken);
  }

  void _applyStatus(ApiState<JoinRequestStatus> state) {
    state.map(
      initial: (_) {},
      loading: (_) {},
      loaded: (loaded) {
        final status = loaded.data;
        setState(() {
          _approvalStatus = status.approvalStatus ?? 'pending';
          _serverMessage = status.statusMessage ?? status.message;
          _tokenInvalid = false;
        });
        if (status.isApproved) {
          _onApproved();
        } else if (status.isRejected) {
          // Only the generic server message is shown - never a raw reason.
          _serverMessage =
              status.statusMessage ?? 'تم رفض طلب انضمامك. يرجى التواصل مع الدعم.';
        } else {
          _serverMessage = status.statusMessage ??
              'تم إنشاء حسابك بنجاح، وطلبك قيد المراجعة من قبل الإدارة. يرجى الانتظار.';
        }
      },
      error: (err) {
        final text = err.error;
        // A 404 from the server means the token is unknown/expired/revoked.
        if (text.contains('غير صالح') || text.contains('منتهي')) {
          setState(() => _tokenInvalid = true);
        }
      },
    );
  }

  Future<void> _onApproved() async {
    _timer?.cancel();
    final Box authBox = Hive.box(AppHSC.authBox);
    await authBox.delete(AppHSC.trackingToken);
    await authBox.delete(AppHSC.pendingContact);
    if (!mounted) return;
    // The account is now usable - the customer logs in to get a token.
    context.nav.pushNamedAndRemoveUntil(Routes.loginScreen, (route) => false);
  }

  Future<void> _startRecovery() async {
    if (ref.read(trackingRecoveryAvailableProvider).valueOrNull != true) return;
    final contact = _contactController.text.trim();
    if (contact.isEmpty) return;
    await ref
        .read(joinRequestRecoveryRequestProvider.notifier)
        .request(contact);
  }

  Future<void> _verifyRecovery() async {
    if (ref.read(trackingRecoveryAvailableProvider).valueOrNull != true) return;
    final contact = _contactController.text.trim();
    final otp = _otpController.text.trim();
    if (contact.isEmpty || otp.isEmpty) return;
    await ref
        .read(joinRequestRecoveryVerifyProvider.notifier)
        .verify(contact, otp);
  }

  void _onRecoverySuccess(JoinRequestRecovery recovery) {
    final token = recovery.trackingToken ?? '';
    if (token.isEmpty) return;
    _persistToken(token);
    setState(() {
      _recoveryStepTwo = false;
      _tokenInvalid = false;
      _otpController.clear();
    });
    _startTimerIfNeeded();
    _checkStatus();
  }

  @override
  Widget build(BuildContext context) {
    final recoveryAvailable = ref.watch(trackingRecoveryAvailableProvider).valueOrNull == true;
    ref.listen(trackingRecoveryAvailableProvider, (_, next) {
      if (next.valueOrNull != true && mounted) {
        setState(() => _recoveryStepTwo = false);
        _otpController.clear();
      }
    });
    final recoveryRequest = ref.watch(joinRequestRecoveryRequestProvider);
    final recoveryVerify = ref.watch(joinRequestRecoveryVerifyProvider);
    // Apply server responses here (a listener, never during build).
    ref.listen(joinRequestStatusProvider, (_, next) => _applyStatus(next));

    final bool requestBusy =
        recoveryRequest.mapOrNull(loading: (_) => true) == true;
    final bool verifyBusy =
        recoveryVerify.mapOrNull(loading: (_) => true) == true;

    final bool isPending = _approvalStatus != 'rejected';
    final String headline = isPending
        ? 'طلبك قيد المراجعة'
        : 'تم رفض طلب الانضمام';

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: EdgeInsets.symmetric(horizontal: 20.w, vertical: 24.h),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(children: [
                Container(
                  width: 38.w,
                  height: 38.h,
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12.r),
                      border: Border.all(color: const Color(0xFFE2E8F0))),
                  child: IconButton(
                    padding: EdgeInsets.zero,
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.arrow_forward_rounded,
                        size: 18, color: Color(0xFF028090)),
                  ),
                ),
              ]),
              AppSpacerH(18.h),
              Center(
                child: Container(
                  width: 96.w,
                  height: 96.h,
                  decoration: BoxDecoration(
                    color: isPending
                        ? const Color(0xFFEAF6F8)
                        : const Color(0xFFFFF1F2),
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    isPending ? Icons.hourglass_top_rounded : Icons.cancel_rounded,
                    size: 44.sp,
                    color: isPending
                        ? const Color(0xFF156172)
                        : const Color(0xFFE63946),
                  ),
                ),
              ),
              AppSpacerH(16.h),
              Center(
                child: Text(headline,
                    style: TextStyle(
                        fontSize: 20.sp,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xFF0B1E2E),
                        fontFamily: 'Cairo')),
              ),
              AppSpacerH(10.h),
              Center(
                child: Text(
                  _serverMessage ??
                      'تم إنشاء حسابك بنجاح، وطلبك قيد المراجعة من قبل الإدارة.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                      fontSize: 12.5.sp,
                      height: 1.7,
                      color: const Color(0xFF475569)),
                ),
              ),
              AppSpacerH(8.h),
              Center(
                child: Container(
                  padding:
                      EdgeInsets.symmetric(horizontal: 12.w, vertical: 6.h),
                  decoration: BoxDecoration(
                    color: isPending
                        ? const Color(0xFFEAF6F8)
                        : const Color(0xFFFFF1F2),
                    borderRadius: BorderRadius.circular(20.r),
                    border: Border.all(
                      color: isPending
                          ? const Color(0xFF156172).withOpacity(0.25)
                          : const Color(0xFFE63946).withOpacity(0.25),
                    ),
                  ),
                  child: Text(
                    _approvalStatus == 'approved'
                        ? 'مقبول'
                        : _approvalStatus == 'rejected'
                            ? 'مرفوض'
                            : 'قيد المراجعة',
                    style: TextStyle(
                        fontSize: 11.sp,
                        fontWeight: FontWeight.w800,
                        color: isPending
                            ? const Color(0xFF156172)
                            : const Color(0xFFE63946)),
                  ),
                ),
              ),
              AppSpacerH(22.h),

              // ---- Manual status refresh + tracking token entry ----------
              if (_tokenInvalid)
                Container(
                  width: double.infinity,
                  padding: EdgeInsets.all(16.w),
                  decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(18.r),
                      border: Border.all(color: const Color(0xFFE2E8F0))),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('رمز المتابعة غير صالح أو منتهي الصلاحية.',
                          style: TextStyle(
                              fontSize: 12.5.sp,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF9A3412))),
                      AppSpacerH(10.h),
                      TextField(
                        controller: _tokenController,
                        decoration: InputDecoration(
                          hintText: 'أدخل رمز المتابعة',
                          hintStyle: TextStyle(
                              fontSize: 12.sp, color: const Color(0xFF94A3B8)),
                          filled: true,
                          fillColor: const Color(0xFFF8FAFC),
                          border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(14.r),
                              borderSide: const BorderSide(
                                  color: Color(0xFFE2E8F0))),
                        ),
                      ),
                      AppSpacerH(10.h),
                      SizedBox(
                        width: double.infinity,
                        height: 46.h,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF028090),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(14.r)),
                          ),
                          onPressed: () {
                            final t = _tokenController.text.trim();
                            if (t.isNotEmpty) {
                              _persistToken(t);
                              _startTimerIfNeeded();
                              _checkStatus();
                            }
                          },
                          child: const Text('متابعة الحالة',
                              style: TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.w800)),
                        ),
                      ),
                    ],
                  ),
                ),

              if (!_tokenInvalid) ...[
                AppSpacerH(6.h),
                Text('سيتم تحديث الحالة تلقائياً. يمكنك أيضاً التحديث يدوياً.',
                    style: TextStyle(
                        fontSize: 11.5.sp, color: const Color(0xFF64748B))),
                AppSpacerH(12.h),
                SizedBox(
                  width: double.infinity,
                  height: 48.h,
                  child: OutlinedButton.icon(
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(color: Color(0xFF028090)),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14.r)),
                    ),
                    onPressed: _checkStatus,
                    icon: const Icon(Icons.refresh_rounded,
                        size: 18, color: Color(0xFF028090)),
                    label: const Text('تحديث الحالة',
                        style: TextStyle(
                            color: Color(0xFF028090),
                            fontWeight: FontWeight.w800)),
                  ),
                ),
              ],

              AppSpacerH(24.h),

              // Only show code recovery when the server explicitly enables it.
              if (recoveryAvailable) Container(
                width: double.infinity,
                padding: EdgeInsets.all(16.w),
                decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(18.r),
                    border: Border.all(color: const Color(0xFFE2E8F0))),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('هل فقدت رمز المتابعة؟',
                        style: TextStyle(
                            fontSize: 14.sp,
                            fontWeight: FontWeight.w900,
                            color: const Color(0xFF0B1E2E))),
                    AppSpacerH(6.h),
                    Text(
                        'أدخل رقم جوالك أو بريدك المسجّل وسنرسل لك رمز تحقق لاستعادة رمز المتابعة.',
                        style: TextStyle(
                            fontSize: 11.5.sp,
                            height: 1.6,
                            color: const Color(0xFF475569))),
                    AppSpacerH(12.h),
                    TextField(
                      controller: _contactController,
                      keyboardType: TextInputType.emailAddress,
                      decoration: InputDecoration(
                        hintText: '05xxxxxxxx أو email',
                        hintStyle: TextStyle(
                            fontSize: 12.sp, color: const Color(0xFF94A3B8)),
                        filled: true,
                        fillColor: const Color(0xFFF8FAFC),
                        border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(14.r),
                            borderSide:
                                const BorderSide(color: Color(0xFFE2E8F0))),
                      ),
                    ),
                    AppSpacerH(12.h),

                    // Step 1: request the recovery OTP.
                    if (!_recoveryStepTwo)
                      SizedBox(
                        width: double.infinity,
                        height: 46.h,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF028090),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(14.r)),
                          ),
                          onPressed: requestBusy
                              ? null
                              : () async {
                                  await _startRecovery();
                                  if (!mounted) return;
                                  // The endpoint always answers generically;
                                  // step two is offered on success OR nothing.
                                  final ok = recoveryRequest.mapOrNull(
                                    loaded: (_) => true,
                                  );
                                  if (ok == true) {
                                    setState(() => _recoveryStepTwo = true);
                                  } else {
                                    final err = recoveryRequest.mapOrNull(
                                        error: (e) => e.error);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(err ??
                                              'تعذر إرسال رمز التحقق، حاول لاحقاً')),
                                    );
                                  }
                                },
                          child: requestBusy
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                      strokeWidth: 2, color: Colors.white))
                              : const Text('إرسال رمز التحقق',
                                  style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.w800)),
                        ),
                      ),

                    // Step 2: verify the recovery OTP.
                    if (_recoveryStepTwo) ...[
                      AppSpacerH(6.h),
                      Text('أدخل رمز التحقق الذي وصلك',
                          style: TextStyle(
                              fontSize: 11.5.sp,
                              color: const Color(0xFF475569))),
                      AppSpacerH(8.h),
                      TextField(
                        controller: _otpController,
                        keyboardType: TextInputType.number,
                        decoration: InputDecoration(
                          hintText: '••••••',
                          hintStyle: TextStyle(
                              fontSize: 12.sp, color: const Color(0xFF94A3B8)),
                          filled: true,
                          fillColor: const Color(0xFFF8FAFC),
                          border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(14.r),
                              borderSide:
                                  const BorderSide(color: Color(0xFFE2E8F0))),
                        ),
                      ),
                      AppSpacerH(10.h),
                      SizedBox(
                        width: double.infinity,
                        height: 46.h,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF028090),
                            shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(14.r)),
                          ),
                          onPressed: verifyBusy
                              ? null
                              : () async {
                                  await _verifyRecovery();
                                  if (!mounted) return;
                                  final result = recoveryVerify.mapOrNull(
                                    loaded: (loaded) => loaded.data,
                                  );
                                  if (result != null &&
                                      (result.trackingToken ?? '').isNotEmpty) {
                                    _onRecoverySuccess(result);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text('تم إنشاء رمز متابعة جديد.')),
                                    );
                                  } else {
                                    final err = recoveryVerify.mapOrNull(
                                        error: (e) => e.error);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(err ??
                                              'رمز غير صالح أو منتهي الصلاحية.')),
                                    );
                                  }
                                },
                          child: verifyBusy
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                      strokeWidth: 2, color: Colors.white))
                              : const Text('تأكيد واستعادة الرمز',
                                  style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.w800)),
                        ),
                      ),
                    ],

                    if (recoveryRequest.mapOrNull(error: (e) => e.error) !=
                        null) ...[
                      AppSpacerH(8.h),
                      Text(recoveryRequest.mapOrNull(error: (e) => e.error) ?? '',
                          style: TextStyle(
                              fontSize: 11.sp, color: AppColors.red)),
                    ],
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
