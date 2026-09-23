import 'package:flutter/material.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/providers/profile_update_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});
  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final List<FocusNode> fNodes = [FocusNode(), FocusNode()];
  final GlobalKey<FormBuilderState> _formkey = GlobalKey<FormBuilderState>();
  final TextEditingController textEditingController = TextEditingController();
  bool obsecureText = true;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: EdgeInsets.zero,
          child: Column(
            children: [
              SizedBox(height: 18.h),
              Align(
                alignment: Alignment.centerRight,
                child: Padding(
                  padding: EdgeInsets.only(right: 16.w),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(12.r),
                    onTap: () => Navigator.pop(context),
                    child: Container(
                      width: 38.w, height: 38.h,
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFFE2E8F0))),
                      child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Color(0xFF028090)),
                    ),
                  ),
                ),
              ),
              SizedBox(height: 8.h),
              Hero(tag: 'logo', child: Image.asset('assets/images/logo.png', height: 78.h, width: 155.w, fit: BoxFit.contain)),
              SizedBox(height: 14.h),
              Text('مرحباً بعودتك', style: TextStyle(fontSize: 22.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
              SizedBox(height: 4.h),
              Text('سجّل دخولك برقم جوالك للمتابعة', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w600, color: const Color(0xFF64748B))),
              SizedBox(height: 20.h),
              Container(
                margin: EdgeInsets.symmetric(horizontal: 20.w),
                padding: EdgeInsets.all(20.w),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(24.r),
                  border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)),
                  boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 18, offset: const Offset(0, 8))],
                ),
                child: FormBuilder(
                  key: _formkey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('رقم الجوال', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(
                        controller: textEditingController,
                        focusNode: fNodes[0],
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
                        textInputAction: TextInputAction.next,
                        validator: FormBuilderValidators.compose([
                          FormBuilderValidators.required(errorText: 'رقم الجوال مطلوب'),
                          FormBuilderValidators.numeric(errorText: 'أرقام فقط'),
                          FormBuilderValidators.minLength(9, errorText: 'قصير جداً'),
                          FormBuilderValidators.maxLength(12, errorText: 'طويل جداً'),
                        ]),
                      ),
                      SizedBox(height: 14.h),
                      Text('كلمة المرور', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(
                        focusNode: fNodes[1],
                        name: 'password',
                        obscureText: obsecureText,
                        decoration: InputDecoration(
                          hintText: '••••••••',
                          hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
                          prefixIcon: Icon(Icons.lock_outline_rounded, size: 18.sp, color: const Color(0xFF028090)),
                          suffixIcon: InkWell(
                            borderRadius: BorderRadius.circular(20.r),
                            onTap: () => setState(() => obsecureText = !obsecureText),
                            child: Icon(obsecureText ? Icons.visibility_off_outlined : Icons.visibility_outlined, size: 18.sp, color: const Color(0xFF64748B)),
                          ),
                          filled: true, fillColor: const Color(0xFFF8FAFC),
                          contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 14.h),
                          border: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
                          enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
                          focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFF00A896), width: 1.4)),
                        ),
                        keyboardType: TextInputType.text,
                        textInputAction: TextInputAction.done,
                        validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: 'كلمة المرور مطلوبة')]),
                      ),
                      SizedBox(height: 10.h),
                      Align(
                        alignment: Alignment.centerLeft,
                        child: InkWell(
                          onTap: () => context.nav.pushNamed(Routes.recoverPassWordStageOne),
                          child: Padding(
                            padding: EdgeInsets.symmetric(vertical: 4.h),
                            child: Text('هل نسيت كلمة المرور؟', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090))),
                          ),
                        ),
                      ),
                      SizedBox(height: 18.h),
                      SizedBox(
                        height: 50.h, width: double.infinity,
                        child: Consumer(
                          builder: (context, ref, _) {
                            return ref.watch(loginProvider).map(
                                  error: (e) {
                                    Future.delayed(transissionDuration).then((_) => ref.refresh(loginProvider));
                                    return Container(
                                      padding: EdgeInsets.all(12.w),
                                      decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: const Color(0xFFE63946).withOpacity(0.22))),
                                      child: Text(e.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center, maxLines: 2),
                                    );
                                  },
                                  loaded: (_) {
                                    final Box box = Hive.box(AppHSC.authBox);
                                    final Box userBox = Hive.box(AppHSC.userBox);
                                    box.putAll(_.data.data!.access!.toMap());
                                    userBox.putAll(_.data.data!.user!.toMap());
                                    Future.delayed(transissionDuration).then((_) {
                                      ref.refresh(loginProvider);
                                      ref.refresh(profileInfoProvider);
                                      ref.refresh(addresListProvider);
                                      Future.delayed(buildDuration).then((_) => context.nav.pushNamedAndRemoveUntil(Routes.homeScreen, (r) => false));
                                    });
                                    return Container(
                                      alignment: Alignment.center,
                                      decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r)),
                                      child: Text('تم تسجيل الدخول بنجاح', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090))),
                                    );
                                  },
                                  initial: (_) => DecoratedBox(
                                    decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.28), blurRadius: 12, offset: const Offset(0, 6))]),
                                    child: ElevatedButton(
                                      onPressed: () {
                                        for (final n in fNodes) { if (n.hasFocus) n.unfocus(); }
                                        if (_formkey.currentState != null && _formkey.currentState!.saveAndValidate()) {
                                          final f = _formkey.currentState!.fields;
                                          ref.read(loginProvider.notifier).login(f['email']!.value as String, f['password']!.value as String);
                                        }
                                      },
                                      style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r)), padding: EdgeInsets.zero),
                                      child: Center(child: Text('تسجيل الدخول', style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo'))),
                                    ),
                                  ),
                                  loading: (_) => const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                                );
                          },
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              SizedBox(height: 18.h),
              Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                Text('ليس لديك حساب؟ ', style: TextStyle(fontSize: 12.sp, color: const Color(0xFF64748B))),
                InkWell(onTap: () => context.nav.pushNamed(Routes.signUpScreen), child: Text('إنشاء حساب', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090)))),
              ]),
              SizedBox(height: 24.h),
            ],
          ),
        ),
      ),
    );
  }
}
