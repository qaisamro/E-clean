import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/constants/config.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';
import 'package:url_launcher/url_launcher.dart';

/// Contact support screen — redesigned to match the app's teal identity.
/// Every channel is hidden automatically when its config value is empty.
class ContactUs extends StatelessWidget {
  const ContactUs({super.key});

  Future<void> _open(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  @override
  Widget build(BuildContext context) {
    final String whatsapp = AppConfig.ctWhatsApp;
    final String phone = AppConfig.ctPhone;
    final String mail = AppConfig.ctMail;

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: SafeArea(
        bottom: false,
        child: SingleChildScrollView(
          child: Column(
            children: [
              // ===== Gradient hero =====
              Container(
                width: double.infinity,
                padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 30.h),
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFF028090), Color(0xFF00A896)],
                    begin: Alignment.topRight,
                    end: Alignment.bottomLeft,
                  ),
                  borderRadius:
                      BorderRadius.vertical(bottom: Radius.circular(28)),
                ),
                child: Column(children: [
                  Row(children: [
                    InkWell(
                      borderRadius: BorderRadius.circular(12.r),
                      onTap: () => context.nav.pop(),
                      child: Container(
                          width: 38.w,
                          height: 38.h,
                          decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.16),
                              borderRadius: BorderRadius.circular(12.r),
                              border: Border.all(
                                  color: Colors.white.withOpacity(0.22))),
                          child: const Icon(Icons.arrow_forward_rounded,
                              size: 18, color: Colors.white)),
                    ),
                    const Spacer(),
                    Image.asset('assets/images/app_icon.png', height: 36.h),
                  ]),
                  SizedBox(height: 18.h),
                  Container(
                    width: 92.w,
                    height: 92.w,
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.16),
                      shape: BoxShape.circle,
                      boxShadow: const [
                        BoxShadow(
                          color: Color(0x5500474A),
                          blurRadius: 18,
                          offset: Offset(0, 6),
                        ),
                      ],
                    ),
                    child: Icon(Icons.support_agent_rounded,
                        size: 44.sp, color: Colors.white),
                  ),
                  SizedBox(height: 14.h),
                  Text('تواصل مع الدعم',
                      style: TextStyle(
                          fontSize: 20.sp,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                          fontFamily: 'Cairo')),
                  SizedBox(height: 6.h),
                  Text('نسعد بخدمتك على مدار الساعة',
                      style: TextStyle(
                          fontSize: 12.sp,
                          fontWeight: FontWeight.w600,
                          color: Colors.white.withOpacity(0.92),
                          fontFamily: 'Cairo')),
                ]),
              ),

              Padding(
                padding: EdgeInsets.fromLTRB(20.w, 20.h, 20.w, 28.h),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Text(
                      'اختر القناة المناسبة وسنقوم بالرد عليك في أقرب وقت',
                      style: TextStyle(
                          fontSize: 12.sp,
                          fontWeight: FontWeight.w600,
                          color: const Color(0xFF64748B),
                          height: 1.5,
                          fontFamily: 'Cairo'),
                    ),
                    SizedBox(height: 18.h),

                    if (whatsapp.isNotEmpty)
                      _channelCard(
                        icon: Icons.chat_rounded,
                        color: const Color(0xFF25D366),
                        title: 'واتساب',
                        subtitle: whatsapp,
                        buttonLabel: 'ابدأ محادثة',
                        buttonColor: const Color(0xFF25D366),
                        onTap: () =>
                            _open('https://wa.me/${whatsapp.replaceAll('+', '')}'),
                      ),

                    if (whatsapp.isNotEmpty && phone.isNotEmpty)
                      SizedBox(height: 12.h),

                    if (phone.isNotEmpty)
                      _channelCard(
                        icon: Icons.phone_in_talk_rounded,
                        color: const Color(0xFF028090),
                        title: 'اتصال هاتفي',
                        subtitle: phone,
                        buttonLabel: 'اتصل بنا',
                        buttonColor: const Color(0xFF028090),
                        onTap: () => _open('tel:$phone'),
                      ),

                    if ((whatsapp.isNotEmpty || phone.isNotEmpty) && mail.isNotEmpty)
                      SizedBox(height: 12.h),

                    if (mail.isNotEmpty)
                      _channelCard(
                        icon: Icons.mail_outline_rounded,
                        color: const Color(0xFFF59E0B),
                        title: 'البريد الإلكتروني',
                        subtitle: mail,
                        buttonLabel: 'أرسل بريداً',
                        buttonColor: const Color(0xFFF59E0B),
                        onTap: () => _open('mailto:$mail'),
                      ),

                    if (whatsapp.isEmpty && phone.isEmpty && mail.isEmpty)
                      Container(
                        padding: EdgeInsets.symmetric(vertical: 36.h),
                        alignment: Alignment.center,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20.r),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: Column(
                          children: [
                            Icon(Icons.contact_support_outlined,
                                size: 44.sp,
                                color: const Color(0xFFCBD5E1)),
                            SizedBox(height: 12.h),
                            Text('لا توجد قنوات تواصل متاحة حالياً.',
                                style: TextStyle(
                                    fontSize: 12.sp,
                                    color: const Color(0xFF64748B),
                                    fontFamily: 'Cairo')),
                          ],
                        ),
                      ),
                    SizedBox(height: 18.h),
                    Container(
                      padding: EdgeInsets.all(14.w),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEFFAF8),
                        borderRadius: BorderRadius.circular(20.r),
                        border:
                            Border.all(color: const Color(0xFF00A896).withOpacity(0.35)),
                      ),
                      child: Row(
                        children: [
                          Container(
                            width: 44.w,
                            height: 44.w,
                            decoration: BoxDecoration(
                              color: const Color(0xFF028090),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(Icons.schedule_rounded,
                                size: 22.sp, color: Colors.white),
                          ),
                          SizedBox(width: 12.w),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('ساعات العمل',
                                    style: TextStyle(
                                        fontSize: 13.sp,
                                        fontWeight: FontWeight.w900,
                                        color: const Color(0xFF0B1E2E),
                                        fontFamily: 'Cairo')),
                                SizedBox(height: 2.h),
                                Text('على مدار الساعة — بلا عطلة رسمية',
                                    style: TextStyle(
                                        fontSize: 11.sp,
                                        color: const Color(0xFF475569),
                                        fontFamily: 'Cairo')),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _channelCard({
    required IconData icon,
    required Color color,
    required String title,
    required String subtitle,
    required String buttonLabel,
    required Color buttonColor,
    required VoidCallback onTap,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22.r),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
              color: const Color(0xFF0B1E2E).withOpacity(0.05),
              blurRadius: 14,
              offset: const Offset(0, 6))
        ],
      ),
      padding: EdgeInsets.all(16.w),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(children: [
            Container(
              width: 52.w,
              height: 52.w,
              decoration: BoxDecoration(
                color: color.withOpacity(0.12),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 26.sp),
            ),
            SizedBox(width: 12.w),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title,
                      style: TextStyle(
                          fontSize: 13.sp,
                          fontWeight: FontWeight.w900,
                          color: const Color(0xFF0B1E2E),
                          fontFamily: 'Cairo')),
                  SizedBox(height: 3.h),
                  Text(subtitle,
                      style: TextStyle(
                          fontSize: 12.sp,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF028090),
                          fontFamily: 'Cairo'),
                      textDirection: TextDirection.ltr,
                      textAlign: TextAlign.right),
                ],
              ),
            ),
          ]),
          SizedBox(height: 14.h),
          SizedBox(
            height: 44.h,
            width: double.infinity,
            child: DecoratedBox(
              decoration: BoxDecoration(
                color: buttonColor,
                borderRadius: BorderRadius.circular(14.r),
                boxShadow: [
                  BoxShadow(
                      color: buttonColor.withOpacity(0.28),
                      blurRadius: 10,
                      offset: const Offset(0, 4)),
                ],
              ),
              child: ElevatedButton.icon(
                onPressed: onTap,
                icon: const Icon(Icons.arrow_forward_rounded,
                    size: 17, color: Colors.white),
                label: Text(
                  buttonLabel,
                  style: TextStyle(
                      fontSize: 13.sp,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                      fontFamily: 'Cairo'),
                ),
                style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.transparent,
                    shadowColor: Colors.transparent,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14.r),
                    )),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
