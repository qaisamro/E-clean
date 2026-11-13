import 'dart:async';

import 'package:expandable/expandable.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:gap/gap.dart';
import 'package:hive_flutter/adapters.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/add_order_model/add_order_model.dart';
import 'package:laundry_customer/models/order_details_model/order.dart';
import 'package:laundry_customer/models/order_details_model/product.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/screens/order/edit_order_screen.dart';
import 'package:laundry_customer/screens/order/order_dialouges.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/push_events.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/buttons/order_cancel_button.dart';
import 'package:laundry_customer/widgets/global_functions.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';
import 'package:laundry_customer/widgets/location_pill_button.dart';

class OrderDetails extends ConsumerStatefulWidget {
  const OrderDetails({
    super.key,
    required this.orderID,
    required this.orderStatus,
  });
  final String orderID;
  final String orderStatus;

  @override
  ConsumerState<OrderDetails> createState() => _OrderDetailsState();
}

class _OrderDetailsState extends ConsumerState<OrderDetails>
    with WidgetsBindingObserver {
  final Box cartsBox = Hive.box(AppHSC.cartBox);
  bool isChatAble = false;

  static const Duration _refreshInterval = Duration(seconds: 10);
  Timer? _refreshTimer;
  bool _resumed = true;

  @override
  void initState() {
    super.initState();
    // Stay in sync with server-side status changes made by the store.
    WidgetsBinding.instance.addObserver(this);
    pushReceived.addListener(_refreshDetailsSilently);
    _refreshTimer = Timer.periodic(
      _refreshInterval,
      (_) => _refreshDetailsSilently(),
    );
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    pushReceived.removeListener(_refreshDetailsSilently);
    _refreshTimer?.cancel();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    _resumed = state == AppLifecycleState.resumed;
    if (_resumed) _refreshDetailsSilently();
    super.didChangeAppLifecycleState(state);
  }

  void _refreshDetailsSilently() {
    if (!mounted || !_resumed) return;
    ref.read(orderDetailsProvider(widget.orderID).notifier).silentRefresh();
  }

  Future<void> _showCancelDialog(BuildContext context, Order order) async {
    final result = await showDialog<bool>(
      context: context,
      builder: (dialogContext) {
        return AlertDialog(
          shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(20.r)),
          title: const Text('إلغاء الطلب',
              style: TextStyle(
                  fontFamily: 'Cairo',
                  fontWeight: FontWeight.w900,
                  fontSize: 17)),
          content: const Text(
            'هل أنت متأكد من إلغاء هذا الطلب؟ لا يمكن التراجع بعد الإلغاء.',
            style: TextStyle(fontFamily: 'Cairo', fontSize: 13.5),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(dialogContext).pop(false),
              child: const Text('تراجع',
                  style: TextStyle(
                      fontFamily: 'Cairo',
                      fontWeight: FontWeight.w800,
                      color: Color(0xFF64748B))),
            ),
            ElevatedButton(
              onPressed: () => Navigator.of(dialogContext).pop(true),
              style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFE63946)),
              child: const Text('إلغاء الطلب',
                  style: TextStyle(
                      fontFamily: 'Cairo',
                      fontWeight: FontWeight.w800,
                      color: Colors.white)),
            ),
          ],
        );
      },
    );
    if (result != true) return;
    ref.read(orderCancelProvider.notifier).cancelOrder(order.id.toString());
  }


  @override
  Widget build(BuildContext context) {
    final Box settingsBox = Hive.box(AppHSC.appSettingsBox);

    ref.listen<ApiState<AddOrderModel>>(orderCancelProvider, (prev, next) {
      next.maybeWhen(
        loaded: (_) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('تم إلغاء الطلب بنجاح')),
          );
          ref.invalidate(orderDetailsProvider(widget.orderID));
          Navigator.of(context).pop();
        },
        error: (e) => ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text(e))),
        orElse: () {},
      );
    });

    ref.listen<ApiState<AddOrderModel>>(
        orderEditDeliveryProvider, (prev, next) {
      next.maybeWhen(
        loaded: (_) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('تم تحديث بيانات الطلب')),
          );
          ref.invalidate(orderDetailsProvider(widget.orderID));
        },
        error: (e) => ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text(e))),
        orElse: () {},
      );
    });

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: Column(
        children: [
          // Top Header Bar matching brand identity
          Container(
            padding: EdgeInsets.fromLTRB(16.w, 0, 16.w, 14.h),
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFF028090), Color(0xFF00A896)],
                begin: Alignment.topRight,
                end: Alignment.bottomLeft,
              ),
              borderRadius: BorderRadius.vertical(bottom: Radius.circular(24)),
            ),
            child: SafeArea(
              bottom: false,
              child: Row(
                children: [
                  IconButton(
                    icon: const Icon(
                      Icons.arrow_back_ios_new_rounded,
                      color: Colors.white,
                      size: 20,
                    ),
                    onPressed: () => context.nav.pop(),
                  ),
                  Expanded(
                    child: Text(
                      S.of(context).ordrdtls,
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontFamily: 'Cairo',
                        fontSize: 16.sp,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                      ),
                    ),
                  ),
                  const SizedBox(width: 48), // Balances the back button
                ],
              ),
            ),
          ),

          // Body Content
          Expanded(
            child: ref.watch(orderDetailsProvider(widget.orderID)).map(
                  initial: (_) => const LoadingWidget(
                    message: 'جاري تحميل تفاصيل الطلب...',
                  ),
                  loading: (_) => const LoadingWidget(
                    message: 'جاري تحميل تفاصيل الطلب...',
                  ),
                  error: (errState) => Center(
                    child: ErrorTextWidget(error: errState.error),
                  ),
                  loaded: (loadedState) {
                    final order = loadedState.data.data?.order;
                    if (order == null) {
                      return const Center(child: Text('لا توجد بيانات للطلب'));
                    }

                    Future.delayed(const Duration(milliseconds: 50), () {
                      if (mounted) {
                        setState(() {
                          isChatAble = order.isChatAble ?? false;
                        });
                      }
                    });

                    final List<OrderDetailsTile> orderWidgets = [];
                    final List<CarItemHiveModel> products = [];
                    final orderProducts = order.products ?? [];

                    for (var i = 0; i < orderProducts.length; i++) {
                      var subproductprice = 0;
                      final subproducts = orderProducts[i].sbproducts ?? [];
                      final orderedSubproducts = order.orderSubProduct ?? [];

                      for (final subproduct in subproducts) {
                        for (final orderedsubproduct in orderedSubproducts) {
                          if (subproduct.id == orderedsubproduct.id) {
                            subproductprice = orderedsubproduct.price ?? 0;
                          }
                        }
                      }

                      final qty = (order.quantity?.quantity != null &&
                              i < order.quantity!.quantity.length)
                          ? order.quantity!.quantity[i].quantity
                          : 1;

                      orderWidgets.add(
                        OrderDetailsTile(
                          product: orderProducts[i],
                          qty: qty,
                          subprice: subproductprice,
                        ),
                      );

                      if (order.orderStatus == 'Pending') {
                        cartsBox.clear();
                      }

                      products.add(
                        CarItemHiveModel(
                          productsId: orderProducts[i].id ?? 0,
                          productsName: orderProducts[i].name ?? '',
                          productsImage: orderProducts[i].imagePath ?? '',
                          productsQTY: qty,
                          unitPrice: orderProducts[i].currentPrice ?? 0.0,
                          serviceName: orderProducts[i].service?.name ?? '',
                        ),
                      );
                    }

                    return ListView(
                      physics: const BouncingScrollPhysics(
                        parent: AlwaysScrollableScrollPhysics(),
                      ),
                      padding: EdgeInsets.fromLTRB(16.w, 14.h, 16.w, 30.h),
                      children: [
                        // 1. Items List Card
                        Container(
                          padding: EdgeInsets.all(16.w),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(18.r),
                            border: Border.all(color: const Color(0xFFE2E8F0)),
                            boxShadow: [
                              BoxShadow(
                                color: const Color(0xFF0B1E2E)
                                    .withValues(alpha: 0.04),
                                blurRadius: 14,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: ExpandablePanel(
                            header: Row(
                              children: [
                                Container(
                                  padding: EdgeInsets.all(7.w),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFFEFFAF8),
                                    borderRadius: BorderRadius.circular(10.r),
                                  ),
                                  child: Icon(
                                    Icons.shopping_bag_outlined,
                                    color: const Color(0xFF028090),
                                    size: 18.sp,
                                  ),
                                ),
                                SizedBox(width: 10.w),
                                Text(
                                  '${S.of(context).itms} (${orderProducts.length})',
                                  style: TextStyle(
                                    fontFamily: 'Cairo',
                                    fontSize: 13.5.sp,
                                    fontWeight: FontWeight.w800,
                                    color: const Color(0xFF0B1E2E),
                                  ),
                                ),
                              ],
                            ),
                            collapsed: const SizedBox(),
                            expanded: Column(
                              children: [
                                SizedBox(height: 10.h),
                                ...orderWidgets,
                              ],
                            ),
                          ),
                        ),
                        SizedBox(height: 14.h),

                        // 2. Shipping Address Card
                        Container(
                          padding: EdgeInsets.all(16.w),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(18.r),
                            border: Border.all(color: const Color(0xFFE2E8F0)),
                            boxShadow: [
                              BoxShadow(
                                color: const Color(0xFF0B1E2E)
                                    .withValues(alpha: 0.04),
                                blurRadius: 14,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  Container(
                                    padding: EdgeInsets.all(7.w),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFEFFAF8),
                                      borderRadius: BorderRadius.circular(10.r),
                                    ),
                                    child: Icon(
                                      Icons.location_on_rounded,
                                      color: const Color(0xFF028090),
                                      size: 18.sp,
                                    ),
                                  ),
                                  SizedBox(width: 10.w),
                                  Text(
                                    S.of(context).shpngadrs,
                                    style: TextStyle(
                                      fontFamily: 'Cairo',
                                      fontSize: 13.5.sp,
                                      fontWeight: FontWeight.w800,
                                      color: const Color(0xFF0B1E2E),
                                    ),
                                  ),
                                ],
                              ),
                              SizedBox(height: 12.h),
                              if (order.customer?.user?.name != null)
                                Text(
                                  order.customer!.user!.name!,
                                  style: TextStyle(
                                    fontFamily: 'Cairo',
                                    fontSize: 13.sp,
                                    fontWeight: FontWeight.w800,
                                    color: const Color(0xFF0B1E2E),
                                  ),
                                ),
                              if (order.customer?.user?.mobile != null &&
                                  order.customer!.user!.mobile!.isNotEmpty) ...[
                                SizedBox(height: 3.h),
                                Text(
                                  order.customer!.user!.mobile!,
                                  style: TextStyle(
                                    fontFamily: 'Cairo',
                                    fontSize: 12.sp,
                                    color: const Color(0xFF64748B),
                                  ),
                                ),
                              ],
                              if (order.address != null) ...[
                                SizedBox(height: 4.h),
                                Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Expanded(
                                      child: Text(
                                        "${order.address!.addressName ?? ''}, ${order.address!.area ?? ''} ${order.address!.addressLine ?? ''}",
                                        style: TextStyle(
                                          fontFamily: 'Cairo',
                                          fontSize: 12.sp,
                                          color: const Color(0xFF475569),
                                          height: 1.35,
                                        ),
                                      ),
                                    ),
                                    SizedBox(width: 8.w),
                                    LocationPillButton(
                                      latitude: order.address!.latitude,
                                      longitude: order.address!.longitude,
                                      fallbackQuery:
                                          "${order.address!.addressLine ?? ''} ${order.address!.addressName ?? ''} ${order.address!.area ?? ''}"
                                              .trim(),
                                    ),
                                  ],
                                ),
                              ],
                            ],
                          ),
                        ),
                        SizedBox(height: 14.h),

                        // 3. Redesigned Luxury Invoice Card
                        _OrderInvoiceCard(
                          order: order,
                          settingsBox: settingsBox,
                        ),

                        // Pending-confirmation window: the store has not accepted
                        // the order yet. Let the customer edit or cancel it.
                        if ((order.orderStatus ?? '').toLowerCase().trim() ==
                            'pending') ...[
                          SizedBox(height: 12.h),
                          Row(
                            children: [
                              Expanded(
                                child: OutlinedButton.icon(
                                  onPressed: () =>
                                      Navigator.of(context).push(
                                    MaterialPageRoute<void>(
                                      builder: (_) =>
                                          EditOrderScreen(orderID: widget.orderID),
                                    ),
                                  ),
                                  icon: const Icon(Icons.edit_rounded,
                                      size: 18, color: Color(0xFF028090)),
                                  label: const Text('تعديل الطلب',
                                      style: TextStyle(
                                          fontWeight: FontWeight.w800,
                                          fontFamily: 'Cairo',
                                          color: Color(0xFF028090))),
                                  style: OutlinedButton.styleFrom(
                                    padding:
                                        EdgeInsets.symmetric(vertical: 12.h),
                                    side: const BorderSide(
                                        color: Color(0xFF028090), width: 1.4),
                                    shape: RoundedRectangleBorder(
                                      borderRadius:
                                          BorderRadius.circular(14.r),
                                    ),
                                  ),
                                ),
                              ),
                              SizedBox(width: 10.w),
                              Expanded(
                                child: OutlinedButton.icon(
                                  onPressed: () =>
                                      _showCancelDialog(context, order),
                                  icon: const Icon(Icons.cancel_outlined,
                                      size: 18, color: Color(0xFFE63946)),
                                  label: const Text('إلغاء الطلب',
                                      style: TextStyle(
                                          fontWeight: FontWeight.w800,
                                          fontFamily: 'Cairo',
                                          color: Color(0xFFE63946))),
                                  style: OutlinedButton.styleFrom(
                                    padding:
                                        EdgeInsets.symmetric(vertical: 12.h),
                                    side: const BorderSide(
                                        color: Color(0xFFE63946), width: 1.4),
                                    shape: RoundedRectangleBorder(
                                      borderRadius:
                                          BorderRadius.circular(14.r),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],

                        // 4. Rate Experience Button (if delivered and not rated yet)
                        if (order.orderStatus == 'Delivered' &&
                            order.rating == null) ...[
                          SizedBox(height: 20.h),
                          CancelOrderButton(
                            title: S.of(context).rateurexprnc,
                            onTap: () {
                              AppOrderDialouges.orderFeedBackDialouge(
                                context: context,
                                orderID: order.id.toString(),
                                ref: ref,
                              );
                            },
                          ),
                        ],
                        SizedBox(height: 40.h),
                        Gap(20.h),
                      ],
                    );
                  },
                ),
          ),
        ],
      ),
    );
  }
}

/// A luxury receipt/bill component inspired by modern fintech and matching the Elite Cleaning visual identity.
class _OrderInvoiceCard extends StatelessWidget {
  const _OrderInvoiceCard({
    required this.order,
    required this.settingsBox,
  });

  final Order order;
  final Box settingsBox;

  Color _getStatusColor(String? status) {
    final s = (status ?? '').toLowerCase().replaceAll(' ', '');
    if (s == 'pending') return const Color(0xFFF59E0B);
    if (s == 'pickedyourorder' || s == 'processing')
      return const Color(0xFF00A896);
    if (s == 'readyforpickup') return const Color(0xFF10B981);
    if (s == 'ontheway') return const Color(0xFF3B82F6);
    if (s == 'delivered') return const Color(0xFF028090);
    if (s == 'cancelled' || s == 'canceled') return const Color(0xFFE63946);
    return const Color(0xFF028090);
  }

  @override
  Widget build(BuildContext context) {
    final currency = settingsBox.get('currency') ?? '\$';
    final bool isPaid = order.paymentStatus?.toLowerCase() == 'paid';
    final statusColor = _getStatusColor(order.orderStatus);

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22.r),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0B1E2E).withValues(alpha: 0.06),
            blurRadius: 18,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(22.r),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Top Header Ribbon with Gradient
            Container(
              padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 14.h),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [Color(0xFF028090), Color(0xFF00A896)],
                  begin: Alignment.topRight,
                  end: Alignment.bottomLeft,
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: EdgeInsets.all(7.w),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.20),
                          borderRadius: BorderRadius.circular(10.r),
                        ),
                        child: Icon(
                          Icons.receipt_long_rounded,
                          color: Colors.white,
                          size: 18.sp,
                        ),
                      ),
                      SizedBox(width: 10.w),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'فاتورة الطلب',
                            style: TextStyle(
                              fontFamily: 'Cairo',
                              fontSize: 14.sp,
                              fontWeight: FontWeight.w900,
                              color: Colors.white,
                            ),
                          ),
                          if (order.orderedAt != null &&
                              order.orderedAt!.isNotEmpty)
                            Text(
                              order.orderedAt!,
                              style: TextStyle(
                                fontFamily: 'Cairo',
                                fontSize: 10.sp,
                                color: Colors.white.withValues(alpha: 0.85),
                              ),
                            ),
                        ],
                      ),
                    ],
                  ),
                  Container(
                    padding:
                        EdgeInsets.symmetric(horizontal: 10.w, vertical: 5.h),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.22),
                      borderRadius: BorderRadius.circular(20.r),
                      border: Border.all(
                        color: Colors.white.withValues(alpha: 0.35),
                      ),
                    ),
                    child: Text(
                      '${order.orderCode ?? ''}',
                      style: TextStyle(
                        fontFamily: 'Cairo',
                        fontSize: 12.sp,
                        fontWeight: FontWeight.w800,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Status Badges
            Padding(
              padding: EdgeInsets.fromLTRB(16.w, 14.h, 16.w, 0),
              child: Wrap(
                spacing: 8.w,
                runSpacing: 8.h,
                children: [
                  // Order Status
                  Container(
                    padding:
                        EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h),
                    decoration: BoxDecoration(
                      color: statusColor.withValues(alpha: 0.10),
                      borderRadius: BorderRadius.circular(20.r),
                      border: Border.all(
                          color: statusColor.withValues(alpha: 0.25)),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Container(
                          width: 7.w,
                          height: 7.w,
                          decoration: BoxDecoration(
                            color: statusColor,
                            shape: BoxShape.circle,
                          ),
                        ),
                        SizedBox(width: 6.w),
                        Text(
                          getLng(
                            en: order.orderStatus,
                            changeLang: order.orderStatusbn?.toString(),
                          ),
                          style: TextStyle(
                            fontFamily: 'Cairo',
                            fontSize: 11.sp,
                            fontWeight: FontWeight.w800,
                            color: statusColor,
                          ),
                        ),
                      ],
                    ),
                  ),

                  // Payment Status
                  Container(
                    padding:
                        EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h),
                    decoration: BoxDecoration(
                      color: isPaid
                          ? const Color(0xFFEFFAF8)
                          : const Color(0xFFFFF7ED),
                      borderRadius: BorderRadius.circular(20.r),
                      border: Border.all(
                        color: isPaid
                            ? const Color(0xFF00A896).withValues(alpha: 0.30)
                            : const Color(0xFFF59E0B).withValues(alpha: 0.30),
                      ),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          isPaid
                              ? Icons.check_circle_rounded
                              : Icons.schedule_rounded,
                          size: 13.sp,
                          color: isPaid
                              ? const Color(0xFF00A896)
                              : const Color(0xFFD97706),
                        ),
                        SizedBox(width: 5.w),
                        Text(
                          getLng(
                            en: order.paymentStatus,
                            changeLang: order.paymentStatusbn?.toString(),
                          ),
                          style: TextStyle(
                            fontFamily: 'Cairo',
                            fontSize: 11.sp,
                            fontWeight: FontWeight.w800,
                            color: isPaid
                                ? const Color(0xFF00A896)
                                : const Color(0xFFD97706),
                          ),
                        ),
                      ],
                    ),
                  ),

                  // Payment Type
                  if (order.paymentType != null &&
                      order.paymentType!.isNotEmpty)
                    Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 10.w, vertical: 6.h),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(20.r),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(
                            Icons.payments_outlined,
                            size: 13.sp,
                            color: const Color(0xFF64748B),
                          ),
                          SizedBox(width: 5.w),
                          Text(
                            order.paymentType!,
                            style: TextStyle(
                              fontFamily: 'Cairo',
                              fontSize: 11.sp,
                              fontWeight: FontWeight.w700,
                              color: const Color(0xFF475569),
                            ),
                          ),
                        ],
                      ),
                    ),
                ],
              ),
            ),

            // Service Schedule (Pickup & Delivery)
            Padding(
              padding: EdgeInsets.fromLTRB(16.w, 14.h, 16.w, 4.h),
              child: Row(
                children: [
                  // Pickup Card
                  Expanded(
                    child: Container(
                      padding: EdgeInsets.all(10.w),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF8FAFC),
                        borderRadius: BorderRadius.circular(12.r),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Icon(
                                Icons.outbox_rounded,
                                size: 14.sp,
                                color: const Color(0xFF028090),
                              ),
                              SizedBox(width: 5.w),
                              Text(
                                S.of(context).pickupat,
                                style: TextStyle(
                                  fontFamily: 'Cairo',
                                  fontSize: 10.5.sp,
                                  fontWeight: FontWeight.w700,
                                  color: const Color(0xFF64748B),
                                ),
                              ),
                            ],
                          ),
                          SizedBox(height: 4.h),
                          Text(
                            '${order.pickDate ?? '-'}  ${order.pickHour ?? ''}',
                            style: TextStyle(
                              fontFamily: 'Cairo',
                              fontSize: 11.5.sp,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF0B1E2E),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ),
                  SizedBox(width: 10.w),
                  // Delivery Card
                  Expanded(
                    child: Container(
                      padding: EdgeInsets.all(10.w),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF8FAFC),
                        borderRadius: BorderRadius.circular(12.r),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Icon(
                                Icons.move_to_inbox_rounded,
                                size: 14.sp,
                                color: const Color(0xFF00A896),
                              ),
                              SizedBox(width: 5.w),
                              Text(
                                S.of(context).dlvryat,
                                style: TextStyle(
                                  fontFamily: 'Cairo',
                                  fontSize: 10.5.sp,
                                  fontWeight: FontWeight.w700,
                                  color: const Color(0xFF64748B),
                                ),
                              ),
                            ],
                          ),
                          SizedBox(height: 4.h),
                          Text(
                            '${order.deliveryDate ?? '-'}  ${order.deliveryHour ?? ''}',
                            style: TextStyle(
                              fontFamily: 'Cairo',
                              fontSize: 11.5.sp,
                              fontWeight: FontWeight.w800,
                              color: const Color(0xFF0B1E2E),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Perforated Receipt Divider
            const _ReceiptDivider(),

            // Financial Breakdown Lines
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w),
              child: Column(
                children: [
                  _billRow(
                    label: S.of(context).sbttl,
                    value:
                        '$currency ${AppGFunctions.convertToFixedTwo(order.amount ?? 0)}',
                  ),
                  SizedBox(height: 8.h),
                  _billRow(
                    label: S.of(context).dlvrychrg,
                    value: (order.deliveryCharge == null ||
                            order.deliveryCharge == 0)
                        ? 'القيمة الخاصة بالتوصيل تحدد لاحقاً'
                        : '$currency ${AppGFunctions.convertToFixedTwo(order.deliveryCharge!)}',
                    isFree: order.deliveryCharge == null ||
                        order.deliveryCharge == 0,
                  ),
                  if (order.discount != null && order.discount! > 0) ...[
                    SizedBox(height: 8.h),
                    _billRow(
                      label: S.of(context).dscnt,
                      value:
                          '- $currency ${AppGFunctions.convertToFixedTwo(order.discount!)}',
                      isDiscount: true,
                    ),
                  ],
                ],
              ),
            ),

            // Grand Total Card Box
            Container(
              margin: EdgeInsets.fromLTRB(14.w, 14.h, 14.w, 16.h),
              padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 12.h),
              decoration: BoxDecoration(
                color: const Color(0xFFEFFAF8),
                borderRadius: BorderRadius.circular(16.r),
                border: Border.all(
                  color: const Color(0xFF00A896).withValues(alpha: 0.35),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        S.of(context).ttl,
                        style: TextStyle(
                          fontFamily: 'Cairo',
                          fontSize: 13.sp,
                          fontWeight: FontWeight.w900,
                          color: const Color(0xFF0B1E2E),
                        ),
                      ),
                      Text(
                        'شامل الضريبة وكافة الرسوم',
                        style: TextStyle(
                          fontFamily: 'Cairo',
                          fontSize: 10.sp,
                          color: const Color(0xFF64748B),
                        ),
                      ),
                    ],
                  ),
                  Text(
                    '$currency ${AppGFunctions.convertToFixedTwo(order.totalAmount ?? 0)}',
                    style: TextStyle(
                      fontFamily: 'Cairo',
                      fontSize: 18.sp,
                      fontWeight: FontWeight.w900,
                      color: const Color(0xFF028090),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _billRow({
    required String label,
    required String value,
    bool isDiscount = false,
    bool isFree = false,
  }) {
    Color valueColor = const Color(0xFF0B1E2E);
    FontWeight weight = FontWeight.w700;

    if (isDiscount || isFree) {
      valueColor = const Color(0xFF10B981);
      weight = FontWeight.w800;
    }

    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            fontFamily: 'Cairo',
            fontSize: 12.sp,
            color:
                isDiscount ? const Color(0xFF10B981) : const Color(0xFF64748B),
            fontWeight: isDiscount ? FontWeight.w700 : FontWeight.w600,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            fontFamily: 'Cairo',
            fontSize: 12.5.sp,
            fontWeight: weight,
            color: valueColor,
          ),
        ),
      ],
    );
  }
}

/// Dashed perforation line with authentic ticket cutouts
class _ReceiptDivider extends StatelessWidget {
  const _ReceiptDivider();

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 28.h,
      child: Stack(
        alignment: Alignment.center,
        children: [
          // Dashed line
          Positioned(
            left: 16.w,
            right: 16.w,
            child: LayoutBuilder(
              builder: (context, constraints) {
                final boxWidth = constraints.constrainWidth();
                const dashWidth = 5.0;
                const dashSpace = 4.0;
                final dashCount = (boxWidth / (dashWidth + dashSpace)).floor();
                return Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: List.generate(dashCount, (_) {
                    return SizedBox(
                      width: dashWidth,
                      height: 1.5,
                      child: DecoratedBox(
                        decoration: BoxDecoration(
                          color: const Color(0xFFE2E8F0),
                          borderRadius: BorderRadius.circular(1),
                        ),
                      ),
                    );
                  }),
                );
              },
            ),
          ),
          // Left notch
          Positioned(
            left: -11.w,
            child: Container(
              width: 22.w,
              height: 22.w,
              decoration: const BoxDecoration(
                color: Color(0xFFFAF7F2),
                shape: BoxShape.circle,
              ),
            ),
          ),
          // Right notch
          Positioned(
            right: -11.w,
            child: Container(
              width: 22.w,
              height: 22.w,
              decoration: const BoxDecoration(
                color: Color(0xFFFAF7F2),
                shape: BoxShape.circle,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class OrderDetailsTile extends StatelessWidget {
  const OrderDetailsTile({
    super.key,
    required this.product,
    required this.qty,
    this.subprice,
  });

  final Product product;
  final int qty;
  final int? subprice;

  @override
  Widget build(BuildContext context) {
    final Box settingsBox = Hive.box(AppHSC.appSettingsBox);
    final currency = settingsBox.get('currency') ?? '\$';
    final unitPrice =
        (product.currentPrice ?? 0) + (subprice != null ? subprice! : 0);
    final totalPrice = unitPrice * qty;

    return Padding(
      padding: EdgeInsets.symmetric(vertical: 8.h),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(10.r),
            child: AppNetworkImage(
              product.imagePath,
              height: 44.h,
              width: 44.w,
            ),
          ),
          SizedBox(width: 12.w),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        product.name ?? '',
                        style: TextStyle(
                          fontFamily: 'Cairo',
                          fontSize: 12.5.sp,
                          fontWeight: FontWeight.w800,
                          color: const Color(0xFF0B1E2E),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    Text(
                      '$currency ${AppGFunctions.convertToFixedTwo(totalPrice)}',
                      style: TextStyle(
                        fontFamily: 'Cairo',
                        fontSize: 12.5.sp,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xFF028090),
                      ),
                    ),
                  ],
                ),
                SizedBox(height: 2.h),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        product.service?.name ?? '',
                        style: TextStyle(
                          fontFamily: 'Cairo',
                          fontSize: 11.sp,
                          color: const Color(0xFF64748B),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    Text(
                      '${qty}x $currency ${AppGFunctions.convertToFixedTwo(unitPrice)}',
                      style: TextStyle(
                        fontFamily: 'Cairo',
                        fontSize: 11.sp,
                        color: const Color(0xFF64748B),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
