import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:image_picker/image_picker.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/providers/profile_update_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class EditProfilePage extends ConsumerStatefulWidget {
  const EditProfilePage({super.key});
  @override
  ConsumerState<ConsumerStatefulWidget> createState() => _EditProfilePageState();
}

class _EditProfilePageState extends ConsumerState<EditProfilePage> {
  final GlobalKey<FormBuilderState> _formkey = GlobalKey<FormBuilderState>();
  File? image;

  InputDecoration _dec({required String hint, Widget? prefix, bool readOnly = false}) => InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
        prefixIcon: prefix,
        filled: true, fillColor: readOnly ? const Color(0xFFF1F5F9) : const Color(0xFFF8FAFC),
        contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 14.h),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFF00A896), width: 1.4)),
      );

  @override
  Widget build(BuildContext context) {
    ref.watch(profileUpdateProvider);
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: ValueListenableBuilder(
        valueListenable: Hive.box(AppHSC.userBox).listenable(),
        builder: (context, Box userBox, _) {
          final Map<String, dynamic> processedData = {};
          userBox.toMap().forEach((k, v) => processedData[k.toString()] = v);
          return CustomScrollView(
            slivers: [
              SliverToBoxAdapter(
                child: Container(
                  padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 24.h),
                  decoration: const BoxDecoration(
                    gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft),
                    borderRadius: BorderRadius.vertical(bottom: Radius.circular(28)),
                  ),
                  child: SafeArea(
                    bottom: false,
                    child: Column(
                      children: [
                        Row(children: [
                          InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: Colors.white24)), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Colors.white))),
                          const Spacer(),
                          Text('تعديل الحساب', style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                          const Spacer(),
                          SizedBox(width: 38.w),
                        ]),
                        SizedBox(height: 18.h),
                        GestureDetector(
                          onTap: () async {
                            if (image == null) {
                              final XFile? picked = await ImagePicker().pickImage(source: ImageSource.gallery);
                              if (picked != null) setState(() => image = File(picked.path));
                            } else {
                              setState(() => image = null);
                            }
                          },
                          child: Stack(
                            alignment: Alignment.center,
                            children: [
                              Container(
                                width: 96.w, height: 96.h,
                                decoration: BoxDecoration(shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 3), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.18), blurRadius: 14, offset: const Offset(0, 6))]),
                                child: ClipOval(
                                  child: image != null
                                      ? Image.file(image!, fit: BoxFit.cover, width: 96.w, height: 96.h)
                                      : (userBox.get('profile_photo_path') != null && userBox.get('profile_photo_path').toString().isNotEmpty
                                          ? AppNetworkImage(userBox.get('profile_photo_path').toString(), fit: BoxFit.cover, width: 96.w, height: 96.h, placeholder: 'assets/images/app_icon.png')
                                          : Container(color: Colors.white, width: 96.w, height: 96.h, child: Icon(Icons.person_rounded, size: 36.sp, color: const Color(0xFF028090)))),
                                ),
                              ),
                              Positioned(
                                bottom: 0, right: 0,
                                child: Container(
                                  padding: EdgeInsets.all(7.w),
                                  decoration: BoxDecoration(color: const Color(0xFFFFB703), shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 2), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.14), blurRadius: 8)]),
                                  child: Icon(image != null ? Icons.close_rounded : Icons.photo_camera_rounded, size: 14.sp, color: Colors.white),
                                ),
                              ),
                            ],
                          ),
                        ),
                        SizedBox(height: 10.h),
                        Text('اضغط لتغيير الصورة', style: TextStyle(fontSize: 11.sp, color: Colors.white.withOpacity(0.92), fontWeight: FontWeight.w600)),
                      ],
                    ),
                  ),
                ),
              ),
              SliverPadding(
                padding: EdgeInsets.fromLTRB(20.w, 16.h, 20.w, 24.h),
                sliver: SliverToBoxAdapter(
                  child: Container(
                    padding: EdgeInsets.all(20.w),
                    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 18, offset: const Offset(0, 8))]),
                    child: FormBuilder(
                      key: _formkey,
                      initialValue: processedData,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('الاسم', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                          SizedBox(height: 8.h),
                          FormBuilderTextField(name: 'first_name', decoration: _dec(hint: 'الاسم الكامل', prefix: Icon(Icons.person_outline_rounded, size: 18.sp, color: const Color(0xFF028090))), validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: 'مطلوب')])),
                          SizedBox(height: 14.h),
                          Text('رقم الجوال', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                          SizedBox(height: 8.h),
                          FormBuilderTextField(name: 'mobile', decoration: _dec(hint: '05xxxxxxxx', prefix: Icon(Icons.phone_rounded, size: 18.sp, color: const Color(0xFF028090)), readOnly: true), readOnly: true),
                          SizedBox(height: 14.h),
                          Row(children: [Text('رقم بديل', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))), SizedBox(width: 6.w), Container(padding: EdgeInsets.symmetric(horizontal: 6.w, vertical: 2.h), decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(8.r)), child: Text('اختياري', style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w700, color: const Color(0xFF028090))))]),
                          SizedBox(height: 8.h),
                          FormBuilderTextField(
                            name: 'alternative_phone',
                            decoration: _dec(hint: '05xxxxxxxx (اختياري)', prefix: Icon(Icons.phone_outlined, size: 18.sp, color: const Color(0xFF94A3B8))),
                            keyboardType: TextInputType.phone,
                            validator: (v) {
                              if (v == null || v.toString().trim().isEmpty) return null;
                              final s = v.toString().trim();
                              if (int.tryParse(s) == null) return 'أرقام فقط';
                              if (s.length < 9) return 'قصير جداً';
                              if (s.length > 12) return 'طويل جداً';
                              return null;
                            },
                          ),
                          SizedBox(height: 16.h),
                          SizedBox(
                            width: double.infinity, height: 44.h,
                            child: OutlinedButton.icon(
                              onPressed: () => context.nav.pushNamed(Routes.changePasswordScreen),
                              icon: Icon(Icons.lock_outline_rounded, size: 16.sp, color: const Color(0xFF028090)),
                              label: Text('تغيير كلمة المرور', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090))),
                              style: OutlinedButton.styleFrom(side: const BorderSide(color: Color(0xFF00A896)), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r)), backgroundColor: const Color(0xFFEFFAF8)),
                            ),
                          ),
                          SizedBox(height: 18.h),
                          SizedBox(
                            height: 50.h, width: double.infinity,
                            child: ref.watch(profileUpdateProvider).map(
                                  initial: (_) => DecoratedBox(
                                    decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.28), blurRadius: 12, offset: const Offset(0, 6))]),
                                    child: ElevatedButton(
                                      onPressed: () {
                                        if (_formkey.currentState!.saveAndValidate()) {
                                          ref.read(profileUpdateProvider.notifier).updateProfile(_formkey.currentState!.value, image);
                                        }
                                      },
                                      style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))),
                                      child: Text('حفظ التغييرات', style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                                    ),
                                  ),
                                  loading: (_) => const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                                  loaded: (_) {
                                    Future.delayed(transissionDuration).then((_) {
                                      ref.refresh(profileUpdateProvider);
                                      ref.refresh(profileInfoProvider);
                                      Future.delayed(buildDuration).then((_) => context.nav.pop());
                                    });
                                    return Container(alignment: Alignment.center, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r)), child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [Icon(Icons.check_circle_rounded, size: 18.sp, color: const Color(0xFF028090)), SizedBox(width: 6.w), Text('تم التحديث بنجاح', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))]));
                                  },
                                  error: (_) {
                                    Future.delayed(transissionDuration).then((_) => ref.refresh(profileUpdateProvider));
                                    return Container(padding: EdgeInsets.all(10.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center));
                                  },
                                ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}
