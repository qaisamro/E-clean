import 'package:laundry_customer/providers/city_providers.dart';
import 'package:laundry_customer/widgets/home_city_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:carousel_slider/carousel_slider.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/models/offers_model/offers_model.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/notfiers/guest_notfiers.dart';
import 'package:laundry_customer/screens/homePage/choose_items.dart';
import 'package:laundry_customer/screens/store/vendor_store_screen.dart';
import 'package:laundry_customer/screens/store/stores_map_screen.dart';
import 'package:laundry_customer/screens/store/service_vendors_screen.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/store_card.dart';
import 'package:laundry_customer/widgets/home_search_bar.dart';
import 'package:laundry_customer/widgets/section_title.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/utils/routes.dart';

class NewHomeTab extends ConsumerStatefulWidget {
  const NewHomeTab({super.key});
  @override
  ConsumerState<NewHomeTab> createState() => _NewHomeTabState();
}

class _NewHomeTabState extends ConsumerState<NewHomeTab> {
  @override
  void initState() {
    super.initState();
    // First-launch prompt: ask to enable location so the home screen can show
    // stores/services/offers for the customer's city. Shown at most once per
    // install (or until a city is saved).
    WidgetsBinding.instance.addPostFrameCallback((_) => _firstLaunchLocationCheck());
  }

  Future<void> _firstLaunchLocationCheck() async {
    if (!mounted) return;
    final box = Hive.box(AppHSC.appSettingsBox);
    if (box.get('home_location_prompt_seen_v1') == true) return;
    // Users who already have a city selected don't need the prompt.
    if (ref.read(citySelectionProvider).city != null) {
      await box.put('home_location_prompt_seen_v1', true);
      return;
    }
    await showLocationActivationPrompt(context);
    if (!mounted) return;
    await box.put('home_location_prompt_seen_v1', true);
  }

  /// Decode full-width banners at the pixel size actually displayed instead of
  /// their (larger) source resolution.
  int? get _bannerDecodeWidth => (MediaQuery.sizeOf(context).width *
          MediaQuery.devicePixelRatioOf(context))
      .round();

  @override
  Widget build(BuildContext context) {
    final city = ref.watch(citySelectionProvider);
    return Container(
      color: const Color(0xFFF5F5F5),
      child: ListView(
        padding: EdgeInsets.zero,
        children: [
          // Modern Luxury Header
          Container(
            clipBehavior: Clip.antiAlias,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFFF2FAF8), Color(0xFFE1EFEA)],
                begin: Alignment.topRight,
                end: Alignment.bottomLeft,
              ),
              borderRadius:
                  BorderRadius.vertical(bottom: Radius.circular(24.r)),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFF156172).withOpacity(0.12),
                  blurRadius: 20,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Stack(
              children: [
                Positioned(
                  top: -70.h,
                  right: -50.w,
                  child: Container(
                    width: 170.h,
                    height: 170.h,
                    decoration: BoxDecoration(
                      gradient: RadialGradient(
                        colors: [
                          Color(0xFF14B8A6).withOpacity(0.30),
                          Color(0xFF14B8A6).withOpacity(0),
                        ],
                      ),
                      shape: BoxShape.circle,
                    ),
                  ),
                ),
                Positioned(
                  bottom: -80.h,
                  left: -60.w,
                  child: Container(
                    width: 190.h,
                    height: 190.h,
                    decoration: BoxDecoration(
                      gradient: RadialGradient(
                        colors: [
                          Color(0xFF156172).withOpacity(0.18),
                          Color(0xFF156172).withOpacity(0),
                        ],
                      ),
                      shape: BoxShape.circle,
                    ),
                  ),
                ),
                Padding(
                  padding: EdgeInsets.only(
                    top: MediaQuery.of(context).padding.top + 10.h,
                    bottom: 16.h,
                    left: 18.w,
                    right: 18.w,
                  ),
                  child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    // User Avatar
                    Stack(
                      children: [
                        Container(
                          width: 48.w,
                          height: 48.h,
                          padding: EdgeInsets.all(2.5.w),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            gradient: const LinearGradient(
                              colors: [Color(0xFF156172), Color(0xFF1b758a)],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            ),
                            boxShadow: [
                              BoxShadow(
                                color:
                                    const Color(0xFF156172).withOpacity(0.18),
                                blurRadius: 8,
                                offset: const Offset(0, 3),
                              ),
                            ],
                          ),
                          child: Container(
                            decoration: const BoxDecoration(
                              color: Colors.white,
                              shape: BoxShape.circle,
                            ),
                            clipBehavior: Clip.antiAlias,
                            child: Hive.box(AppHSC.authBox).get('token') != null
                                ? Center(
                                    child: Text(
                                      (Hive.box(AppHSC.userBox)
                                                  .get('name')
                                                  ?.toString() ??
                                              'U')
                                          .characters
                                          .first
                                          .toUpperCase(),
                                      style: TextStyle(
                                        fontSize: 20.sp,
                                        fontWeight: FontWeight.w900,
                                        color: const Color(0xFF156172),
                                        fontFamily: 'Cairo',
                                      ),
                                    ),
                                  )
                                : Container(
                                    decoration: const BoxDecoration(
                                      gradient: LinearGradient(
                                        colors: [
                                          Color(0xFFEAF6F8),
                                          Color(0xFFCAE6EC)
                                        ],
                                      ),
                                    ),
                                    child: Center(
                                      child: Icon(
                                        Icons.person_rounded,
                                        size: 24.sp,
                                        color: const Color(0xFF156172),
                                      ),
                                    ),
                                  ),
                          ),
                        ),
                        if (Hive.box(AppHSC.authBox).get('token') != null)
                          Positioned(
                            bottom: 2.h,
                            right: 2.w,
                            child: Container(
                              width: 11.w,
                              height: 11.h,
                              decoration: BoxDecoration(
                                color: const Color(0xFF10B981),
                                shape: BoxShape.circle,
                                border:
                                    Border.all(color: Colors.white, width: 2),
                              ),
                            ),
                          ),
                      ],
                    ),

                    SizedBox(width: 12.w),

                    // Greeting & Name & Location / Guest Info
                    Expanded(
                      child: Hive.box(AppHSC.authBox).get('token') != null
                          ? Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Container(
                                  padding: EdgeInsets.symmetric(
                                      horizontal: 8.w, vertical: 2.h),
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(20.r),
                                    border: Border.all(
                                      color: const Color(0xFF156172)
                                          .withOpacity(0.28),
                                    ),
                                  ),
                                  child: Text(
                                    "مرحباً بعودتك",
                                    style: TextStyle(
                                      fontSize: 10.5.sp,
                                      color: const Color(0xFF156172),
                                      fontWeight: FontWeight.w700,
                                      fontFamily: 'Cairo',
                                    ),
                                  ),
                                ),
                                SizedBox(height: 3.h),
                                Text(
                                  Hive.box(AppHSC.userBox)
                                          .get('name')
                                          ?.toString() ??
                                      "المستخدم",
                                  style: TextStyle(
                                    fontSize: 16.sp,
                                    fontWeight: FontWeight.w900,
                                    color: const Color(0xFF103C3E),
                                    fontFamily: 'Cairo',
                                    height: 1.2,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                SizedBox(height: 2.h),
                                Row(
                                  children: [
                                    Icon(
                                      Icons.location_on_rounded,
                                      size: 12.sp,
                                      color: const Color(0xFF156172),
                                    ),
                                    SizedBox(width: 3.w),
                                    Expanded(
                                      child: Text(
                                        "خدمة توصيل سريعة وموثوقة",
                                        style: TextStyle(
                                          fontSize: 10.5.sp,
                                          fontWeight: FontWeight.w600,
                                          color: const Color(0xFF41605C),
                                          fontFamily: 'Cairo',
                                        ),
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            )
                          : Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text(
                                  "تصفح كـ ضيف",
                                  style: TextStyle(
                                    fontSize: 17.sp,
                                    fontWeight: FontWeight.w900,
                                    color: const Color(0xFF103C3E),
                                    fontFamily: 'Cairo',
                                    height: 1.2,
                                  ),
                                ),
                                SizedBox(height: 3.h),
                                Text(
                                  "استكشف الخدمات والمتاجر والعروض",
                                  style: TextStyle(
                                    fontSize: 11.sp,
                                    fontWeight: FontWeight.w600,
                                    color: const Color(0xFF41605C),
                                    fontFamily: 'Cairo',
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                    ),

                    // Quick Action Buttons (Notifications & Auth)
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        // Notifications Button with badge
                        Stack(
                          children: [
                            Container(
                              width: 40.w,
                              height: 40.h,
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(12.r),
                                border:
                                    Border.all(color: const Color(0xFFCFDEDA)),
                              ),
                              child: IconButton(
                                onPressed: () {
                                  ref
                                      .read(homeScreenPageControllerProvider)
                                      .jumpToPage(2);
                                },
                                icon: Icon(
                                  Icons.notifications_outlined,
                                  color: const Color(0xFF156172),
                                  size: 20.sp,
                                ),
                                padding: EdgeInsets.zero,
                              ),
                            ),
                            Positioned(
                              top: 7.h,
                              right: 7.w,
                              child: Container(
                                width: 8.w,
                                height: 8.h,
                                decoration: const BoxDecoration(
                                  color: Color(0xFFFF5722),
                                  shape: BoxShape.circle,
                                ),
                              ),
                            ),
                          ],
                        ),
                        SizedBox(width: 8.w),
                        // Profile / Login Button
                        if (Hive.box(AppHSC.authBox).get('token') != null)
                          Container(
                            width: 40.w,
                            height: 40.h,
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(12.r),
                              border:
                                  Border.all(color: const Color(0xFFCFDEDA)),
                            ),
                            child: IconButton(
                              onPressed: () {
                                ref
                                    .read(homeScreenPageControllerProvider)
                                    .jumpToPage(3);
                              },
                              icon: Icon(
                                Icons.person_outline_rounded,
                                color: const Color(0xFF156172),
                                size: 20.sp,
                              ),
                              padding: EdgeInsets.zero,
                            ),
                          )
                        else
                          ElevatedButton.icon(
                            onPressed: () {
                              Navigator.pushNamed(context, Routes.loginScreen);
                            },
                            icon: Icon(
                              Icons.login_rounded,
                              size: 14.sp,
                              color: Colors.white,
                            ),
                            label: Text(
                              "دخول",
                              style: TextStyle(
                                fontFamily: 'Cairo',
                                fontSize: 11.5.sp,
                                fontWeight: FontWeight.w800,
                                color: Colors.white,
                              ),
                            ),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF156172),
                              elevation: 0,
                              padding: EdgeInsets.symmetric(
                                  horizontal: 12.w, vertical: 8.h),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(12.r),
                              ),
                              minimumSize: Size(0, 40.h),
                            ),
                          ),
                      ],
                    ),
                  ],
                ),
                SizedBox(height: 10.h),
                Wrap(
                    spacing: 8,
                    runSpacing: 6,
                    crossAxisAlignment: WrapCrossAlignment.center,
                    children: [
                      _buildAreaPicker(),
                      OutlinedButton.icon(
                        onPressed: () => Navigator.of(context).push(
                            MaterialPageRoute<void>(
                                builder: (_) => const StoresMapScreen())),
                        icon: const Icon(Icons.map_outlined, size: 18),
                        label: const Text('خريطة المتاجر'),
                        style: OutlinedButton.styleFrom(
                            foregroundColor: const Color(0xFF156172)),
                      ),
                    ]),
              ],
                ),
                ),
              ],
            ),
          ),

          // Smart search — scoped to the selected area
          // (sits between the zone/header section and the top banner)
          Padding(
            padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 2.h),
            child: const HomeSearchBar(),
          ),

          if (city.city == null)
            const HomeCityNotice()
          else ...[
            // Main Hero Banner - Dynamic from Super-admin /api/banner (only banner, no promo)
            Consumer(builder: (context, ref, _) {
              return ref.watch(allPromotionsProvider).map(
                    initial: (_) => const SizedBox.shrink(),
                    loading: (_) => Padding(
                        padding: EdgeInsets.symmetric(
                            horizontal: 16.w, vertical: 12.h),
                        child: Container(
                            height: 260.h,
                            decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(24.r),
                                color: Color(0xFFE2E8F0)))),
                    loaded: (_) {
                      final banners = _.data.data?.promotions ?? [];
                      print(
                          "DEBUG BANNER: banners loaded, count=${banners.length}, firstImg=${banners.isNotEmpty ? banners.first.imagePath : 'empty'}");
                      if (banners.isEmpty) {
                        // Fallback to static hero banner when no banners from API
                        return Padding(
                          padding: EdgeInsets.symmetric(
                              horizontal: 16.w, vertical: 12.h),
                          child: Container(
                            height: 260.h,
                            padding: EdgeInsets.symmetric(
                                horizontal: 16.w, vertical: 16.h),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(24.r),
                              color: Color(0xFF156172),
                              image: DecorationImage(
                                  image:
                                      AssetImage('assets/images/app_icon.png'),
                                  fit: BoxFit.cover,
                                  opacity: 0.15,
                                  colorFilter: ColorFilter.mode(
                                      Color(0xFF0A4A54).withOpacity(0.6),
                                      BlendMode.multiply)),
                              boxShadow: [
                                BoxShadow(
                                    color: Color(0xFF156172).withOpacity(0.35),
                                    blurRadius: 24,
                                    offset: const Offset(0, 10))
                              ],
                            ),
                            child: Stack(
                              children: [
                                Positioned(
                                    top: 12.h,
                                    right: 16.w,
                                    child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.end,
                                        children: [
                                          Text("كل خدمات التنظيف",
                                              style: TextStyle(
                                                  fontSize: 22.sp,
                                                  fontWeight: FontWeight.w900,
                                                  color: Colors.white,
                                                  fontFamily: 'Cairo',
                                                  height: 1.2),
                                              textAlign: TextAlign.right),
                                          Text("والدرای كلين",
                                              style: TextStyle(
                                                  fontSize: 22.sp,
                                                  fontWeight: FontWeight.w900,
                                                  color: Color(0xFFFFB703),
                                                  fontFamily: 'Cairo',
                                                  height: 1.2),
                                              textAlign: TextAlign.right),
                                          Text("في مكان واحد",
                                              style: TextStyle(
                                                  fontSize: 22.sp,
                                                  fontWeight: FontWeight.w900,
                                                  color: Colors.white,
                                                  fontFamily: 'Cairo',
                                                  height: 1.2),
                                              textAlign: TextAlign.right),
                                          SizedBox(height: 8.h),
                                          Text("بكل سهولة .. ودقة",
                                              style: TextStyle(
                                                  fontSize: 13.sp,
                                                  color: Colors.white70,
                                                  fontWeight: FontWeight.w500,
                                                  fontFamily: 'Cairo'),
                                              textAlign: TextAlign.right),
                                          SizedBox(height: 14.h),
                                          ElevatedButton.icon(
                                            onPressed: () {},
                                            icon: Icon(Icons.location_on,
                                                size: 16.sp,
                                                color: Color(0xFF156172)),
                                            label: Text("من دورا .. لكل المدن",
                                                style: TextStyle(
                                                    fontSize: 12.sp,
                                                    fontWeight: FontWeight.w900,
                                                    color: Color(0xFF083744),
                                                    fontFamily: 'Cairo')),
                                            style: ElevatedButton.styleFrom(
                                                backgroundColor: Colors.white,
                                                foregroundColor:
                                                    Color(0xFF083744),
                                                shape: RoundedRectangleBorder(
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                            30.r)),
                                                padding: EdgeInsets.symmetric(
                                                    horizontal: 20.w,
                                                    vertical: 10.h)),
                                          ),
                                        ])),
                                Positioned(
                                    bottom: 16.h,
                                    left: 16.w,
                                    child: Container(
                                        width: 100.w,
                                        height: 100.h,
                                        decoration: BoxDecoration(
                                            borderRadius:
                                                BorderRadius.circular(20.r),
                                            color:
                                                Colors.white.withOpacity(0.1),
                                            border: Border.all(
                                                color: Colors.white24,
                                                width: 1.5)),
                                        child: Center(
                                            child: Icon(
                                                Icons.local_laundry_service_rounded,
                                                size: 52.sp,
                                                color: Colors.white
                                                    .withOpacity(0.92))))),
                                Positioned(
                                    top: 16.h,
                                    left: 16.w,
                                    child: Container(
                                        padding: EdgeInsets.symmetric(
                                            horizontal: 10.w, vertical: 4.h),
                                        decoration: BoxDecoration(
                                            color:
                                                Colors.white.withOpacity(0.15),
                                            borderRadius:
                                                BorderRadius.circular(20.r),
                                            border: Border.all(
                                                color: Colors.white24)),
                                        child: Row(children: [
                                          Container(
                                              width: 8.w,
                                              height: 8.h,
                                              decoration: BoxDecoration(
                                                  color: Color(0xFF1b758a),
                                                  shape: BoxShape.circle)),
                                          SizedBox(width: 6.w),
                                          Text("من دورا .. لكل المدن",
                                              style: TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 10.sp,
                                                  fontWeight: FontWeight.bold,
                                                  fontFamily: 'Cairo'))
                                        ]))),
                              ],
                            ),
                          ),
                        );
                      }
                      // Dynamic banner slider from Super-admin (image only, no text)
                      final sliderBanners = banners
                          .where(
                              (b) => (b.imagePath?.toString() ?? '').isNotEmpty)
                          .toList();
                      if (sliderBanners.isEmpty) {
                        return Container(
                          height: 200.h,
                          margin: EdgeInsets.symmetric(
                              horizontal: 16.w, vertical: 8.h),
                          decoration: BoxDecoration(
                              color: Color(0xFF156172),
                              borderRadius: BorderRadius.circular(24.r)),
                        );
                      }
                      return Padding(
                        padding: EdgeInsets.symmetric(
                            horizontal: 16.w, vertical: 8.h),
                        child: CarouselSlider(
                          options: CarouselOptions(
                            height: 200.h,
                            viewportFraction: 1.0,
                            autoPlay: sliderBanners.length > 1,
                            autoPlayInterval: const Duration(seconds: 4),
                            autoPlayAnimationDuration:
                                const Duration(milliseconds: 800),
                            enlargeCenterPage: false,
                            enableInfiniteScroll: sliderBanners.length > 1,
                          ),
                          items: sliderBanners.map((banner) {
                            final img =
                                resolveApiImageUrl(banner.imagePath.toString());
                            return Builder(builder: (context) {
                              return ClipRRect(
                                borderRadius: BorderRadius.circular(24.r),
                                child: Container(
                                  decoration: BoxDecoration(
                                    color: Color(0xFF156172),
                                    boxShadow: [
                                      BoxShadow(
                                          color: Color(0xFF156172)
                                              .withOpacity(0.3),
                                          blurRadius: 16,
                                          offset: const Offset(0, 8))
                                    ],
                                  ),
                                  clipBehavior: Clip.antiAlias,
                                  child: Image.network(
                                    img,
                                    fit: BoxFit.cover,
                                    width: double.infinity,
                                    cacheWidth: _bannerDecodeWidth,
                                    loadingBuilder: (context, child,
                                            progress) =>
                                        progress == null
                                            ? child
                                            : Container(
                                                color: Color(0xFFE2E8F0),
                                                alignment: Alignment.center,
                                                child: const SizedBox(
                                                    width: 24,
                                                    height: 24,
                                                    child:
                                                        CircularProgressIndicator(
                                                            strokeWidth: 2))),
                                    errorBuilder:
                                        (context, error, stackTrace) =>
                                            Container(color: Color(0xFF156172)),
                                  ),
                                ),
                              );
                            });
                          }).toList(),
                        ),
                      );
                    },
                    error: (_) => const SizedBox.shrink(),
                  );
            }),

            // Services Section - dynamic from API
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w, vertical: 6.h),
              child: Consumer(builder: (context, ref, _) {
                return ref.watch(allServicesProvider).map(
                      initial: (_) => const SizedBox.shrink(),
                      loading: (_) => SizedBox(
                        height: 104.h,
                        child: ListView(
                          scrollDirection: Axis.horizontal,
                          physics: const BouncingScrollPhysics(),
                          children: List.generate(
                            4,
                            (_) => Padding(
                              padding: EdgeInsets.symmetric(horizontal: 6.w),
                              child: Column(
                                children: [
                                  Container(
                                    width: 64.w,
                                    height: 64.h,
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFE2E8F0),
                                      borderRadius: BorderRadius.circular(18.r),
                                    ),
                                  ),
                                  SizedBox(height: 8.h),
                                  Container(
                                    width: 50.w,
                                    height: 10.h,
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFE2E8F0),
                                      borderRadius: BorderRadius.circular(4.r),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                      loaded: (_) {
                        final services = _.data.data?.services ?? [];
                        if (services.isEmpty) return const SizedBox.shrink();
                        return Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const HomeSectionTitle(text: 'خدماتنا'),
                            SizedBox(height: 10.h),
                            SizedBox(
                              height: 106.h,
                              child: ListView.separated(
                                scrollDirection: Axis.horizontal,
                                physics: const BouncingScrollPhysics(),
                                itemCount: services.length,
                                separatorBuilder: (_, __) =>
                                    SizedBox(width: 12.w),
                                itemBuilder: (context, i) {
                                  final s = services[i];
                                  return _serviceDynamicCard(s, context, ref);
                                },
                              ),
                            ),
                          ],
                        );
                      },
                      error: (_) => const SizedBox.shrink(),
                    );
              }),
            ),

            SizedBox(height: 12.h),

            // Best Offers Section (Modern Square Cards Slider) - API only, no static data.
            // The whole section is hidden until the backend returns at least one offer.
            Consumer(builder: (context, ref, __) {
              final hideOffersSection = ref.watch(offersProvider).maybeWhen(
                    loaded: (d) => (d.data?.offers ?? []).isEmpty,
                    orElse: () => false,
                  );
              if (hideOffersSection) return const SizedBox.shrink();
              return Column(
                children: [
                  Padding(
                    padding: EdgeInsets.symmetric(horizontal: 16.w),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const HomeSectionTitle(text: 'أفضل العروض والخصومات'),
                        SizedBox(height: 12.h),
                        Consumer(builder: (context, ref, child) {
                          final vendorsAsync = ref.watch(vendorsProvider);
                          final selectedArea =
                              ref.watch(selectedVendorAreaProvider);
                          return ref.watch(offersProvider).map(
                                initial: (_) => const SizedBox.shrink(),
                                loading: (_) => SizedBox(
                                  height: 195.h,
                                  child: ListView.separated(
                                    scrollDirection: Axis.horizontal,
                                    itemCount: 3,
                                    separatorBuilder: (_, __) =>
                                        SizedBox(width: 12.w),
                                    itemBuilder: (_, __) => Container(
                                      width: 175.w,
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius:
                                            BorderRadius.circular(20.r),
                                        border: Border.all(
                                            color: const Color(0xFFE2E8F0)),
                                      ),
                                    ),
                                  ),
                                ),
                                loaded: (_) {
                                  var offers = _.data.data?.offers ?? [];
                                  if (selectedArea != 'الكل') {
                                    final areaVendorIds =
                                        vendorsAsync.maybeWhen<Set<int?>>(
                                      loaded: (d) => {
                                        for (final v in (d.data?.vendors ??
                                            const <Vendor>[]))
                                          if (_vendorMatchesArea(
                                              v, selectedArea))
                                            v.id,
                                      },
                                      orElse: () => const <int?>{},
                                    );
                                    offers = offers
                                        .where((o) =>
                                            o.vendorId != null &&
                                            areaVendorIds.contains(o.vendorId))
                                        .toList();
                                  }
                                  if (offers.isEmpty) {
                                    // No static fallback data: hide the offers list.
                                    return const SizedBox.shrink();
                                  }
                                  return SizedBox(
                                    height: 195.h,
                                    child: ListView.separated(
                                      scrollDirection: Axis.horizontal,
                                      physics: const BouncingScrollPhysics(),
                                      itemCount: offers.length,
                                      separatorBuilder: (_, __) =>
                                          SizedBox(width: 12.w),
                                      itemBuilder: (context, index) {
                                        return _offerSquareCard(offers[index]);
                                      },
                                    ),
                                  );
                                },
                                error: (_) => const SizedBox.shrink(),
                              );
                        }),
                      ],
                    ),
                  ),
                  SizedBox(height: 24.h),
                ],
              );
            }),
            // Stores Section - Multi-Vendor with Area Filter only
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w),
              child: Consumer(
                builder: (context, ref, child) {
                  final vendorsAsync = ref.watch(vendorsProvider);
                  final selectedArea = ref.watch(selectedVendorAreaProvider);

                  return vendorsAsync.map(
                    initial: (_) => const SizedBox.shrink(),
                    loading: (_) => Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const HomeSectionTitle(
                                text: 'المتاجر المتاحة', fontSize: 13),
                            SizedBox(width: 8.w),
                            Container(
                              padding: EdgeInsets.symmetric(
                                  horizontal: 8.w, vertical: 2.h),
                              decoration: BoxDecoration(
                                color: const Color(0xFFEAF6F8),
                                borderRadius: BorderRadius.circular(20.r),
                              ),
                              child: Text(
                                "...",
                                style: TextStyle(
                                  fontSize: 11.sp,
                                  fontWeight: FontWeight.bold,
                                  color: const Color(0xFF156172),
                                ),
                              ),
                            ),
                          ],
                        ),
                        SizedBox(height: 12.h),
                        GridView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          gridDelegate:
                              SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            crossAxisSpacing: 12.w,
                            mainAxisSpacing: 12.h,
                            childAspectRatio: 0.78,
                          ),
                          itemCount: 4,
                          itemBuilder: (_, __) => const StoreGridCardShimmer(),
                        ),
                      ],
                    ),
                    loaded: (_) {
                      final allVendors = _.data.data?.vendors ?? [];

                      // Filter vendors according to selected area
                      final filteredVendors = selectedArea == 'الكل'
                          ? allVendors
                          : allVendors
                              .where((v) => _vendorMatchesArea(v, selectedArea))
                              .toList();

                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Header
                          Row(
                            children: [
                              const HomeSectionTitle(
                                  text: 'المتاجر المتاحة', fontSize: 13),
                              SizedBox(width: 8.w),
                              Container(
                                padding: EdgeInsets.symmetric(
                                    horizontal: 8.w, vertical: 2.h),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFEAF6F8),
                                  borderRadius: BorderRadius.circular(20.r),
                                  border: Border.all(
                                    color: const Color(0xFF1b758a)
                                        .withOpacity(0.3),
                                  ),
                                ),
                                child: Text(
                                  "${filteredVendors.length} متجر",
                                  style: TextStyle(
                                    fontSize: 11.sp,
                                    fontWeight: FontWeight.bold,
                                    fontFamily: 'Cairo',
                                    color: const Color(0xFF156172),
                                  ),
                                ),
                              ),
                              const Spacer(),
                            ],
                          ),

                          SizedBox(height: 14.h),

                          // Vendors 2-Column Grid
                          if (filteredVendors.isEmpty)
                            Container(
                              width: double.infinity,
                              padding: EdgeInsets.symmetric(
                                  vertical: 28.h, horizontal: 16.w),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(18.r),
                                border: Border.all(
                                  color: const Color(0xFFE2E8F0),
                                ),
                              ),
                              child: Column(
                                children: [
                                  Container(
                                    width: 50.w,
                                    height: 50.h,
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFEAF6F8),
                                      shape: BoxShape.circle,
                                    ),
                                    child: Icon(
                                      Icons.location_off_rounded,
                                      size: 26.sp,
                                      color: const Color(0xFF156172),
                                    ),
                                  ),
                                  SizedBox(height: 10.h),
                                  Text(
                                    "لا توجد متاجر متاحة في \"$selectedArea\"",
                                    style: TextStyle(
                                      fontFamily: 'Cairo',
                                      fontSize: 13.sp,
                                      color: const Color(0xFF0F2E35),
                                      fontWeight: FontWeight.w700,
                                    ),
                                    textAlign: TextAlign.center,
                                  ),
                                  SizedBox(height: 4.h),
                                  Text(
                                    "اختر مدينة أخرى لعرض المتاجر المتاحة فيها",
                                    style: TextStyle(
                                      fontFamily: 'Cairo',
                                      fontSize: 11.sp,
                                      color: const Color(0xFF64748B),
                                    ),
                                    textAlign: TextAlign.center,
                                  ),
                                  SizedBox(height: 12.h),
                                  ElevatedButton(
                                    onPressed: () {
                                      showHomeCityPicker(context);
                                    },
                                    style: ElevatedButton.styleFrom(
                                      backgroundColor: const Color(0xFF156172),
                                      foregroundColor: Colors.white,
                                      shape: RoundedRectangleBorder(
                                        borderRadius:
                                            BorderRadius.circular(20.r),
                                      ),
                                      padding: EdgeInsets.symmetric(
                                          horizontal: 18.w, vertical: 8.h),
                                    ),
                                    child: Text(
                                      "اختيار مدينة أخرى",
                                      style: TextStyle(
                                        fontFamily: 'Cairo',
                                        fontSize: 11.5.sp,
                                        fontWeight: FontWeight.w700,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            )
                          else
                            GridView.builder(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              gridDelegate:
                                  SliverGridDelegateWithFixedCrossAxisCount(
                                crossAxisCount: 2,
                                crossAxisSpacing: 12.w,
                                mainAxisSpacing: 12.h,
                                childAspectRatio: 0.78,
                              ),
                              itemCount: filteredVendors.length,
                              itemBuilder: (context, index) {
                                final v = filteredVendors[index];
                                return StoreGridCard(
                                  vendor: v,
                                  onTap: () => Navigator.of(context).push(
                                    MaterialPageRoute(
                                      builder: (_) =>
                                          VendorStoreScreen(vendor: v),
                                    ),
                                  ),
                                );
                              },
                            ),
                        ],
                      );
                    },
                    error: (_) => Center(child: Text(_.error.toString())),
                  );
                },
              ),
            ),

            // Wide Banners 1200x318 - directly below Available Stores
            Consumer(builder: (context, ref, _) {
              return ref.watch(wideBannersProvider).map(
                    initial: (_) => const SizedBox.shrink(),
                    loading: (_) => Padding(
                      padding:
                          EdgeInsets.symmetric(horizontal: 16.w, vertical: 8.h),
                      child: Container(
                        height: 104.h,
                        decoration: BoxDecoration(
                          color: const Color(0xFFE2E8F0),
                          borderRadius: BorderRadius.circular(16.r),
                        ),
                      ),
                    ),
                    loaded: (_) {
                      final banners = _.data.data?.wideBanners ?? [];
                      final imgs = banners
                          .where(
                              (b) => (b.imagePath?.toString() ?? '').isNotEmpty)
                          .toList();
                      if (imgs.isEmpty) return const SizedBox.shrink();
                      return Padding(
                        padding: EdgeInsets.symmetric(
                            horizontal: 16.w, vertical: 8.h),
                        child: AspectRatio(
                          aspectRatio: 1200 / 318,
                          child: CarouselSlider(
                            options: CarouselOptions(
                              viewportFraction: 1.0,
                              autoPlay: imgs.length > 1,
                              autoPlayInterval: const Duration(seconds: 4),
                              autoPlayAnimationDuration:
                                  const Duration(milliseconds: 800),
                              enlargeCenterPage: false,
                              enableInfiniteScroll: imgs.length > 1,
                            ),
                            items: imgs.map((banner) {
                              final img = resolveApiImageUrl(
                                  banner.imagePath.toString());
                              return Builder(builder: (context) {
                                return ClipRRect(
                                  borderRadius: BorderRadius.circular(16.r),
                                  child: Image.network(
                                    img,
                                    fit: BoxFit.cover,
                                    width: double.infinity,
                                    cacheWidth: _bannerDecodeWidth,
                                    loadingBuilder: (context, child,
                                            progress) =>
                                        progress == null
                                            ? child
                                            : Container(
                                                color: const Color(0xFFE2E8F0),
                                                alignment: Alignment.center,
                                                child: const SizedBox(
                                                    width: 22,
                                                    height: 22,
                                                    child:
                                                        CircularProgressIndicator(
                                                            strokeWidth: 2))),
                                    errorBuilder:
                                        (context, error, stackTrace) =>
                                            Container(
                                                color: const Color(0xFFE2E8F0)),
                                  ),
                                );
                              });
                            }).toList(),
                          ),
                        ),
                      );
                    },
                    error: (_) => const SizedBox.shrink(),
                  );
            }),
            SizedBox(height: 20.h),
            // Partners / Trusted Vendors — controlled by the super admin
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16.w),
              child: ValueListenableBuilder(
                valueListenable:
                    Hive.box(AppHSC.appSettingsBox).listenable(),
                builder: (context, Box box, _) {
                  final raw = box.get('show_partners_section');
                  final enabled = raw == null ||
                      raw == true ||
                      raw == 1 ||
                      raw.toString() == '1';
                  if (!enabled) return const SizedBox.shrink();
                  return Consumer(builder: (context, ref, _) {
                final vendorsAsync = ref.watch(vendorsProvider);
                final selectedArea = ref.watch(selectedVendorAreaProvider);
                return vendorsAsync.maybeWhen(
                  loaded: (_) {
                    var vendors = _.data?.vendors ?? [];
                    // Only stores the super admin marked as trusted partners
                    vendors = vendors.where((v) => v.showInPartners).toList();
                    if (selectedArea != 'الكل') {
                      vendors = vendors
                          .where((v) => _vendorMatchesArea(v, selectedArea))
                          .toList();
                    }
                    if (vendors.isEmpty) return const SizedBox.shrink();
                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const HomeSectionTitle(text: 'شركاؤنا الموثوقون'),
                        SizedBox(height: 12.h),
                        SizedBox(
                          height: 96.h,
                          child: ListView.separated(
                            scrollDirection: Axis.horizontal,
                            physics: const BouncingScrollPhysics(),
                            itemCount: vendors.length,
                            separatorBuilder: (_, __) => SizedBox(width: 14.w),
                            itemBuilder: (context, i) {
                              final v = vendors[i];
                              final hasRealImage = v.displayImage.isNotEmpty &&
                                  !v.displayImage.contains('dummy-placeholder');
                              return GestureDetector(
                                onTap: () => Navigator.of(context).push(
                                  MaterialPageRoute(
                                    builder: (_) =>
                                        VendorStoreScreen(vendor: v),
                                  ),
                                ),
                                child: SizedBox(
                                  width: 68.w,
                                  child: Column(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Container(
                                        width: 62.w,
                                        height: 62.h,
                                        padding: EdgeInsets.all(2.5.w),
                                        decoration: BoxDecoration(
                                          shape: BoxShape.circle,
                                          gradient: const LinearGradient(
                                            colors: [
                                              Color(0xFF156172),
                                              Color(0xFF1b758a),
                                            ],
                                            begin: Alignment.topLeft,
                                            end: Alignment.bottomRight,
                                          ),
                                          boxShadow: [
                                            BoxShadow(
                                              color: const Color(0xFF156172)
                                                  .withOpacity(0.18),
                                              blurRadius: 10,
                                              offset: const Offset(0, 4),
                                            ),
                                          ],
                                        ),
                                        child: Container(
                                          decoration: const BoxDecoration(
                                            color: Colors.white,
                                            shape: BoxShape.circle,
                                          ),
                                          clipBehavior: Clip.antiAlias,
                                          child: hasRealImage
                                              ? AppNetworkImage(
                                                  v.displayImage,
                                                  width: 58.w,
                                                  height: 58.h,
                                                  fit: BoxFit.cover,
                                                  placeholder:
                                                      'assets/images/app_icon.png',
                                                )
                                              : Center(
                                                  child: Text(
                                                    v.displayName.isNotEmpty
                                                        ? v.displayName
                                                            .characters.first
                                                            .toUpperCase()
                                                        : "M",
                                                    style: TextStyle(
                                                      fontFamily: 'Cairo',
                                                      fontSize: 18.sp,
                                                      fontWeight:
                                                          FontWeight.w900,
                                                      color: const Color(
                                                          0xFF156172),
                                                    ),
                                                  ),
                                                ),
                                        ),
                                      ),
                                      SizedBox(height: 6.h),
                                      Text(
                                        v.displayName,
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        textAlign: TextAlign.center,
                                        style: TextStyle(
                                          fontFamily: 'Cairo',
                                          fontSize: 10.5.sp,
                                          fontWeight: FontWeight.w700,
                                          color: const Color(0xFF334155),
                                          height: 1.1,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                      ],
                    );
                  },
                  orElse: () => const SizedBox.shrink(),
                );
              });
                },
              ),
            ),
          ],
          SizedBox(height: 120.h),
        ],
      ),
    );
  }

  Widget _serviceDynamicCard(
      Service service, BuildContext context, WidgetRef ref) {
    final img = service.imagePath?.toString().trim() ?? "";
    final name = service.name?.toString().trim() ?? "";
    final hasImage = img.isNotEmpty;
    return GestureDetector(
      onTap: () {
        Navigator.of(context).push(MaterialPageRoute(
          builder: (_) => ServiceVendorsScreen(service: service),
        ));
      },
      child: SizedBox(
        width: 72.w,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 64.w,
              height: 64.h,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(18.r),
                border: Border.all(
                  color: const Color(0xFF156172).withOpacity(0.14),
                  width: 1.2,
                ),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF156172).withOpacity(0.08),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              clipBehavior: Clip.antiAlias,
              child: hasImage
                  ? AppNetworkImage(
                      img,
                      fit: BoxFit.cover,
                      width: 64.w,
                      height: 64.h,
                      placeholder: 'assets/images/app_icon.png',
                    )
                  : Container(
                      decoration: const BoxDecoration(
                        gradient: LinearGradient(
                          colors: [Color(0xFFEAF6F8), Color(0xFFD6EEF2)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                      ),
                      child: Center(
                        child: Icon(
                          Icons.local_laundry_service_rounded,
                          size: 26.sp,
                          color: const Color(0xFF156172),
                        ),
                      ),
                    ),
            ),
            SizedBox(height: 6.h),
            Text(
              name.isNotEmpty ? name : 'خدمة',
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 11.sp,
                fontWeight: FontWeight.w800,
                color: const Color(0xFF0F2E35),
                fontFamily: 'Cairo',
                height: 1.1,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _offerSquareCard(OfferItem offer) {
    final img = offer.imagePath?.toString().trim() ?? "";
    final title = offer.title?.toString().trim() ?? 'عرض خاص';
    final vendor = offer.vendorName?.toString().trim() ?? 'مغسلة معتمدة';
    final discountLabel = offer.discountLabel?.toString().trim() ??
        (offer.discountValue != null && offer.discountValue! > 0
            ? "${offer.discountValue!.toInt()}% OFF"
            : "خصم خاص");
    final description = offer.description?.toString().trim() ?? '';
    final hasImage = img.isNotEmpty && !img.contains('dummy-placeholder');

    return Container(
      width: 175.w,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20.r),
        border: Border.all(
          color: const Color(0xFFE2E8F0),
          width: 1.2,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0F2E35).withOpacity(0.06),
            blurRadius: 14,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Banner / Cover image with discount badge
          SizedBox(
            height: 96.h,
            width: double.infinity,
            child: Stack(
              fit: StackFit.expand,
              children: [
                hasImage
                    ? AppNetworkImage(
                        img,
                        fit: BoxFit.cover,
                        width: double.infinity,
                        height: 96.h,
                        placeholder: 'assets/images/app_icon.png',
                      )
                    : Container(
                        decoration: const BoxDecoration(
                          gradient: LinearGradient(
                            colors: [Color(0xFF0A4A54), Color(0xFF156172)],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                        ),
                        child: Center(
                          child: Icon(
                            Icons.card_giftcard_rounded,
                            size: 36.sp,
                            color: Colors.white.withOpacity(0.92),
                          ),
                        ),
                      ),
                if (discountLabel.isNotEmpty)
                  Positioned(
                    top: 8.h,
                    left: 8.w,
                    child: Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 7.w, vertical: 3.h),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFFFF5722), Color(0xFFFF8A65)],
                        ),
                        borderRadius: BorderRadius.circular(20.r),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFFFF5722).withOpacity(0.3),
                            blurRadius: 6,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Text(
                        discountLabel,
                        style: TextStyle(
                          fontSize: 9.5.sp,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                          fontFamily: 'Cairo',
                        ),
                      ),
                    ),
                  ),
              ],
            ),
          ),
          // Body content
          Expanded(
            child: Padding(
              padding: EdgeInsets.all(10.w),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        title,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(
                          fontSize: 12.5.sp,
                          fontWeight: FontWeight.w900,
                          color: const Color(0xFF0F2E35),
                          fontFamily: 'Cairo',
                          height: 1.15,
                        ),
                      ),
                      SizedBox(height: 3.h),
                      Row(
                        children: [
                          Icon(
                            Icons.storefront_rounded,
                            size: 11.sp,
                            color: const Color(0xFF156172),
                          ),
                          SizedBox(width: 3.w),
                          Expanded(
                            child: Text(
                              vendor,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: TextStyle(
                                fontSize: 10.sp,
                                fontWeight: FontWeight.w600,
                                color: const Color(0xFF64748B),
                                fontFamily: 'Cairo',
                              ),
                            ),
                          ),
                        ],
                      ),
                      if (description.isNotEmpty) ...[
                        SizedBox(height: 2.h),
                        Text(
                          description,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(
                            fontSize: 9.sp,
                            color: Colors.grey.shade600,
                            fontFamily: 'Cairo',
                          ),
                        ),
                      ],
                    ],
                  ),
                  // CTA button
                  GestureDetector(
                    onTap: () {
                      final List<Vendor> vendors =
                          ref.read(vendorsProvider).maybeWhen(
                                loaded: (d) => d.data?.vendors ?? const [],
                                orElse: () => const [],
                              );
                      final Vendor targetVendor = vendors.firstWhere(
                        (v) => v.id.toString() == offer.vendorId?.toString(),
                        orElse: () => Vendor(id: offer.vendorId, name: vendor),
                      );
                      // Offer linked to a product → open that product's service
                      if (offer.serviceId != null) {
                        ref.read(productsFilterProvider.notifier).state =
                            ProducServiceVariavtionDataModel(
                          servieID: offer.serviceId!.toString(),
                          variationID: '',
                        );
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) => ChooseItems(
                              service: Service(
                                id: offer.serviceId,
                                name: offer.serviceName ??
                                    offer.title ??
                                    'عرض خاص',
                              ),
                              vendorId: offer.vendorId?.toString(),
                              vendorName: offer.vendorName,
                            ),
                          ),
                        );
                      } else {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (_) =>
                                VendorStoreScreen(vendor: targetVendor),
                          ),
                        );
                      }
                    },
                    child: Container(
                      width: double.infinity,
                      padding: EdgeInsets.symmetric(vertical: 4.h),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEAF6F8),
                        borderRadius: BorderRadius.circular(8.r),
                      ),
                      child: Center(
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text(
                              "استفد من العرض",
                              style: TextStyle(
                                fontFamily: 'Cairo',
                                fontSize: 9.5.sp,
                                fontWeight: FontWeight.w800,
                                color: const Color(0xFF156172),
                              ),
                            ),
                            SizedBox(width: 4.w),
                            Icon(
                              Icons.arrow_forward_rounded,
                              size: 11.sp,
                              color: const Color(0xFF156172),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAreaPicker() => const HomeCityPicker();

  bool _vendorMatchesArea(dynamic vendor, String area) {
    final id = ref.read(currentCityIdProvider);
    return id != null && (vendor as Vendor).cityId == id;
  }
}
