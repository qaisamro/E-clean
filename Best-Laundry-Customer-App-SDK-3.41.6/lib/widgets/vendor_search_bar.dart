import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';

class VendorSearchBar extends ConsumerStatefulWidget {
  const VendorSearchBar({super.key});
  @override
  ConsumerState<VendorSearchBar> createState() => _VendorSearchBarState();
}

class _VendorSearchBarState extends ConsumerState<VendorSearchBar> {
  final TextEditingController _controller = TextEditingController();
  Timer? _debounce;
  @override
  void dispose() {
    _debounce?.cancel();
    _controller.dispose();
    super.dispose();
  }

  void _onSearchChanged(String query) {
    if (_debounce?.isActive ?? false) _debounce!.cancel();
    _debounce = Timer(const Duration(milliseconds: 400), () {
      ref.read(vendorSearchQueryProvider.notifier).state = query.trim();
      ref.invalidate(vendorsProvider);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16.r),
        border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)),
        boxShadow: [BoxShadow(color: const Color(0xFF0C2238).withOpacity(0.06), blurRadius: 16, offset: const Offset(0, 6))],
      ),
      child: TextField(
        controller: _controller,
        onChanged: (v) {
          setState(() {});
          _onSearchChanged(v);
        },
        textInputAction: TextInputAction.search,
        style: TextStyle(fontSize: 12.sp, color: const Color(0xFF0B1E2E), fontWeight: FontWeight.w600),
        decoration: InputDecoration(
          hintText: 'ابحث عن متجر بالعنوان أو الاسم...',
          hintStyle: TextStyle(color: const Color(0xFF94A3B8), fontSize: 11.5.sp, fontWeight: FontWeight.w500),
          prefixIcon: Icon(Icons.search, size: 20.sp, color: const Color(0xFF94A3B8)),
          suffixIcon: _controller.text.isNotEmpty
              ? IconButton(icon: Icon(Icons.clear_rounded, size: 16.sp, color: const Color(0xFF94A3B8)), onPressed: () { _controller.clear(); _onSearchChanged(''); setState(() {}); })
              : IconButton(icon: Icon(Icons.tune_rounded, size: 18.sp, color: const Color(0xFF94A3B8)), onPressed: () {}),
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(16.r), borderSide: BorderSide.none),
          filled: true,
          fillColor: Colors.white,
          contentPadding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 12.h),
        ),
        onSubmitted: _onSearchChanged,
      ),
    );
  }
}
