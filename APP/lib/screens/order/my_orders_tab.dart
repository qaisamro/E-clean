import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/screens/order/my_orders_tab_signed.dart';

class MyOrdersTab extends ConsumerWidget {
  const MyOrdersTab({super.key});
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Container(
      color: const Color(0xFFFAF7F2),
      child: Column(
        children: [
          Container(
            padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 16.h),
            decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF028090), Color(0xFF00A896)], begin: Alignment.topRight, end: Alignment.bottomLeft), borderRadius: BorderRadius.vertical(bottom: Radius.circular(24))),
            child: SafeArea(
              bottom: false,
              child: Row(children: [
                Text(S.of(context).myorder, style: TextStyle(fontSize: 18.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                const Spacer(),
                Container(padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h), decoration: BoxDecoration(color: Colors.white.withOpacity(0.18), borderRadius: BorderRadius.circular(20.r), border: Border.all(color: Colors.white24)), child: Row(children: [Icon(Icons.receipt_long_rounded, size: 14.sp, color: Colors.white), SizedBox(width: 4.w), Text('طلباتي', style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: Colors.white))])),
              ]),
            ),
          ),
          Expanded(
            child: ValueListenableBuilder(
              valueListenable: Hive.box(AppHSC.authBox).listenable(),
              builder: (context, Box authbox, _) {
                return authbox.get(AppHSC.authToken) != null ? const MyOrdersSignedIn() : const NotSignedInOrders();
              },
            ),
          ),
        ],
      ),
    );
  }
}

class NotSignedInOrders extends StatelessWidget {
  const NotSignedInOrders({super.key});
  @override
  Widget build(BuildContext context) {
    return Center(
      child: Container(
        margin: EdgeInsets.all(20.w),
        padding: EdgeInsets.all(24.w),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 18)]),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(width: 80.w, height: 80.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), shape: BoxShape.circle), child: Icon(Icons.login_rounded, size: 32.sp, color: const Color(0xFF028090))),
          SizedBox(height: 16.h),
          Text('سجّل دخولك لعرض طلباتك', style: TextStyle(fontSize: 15.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'), textAlign: TextAlign.center),
          SizedBox(height: 6.h),
          Text('ستجد هنا جميع طلباتك وحالتها', style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B)), textAlign: TextAlign.center),
        ]),
      ),
    );
  }
}
