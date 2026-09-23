import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class SignUpScreen extends StatefulWidget {
  const SignUpScreen({super.key});
  @override
  State<SignUpScreen> createState() => _SignUpScreenState();
}

class _SignUpScreenState extends State<SignUpScreen> {
  final List<FocusNode> fNodes = [FocusNode(), FocusNode(), FocusNode(), FocusNode(), FocusNode(), FocusNode()];
  final GlobalKey<FormBuilderState> _formkey = GlobalKey<FormBuilderState>();
  bool obsecureText = true;
  bool obsecureTextTwo = true;
  bool shouldRemember = false;

  String validationError({required String fieldName}) => 'هذا الحقل مطلوب';

  InputDecoration _dec({required String hint, Widget? prefix, Widget? suffix}) => InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
        prefixIcon: prefix,
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
          child: Column(
            children: [
              SizedBox(height: 14.h),
              Padding(
                padding: EdgeInsets.symmetric(horizontal: 16.w),
                child: Row(children: [
                  InkWell(
                    borderRadius: BorderRadius.circular(12.r),
                    onTap: () => Navigator.pop(context),
                    child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFFE2E8F0))), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Color(0xFF028090))),
                  ),
                  const Spacer(),
                  Hero(tag: 'logo', child: Image.asset('assets/images/logo.png', height: 42.h)),
                ]),
              ),
              SizedBox(height: 14.h),
              Text('إنشاء حساب جديد', style: TextStyle(fontSize: 22.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
              SizedBox(height: 4.h),
              Text('أدخل بياناتك وسيصلك الرمز داخل التطبيق مجاناً', style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B)), textAlign: TextAlign.center),
              SizedBox(height: 16.h),
              Container(
                margin: EdgeInsets.symmetric(horizontal: 20.w),
                padding: EdgeInsets.all(20.w),
                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 18, offset: const Offset(0, 8))]),
                child: FormBuilder(
                  key: _formkey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('الاسم الكامل', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(focusNode: fNodes[0], name: 'first_name', decoration: _dec(hint: 'الاسم الكامل', prefix: Icon(Icons.person_outline_rounded, size: 18.sp, color: const Color(0xFF028090))), keyboardType: TextInputType.name, textInputAction: TextInputAction.next, validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: validationError(fieldName: 'الاسم'))])),
                      SizedBox(height: 14.h),
                      Text('رقم الجوال', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(focusNode: fNodes[3], name: 'mobile', decoration: _dec(hint: '05xxxxxxxx', prefix: Icon(Icons.phone_rounded, size: 18.sp, color: const Color(0xFF028090))), keyboardType: TextInputType.phone, textInputAction: TextInputAction.next, validator: FormBuilderValidators.compose([FormBuilderValidators.numeric(errorText: 'أرقام فقط'), FormBuilderValidators.required(errorText: validationError(fieldName: 'الجوال')), FormBuilderValidators.maxLength(12, errorText: 'أقصى 12'), FormBuilderValidators.minLength(10, errorText: 'أدنى 10')])),
                      SizedBox(height: 14.h),
                      Text('كلمة المرور', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(focusNode: fNodes[4], name: 'password', obscureText: obsecureText, decoration: _dec(hint: '••••••••', prefix: Icon(Icons.lock_outline_rounded, size: 18.sp, color: const Color(0xFF028090)), suffix: InkWell(onTap: () => setState(() => obsecureText = !obsecureText), child: Icon(obsecureText ? Icons.visibility_off_outlined : Icons.visibility_outlined, size: 18.sp, color: const Color(0xFF64748B)))), keyboardType: TextInputType.text, textInputAction: TextInputAction.next, validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: validationError(fieldName: 'كلمة المرور'))])),
                      SizedBox(height: 14.h),
                      Text('تأكيد كلمة المرور', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(focusNode: fNodes[5], name: 'password_confirmation', obscureText: obsecureTextTwo, decoration: _dec(hint: '••••••••', prefix: Icon(Icons.lock_outline_rounded, size: 18.sp, color: const Color(0xFF028090)), suffix: InkWell(onTap: () => setState(() => obsecureTextTwo = !obsecureTextTwo), child: Icon(obsecureTextTwo ? Icons.visibility_off_outlined : Icons.visibility_outlined, size: 18.sp, color: const Color(0xFF64748B)))), keyboardType: TextInputType.text, textInputAction: TextInputAction.done, validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: validationError(fieldName: 'التأكيد'))])),
                      SizedBox(height: 14.h),
                      Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        InkWell(onTap: () => setState(() => shouldRemember = !shouldRemember), child: SvgPicture.asset(shouldRemember ? 'assets/svgs/icon_selection_ticked.svg' : 'assets/svgs/icon_selection_unticked.svg', height: 18.h, width: 18.w)),
                        SizedBox(width: 8.w),
                        Expanded(
                          child: Wrap(children: [
                            Text('أوافق على ', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF475569))),
                            InkWell(onTap: () => _showTos(context, true), child: Text('الشروط والأحكام ', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090)))),
                            Text('و ', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF475569))),
                            InkWell(onTap: () => _showTos(context, false), child: Text('سياسة الخصوصية', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090)))),
                          ]),
                        ),
                      ]),
                      SizedBox(height: 18.h),
                      SizedBox(
                        height: 50.h, width: double.infinity,
                        child: Consumer(builder: (context, ref, _) {
                          return ref.watch(registrationProvider).map(
                                initial: (_) => DecoratedBox(
                                  decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.28), blurRadius: 12, offset: const Offset(0, 6))]),
                                  child: ElevatedButton(
                                    onPressed: () {
                                      for (final n in fNodes) { if (n.hasFocus) n.unfocus(); }
                                      if (_formkey.currentState != null && _formkey.currentState!.saveAndValidate()) {
                                        if (shouldRemember) {
                                          ref.read(registrationProvider.notifier).register(_formkey.currentState!.value);
                                        } else {
                                          EasyLoading.showError('يجب الموافقة على الشروط');
                                        }
                                      }
                                    },
                                    style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))),
                                    child: Text('إنشاء حساب', style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                                  ),
                                ),
                                loading: (_) => const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                                loaded: (_) {
                                  final Box box = Hive.box(AppHSC.authBox);
                                  final Box userBox = Hive.box(AppHSC.userBox);
                                  box.putAll(_.data.data!.access!.toMap());
                                  userBox.putAll(_.data.data!.user!.toMap());
                                  Future.delayed(transissionDuration).then((_) {
                                    ref.refresh(registrationProvider);
                                    Future.delayed(buildDuration).then((_) => context.nav.pushNamed(Routes.signUpImageUpload));
                                  });
                                  return Container(alignment: Alignment.center, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r)), child: Text('تم إنشاء الحساب بنجاح', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090))));
                                },
                                error: (_) {
                                  Future.delayed(transissionDuration).then((_) => ref.refresh(registrationProvider));
                                  return Container(padding: EdgeInsets.all(10.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFFE63946).withOpacity(0.22))), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center, maxLines: 2));
                                },
                              );
                        }),
                      ),
                    ],
                  ),
                ),
              ),
              SizedBox(height: 16.h),
              Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                Text('لديك حساب بالفعل؟ ', style: TextStyle(fontSize: 12.sp, color: const Color(0xFF64748B))),
                InkWell(onTap: () => context.nav.pushNamed(Routes.loginScreen), child: Text('تسجيل الدخول', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090)))),
              ]),
              SizedBox(height: 24.h),
            ],
          ),
        ),
      ),
    );
  }

  void _showTos(BuildContext context, bool isTos) {
    showDialog(context: context, builder: (c) => Padding(padding: EdgeInsets.all(20.w), child: Consumer(builder: (context, ref, _) {
      final prov = isTos ? ref.watch(tosProvider) : ref.watch(privacyProvider);
      return Container(
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r)),
        child: prov.map(
          initial: (_) => const SizedBox(), loading: (_) => const Center(child: CircularProgressIndicator()),
          loaded: (_) => Padding(padding: EdgeInsets.all(14.w), child: Column(children: [
            Text(_.data.data!.setting!.title!, style: TextStyle(fontSize: 18.sp, fontWeight: FontWeight.w900)),
            SizedBox(height: 12.h),
            Expanded(child: SingleChildScrollView(child: Html(data: _.data.data!.setting!.content!))),
            SizedBox(height: 12.h),
            SizedBox(width: double.infinity, child: ElevatedButton(onPressed: () => Navigator.pop(context), style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF028090), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))), child: const Text('حسناً', style: TextStyle(color: Colors.white)))),
          ])),
          error: (_) => Center(child: Text(_.error)),
        ),
      );
    })));
  }
}
