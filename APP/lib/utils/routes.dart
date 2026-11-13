import 'package:flutter/material.dart';
import 'package:laundry_customer/misc/misc_global_variables.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/screens/address/add_update_address.dart';
import 'package:laundry_customer/screens/address/manage_address_screen.dart';
import 'package:laundry_customer/screens/auth/login_screen.dart';
import 'package:laundry_customer/screens/auth/join_request_waiting_screen.dart';
import 'package:laundry_customer/screens/auth/password_recovery.dart';
import 'package:laundry_customer/screens/auth/password_recovery_stage_three.dart';
import 'package:laundry_customer/screens/auth/password_recovery_stage_two.dart';
import 'package:laundry_customer/screens/auth/post_code_check_screen.dart';
import 'package:laundry_customer/screens/auth/pot_code_check_success.dart';
import 'package:laundry_customer/screens/auth/sign_up_otp_verification.dart';
import 'package:laundry_customer/screens/auth/sign_up_screen.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/screens/homePage/choose_items.dart';
import 'package:laundry_customer/screens/homePage/create_password_screen.dart';
import 'package:laundry_customer/screens/homePage/edit_profile.dart';
import 'package:laundry_customer/screens/homePage/home_screen.dart';
import 'package:laundry_customer/screens/store/vendor_store_screen.dart';
import 'package:laundry_customer/screens/onBoarding/splash_screen.dart';
import 'package:laundry_customer/screens/order/delivery_schedule_picker.dart';
import 'package:laundry_customer/screens/order/my_order_home_tile.dart';
import 'package:laundry_customer/screens/order/order_details_page.dart';
import 'package:laundry_customer/screens/order/schedule_picker.dart';
import 'package:laundry_customer/screens/other/about_us.dart';
import 'package:laundry_customer/screens/other/contact_us.dart';
import 'package:laundry_customer/screens/other/privacy_policy.dart';
import 'package:laundry_customer/screens/other/terms_of_service.dart';
import 'package:laundry_customer/screens/payment/order_success.dart';
import 'package:laundry_customer/screens/payment/payment_screen.dart';
import 'package:page_transition/page_transition.dart';

class Routes {
  /*We are mapping all th eroutes here
  so that we can call any named route
  without making typing mistake
  */
  Routes._();
  //core
  static const splash = '/';
  static const onBoarding = '/onBoarding';
  static const loginScreen = '/loginScreen';
  static const postcodecheckScreen = '/postcodecheckScreen';
  static const postcodesuccessScreen = '/postcodesuccessScreen';
  static const recoverPassWordStageOne = '/recoverPassWordStageOne';
  static const recoverPassWordStageTwo = '/recoverPassWordStageTwo';
  static const recoverPassWordStageThree = '/recoverPassWordStageThree';
  static const signUpScreen = '/signUpScreen';
  static const signUpOtpScreen = '/signUpOtpScreen';
  static const joinRequestWaitingScreen = '/joinRequestWaitingScreen';
  static const homeScreen = '/homeScreen';
  static const orderDetails = '/orderDetails';
  // static const messageScreen = '/messageScreen';
  static const checkOutScreen = '/checkOutScreen';
  static const orderSuccessScreen = '/orderSuccessScreen';

  //Functionality
  static const chooseItemScreen = '/chooseItemScreen';
  //Functionality
  static const manageAddressScreen = '/manageAddressScreen';
  static const addOrUpdateAddressScreen = '/addOrUpdateAddressScreen';
  //others
  static const privacyPolicyScreen = '/privacyPolicyScreen';
  static const termsOfServiceScreen = '/termsOfServiceScreen';
  static const aboutUsScreen = '/aboutUsScreen';
  static const contactUsScreen = '/contactUsScreen';
  static const changePasswordScreen = '/changePasswordScreen';
  static const schedulePickerScreen = '/schedulePickerScreen';
  static const deilverySchedulePickerScreen = '/deilverySchedulePickerScreen';

  static const editProfileScreen = '/editProfileScreen';
  static const vendorStoreScreen = '/vendorStoreScreen';
}

Route generatedRoutes(RouteSettings settings) {
  Widget child;

  switch (settings.name) {
    //core
    case Routes.splash:
      child = const SplashScreen();

    case Routes.loginScreen:
      child = const LoginScreen();
    case Routes.postcodecheckScreen:
      child = const PostCodeCheckScreen();
    case Routes.changePasswordScreen:
      child = const CreatePasswordScreen();
    case Routes.postcodesuccessScreen:
      child = const PostCodeCheckSuccess();
    case Routes.recoverPassWordStageOne:
      child = RecoverPasswordStageOne();
    case Routes.recoverPassWordStageTwo:
      child = RecoverPasswordStageTwo(
        forEmailorPhone: settings.arguments! as String,
      );
    case Routes.recoverPassWordStageThree:
      child = RecoverPasswordStageThree(
        token: settings.arguments! as String,
      );
    case Routes.signUpScreen:
      child = const SignUpScreen();
    case Routes.signUpOtpScreen:
      final dynamic otpArgs = settings.arguments;
      String otpContact = '';
      String otpMode = 'register';
      if (otpArgs is String) {
        otpContact = otpArgs;
      } else if (otpArgs is Map) {
        otpContact = otpArgs['contact']?.toString() ?? '';
        otpMode = otpArgs['mode']?.toString() ?? 'register';
      }
      child = SignUpOtpVerification(
        forEmailorPhone: otpContact,
        mode: otpMode,
      );
    case Routes.joinRequestWaitingScreen:
      child = JoinRequestWaitingScreen(
        args: settings.arguments is JoinRequestWaitingArgs
            ? settings.arguments! as JoinRequestWaitingArgs
            : JoinRequestWaitingArgs(
                contact: (settings.arguments as Map?)?['contact']?.toString() ??
                    '',
                trackingToken: (settings.arguments as Map?)?['tracking_token']
                        ?.toString() ??
                    '',
              ),
      );
    case Routes.homeScreen:
      child = const HomeScreen();
    case Routes.orderDetails:
      final DetailsArg arg = settings.arguments! as DetailsArg;
      child = OrderDetails(
        orderID: arg.orderId,
        orderStatus: arg.orderStatus,
      );
    // case Routes.messageScreen:
    //   final messageScreenArgument = settings.arguments as MessageScreenArgument;
    //   child = MessageScreen(
    //     messageScreenArgument: messageScreenArgument,
    //   );
    // break;
    case Routes.checkOutScreen:
      child = const CheckOutScreen();
    case Routes.orderSuccessScreen:
      child = OrderSuccessScreen(
        details: settings.arguments! as Map<String, dynamic>,
      );
    //Functionality
    case Routes.chooseItemScreen:
      if (settings.arguments is Map<String, dynamic>) {
        final map = settings.arguments! as Map<String, dynamic>;
        child = ChooseItems(
          service: map['service'] as Service,
          vendorId: map['vendorId'] as String?,
          vendorName: map['vendorName'] as String?,
        );
      } else {
        child = ChooseItems(
          service: settings.arguments! as Service,
        );
      }
    case Routes.vendorStoreScreen:
      child = VendorStoreScreen(
        vendor: settings.arguments! as Vendor,
      );
    //Functionality
    case Routes.manageAddressScreen:
      child = const ManageAddressScreen();
    case Routes.addOrUpdateAddressScreen:
      child = AddOrEditAddress(
        address: settings.arguments as Address?,
      );
    //Others
    case Routes.privacyPolicyScreen:
      child = const PrivacyPolicy();
    case Routes.termsOfServiceScreen:
      child = const TermsOfService();
    case Routes.aboutUsScreen:
      child = const AboutUs();
    case Routes.contactUsScreen:
      child = const ContactUs();
    case Routes.schedulePickerScreen:
      child = const SchedulerPicker();
    case Routes.deilverySchedulePickerScreen:
      child = const DeliverySchedulerPicker();
    //Card
    case Routes.editProfileScreen:
      child = const EditProfilePage();

    default:
      throw Exception('Invalid route: ${settings.name}');
  }

  return PageTransition(
    child: child,
    type: PageTransitionType.fade,
    settings: settings,
    duration: transissionDuration,
    reverseDuration: transissionDuration,
  );
}
