import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:intl/intl.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/address_book_provider.dart';
import 'package:laundry_customer/screens/address/manage_address_screen.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/screens/payment/payment_screen.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

// ignore: must_be_immutable
class PaymentSection extends ConsumerStatefulWidget {
  const PaymentSection({
    super.key,
    required this.instruction,
    required this.selectedPaymentType,
  });
  final TextEditingController instruction;
  final PaymentType selectedPaymentType;

  @override
  ConsumerState<PaymentSection> createState() => _PaymentSectionState();
}

class _PaymentSectionState extends ConsumerState<PaymentSection> {
  final Box appSettingsBox = Hive.box(AppHSC.appSettingsBox);
  final TextEditingController coupon = TextEditingController();

  int? couponID;

  String get _currency => (appSettingsBox.get('currency') ?? '\$').toString();

  double _calculateDeliveryFee(List<CarItemHiveModel> cartItems,
      {required double freeThreshold, required double fee}) {
    if (AppGFunctions.calculateTotal(cartItems).toInt() < freeThreshold) {
      return fee;
    }
    return 0.0;
  }

  @override
  void dispose() {
    coupon.dispose();
    super.dispose();
  }

  Widget _summaryRow(String title, String value,
      {bool isBold = false, Color? valueColor}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          title,
          style: TextStyle(
            fontSize: 13.sp,
            color: const Color(0xFF64748B),
            fontWeight: FontWeight.w600,
            fontFamily: 'Cairo',
          ),
        ),
        Text(
          value,
          style: TextStyle(
            fontSize: 13.sp,
            color: valueColor ?? const Color(0xFF0B1E2E),
            fontWeight: isBold ? FontWeight.w900 : FontWeight.w700,
            fontFamily: 'Cairo',
          ),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    ref.watch(couponProvider).maybeWhen(
          orElse: () {},
          loaded: (_) {
            couponID = _.data?.coupon?.id;
          },
        );
    double? dlvrychrg = 0;
    double? free = 0;
    ref.watch(settingsProvider).whenOrNull(
      loaded: (data) {
        if (data.data != null &&
            data.data!.deliveryCost != null &&
            data.data!.feeCost != null) {
          dlvrychrg = data.data!.deliveryCost!.toDouble();
          free = data.data!.feeCost!.toDouble();
        }
      },
    );
    // Vendor-scoped cart: if activeVendorId is set, show only that vendor's cart
    final String? activeVendorId = ref.watch(activeVendorIdProvider);
    ref.watch(vendorCartsProvider);
    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.cartBox).listenable(),
      builder: (
        BuildContext context,
        Box cartBox,
        Widget? child,
      ) {
        final List<CarItemHiveModel> cartItems = activeVendorId != null
            ? CartHelper.getCartForVendor(activeVendorId)
            : CartHelper.getAllItems();
        ref.watch(couponProvider).maybeWhen(
              orElse: () {},
              loaded: (_) {
                if (_.data?.coupon?.discount != null) {
                  if (_.data!.coupon!.type!.toLowerCase() ==
                      "percent".toLowerCase()) {
                    final double subToatalAmount = calculateTotal(
                      cartItems,
                    );
                    Future.delayed(buildDuration).then((value) {
                      ref.read(discountAmountProvider.notifier).state =
                          subToatalAmount *
                              (_.data!.coupon!.discount!.toDouble() / 100);
                    });
                  } else {
                    Future.delayed(buildDuration).then((value) {
                      ref.read(discountAmountProvider.notifier).state =
                          _.data!.coupon!.discount!.toDouble();
                    });
                  }
                }
              },
            );
        final double subtotal = AppGFunctions.calculateTotal(cartItems);
        final double deliveryFee = _calculateDeliveryFee(
          cartItems,
          freeThreshold: free ?? 0,
          fee: dlvrychrg ?? 0,
        );
        final double discount = ref.watch(discountAmountProvider);
        final double grandTotal = subtotal + deliveryFee - discount;
        return Container(
          width: double.infinity,
          padding: EdgeInsets.fromLTRB(20.w, 16.h, 20.w, 22.h),
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
            border: Border(
              top: BorderSide(color: Color(0xFFE2E8F0)),
            ),
            boxShadow: [
              BoxShadow(
                color: Color(0x1A0B1E2E),
                blurRadius: 26,
                offset: Offset(0, -8),
              ),
            ],
          ),
          child: Consumer(
            builder: (context, ref, child) {
              return ref.watch(placeOrdersProvider).map(
                    initial: (_) => Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        // ===== عنوان الفاتورة =====
                        Row(
                          children: [
                            Container(
                              width: 36.w,
                              height: 36.h,
                              decoration: const BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [
                                    Color(0xFF028090),
                                    Color(0xFF00A896),
                                  ],
                                  begin: Alignment.topRight,
                                  end: Alignment.bottomLeft,
                                ),
                                shape: BoxShape.circle,
                                boxShadow: [
                                  BoxShadow(
                                    color: Color(0x3300A896),
                                    blurRadius: 10,
                                    offset: Offset(0, 4),
                                  ),
                                ],
                              ),
                              child: const Icon(
                                Icons.receipt_long_rounded,
                                size: 18,
                                color: Colors.white,
                              ),
                            ),
                            SizedBox(width: 10.w),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    S.of(context).ttlpybl,
                                    style: TextStyle(
                                      fontSize: 15.5.sp,
                                      fontWeight: FontWeight.w900,
                                      color: const Color(0xFF0B1E2E),
                                      fontFamily: 'Cairo',
                                    ),
                                  ),
                                  Text(
                                    'المجموع النهائي للفاتورة',
                                    style: TextStyle(
                                      fontSize: 10.sp,
                                      fontWeight: FontWeight.w600,
                                      color: const Color(0xFF64748B),
                                      fontFamily: 'Cairo',
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            // كوبونه مطبق — شريحة حذف مدمجة
                            ref.watch(couponProvider).maybeWhen(
                                  orElse: () => const SizedBox.shrink(),
                                  loaded: (state) =>
                                      state.data?.coupon?.code != null
                                          ? Container(
                                              padding: const EdgeInsets.symmetric(
                                                  horizontal: 10,
                                                  vertical: 6),
                                              decoration: BoxDecoration(
                                                color: const Color(0xFFEFFAF8),
                                                borderRadius:
                                                    BorderRadius.circular(10.r),
                                                border: Border.all(
                                                    color: const Color(0xFF00A896)
                                                        .withOpacity(0.35)),
                                              ),
                                              child: Row(
                                                mainAxisSize:
                                                    MainAxisSize.min,
                                                children: [
                                                  const Icon(
                                                    Icons.sell_rounded,
                                                    size: 13,
                                                    color: Color(0xFF028090),
                                                  ),
                                                  const SizedBox(width: 4),
                                                  Text(
                                                    state.data!.coupon!.code!,
                                                    style: TextStyle(
                                                      fontSize: 11.sp,
                                                      fontWeight: FontWeight.w900,
                                                      color: const Color(
                                                          0xFF028090),
                                                      fontFamily: 'Cairo',
                                                    ),
                                                  ),
                                                  const SizedBox(width: 4),
                                                  InkWell(
                                                    borderRadius:
                                                        BorderRadius.circular(8),
                                                    onTap: () {
                                                      ref.refresh(
                                                          couponProvider);
                                                      ref.refresh(
                                                          discountAmountProvider);
                                                    },
                                                    child: const Icon(
                                                      Icons.close_rounded,
                                                      size: 14,
                                                      color: Color(0xFF64748B),
                                                    ),
                                                  ),
                                                ],
                                              ),
                                            )
                                          : const SizedBox.shrink(),
                                ),
                          ],
                        ),
                        const SizedBox(height: 16),

                        // ===== السطور =====
                        _summaryRow(
                            'المجموع الفرعي',
                            '$_currency${subtotal.toStringAsFixed(2)}'),
                        const SizedBox(height: 9),
                        _summaryRow(
                          'التوصيل',
                          deliveryFee == 0
                              ? 'القيمة الخاصة بالتوصيل تحدد لاحقاً'
                              : '$_currency${deliveryFee.toStringAsFixed(2)}',
                          isBold: true,
                          valueColor:
                              deliveryFee == 0 ? const Color(0xFF00A896) : null,
                        ),
                        if (discount > 0) ...[
                          const SizedBox(height: 9),
                          _summaryRow(
                              'الخصم',
                              '- $_currency${discount.toStringAsFixed(2)}',
                              isBold: true,
                              valueColor: const Color(0xFF00A896)),
                        ],

                        const SizedBox(height: 15),
                        const Divider(height: 1, color: Color(0xFFF1F5F9)),
                        const SizedBox(height: 12),

                        // ===== الإجمالي النهائي =====
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'الإجمالي',
                                  style: TextStyle(
                                    fontSize: 13.sp,
                                    fontWeight: FontWeight.w800,
                                    color: const Color(0xFF1E293B),
                                    fontFamily: 'Cairo',
                                  ),
                                ),
                                Text(
                                  '+ التوصيل عند الاستلام',
                                  style: TextStyle(
                                    fontSize: 9.5.sp,
                                    fontWeight: FontWeight.w600,
                                    color: const Color(0xFF94A3B8),
                                    fontFamily: 'Cairo',
                                  ),
                                ),
                              ],
                            ),
                            RichText(
                              text: TextSpan(
                                text:
                                    '$_currency${grandTotal.toStringAsFixed(2)}',
                                style: const TextStyle(
                                  fontFamily: 'Cairo',
                                  fontWeight: FontWeight.w900,
                                  fontSize: 24,
                                  color: Color(0xFF028090),
                                  height: 1,
                                ),
                              ),
                            ),
                          ],
                        ),

                        const SizedBox(height: 18),

                        // ===== تأكيد الطلب =====
                        SizedBox(
                          width: double.infinity,
                          height: 58.h,
                          child: DecoratedBox(
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [
                                  Color(0xFF028090),
                                  Color(0xFF00A896)
                                ],
                                begin: Alignment.topRight,
                                end: Alignment.bottomLeft,
                              ),
                              borderRadius:
                                  BorderRadius.circular(18.r),
                              boxShadow: [
                                BoxShadow(
                                  color: const Color(0xFF028090)
                                      .withOpacity(0.32),
                                  blurRadius: 16,
                                  offset: const Offset(0, 8),
                                ),
                              ],
                            ),
                            child: ElevatedButton.icon(
                              onPressed: () async {
                                /*
                                        Order Placement Data Validation and Logic Processed Here
                                        */
                                final DateFormat formatter = DateFormat(
                                  'yyyy-MM-dd',
                                  'en', // Latin digits for the API (iOS ar locale fix)
                                );
                                final isOrderProcessing =
                                    ref.watch(orderProcessingProvider);

                                if (!isOrderProcessing) {
                                  ref
                                      .watch(
                                        orderProcessingProvider.notifier,
                                      )
                                      .state = true;
                                  final pickUp =
                                      ref.read(scheduleProvider('Pick Up'));
                                  final delivery = ref.read(
                                    scheduleProvider('Delivery'),
                                  );
                                  final address = ref.read(addressIDProvider);
                                  final addressBook =
                                      ref.read(addressBookProvider);
                                  final addressIsAvailable = !addressBook
                                          .loading &&
                                      !addressBook.busy &&
                                      addressBook.error == null &&
                                      selectedOwnedAddress(
                                              addressBook.addresses, address) !=
                                          null;

                                  //Cheks All Reguired Data Is AvailAble
                                  if (pickUp != null &&
                                      delivery != null &&
                                      (pickUp.vendorId == null ||
                                          pickUp.vendorId == activeVendorId) &&
                                      (delivery.vendorId == null ||
                                          delivery.vendorId == activeVendorId) &&
                                      address != '' &&
                                      addressIsAvailable &&
                                      cartItems.isNotEmpty) {
                                    //Has All Data

                                    //Orders are cash-on-delivery only: the
                                    //backend expects payment_type=cash.
                                    await ref
                                        .watch(
                                          placeOrdersProvider.notifier,
                                        )
                                        .addOrder(
                                          OrderPlaceModel(
                                            address_id: address,
                                            pick_date:
                                                formatter.format(pickUp.dateTime),
                                            pick_hour:
                                                pickUp.schedule.hour.toString(),
                                            delivery_date: formatter.format(
                                                delivery.dateTime),
                                            delivery_hour:
                                                delivery.schedule.hour.toString(),
                                            coupon_id: couponID?.toString() ?? '',
                                            instruction: widget.instruction.text,
                                            products: cartItems
                                                .map(
                                                  (e) => OrderProductModel(
                                                    id: e.productsId.toString(),
                                                    quantity: e.productsQTY
                                                        .toString(),
                                                    subid: e.subProduct?.id
                                                        .toString(),
                                                  ),
                                                )
                                                .toList(),
                                            additional_service_id: [],
                                            paymentType: 'cash',
                                            vendorId: activeVendorId,
                                          ),
                                        );
                                  } else {
                                    //Missing Data
                                    EasyLoading.showError(
                                      S.of(context).plsslctalflds,
                                    );
                                  }
                                  ref
                                      .watch(orderProcessingProvider.notifier)
                                      .state = false;
                                } else {
                                  EasyLoading.showError(
                                    S.of(context).wrprcsngprvsdlvry,
                                  );
                                }
                              },
                              icon: const Icon(
                                Icons.check_circle_outline_rounded,
                                size: 22,
                                color: Colors.white,
                              ),
                              label: Text(
                                S.of(context).pynw,
                                style: const TextStyle(
                                  fontFamily: 'Cairo',
                                  fontWeight: FontWeight.w900,
                                  fontSize: 17,
                                  color: Colors.white,
                                ),
                              ),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.transparent,
                                shadowColor: Colors.transparent,
                                shape: RoundedRectangleBorder(
                                  borderRadius:
                                      BorderRadius.circular(18.r),
                                ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                    loading: (_) => const LoadingWidget(),
                    loaded: (_) {
                      final String amount = (calculateTotal(cartItems) -
                              ref.watch(
                                discountAmountProvider,
                              ))
                          .toStringAsFixed(2);

                      Future.delayed(buildDuration).then((value) async {
                        // EasyLoading.showSuccess(
                        //   S.of(context).ordrplcd,
                        // );

                        if (activeVendorId != null) {
                          CartHelper.clearVendorCart(activeVendorId);
                          ref.refreshVendorCarts();
                        } else {
                          CartHelper.clearAll();
                          ref.refreshVendorCarts();
                        }
                        ref.refresh(placeOrdersProvider);
                        ref.refresh(couponProvider);
                        ref.refresh(
                          discountAmountProvider,
                        );
                        ref
                            .watch(
                              dateProvider('Pick Up').notifier,
                            )
                            .state = null;
                        ref
                            .watch(
                              dateProvider('Delivery').notifier,
                            )
                            .state = null;
                        ref
                            .watch(
                              scheduleProvider('Pick Up').notifier,
                            )
                            .state = null;
                        ref
                            .watch(
                              scheduleProvider('Delivery').notifier,
                            )
                            .state = null;
                        // Cash on delivery: the order is confirmed as soon
                        // as it is placed - no online gateway involved.
                        context.nav.pushNamedAndRemoveUntil(
                          Routes.orderSuccessScreen,
                          arguments: {
                            'id': _.data.data!.order!.orderCode,
                            'amount': amount,
                            'couponID': couponID.toString(),
                            'isCOD': true,
                          },
                          (route) => false,
                        );
                      });
                      return MessageTextWidget(
                        msg: S.of(context).ordrplcd,
                      );
                      // return Text("Text");
                    },
                    error: (_) {
                      Future.delayed(
                        const Duration(),
                      ).then((value) {
                        ref.refresh(placeOrdersProvider);
                      });
                      return const ErrorTextWidget(
                        error: '',
                      );
                    },
                  );
            },
          ),
        );
      },
    );
  }

  double calculateTotal(List<CarItemHiveModel> cartItems) {
    double amount = 0;
    for (final element in cartItems) {
      if (element.subProduct != null) {
        amount += element.productsQTY *
            (element.unitPrice + element.subProduct!.price!);
      } else {
        amount += element.productsQTY * element.unitPrice;
      }
    }

    return amount;
  }
}
