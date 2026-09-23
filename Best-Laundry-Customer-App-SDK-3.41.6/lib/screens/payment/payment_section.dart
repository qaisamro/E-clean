import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:intl/intl.dart';
import 'package:laundry_customer/constants/app_box_decoration.dart';
import 'package:laundry_customer/constants/app_text_decor.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/screens/payment/payment_screen.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/buttons/full_width_button.dart';
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

  bool isMakingPayment = false;

  bool isPaid = false;

  @override
  Widget build(BuildContext context) {
    ref.watch(couponProvider).maybeWhen(
          orElse: () {},
          loaded: (_) {
            couponID = _.data?.coupon?.id;
          },
        );
    int? minimum = 0;
    double? dlvrychrg = 0;
    double? free = 0;
    ref.watch(settingsProvider).whenOrNull(
      loaded: (data) {
        if (data.data != null &&
            data.data!.deliveryCost != null &&
            data.data!.feeCost != null) {
          minimum = data.data!.minimumCost;
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
              error: (_) {
                EasyLoading.showError(_);
                ref.refresh(couponProvider);
              },
              loaded: (_) {
                if (_.data?.coupon?.discount != null) {
                  if (_.data!.coupon!.type!.toLowerCase() ==
                      "percent".toLowerCase()) {
                    final double subToatalAmount = calculateTotal(
                      cartItems,
                    );
                    Future.delayed(buildDuration).then((value) {
                      ref
                              .watch(
                                discountAmountProvider.notifier,
                              )
                              .state =
                          subToatalAmount * (_.data!.coupon!.discount! / 100);
                    });
                  } else {
                    Future.delayed(buildDuration).then((value) {
                      ref
                          .watch(
                            discountAmountProvider.notifier,
                          )
                          .state = _.data!.coupon!.discount!.toDouble();
                    });
                  }
                }
              },
            );
        return Container(
          width: 375.w,
          margin: EdgeInsets.only(
            top: 20.h,
          ),
          padding: EdgeInsets.symmetric(
            horizontal: 20.w,
            vertical: 25.h,
          ),
          decoration: AppBoxDecorations.pageCommonCard.copyWith(
            borderRadius: const BorderRadius.vertical(
              top: Radius.circular(20),
            ),
            border: Border(
              top: BorderSide(
                color: Colors.grey.withOpacity(0.2),
              ),
            ),
          ),
          child: SizedBox(
            // height: 70.h,
            child: Consumer(
              builder: (context, ref, child) {
                return ref.watch(placeOrdersProvider).map(
                      initial: (_) => Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                            ),
                          ),
                          ref.watch(couponProvider).maybeWhen(
                                orElse: () => const SizedBox(),
                                loaded: (_) => GestureDetector(
                                  onTap: () {
                                    ref.refresh(couponProvider);
                                    ref.refresh(
                                      discountAmountProvider,
                                    );
                                  },
                                  child: SizedBox(
                                    height: 20.h,
                                    width: 335.w,
                                    child: Row(
                                      mainAxisAlignment: MainAxisAlignment.end,
                                      children: [
                                        Text(
                                          '${S.of(context).removeCoupon} ${_.data?.coupon?.code ?? ''}',
                                          style: AppTextDecor.osRegular12red,
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                          AppSpacerH(24.h),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                S.of(context).ttlpybl,
                                style: AppTextDecor.osSemiBold18black,
                              ),
                              if (AppGFunctions.calculateTotal(
                                    cartItems,
                                  ).toInt() <
                                  free!) ...[
                                Text(
                                  '${appSettingsBox.get('currency') ?? '\$'}${(AppGFunctions.calculateTotal(cartItems) + dlvrychrg! - ref.watch(discountAmountProvider)).toStringAsFixed(2)}',
                                  style: AppTextDecor.osSemiBold18black,
                                ),
                              ] else ...[
                                Text(
                                  '${appSettingsBox.get('currency') ?? '\$'}${(AppGFunctions.calculateTotal(cartItems) - ref.watch(discountAmountProvider)).toStringAsFixed(2)}',
                                  style: AppTextDecor.osSemiBold18black,
                                ),
                              ],
                              // Text(
                              //   '${appSettingsBox.get('currency') ?? '\$'}${(calculateTotal(cartItems) + dlvrychrg! - ref.watch(discountAmountProvider)).toStringAsFixed(2)}',
                              //   style: AppTextDecor.osSemiBold18black,
                              // ),
                            ],
                          ),
                          AppSpacerH(10.h),
                          if (isMakingPayment)
                            const Center(
                              child: CircularProgressIndicator(),
                            )
                          else
                            AppTextButton(
                              title: S.of(context).pynw,
                              onTap: () async {
                                /*
                                      Order Placement Data Validation and Logic Processed Here
                                      */
                                final DateFormat formatter = DateFormat(
                                  'yyyy-MM-dd',
                                );
                                final isOrderProcessing =
                                    ref.watch(orderProcessingProvider);

                                if (!isOrderProcessing) {
                                  ref
                                      .watch(
                                        orderProcessingProvider.notifier,
                                      )
                                      .state = true;
                                  final pickUp = ref.watch(
                                    scheduleProvider('Pick Up'),
                                  );
                                  final delivery = ref.watch(
                                    scheduleProvider(
                                      'Delivery',
                                    ),
                                  );
                                  final address = ref.watch(
                                    addressIDProvider,
                                  );

                                  //Cheks All Reguired Data Is AvailAble
                                  if (pickUp != null &&
                                      delivery != null &&
                                      address != '' &&
                                      cartItems.isNotEmpty) {
                                    //Has All Data

                                    final selectedPaymentType =
                                        widget.selectedPaymentType.name;

                                    await ref
                                        .watch(
                                          placeOrdersProvider.notifier,
                                        )
                                        .addOrder(
                                          OrderPlaceModel(
                                            address_id: address,
                                            pick_date:
                                                "${pickUp.dateTime.year}-${pickUp.dateTime.month}-${pickUp.dateTime.day}",
                                            pick_hour:
                                                pickUp.schedule.hour.toString(),
                                            delivery_date:
                                                "${delivery.dateTime.year}-${delivery.dateTime.month}-${delivery.dateTime.day}",
                                            delivery_hour: delivery
                                                .schedule.hour
                                                .toString(),
                                            coupon_id:
                                                couponID?.toString() ?? '',
                                            instruction:
                                                widget.instruction.text,
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
                                            paymentType: selectedPaymentType,
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
                          // context.nav.pushNamedAndRemoveUntil(
                          //   Routes.orderSuccessScreen,
                          //   arguments: {
                          //     'id': _.data.data!.order!.orderCode,
                          //     'amount': amount,
                          //     'couponID': couponID.toString(),
                          //     'isCOD': selectedPaymentType == PaymentType.cod
                          //   },
                          //   (route) => false,
                          // );

                          if (widget.selectedPaymentType == PaymentType.cash ||
                              isPaid) {
                            context.nav.pushNamedAndRemoveUntil(
                              Routes.orderSuccessScreen,
                              arguments: {
                                'id': _.data.data!.order!.orderCode,
                                'amount': amount,
                                'couponID': couponID.toString(),
                                'isCOD': widget.selectedPaymentType ==
                                    PaymentType.cash,
                              },
                              (route) => false,
                            );
                          } else {
                            context.nav.pushNamedAndRemoveUntil(
                              Routes.orderSuccessScreen,
                              arguments: {
                                'id': _.data.data!.order!.orderCode,
                                'amount': amount,
                                'couponID': couponID.toString(),
                                'isCOD': false,
                              },
                              (route) => false,
                            );
                          }
                            // if (!isMakingPayment) {
                            //   isMakingPayment = true;
                            //   final PaymentController pay = PaymentController();

                            //   isPaid = await pay.makePayment(
                            //     amount: amount,
                            //     currency: 'GBP',
                            //     couponID: couponID.toString(),
                            //     orderID: _.data.data!.order!.orderCode!,
                            //   );

                            //   isMakingPayment = false;
                            //   setState(() {});

                            //   if (isPaid) {
                            //     context.nav.pushNamedAndRemoveUntil(
                            //       Routes.orderSuccessScreen,
                            //       arguments: {
                            //         'id': _.data.data!.order!.orderCode,
                            //         'amount': amount,
                            //         'couponID': couponID.toString(),
                            //         'isCOD': widget.selectedPaymentType ==
                            //             PaymentType.cod,
                            //         "isPaidOnline": true,
                            //       },
                            //       (route) => false,
                            //     );
                            //   } else {
                            //     context.nav.pushNamedAndRemoveUntil(
                            //       Routes.orderSuccessScreen,
                            //       arguments: {
                            //         'id': _.data.data!.order!.orderCode,
                            //         'amount': amount,
                            //         'couponID': couponID.toString(),
                            //         'isCOD': widget.selectedPaymentType ==
                            //             PaymentType.cod,
                            //         "isPaidOnline": false,
                            //       },
                            //       (route) => false,
                            //     );
                            //   }
                            // }
                            // print("Paid : $isPaid");
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
