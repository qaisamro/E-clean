import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/app_text_decor.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/global_functions.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

class HomeTabCard extends ConsumerWidget {
  const HomeTabCard({
    super.key,
    required this.service,
    this.ontap,
  });
  final Service service;
  final Function()? ontap;

  double _categoryFontSize(int textLength) {
    if (textLength > 30) return 7.5.sp;
    if (textLength > 22) return 8.5.sp;
    return 10.sp;
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    ref.watch(servicesVariationsProvider(service.id!.toString()));
    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.appSettingsBox).listenable(),
      builder: (BuildContext context, Box appSettingsBox, Widget? child) {
        final selectedLocal = appSettingsBox.get(AppHSC.appLocal);
        return GestureDetector(
          onTap: ontap,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              ClipRRect(
                borderRadius: BorderRadius.circular(5.h),
                child: SizedBox(
                  height: 60.h,
                  width: 60.h,
                  child: AppNetworkImage(
                    service.imagePath,
                    fit: BoxFit.cover,
                  ),
                ),
              ),
              AppSpacerH(5.h),
              SizedBox(
                width: 92.w,
                child: Text(
                  getLng(
                    en: service.name,
                    changeLang: service.nameBn.toString(),
                  ),
                  style: AppTextDecor.osSemiBold10black.copyWith(
                    fontSize: _categoryFontSize(
                      getLng(
                        en: service.name,
                        changeLang: service.nameBn.toString(),
                      ).length,
                    ),
                  ),
                  maxLines: 2,
                  textAlign: TextAlign.center,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
