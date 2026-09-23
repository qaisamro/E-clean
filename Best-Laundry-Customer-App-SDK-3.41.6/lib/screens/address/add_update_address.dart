import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class AddOrEditAddress extends ConsumerStatefulWidget {
  const AddOrEditAddress({super.key, this.address});
  final Address? address;
  @override
  ConsumerState<ConsumerStatefulWidget> createState() => _AddOrEditAddressState();
}

class _AddOrEditAddressState extends ConsumerState<AddOrEditAddress> {
  final GlobalKey<FormBuilderState> _formkey = GlobalKey<FormBuilderState>();
  List postCodelist = [];
  bool isMatchFound = false;

  void postCodeValidation({required String postCode}) {
    isMatchFound = false;
    for (final e in postCodelist) {
      final code = e.toString().toLowerCase().replaceAll(' ', '');
      final int len = code.length;
      if (code == postCode) {
        setState(() => isMatchFound = true);
      } else if (postCode.length > 3 && code.substring(0, len) == postCode.substring(0, len)) {
        setState(() => isMatchFound = true);
      }
    }
    if (isMatchFound) {
      ref.watch(addAddresProvider.notifier).addAddress(address: _formkey.currentState!.value).then((_) => setState(() => isMatchFound = false));
    } else {
      EasyLoading.showError("Service not Available in Your Area");
    }
  }

  String validationError({required String fieldName}) => '$fieldName مطلوب';

  InputDecoration _dec({required String hint, Widget? prefix}) => InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
        prefixIcon: prefix,
        filled: true, fillColor: const Color(0xFFF8FAFC),
        contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 14.h),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFFE2E8F0))),
        focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: const BorderSide(color: Color(0xFF00A896), width: 1.4)),
      );

  @override
  Widget build(BuildContext context) {
    ref.watch(settingsProvider).maybeWhen(orElse: () {}, loaded: (_) => postCodelist = _.data?.postCode ?? []);
    final isEdit = widget.address != null;
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: Column(
        children: [
          Container(
            padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 18.h),
            decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.vertical(bottom: Radius.circular(24))),
            child: SafeArea(
              bottom: false,
              child: Row(children: [
                InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: Colors.white24)), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Colors.white))),
                SizedBox(width: 12.w),
                Text(isEdit ? S.of(context).updtadrs : S.of(context).adadres, style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                const Spacer(),
                Container(width: 36.w, height: 36.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), shape: BoxShape.circle), child: Icon(isEdit ? Icons.edit_location_alt_rounded : Icons.add_location_alt_rounded, size: 18.sp, color: Colors.white)),
              ]),
            ),
          ),
          Expanded(
            child: SingleChildScrollView(
              padding: EdgeInsets.fromLTRB(20.w, 16.h, 20.w, 16.h),
              child: Container(
                padding: EdgeInsets.all(20.w),
                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 18, offset: const Offset(0, 8))]),
                child: FormBuilder(
                  key: _formkey,
                  initialValue: widget.address != null ? widget.address!.toMap() : {},
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('اسم المنزل', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(name: 'address_name', decoration: _dec(hint: 'مثال: منزل، عمل', prefix: Icon(Icons.home_outlined, size: 18.sp, color: const Color(0xFF028090))), validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: validationError(fieldName: 'الاسم'))])),
                      SizedBox(height: 14.h),
                      Text('العنوان', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(name: 'address_line', decoration: _dec(hint: 'الشارع، الحي', prefix: Icon(Icons.location_on_outlined, size: 18.sp, color: const Color(0xFF028090))), validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: validationError(fieldName: 'العنوان'))])),
                      SizedBox(height: 14.h),
                      FormBuilderTextField(name: "address_line2", decoration: _dec(hint: 'تفاصيل إضافية (اختياري)', prefix: Icon(Icons.notes_rounded, size: 18.sp, color: const Color(0xFF94A3B8)))),
                      SizedBox(height: 14.h),
                      Text('المنطقة', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(name: 'area', decoration: _dec(hint: 'مثال: دورا', prefix: Icon(Icons.map_outlined, size: 18.sp, color: const Color(0xFF028090))), validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: validationError(fieldName: 'المنطقة'))])),
                      SizedBox(height: 14.h),
                      Text('الرمز البريدي', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B))),
                      SizedBox(height: 8.h),
                      FormBuilderTextField(name: 'post_code', decoration: _dec(hint: '00000', prefix: Icon(Icons.local_post_office_outlined, size: 18.sp, color: const Color(0xFF028090))), validator: FormBuilderValidators.compose([FormBuilderValidators.required(errorText: validationError(fieldName: 'الرمز'))])),
                      SizedBox(height: 22.h),
                      SizedBox(
                        height: 50.h, width: double.infinity,
                        child: isEdit
                            ? ref.watch(updateAddresProvider).map(
                                initial: (_) => DecoratedBox(decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(16.r)), child: ElevatedButton(onPressed: () { if (_formkey.currentState!.saveAndValidate()) ref.read(updateAddresProvider.notifier).updateAddress(address: _formkey.currentState!.value, addressID: widget.address!.id!.toString()); }, style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))), child: Text(S.of(context).updtadrs, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')))),
                                loading: (_) => const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                                loaded: (_) { Future.delayed(apiDataDuration).then((_) { ref.refresh(updateAddresProvider); ref.refresh(addresListProvider); ref.refresh(addAddresProvider); Future.delayed(transissionDuration).then((_) => context.nav.pop()); }); return Container(alignment: Alignment.center, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r)), child: Text('تم التحديث', style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))); },
                                error: (_) { Future.delayed(transissionDuration).then((_) { ref.refresh(addAddresProvider); ref.refresh(updateAddresProvider); ref.refresh(addresListProvider); }); return Container(padding: EdgeInsets.all(10.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center)); },
                              )
                            : ref.watch(addAddresProvider).map(
                                initial: (_) => DecoratedBox(decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(16.r)), child: ElevatedButton(onPressed: () { if (_formkey.currentState!.saveAndValidate()) ref.read(addAddresProvider.notifier).addAddress(address: _formkey.currentState!.value); }, style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))), child: Text(S.of(context).adadres, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')))),
                                loading: (_) => const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                                loaded: (d) { Future.delayed(apiDataDuration).then((_) { ref.refresh(updateAddresProvider); ref.refresh(addresListProvider); ref.refresh(addAddresProvider); Future.delayed(transissionDuration).then((_) => context.nav.pop()); }); return Container(alignment: Alignment.center, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(12.r)), child: Text(d.data, style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))); },
                                error: (_) { Future.delayed(transissionDuration).then((_) { ref.refresh(addAddresProvider); ref.refresh(updateAddresProvider); ref.refresh(addresListProvider); }); return Container(padding: EdgeInsets.all(10.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center)); },
                              ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
