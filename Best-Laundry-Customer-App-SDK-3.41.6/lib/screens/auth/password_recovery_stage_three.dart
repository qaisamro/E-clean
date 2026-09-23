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

class RecoverPasswordStageThree extends StatefulWidget {
  const RecoverPasswordStageThree({super.key, required this.token});
  final String token;
  @override
  State<RecoverPasswordStageThree> createState() => _RecoverPasswordStageThreeState();
}

class _RecoverPasswordStageThreeState extends State<RecoverPasswordStageThree> {
  final List<FocusNode> fNodes = [FocusNode(), FocusNode()];
  bool obsecureTextOne = true;
  bool obsecureTextTwo = true;
  final GlobalKey<FormBuilderState> _formkey = GlobalKey<FormBuilderState>();

  InputDecoration _dec({required String hint, Widget? suffix}) => InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
        prefixIcon: Icon(Icons.lock_outline_rounded, size: 18.sp, color: const Color(0xFF028090)),
        suffixIcon: suffix,
        filled: true, fillColor: const Color(0xFFF8FAFC),
        contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 14.h),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFF00A896), width: 1.4)),
      );

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
                    InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFFE2E8F0))), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Color(0xFF028090)))),
                    const Spacer(),
                    Image.asset('assets/images/logo.png', height: 36.h),
                  ]),
                  SizedBox(height: 22.h),
                  Text('إنشاء كلمة مرور جديدة', style: TextStyle(fontSize: 22.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
                  SizedBox(height: 6.h),
                  Text('أدخل كلمة المرور الجديدة وستُحفظ داخل التطبيق', style: TextStyle(fontSize: 12.5.sp, color: const Color(0xFF64748B))),
                  SizedBox(height: 20.h),
                  Container(
                    padding: EdgeInsets.all(20.w),
                    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 18, offset: const Offset(0, 8))]),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('كلمة المرور الجديدة', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                        SizedBox(height: 8.h),
                        FormBuilderTextField(
                          focusNode: fNodes[0], name: 'password', obscureText: obsecureTextOne,
                          decoration: _dec(hint: '••••••••', suffix: InkWell(onTap: () => setState(() => obsecureTextOne = !obsecureTextOne), child: Icon(obsecureTextOne ? Icons.visibility_outlined : Icons.visibility_off_outlined, size: 18.sp, color: const Color(0xFF64748B)))),
                          keyboardType: TextInputType.text, textInputAction: TextInputAction.next,
                          validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: 'مطلوب')]),
                        ),
                        SizedBox(height: 14.h),
                        Text('تأكيد كلمة المرور', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                        SizedBox(height: 8.h),
                        FormBuilderTextField(
                          focusNode: fNodes[1], name: 'password2', obscureText: obsecureTextTwo,
                          decoration: _dec(hint: '••••••••', suffix: InkWell(onTap: () => setState(() => obsecureTextTwo = !obsecureTextTwo), child: Icon(obsecureTextTwo ? Icons.visibility_outlined : Icons.visibility_off_outlined, size: 18.sp, color: const Color(0xFF64748B)))),
                          keyboardType: TextInputType.text, textInputAction: TextInputAction.done,
                          validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: 'مطلوب')]),
                        ),
                        SizedBox(height: 20.h),
                        SizedBox(
                          height: 50.h, width: double.infinity,
                          child: Consumer(builder: (context, ref, _) {
                            return ref.watch(forgotPassResetPassProvider).map(
                                  error: (_) {
                                    Future.delayed(transissionDuration).then((_) => ref.refresh(forgotPassResetPassProvider));
                                    return Container(padding: EdgeInsets.all(12.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center));
                                  },
                                  initial: (_) => DecoratedBox(
                                    decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.28), blurRadius: 12, offset: const Offset(0, 6))]),
                                    child: ElevatedButton(
                                      onPressed: () {
                                        for (final n in fNodes) { if (n.hasFocus) n.unfocus(); }
                                        if (_formkey.currentState != null && _formkey.currentState!.saveAndValidate()) {
                                          final f = _formkey.currentState!.fields;
                                          ref.read(forgotPassResetPassProvider.notifier).resetPassword(f['password']!.value as String, f['password2']!.value as String, widget.token);
                                        }
                                      },
                                      style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))),
                                      child: Text(S.of(context).rstpswrd, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                                    ),
                                  ),
                                  loaded: (_) {
                                    Future.delayed(transissionDuration).then((_) {
                                      ref.refresh(forgotPassResetPassProvider);
                                      Future.delayed(buildDuration).then((_) => context.nav.pushNamedAndRemoveUntil(Routes.loginScreen, (r) => false));
                                    });
                                    return Container(alignment: Alignment.center, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r)), child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.check_circle_rounded, size: 18.sp, color: const Color(0xFF028090)), SizedBox(width: 6.w), Text('تم التحديث — سجّل دخولك الآن', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))]));
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
