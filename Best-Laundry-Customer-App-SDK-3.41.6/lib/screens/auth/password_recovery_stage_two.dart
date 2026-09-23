import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';
import 'package:pin_code_fields/pin_code_fields.dart';

// ignore: must_be_immutable
class RecoverPasswordStageTwo extends StatelessWidget {
  final formKey = GlobalKey<FormState>();
  TextEditingController textEditingController = TextEditingController();
  final String forEmailorPhone;
  RecoverPasswordStageTwo({super.key, required this.forEmailorPhone});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: EdgeInsets.symmetric(horizontal: 20.w),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(height: 14.h),
                Row(children: [
                  InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFFE2E8F0))), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Color(0xFF028090)))),
                  const Spacer(),
                  Image.asset('assets/images/logo.png', height: 36.h),
                ]),
                SizedBox(height: 22.h),
                Text('أدخل رمز التحقق', style: TextStyle(fontSize: 22.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
                SizedBox(height: 6.h),
                Text('تم إرسال رمز مكون من 4 أرقام إلى $forEmailorPhone داخل التطبيق', style: TextStyle(fontSize: 12.5.sp, color: const Color(0xFF64748B), height: 1.4)),
                SizedBox(height: 20.h),
                Container(
                  padding: EdgeInsets.all(20.w),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 18, offset: const Offset(0, 8))]),
                  child: Column(
                    children: [
                      Form(
                        key: formKey,
                        child: PinCodeTextField(
                          appContext: context,
                          length: 4,
                          hintCharacter: '—',
                          animationType: AnimationType.fade,
                          validator: (v) => null,
                          pinTheme: PinTheme(
                            shape: PinCodeFieldShape.box,
                            borderRadius: BorderRadius.circular(14.r),
                            fieldHeight: 54.w,
                            fieldWidth: 62.w,
                            activeFillColor: const Color(0xFFF8FAFC),
                            inactiveFillColor: const Color(0xFFF8FAFC),
                            selectedFillColor: Colors.white,
                            activeColor: const Color(0xFF00A896),
                            selectedColor: const Color(0xFF028090),
                            inactiveColor: const Color(0xFFE2E8F0),
                          ),
                          cursorColor: const Color(0xFF028090),
                          enableActiveFill: true,
                          animationDuration: const Duration(milliseconds: 250),
                          controller: textEditingController,
                          keyboardType: TextInputType.number,
                          onCompleted: (v) {},
                          onChanged: (v) {},
                        ),
                      ),
                      SizedBox(height: 6.h),
                      Consumer(builder: (context, ref, _) {
                        final int time = ref.watch(forgotPassTimerProvider);
                        return Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                          if (time > 0)
                            Container(
                              padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h),
                              decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(20.r)),
                              child: Text('إعادة الإرسال خلال ${time > 9 ? time : '0$time'} ث', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w700, color: const Color(0xFF028090))),
                            )
                          else
                            const SizedBox(),
                          if (time <= 0)
                            ref.watch(forgotPassProvider).maybeMap(
                                  orElse: () => const SizedBox(),
                                  initial: (_) => InkWell(
                                    onTap: () async {
                                      await ref.read(forgotPassProvider.notifier).forgotPassword(forEmailorPhone);
                                      ref.read(forgotPassProvider).maybeWhen(orElse: () {}, loaded: (_) => ref.read(forgotPassTimerProvider.notifier).startTimer());
                                    },
                                    child: Container(
                                      padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 8.h),
                                      decoration: BoxDecoration(color: const Color(0xFFFFF7ED), borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFF59E0B).withOpacity(0.3))),
                                      child: Text(S.of(context).rsndotp, style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w900, color: const Color(0xFFF59E0B))),
                                    ),
                                  ),
                                  loading: (_) => SizedBox(height: 14.h, width: 14.w, child: const CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090))),
                                  error: (_) => const SizedBox(),
                                )
                          else
                            const SizedBox(),
                        ]);
                      }),
                      SizedBox(height: 20.h),
                      SizedBox(
                        height: 50.h, width: double.infinity,
                        child: Consumer(builder: (context, ref, _) {
                          return ref.watch(forgotPassOtpVerificationProvider).map(
                                error: (_) {
                                  Future.delayed(transissionDuration).then((_) => ref.refresh(forgotPassOtpVerificationProvider));
                                  return Container(padding: EdgeInsets.all(12.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center));
                                },
                                loaded: (_) {
                                  Future.delayed(transissionDuration).then((_) {
                                    ref.refresh(forgotPassOtpVerificationProvider);
                                    Future.delayed(buildDuration).then((_) => context.nav.pushNamed(Routes.recoverPassWordStageThree, arguments: _.data.data!.token));
                                  });
                                  return Container(alignment: Alignment.center, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r)), child: Text(S.of(context).scs, style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090))));
                                },
                                initial: (_) => DecoratedBox(
                                  decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.28), blurRadius: 12, offset: const Offset(0, 6))]),
                                  child: ElevatedButton(
                                    onPressed: () => ref.read(forgotPassOtpVerificationProvider.notifier).verifyOtp(forEmailorPhone, textEditingController.text),
                                    style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))),
                                    child: Text(S.of(context).vrfyotp, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                                  ),
                                ),
                                loading: (_) => const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                              );
                        }),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: 24.h),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
