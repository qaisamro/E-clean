import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/constants/config.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:url_launcher/url_launcher.dart';

class ContactUs extends ConsumerWidget {
  const ContactUs({super.key});
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
                    Expanded(child: Text(S.of(context).cntctus, style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo'))),
                    Container(width: 36.w, height: 36.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), shape: BoxShape.circle), child: Icon(Icons.support_agent_rounded, size: 18.sp, color: Colors.white)),
                  ]),
                  SizedBox(height: 8.h),
                  Text('نحن هنا لمساعدتك في أي وقت', style: TextStyle(fontSize: 11.5.sp, color: Colors.white.withOpacity(0.92))),
                ],
              ),
            ),
          ),
          Expanded(
            child: SingleChildScrollView(
              padding: EdgeInsets.fromLTRB(16.w, 16.h, 16.w, 16.h),
              child: Column(
                children: [
                  Image.asset('assets/images/contact_us.png', height: 140.h, fit: BoxFit.contain),
                  SizedBox(height: 16.h),
                  _contactCard(icon: Icons.location_on_rounded, color: const Color(0xFF00A896), title: 'العنوان', subtitle: AppConfig.ctAboutCompany, onTap: null),
                  SizedBox(height: 12.h),
                  _contactCard(icon: Icons.mail_rounded, color: const Color(0xFF028090), title: 'البريد الإلكتروني', subtitle: AppConfig.ctMail, trailing: 'إرسال', onTap: () async { if (!await launchUrl(Uri.parse('mailto:${AppConfig.ctMail}'))) EasyLoading.showError("Couldn't Mail"); }),
                  SizedBox(height: 12.h),
                  _contactCard(icon: Icons.phone_rounded, color: const Color(0xFFF59E0B), title: 'الهاتف', subtitle: AppConfig.ctAboutCompany, trailing: 'اتصال', onTap: () async { final tel = AppConfig.ctAboutCompany.replaceAll(RegExp(r'[^0-9+]'), ''); if (tel.isNotEmpty) { final uri = Uri.parse('tel:$tel'); if (await canLaunchUrl(uri)) await launchUrl(uri); } }),
                  SizedBox(height: 12.h),
                  Container(
                    padding: EdgeInsets.all(16.w),
                    decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)]), borderRadius: BorderRadius.circular(20.r), boxShadow: [BoxShadow(color: const Color(0xFF00A896).withOpacity(0.22), blurRadius: 14, offset: const Offset(0, 6))]),
                    child: Row(children: [
                      Container(width: 40.w, height: 40.h, decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), shape: BoxShape.circle), child: Icon(Icons.chat_bubble_rounded, size: 18.sp, color: Colors.white)),
                      SizedBox(width: 12.w),
                      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text('هل تحتاج مساعدة فورية؟', style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: Colors.white)), Text('فريقنا جاهز للرد خلال دقائق', style: TextStyle(fontSize: 11.sp, color: Colors.white.withOpacity(0.92)))])),
                      Icon(Icons.chevron_left_rounded, color: Colors.white, size: 18.sp),
                    ]),
                  ),
                ],
              ),
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

  Widget _contactCard({required IconData icon, required Color color, required String title, required String subtitle, String? trailing, VoidCallback? onTap}) {
    return InkWell(
      borderRadius: BorderRadius.circular(20.r),
      onTap: onTap,
      child: Container(
        padding: EdgeInsets.all(16.w),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))]),
        child: Row(children: [
          Container(width: 44.w, height: 44.h, decoration: BoxDecoration(color: color.withOpacity(0.12), borderRadius: BorderRadius.circular(12.r)), child: Icon(icon, size: 20.sp, color: color)),
          SizedBox(width: 12.w),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(title, style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF64748B))), SizedBox(height: 2.h), Text(subtitle, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w800, color: const Color(0xFF0B1E2E)), maxLines: 2, overflow: TextOverflow.ellipsis)])),
          if (trailing != null) Container(padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h), decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(20.r)), child: Text(trailing, style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w900, color: Colors.white))),
        ]),
      ),
    );
  }
}
