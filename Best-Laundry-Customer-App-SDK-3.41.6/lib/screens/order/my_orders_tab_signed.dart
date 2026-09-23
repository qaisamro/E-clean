import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:intl/intl.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/models/all_orders_model/order.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/screens/order/my_order_home_tile.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/global_functions.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class MyOrdersSignedIn extends ConsumerWidget {
  const MyOrdersSignedIn({super.key});
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return ref.watch(allOrdersProvider).map(
          initial: (_) => const SizedBox(),
          loading: (_) => const Center(child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090))),
          loaded: (_) {
            if (_.data.data!.orders!.isEmpty) {
              return Center(
                child: Container(
                  margin: EdgeInsets.only(top: 40.h),
                  padding: EdgeInsets.all(24.w),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9))),
                  child: Column(mainAxisSize: MainAxisSize.min, children: [
                    Container(width: 80.w, height: 80.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), shape: BoxShape.circle), child: Icon(Icons.receipt_long_rounded, size: 36.sp, color: const Color(0xFF94A3B8))),
                    SizedBox(height: 16.h),
                    Text(S.of(context).noordrfnd, style: TextStyle(fontSize: 14.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'), textAlign: TextAlign.center),
                    SizedBox(height: 6.h),
                    Text('ستظهر طلباتك هنا بعد إتمام أول طلب', style: TextStyle(fontSize: 11.5.sp, color: const Color(0xFF64748B))),
                  ]),
                ),
              );
            }
            return ListView.builder(
              padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 100.h),
              itemCount: _.data.data!.orders!.length,
              itemBuilder: (context, i) => Padding(padding: EdgeInsets.only(bottom: 12.h), child: OrderTile(data: _.data.data!.orders![i])),
            );
          },
          error: (_) => Center(child: Container(padding: EdgeInsets.all(14.w), decoration: BoxDecoration(color: const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(12.r)), child: Text(_.error, style: TextStyle(fontSize: 11.sp, color: const Color(0xFFE63946))))),
        );
  }
}

class OrderTile extends StatelessWidget {
  OrderTile({super.key, required this.data});
  final Order data;
  final Box settingsBox = Hive.box(AppHSC.appSettingsBox);
  @override
  Widget build(BuildContext context) {
    final bool isPaid = data.paymentStatus?.toLowerCase() == 'paid';
    return InkWell(
      borderRadius: BorderRadius.circular(20.r),
      onTap: () => context.nav.pushNamed(Routes.orderDetails, arguments: DetailsArg(orderId: data.id.toString(), orderStatus: data.orderStatus ?? '')),
      child: Container(
        padding: EdgeInsets.all(16.w),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r), border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.9)), boxShadow: [BoxShadow(color: const Color(0xFF0B1E2E).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))]),
        child: Column(
          children: [
            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              Row(children: [Container(width: 32.w, height: 32.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), borderRadius: BorderRadius.circular(10.r)), child: Icon(Icons.receipt_rounded, size: 16.sp, color: const Color(0xFF028090))), SizedBox(width: 8.w), Text('#${data.orderCode}', style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E)))]),
              Container(
                padding: EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h),
                decoration: BoxDecoration(color: getOrderStatusColor().withOpacity(0.12), borderRadius: BorderRadius.circular(20.r), border: Border.all(color: getOrderStatusColor().withOpacity(0.22))),
                child: Text(getLng(en: data.orderStatus, changeLang: data.orderStatusbn), style: TextStyle(fontSize: 10.5.sp, fontWeight: FontWeight.w900, color: getOrderStatusColor())),
              ),
            ]),
            SizedBox(height: 12.h),
            _row(S.of(context).date, DateFormat("dd MMM, yyyy").format(DateTime.parse(data.orderedAt!.split(" ").first))),
            SizedBox(height: 8.h),
            _row(S.of(context).dlvryoptn, data.paymentTypebn ?? data.paymentType ?? ''),
            SizedBox(height: 8.h),
            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              Text(S.of(context).pyblamnt, style: TextStyle(fontSize: 12.sp, color: const Color(0xFF64748B))),
              Text('${settingsBox.get('currency') ?? '\$'}${AppGFunctions.convertToFixedTwo(data.totalAmount!)}', style: TextStyle(fontSize: 13.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E))),
            ]),
            SizedBox(height: 8.h),
            Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
              Text(S.of(context).pymntstats, style: TextStyle(fontSize: 12.sp, color: const Color(0xFF64748B))),
              Container(padding: EdgeInsets.symmetric(horizontal: 8.w, vertical: 4.h), decoration: BoxDecoration(color: isPaid ? const Color(0xFFEFFAF8) : const Color(0xFFFFF1F2), borderRadius: BorderRadius.circular(8.r)), child: Text(getLng(en: data.paymentStatus, changeLang: data.paymentStatusbn), style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: isPaid ? const Color(0xFF028090) : const Color(0xFFE63946)))),
            ]),
            SizedBox(height: 12.h),
            Divider(height: 1, color: const Color(0xFFF1F5F9)),
            SizedBox(height: 12.h),
            Row(children: [
              Container(width: 28.w, height: 28.h, decoration: BoxDecoration(color: const Color(0xFFEFFAF8), shape: BoxShape.circle), child: Icon(Icons.location_on_rounded, size: 14.sp, color: const Color(0xFF00A896))),
              SizedBox(width: 8.w),
              Expanded(child: Text(AppGFunctions.processAdAddess(Address.fromMap(data.address!.toMap())), style: TextStyle(fontSize: 11.sp, color: const Color(0xFF475569), height: 1.3), maxLines: 2, overflow: TextOverflow.ellipsis)),
              Icon(Icons.chevron_left_rounded, size: 18.sp, color: const Color(0xFF94A3B8)),
            ]),
          ],
        ),
      ),
    );
  }

  Widget _row(String title, String value) => Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
        Text(title, style: TextStyle(fontSize: 12.sp, color: const Color(0xFF64748B))),
        Text(value, style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w700, color: const Color(0xFF334155))),
      ]);

  Color getOrderStatusColor() {
    final s = data.orderStatus!.toLowerCase();
    if (s == 'pending') return const Color(0xFFF59E0B);
    if (s.replaceAll(' ', '') == 'pickedYourOrder'.toLowerCase()) return const Color(0xFF00A896);
    return const Color(0xFF028090);
  }
}
