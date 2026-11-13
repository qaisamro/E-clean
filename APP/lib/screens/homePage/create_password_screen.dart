import 'package:flutter/material.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:laundry_customer/providers/profile_update_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';

/// شاشة تغيير كلمة المرور — تصميم عربي احترافي بنفس هوية التطبيق الحديثة:
/// ترويسة متدرجة، بطاقة بيضاء مستديرة، مؤشر قوة كلمة المرور
/// ورسائل عربية واضحة.
class CreatePasswordScreen extends ConsumerStatefulWidget {
  const CreatePasswordScreen({super.key});

  @override
  ConsumerState<ConsumerStatefulWidget> createState() =>
      _CreatePasswordScreenState();
}

class _CreatePasswordScreenState extends ConsumerState<CreatePasswordScreen> {
  static const _teal = Color(0xFF028090);
  static const _tealDark = Color(0xFF00A896);
  static const _navy = Color(0xFF0B1E2E);
  static const _slate = Color(0xFF475569);
  static const _gray = Color(0xFF64748B);
  static const _grayLight = Color(0xFF94A3B8);
  static const _border = Color(0xFFE2E8F0);
  static const _field = Color(0xFFF8FAFC);
  static const _red = Color(0xFFE63946);
  static const _redSoft = Color(0xFFFFF1F2);
  static const _green = Color(0xFF16A34A);
  static const _greenSoft = Color(0xFFE7F7EE);
  static const _amber = Color(0xFFF59E0B);
  static const _tipBg = Color(0xFFEFFAF8);

  final _formKey = GlobalKey<FormBuilderState>();

  bool _currentObscure = true;
  bool _newObscure = true;
  bool _confirmObscure = true;

  int _strength = 0;
  String _newPassword = '';
  String _confirmPassword = '';

  static const _strengthLabels = [
    'ابدأ بإدخال كلمة المرور',
    'ضعيفة',
    'متوسطة',
    'قوية',
    'قوية جداً',
  ];

  static const _strengthColors = [
    Color(0xFFE2E8F0),
    _red,
    _amber,
    _tealDark,
    _green,
  ];

  int _strengthScore(String password) {
    if (password.isEmpty) return 0;
    var score = 0;
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (RegExp('[A-Za-z]').hasMatch(password) &&
        RegExp('[0-9]').hasMatch(password)) {
      score++;
    }
    if (RegExp(r'[!@#$%^&*(),.?":{}|<>_\-+=/\\[\]`~;:]').hasMatch(password)) {
      score++;
    }
    return score.clamp(0, 4);
  }

  InputDecoration _fieldDecoration({
    required String hint,
    required IconData icon,
    Widget? suffix,
  }) {
    return InputDecoration(
      hintText: hint,
      hintStyle: TextStyle(fontSize: 12.5.sp, color: _grayLight),
      filled: true,
      fillColor: _field,
      contentPadding:
          EdgeInsets.symmetric(horizontal: 16.w, vertical: 15.h),
      prefixIcon: Icon(icon, size: 20.sp, color: _teal),
      suffixIcon: suffix,
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16.r),
        borderSide: const BorderSide(color: _border),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16.r),
        borderSide: const BorderSide(color: _border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16.r),
        borderSide: const BorderSide(color: _teal, width: 1.6),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16.r),
        borderSide: const BorderSide(color: _red, width: 1.4),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(16.r),
        borderSide: const BorderSide(color: _red, width: 1.6),
      ),
    );
  }

  Widget _eyeToggle({
    required bool obscure,
    required VoidCallback onToggle,
  }) {
    return GestureDetector(
      onTap: onToggle,
      behavior: HitTestBehavior.opaque,
      child: Padding(
        padding: EdgeInsetsDirectional.only(end: 6.w),
        child: Icon(
          obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined,
          size: 21.sp,
          color: _gray,
        ),
      ),
    );
  }

  Widget _sectionLabel(String text) {
    return Align(
      alignment: AlignmentDirectional.centerStart,
      child: Padding(
        padding: EdgeInsets.only(bottom: 8.h),
        child: Text(
          text,
          style: TextStyle(
            fontFamily: 'Cairo',
            fontSize: 12.5.sp,
            fontWeight: FontWeight.w800,
            color: _navy,
          ),
        ),
      ),
    );
  }

  Widget _strengthMeter() {
    return Padding(
      padding: EdgeInsets.only(top: 10.h),
      child: Column(
        children: [
          Row(
            children: List.generate(4, (index) {
              final filled = index < _strength;
              final color =
                  filled ? _strengthColors[_strength] : const Color(0xFFE2E8F0);
              return Expanded(
                child: Container(
                  margin: EdgeInsetsDirectional.only(
                    end: index < 3 ? 6.w : 0,
                  ),
                  height: 5.h,
                  decoration: BoxDecoration(
                    color: color,
                    borderRadius: BorderRadius.circular(4.r),
                  ),
                ),
              );
            }),
          ),
          SizedBox(height: 6.h),
          Align(
            alignment: AlignmentDirectional.centerEnd,
            child: Text(
              _strengthLabels[_strength],
              style: TextStyle(
                fontFamily: 'Cairo',
                fontSize: 11.sp,
                fontWeight: FontWeight.w700,
                color: _strength == 0 ? _grayLight : _strengthColors[_strength],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _matchIndicator() {
    final hasText = _confirmPassword.isNotEmpty;
    final matches = hasText && _confirmPassword == _newPassword;
    final color = !hasText ? _grayLight : (matches ? _green : _red);
    return Padding(
      padding: EdgeInsets.only(top: 8.h),
      child: Row(
        children: [
          Icon(
            matches ? Icons.check_circle_rounded : Icons.cancel_rounded,
            size: 15.sp,
            color: !hasText ? _grayLight : color,
          ),
          SizedBox(width: 6.w),
          Text(
            !hasText
                ? 'تأكد أن كلمة المرور مطابقة للجديدة'
                : (matches
                    ? 'كلمتا المرور متطابقتان'
                    : 'كلمتا المرور غير متطابقتين'),
            style: TextStyle(
              fontFamily: 'Cairo',
              fontSize: 11.sp,
              fontWeight: FontWeight.w700,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _tipsCard() {
    const tips = [
      'استخدم 8 أحرف على الأقل',
      'اجمع بين الأحرف والأرقام والرموز',
      'لا تستخدم كلمة مرور قديمة',
    ];
    return Container(
      width: double.infinity,
      margin: EdgeInsets.only(top: 18.h),
      padding: EdgeInsets.all(14.w),
      decoration: BoxDecoration(
        color: _tipBg,
        borderRadius: BorderRadius.circular(16.r),
        border: Border.all(color: const Color(0xFFBFE9E6)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.tips_and_updates_rounded,
                  size: 17.sp, color: _teal),
              SizedBox(width: 8.w),
              Text(
                'لكلمة مرور قوية',
                style: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 12.sp,
                  fontWeight: FontWeight.w900,
                  color: _teal,
                ),
              ),
            ],
          ),
          SizedBox(height: 8.h),
          ...tips.map(
            (tip) => Padding(
              padding: EdgeInsets.symmetric(vertical: 2.5.h),
              child: Row(
                children: [
                  Icon(Icons.check_rounded,
                      size: 15.sp, color: _tealDark),
                  SizedBox(width: 8.w),
                  Expanded(
                    child: Text(
                      tip,
                      style: TextStyle(
                        fontFamily: 'Cairo',
                        fontSize: 11.5.sp,
                        fontWeight: FontWeight.w600,
                        color: _slate,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _submitButton({required bool loading}) {
    return GestureDetector(
      onTap: loading
          ? null
          : () {
              if (!(_formKey.currentState?.saveAndValidate() ?? false)) {
                return;
              }
              final fields = _formKey.currentState!.fields;
              ref.watch(changePassProvider.notifier).changePassword(
                    fields['current_password']!.value as String,
                    fields['password']!.value as String,
                    fields['password_confirmation']!.value as String,
                  );
            },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 250),
        height: 52.h,
        width: double.infinity,
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [_teal, _tealDark],
            begin: Alignment.centerRight,
            end: Alignment.centerLeft,
          ),
          borderRadius: BorderRadius.circular(14.r),
          boxShadow: const [
            BoxShadow(
              color: Color(0x4700A896),
              blurRadius: 14,
              offset: Offset(0, 6),
            ),
          ],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            if (loading)
              SizedBox(
                width: 20.sp,
                height: 20.sp,
                child: CircularProgressIndicator(
                  strokeWidth: 2.4,
                  color: Colors.white.withOpacity(0.9),
                ),
              )
            else ...[
              const Icon(Icons.lock_reset_rounded,
                  color: Colors.white, size: 20),
              SizedBox(width: 10.w),
            ],
            SizedBox(width: 8.w),
            Text(
              loading ? 'جارٍ تغيير كلمة المرور...' : 'تغيير كلمة المرور',
              style: TextStyle(
                fontFamily: 'Cairo',
                fontSize: 14.sp,
                fontWeight: FontWeight.w900,
                color: Colors.white,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _errorBox(String error) {
    return Container(
      margin: EdgeInsets.only(top: 12.h),
      width: double.infinity,
      padding: EdgeInsets.all(12.w),
      decoration: BoxDecoration(
        color: _redSoft,
        borderRadius: BorderRadius.circular(14.r),
        border: Border.all(color: const Color(0xFFF5C2C7)),
      ),
      child: Row(
        children: [
          const Icon(Icons.error_outline_rounded, color: _red, size: 20),
          SizedBox(width: 8.w),
          Expanded(
            child: Text(
              error,
              style: TextStyle(
                fontFamily: 'Cairo',
                fontSize: 11.5.sp,
                fontWeight: FontWeight.w700,
                color: _red,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _successBox() {
    return Container(
      margin: EdgeInsets.only(top: 12.h),
      width: double.infinity,
      padding: EdgeInsets.all(16.w),
      decoration: BoxDecoration(
        color: _greenSoft,
        borderRadius: BorderRadius.circular(16.r),
        border: Border.all(color: const Color(0xFFBFE9CB)),
      ),
      child: Column(
        children: [
          const Icon(Icons.check_circle_rounded, color: _green, size: 38),
          SizedBox(height: 8.h),
          Text(
            'تم تغيير كلمة المرور بنجاح',
            style: TextStyle(
              fontFamily: 'Cairo',
              fontSize: 14.sp,
              fontWeight: FontWeight.w900,
              color: _green,
            ),
          ),
          SizedBox(height: 4.h),
          Text(
            'سيتم إعادتك للملف الشخصي...',
            style: TextStyle(
              fontFamily: 'Cairo',
              fontSize: 11.sp,
              fontWeight: FontWeight.w600,
              color: _green.withOpacity(0.8),
            ),
          ),
        ],
      ),
    );
  }

  Widget _header(BuildContext context) {
    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [Color(0xFF028090), Color(0xFF00A896)],
          begin: Alignment.topRight,
          end: Alignment.bottomLeft,
        ),
        borderRadius: BorderRadius.vertical(bottom: Radius.circular(28)),
      ),
      child: SafeArea(
        bottom: false,
        child: Padding(
          padding: EdgeInsets.fromLTRB(20.w, 6.h, 20.w, 26.h),
          child: Column(
            children: [
              Row(
                children: [
                  InkWell(
                    onTap: () => context.nav.pop(),
                    borderRadius: BorderRadius.circular(12.r),
                    child: Container(
                      width: 38.w,
                      height: 38.h,
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.16),
                        borderRadius: BorderRadius.circular(12.r),
                        border: Border.all(color: Colors.white24),
                      ),
                      child: const Icon(
                        Icons.arrow_back_rounded,
                        color: Colors.white,
                        size: 19,
                      ),
                    ),
                  ),
                ],
              ),
              SizedBox(height: 14.h),
              Container(
                width: 74.w,
                height: 74.h,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.16),
                  shape: BoxShape.circle,
                  border: Border.all(color: Colors.white30),
                ),
                child: const Icon(
                  Icons.lock_reset_rounded,
                  color: Colors.white,
                  size: 36,
                ),
              ),
              SizedBox(height: 12.h),
              Text(
                'تغيير كلمة المرور',
                style: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 21.sp,
                  fontWeight: FontWeight.w900,
                  color: Colors.white,
                ),
              ),
              SizedBox(height: 4.h),
              Text(
                'حافظ على أمان حسابك بتحديث بيانات الدخول',
                style: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 11.sp,
                  fontWeight: FontWeight.w600,
                  color: Colors.white.withOpacity(0.85),
                ),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SingleChildScrollView(
        child: FormBuilder(
          key: _formKey,
          child: Column(
            children: [
              _header(context),
              Transform.translate(
                offset: const Offset(0, -14),
                child: Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16.w),
                  child: Container(
                    width: double.infinity,
                    padding: EdgeInsets.fromLTRB(18.w, 20.h, 18.w, 18.h),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(22.r),
                      border: Border.all(color: _border),
                      boxShadow: const [
                        BoxShadow(
                          color: Color(0x140B1E2E),
                          blurRadius: 18,
                          offset: Offset(0, 8),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _sectionLabel('كلمة المرور الحالية'),
                        FormBuilderTextField(
                          name: 'current_password',
                          obscureText: _currentObscure,
                          decoration: _fieldDecoration(
                            hint: 'أدخل كلمة المرور الحالية',
                            icon: Icons.lock_outline_rounded,
                            suffix: _eyeToggle(
                              obscure: _currentObscure,
                              onToggle: () => setState(
                                  () => _currentObscure = !_currentObscure),
                            ),
                          ),
                          keyboardType: TextInputType.text,
                          textInputAction: TextInputAction.next,
                          validator: FormBuilderValidators.compose([
                            FormBuilderValidators.required(errorText: 'يرجى إدخال كلمة المرور الحالية'),
                          ]),
                          style: TextStyle(
                            fontFamily: 'Cairo',
                            fontSize: 13.sp,
                          ),
                        ),
                        SizedBox(height: 16.h),
                        _sectionLabel('كلمة المرور الجديدة'),
                        FormBuilderTextField(
                          name: 'password',
                          obscureText: _newObscure,
                          onChanged: (value) {
                            setState(() {
                              _newPassword = value ?? '';
                              _strength = _strengthScore(_newPassword);
                            });
                          },
                          decoration: _fieldDecoration(
                            hint: 'أدخل كلمة مرور جديدة',
                            icon: Icons.key_rounded,
                            suffix: _eyeToggle(
                              obscure: _newObscure,
                              onToggle: () =>
                                  setState(() => _newObscure = !_newObscure),
                            ),
                          ),
                          keyboardType: TextInputType.text,
                          textInputAction: TextInputAction.next,
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'يرجى إدخال كلمة المرور الجديدة';
                            }
                            if (value.length < 8) {
                              return 'يجب أن تكون كلمة المرور 8 أحرف على الأقل';
                            }
                            return null;
                          },
                          style: TextStyle(
                            fontFamily: 'Cairo',
                            fontSize: 13.sp,
                          ),
                        ),
                        _strengthMeter(),
                        SizedBox(height: 16.h),
                        _sectionLabel('تأكيد كلمة المرور الجديدة'),
                        FormBuilderTextField(
                          name: 'password_confirmation',
                          obscureText: _confirmObscure,
                          onChanged: (value) {
                            setState(() => _confirmPassword = value ?? '');
                          },
                          decoration: _fieldDecoration(
                            hint: 'أعد إدخال كلمة المرور الجديدة',
                            icon: Icons.verified_user_outlined,
                            suffix: _eyeToggle(
                              obscure: _confirmObscure,
                              onToggle: () => setState(
                                  () => _confirmObscure = !_confirmObscure),
                            ),
                          ),
                          keyboardType: TextInputType.text,
                          textInputAction: TextInputAction.done,
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'يرجى تأكيد كلمة المرور';
                            }
                            final newPassword =
                                _formKey.currentState?.fields['password']?.value
                                    as String?;
                            if (newPassword != value) {
                              return 'كلمة المرور غير متطابقة';
                            }
                            return null;
                          },
                          style: TextStyle(
                            fontFamily: 'Cairo',
                            fontSize: 13.sp,
                          ),
                        ),
                        _matchIndicator(),
                        _tipsCard(),
                        SizedBox(height: 20.h),
                        ref.watch(changePassProvider).map(
                              initial: (_) => _submitButton(loading: false),
                              loading: (_) => _submitButton(loading: true),
                              error: (err) {
                                Future.delayed(const Duration(seconds: 4), () {
                                  if (mounted) {
                                    ref.invalidate(changePassProvider);
                                  }
                                });
                                return Column(
                                  children: [
                                    _submitButton(loading: false),
                                    _errorBox(err.error),
                                  ],
                                );
                              },
                              loaded: (_) {
                                Future.delayed(const Duration(milliseconds: 1200), () {
                                  ref.invalidate(changePassProvider);
                                  if (mounted) {
                                    context.nav.pop();
                                  }
                                });
                                return _successBox();
                              },
                            ),
                      ],
                    ),
                  ),
                ),
              ),
              SizedBox(height: 26.h),
            ],
          ),
        ),
      ),
    );
  }
}
