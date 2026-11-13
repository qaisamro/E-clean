import 'package:flutter/material.dart';
import 'package:flutter_easyloading/flutter_easyloading.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/generated/l10n.dart';
import 'package:laundry_customer/misc/global_functions.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/models/hive_cart_item_model.dart';
import 'package:laundry_customer/models/products_model/product.dart';
import 'package:laundry_customer/models/variations_model/variant.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/providers/settings_provider.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/services/api_service.dart';
import 'package:laundry_customer/utils/cart_helper.dart';
import 'package:laundry_customer/utils/cart_single_vendor_guard.dart';
import 'package:laundry_customer/widgets/product_price_block.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/busy_loader.dart';
import 'package:laundry_customer/widgets/global_functions.dart';
import 'package:url_launcher/url_launcher.dart';

class VendorStoreScreen extends ConsumerStatefulWidget {
  const VendorStoreScreen({super.key, required this.vendor});
  final Vendor vendor;

  @override
  ConsumerState<VendorStoreScreen> createState() => _VendorStoreScreenState();
}

class _VendorStoreScreenState extends ConsumerState<VendorStoreScreen> {
  String? selectedServiceId;
  String? selectedServiceName;
  int selectedVariantIndex = 0;
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';
  String? _vendorPhone;
  double? _liveRating;
  int? _liveRatingCount;

  static const String _defaultCoverImage =
      'https://images.unsplash.com/photo-1545127398-14699f92334b?q=80&w=1000&auto=format&fit=crop';

  @override
  void initState() {
    super.initState();
    _vendorPhone = widget.vendor.phone;
    _liveRating = widget.vendor.rating;
    _liveRatingCount = widget.vendor.ratingCount;

    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(activeVendorIdProvider.notifier).state =
          widget.vendor.id.toString();
      ref.read(selectedVendorProvider.notifier).state = widget.vendor;
      _fetchLatestVendorDetails();
    });
  }

  Future<void> _fetchLatestVendorDetails() async {
    try {
      final dio = getDio();
      final res = await dio.get('/vendors/${widget.vendor.id}');
      final data = res.data['data']?['vendor'];
      if (data != null && mounted) {
        setState(() {
          final phone = data['phone']?.toString();
          if (phone != null && phone.isNotEmpty) {
            _vendorPhone = phone;
          }
          if (data['rating'] != null) {
            _liveRating = double.tryParse(data['rating'].toString());
          }
          if (data['rating_count'] != null) {
            _liveRatingCount = int.tryParse(data['rating_count'].toString());
          }
        });
      }
    } catch (_) {}
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _makePhoneCall() async {
    final phone = _vendorPhone ?? widget.vendor.phone;
    if (phone == null || phone.trim().isEmpty) {
      EasyLoading.showToast('رقم هاتف المتجر غير متوفر');
      return;
    }
    final cleanPhone = phone.replaceAll(RegExp(r'[^0-9+]'), '');
    final uri = Uri.parse('tel:$cleanPhone');
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    } else {
      EasyLoading.showToast('تعذر إجراء المكالمة بالرقم $cleanPhone');
    }
  }

  Future<void> _openLocation(Vendor vendor) async {
    if (vendor.latitude != null && vendor.longitude != null) {
      final uri = Uri.parse(
          'https://www.google.com/maps/search/?api=1&query=${vendor.latitude},${vendor.longitude}');
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
        return;
      }
    }
    if (vendor.displayAddress.isNotEmpty) {
      final query = Uri.encodeComponent(vendor.displayAddress);
      final uri =
          Uri.parse('https://www.google.com/maps/search/?api=1&query=$query');
      if (await canLaunchUrl(uri)) {
        await launchUrl(uri, mode: LaunchMode.externalApplication);
        return;
      }
    }
    EasyLoading.showToast('الموقع غير محدد لهذا المتجر');
  }

  void _openAllServicesSheet(List<Service> services) {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (ctx) => _AllServicesSheet(
        services: services,
        selectedServiceId: selectedServiceId,
        onSelectService: (Service svc) {
          final svcName = getLng(
            en: svc.name,
            changeLang: svc.nameBn?.toString(),
          );
          setState(() {
            selectedServiceId = svc.id.toString();
            selectedServiceName = svcName;
            selectedVariantIndex = 0;
          });
          ref.invalidate(servicesVariationsProvider(svc.id.toString()));
          ref
              .read(vendorProductFilterProvider(widget.vendor.id.toString())
                  .notifier)
              .state = VendorProductFilter(
            vendorId: widget.vendor.id.toString(),
            serviceId: svc.id.toString(),
            variantId: '',
          );
          Navigator.pop(ctx);
        },
        onSelectAll: () {
          setState(() {
            selectedServiceId = 'all';
            selectedServiceName = 'جميع الخدمات';
            selectedVariantIndex = 0;
          });
          ref
              .read(vendorProductFilterProvider(widget.vendor.id.toString())
                  .notifier)
              .state = VendorProductFilter(
            vendorId: widget.vendor.id.toString(),
            serviceId: 'all',
            variantId: '',
          );
          Navigator.pop(ctx);
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final vendorId = widget.vendor.id.toString();
    final vendorServicesAsync = ref.watch(vendorServicesProvider(vendorId));
    final appSettingsBox = Hive.box(AppHSC.appSettingsBox);
    final String currency =
        (appSettingsBox.get('currency') as String?) ?? '₪';
    int? minimum = 0;
    ref.watch(settingsProvider).whenOrNull(loaded: (d) {
      minimum = d.data!.minimumCost;
    });
    ref.watch(vendorCartsProvider);
    final vendorCartItems = CartHelper.getCartForVendor(vendorId);

    final coverUrl = (widget.vendor.backgroundPath?.isNotEmpty ?? false)
        ? widget.vendor.backgroundPath!
        : _defaultCoverImage;

    // Rating calculations: default 5.0 stars (without "(5 نجوم)" text)
    final double ratingVal = _liveRating ?? widget.vendor.displayRating;
    final int ratingReviews =
        _liveRatingCount ?? widget.vendor.displayRatingCount;
    final String ratingBadgeText = ratingReviews > 0
        ? '${ratingVal.toStringAsFixed(1)} ($ratingReviews تقييم)'
        : '5.0';

    // Dimensions: Cover height = 220.h, Card starts at 110.h (half on cover, half below)
    const double coverHeight = 220.0;
    const double cardTopPosition = 110.0;

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      body: Stack(
        children: [
          CustomScrollView(
            slivers: [
              // 1. Cover Image + Floating Card with ample space so it never collides with services
              SliverToBoxAdapter(
                child: SizedBox(
                  // 350.h gives enough vertical room for the card (ends ~320.h) + breathing space
                  height: 350.h,
                  child: Stack(
                    clipBehavior: Clip.none,
                    children: [
                      // A. Hero Cover Background with Turquoise/Teal Shadow Overlay (مطابق للصورة المرفقة)
                      Positioned(
                        top: 0,
                        left: 0,
                        right: 0,
                        height: coverHeight.h,
                        child: Stack(
                          children: [
                            Positioned.fill(
                              child: AppNetworkImage(
                                coverUrl,
                                fit: BoxFit.cover,
                                width: double.infinity,
                                height: coverHeight.h,
                              ),
                            ),
                            // Turquoise / Teal Gradient Overlay matching attached screenshot
                            Positioned.fill(
                              child: Container(
                                decoration: BoxDecoration(
                                  gradient: LinearGradient(
                                    begin: Alignment.topCenter,
                                    end: Alignment.bottomCenter,
                                    colors: [
                                      const Color(0xFF004D5A)
                                          .withValues(alpha: 0.92), // Rich dark teal at status bar
                                      const Color(0xFF028090)
                                          .withValues(alpha: 0.72), // Vibrant brand teal tint
                                      const Color(0xFF00A896)
                                          .withValues(alpha: 0.35), // Soft cyan glow over clothes
                                      Colors.transparent,           // Clear view in lower portion
                                      Colors.black
                                          .withValues(alpha: 0.22), // Subtle bottom fade
                                    ],
                                    stops: const [0.0, 0.24, 0.50, 0.78, 1.0],
                                  ),
                                ),
                              ),
                            ),
                            // Back Button on Top RIGHT (في اليمين)
                            SafeArea(
                              bottom: false,
                              child: Padding(
                                padding: EdgeInsets.symmetric(
                                  horizontal: 16.w,
                                  vertical: 8.h,
                                ),
                                child: Align(
                                  alignment: Alignment.topRight,
                                  child: InkWell(
                                    borderRadius: BorderRadius.circular(20.r),
                                    onTap: () => context.nav.pop(),
                                    child: Container(
                                      width: 40.w,
                                      height: 40.h,
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        shape: BoxShape.circle,
                                        boxShadow: [
                                          BoxShadow(
                                            color: Colors.black
                                                .withValues(alpha: 0.16),
                                            blurRadius: 10,
                                            offset: const Offset(0, 3),
                                          ),
                                        ],
                                      ),
                                      child: Center(
                                        child: Icon(
                                          Icons.arrow_forward_ios_rounded,
                                          size: 16.sp,
                                          color: const Color(0xFF0F172A),
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                      // B. Store Info Card with Teal Glow: half on the cover, half below it
                      Positioned(
                        top: cardTopPosition.h,
                        left: 16.w,
                        right: 16.w,
                        child: Container(
                          padding: EdgeInsets.all(16.w),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(28.r),
                            border: Border.all(
                              color: const Color(0xFF00A896)
                                  .withValues(alpha: 0.25),
                              width: 1.2,
                            ),
                            boxShadow: [
                              // Teal ambient glow matching site brand
                              BoxShadow(
                                color: const Color(0xFF028090)
                                    .withValues(alpha: 0.14),
                                blurRadius: 24,
                                spreadRadius: 1,
                                offset: const Offset(0, 8),
                              ),
                              BoxShadow(
                                color: const Color(0xFF00A896)
                                    .withValues(alpha: 0.08),
                                blurRadius: 12,
                                offset: const Offset(0, 3),
                              ),
                              BoxShadow(
                                color: const Color(0xFF0B1E2E)
                                    .withValues(alpha: 0.05),
                                blurRadius: 14,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              // Store Header: Logo ON THE RIGHT, Name & Location & Rating to the left of the logo
                              Directionality(
                                textDirection: TextDirection.rtl,
                                child: Row(
                                  crossAxisAlignment: CrossAxisAlignment.center,
                                  children: [
                                    // 1. Store Logo on the RIGHT (اللوجو على جهة اليمين)
                                    Container(
                                      width: 66.w,
                                      height: 66.h,
                                      padding: EdgeInsets.all(4.w),
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius:
                                            BorderRadius.circular(18.r),
                                        border: Border.all(
                                          color: const Color(0xFF00A896)
                                              .withValues(alpha: 0.25),
                                        ),
                                        boxShadow: [
                                          BoxShadow(
                                            color: const Color(0xFF028090)
                                                .withValues(alpha: 0.10),
                                            blurRadius: 8,
                                            offset: const Offset(0, 2),
                                          ),
                                        ],
                                      ),
                                      child: ClipRRect(
                                        borderRadius:
                                            BorderRadius.circular(14.r),
                                        child: AppNetworkImage(
                                          widget.vendor.displayImage,
                                          fit: BoxFit.contain,
                                          width: 58.w,
                                          height: 58.h,
                                        ),
                                      ),
                                    ),
                                    SizedBox(width: 14.w),

                                    // 2. Name, Location, Rating to the left of the Logo
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            widget.vendor.displayName,
                                            style: TextStyle(
                                              fontSize: 17.sp,
                                              fontWeight: FontWeight.w900,
                                              color: const Color(0xFF0F172A),
                                              fontFamily: 'Cairo',
                                            ),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                          SizedBox(height: 4.h),
                                          Row(
                                            children: [
                                              Icon(
                                                Icons.location_on_rounded,
                                                size: 14.sp,
                                                color: const Color(0xFF00A896),
                                              ),
                                              SizedBox(width: 4.w),
                                              Expanded(
                                                child: Text(
                                                  widget.vendor.city?.isNotEmpty ==
                                                          true
                                                      ? widget.vendor.city!
                                                      : widget.vendor
                                                              .displayAddress
                                                              .isNotEmpty
                                                          ? widget.vendor
                                                              .displayAddress
                                                          : 'فلسطين',
                                                  style: TextStyle(
                                                    fontSize: 12.sp,
                                                    color:
                                                        const Color(0xFF64748B),
                                                    fontFamily: 'Cairo',
                                                  ),
                                                  maxLines: 1,
                                                  overflow:
                                                      TextOverflow.ellipsis,
                                                ),
                                              ),
                                            ],
                                          ),
                                          SizedBox(height: 6.h),
                                          // Rating Pill Badge: Star + 5.0 (No "5 نجوم" text!)
                                          Container(
                                            padding: EdgeInsets.symmetric(
                                                horizontal: 10.w,
                                                vertical: 3.h),
                                            decoration: BoxDecoration(
                                              color: const Color(0xFFF1F5F9),
                                              borderRadius:
                                                  BorderRadius.circular(16.r),
                                            ),
                                            child: Row(
                                              mainAxisSize: MainAxisSize.min,
                                              children: [
                                                Icon(
                                                  Icons.star_rounded,
                                                  size: 15.sp,
                                                  color:
                                                      const Color(0xFFF59E0B),
                                                ),
                                                SizedBox(width: 4.w),
                                                Text(
                                                  ratingBadgeText,
                                                  style: TextStyle(
                                                    fontSize: 11.sp,
                                                    fontWeight: FontWeight.w700,
                                                    color:
                                                        const Color(0xFF1E293B),
                                                    fontFamily: 'Cairo',
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              SizedBox(height: 14.h),

                              // Action Buttons Row: [الموقع] and [اتصال] (تواصل معنا removed as requested)
                              Row(
                                children: [
                                  // 1. Location Pill Button ("الموقع")
                                  Expanded(
                                    child: InkWell(
                                      borderRadius:
                                          BorderRadius.circular(24.r),
                                      onTap: () =>
                                          _openLocation(widget.vendor),
                                      child: Container(
                                        height: 42.h,
                                        decoration: BoxDecoration(
                                          color: Colors.white,
                                          borderRadius:
                                              BorderRadius.circular(24.r),
                                          border: Border.all(
                                              color: const Color(0xFFE2E8F0)),
                                        ),
                                        child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            Icon(
                                              Icons.location_on_outlined,
                                              size: 16.sp,
                                              color: const Color(0xFF028090),
                                            ),
                                            SizedBox(width: 6.w),
                                            Text(
                                              'الموقع',
                                              style: TextStyle(
                                                fontSize: 12.sp,
                                                fontWeight: FontWeight.w800,
                                                color: const Color(0xFF0F172A),
                                                fontFamily: 'Cairo',
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ),
                                  ),
                                  SizedBox(width: 10.w),

                                  // 2. Call Real Store Button ("اتصال")
                                  Expanded(
                                    child: InkWell(
                                      borderRadius:
                                          BorderRadius.circular(24.r),
                                      onTap: _makePhoneCall,
                                      child: Container(
                                        height: 42.h,
                                        decoration: BoxDecoration(
                                          gradient: const LinearGradient(
                                            colors: [
                                              Color(0xFF028090),
                                              Color(0xFF00A896),
                                            ],
                                          ),
                                          borderRadius:
                                              BorderRadius.circular(24.r),
                                          boxShadow: [
                                            BoxShadow(
                                              color: const Color(0xFF00A896)
                                                  .withValues(alpha: 0.28),
                                              blurRadius: 8,
                                              offset: const Offset(0, 3),
                                            ),
                                          ],
                                        ),
                                        child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            Icon(
                                              Icons.phone_rounded,
                                              size: 16.sp,
                                              color: Colors.white,
                                            ),
                                            SizedBox(width: 6.w),
                                            Text(
                                              'اتصال',
                                              style: TextStyle(
                                                fontSize: 12.sp,
                                                fontWeight: FontWeight.w800,
                                                color: Colors.white,
                                                fontFamily: 'Cairo',
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              SizedBox(height: 12.h),

                              // Search Bar ("ابحث عن خدمة ...")
                              Container(
                                height: 44.h,
                                padding:
                                    EdgeInsets.symmetric(horizontal: 14.w),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFF1F5F9),
                                  borderRadius: BorderRadius.circular(24.r),
                                ),
                                child: Row(
                                  children: [
                                    Expanded(
                                      child: TextField(
                                        controller: _searchController,
                                        onChanged: (val) {
                                          setState(() {
                                            _searchQuery = val.trim();
                                          });
                                        },
                                        style: TextStyle(
                                          fontSize: 12.sp,
                                          fontFamily: 'Cairo',
                                          color: const Color(0xFF0F172A),
                                        ),
                                        decoration: InputDecoration(
                                          isDense: true,
                                          hintText: 'ابحث عن خدمة ...',
                                          hintStyle: TextStyle(
                                            fontSize: 12.sp,
                                            color: const Color(0xFF94A3B8),
                                            fontFamily: 'Cairo',
                                          ),
                                          border: InputBorder.none,
                                          contentPadding: EdgeInsets.zero,
                                        ),
                                      ),
                                    ),
                                    if (_searchQuery.isNotEmpty)
                                      InkWell(
                                        onTap: () {
                                          _searchController.clear();
                                          setState(() {
                                            _searchQuery = '';
                                          });
                                        },
                                        child: Padding(
                                          padding: EdgeInsets.symmetric(
                                              horizontal: 4.w),
                                          child: Icon(
                                            Icons.close_rounded,
                                            size: 16.sp,
                                            color: const Color(0xFF94A3B8),
                                          ),
                                        ),
                                      ),
                                    Icon(
                                      Icons.search_rounded,
                                      size: 20.sp,
                                      color: const Color(0xFF94A3B8),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // 2. Services / Categories Selector & Section Header
              SliverToBoxAdapter(
                child: vendorServicesAsync.map(
                  initial: (_) => const SizedBox(),
                  loading: (_) => const Padding(
                    padding: EdgeInsets.only(top: 10),
                    child: Center(
                        child: AppLoadingIndicator(size: 40, showText: false)),
                  ),
                  loaded: (_) {
                    final services = _.data.data?.services ?? [];
                    if (services.isEmpty) {
                      return Padding(
                        padding: EdgeInsets.only(top: 20.h),
                        child: Center(
                          child: Text(
                            S.of(context).nosrvcavlbl,
                            style: const TextStyle(
                                color: Color(0xFF64748B), fontFamily: 'Cairo'),
                          ),
                        ),
                      );
                    }
                    if (selectedServiceId == null && services.isNotEmpty) {
                      selectedServiceId = services.first.id.toString();
                      selectedServiceName = getLng(
                        en: services.first.name,
                        changeLang: services.first.nameBn.toString(),
                      );
                      Future.microtask(() => ref
                          .read(
                              vendorProductFilterProvider(vendorId).notifier)
                          .state = VendorProductFilter(
                        vendorId: vendorId,
                        serviceId: selectedServiceId!,
                        variantId: '',
                      ));
                    }

                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Categories Horizontal List (including "الكل" + All Store Services)
                        SizedBox(
                          height: 102.h,
                          child: ListView.separated(
                            padding: EdgeInsets.symmetric(horizontal: 16.w),
                            scrollDirection: Axis.horizontal,
                            itemCount: services.length + 1,
                            separatorBuilder: (_, __) => SizedBox(width: 10.w),
                            itemBuilder: (context, i) {
                              // Item 0 is "الكل" (All products & services)
                              if (i == 0) {
                                final isSel = selectedServiceId == 'all';
                                return InkWell(
                                  borderRadius: BorderRadius.circular(20.r),
                                  onTap: () {
                                    setState(() {
                                      selectedServiceId = 'all';
                                      selectedServiceName = 'جميع الخدمات';
                                      selectedVariantIndex = 0;
                                    });
                                    ref
                                        .read(
                                            vendorProductFilterProvider(vendorId)
                                                .notifier)
                                        .state = VendorProductFilter(
                                      vendorId: vendorId,
                                      serviceId: 'all',
                                      variantId: '',
                                    );
                                  },
                                  child: AnimatedContainer(
                                    duration: const Duration(milliseconds: 220),
                                    width: 84.w,
                                    padding: EdgeInsets.symmetric(
                                        vertical: 8.h, horizontal: 6.w),
                                    decoration: BoxDecoration(
                                      color: isSel
                                          ? const Color(0xFF028090)
                                          : Colors.white,
                                      borderRadius: BorderRadius.circular(20.r),
                                      border: Border.all(
                                        color: isSel
                                            ? const Color(0xFF028090)
                                            : const Color(0xFFE2E8F0),
                                      ),
                                      boxShadow: [
                                        BoxShadow(
                                          color: isSel
                                              ? const Color(0xFF028090)
                                                  .withValues(alpha: 0.35)
                                              : const Color(0xFF0B1E2E)
                                                  .withValues(alpha: 0.04),
                                          blurRadius: isSel ? 10 : 6,
                                          offset: const Offset(0, 3),
                                        ),
                                      ],
                                    ),
                                    child: Column(
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: [
                                        Container(
                                          width: 44.w,
                                          height: 44.h,
                                          decoration: BoxDecoration(
                                            color: isSel
                                                ? Colors.white
                                                    .withValues(alpha: 0.18)
                                                : const Color(0xFFF8FAFC),
                                            borderRadius:
                                                BorderRadius.circular(14.r),
                                          ),
                                          child: Center(
                                            child: Icon(
                                              Icons.grid_view_rounded,
                                              size: 22.sp,
                                              color: isSel
                                                  ? Colors.white
                                                  : const Color(0xFF028090),
                                            ),
                                          ),
                                        ),
                                        SizedBox(height: 6.h),
                                        Text(
                                          'الكل',
                                          style: TextStyle(
                                            fontSize: 11.sp,
                                            fontWeight: FontWeight.w900,
                                            color: isSel
                                                ? Colors.white
                                                : const Color(0xFF0F172A),
                                            fontFamily: 'Cairo',
                                          ),
                                          maxLines: 1,
                                          textAlign: TextAlign.center,
                                        ),
                                      ],
                                    ),
                                  ),
                                );
                              }

                              final svc = services[i - 1];
                              final isSel =
                                  selectedServiceId == svc.id.toString();
                              final svcName = getLng(
                                en: svc.name,
                                changeLang: svc.nameBn.toString(),
                              );

                              return InkWell(
                                borderRadius: BorderRadius.circular(20.r),
                                onTap: () {
                                  setState(() {
                                    selectedServiceId = svc.id.toString();
                                    selectedServiceName = svcName;
                                    selectedVariantIndex = 0;
                                  });
                                  ref.invalidate(servicesVariationsProvider(
                                      svc.id.toString()));
                                  ref
                                      .read(
                                          vendorProductFilterProvider(vendorId)
                                              .notifier)
                                      .state = VendorProductFilter(
                                    vendorId: vendorId,
                                    serviceId: svc.id.toString(),
                                    variantId: '',
                                  );
                                },
                                child: AnimatedContainer(
                                  duration: const Duration(milliseconds: 220),
                                  width: 84.w,
                                  padding: EdgeInsets.symmetric(
                                      vertical: 8.h, horizontal: 6.w),
                                  decoration: BoxDecoration(
                                    color: isSel
                                        ? const Color(0xFF028090)
                                        : Colors.white,
                                    borderRadius: BorderRadius.circular(20.r),
                                    border: Border.all(
                                      color: isSel
                                          ? const Color(0xFF028090)
                                          : const Color(0xFFE2E8F0),
                                    ),
                                    boxShadow: [
                                      BoxShadow(
                                        color: isSel
                                            ? const Color(0xFF028090)
                                                .withValues(alpha: 0.35)
                                            : const Color(0xFF0B1E2E)
                                                .withValues(alpha: 0.04),
                                        blurRadius: isSel ? 10 : 6,
                                        offset: const Offset(0, 3),
                                      ),
                                    ],
                                  ),
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Container(
                                        width: 44.w,
                                        height: 44.h,
                                        decoration: BoxDecoration(
                                          color: isSel
                                              ? Colors.white
                                                  .withValues(alpha: 0.18)
                                              : const Color(0xFFF8FAFC),
                                          borderRadius:
                                              BorderRadius.circular(14.r),
                                        ),
                                        child: Center(
                                          child: ClipRRect(
                                            borderRadius:
                                                BorderRadius.circular(10.r),
                                            child: AppNetworkImage(
                                              svc.imagePath,
                                              height: 32.h,
                                              width: 32.w,
                                              fit: BoxFit.contain,
                                            ),
                                          ),
                                        ),
                                      ),
                                      SizedBox(height: 6.h),
                                      Text(
                                        svcName,
                                        style: TextStyle(
                                          fontSize: 11.sp,
                                          fontWeight: FontWeight.w900,
                                          color: isSel
                                              ? Colors.white
                                              : const Color(0xFF0F172A),
                                          fontFamily: 'Cairo',
                                        ),
                                        maxLines: 1,
                                        textAlign: TextAlign.center,
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ],
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                        SizedBox(height: 16.h),

                        // Section Header: "خدمات [الاسم]" on Right / "عرض الكل" on Left (RTL)
                        Padding(
                          padding: EdgeInsets.symmetric(horizontal: 20.w),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                selectedServiceName != null
                                    ? selectedServiceId == 'all'
                                        ? 'جميع الخدمات'
                                        : 'خدمات $selectedServiceName'
                                    : 'الخدمات المتاحة',
                                style: TextStyle(
                                  fontSize: 16.sp,
                                  fontWeight: FontWeight.w900,
                                  color: const Color(0xFF0F172A),
                                  fontFamily: 'Cairo',
                                ),
                              ),
                              // Interactive "عرض الكل" Button
                              InkWell(
                                onTap: () => _openAllServicesSheet(services),
                                borderRadius: BorderRadius.circular(12.r),
                                child: Padding(
                                  padding: EdgeInsets.symmetric(
                                      horizontal: 6.w, vertical: 4.h),
                                  child: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Text(
                                        selectedServiceId == 'all'
                                            ? 'تصفية الأقسام'
                                            : 'عرض الكل',
                                        style: TextStyle(
                                          fontSize: 12.sp,
                                          fontWeight: FontWeight.w800,
                                          color: const Color(0xFF028090),
                                          fontFamily: 'Cairo',
                                        ),
                                      ),
                                      SizedBox(width: 4.w),
                                      Icon(
                                        Icons.arrow_back_ios_new_rounded,
                                        size: 12.sp,
                                        color: const Color(0xFF028090),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        SizedBox(height: 10.h),

                        // Optional Variations Tabs (Ironing, Washing, Dry Clean...)
                        if (selectedServiceId != null &&
                            selectedServiceId != 'all')
                          Consumer(
                            builder: (context, ref, _) {
                              final varAsync = ref.watch(
                                  servicesVariationsProvider(
                                      selectedServiceId!));
                              return varAsync.map(
                                initial: (_) => const SizedBox.shrink(),
                                loading: (_) => const SizedBox.shrink(),
                                loaded: (vd) {
                                  final vars = vd.data.data?.variants ?? [];
                                  if (vars.isEmpty) {
                                    return const SizedBox.shrink();
                                  }
                                  vars.sort((a, b) => a.id!.compareTo(b.id!));
                                  final cur = ref.watch(
                                      vendorProductFilterProvider(vendorId));
                                  if (cur.variantId.isEmpty) {
                                    Future.microtask(() => ref
                                        .read(vendorProductFilterProvider(
                                                vendorId)
                                            .notifier)
                                        .state = cur.copyWith(
                                            variantId:
                                                vars.first.id.toString()));
                                  }
                                  return SizedBox(
                                    height: 42.h,
                                    child: ListView.builder(
                                      scrollDirection: Axis.horizontal,
                                      padding: EdgeInsets.symmetric(
                                          horizontal: 16.w),
                                      itemCount: vars.length,
                                      itemBuilder: (context, idx) {
                                        final Variant v = vars[idx];
                                        final isActive =
                                            selectedVariantIndex == idx;
                                        return Padding(
                                          padding: EdgeInsets.only(left: 8.w),
                                          child: InkWell(
                                            borderRadius:
                                                BorderRadius.circular(20.r),
                                            onTap: () {
                                              setState(() =>
                                                  selectedVariantIndex = idx);
                                              ref
                                                  .read(
                                                      vendorProductFilterProvider(
                                                              vendorId)
                                                          .notifier)
                                                  .state = ref
                                                      .read(
                                                          vendorProductFilterProvider(
                                                              vendorId))
                                                      .copyWith(
                                                          variantId: v.id
                                                              .toString());
                                            },
                                            child: Container(
                                              padding: EdgeInsets.symmetric(
                                                  horizontal: 16.w),
                                              decoration: BoxDecoration(
                                                color: isActive
                                                    ? const Color(0xFF028090)
                                                    : Colors.white,
                                                borderRadius:
                                                    BorderRadius.circular(20.r),
                                                border: Border.all(
                                                  color: isActive
                                                      ? const Color(0xFF028090)
                                                      : const Color(0xFFE2E8F0),
                                                ),
                                              ),
                                              child: Center(
                                                child: Text(
                                                  getLng(
                                                    en: v.name,
                                                    changeLang:
                                                        v.nameBn.toString(),
                                                  ),
                                                  style: TextStyle(
                                                    fontSize: 12.sp,
                                                    fontWeight: isActive
                                                        ? FontWeight.w900
                                                        : FontWeight.w700,
                                                    color: isActive
                                                        ? Colors.white
                                                        : const Color(
                                                            0xFF334155),
                                                    fontFamily: 'Cairo',
                                                  ),
                                                ),
                                              ),
                                            ),
                                          ),
                                        );
                                      },
                                    ),
                                  );
                                },
                                error: (_) => const SizedBox.shrink(),
                              );
                            },
                          ),
                      ],
                    );
                  },
                  error: (e) => Center(
                    child: Text(
                      e.error,
                      style: const TextStyle(
                          color: Color(0xFFE63946), fontFamily: 'Cairo'),
                    ),
                  ),
                ),
              ),

              // 3. Products List
              if (selectedServiceId != null)
                Consumer(
                  builder: (context, ref, _) {
                    final filter =
                        ref.watch(vendorProductFilterProvider(vendorId));
                    if (filter.serviceId.isEmpty &&
                        selectedServiceId != 'all') {
                      return const SliverToBoxAdapter(
                        child: Padding(
                          padding: EdgeInsets.only(top: 30),
                          child: Center(
                              child: AppLoadingIndicator(
                                  size: 44, showText: false)),
                        ),
                      );
                    }
                    final prodAsync = ref.watch(vendorProductsProvider(filter));
                    return prodAsync.map(
                      initial: (_) => const SliverToBoxAdapter(
                        child: Padding(
                          padding: EdgeInsets.only(top: 30),
                          child: Center(
                              child: AppLoadingIndicator(
                                  size: 44, showText: false)),
                        ),
                      ),
                      loading: (_) => const SliverToBoxAdapter(
                        child: Padding(
                          padding: EdgeInsets.only(top: 30),
                          child: Center(
                              child: AppLoadingIndicator(
                                  size: 44, showText: false)),
                        ),
                      ),
                      loaded: (pd) {
                        var prods = pd.data.data?.products ?? [];

                        // Live search filter
                        if (_searchQuery.isNotEmpty) {
                          prods = prods.where((p) {
                            final name = p.name?.toLowerCase() ?? '';
                            final nameBn =
                                p.nameBn?.toString().toLowerCase() ?? '';
                            final desc = p.description?.toLowerCase() ?? '';
                            final q = _searchQuery.toLowerCase();
                            return name.contains(q) ||
                                nameBn.contains(q) ||
                                desc.contains(q);
                          }).toList();
                        }

                        if (prods.isEmpty) {
                          return SliverToBoxAdapter(
                            child: Padding(
                              padding: EdgeInsets.only(top: 40.h),
                              child: Center(
                                child: Text(
                                  _searchQuery.isNotEmpty
                                      ? 'لا توجد نتائج مطابقة للبحث'
                                      : 'لا توجد منتجات',
                                  style: const TextStyle(
                                    color: Color(0xFF64748B),
                                    fontFamily: 'Cairo',
                                  ),
                                ),
                              ),
                            ),
                          );
                        }

                        return SliverPadding(
                          padding: EdgeInsets.fromLTRB(16.w, 8.h, 16.w, 120.h),
                          sliver: SliverList.builder(
                            itemCount: prods.length,
                            itemBuilder: (context, i) => Padding(
                              padding: EdgeInsets.only(bottom: 12.h),
                              child: _VendorProductCard(
                                product: prods[i],
                                vendor: widget.vendor,
                                currency: currency,
                              ),
                            ),
                          ),
                        );
                      },
                      error: (e) => SliverToBoxAdapter(
                        child: Center(
                          child: Text(
                            e.error,
                            style: const TextStyle(
                                color: Color(0xFFE63946), fontFamily: 'Cairo'),
                          ),
                        ),
                      ),
                    );
                  },
                ),
            ],
          ),

          // 4. Floating Bottom Cart Bar
          if (vendorCartItems.isNotEmpty)
            Positioned(
              bottom: 0,
              left: 0,
              right: 0,
              child: Container(
                padding: EdgeInsets.fromLTRB(
                  16.w,
                  12.h,
                  16.w,
                  12.h + MediaQuery.of(context).viewPadding.bottom,
                ),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius:
                      const BorderRadius.vertical(top: Radius.circular(24)),
                  border: Border(
                      top: BorderSide(
                          color: const Color(0xFFE2E8F0).withValues(alpha: 0.9))),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF0B1E2E).withValues(alpha: 0.10),
                      blurRadius: 18,
                      offset: const Offset(0, -6),
                    ),
                  ],
                ),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Expanded(
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            S.of(context).ttl,
                            style: TextStyle(
                              fontSize: 11.sp,
                              color: const Color(0xFF64748B),
                              fontFamily: 'Cairo',
                            ),
                          ),
                          Text(
                            '$currency${CartHelper.calculateTotalForVendor(vendorId).toStringAsFixed(2)}',
                            style: TextStyle(
                              fontSize: 17.sp,
                              fontWeight: FontWeight.w900,
                              color: const Color(0xFF0F172A),
                              fontFamily: 'Cairo',
                            ),
                          ),
                          Text(
                            '${vendorCartItems.length} عناصر • ${widget.vendor.displayName}',
                            style: TextStyle(
                              fontSize: 10.sp,
                              color: const Color(0xFF94A3B8),
                              fontFamily: 'Cairo',
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                    SizedBox(width: 12.w),
                    SizedBox(
                      height: 46.h,
                      width: 140.w,
                      child: DecoratedBox(
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [Color(0xFF028090), Color(0xFF00A896)],
                          ),
                          borderRadius: BorderRadius.circular(16.r),
                          boxShadow: [
                            BoxShadow(
                              color:
                                  const Color(0xFF00A896).withValues(alpha: 0.3),
                              blurRadius: 8,
                              offset: const Offset(0, 3),
                            ),
                          ],
                        ),
                        child: ElevatedButton(
                          onPressed: () {
                            final authBox = Hive.box(AppHSC.authBox);
                            if (authBox.get(AppHSC.authToken) == null ||
                                authBox.get(AppHSC.authToken) == '') {
                              context.nav.pushNamed(Routes.loginScreen);
                              return;
                            }
                            if (CartHelper.calculateTotalForVendor(vendorId) >=
                                (minimum ?? 0)) {
                              ref.read(activeVendorIdProvider.notifier).state =
                                  vendorId;
                              context.nav.pushNamed(Routes.checkOutScreen);
                            } else {
                              EasyLoading.showError(
                                  '${S.of(context).mnmmordramnt} ${AppGFunctions.convertToFixedTwo(minimum!)}');
                            }
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.transparent,
                            shadowColor: Colors.transparent,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16.r),
                            ),
                          ),
                          child: Text(
                            S.of(context).ordrnow,
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w900,
                              fontFamily: 'Cairo',
                            ),
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
}

/// Product Card matching exact UI in screenshot
class _VendorProductCard extends ConsumerStatefulWidget {
  const _VendorProductCard({
    required this.product,
    required this.vendor,
    required this.currency,
  });

  final Product product;
  final Vendor vendor;
  final String currency;

  @override
  ConsumerState<_VendorProductCard> createState() => _VendorProductCardState();
}

class _VendorProductCardState extends ConsumerState<_VendorProductCard> {
  @override
  Widget build(BuildContext context) {
    final vendorId = widget.vendor.id.toString();
    final hasSubproducts = widget.product.sbproducts != null &&
        widget.product.sbproducts!.isNotEmpty;

    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.cartBox).listenable(),
      builder: (context, Box cartBox, _) {
        final items = CartHelper.getCartForVendor(vendorId);

        // Exact match for simple products
        final exact = CartHelper.findCartItem(
          vendorId: vendorId,
          productId: widget.product.id!,
        );

        // Count total items in cart for product with variants
        int totalSubproductsInCart = 0;
        if (hasSubproducts) {
          for (final it in items) {
            if (it.productsId == widget.product.id ||
                (it.subProduct != null &&
                    widget.product.sbproducts!.any((sp) =>
                        sp.id == it.subProduct!.id ||
                        sp.id == it.productsId))) {
              totalSubproductsInCart += it.productsQTY;
            }
          }
        }

        return Container(
          padding: EdgeInsets.all(12.w),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(22.r),
            border: Border.all(
              color: const Color(0xFFE2E8F0).withValues(alpha: 0.9),
            ),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFF0B1E2E).withValues(alpha: 0.04),
                blurRadius: 10,
                offset: const Offset(0, 3),
              ),
            ],
          ),
          child: Row(
            children: [
              // 1. Product Image on the Right (in RTL)
              ClipRRect(
                borderRadius: BorderRadius.circular(16.r),
                child: AppNetworkImage(
                  widget.product.imagePath,
                  height: 74.h,
                  width: 74.w,
                  fit: BoxFit.cover,
                ),
              ),
              SizedBox(width: 12.w),

              // 2. Product Name & Pricing (next to the image, RTL start)
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      getLng(
                        en: widget.product.name,
                        changeLang: widget.product.nameBn.toString(),
                      ),
                      style: TextStyle(
                        fontSize: 14.sp,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xFF0F172A),
                        fontFamily: 'Cairo',
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      textAlign: TextAlign.start,
                    ),
                    SizedBox(height: 4.h),
                    ProductPriceBlock(
                      currency: widget.currency,
                      currentPrice:
                          (widget.product.currentPrice ?? 0).toDouble(),
                      oldPrice: widget.product.oldPrice?.toDouble(),
                    ),
                  ],
                ),
              ),
              SizedBox(width: 12.w),

              // 3. Add / Options / Counter Button (moved to end -> Left side in RTL)
              if (hasSubproducts) ...[
                if (totalSubproductsInCart == 0)
                  _ProductAddButton(
                    label: 'إضافة',
                    icon: Icons.shopping_cart_outlined,
                    onTap: () => showModalBottomSheet(
                      backgroundColor: Colors.transparent,
                      context: context,
                      isScrollControlled: true,
                      builder: (_) => _VendorSubProductSheet(
                        product: widget.product,
                        vendor: widget.vendor,
                        currency: widget.currency,
                      ),
                    ),
                  )
                else
                  _ProductOptionsBadge(
                    totalQty: totalSubproductsInCart,
                    onTap: () => showModalBottomSheet(
                      backgroundColor: Colors.transparent,
                      context: context,
                      isScrollControlled: true,
                      builder: (_) => _VendorSubProductSheet(
                        product: widget.product,
                        vendor: widget.vendor,
                        currency: widget.currency,
                      ),
                    ),
                  ),
              ] else ...[
                if (exact == null)
                  _ProductAddButton(
                    label: 'إضافة',
                    icon: Icons.shopping_cart_outlined,
                    onTap: () async {
                      final n = CarItemHiveModel(
                        productsId: widget.product.id!,
                        productsName: widget.product.name!,
                        productsImage: widget.product.imagePath ?? '',
                        productsQTY: 1,
                        unitPrice: widget.product.currentPrice!,
                        serviceName: widget.product.service?.name ?? '',
                        vendorId: vendorId,
                        vendorName: widget.vendor.displayName,
                      );
                      final allowed = await ensureSingleVendorOrConfirmClear(
                        context: context,
                        ref: ref,
                        vendorId: vendorId,
                      );
                      if (!allowed) return;
                      CartHelper.addItem(vendorId, n);
                      ref.refreshVendorCarts();
                    },
                  )
                else
                  _ProductQtyCounter(
                    qty: exact.productsQTY,
                    onInc: () {
                      CartHelper.increment(
                        vendorId: vendorId,
                        productId: exact.productsId,
                        subProductId: exact.subProduct?.id,
                      );
                      ref.refreshVendorCarts();
                    },
                    onDec: () {
                      CartHelper.decrement(
                        vendorId: vendorId,
                        productId: exact.productsId,
                        subProductId: exact.subProduct?.id,
                      );
                      ref.refreshVendorCarts();
                    },
                  ),
              ],
            ],
          ),
        );
      },
    );
  }
}

/// Add to Cart Pill Button matching screenshot
class _ProductAddButton extends StatelessWidget {
  const _ProductAddButton({
    required this.onTap,
    this.label,
    this.icon,
  });

  final VoidCallback onTap;
  final String? label;
  final IconData? icon;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 38.h,
      width: 98.w,
      child: DecoratedBox(
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [Color(0xFF028090), Color(0xFF00A896)],
            begin: Alignment.topRight,
            end: Alignment.bottomLeft,
          ),
          borderRadius: BorderRadius.circular(20.r),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF00A896).withValues(alpha: 0.3),
              blurRadius: 6,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: ElevatedButton(
          onPressed: onTap,
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.transparent,
            shadowColor: Colors.transparent,
            padding: EdgeInsets.zero,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(20.r),
            ),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                icon ?? Icons.shopping_cart_outlined,
                size: 15.sp,
                color: Colors.white,
              ),
              SizedBox(width: 4.w),
              Text(
                label ?? 'إضافة',
                style: TextStyle(
                  fontSize: 12.sp,
                  fontWeight: FontWeight.w900,
                  color: Colors.white,
                  fontFamily: 'Cairo',
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

/// Quantity Selector for items already in cart
class _ProductQtyCounter extends StatelessWidget {
  const _ProductQtyCounter({
    required this.qty,
    required this.onInc,
    required this.onDec,
  });

  final int qty;
  final VoidCallback onInc;
  final VoidCallback onDec;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 38.h,
      width: 98.w,
      decoration: BoxDecoration(
        color: const Color(0xFFEFFAF8),
        borderRadius: BorderRadius.circular(20.r),
        border: Border.all(color: const Color(0xFF00A896), width: 1.2),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          InkWell(
            onTap: onDec,
            borderRadius: BorderRadius.circular(16.r),
            child: SizedBox(
              width: 30.w,
              height: double.infinity,
              child: Center(
                child: Icon(
                  qty <= 1 ? Icons.delete_outline_rounded : Icons.remove_rounded,
                  size: 16.sp,
                  color: qty <= 1
                      ? const Color(0xFFE63946)
                      : const Color(0xFF028090),
                ),
              ),
            ),
          ),
          Text(
            '$qty',
            style: TextStyle(
              fontSize: 13.sp,
              fontWeight: FontWeight.w900,
              color: const Color(0xFF028090),
              fontFamily: 'Cairo',
            ),
          ),
          InkWell(
            onTap: onInc,
            borderRadius: BorderRadius.circular(16.r),
            child: SizedBox(
              width: 30.w,
              height: double.infinity,
              child: Center(
                child: Icon(
                  Icons.add_rounded,
                  size: 16.sp,
                  color: const Color(0xFF028090),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// Options badge for products with multiple subproducts already added
class _ProductOptionsBadge extends StatelessWidget {
  const _ProductOptionsBadge({
    required this.totalQty,
    required this.onTap,
  });

  final int totalQty;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20.r),
      child: Container(
        height: 38.h,
        width: 98.w,
        decoration: BoxDecoration(
          color: const Color(0xFFEFFAF8),
          borderRadius: BorderRadius.circular(20.r),
          border: Border.all(color: const Color(0xFF00A896), width: 1.2),
        ),
        padding: EdgeInsets.symmetric(horizontal: 4.w),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              '$totalQty بالسلة',
              style: TextStyle(
                fontSize: 11.sp,
                fontWeight: FontWeight.w900,
                color: const Color(0xFF028090),
                fontFamily: 'Cairo',
              ),
            ),
            SizedBox(width: 2.w),
            Icon(
              Icons.edit_note_rounded,
              size: 16.sp,
              color: const Color(0xFF028090),
            ),
          ],
        ),
      ),
    );
  }
}

/// Bottom Sheet for selecting subproducts / variants
class _VendorSubProductSheet extends ConsumerStatefulWidget {
  const _VendorSubProductSheet({
    required this.product,
    required this.vendor,
    required this.currency,
  });

  final Product product;
  final Vendor vendor;
  final String currency;

  @override
  ConsumerState<_VendorSubProductSheet> createState() =>
      _VendorSubProductSheetState();
}

class _VendorSubProductSheetState
    extends ConsumerState<_VendorSubProductSheet> {
  @override
  Widget build(BuildContext context) {
    final vendorId = widget.vendor.id.toString();

    return ValueListenableBuilder(
      valueListenable: Hive.box(AppHSC.cartBox).listenable(),
      builder: (context, Box cartBox, _) {
        return Container(
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          padding: EdgeInsets.fromLTRB(
            16.w,
            12.h,
            16.w,
            16.h + MediaQuery.of(context).viewPadding.bottom,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Center(
                child: Container(
                  width: 40.w,
                  height: 4.h,
                  decoration: BoxDecoration(
                    color: const Color(0xFFE2E8F0),
                    borderRadius: BorderRadius.circular(2.r),
                  ),
                ),
              ),
              SizedBox(height: 14.h),
              Text(
                S.of(context).slctvarient,
                style: TextStyle(
                  fontSize: 11.sp,
                  color: const Color(0xFF64748B),
                  fontFamily: 'Cairo',
                ),
              ),
              Text(
                widget.product.name ?? '',
                style: TextStyle(
                  fontSize: 16.sp,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF0F172A),
                  fontFamily: 'Cairo',
                ),
              ),
              SizedBox(height: 12.h),
              SizedBox(
                height: 280.h,
                child: ListView.builder(
                  itemCount: widget.product.sbproducts!.length,
                  itemBuilder: (context, i) {
                    final sp = widget.product.sbproducts![i];
                    final cartItem = CartHelper.findCartItem(
                      vendorId: vendorId,
                      productId: sp.id ?? 0,
                      subProductId: sp.id,
                    );
                    final alt = CartHelper.getCartForVendor(vendorId)
                        .where((e) =>
                            e.subProduct?.id == sp.id ||
                            e.productsId == sp.id)
                        .firstOrNull;
                    final eff = cartItem ?? alt;
                    final inCart = eff != null;
                    return Container(
                      margin: EdgeInsets.only(bottom: 8.h),
                      padding: EdgeInsets.symmetric(
                          horizontal: 14.w, vertical: 12.h),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16.r),
                        border: Border.all(color: const Color(0xFFE2E8F0)),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  sp.name ?? 'Default',
                                  style: TextStyle(
                                    fontSize: 13.sp,
                                    fontWeight: FontWeight.w800,
                                    color: const Color(0xFF0F172A),
                                    fontFamily: 'Cairo',
                                  ),
                                ),
                                Text(
                                  "${widget.currency} ${sp.price}",
                                  style: TextStyle(
                                    fontSize: 12.sp,
                                    fontWeight: FontWeight.w800,
                                    color: const Color(0xFF028090),
                                    fontFamily: 'Cairo',
                                  ),
                                ),
                              ],
                            ),
                          ),
                          SizedBox(width: 8.w),
                          inCart
                              ? _ProductQtyCounter(
                                  qty: eff.productsQTY,
                                  onDec: () {
                                    CartHelper.decrement(
                                      vendorId: vendorId,
                                      productId: eff.productsId,
                                      subProductId: eff.subProduct?.id ??
                                          eff.subproductsId,
                                    );
                                    ref.refreshVendorCarts();
                                  },
                                  onInc: () {
                                    CartHelper.increment(
                                      vendorId: vendorId,
                                      productId: eff.productsId,
                                      subProductId: eff.subProduct?.id ??
                                          eff.subproductsId,
                                    );
                                    ref.refreshVendorCarts();
                                  },
                                )
                              : _ProductAddButton(
                                  label: 'إضافة',
                                  onTap: () async {
                                    final n = CarItemHiveModel(
                                      productsId: sp.id ?? 0,
                                      subproductsId: sp.id,
                                      productsName:
                                          sp.name ?? widget.product.name ?? '',
                                      productsImage:
                                          widget.product.imagePath ?? '',
                                      productsQTY: 1,
                                      unitPrice: (sp.price ?? 0).toDouble(),
                                      serviceName:
                                          widget.product.service?.name ?? '',
                                      subProduct: sp,
                                      vendorId: vendorId,
                                      vendorName: widget.vendor.displayName,
                                    );
                                    final allowed =
                                        await ensureSingleVendorOrConfirmClear(
                                      context: context,
                                      ref: ref,
                                      vendorId: vendorId,
                                    );
                                    if (!allowed) return;
                                    CartHelper.addItem(vendorId, n);
                                    ref.refreshVendorCarts();
                                  },
                                ),
                        ],
                      ),
                    );
                  },
                ),
              ),
              SizedBox(height: 12.h),
              ValueListenableBuilder(
                valueListenable: Hive.box(AppHSC.cartBox).listenable(),
                builder: (context, Box b, _) => Text(
                  "${widget.currency}${CartHelper.calculateTotalForVendor(vendorId).toStringAsFixed(2)}",
                  style: TextStyle(
                    fontSize: 16.sp,
                    fontWeight: FontWeight.w900,
                    color: const Color(0xFF028090),
                    fontFamily: 'Cairo',
                  ),
                ),
              ),
              SizedBox(height: 12.h),
              SizedBox(
                width: double.infinity,
                height: 48.h,
                child: DecoratedBox(
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFF028090), Color(0xFF00A896)],
                    ),
                    borderRadius: BorderRadius.circular(16.r),
                  ),
                  child: ElevatedButton(
                    onPressed: () => context.nav.pop(),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.transparent,
                      shadowColor: Colors.transparent,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16.r),
                      ),
                    ),
                    child: Text(
                      S.of(context).cnfirm,
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w900,
                        fontFamily: 'Cairo',
                        fontSize: 14.sp,
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}

extension _FirstOrNull<E> on Iterable<E> {
  E? get firstOrNull => isEmpty ? null : first;
}

/// Bottom Sheet for viewing all services / categories in the store
class _AllServicesSheet extends StatelessWidget {
  const _AllServicesSheet({
    required this.services,
    required this.selectedServiceId,
    required this.onSelectService,
    required this.onSelectAll,
  });

  final List<Service> services;
  final String? selectedServiceId;
  final ValueChanged<Service> onSelectService;
  final VoidCallback onSelectAll;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(28)),
      ),
      padding: EdgeInsets.fromLTRB(
        18.w,
        14.h,
        18.w,
        20.h + MediaQuery.of(context).viewPadding.bottom,
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Drag handle
          Center(
            child: Container(
              width: 44.w,
              height: 4.h,
              decoration: BoxDecoration(
                color: const Color(0xFFE2E8F0),
                borderRadius: BorderRadius.circular(2.r),
              ),
            ),
          ),
          SizedBox(height: 16.h),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'أقسام وخدمات المتجر',
                style: TextStyle(
                  fontSize: 16.sp,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF0F172A),
                  fontFamily: 'Cairo',
                ),
              ),
              InkWell(
                onTap: () => Navigator.pop(context),
                borderRadius: BorderRadius.circular(20.r),
                child: Padding(
                  padding: EdgeInsets.all(4.w),
                  child: Icon(
                    Icons.close_rounded,
                    size: 20.sp,
                    color: const Color(0xFF64748B),
                  ),
                ),
              ),
            ],
          ),
          SizedBox(height: 16.h),
          // View all products option button
          InkWell(
            borderRadius: BorderRadius.circular(16.r),
            onTap: onSelectAll,
            child: Container(
              padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 12.h),
              decoration: BoxDecoration(
                color: selectedServiceId == 'all'
                    ? const Color(0xFF028090)
                    : const Color(0xFFEFFAF8),
                borderRadius: BorderRadius.circular(16.r),
                border: Border.all(
                  color: const Color(0xFF00A896).withValues(alpha: 0.3),
                ),
              ),
              child: Row(
                children: [
                  Container(
                    width: 38.w,
                    height: 38.h,
                    decoration: BoxDecoration(
                      color: selectedServiceId == 'all'
                          ? Colors.white.withValues(alpha: 0.2)
                          : Colors.white,
                      borderRadius: BorderRadius.circular(12.r),
                    ),
                    child: Icon(
                      Icons.grid_view_rounded,
                      size: 20.sp,
                      color: selectedServiceId == 'all'
                          ? Colors.white
                          : const Color(0xFF028090),
                    ),
                  ),
                  SizedBox(width: 12.w),
                  Expanded(
                    child: Text(
                      'عرض جميع المنتجات والخدمات (الكل)',
                      style: TextStyle(
                        fontSize: 13.sp,
                        fontWeight: FontWeight.w800,
                        color: selectedServiceId == 'all'
                            ? Colors.white
                            : const Color(0xFF028090),
                        fontFamily: 'Cairo',
                      ),
                    ),
                  ),
                  Icon(
                    Icons.arrow_forward_ios_rounded,
                    size: 14.sp,
                    color: selectedServiceId == 'all'
                        ? Colors.white
                        : const Color(0xFF028090),
                  ),
                ],
              ),
            ),
          ),
          SizedBox(height: 14.h),
          ConstrainedBox(
            constraints: BoxConstraints(maxHeight: 340.h),
            child: GridView.builder(
              shrinkWrap: true,
              itemCount: services.length,
              gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 3,
                crossAxisSpacing: 10.w,
                mainAxisSpacing: 10.h,
                childAspectRatio: 0.88,
              ),
              itemBuilder: (context, idx) {
                final svc = services[idx];
                final isSel = selectedServiceId == svc.id.toString();
                final name =
                    getLng(en: svc.name, changeLang: svc.nameBn?.toString());
                return InkWell(
                  borderRadius: BorderRadius.circular(16.r),
                  onTap: () => onSelectService(svc),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    padding: EdgeInsets.all(8.w),
                    decoration: BoxDecoration(
                      color: isSel
                          ? const Color(0xFF028090)
                          : const Color(0xFFF8FAFC),
                      borderRadius: BorderRadius.circular(16.r),
                      border: Border.all(
                        color: isSel
                            ? const Color(0xFF028090)
                            : const Color(0xFFE2E8F0),
                      ),
                      boxShadow: isSel
                          ? [
                              BoxShadow(
                                color: const Color(0xFF028090)
                                    .withValues(alpha: 0.25),
                                blurRadius: 8,
                                offset: const Offset(0, 3),
                              )
                            ]
                          : null,
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        ClipRRect(
                          borderRadius: BorderRadius.circular(10.r),
                          child: AppNetworkImage(
                            svc.imagePath,
                            height: 38.h,
                            width: 38.w,
                            fit: BoxFit.contain,
                          ),
                        ),
                        SizedBox(height: 8.h),
                        Text(
                          name,
                          style: TextStyle(
                            fontSize: 11.sp,
                            fontWeight: FontWeight.w800,
                            color: isSel
                                ? Colors.white
                                : const Color(0xFF1E293B),
                            fontFamily: 'Cairo',
                          ),
                          maxLines: 2,
                          textAlign: TextAlign.center,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
