import 'package:carousel_slider/carousel_slider.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/screens/store/vendor_store_screen.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/store_card.dart';
import 'package:laundry_customer/widgets/vendor_search_bar.dart';

class HomeTab extends ConsumerStatefulWidget {
  const HomeTab({super.key});
  @override
  ConsumerState<HomeTab> createState() => _HomeTabState();
}

class _HomeTabState extends ConsumerState<HomeTab> {
  @override
  Widget build(BuildContext context) {
    final bool isAr = Localizations.localeOf(context).languageCode == 'ar';
    return Directionality(
      textDirection: isAr ? TextDirection.rtl : TextDirection.ltr,
      child: Container(
        color: const Color(0xFFFAF7F2),
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            // BEGIN: GreetingHeader - بدون أيقونة السلام
            Container(
              color: Colors.white,
              child: SafeArea(
                bottom: false,
                child: Padding(
                  padding: EdgeInsets.fromLTRB(20.w, 12.h, 20.w, 12.h),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Container(
                              padding: EdgeInsets.symmetric(horizontal: 8.w, vertical: 2.h),
                              decoration: BoxDecoration(
                                color: const Color(0xFFEFFAF8),
                                borderRadius: BorderRadius.circular(20.r),
                                border: Border.all(color: const Color(0xFF00A896).withOpacity(0.14)),
                              ),
                              child: Text(isAr ? "أهلاً بك معنا" : "Welcome with us", style: TextStyle(fontSize: 10.5.sp, fontWeight: FontWeight.w700, color: const Color(0xFF028090))),
                            ),
                            SizedBox(height: 4.h),
                            Text(
                              Hive.box(AppHSC.authBox).get('token') != null
                                  ? (Hive.box(AppHSC.userBox).get('name')?.toString().trim().isNotEmpty == true
                                      ? Hive.box(AppHSC.userBox).get('name').toString()
                                      : (isAr ? "مرحباً بك" : "Welcome"))
                                  : (isAr ? "أهلاً وسهلاً — سجّل دخولك للمتابعة" : "Welcome — Please log in to continue"),
                              style: TextStyle(fontSize: 17.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), height: 1.1, fontFamily: 'Cairo'),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                      SizedBox(width: 12.w),
                      Material(
                        color: Colors.transparent,
                        child: InkWell(
                          borderRadius: BorderRadius.circular(16.r),
                          onTap: () {
                            final hasToken = Hive.box(AppHSC.authBox).get('token') != null;
                            if (!hasToken) {
                              context.nav.pushNamed(Routes.loginScreen);
                            } else {
                              // إذا مسجل بالفعل اذهب للملف الشخصي
                              context.nav.pushNamed(Routes.loginScreen);
                            }
                          },
                          child: Container(
                            width: 44.w,
                            height: 44.h,
                            decoration: BoxDecoration(
                              color: const Color(0xFFF8F5EE),
                              borderRadius: BorderRadius.circular(16.r),
                              border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.8)),
                            ),
                            child: const Icon(Icons.arrow_forward, size: 18, color: Color(0xFF028090)),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),

            // BEGIN: HeroPromotionalBanner - صورة فقط ديناميكية من السوبر أدمن GET /api/promotions
            Consumer(
              builder: (context, ref, _) {
                return ref.watch(allPromotionsProvider).map(
                      initial: (_) => const SizedBox.shrink(),
                      loading: (_) => Padding(
                        padding: EdgeInsets.symmetric(horizontal: 20.w, vertical: 6.h),
                        child: Container(
                          height: 170.h,
                          width: double.infinity,
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(24.r)),
                          child: const Center(child: SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF028090)))),
                        ),
                      ),
                      loaded: (_) {
                        final promos = _.data.data?.promotions ?? [];
                        if (promos.isEmpty) return const SizedBox.shrink();
                        return Padding(
                          padding: EdgeInsets.fromLTRB(20.w, 6.h, 20.w, 4.h),
                          child: CarouselSlider(
                            options: CarouselOptions(
                              height: 168.h,
                              viewportFraction: 1.0,
                              autoPlay: promos.length > 1,
                              autoPlayInterval: const Duration(seconds: 4),
                              enlargeCenterPage: false,
                              aspectRatio: 16 / 9,
                            ),
                            items: promos.map((promo) {
                              final img = promo.imagePath?.toString() ?? "";
                              return Builder(builder: (context) {
                                return Container(
                                  width: double.infinity,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(24.r),
                                    boxShadow: [BoxShadow(color: const Color(0xFF028090).withOpacity(0.18), blurRadius: 22, offset: const Offset(0, 10))],
                                  ),
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(24.r),
                                    child: img.isNotEmpty
                                        ? AppNetworkImage(img, fit: BoxFit.cover, width: double.infinity, height: 168.h, placeholder: 'assets/images/app_icon.png')
                                        : Container(
                                            color: const Color(0xFFEFFAF8),
                                            alignment: Alignment.center,
                                            child: Icon(Icons.image_outlined, size: 32.sp, color: const Color(0xFF94A3B8)),
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
              },
            ),

            // BEGIN: ServicesSection
            Padding(
              padding: EdgeInsets.fromLTRB(20.w, 18.h, 20.w, 0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(isAr ? "الخدمات الرئيسية" : "Main Services", style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w800, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
                          Text(isAr ? "اختر فئتك المفضلة للتسوق الفوري" : "Choose your favorite category", style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w600, color: const Color(0xFF64748B))),
                        ],
                      ),
                      GestureDetector(
                        onTap: () {},
                        child: Row(
                          children: [
                            Text(isAr ? "عرض الكل" : "View All", style: TextStyle(fontSize: 11.5.sp, fontWeight: FontWeight.w800, color: const Color(0xFF00A896))),
                            SizedBox(width: 2.w),
                            Icon(Icons.chevron_left, size: 14.sp, color: const Color(0xFF00A896)),
                          ],
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 14.h),
                  Consumer(
                    builder: (context, ref, _) {
                      return ref.watch(allServicesProvider).map(
                            initial: (_) => const SizedBox.shrink(),
                            loading: (_) => GridView.count(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              crossAxisCount: 4,
                              mainAxisSpacing: 10.h,
                              crossAxisSpacing: 10.w,
                              children: List.generate(4, (_) => Container(decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16.r)))),
                            ),
                            loaded: (_) {
                              final services = _.data.data!.services!;
                              if (services.isEmpty) {
                                return Container(
                                  width: double.infinity,
                                  padding: EdgeInsets.all(18.w),
                                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16.r)),
                                  child: Text(isAr ? "لا توجد خدمات متاحة" : "No services available", textAlign: TextAlign.center, style: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8))),
                                );
                              }
                              return GridView.builder(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                                  crossAxisCount: 4,
                                  mainAxisSpacing: 10.h,
                                  crossAxisSpacing: 10.w,
                                  childAspectRatio: 0.64,
                                ),
                                itemCount: services.length,
                                itemBuilder: (context, i) {
                                  final s = services[i];
                                  final isActive = i == 0;
                                  return _serviceCardHTML(s, ref, context, isActive);
                                },
                              );
                            },
                            error: (_) => Text(_.error.toString(), style: TextStyle(fontSize: 11.sp, color: Colors.red)),
                          );
                    },
                  ),
                ],
              ),
            ),

            // مسافة ديناميكية حسب عناصر الخدمات
            Consumer(
              builder: (context, ref, _) {
                final count = ref.watch(allServicesProvider).maybeMap(loaded: (s) => s.data.data?.services?.length ?? 0, orElse: () => 0);
                final isLoading = ref.watch(allServicesProvider).maybeMap(loading: (_) => true, orElse: () => false);
                double gap;
                if (isLoading || count == 0) {
                  gap = 12.h;
                } else if (count <= 4) {
                  gap = 16.h;
                } else if (count <= 8) {
                  gap = 20.h;
                } else {
                  gap = 24.h;
                }
                return SizedBox(height: gap);
              },
            ),

            // BEGIN: StoresSearchAndFilter
            Padding(
              padding: EdgeInsets.fromLTRB(20.w, 0, 20.w, 0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Text(isAr ? "المتاجر المتاحة" : "Available Stores", style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
                          SizedBox(width: 8.w),
                          Consumer(builder: (context, ref, _) {
                            final c = ref.watch(vendorsProvider).maybeMap(loaded: (s) => s.data.data?.vendors?.length ?? 0, orElse: () => 0);
                            return Container(
                              padding: EdgeInsets.symmetric(horizontal: 7.w, vertical: 2.h),
                              decoration: BoxDecoration(color: const Color(0xFFE2E8F0).withOpacity(0.72), borderRadius: BorderRadius.circular(20.r)),
                              child: Text("$c+", style: TextStyle(fontSize: 10.5.sp, fontWeight: FontWeight.w800, color: const Color(0xFF334155))),
                            );
                          }),
                        ],
                      ),
                      InkWell(
                        borderRadius: BorderRadius.circular(8.r),
                        onTap: () => _showFilterSheet(context, ref),
                        child: Padding(
                          padding: EdgeInsets.symmetric(horizontal: 6.w, vertical: 4.h),
                          child: Row(
                            children: [
                              Icon(Icons.tune_rounded, size: 14.sp, color: const Color(0xFF028090)),
                              SizedBox(width: 4.w),
                              Text(isAr ? "تصفية" : "Filter", style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090))),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 12.h),
                  const VendorSearchBar(),
                ],
              ),
            ),

            // BEGIN: StoreCardsList - بيانات حقيقية فقط - شبكة مربعة 2 في الصف
            Padding(
              padding: EdgeInsets.fromLTRB(20.w, 14.h, 20.w, 0),
              child: Consumer(
                builder: (context, ref, _) {
                  final vendorsAsync = ref.watch(vendorsProvider);
                  return vendorsAsync.map(
                    initial: (_) => const SizedBox.shrink(),
                    loading: (_) => GridView.count(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      crossAxisCount: 2,
                      mainAxisSpacing: 12.h,
                      crossAxisSpacing: 12.w,
                      childAspectRatio: 0.82,
                      children: List.generate(4, (_) => Container(decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20.r)))),
                    ),
                    loaded: (_) {
                      final vendors = _.data.data?.vendors ?? [];
                      if (vendors.isEmpty) {
                        return Container(
                          width: double.infinity,
                          padding: EdgeInsets.all(20.w),
                          decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16.r)),
                          child: Text(isAr ? "لا توجد متاجر متاحة حالياً" : "No stores available", textAlign: TextAlign.center, style: TextStyle(fontSize: 12.sp, color: const Color(0xFF94A3B8))),
                        );
                      }
                      return GridView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2,
                          mainAxisSpacing: 12.h,
                          crossAxisSpacing: 12.w,
                          childAspectRatio: 0.82,
                        ),
                        itemCount: vendors.length,
                        itemBuilder: (context, i) => _storeSquareCard(vendors[i], context),
                      );
                    },
                    error: (_) => Text(_.error.toString(), style: TextStyle(fontSize: 11.sp, color: Colors.red)),
                  );
                },
              ),
            ),
            SizedBox(height: 120.h),
          ],
        ),
      ),
    );
  }

  Widget _serviceCardHTML(dynamic service, WidgetRef ref, BuildContext context, bool isActive) {
    final img = service.imagePath?.toString() ?? service.thumbnailPath?.toString() ?? "";
    final name = service.name?.toString().trim() ?? "";
    final hasImage = img.isNotEmpty;
    // وصف ديناميكي حقيقي فقط - لا نص ثابت
    String subTitle = "";
    final desc = service.description?.toString().trim() ?? "";
    if (desc.isNotEmpty) {
      subTitle = desc.length > 14 ? "${desc.substring(0, 14)}…" : desc;
    }
    return GestureDetector(
      onTap: () {
        ref.refresh(servicesVariationsProvider(service.id.toString()));
        ref.refresh(productsFilterProvider);
        ref.watch(itemSelectMenuIndexProvider.notifier).state = 0;
        context.nav.pushNamed(Routes.chooseItemScreen, arguments: service);
      },
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: double.infinity,
            height: 74.h,
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16.r),
              border: Border.all(color: isActive ? const Color(0xFF00A896).withOpacity(0.26) : const Color(0xFFE2E8F0).withOpacity(0.75), width: isActive ? 1.6 : 1),
              boxShadow: [BoxShadow(color: const Color(0xFF0C2238).withOpacity(0.06), blurRadius: 14, offset: const Offset(0, 6))],
            ),
            padding: EdgeInsets.all(8.w),
            child: Stack(
              children: [
                Center(
                  child: Container(
                    width: 44.w,
                    height: 44.h,
                    decoration: BoxDecoration(
                      color: hasImage ? Colors.white : const Color(0xFFEFFAF8),
                      borderRadius: BorderRadius.circular(12.r),
                      border: Border.all(color: const Color(0xFF00A896).withOpacity(0.12)),
                    ),
                    child: hasImage
                        ? ClipRRect(
                            borderRadius: BorderRadius.circular(12.r),
                            child: AppNetworkImage(img, fit: BoxFit.cover, width: 44.w, height: 44.h, placeholder: 'assets/images/app_icon.png'),
                          )
                        : Icon(Icons.category_rounded, size: 20.sp, color: const Color(0xFF028090)),
                  ),
                ),
                if (isActive)
                  Positioned(top: 0, right: 0, child: Container(width: 8.w, height: 8.h, decoration: const BoxDecoration(color: Color(0xFF00A896), shape: BoxShape.circle))),
              ],
            ),
          ),
          SizedBox(height: 6.h),
          SizedBox(
            height: 14.h,
            child: Text(name.isNotEmpty ? name : "", style: TextStyle(fontSize: 11.sp, fontWeight: FontWeight.w800, color: const Color(0xFF1E293B), height: 1), maxLines: 1, overflow: TextOverflow.ellipsis, textAlign: TextAlign.center),
          ),
          SizedBox(
            height: 12.h,
            child: subTitle.isNotEmpty
                ? Text(subTitle, style: TextStyle(fontSize: 9.sp, fontWeight: FontWeight.w600, color: const Color(0xFF64748B), height: 1), maxLines: 1, overflow: TextOverflow.ellipsis, textAlign: TextAlign.center)
                : const SizedBox.shrink(),
          ),
        ],
      ),
    );
  }

  void _showFilterSheet(BuildContext context, WidgetRef ref) {
    final currentQuery = ref.read(vendorSearchQueryProvider);
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(20.r))),
      builder: (_) => Padding(
        padding: EdgeInsets.fromLTRB(20.w, 16.h, 20.w, 24.h + MediaQuery.of(context).viewPadding.bottom),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(child: Container(width: 36.w, height: 4.h, decoration: BoxDecoration(color: const Color(0xFFE2E8F0), borderRadius: BorderRadius.circular(20.r)))),
            SizedBox(height: 16.h),
            Text("تصفية المتاجر", style: TextStyle(fontSize: 16.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo')),
            SizedBox(height: 4.h),
            Text("ابحث بالعنوان أو اختر إجراء سريع", style: TextStyle(fontSize: 11.sp, color: const Color(0xFF64748B))),
            SizedBox(height: 18.h),
            if (currentQuery.isNotEmpty)
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  onPressed: () {
                    ref.read(vendorSearchQueryProvider.notifier).state = '';
                    ref.invalidate(vendorsProvider);
                    Navigator.pop(context);
                  },
                  icon: Icon(Icons.clear_rounded, size: 18.sp, color: const Color(0xFF028090)),
                  label: Text("إعادة تعيين — عرض كل المتاجر", style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w800, color: const Color(0xFF028090))),
                  style: OutlinedButton.styleFrom(padding: EdgeInsets.symmetric(vertical: 12.h), side: const BorderSide(color: Color(0xFF00A896)), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))),
                ),
              ),
            if (currentQuery.isNotEmpty) SizedBox(height: 10.h),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () => Navigator.pop(context),
                icon: Icon(Icons.search_rounded, size: 18.sp, color: Colors.white),
                label: Text("متابعة البحث", style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: Colors.white)),
                style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF028090), padding: EdgeInsets.symmetric(vertical: 12.h), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12.r))),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _storeSquareCard(dynamic vendor, BuildContext context) {
    final img = vendor.displayImage?.toString() ?? "";
    final hasImage = img.isNotEmpty && !img.contains('dummy-placeholder');
    final name = vendor.displayName?.toString() ?? "";
    final addr = vendor.displayAddress?.toString() ?? "";
    return GestureDetector(
      onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => VendorStoreScreen(vendor: vendor))),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20.r),
          border: Border.all(color: const Color(0xFFE2E8F0).withOpacity(0.85)),
          boxShadow: [BoxShadow(color: const Color(0xFF0C2238).withOpacity(0.07), blurRadius: 16, offset: const Offset(0, 6))],
        ),
        child: Column(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.vertical(top: Radius.circular(20.r)),
              child: hasImage
                  ? AspectRatio(
                      aspectRatio: 1.42,
                      child: AppNetworkImage(img, fit: BoxFit.cover, width: double.infinity, placeholder: 'assets/images/app_icon.png'),
                    )
                  : Container(
                      height: 108.h,
                      width: double.infinity,
                      decoration: const BoxDecoration(gradient: LinearGradient(colors: [Color(0xFF00A896), Color(0xFF028090)], begin: Alignment.topRight, end: Alignment.bottomLeft)),
                      child: Center(
                        child: Text(name.isNotEmpty ? name.characters.first.toUpperCase() : "M", style: TextStyle(fontSize: 26.sp, fontWeight: FontWeight.w900, color: Colors.white, fontFamily: 'Cairo')),
                      ),
                    ),
            ),
            Expanded(
              child: Padding(
                padding: EdgeInsets.fromLTRB(10.w, 9.h, 10.w, 9.h),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(name, style: TextStyle(fontSize: 12.sp, fontWeight: FontWeight.w900, color: const Color(0xFF0B1E2E), fontFamily: 'Cairo'), maxLines: 1, overflow: TextOverflow.ellipsis),
                    SizedBox(height: 3.h),
                    Row(
                      children: [
                        Icon(Icons.location_on_rounded, size: 11.sp, color: const Color(0xFF00A896)),
                        SizedBox(width: 3.w),
                        Expanded(child: Text(addr.isNotEmpty ? addr : 'لا يوجد عنوان', style: TextStyle(fontSize: 10.sp, fontWeight: FontWeight.w600, color: const Color(0xFF64748B)), maxLines: 1, overflow: TextOverflow.ellipsis)),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
