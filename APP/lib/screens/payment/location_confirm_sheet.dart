import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/services/location_service.dart';

class LocationConfirmSheet extends ConsumerStatefulWidget {
  const LocationConfirmSheet({
    super.key,
    required this.picked,
    required this.addressLine,
  });

  final PickedLocation picked;
  final TextEditingController addressLine;

  @override
  ConsumerState<LocationConfirmSheet> createState() =>
      _LocationConfirmSheetState();
}

class _LocationConfirmSheetState extends ConsumerState<LocationConfirmSheet> {
  List postCodeList = [];
  String? _error;

  InputDecoration _dec(String hint) => InputDecoration(
        hintText: hint,
        hintStyle: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8)),
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

  bool _matches(String input) {
    final code = input.toLowerCase().replaceAll(' ', '');
    if (code.isEmpty) return false;
    for (final e in postCodeList) {
      final available = e.toString().toLowerCase().replaceAll(' ', '');
      if (available == code) return true;
      if (code.length > 3 &&
          available.length >= code.length &&
          available.substring(0, code.length) == code) {
        return true;
      }
    }
    return false;
  }

  void _confirm() {
    if (widget.addressLine.text.trim().isEmpty) {
      setState(() => _error = 'العنوان مطلوب');
      return;
    }
    if (postCodeList.isNotEmpty && !_matches(widget.picked.postCode)) {
      setState(() => _error = 'الخدمة غير متوفرة في منطقتك');
      return;
    }
    Navigator.of(context).pop(true);
  }

  @override
  Widget build(BuildContext context) {
    ref.watch(settingsProvider).maybeWhen(
          orElse: () {},
          loaded: (_) => postCodeList = _.data?.postCode ?? [],
        );

    return Padding(
      padding:
          EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
      child: Container(
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(26)),
        ),
        padding: EdgeInsets.fromLTRB(18.w, 14.h, 18.w, 22.h),
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 44.w,
                  height: 5.h,
                  decoration: BoxDecoration(
                      color: const Color(0xFFE2E8F0),
                      borderRadius: BorderRadius.circular(3.r)),
                ),
              ),
              SizedBox(height: 16.h),
              Row(children: [
                Container(
                  width: 38.w,
                  height: 38.h,
                  decoration: BoxDecoration(
                      color: const Color(0xFFEFFAF8),
                      borderRadius: BorderRadius.circular(12.r)),
                  child: Icon(Icons.my_location_rounded,
                      size: 19.sp, color: const Color(0xFF028090)),
                ),
                SizedBox(width: 10.w),
                Expanded(
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('عنوان موقعك الحالي',
                            style: TextStyle(
                                fontSize: 14.sp,
                                fontWeight: FontWeight.w900,
                                color: const Color(0xFF0B1E2E),
                                fontFamily: 'Cairo')),
                        Text(
                          '${widget.picked.latitude.toStringAsFixed(5)}, ${widget.picked.longitude.toStringAsFixed(5)}',
                          style: TextStyle(
                              fontSize: 10.sp, color: const Color(0xFF94A3B8)),
                        ),
                      ]),
                ),
              ]),
              SizedBox(height: 16.h),
              TextField(
                controller: widget.addressLine,
                decoration: _dec('العنوان بالتفصيل (الشارع، البناية)'),
              ),
              if (_error != null) ...[
                SizedBox(height: 10.h),
                Container(
                  width: double.infinity,
                  padding: EdgeInsets.all(10.w),
                  decoration: BoxDecoration(
                      color: const Color(0xFFFFF1F2),
                      borderRadius: BorderRadius.circular(10.r)),
                  child: Text(_error!,
                      style: TextStyle(
                          fontSize: 11.sp, color: const Color(0xFFE63946))),
                ),
              ],
              SizedBox(height: 16.h),
              Row(children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () => Navigator.of(context).pop(false),
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(color: Color(0xFFCBD5E1)),
                      padding: EdgeInsets.symmetric(vertical: 14.h),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14.r)),
                    ),
                    child: Text('إلغاء',
                        style: TextStyle(
                            fontWeight: FontWeight.w800,
                            color: const Color(0xFF64748B),
                            fontFamily: 'Cairo')),
                  ),
                ),
                SizedBox(width: 10.w),
                Expanded(
                  flex: 2,
                  child: ElevatedButton.icon(
                    onPressed: _confirm,
                    icon: Icon(Icons.check_rounded,
                        size: 17.sp, color: Colors.white),
                    label: Text('حفظ واستخدام العنوان',
                        style: TextStyle(
                            fontSize: 12.sp,
                            fontWeight: FontWeight.w800,
                            color: Colors.white,
                            fontFamily: 'Cairo')),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF028090),
                      padding: EdgeInsets.symmetric(vertical: 14.h),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(14.r)),
                    ),
                  ),
                ),
              ]),
            ],
          ),
        ),
      ),
    );
  }
}
