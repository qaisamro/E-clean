import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/models/offers_model/offers_model.dart';
import 'package:laundry_customer/models/promotions_model/promotion.dart';
import 'package:laundry_customer/screens/homePage/choose_items.dart';
import 'package:laundry_customer/screens/store/vendor_store_screen.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/store_card.dart';
import 'package:laundry_customer/widgets/vendor_search_bar.dart';
import 'package:laundry_customer/widgets/misc_widgets.dart';

// This is the new HomeTab design inspired by the provided HTML
// Colors from HTML: #195e77, #0d3645, #64a89b, #f5f5f5
class NewHomeTab extends ConsumerStatefulWidget {
  const NewHomeTab({super.key});
  @override
  ConsumerState<NewHomeTab> createState() => _NewHomeTabState();
}

class _NewHomeTabState extends ConsumerState<NewHomeTab> {
  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: Container(
        color: const Color(0xFFF5F5F5),
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            // Greeting Header - as in HTML
            Container(
              color: Colors.white,
              padding: EdgeInsets.symmetric(horizontal: 20.w, vertical: 16.h),
              child: Row(
                children: [
                  Container(
                    width: 48.w,
                    height: 48.h,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [Color(0xFF156172), Color(0xFF1b758a)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                      borderRadius: BorderRadius.circular(12.r),
                    ),
                    child: const Icon(Icons.waving_hand_rounded,
                        color: Colors.white, size: 26),
                  ),
                  SizedBox(width: 12.w),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        padding: EdgeInsets.symmetric(
                            horizontal: 8.w, vertical: 2.h),
                        decoration: BoxDecoration(
                          color: Color(0xFFEAF6F8),
                          borderRadius: BorderRadius.circular(20.r),
                          border: Border.all(
                              color: Color(0xFF1b758a).withOpacity(0.3)),
                        ),
                        child: Text("أهلاً بك معنا",
                            style: TextStyle(
                                fontSize: 11.sp,
                                color: Color(0xFF156172),
                                fontWeight: FontWeight.w600)),
                      ),
                      SizedBox(height: 4.h),
                      Text(
                        Hive.box(AppHSC.authBox).get('token') != null
                            ? Hive.box(AppHSC.userBox)
                                    .get('name')
                                    ?.toString() ??
                                S.of(context).hello
                            : "برجاء تسجيل الدخول",
                        style: TextStyle(
                            fontSize: 18.sp,
                            fontWeight: FontWeight.w800,
                            color: Colors.black87),
                      ),
                    ],
                  ),
                  const Spacer(),
                  IconButton(
                    onPressed: () {},
                    icon: const Icon(Icons.login, color: Color(0xFF156172)),
                    style: IconButton.styleFrom(
                        backgroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12.r),
                            side: const BorderSide(color: Color(0xFFE5E7EB)))),
                  ),
                ],
              ),
            ),
            Consumer(
              builder: (context, ref, child) {
                return ref.watch(allPromotionsProvider).map(
                      initial: (_) => _heroBanner(null),
                      loading: (_) => _heroBanner(null),
                      error: (_) => _heroBanner(null),
                      loaded: (state) {
                        final promotions =
                            state.data.data?.promotions ?? <Promotion>[];
                        return _heroBanner(
                            promotions.isEmpty ? null : promotions.first);
                      },
                    );
              },
            ),
            // Services Section
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("الخدمات الرئيسية",
                              style: TextStyle(
                                  fontSize: 16.sp,
                                  fontWeight: FontWeight.bold)),
                          Text("اختر فئتك المفضلة للتسوق الفوري",
                              style: TextStyle(
                                  fontSize: 12.sp, color: Colors.grey)),
                        ],
                      ),
                      TextButton(
                          onPressed: () {},
                          child: const Text("عرض الكل",
                              style: TextStyle(
                                  color: Color(0xFF156172),
                                  fontWeight: FontWeight.bold,
                                  fontSize: 12))),
                    ],
                  ),
                  SizedBox(height: 12.h),
                  // Real services from all vendors - unique, single swipeable row
                  SizedBox(
                    height: 120.h,
                    child: Consumer(builder: (context, ref, _) {
                      return ref.watch(allServicesProvider).map(
                            initial: (_) => const SizedBox.shrink(),
                            loading: (_) => ListView(
                              scrollDirection: Axis.horizontal,
                              padding: EdgeInsets.symmetric(horizontal: 16.w),
                              children: List.generate(
                                  4,
                                  (i) => Padding(
                                      padding:
                                          EdgeInsets.symmetric(horizontal: 6.w),
                                      child: Container(
                                          width: 110.w,
                                          height: 90.h,
                                          decoration: BoxDecoration(
                                              color: Color(0xFFE2E8F0),
                                              borderRadius:
                                                  BorderRadius.circular(
                                                      16.r))))),
                            ),
                            loaded: (_) {
                              final services = _.data.data?.services ?? [];
                              // Deduplicate by service id
                              final seenIds = <int>{};
                              final distinct = services
                                  .where((service) =>
                                      service.id != null &&
                                      seenIds.add(service.id!))
                                  .toList();
                              if (distinct.isEmpty) {
                                return Padding(
                                  padding: EdgeInsets.symmetric(vertical: 18.h),
                                  child: Text(
                                    "لا توجد خدمات متاحة حالياً",
                                    style: TextStyle(
                                      fontSize: 12.sp,
                                      color: Colors.grey.shade600,
                                    ),
                                  ),
                                );
                              }
                              return ListView.separated(
                                scrollDirection: Axis.horizontal,
                                padding: EdgeInsets.only(
                                    right: 12.w,
                                    left: 0,
                                    top: 6.h,
                                    bottom: 6.h),
                                itemCount: distinct.length,
                                separatorBuilder: (_, __) =>
                                    SizedBox(width: 10.w),
                                itemBuilder: (context, i) {
                                  final s = distinct[i];
                                  final img = (s.imagePath ?? '').toString();
                                  final name = s.name?.trim() ?? '';
                                  return GestureDetector(
                                    onTap: () {
                                      Navigator.of(context).push(
                                          MaterialPageRoute(
                                              builder: (_) => ChooseItems(
                                                  service: s,
                                                  vendorId: null,
                                                  vendorName: null)));
                                    },
                                    child: Container(
                                      width: 100.w,
                                      padding: EdgeInsets.symmetric(
                                          horizontal: 8.w, vertical: 8.h),
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius:
                                            BorderRadius.circular(16.r),
                                        border: Border.all(
                                            color: Color(0xFF156172)
                                                .withOpacity(0.15),
                                            width: 1),
                                        boxShadow: [
                                          BoxShadow(
                                              color: Color(0xFF156172)
                                                  .withOpacity(0.08),
                                              blurRadius: 8,
                                              offset: Offset(0, 4))
                                        ],
                                      ),
                                      child: Column(
                                        mainAxisSize: MainAxisSize.min,
                                        children: [
                                          Container(
                                            width: 44.w,
                                            height: 44.h,
                                            decoration: BoxDecoration(
                                              color: img.isNotEmpty
                                                  ? Colors.white
                                                  : Color(0xFFEAF6F8),
                                              borderRadius:
                                                  BorderRadius.circular(12.r),
                                              border: Border.all(
                                                  color: Color(0xFF1b758a)
                                                      .withOpacity(0.12)),
                                            ),
                                            child: img.isNotEmpty
                                                ? ClipRRect(
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                            12.r),
                                                    child: AppNetworkImage(img,
                                                        width: 44.w,
                                                        height: 44.h,
                                                        fit: BoxFit.cover))
                                                : Icon(Icons.category_rounded,
                                                    size: 20.sp,
                                                    color: Color(0xFF156172)),
                                          ),
                                          SizedBox(height: 6.h),
                                          Text(name.isNotEmpty ? name : '',
                                              style: TextStyle(
                                                  fontSize: 11.sp,
                                                  fontWeight: FontWeight.w800,
                                                  color: Color(0xFF083744),
                                                  height: 1),
                                              maxLines: 1,
                                              overflow: TextOverflow.ellipsis,
                                              textAlign: TextAlign.center),
                                        ],
                                      ),
                                    ),
                                  );
                                },
                              );
                            },
                            error: (_) => Padding(
                                padding: EdgeInsets.symmetric(horizontal: 16.w),
                                child: Text(_.error.toString(),
                                    style: TextStyle(
                                        fontSize: 10.sp, color: Colors.red))),
                          );
                    }),
                  ),
                ],
              ),
            ),
            SizedBox(height: 24.h),
            // Best Offers Section
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text("أفضل العروض",
                          style: TextStyle(
                              fontSize: 16.sp,
                              fontWeight: FontWeight.w900,
                              fontFamily: 'Cairo',
                              color: Color(0xFF156172))),
                      TextButton(
                          onPressed: () {},
                          child: Text("عرض الكل",
                              style: TextStyle(
                                  color: Color(0xFF156172),
                                  fontWeight: FontWeight.bold,
                                  fontSize: 12))),
                    ],
                  ),
                  SizedBox(height: 8.h),
                  Consumer(
                    builder: (context, ref, child) {
                      return ref.watch(offersProvider).map(
                            initial: (_) => const SizedBox.shrink(),
                            loading: (_) => SizedBox(
                                height: 90.h,
                                child: const Center(
                                    child: CircularProgressIndicator(
                                        strokeWidth: 2))),
                            error: (_) => const SizedBox.shrink(),
                            loaded: (state) {
                              final offers =
                                  state.data.data?.offers ?? <OfferItem>[];
                              if (offers.isEmpty)
                                return const SizedBox.shrink();
                              return Container(
                                height: 90.h,
                                decoration: BoxDecoration(
                                  gradient: const LinearGradient(colors: [
                                    Color(0xFFEAF6F8),
                                    Color(0xFFCAE6EC)
                                  ]),
                                  borderRadius: BorderRadius.circular(20.r),
                                  border: Border.all(
                                      color: const Color(0xFF156172)
                                          .withOpacity(0.15),
                                      width: 1.2),
                                ),
                                child: ListView.separated(
                                  scrollDirection: Axis.horizontal,
                                  padding:
                                      EdgeInsets.symmetric(horizontal: 12.w),
                                  itemCount: offers.length,
                                  separatorBuilder: (_, __) =>
                                      SizedBox(width: 10.w),
                                  itemBuilder: (context, index) {
                                    final offer = offers[index];
                                    return _offerCard(
                                      offer.title ?? 'عرض خاص',
                                      offer.vendorName ?? 'متجر مشارك',
                                      offer.imagePath ?? '',
                                    );
                                  },
                                ),
                              );
                            },
                          );
                    },
                  ),
                ],
              ),
            ),
            SizedBox(height: 16.h),
            // Free Delivery Banner
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w),
              child: Container(
                padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 14.h),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                      colors: [Color(0xFF0A4A54), Color(0xFF1b758a)]),
                  borderRadius: BorderRadius.circular(24.r),
                  boxShadow: [
                    BoxShadow(
                        color: const Color(0xFF156172).withOpacity(0.20),
                        blurRadius: 16,
                        offset: const Offset(0, 8))
                  ],
                ),
                child: Row(
                  children: [
                    Icon(Icons.local_shipping,
                        size: 28.sp, color: Colors.white.withOpacity(0.9)),
                    SizedBox(width: 12.w),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("توصيل مجاني",
                              style: TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.w900,
                                  fontSize: 15.sp,
                                  fontFamily: 'Cairo')),
                          Text(
                              "لأول 100 عميل داخل حدود مدينة دورا والمناطق المجاورة",
                              style: TextStyle(
                                  color: Colors.white70,
                                  fontSize: 10.sp,
                                  height: 1.2)),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
            SizedBox(height: 24.h),
            // Stores Section - Multi-Vendor with filter
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text("المتاجر المتاحة",
                          style: TextStyle(
                              fontSize: 16.sp, fontWeight: FontWeight.w800)),
                      SizedBox(width: 8.w),
                      Container(
                        padding: EdgeInsets.symmetric(
                            horizontal: 8.w, vertical: 2.h),
                        decoration: BoxDecoration(
                            color: Color(0xFFEAF6F8),
                            borderRadius: BorderRadius.circular(20.r),
                            border: Border.all(
                                color: Color(0xFF1b758a).withOpacity(0.3))),
                        child: Consumer(builder: (context, ref, _) {
                          final vendors = ref.watch(vendorsProvider).maybeMap(
                              loaded: (s) => s.data.data?.vendors?.length ?? 0,
                              orElse: () => 0);
                          return Text("$vendors+",
                              style: TextStyle(
                                  fontSize: 11.sp,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xFF156172)));
                        }),
                      ),
                      const Spacer(),
                      Icon(Icons.tune, size: 18, color: Color(0xFF1b758a)),
                      SizedBox(width: 4.w),
                      Text("تصفية",
                          style: TextStyle(
                              fontSize: 12.sp, color: Color(0xFF156172))),
                    ],
                  ),
                  SizedBox(height: 12.h),
                  const VendorSearchBar(),
                  SizedBox(height: 12.h),
                  Consumer(
                    builder: (context, ref, child) {
                      final vendorsAsync = ref.watch(vendorsProvider);
                      return vendorsAsync.map(
                        initial: (_) => const SizedBox(),
                        loading: (_) => Column(
                            children: List.generate(
                                2, (_) => const StoreCardShimmer())),
                        loaded: (_) {
                          final vendors = _.data.data?.vendors ?? [];
                          if (vendors.isEmpty)
                            return Padding(
                                padding: EdgeInsets.all(16.w),
                                child: const Text("لا توجد متاجر متاحة حالياً",
                                    textAlign: TextAlign.center));
                          return Column(
                              children: vendors
                                  .map((v) => StoreCard(
                                      vendor: v,
                                      onTap: () => Navigator.of(context).push(
                                          MaterialPageRoute(
                                              builder: (_) => VendorStoreScreen(
                                                  vendor: v)))))
                                  .toList());
                        },
                        error: (_) => Text(_.error.toString()),
                      );
                    },
                  ),
                ],
              ),
            ),
            SizedBox(height: 20.h),
            // Partners / Trusted Vendors Banner (dynamic from vendors)
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w),
              child: Consumer(builder: (context, ref, _) {
                final vendorsAsync = ref.watch(vendorsProvider);
                return vendorsAsync.maybeWhen(
                  loaded: (_) {
                    final vendors = _.data?.vendors ?? [];
                    if (vendors.isEmpty) return SizedBox.shrink();
                    return Container(
                      padding: EdgeInsets.symmetric(
                          horizontal: 16.w, vertical: 14.h),
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                            colors: [Color(0xFF0A4A54), Color(0xFF1b758a)],
                            begin: Alignment.centerLeft,
                            end: Alignment.centerRight),
                        borderRadius: BorderRadius.circular(24.r),
                        boxShadow: [
                          BoxShadow(
                              color: Color(0xFF156172).withOpacity(0.25),
                              blurRadius: 16,
                              offset: const Offset(0, 8))
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("شركاؤنا الموثوقون",
                              style: TextStyle(
                                  fontSize: 15.sp,
                                  fontWeight: FontWeight.w900,
                                  color: Colors.white,
                                  fontFamily: 'Cairo')),
                          SizedBox(height: 10.h),
                          SizedBox(
                            height: 70.h,
                            child: ListView.separated(
                              scrollDirection: Axis.horizontal,
                              itemCount: vendors.length,
                              separatorBuilder: (_, __) => SizedBox(width: 8.w),
                              itemBuilder: (context, i) {
                                final v = vendors[i];
                                return GestureDetector(
                                  onTap: () => Navigator.of(context).push(
                                      MaterialPageRoute(
                                          builder: (_) =>
                                              VendorStoreScreen(vendor: v))),
                                  child: Container(
                                      width: 60.w,
                                      height: 60.h,
                                      decoration: BoxDecoration(
                                          color: Colors.white,
                                          borderRadius:
                                              BorderRadius.circular(12.r),
                                          border: Border.all(
                                              color: Color(0xFF156172)
                                                  .withOpacity(0.2))),
                                      child: ClipOval(
                                          child: AppNetworkImage(
                                              v.displayImage ?? '',
                                              height: 60.h,
                                              width: 60.w,
                                              fit: BoxFit.cover,
                                              placeholder:
                                                  'assets/images/app_icon.png'))),
                                );
                              },
                            ),
                          ),
                        ],
                      ),
                    );
                  },
                  orElse: () => SizedBox.shrink(),
                );
              }),
            ),
            SizedBox(height: 120.h),
          ],
        ),
      ),
    );
  }

  Widget _serviceCard(String title, String subtitle, String icon, bool active) {
    return Column(
      children: [
        Container(
          width: double.infinity,
          padding: EdgeInsets.all(12.w),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16.r),
            border: Border.all(
                color: active
                    ? Color(0xFF156172).withOpacity(0.3)
                    : Colors.grey.shade200),
            boxShadow: [
              BoxShadow(
                  color: Colors.black.withOpacity(0.03),
                  blurRadius: 8,
                  offset: const Offset(0, 2))
            ],
          ),
          child: Column(
            children: [
              Container(
                width: 44.w,
                height: 44.h,
                decoration: BoxDecoration(
                  color: active ? Color(0xFFEAF6F8) : Colors.white,
                  borderRadius: BorderRadius.circular(12.r),
                  border: Border.all(
                      color: active
                          ? Color(0xFF1b758a).withOpacity(0.3)
                          : Colors.transparent),
                ),
                child: Center(
                    child: Text(icon, style: TextStyle(fontSize: 20.sp))),
              ),
              SizedBox(height: 8.h),
              Text(title,
                  style:
                      TextStyle(fontSize: 12.sp, fontWeight: FontWeight.bold)),
              Text(subtitle,
                  style: TextStyle(fontSize: 10.sp, color: Colors.grey)),
            ],
          ),
        ),
      ],
    );
  }

  Widget _heroBanner(Promotion? promotion) {
    final title = promotion?.title?.trim();
    final description = promotion?.description?.trim();
    final imagePath = promotion?.imagePath?.trim() ?? '';

    return Padding(
      padding: EdgeInsets.all(16.w),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(26.r),
        child: Container(
          constraints: BoxConstraints(minHeight: 190.h),
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF1b758a), Color(0xFF156172), Color(0xFF0A4A54)],
              begin: Alignment.topRight,
              end: Alignment.bottomLeft,
            ),
          ),
          child: Stack(
            children: [
              if (imagePath.isNotEmpty)
                Positioned.fill(
                  child: Opacity(
                    opacity: 0.36,
                    child: AppNetworkImage(imagePath, fit: BoxFit.cover),
                  ),
                ),
              Positioned.fill(
                child: DecoratedBox(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [
                        Colors.black.withOpacity(0.04),
                        Colors.black.withOpacity(0.48)
                      ],
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                    ),
                  ),
                ),
              ),
              Padding(
                padding: EdgeInsets.all(20.w),
                child: Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Container(
                                width: 8.w,
                                height: 8.h,
                                decoration: const BoxDecoration(
                                    color: Color(0xFFFFB703),
                                    shape: BoxShape.circle),
                              ),
                              SizedBox(width: 6.w),
                              Text(
                                title?.isNotEmpty == true
                                    ? title!
                                    : "عروض المتاجر",
                                style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 12.sp,
                                    fontWeight: FontWeight.bold),
                              ),
                            ],
                          ),
                          SizedBox(height: 16.h),
                          Text(
                            title?.isNotEmpty == true
                                ? title!
                                : "اطلب خدماتك بسهولة",
                            style: TextStyle(
                                color: Colors.white,
                                fontSize: 25.sp,
                                fontWeight: FontWeight.w900,
                                height: 1.2),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                          if (description?.isNotEmpty == true) ...[
                            SizedBox(height: 8.h),
                            Text(
                              description!,
                              style: TextStyle(
                                  color: Colors.white70,
                                  fontSize: 11.sp,
                                  height: 1.4),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                          SizedBox(height: 14.h),
                          ElevatedButton.icon(
                            onPressed: () {},
                            icon:
                                const Icon(Icons.arrow_back_rounded, size: 15),
                            label: const Text("استكشف الآن",
                                style: TextStyle(
                                    fontWeight: FontWeight.w800, fontSize: 12)),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFFFFB703),
                              foregroundColor: const Color(0xFF0F2732),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12.r)),
                              padding: EdgeInsets.symmetric(
                                  horizontal: 14.w, vertical: 10.h),
                            ),
                          ),
                        ],
                      ),
                    ),
                    SizedBox(width: 12.w),
                    Container(
                      width: 76.w,
                      height: 76.h,
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.15),
                        shape: BoxShape.circle,
                        border:
                            Border.all(color: Colors.white.withOpacity(0.22)),
                      ),
                      child: const Icon(Icons.local_shipping_rounded,
                          color: Colors.white, size: 42),
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

  Widget _offerCard(String title, String vendorName, String imagePath) {
    return Container(
      width: 260.w,
      padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 10.h),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16.r),
        border:
            Border.all(color: Color(0xFF156172).withOpacity(0.15), width: 1.2),
      ),
      child: Row(
        children: [
          Container(
            width: 50.w,
            height: 50.h,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                  colors: [Color(0xFFEAF6F8), Color(0xFFCAE6EC)]),
              borderRadius: BorderRadius.circular(12.r),
            ),
            child: imagePath.isNotEmpty
                ? ClipRRect(
                    borderRadius: BorderRadius.circular(12.r),
                    child: AppNetworkImage(imagePath,
                        fit: BoxFit.cover, width: 50.w, height: 50.h),
                  )
                : Icon(Icons.local_offer_rounded,
                    size: 22.sp, color: const Color(0xFF156172)),
          ),
          SizedBox(width: 10.w),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title,
                    style: TextStyle(
                        fontSize: 12.sp,
                        fontWeight: FontWeight.w900,
                        color: Color(0xFF156172))),
                SizedBox(height: 3.h),
                Text(vendorName,
                    style: TextStyle(fontSize: 9.sp, color: Colors.grey)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
