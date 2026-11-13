import 'package:flutter/material.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

/// Modern forgot-password screen matching the app's visual identity:
/// gradient hero with a friendly lock illustration, rounded card,
/// teal-gradient CTA.
class RecoverPasswordStageOne extends ConsumerWidget {
  final FocusNode fNode = FocusNode();
  final GlobalKey<FormBuilderState> _formkey = GlobalKey<FormBuilderState>();
  String email = '';

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // ===== Gradient hero =====
              Container(
                padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 26.h),
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFF028090), Color(0xFF00A896)],
                    begin: Alignment.topRight,
                    end: Alignment.bottomLeft,
                  ),
                  borderRadius:
                      BorderRadius.vertical(bottom: Radius.circular(28)),
                ),
                child: Column(children: [
                  SizedBox(height: 14.h),
                  Row(children: [
                    InkWell(
                      borderRadius: BorderRadius.circular(12.r),
                      onTap: () => context.nav.pop(),
                      child: Container(
                          width: 38.w,
                          height: 38.h,
                          decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.16),
                              borderRadius: BorderRadius.circular(12.r),
                              border: Border.all(
                                  color: Colors.white.withOpacity(0.22))),
                          child: const Icon(Icons.arrow_forward_rounded,
                              size: 18, color: Colors.white)),
                    ),
                    const Spacer(),
                    Image.asset('assets/images/app_icon.png',
                        height: 36.h),
                  ]),
                  SizedBox(height: 22.h),
                  // Friendly lock art built with coins (no image assets)
                  Container(
                    width: 88.w,
                    height: 88.w,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.16),
                      shape: BoxShape.circle,
                      boxShadow: const [
                        BoxShadow(
                          color: Color(0x5500474A),
                          blurRadius: 18,
                          offset: Offset(0, 6),
                        ),
                      ],
                    ),
                    child: Icon(Icons.lock_reset_rounded,
                        size: 44.sp, color: Colors.white),
                  ),
                  SizedBox(height: 14.h),
                  Text('استعادة كلمة المرور',
                      style: TextStyle(
                          fontSize: 20.sp,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                          fontFamily: 'Cairo')),
                  SizedBox(height: 6.h),
                  Text('أدخل رقم جوالك وسنرسل لك رمزاً للمتابعة',
                      style: TextStyle(
                          fontSize: 12.sp,
                          fontWeight: FontWeight.w600,
                          color: Colors.white.withOpacity(0.92),
                          height: 1.4,
                          fontFamily: 'Cairo')),
                ]),
              ),

              Padding(
                padding: EdgeInsets.fromLTRB(20.w, 20.h, 20.w, 24.h),
                child: FormBuilder(
                  key: _formkey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // ===== Form card =====
                      Container(
                        padding: EdgeInsets.all(20.w),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(24.r),
                          border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)),
                          boxShadow: [
                            BoxShadow(
                                color: const Color(0xFF0B1E2E).withOpacity(0.06),
                                blurRadius: 18,
                                offset: const Offset(0, 8))
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text('رقم الجوال',
                                style: TextStyle(
                                    fontSize: 12.sp,
                                    fontWeight: FontWeight.w800,
                                    color: const Color(0xFF1E293B))),
                            SizedBox(height: 8.h),
                            FormBuilderTextField(
                              focusNode: fNode,
                              name: 'email',
                              decoration: InputDecoration(
                                hintText: '05xxxxxxxx',
                                hintStyle: TextStyle(
                                    fontSize: 12.sp,
                                    color: const Color(0xFF94A3B8)),
                                prefixIcon: Icon(Icons.phone_rounded,
                                    size: 18.sp, color: const Color(0xFF028090)),
                                filled: true,
                                fillColor: const Color(0xFFF8FAFC),
                                contentPadding: EdgeInsets.symmetric(
                                    horizontal: 14.w, vertical: 14.h),
                                border: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16.r),
                                    borderSide:
                                        const BorderSide(color: Color(0xFFE2E8F0))),
                                enabledBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16.r),
                                    borderSide:
                                        const BorderSide(color: Color(0xFFE2E8F0))),
                                focusedBorder: OutlineInputBorder(
                                    borderRadius: BorderRadius.circular(16.r),
                                    borderSide: const BorderSide(
                                        color: Color(0xFF00A896), width: 1.4)),
                              ),
                              keyboardType: TextInputType.phone,
                              textInputAction: TextInputAction.done,
                              textDirection: TextDirection.ltr,
                              style: TextStyle(
                                fontFamily: 'Cairo',
                                color: const Color(0xFF0B1E2E),
                                fontWeight: FontWeight.w700,
                              ),
                              validator: FormBuilderValidators.compose([
                                FormBuilderValidators.required(
                                    errorText: 'رقم الجوال مطلوب'),
                                FormBuilderValidators.numeric(
                                    errorText: 'أرقام فقط'),
                                FormBuilderValidators.minLength(9,
                                    errorText: 'قصير جداً'),
                                FormBuilderValidators.maxLength(12,
                                    errorText: 'طويل جداً'),
                              ]),
                            ),
                            SizedBox(height: 20.h),
                            SizedBox(
                              height: ref.watch(forgotPassProvider).maybeWhen(
                                loaded: (result) =>
                                    result.requiresOtp ? 50.h : null,
                                orElse: () => 50.h,
                              ),
                              width: double.infinity,
                              child: Consumer(builder: (context, ref, _) {
                                return ref.watch(forgotPassProvider).map(
                                      initial: (_) => DecoratedBox(
                                        decoration: BoxDecoration(
                                          gradient: const LinearGradient(
                                              colors: [
                                                Color(0xFF028090),
                                                Color(0xFF00A896)
                                              ],
                                              begin: Alignment.topRight,
                                              end: Alignment.bottomLeft),
                                          borderRadius:
                                              BorderRadius.circular(16.r),
                                          boxShadow: [
                                            BoxShadow(
                                                color: const Color(0xFF00A896)
                                                    .withOpacity(0.28),
                                                blurRadius: 12,
                                                offset: const Offset(0, 6))
                                          ]),
                                        child: ElevatedButton(
                                          onPressed: () {
                                            if (fNode.hasFocus) fNode.unfocus();
                                            if (_formkey.currentState != null &&
                                                _formkey.currentState!
                                                    .saveAndValidate()) {
                                              email = _formkey.currentState!
                                                  .fields['email']!.value as String;
                                              ref
                                                  .read(forgotPassProvider.notifier)
                                                  .forgotPassword(email);
                                            }
                                          },
                                          style: ElevatedButton.styleFrom(
                                              backgroundColor: Colors.transparent,
                                              shadowColor: Colors.transparent,
                                              shape: RoundedRectangleBorder(
                                                  borderRadius:
                                                      BorderRadius.circular(16.r))),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.center,
                                            children: const [
                                              Icon(Icons.mark_email_read_rounded,
                                                  size: 20, color: Colors.white),
                                              SizedBox(width: 10),
                                              Text('إرسال الرمز',
                                                  style: TextStyle(
                                                      fontSize: 15,
                                                      fontWeight: FontWeight.w900,
                                                      color: Colors.white,
                                                      fontFamily: 'Cairo')),
                                            ],
                                          ),
                                        ),
                                      ),
                                      error: (_) {
                                        Future.delayed(transissionDuration).then(
                                            (_) => ref.refresh(forgotPassProvider));
                                        return Container(
                                            padding: EdgeInsets.all(12.w),
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
                                                textAlign: TextAlign.center));
                                      },
                                      loaded: (_) {
                                        if (!_.data.requiresOtp) {
                                          return Padding(
                                            padding: const EdgeInsets.all(12),
                                            child: Column(children: [Text(_.data.message.isNotEmpty
                                                ? _.data.message
                                                : 'تواصل مع الدعم لاستعادة حسابك.',
                                              textDirection: TextDirection.rtl,
                                              textAlign: TextAlign.center),
                                              SizedBox(height: 12.h),
                                              SizedBox(
                                                height: 44.h,
                                                child: TextButton.icon(onPressed: () => context.nav.pushNamed(Routes.contactUsScreen),
                                                  icon: const Icon(Icons.support_agent_rounded, color: Color(0xFF028090)),
                                                  label: const Text('التواصل مع الدعم', style: TextStyle(fontWeight: FontWeight.w800, color: Color(0xFF028090))),
                                                ),
                                              ),
                                              TextButton(onPressed: () => ref.invalidate(forgotPassProvider),
                                                child: const Text('إعادة المحاولة')),
                                            ]),
                                          );
                                        }
                                        final String otp =
                                            _.data.otp;
                                        final bool isOtp = otp.isNotEmpty &&
                                            otp != 'Success' &&
                                            RegExp(r'^\d+$').hasMatch(otp);
                                        if (isOtp) {
                                          WidgetsBinding.instance
                                              .addPostFrameCallback((_) {
                                            final m = ScaffoldMessenger.of(context);
                                            m.clearMaterialBanners();
                                            m.showMaterialBanner(MaterialBanner(
                                              backgroundColor:
                                                  const Color(0xFF028090),
                                              leading: const Icon(
                                                  Icons.mark_chat_read_rounded,
                                                  color: Colors.white),
                                              content: Text(
                                                  'رمز التحقق: $otp  •  داخل التطبيق مجاناً',
                                                  style: const TextStyle(
                                                      color: Colors.white,
                                                      fontWeight: FontWeight.w800,
                                                      fontFamily: 'Cairo')),
                                              actions: [
                                                TextButton(
                                                    onPressed: () =>
                                                        m.clearMaterialBanners(),
                                                    child: const Text('إخفاء',
                                                        style: TextStyle(
                                                            color: Colors.white)))
                                              ],
                                            ));
                                            Future.delayed(
                                                const Duration(seconds: 6), () {
                                              try {
                                                m.clearMaterialBanners();
                                              } catch (_) {}
                                            });
                                          });
                                        }
                                        Future.delayed(transissionDuration)
                                            .then((_) {
                                          ref
                                              .watch(
                                                  forgotPassTimerProvider.notifier)
                                              .startTimer();
                                          Future.delayed(buildDuration).then((_) =>
                                              context.nav.pushNamed(
                                                  Routes.recoverPassWordStageTwo,
                                                  arguments: email));
                                        });
                                        return Column(children: [
                                          Container(
                                              padding: EdgeInsets.symmetric(
                                                  vertical: 14.h),
                                              alignment: Alignment.center,
                                              decoration: BoxDecoration(
                                                  color: const Color(0xFFEFFAF8),
                                                  borderRadius:
                                                      BorderRadius.circular(12.r)),
                                              child: Text(
                                                  isOtp
                                                      ? 'تم إرسال الرمز داخل التطبيق'
                                                      : S.of(context).scs,
                                                  style: TextStyle(
                                                      fontSize: 12.sp,
                                                      fontWeight: FontWeight.w800,
                                                      color: const Color(
                                                          0xFF028090)))),
                                          if (isOtp)
                                            Container(
                                              margin: EdgeInsets.only(top: 10.h),
                                              padding: EdgeInsets.symmetric(
                                                  horizontal: 14.w, vertical: 10.h),
                                              decoration: BoxDecoration(
                                                  color: const Color(0xFFEFFAF8),
                                                  borderRadius:
                                                      BorderRadius.circular(12.r),
                                                  border: Border.all(
                                                      color: const Color(0xFF00A896)
                                                          .withOpacity(0.22))),
                                              child: Row(
                                                  mainAxisAlignment:
                                                      MainAxisAlignment.center,
                                                  children: [
                                                    Icon(Icons.verified_rounded,
                                                        size: 18.sp,
                                                        color: const Color(
                                                            0xFF028090)),
                                                    SizedBox(width: 6.w),
                                                    Text('الرمز: $otp',
                                                        style: TextStyle(
                                                            color: const Color(
                                                                0xFF028090),
                                                            fontWeight:
                                                                FontWeight.w900,
                                                            fontSize: 18.sp))
                                                  ]),
                                            ),
                                        ]);
                                      },
                                      loading: (_) => const Center(
                                          child: SizedBox(
                                              width: 22,
                                              height: 22,
                                              child: CircularProgressIndicator(
                                                  strokeWidth: 2,
                                                  color: Color(0xFF028090)))),
                                    );
                              }),
                            ),
                            SizedBox(height: 12.h),
                            // ===== Contact support hint =====
                            Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.support_agent_rounded,
                                    size: 16.sp,
                                    color: const Color(0xFF94A3B8)),
                                SizedBox(width: 6.w),
                                Text(
                                  'مشكلة في الاستعادة؟ ',
                                  style: TextStyle(
                                      fontSize: 11.sp,
                                      color: const Color(0xFF64748B),
                                      fontFamily: 'Cairo'),
                                ),
                                InkWell(
                                  onTap: () => context.nav
                                      .pushNamed(Routes.contactUsScreen),
                                  child: Text(
                                    'تواصل مع الدعم',
                                    style: TextStyle(
                                        fontSize: 11.sp,
                                        fontWeight: FontWeight.w900,
                                        color: const Color(0xFF028090),
                                        fontFamily: 'Cairo',
                                        decoration:
                                            TextDecoration.underline),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      SizedBox(height: 12.h),
                      Text(
                        'بإتمامك هذه الخطوة فأنت توافق على',
                        style: TextStyle(
                            fontSize: 11.sp, color: const Color(0xFF94A3B8)),
                      ),
                      SizedBox(height: 4.h),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            'استعادة حسابك وإعادة تعيين كلمة المرور',
                            style: TextStyle(
                                fontSize: 11.sp,
                                fontWeight: FontWeight.w700,
                                color: const Color(0xFF1E293B)),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
