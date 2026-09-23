import 'package:flutter/material.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

// ignore: must_be_immutable
class RecoverPasswordStageOne extends StatelessWidget {
  final FocusNode fNode = FocusNode();
  final GlobalKey<FormBuilderState> _formkey = GlobalKey<FormBuilderState>();
  String email = '';

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: EdgeInsets.symmetric(horizontal: 20.w),
            child: FormBuilder(
              key: _formkey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  SizedBox(height: 14.h),
                  Row(children: [
                    InkWell(
                      borderRadius: BorderRadius.circular(12.r),
                      onTap: () => context.nav.pop(),
                      child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFFE2E8F0))), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Color(0xFF028090))),
                    ),
                    const Spacer(),
                    Image.asset('assets/images/logo.png', height: 36.h),
                  ]),
                  SizedBox(height: 22.h),
                  Text('استعادة كلمة المرور', style: TextStyle(fontSize: 22.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
                  SizedBox(height: 6.h),
                  Text('أدخل رقم جوالك لإرسال رمز التحقق داخل التطبيق مجاناً', style: TextStyle(fontSize: 12.5.sp, fontWeight: FontWeight.w600, color: const Color(0xFF64748B), height: 1.4)),
                  SizedBox(height: 20.h),
                  Container(
                    padding: EdgeInsets.all(20.w),
                    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 18, offset: const Offset(0, 8))]),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('رقم الجوال', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                        SizedBox(height: 8.h),
                        FormBuilderTextField(
                          focusNode: fNode,
                          name: 'email',
                          decoration: InputDecoration(
                            hintText: '05xxxxxxxx',
                            hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
                            prefixIcon: Icon(Icons.phone_rounded, size: 18.sp, color: const Color(0xFF028090)),
                            filled: true, fillColor: const Color(0xFFF8FAFC),
                            contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 14.h),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
                            enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
                            focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFF00A896), width: 1.4)),
                          ),
                          keyboardType: TextInputType.phone,
                          textInputAction: TextInputAction.done,
                          validator: FormBuilderValidators.compose([
                            FormBuilderValidators.required(errorText: 'رقم الجوال مطلوب'),
                            FormBuilderValidators.numeric(errorText: 'أرقام فقط'),
                            FormBuilderValidators.minLength(9, errorText: 'قصير جداً'),
                            FormBuilderValidators.maxLength(12, errorText: 'طويل جداً'),
                          ]),
                        ),
                        SizedBox(height: 20.h),
                        SizedBox(
                          height: 50.h, width: double.infinity,
                          child: Consumer(builder: (context, ref, _) {
                            return ref.watch(forgotPassProvider).map(
                                  initial: (_) => DecoratedBox(
                                    decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.28), blurRadius: 12, offset: const Offset(0, 6))]),
                                    child: ElevatedButton(
                                      onPressed: () {
                                        if (fNode.hasFocus) fNode.unfocus();
                                        if (_formkey.currentState != null && _formkey.currentState!.saveAndValidate()) {
                                          email = _formkey.currentState!.fields['email']!.value as String;
                                          ref.read(forgotPassProvider.notifier).forgotPassword(email);
                                        }
                                      },
                                      style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))),
                                      child: Text(S.of(context).sndotp, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                                    ),
                                  ),
                                  error: (_) {
                                    Future.delayed(transissionDuration).then((_) => ref.refresh(forgotPassProvider));
                                    return Container(padding: EdgeInsets.all(12.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFFE63946).withOpacity(0.22))), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center));
                                  },
                                  loaded: (_) {
                                    final String otp = (_.data ?? '').toString();
                                    final bool isOtp = otp.isNotEmpty && otp != 'Success' && RegExp(r'^\d+$').hasMatch(otp);
                                    if (isOtp) {
                                      WidgetsBinding.instance.addPostFrameCallback((_) {
                                        final m = ScaffoldMessenger.of(context);
                                        m.clearMaterialBanners();
                                        m.showMaterialBanner(MaterialBanner(
                                          backgroundColor: const Color(0xFF028090),
                                          leading: const Icon(Icons.mark_chat_read_rounded, color: Colors.white),
                                          content: Text('رمز التحقق: $otp  •  داخل التطبيق مجاناً', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontFamily: 'Cairo')),
                                          actions: [TextButton(onPressed: () => m.clearMaterialBanners(), child: const Text('إخفاء', style: TextStyle(color: Colors.white)))],
                                        ));
                                        Future.delayed(const Duration(seconds: 6), () { try { m.clearMaterialBanners(); } catch (_) {} });
                                      });
                                    }
                                    Future.delayed(transissionDuration).then((_) {
                                      ref.watch(forgotPassTimerProvider.notifier).startTimer();
                                      Future.delayed(buildDuration).then((_) => context.nav.pushNamed(Routes.recoverPassWordStageTwo, arguments: email));
                                    });
                                    return Column(children: [
                                      Container(padding: EdgeInsets.symmetric(vertical: 14.h), alignment: Alignment.center, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r)), child: Text(isOtp ? 'تم إرسال الرمز داخل التطبيق' : S.of(context).scs, style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))),
                                      if (isOtp)
                                        Container(
                                          margin: EdgeInsets.only(top: 10.h), padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 10.h),
                                          decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFF00A896).withOpacity(0.22))),
                                          child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.verified_rounded, size: 18.sp, color: const Color(0xFF028090)), SizedBox(width: 6.w), Text('الرمز: $otp', style: TextStyle(color: const Color(0xFF028090), fontWeight: FontWeight.w900, fontSize: 18.sp))]),
                                        ),
                                    ]);
                                  },
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
      ),
    );
  }
}
