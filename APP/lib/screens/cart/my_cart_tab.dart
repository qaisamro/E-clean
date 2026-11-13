import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/providers/order_update_provider.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/screens/cart/my_cart_with_image_card.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/laundry_basket_icon.dart';

/// Design tokens for the cart screen (shared with item cards).
const _teal = Color(0xFF028090);
const _tealDark = Color(0xFF00A896);
const _navy = Color(0xFF0B1E2E);
const _slate = Color(0xFF475569);
const _gray = Color(0xFF64748B);
const _grayLight = Color(0xFF94A3B8);
const _border = Color(0xFFE9EEF2);
const _borderSoft = Color(0xFFE2E8F0);
const _creamy = Color(0xFFFAF7F2);
const _white = Color(0xFFFFFFFF);
const _red = Color(0xFFE63946);
const _redSoft = Color(0xFFFFF1F2);

const _accentCycle = [
  Color(0xFF028090), // teal primary
  Color(0xFFF59E0B), // amber/gold
  Color(0xFF00A896), // teal light
  Color(0xFF2563EB), // blue
  Color(0xFF16A34A), // green
  Color(0xFFE11D48), // red/crimson
];

class MyCartTab extends ConsumerWidget {
  MyCartTab({super.key});
  final Box appSettingsBox = Hive.box(AppHSC.appSettingsBox);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final orderId = ref.watch(orderIdProvider);
    int? minimum = 0;
    double? dlvrychrg = 0;
    double? free = 0;
    ref.watch(settingsProvider).whenOrNull(loaded: (d) {
      minimum = d.data!.minimumCost;
      dlvrychrg = d.data!.deliveryCost!.toDouble();
      free = d.data!.feeCost!.toDouble();
    });
    ref.watch(couponProvider);
    ref.watch(discountAmountProvider);
    ref.watch(vendorCartsProvider);

    return Container(
      color: _creamy,
      child: Column(
        children: [
          // ===== Modern gradient header =====
          Container(
            padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 18.h),
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFF028090), Color(0xFF00A896)],
                begin: Alignment.topRight,
                end: Alignment.bottomLeft,
              ),
              borderRadius: BorderRadius.vertical(bottom: Radius.circular(26)),
            ),
            child: SafeArea(
              bottom: false,
              child: Row(children: [
                Container(
                  width: 42.w,
                  height: 42.w,
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.16),
                    shape: BoxShape.circle,
                  ),
                  child: const LaundryBasketIcon(size: 22, color: Colors.white),
                ),
                SizedBox(width: 12.w),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('السلة',
                          style: TextStyle(
                              fontSize: 19.sp,
                              fontWeight: FontWeight.w900,
                              color: Colors.white,
                              fontFamily: 'Cairo')),
                      Text('يمكنك الطلب من متجر واحد في كل مرة',
                          style: TextStyle(
                              fontSize: 11.sp,
                              fontWeight: FontWeight.w600,
                              color: Colors.white.withOpacity(.88))),
                    ],
                  ),
                ),
                ValueListenableBuilder(
                  valueListenable: Hive.box(AppHSC.cartBox).listenable(),
                  builder: (context, Box box, _) {
                    final c = CartHelper.getAllItems().length;
                    return Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 12.w, vertical: 7.h),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.18),
                        borderRadius: BorderRadius.circular(20.r),
                        border: Border.all(color: Colors.white24),
                      ),
                      child: Row(children: [
                        const LaundryBasketIcon(
                            size: 14, color: Colors.white),
                        SizedBox(width: 6.w),
                        Text('$c عنصر',
                            style: TextStyle(
                                fontSize: 11.sp,
                                fontWeight: FontWeight.w800,
                                color: Colors.white)),
                      ]),
                    );
                  },
                ),
              ]),
            ),
          ),

          // ===== Cart body =====
          Expanded(
            child: ValueListenableBuilder(
              valueListenable: Hive.box(AppHSC.authBox).listenable(),
              builder: (context, Box authbox, _) {
                if (authbox.get(AppHSC.authToken) == null) {
                  return _EmptyState(
                    circleIcon: Icons.lock_outline_rounded,
                    title: 'سجّل دخولك لعرض سلتك',
                    subtitle: 'ستجد هنا منتجاتك قبل الطلب',
                    iconColor: Color(0xFFE2E8F0),
                    iconAccent: Color(0xFF94A3B8),
                    primaryLabel: 'تسجيل الدخول',
                    primaryIcon: Icons.login_rounded,
                    onPrimary: (context) =>
                        Navigator.of(context).pushNamed(Routes.loginScreen),
                  );
                }
                return ValueListenableBuilder(
                  valueListenable: Hive.box(AppHSC.cartBox).listenable(),
                  builder: (context, Box cartBox, _) {
                    final vendorCarts = CartHelper.getAllVendorCarts();
                    final allItems = CartHelper.getAllItems();
                    if (allItems.isEmpty) {
                      return _EmptyState(
                        circleAsset: 'assets/svgs/icon_laundry_basket.svg',
                        title: 'سلتك فارغة',
                        subtitle: 'أضف منتجات من الخدمات أو المتاجر',
                        iconColor: Color(0xFFEFFAF8),
                        iconAccent: Color(0xFF028090),
                        primaryLabel: 'تصفح المتاجر والخدمات',
                        primaryIcon: Icons.storefront_rounded,
                        onPrimary: (context) =>
                            context.nav.pushNamedAndRemoveUntil(
                                Routes.homeScreen, (route) => false),
                      );
                    }

                    final currency =
                        appSettingsBox.get('currency')?.toString() ?? '\$';
                    final double allSubtotal = calculateTotal(allItems);
                    final double discount = ref.watch(discountAmountProvider);
                    final freeBelow = (free ?? 0).toDouble();
                    final delivery =
                        allSubtotal.toInt() < freeBelow ? (dlvrychrg ?? 0) : 0.0;
                    final double grandTotal =
                        (allSubtotal + delivery - discount)
                            .clamp(0, double.infinity)
                            .toDouble();

                    return ListView(
                      padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 140.h),
                      children: [
                        // ===== Vendor carts =====
                        ...vendorCarts.entries.toList().asMap().entries.map((entry) {
                          final int index = entry.key;
                          final MapEntry<String, List<CarItemHiveModel>> e =
                              entry.value;
                          final vid = e.key;
                          final items = e.value;
                          final String rawName =
                              items.first.vendorName ?? '';
                          final vName = rawName.isNotEmpty
                              ? rawName
                              : (vid == 'default' ? 'المتجر الرئيسي' : 'متجر #$vid');
                          final double vTotal =
                              CartHelper.calculateTotalForVendor(vid);
                          final accent = _accentCycle[index % _accentCycle.length];
                          return _VendorCartCard(
                            key: ValueKey('vendor-cart-$vid'),
                            vid: vid,
                            name: vName,
                            accent: accent,
                            items: items,
                            vTotal: vTotal,
                            currency: currency,
                            onClear: () {
                              CartHelper.clearVendorCart(vid);
                              ref.refreshVendorCarts();
                            },
                          );
                        }),

                        // ===== Order summary =====
                        _OrderSummaryCard(
                          allSubtotal: allSubtotal,
                          delivery: delivery,
                          discount: discount,
                          grandTotal: grandTotal,
                          currency: currency,
                          vendorRows: vendorCarts.entries
                              .map((e) => MapEntry(
                                    e.value.first.vendorName ?? 'متجر',
                                    CartHelper.calculateTotalForVendor(
                                        e.key),
                                  ))
                              .toList(),
                        ),
                        SizedBox(height: 10.h),

                        // ===== Unified single-store checkout =====
                        _GradientButton(
                          onTap: () {
                            final nonEmpty = vendorCarts.entries
                                .where((e) => e.value.isNotEmpty)
                                .toList();
                            if (nonEmpty.isEmpty) return;
                            if (nonEmpty.length > 1) {
                              EasyLoading.showError(
                                  'يمكن إتمام الطلب من متجر واحد فقط — أفرغ العربات الأخرى أولاً');
                              return;
                            }
                            final vid = nonEmpty.single.key;
                            final items = nonEmpty.single.value;
                            ref.read(activeVendorIdProvider.notifier).state =
                                vid;
                            if (calculateTotal(items) >= (minimum ?? 0)) {
                              ref.refresh(addresListProvider);
                              context.nav.pushNamed(Routes.checkOutScreen);
                            } else {
                              EasyLoading.showError(
                                  '${S.of(context).mnmmordramnt} ${AppGFunctions.convertToFixedTwo(minimum!)}');
                            }
                          },
                          label: 'إتمام الطلب',
                          icon: Icons.receipt_long_rounded,
                          radius: 16.r,
                        ),
                        SizedBox(height: 10.h),

                        // ===== Empty carts (multi vendor) =====
                        if (vendorCarts.length > 1)
                          SizedBox(
                            width: double.infinity,
                            height: 44.h,
                            child: OutlinedButton.icon(
                              onPressed: () {
                                CartHelper.clearAll();
                                ref.refreshVendorCarts();
                              },
                              icon: const Icon(Icons.delete_sweep_rounded,
                                  size: 18),
                              style: OutlinedButton.styleFrom(
                                foregroundColor: _red,
                                side: const BorderSide(color: _red),
                                shape: RoundedRectangleBorder(
                                  borderRadius:
                                      BorderRadius.circular(14.r),
                                ),
                              ),
                              label: Text('إفراغ كل العربات',
                                  style: TextStyle(
                                      fontWeight: FontWeight.w800,
                                      fontSize: 13.sp)),
                            ),
                          ),

                        // ===== Update order (edit mode only) =====
                        if (orderId != '') ...[
                          SizedBox(height: 6.h),
                          Consumer(builder: (context, ref, _) {
                            return ref.watch(updateOrderProvider).map(
                                  initial: (_) => _GradientButton(
                                    onTap: () => updateOrderProduct(
                                        ref: ref,
                                        orderId: orderId,
                                        cartItems: allItems,
                                        context: context),
                                    label: S.of(context).updateproduct,
                                    icon: Icons.receipt_long_rounded,
                                    radius: 16.r,
                                  ),
                                  loading: (_) => const Center(
                                      child: Padding(
                                    padding: EdgeInsets.all(8),
                                    child: SizedBox(
                                      width: 26,
                                      height: 26,
                                      child: CircularProgressIndicator(
                                          strokeWidth: 2.4,
                                          color: _teal),
                                    ),
                                  )),
                                  loaded: (_) => const SizedBox(),
                                  error: (_) => Center(
                                      child: Text('حدث خطأ ما',
                                          style: TextStyle(
                                              color: const Color(0xFFE63946),
                                              fontWeight: FontWeight.w700))),
                                );
                          }),
                          SizedBox(height: 10.h),
                          SizedBox(
                            width: double.infinity,
                            height: 44.h,
                            child: OutlinedButton(
                              onPressed: () {
                                ref.read(homeScreenPageControllerProvider)
                                    .animateToPage(4,
                                        duration: transissionDuration,
                                        curve: Curves.easeInOut);
                                ref
                                    .read(homeScreenIndexProvider.notifier)
                                    .state = 4;
                              },
                              style: OutlinedButton.styleFrom(
                                side: const BorderSide(color: _tealDark),
                                shape: RoundedRectangleBorder(
                                  borderRadius:
                                      BorderRadius.circular(14.r),
                                ),
                              ),
                              child: Text(S.of(context).addmore,
                                  style: TextStyle(
                                      color: _teal,
                                      fontWeight: FontWeight.w800,
                                      fontSize: 13.sp)),
                            ),
                          ),
                        ],
                      ],
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  void updateOrderProduct({
    required WidgetRef ref,
    required String orderId,
    required List<CarItemHiveModel> cartItems,
    required BuildContext context,
  }) {
    final List<OrderProductModel> products = [];
    for (final c in cartItems) {
      products.add(OrderProductModel(
          id: c.productsId.toString(), quantity: c.productsQTY.toString()));
    }
    ref.read(orderRepoProvider).updateOrder(products, orderId).then((_) {
      EasyLoading.showSuccess(S.of(context).orderupdatesuccmes);
      ref.read(orderIdProvider.notifier).state = '';
      CartHelper.clearAll();
      ref.refreshVendorCarts();
      ref.read(homeScreenIndexProvider.notifier).state = 1;
      ref.read(homeScreenPageControllerProvider).animateToPage(1,
          duration: transissionDuration, curve: Curves.easeInOut);
    });
  }

  double calculateTotal(List<CarItemHiveModel> cartItems) {
    double a = 0;
    for (final e in cartItems) {
      if (e.subProduct != null) {
        a += e.productsQTY * (e.unitPrice + (e.subProduct!.price?.toDouble() ?? 0));
      } else {
        a += e.productsQTY * e.unitPrice;
      }
    }
    return a;
  }
}

/// Modern empty / signed-out state with a CTA.
class _EmptyState extends StatelessWidget {
  const _EmptyState({
    this.circleIcon,
    this.circleAsset,
    required this.title,
    required this.subtitle,
    required this.iconColor,
    required this.iconAccent,
    required this.primaryLabel,
    required this.primaryIcon,
    required this.onPrimary,
  });

  final IconData? circleIcon;
  final String? circleAsset;
  final String title;
  final String subtitle;
  final Color iconColor;
  final Color iconAccent;
  final String primaryLabel;
  final IconData primaryIcon;
  final void Function(BuildContext context) onPrimary;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 116.w,
              height: 116.w,
              decoration: BoxDecoration(
                color: iconColor,
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: iconAccent.withOpacity(0.16),
                    blurRadius: 32,
                    spreadRadius: 4,
                  ),
                ],
              ),
              child: circleAsset != null
                  ? LaundryBasketIcon(size: 48.sp, color: iconAccent)
                  : Icon(circleIcon!, size: 48.sp, color: iconAccent),
            ),
            SizedBox(height: 20.h),
            Text(title,
                style: TextStyle(
                    fontFamily: 'Cairo',
                    fontSize: 17.sp,
                    fontWeight: FontWeight.w900,
                    color: _navy),
                textAlign: TextAlign.center),
            SizedBox(height: 8.h),
            Text(subtitle,
                style: TextStyle(
                    fontFamily: 'Cairo',
                    fontSize: 12.sp,
                    height: 1.6,
                    color: _gray),
                textAlign: TextAlign.center),
            SizedBox(height: 22.h),
            SizedBox(
              height: 46.h,
              child: FilledButton.icon(
                onPressed: () => onPrimary(context),
                icon: Icon(primaryIcon, size: 18),
                style: FilledButton.styleFrom(
                  backgroundColor: _teal,
                  padding: EdgeInsets.symmetric(horizontal: 22.w),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14.r),
                  ),
                ),
                label: Text(primaryLabel,
                    style: TextStyle(
                        fontFamily: 'Cairo',
                        fontWeight: FontWeight.w800,
                        fontSize: 13.sp)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

/// Polished vendor cart group with dashed divider + accent stripe.
class _VendorCartCard extends ConsumerWidget {
  const _VendorCartCard({
    super.key,
    required this.vid,
    required this.name,
    required this.accent,
    required this.items,
    required this.vTotal,
    required this.currency,
    required this.onClear,
  });

  final String vid;
  final String name;
  final Color accent;
  final List<CarItemHiveModel> items;
  final double vTotal;
  final String currency;
  final VoidCallback onClear;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Container(
      margin: EdgeInsets.only(bottom: 12.h),
      clipBehavior: Clip.antiAlias,
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(22.r),
        border: Border.all(color: _border),
        boxShadow: const [
          BoxShadow(
            color: Color(0x140B1E2E),
            blurRadius: 18,
            offset: Offset(0, 8),
          ),
        ],
      ),
      child: Directionality(
        textDirection: TextDirection.rtl,
        child: IntrinsicHeight(
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Accent stripe on the RIGHT in RTL
              Container(width: 5.w, color: accent),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // ===== Store header =====
                    Padding(
                      padding: EdgeInsets.fromLTRB(10.w, 12.h, 14.w, 12.h),
                      child: Row(
                        children: [
                          // Delete button on the LEFT (leading in RTL = visual left)
                          InkWell(
                            onTap: onClear,
                            borderRadius: BorderRadius.circular(10.r),
                            child: Container(
                              padding: EdgeInsets.all(8.w),
                              decoration: BoxDecoration(
                                color: _redSoft,
                                borderRadius: BorderRadius.circular(10.r),
                              ),
                              child: Icon(Icons.delete_outline_rounded,
                                  size: 17.sp, color: _red),
                            ),
                          ),
                          SizedBox(width: 10.w),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(name,
                                    style: TextStyle(
                                        fontFamily: 'Cairo',
                                        fontSize: 13.sp,
                                        fontWeight: FontWeight.w900,
                                        color: _navy),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis),
                                Text('${items.length} عناصر',
                                    style: TextStyle(
                                        fontSize: 10.sp, color: _grayLight)),
                              ],
                            ),
                          ),
                          SizedBox(width: 10.w),
                          // Store icon on the RIGHT in RTL
                          Container(
                            width: 38.w,
                            height: 38.h,
                            decoration: BoxDecoration(
                              color: accent.withOpacity(0.12),
                              borderRadius: BorderRadius.circular(12.r),
                            ),
                            child: Icon(Icons.store_rounded,
                                size: 19.sp, color: accent),
                          ),
                        ],
                      ),
                    ),
                    const _DashedDivider(),

                    // ===== Items =====
                    ...items.map((it) => MyCartItemImageCard(
                        carItemHiveModel: it, vendorId: vid)),

                    // ===== Subtotal =====
                    const _DashedDivider(),
                    Padding(
                      padding: EdgeInsets.fromLTRB(14.w, 8.h, 14.w, 12.h),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('$currency${vTotal.toStringAsFixed(2)}',
                              style: TextStyle(
                                  fontSize: 12.5.sp,
                                  fontWeight: FontWeight.w900,
                                  color: _navy)),
                          Text('المجموع الفرعي',
                              style: TextStyle(
                                  fontSize: 11.sp, color: _gray)),
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
}

class _OrderSummaryCard extends StatelessWidget {
  const _OrderSummaryCard({
    required this.allSubtotal,
    required this.delivery,
    required this.discount,
    required this.grandTotal,
    required this.currency,
    required this.vendorRows,
  });

  final double allSubtotal;
  final double delivery;
  final double discount;
  final double grandTotal;
  final String currency;
  final List<MapEntry<String, double>> vendorRows;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.fromLTRB(16.w, 14.h, 16.w, 16.h),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(22.r),
        border: Border.all(color: _border),
        boxShadow: const [
          BoxShadow(
            color: Color(0x140B1E2E),
            blurRadius: 16,
            offset: Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            Container(
              width: 34.w,
              height: 34.h,
              decoration: BoxDecoration(
                color: const Color(0xFFEFFAF8),
                borderRadius: BorderRadius.circular(11.r),
              ),
              child: const Icon(Icons.receipt_long_rounded,
                  size: 18, color: _teal),
            ),
            SizedBox(width: 10.w),
            Text('ملخص الطلب',
                style: TextStyle(
                    fontFamily: 'Cairo',
                    fontSize: 14.sp,
                    fontWeight: FontWeight.w900,
                    color: _navy)),
          ]),
          SizedBox(height: 12.h),
          ...vendorRows.map((row) => Padding(
                padding: EdgeInsets.only(bottom: 6.h),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('المجموع (${row.key})',
                        style: TextStyle(fontSize: 11.sp, color: _gray)),
                    Text('$currency${row.value.toStringAsFixed(2)}',
                        style: TextStyle(
                            fontSize: 11.sp,
                            fontWeight: FontWeight.w800,
                            color: _slate)),
                  ],
                ),
              )),
          const _DashedDivider(),
          Padding(
            padding: EdgeInsets.symmetric(vertical: 2.h),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('المجموع الفرعي',
                    style: TextStyle(fontSize: 12.sp, color: _gray)),
                Text('$currency${allSubtotal.toStringAsFixed(2)}',
                    style: TextStyle(
                        fontSize: 12.sp,
                        fontWeight: FontWeight.w800,
                        color: _slate)),
              ],
            ),
          ),
          SizedBox(height: 4.h),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('تكلفة التوصيل',
                  style: TextStyle(fontSize: 11.sp, color: _gray)),
              delivery <= 0
                  ? Container(
                      padding: EdgeInsets.symmetric(
                          horizontal: 8.w, vertical: 3.h),
                      decoration: BoxDecoration(
                        color: const Color(0xFFE7F7EE),
                        borderRadius: BorderRadius.circular(8.r),
                      ),
                      child: Text('القيمة الخاصة بالتوصيل تحدد لاحقاً',
                          style: TextStyle(
                              fontSize: 10.5.sp,
                              fontWeight: FontWeight.w800,
                              color: Color(0xFF23A26D))),
                    )
                  : Text('$currency${delivery.toStringAsFixed(2)}',
                      style: TextStyle(
                          fontSize: 11.sp,
                          fontWeight: FontWeight.w700,
                          color: _slate)),
            ],
          ),
          if (discount > 0) ...[
            SizedBox(height: 4.h),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('الخصم',
                    style: TextStyle(fontSize: 11.sp, color: _gray)),
                Text('- $currency${discount.toStringAsFixed(2)}',
                    style: TextStyle(
                        fontSize: 11.sp,
                        fontWeight: FontWeight.w800,
                        color: _tealDark)),
              ],
            ),
          ],
          const _DashedDivider(),
          Padding(
            padding: EdgeInsets.only(top: 2.h),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('الإجمالي',
                    style: TextStyle(
                        fontSize: 15.sp,
                        fontWeight: FontWeight.w900,
                        color: _navy)),
                Text('$currency${grandTotal.toStringAsFixed(2)}',
                    style: TextStyle(
                        fontSize: 17.sp,
                        fontWeight: FontWeight.w900,
                        color: _teal)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _DashedDivider extends StatelessWidget {
  const _DashedDivider();

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 10.h),
      child: Row(
        children: List.generate(
          22,
          (_) => Expanded(
            child: Padding(
              padding: EdgeInsets.symmetric(horizontal: 1.5.w),
              child: Container(
                height: 1.2,
                decoration: BoxDecoration(
                  color: _borderSoft,
                  borderRadius: BorderRadius.circular(2.r),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

/// Gradient full-width primary button used by update-mode (edit order) and,
/// previously, for the removed checkout CTA.
class _GradientButton extends StatelessWidget {
  const _GradientButton({
    required this.onTap,
    required this.label,
    required this.icon,
    this.radius = 16,
  });

  final VoidCallback onTap;
  final String label;
  final IconData icon;
  final double radius;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 50.h,
      width: double.infinity,
      child: DecoratedBox(
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [Color(0xFF028090), Color(0xFF00A896)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(radius),
          boxShadow: const [
            BoxShadow(
              color: Color(0x4700A896),
              blurRadius: 14,
              offset: Offset(0, 7),
            ),
          ],
        ),
        child: ElevatedButton.icon(
          onPressed: onTap,
          icon: Icon(icon, size: 18.sp, color: Colors.white),
          label: Text(label,
              style: const TextStyle(
                  fontFamily: 'Cairo',
                  fontWeight: FontWeight.w900,
                  color: Colors.white)),
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.transparent,
            shadowColor: Colors.transparent,
          ),
        ),
      ),
    );
  }
}

