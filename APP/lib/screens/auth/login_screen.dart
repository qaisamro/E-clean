import 'package:flutter/material.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/auth_flow_model.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/screens/auth/join_request_waiting_screen.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';

/// Login screen.
///
/// The backend answers `POST /login` with:
/// * `200` + `data.access` -> the account may use the app.
/// * `403` + `data.next_step` -> the account must first verify its phone
///   (`verify_otp`) or wait for admin approval (`await_approval`), or its
///   join request was `rejected`.
/// * `401` -> the stored session is no longer valid (never silently reused).
///
/// The screen branches on the server response instead of assuming a session.
class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final GlobalKey<FormBuilderState> _formkey = GlobalKey<FormBuilderState>();
  final FocusNode _contactFocus = FocusNode();
  final FocusNode _passwordFocus = FocusNode();
  bool obsecureText = true;

  @override
  void dispose() {
    _contactFocus.dispose();
    _passwordFocus.dispose();
    super.dispose();
  }

  InputDecoration _dec(
          {required String hint, Widget? prefix, Widget? suffix}) =>
      InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
        prefixIcon: prefix,
        suffixIcon: suffix,
        filled: true,
        fillColor: const Color(0xFFF8FAFC),
        contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 14.h),
        border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16.r),
            borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16.r),
            borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16.r),
            borderSide: const BorderSide(color: Color(0xFF00A896), width: 1.4)),
      );

  /// Persist the session atomically: only a real access token is written, and
  /// any stale join-request token is dropped so a new login starts clean.
  Future<void> _saveSession(String type, String token, Map user) async {
    final Box authBox = Hive.box(AppHSC.authBox);
    final Box userBox = Hive.box(AppHSC.userBox);
    await authBox.put(AppHSC.authTokenType, type);
    await authBox.put(AppHSC.authToken, token);
    await authBox.delete(AppHSC.trackingToken);
    await authBox.delete(AppHSC.pendingContact);
    userBox.putAll(Map<String, dynamic>.from(user));
  }

  void _goHome() {
    context.nav.pushNamedAndRemoveUntil(Routes.homeScreen, (route) => false);
  }

  Future<void> _handleGate(
    AuthNextStep step,
    String contact,
    String? serverMessage,
  ) async {
    final Box authBox = Hive.box(AppHSC.authBox);
    await authBox.put(AppHSC.pendingContact, contact);

    if (!mounted) return;

    if (step.isVerifyOtp) {
      // Fire the resend in the background; failures surface in the OTP screen.
      final container = ProviderScope.containerOf(context);
      await container
          .read(registrationResendOtpProvider.notifier)
          .resendOtp(contact);
      if (!mounted) return;
      context.nav.pushNamed(
        Routes.signUpOtpScreen,
        arguments: {'contact': contact, 'mode': 'login'},
      );
      return;
    }

    if (step.isAwaitApproval || step.isRejected) {
      context.nav.pushNamed(
        Routes.joinRequestWaitingScreen,
        arguments: JoinRequestWaitingArgs(
          contact: contact,
          trackingToken: authBox.get(AppHSC.trackingToken) as String? ?? '',
        ),
      );
      return;
    }

    if (step.isRetryLater) {
      // The join-request store is temporarily unavailable: surface the
      // server message instead of pretending the login failed outright.
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(serverMessage ?? 'يرجى المحاولة بعد قليل.')),
      );
      return;
    }

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(serverMessage ?? 'تعذر تسجيل الدخول، حاول مرة أخرى')),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            children: [
              SizedBox(height: 14.h),
              Padding(
                padding: EdgeInsets.symmetric(horizontal: 16.w),
                child: Row(children: [
                  InkWell(
                    borderRadius: BorderRadius.circular(12.r),
                    onTap: () => Navigator.pop(context),
                    child: Container(
                        width: 38.w,
                        height: 38.h,
                        decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12.r),
                            border: Border.all(color: const Color(0xFFE2E8F0))),
                        child: const Icon(Icons.arrow_forward_rounded,
                            size: 18, color: Color(0xFF028090))),
                  ),
                ]),
              ),
              SizedBox(height: 14.h),
              Text('تسجيل الدخول',
                  style: TextStyle(
                      fontSize: 22.sp,
                      fontWeight: FontWeight.w900,
                      color: const Color(0xFF0B1E2E),
                      fontFamily: 'Cairo')),
              SizedBox(height: 4.h),
              Text('أدخل رقم جوالك أو بريدك الإلكتروني وكلمة المرور',
                  style: TextStyle(
                      fontSize: 11.5.sp, color: const Color(0xFF64748B)),
                  textAlign: TextAlign.center),
              SizedBox(height: 16.h),
              Container(
                margin: EdgeInsets.symmetric(horizontal: 20.w),
                padding: EdgeInsets.all(20.w),
                decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(24.r),
                    border: Border.all(
                        color: const Color(0xFFE2E8F0).withOpacity(0.9)),
                    boxShadow: [
                      BoxShadow(
                          color: const Color(0xFF0B1E2E).withOpacity(0.06),
                          blurRadius: 18,
                          offset: const Offset(0, 8))
                    ]),
                child: FormBuilder(
                  key: _formkey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('رقم الجوال أو البريد الإلكتروني',
                          style: TextStyle(
                              fontSize: 12.sp,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(
                          focusNode: _contactFocus,
                          name: 'contact',
                          decoration: _dec(
                              hint: '05xxxxxxxx أو email',
                              prefix: Icon(Icons.person_outline_rounded,
                                  size: 18.sp, color: const Color(0xFF028090))),
                          keyboardType: TextInputType.emailAddress,
                          textInputAction: TextInputAction.next,
                          validator: (v) =>
                              (v == null || v.trim().isEmpty) ? 'هذا الحقل مطلوب' : null),
                      SizedBox(height: 14.h),
                      Text('كلمة المرور',
                          style: TextStyle(
                              fontSize: 12.sp,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(
                          focusNode: _passwordFocus,
                          name: 'password',
                          obscureText: obsecureText,
                          decoration: _dec(
                              hint: '••••••••',
                              prefix: Icon(Icons.lock_outline_rounded,
                                  size: 18.sp, color: const Color(0xFF028090)),
                              suffix: InkWell(
                                  onTap: () => setState(
                                      () => obsecureText = !obsecureText),
                                  child: Icon(
                                      obsecureText
                                          ? Icons.visibility_off_outlined
                                          : Icons.visibility_outlined,
                                      size: 18.sp,
                                      color: const Color(0xFF64748B)))),
                          keyboardType: TextInputType.text,
                          textInputAction: TextInputAction.done,
                          validator: (v) =>
                              (v == null || v.trim().isEmpty) ? 'هذا الحقل مطلوب' : null),
                      SizedBox(height: 18.h),
                      SizedBox(
                        height: 50.h,
                        width: double.infinity,
                        child: Consumer(builder: (context, ref, _) {
                          return ref.watch(loginProvider).map(
                                initial: (_) => DecoratedBox(
                                  decoration: BoxDecoration(
                                      gradient: const LinearGradient(
                                          colors: [
                                            Color(0xFF028090),
                                            Color(0xFF00A896)
                                          ],
                                          begin: Alignment.topRight,
                                          end: Alignment.bottomLeft),
                                      borderRadius: BorderRadius.circular(16.r),
                                      boxShadow: [
                                        BoxShadow(
                                            color: const Color(0xFF00A896)
                                                .withOpacity(0.28),
                                            blurRadius: 12,
                                            offset: const Offset(0, 6))
                                      ]),
                                  child: ElevatedButton(
                                    onPressed: () {
                                      _contactFocus.unfocus();
                                      _passwordFocus.unfocus();
                                      if (_formkey.currentState != null &&
                                          _formkey.currentState!
                                              .saveAndValidate()) {
                                        final values =
                                            _formkey.currentState!.value;
                                        ref
                                            .read(loginProvider.notifier)
                                            .login(
                                              (values['contact'] as String)
                                                  .trim(),
                                              (values['password'] as String).trim(),
                                            );
                                      }
                                    },
                                    style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.transparent,
                                        shadowColor: Colors.transparent,
                                        shape: RoundedRectangleBorder(
                                            borderRadius:
                                                BorderRadius.circular(16.r))),
                                    child: Text('تسجيل الدخول',
                                        style: TextStyle(
                                            fontSize: 14.sp,
                                            fontWeight: FontWeight.w900,
                                            color: Colors.white,
                                            fontFamily: 'Cairo')),
                                  ),
                                ),
                                loading: (_) => const Center(
                                    child: SizedBox(
                                        width: 22,
                                        height: 22,
                                        child: CircularProgressIndicator(
                                            strokeWidth: 2,
                                            color: Color(0xFF028090)))),
                                loaded: (_) {
                                  final data = _.data.data;
                                  final access = data?.access;
                                  final nextStep =
                                      data?.nextStep ?? AuthNextStep.unknown;

                                  // 1) Full session -> persist and enter.
                                  if (access != null &&
                                      (access.token ?? '').isNotEmpty &&
                                      nextStep.isReady) {
                                    _saveSession(
                                      access.authType ?? 'Bearer',
                                      access.token!,
                                      data?.user?.toMap() ??
                                          <String, dynamic>{},
                                    );
                                    Future.delayed(transissionDuration)
                                        .then((_) => _goHome());
                                    return Container(
                                        alignment: Alignment.center,
                                        decoration: BoxDecoration(
                                            color: const Color(0xFFEFFAF8),
                                            borderRadius:
                                                BorderRadius.circular(12.r)),
                                        child: Text('تم تسجيل الدخول بنجاح',
                                            style: TextStyle(
                                                fontSize: 12.sp,
                                                fontWeight: FontWeight.w800,
                                                color: const Color(0xFF028090))));
                                  }

                                  // 2) Gate -> follow the server's next_step.
                                  final contact = (_formkey
                                          .currentState
                                          ?.value['contact'] as String?)
                                      ?.trim() ??
                                      '';
                                  final serverMessage =
                                      data?.gateMessage ?? _.data.message;
                                  Future.delayed(buildDuration).then((_) {
                                    ref.refresh(loginProvider);
                                    _handleGate(
                                        nextStep, contact, serverMessage);
                                  });
                                  return Container(
                                      alignment: Alignment.center,
                                      decoration: BoxDecoration(
                                          color: const Color(0xFFFFF7ED),
                                          borderRadius:
                                              BorderRadius.circular(12.r)),
                                      child: Text(serverMessage ?? '...',
                                          textAlign: TextAlign.center,
                                          style: TextStyle(
                                              fontSize: 11.5.sp,
                                              fontWeight: FontWeight.w700,
                                              color: const Color(0xFF9A3412))));
                                },
                                error: (_) {
                                  Future.delayed(transissionDuration).then(
                                      (_) => ref.refresh(loginProvider));
                                  return Container(
                                      padding: EdgeInsets.all(10.w),
                                      decoration: BoxDecoration(
                                          color: const Color(0xFFFFF1F2),
                                          borderRadius:
                                              BorderRadius.circular(12.r),
                                          border: Border.all(
                                              color: const Color(0xFFE63946)
                                                  .withOpacity(0.22))),
                                      child: Text(_.error,
                                          style: TextStyle(
                                              fontSize: 11.sp,
                                              color: const Color(0xFFE63946)),
                                          textAlign: TextAlign.center,
                                          maxLines: 3));
                                },
                              );
                        }),
                      ),
                      Align(
                        alignment: Alignment.centerLeft,
                        child: TextButton(
                          onPressed: () => context.nav
                              .pushNamed(Routes.recoverPassWordStageOne),
                          child: Text('هل نسيت كلمة المرور؟',
                              style: TextStyle(
                                  fontSize: 11.sp,
                                  fontWeight: FontWeight.w700,
                                  color: const Color(0xFF028090))),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              SizedBox(height: 16.h),
              Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                Text('ليس لديك حساب؟ ',
                    style: TextStyle(
                        fontSize: 12.sp, color: const Color(0xFF64748B))),
                InkWell(
                  onTap: () => context.nav.pushNamed(Routes.signUpScreen),
                  child: Text('إنشاء حساب جديد',
                      style: TextStyle(
                          fontSize: 12.sp,
                          fontWeight: FontWeight.w900,
                          color: const Color(0xFF028090))),
                ),
              ]),
              SizedBox(height: 24.h),
            ],
          ),
        ),
      ),
    );
  }
}
