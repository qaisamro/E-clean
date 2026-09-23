import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/auth_provider.dart';
import 'package:laundry_customer/providers/profile_update_provider.dart';
import 'package:laundry_customer/screens/message/logic/socket.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/buttons/full_width_button.dart';
import 'package:laundry_customer/widgets/buttons/rounder_button.dart';
import 'package:laundry_customer/widgets/custom_tile.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class UsignedUserTab extends ConsumerWidget {
  const UsignedUserTab({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Container(
      color: const Color(0xFFFAF7F2),
      child: ValueListenableBuilder(
        valueListenable: Hive.box(AppHSC.userBox).listenable(),
        builder: (context, Box userBox, _) {
          return ValueListenableBuilder(
            valueListenable: Hive.box(AppHSC.authBox).listenable(),
            builder: (context, Box authBox, _) {
              final bool isLogged = authBox.get('token') != null;
              final String name = (userBox.get('name') ?? '').toString();
              final String phone = (userBox.get('mobile') ?? userBox.get('phone') ?? '').toString();
              final String photo = (userBox.get('profile_photo_path') ?? '').toString();

              return CustomScrollView(
                slivers: [
                  // هيدر عصري متدرج
                  SliverToBoxAdapter(
                    child: Container(
                      width: double.infinity,
                      padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 22.h),
                      decoration: const BoxDecoration(
                        gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft),
                        borderRadius: BorderRadius.vertical(bottom: Radius.circular(28)),
                      ),
                      child: SafeArea(
                        bottom: false,
                        child: Column(
                          children: [
                            SizedBox(height: 10.h),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text('حسابي', style: TextStyle(fontSize: 18.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                                Container(
                                  padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h),
                                  decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(20.r), border: Border.all(color: Colors.white24)),
                                  child: Row(children: [Icon(Icons.verified_user_rounded, size: 14.sp, color: Colors.white), SizedBox(width: 4.w), Text(isLogged ? 'مسجل' : 'زائر', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: Colors.white))]),
                                ),
                              ],
                            ),
                            SizedBox(height: 18.h),
                            if (isLogged) ...[
                              Stack(
                                alignment: Alignment.center,
                                children: [
                                  Container(
                                    width: 92.w, height: 92.h,
                                    decoration: BoxDecoration(shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 3), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.16), blurRadius: 14, offset: const Offset(0, 6))]),
                                    child: ClipOval(
                                      child: photo.isNotEmpty
                                          ? AppNetworkImage(photo, fit: BoxFit.cover, width: 92.w, height: 92.h, placeholder: 'assets/images/app_icon.png')
                                          : Container(color: Colors.white, child: Icon(Icons.person_rounded, size: 36.sp, color: const Color(0xFF028090))),
                                    ),
                                  ),
                                  Positioned(
                                    bottom: 0, right: 6.w,
                                    child: InkWell(
                                      onTap: () => context.nav.pushNamed(Routes.editProfileScreen),
                                      borderRadius: BorderRadius.circular(20.r),
                                      child: Container(
                                        padding: EdgeInsets.all(6.w),
                                        decoration: BoxDecoration(color: const Color(0xFFFFB703), shape: BoxShape.circle, border: Border.all(color: Colors.white, width: 2), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.14), blurRadius: 8)]),
                                        child: Icon(Icons.edit_rounded, size: 14.sp, color: Colors.white),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              SizedBox(height: 12.h),
                              Text(name.isNotEmpty ? name : 'مستخدم', style: TextStyle(fontSize: 18.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                              if (phone.isNotEmpty) ...[SizedBox(height: 2.h), Text(phone, style: TextStyle(fontSize: 12.sp, color: Colors.white.withOpacity(0.92), fontWeight: FontWeight.w600))],
                              SizedBox(height: 12.h),
                              InkWell(
                                borderRadius: BorderRadius.circular(20.r),
                                onTap: () => context.nav.pushNamed(Routes.editProfileScreen),
                                child: Container(
                                  padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 8.h),
                                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.10), blurRadius: 10)]),
                                  child: Row(mainAxisSize: MainAxisSize.min, children: [Icon(Icons.edit_outlined, size: 14.sp, color: const Color(0xFF028090)), SizedBox(width: 4.w), Text(S.of(context).edt, style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: const Color(0xFF028090)))]),
                                ),
                              ),
                            ] else ...[
                              Container(
                                padding: EdgeInsets.all(18.w),
                                decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 16)]),
                                child: Row(
                                  children: [
                                    Container(width: 56.w, height: 56.h, decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFF00A896), Color(0xFF028090)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r)), child: Icon(Icons.person_rounded, size: 28.sp, color: Colors.white)),
                                    SizedBox(width: 12.w),
                                    Expanded(
                                      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                        Text('مرحباً بك', style: TextStyle(fontSize: 15.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
                                        SizedBox(height: 2.h),
                                        Text('سجّل دخولك لعرض طلباتك وعناوينك', style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))),
                                      ]),
                                    ),
                                  ],
                                ),
                              ),
                              SizedBox(height: 12.h),
                              SizedBox(
                                width: double.infinity, height: 48.h,
                                child: DecoratedBox(
                                  decoration: BoxDecoration(gradient: const LinearGradient(colors: [Color(0xFFFFB703), Color(0xFFF59E0B)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.circular(16.r), boxShadow: [BoxShadow(color: const Color(0xFFFFB703).withOpacity(0.32), blurRadius: 12, offset: const Offset(0, 6))]),
                                  child: ElevatedButton(
                                    onPressed: () => context.nav.pushNamed(Routes.loginScreen),
                                    style: ElevatedButton.styleFrom(backgroundColor: Colors.transparent, shadowColor: Colors.transparent, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16.r))),
                                    child: Text(S.of(context).login, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
                                  ),
                                ),
                              ),
                            ],
                          ],
                        ),
                      ),
                    ),
                  ),

                  SliverPadding(
                    padding: EdgeInsets.fromLTRB(16.w, 16.h, 16.w, 120.h),
                    sliver: SliverList.list(
                      children: [
                        if (isLogged)
                          _sectionCard(
                            title: 'الحساب',
                            children: [
                              _tile(icon: Icons.location_on_rounded, color: const Color(0xFF00A896), title: S.of(context).mngaddrs, onTap: () { ref.refresh(addresListProvider); context.nav.pushNamed(Routes.manageAddressScreen); }),
                            ],
                          ),
                        if (isLogged) SizedBox(height: 14.h),

                        _sectionCard(
                          title: 'الدعم والمساعدة',
                          children: [
                            _tile(icon: Icons.lock_outline_rounded, color: const Color(0xFF028090), title: S.of(context).privacyPolicy, onTap: () => context.nav.pushNamed(Routes.privacyPolicyScreen)),
                            _divider(),
                            _tile(icon: Icons.gavel_rounded, color: const Color(0xFF64748B), title: S.of(context).trmsofsrvc, onTap: () => context.nav.pushNamed(Routes.termsOfServiceScreen)),
                            _divider(),
                            _tile(icon: Icons.support_agent_rounded, color: const Color(0xFF00A896), title: S.of(context).cntctus, onTap: () => context.nav.pushNamed(Routes.contactUsScreen)),
                            _divider(),
                            _tile(icon: Icons.info_outline_rounded, color: const Color(0xFFF59E0B), title: S.of(context).abtus, onTap: () => context.nav.pushNamed(Routes.aboutUsScreen), hideBorder: true),
                          ],
                        ),
                        SizedBox(height: 14.h),

                        if (isLogged)
                          _sectionCard(
                            title: 'الإجراءات',
                            isDestructive: true,
                            children: [
                              _tile(icon: Icons.delete_outline_rounded, color: const Color(0xFFE63946), title: S.of(context).deleteAcc, isDestructive: true, onTap: () => _showDeleteDialog(context, ref)),
                              _divider(),
                              _tile(icon: Icons.logout_rounded, color: const Color(0xFF64748B), title: S.of(context).lgout, onTap: () => _showLogoutDialog(context, ref), hideBorder: true),
                            ],
                          ),
                        if (isLogged) SizedBox(height: 14.h),

                        Center(child: Text('الإصدار 1.0.0  •  صُنع بعناية', style: TextStyle(fontSize: 10.sp, color: const Color(0xFF94A3B8)))),
                      ],
                    ),
                  ),
                ],
              );
            },
          );
        },
      ),
    );
  }

  Widget _sectionCard({String? title, List<Widget>? children, Widget? child, bool isDestructive = false}) {
    return Container(
      padding: EdgeInsets.all(14.w),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 16, offset: const Offset(0, 6))]),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (title != null) ...[Text(title, style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: isDestructive ? const Color(0xFFE63946) : const Color(0xFF028090), fontFamily: 'Cairo')), SizedBox(height: 10.h)],
          if (child != null) child else ...?children,
        ],
      ),
    );
  }

  Widget _tile({required IconData icon, required Color color, required String title, required VoidCallback onTap, bool isDestructive = false, bool hideBorder = false}) {
    return InkWell(
      borderRadius: BorderRadius.circular(12.r),
      onTap: onTap,
      child: Container(
        padding: EdgeInsets.symmetric(vertical: 12.h),
        decoration: BoxDecoration(border: hideBorder ? null : Border(bottom: BorderSide(color: const Color(0xFFF1F5F9)))),
        child: Row(
          children: [
            Container(width: 36.w, height: 36.h, decoration: BoxDecoration(color: color.withOpacity(isDestructive ? 0.12 : 0.10), borderRadius: BorderRadius.circular(10.r)), child: Icon(icon, size: 18.sp, color: color)),
            SizedBox(width: 12.w),
            Expanded(child: Text(title, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w800, color: isDestructive ? const Color(0xFFE63946) : const Color(0xFF0B1E2E)))),
            Icon(Icons.chevron_left_rounded, size: 18.sp, color: const Color(0xFF94A3B8)),
          ],
        ),
      ),
    );
  }

  Widget _divider() => Divider(height: 1, thickness: 1, color: const Color(0xFFF1F5F9));

  void _showLogoutDialog(BuildContext context, WidgetRef ref) {
    showDialog(context: context, builder: (c) => _confirmDialog(context, ref, title: S.of(context).urabttolgot, subtitle: S.of(context).arusre, isDelete: false));
  }

  void _showDeleteDialog(BuildContext context, WidgetRef ref) {
    showDialog(context: context, builder: (c) => _confirmDialog(context, ref, title: S.of(context).yourAccountWillBeDeleted, subtitle: S.of(context).arusre, isDelete: true));
  }

  Widget _confirmDialog(BuildContext context, WidgetRef ref, {required String title, required String subtitle, required bool isDelete}) {
    return Center(
      child: Padding(
        padding: EdgeInsets.all(20.w),
        child: Container(
          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r)),
          padding: EdgeInsets.all(20.w),
          child: Consumer(builder: (context, ref2, _) {
            return ref2.watch(logOutProvider).map(
                  initial: (_) => Column(mainAxisSize: MainAxisSize.min, children: [
                    Container(width: 48.w, height: 48.h, decoration: BoxDecoration(color: (isDelete ? const Color(0xFFE63946) : const Color(0xFF028090)).withOpacity(0.12), shape: BoxShape.circle), child: Icon(isDelete ? Icons.delete_forever_rounded : Icons.logout_rounded, size: 24.sp, color: isDelete ? const Color(0xFFE63946) : const Color(0xFF028090))),
                    SizedBox(height: 12.h),
                    Text(title, style: TextStyle(fontSize: 15.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'), textAlign: TextAlign.center),
                    SizedBox(height: 6.h),
                    Text(subtitle, style: TextStyle(fontSize: 12.sp, color: const Color(0xFF64748B)), textAlign: TextAlign.center),
                    SizedBox(height: 18.h),
                    Row(children: [
                      Expanded(child: OutlinedButton(onPressed: () => Navigator.pop(context), style: OutlinedButton.styleFrom(padding: EdgeInsets.symmetric(vertical: 12.h), side: const BorderSide(color: Color(0xFFE2E8F0)), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))), child: Text(S.of(context).no, style: TextStyle(fontWeight: FontWeight.w800, color: const Color(0xFF0B1E2E))))),
                      SizedBox(width: 10.w),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () => ref2.read(logOutProvider.notifier).logout().then((_) => ref2.read(socketProvider).socket!.dispose()),
                          style: ElevatedButton.styleFrom(backgroundColor: isDelete ? const Color(0xFFE63946) : const Color(0xFF028090), padding: EdgeInsets.symmetric(vertical: 12.h), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))),
                          child: Text(S.of(context).y, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900)),
                        ),
                      ),
                    ]),
                  ]),
                  loading: (_) => SizedBox(height: 80.h, child: const Center(child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                  error: (_) {
                    Future.delayed(const Duration(milliseconds: 800), () => ref2.refresh(logOutProvider));
                    return Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946)), textAlign: TextAlign.center);
                  },
                  loaded: (_) {
                    Future.delayed(const Duration(milliseconds: 700), () {
                      Navigator.pop(context);
                      Hive.box(AppHSC.userBox).clear();
                      Hive.box(AppHSC.authBox).clear();
                      ref2.refresh(profileInfoProvider);
                      ref2.refresh(logOutProvider);
                      context.nav.pushNamed(Routes.loginScreen);
                    });
                    return Column(mainAxisSize: MainAxisSize.min, children: [Icon(Icons.check_circle_rounded, size: 44.sp, color: const Color(0xFF00A896)), SizedBox(height: 10.h), Text(S.of(context).lgdot, style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090)))] );
                  },
                );
          }),
        ),
      ),
    );
  }
}
