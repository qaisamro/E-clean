import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class TermsOfService extends ConsumerWidget {
  const TermsOfService({super.key});
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: Column(
        children: [
          Container(
            padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 18.h),
            decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.vertical(bottom: Radius.circular(24))),
            child: SafeArea(
              bottom: false,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(children: [
                    InkWell(borderRadius: BorderRadius.circular(12.r), onTap: () => context.nav.pop(), child: Container(width: 38.w, height: 38.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(12.r), border: Border.all(color: Colors.white24)), child: const Icon(Icons.arrow_forward_rounded, size: 18, color: Colors.white))),
                    SizedBox(width: 12.w),
                    Expanded(child: Text(S.of(context).trmsofsrvc, style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo'))),
                    Container(width: 36.w, height: 36.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), shape: BoxShape.circle), child: Icon(Icons.gavel_rounded, size: 18.sp, color: Colors.white)),
                  ]),
                  SizedBox(height: 8.h),
                  Text('القواعد التي تضمن تجربة عادلة للجميع', style: TextStyle(fontSize: 11.5.sp, color: Colors.white.withOpacity(0.92))),
                ],
              ),
            ),
          ),
          Expanded(
            child: ref.watch(tosProvider).map(
                  initial: (_) => const SizedBox(),
                  loading: (_) => const Center(child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090))),
                  loaded: (_) => SingleChildScrollView(
                    padding: EdgeInsets.fromLTRB(16.w, 16.h, 16.w, 16.h),
                    child: Container(
                      padding: EdgeInsets.all(18.w),
                      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 16, offset: const Offset(0, 6))]),
                      child: Html(style: {'*': Style(color: const Color(0xFF1E293B), fontSize: FontSize(13.sp), lineHeight: LineHeight(1.7), fontFamily: 'Cairo')}, data: _.data.data!.setting!.content!),
                    ),
                  ),
                  error: (_) => Center(child: Container(margin: EdgeInsets.all(20.w), padding: EdgeInsets.all(16.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center))),
                ),
          ),
          Container(
            padding: EdgeInsets.fromLTRB(20.w, 12.h, 20.w, 12.h + MediaQuery.of(context).viewPadding.bottom),
            decoration: BoxDecoration(color: Colors.white, border: Border(top: BorderSide(color: const Color(0xFFE2E8F0))), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 12, offset: const Offset(0, -4))]),
            child: SizedBox(
              width: double.infinity, height: 48.h,
              child: DecoratedBox(
                decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r)),
                child: ElevatedButton(onPressed: () => context.nav.pop(), style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))), child: Text(S.of(context).cls, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo'))),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
