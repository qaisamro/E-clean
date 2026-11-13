import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/providers/city_providers.dart';
import 'package:laundry_customer/providers/guest_providers.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/screens/store/service_vendors_screen.dart';
import 'package:laundry_customer/screens/store/vendor_store_screen.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';
import 'package:laundry_customer/widgets/home_city_picker.dart';

/// Full-width smart search bar for the home screen.
///
/// Results are scoped to the currently selected city/area (same rule the
/// stores grid uses) and are predicted live, starting from the first typed
/// letter, across both stores and services. Tapping a suggestion opens the
/// store page or the service page directly.
class HomeSearchBar extends ConsumerStatefulWidget {
  const HomeSearchBar({super.key});

  @override
  ConsumerState<HomeSearchBar> createState() => _HomeSearchBarState();
}

class _HomeSearchBarState extends ConsumerState<HomeSearchBar> {
  static const int _maxSuggestionsPerGroup = 4;

  final TextEditingController _controller = TextEditingController();
  String _query = '';

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  String _norm(Object? value) => (value ?? '').toString().trim().toLowerCase();

  void _clear() {
    _controller.clear();
    setState(() => _query = '');
  }

  Widget _groupLabel(String label) => Padding(
        padding: EdgeInsets.fromLTRB(14.w, 10.h, 14.w, 4.h),
        child: Text(
          label,
          style: TextStyle(
            fontSize: 10.5.sp,
            fontWeight: FontWeight.w800,
            color: const Color(0xFF156172),
            fontFamily: 'Cairo',
            letterSpacing: 0.2,
          ),
        ),
      );

  Widget _suggestionRow({
    required Widget avatar,
    required String title,
    String? subtitle,
    required String tag,
    required VoidCallback onTap,
  }) {
    return InkWell(
      borderRadius: BorderRadius.circular(12.r),
      onTap: onTap,
      child: Padding(
        padding: EdgeInsets.symmetric(horizontal: 12.w, vertical: 7.h),
        child: Row(
          children: [
            Container(
              width: 40.w,
              height: 40.h,
              decoration: BoxDecoration(
                color: const Color(0xFFEAF6F8),
                borderRadius: BorderRadius.circular(12.r),
              ),
              clipBehavior: Clip.antiAlias,
              child: avatar,
            ),
            SizedBox(width: 10.w),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    title,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      fontSize: 12.sp,
                      fontWeight: FontWeight.w800,
                      color: const Color(0xFF0F2E35),
                      fontFamily: 'Cairo',
                    ),
                  ),
                  if (subtitle != null && subtitle.trim().isNotEmpty) ...[
                    SizedBox(height: 2.h),
                    Text(
                      subtitle.trim(),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                        fontSize: 10.sp,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF64748B),
                        fontFamily: 'Cairo',
                      ),
                    ),
                  ],
                ],
              ),
            ),
            SizedBox(width: 8.w),
            Container(
              padding: EdgeInsets.symmetric(horizontal: 8.w, vertical: 3.h),
              decoration: BoxDecoration(
                color: const Color(0xFFEAF6F8),
                borderRadius: BorderRadius.circular(10.r),
                border: Border.all(
                  color: const Color(0xFF1b758a).withOpacity(0.25),
                ),
              ),
              child: Text(
                tag,
                style: TextStyle(
                  fontSize: 9.sp,
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFF156172),
                  fontFamily: 'Cairo',
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final query = _query.trim();
    final q = query.toLowerCase();
    final bool showPanel = q.isNotEmpty;
    final bool areaSelected = ref.watch(citySelectionProvider).city != null;

    // Scoped to the selected area, same rule used by the stores grid.
    final selectedArea = ref.watch(selectedVendorAreaProvider);
    final currentCityId = ref.watch(currentCityIdProvider);

    final vendorList = ref.watch(vendorsProvider).maybeWhen<List<Vendor>>(
          loaded: (d) => d.data?.vendors ?? const <Vendor>[],
          orElse: () => const <Vendor>[],
        );
    final serviceList = ref.watch(allServicesProvider).maybeWhen<List<Service>>(
          loaded: (d) => d.data?.services ?? const <Service>[],
          orElse: () => const <Service>[],
        );
    final bool vendorsLoading =
        ref.watch(vendorsProvider).maybeWhen(loading: () => true, orElse: () => false);
    final bool servicesLoading =
        ref.watch(allServicesProvider).maybeWhen(loading: () => true, orElse: () => false);

    final areaVendors = selectedArea == 'الكل' || currentCityId == null
        ? vendorList
        : vendorList.where((v) => v.cityId == currentCityId).toList();

    final matchingVendors = q.isEmpty
        ? const <Vendor>[]
        : areaVendors
            .where((v) =>
                _norm(v.name).contains(q) ||
                _norm(v.address).contains(q) ||
                _norm(v.description).contains(q))
            .take(_maxSuggestionsPerGroup)
            .toList();

    final matchingServices = q.isEmpty
        ? const <Service>[]
        : serviceList
            .where((s) =>
                _norm(s.name).contains(q) || _norm(s.description).contains(q))
            .take(_maxSuggestionsPerGroup)
            .toList();

    final bool hasAnySuggestion =
        matchingVendors.isNotEmpty || matchingServices.isNotEmpty;

    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18.r),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0C2238).withOpacity(0.07),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          TextField(
            controller: _controller,
            textInputAction: TextInputAction.search,
            onChanged: (v) => setState(() => _query = v),
            onSubmitted: (_) => FocusScope.of(context).unfocus(),
            cursorColor: const Color(0xFF156172),
            style: TextStyle(
              fontSize: 13.sp,
              color: const Color(0xFF0B1E2E),
              fontWeight: FontWeight.w600,
              fontFamily: 'Cairo',
            ),
            decoration: InputDecoration(
              hintText: 'ابحث عن متجر أو خدمة في منطقتك…',
              hintStyle: TextStyle(
                color: const Color(0xFF94A3B8),
                fontSize: 12.sp,
                fontWeight: FontWeight.w500,
                fontFamily: 'Cairo',
              ),
              prefixIcon: Icon(Icons.search_rounded,
                  size: 22.sp, color: const Color(0xFF156172)),
              suffixIcon: query.isEmpty
                  ? Icon(Icons.storefront_outlined,
                      size: 19.sp, color: const Color(0xFFCBD5E1))
                  : IconButton(
                      tooltip: 'مسح البحث',
                      icon: Icon(Icons.close_rounded,
                          size: 20.sp, color: const Color(0xFF94A3B8)),
                      onPressed: _clear,
                    ),
              border: InputBorder.none,
              contentPadding:
                  EdgeInsets.symmetric(horizontal: 6.w, vertical: 14.h),
            ),
          ),
          if (showPanel) ...[
            const Divider(height: 1, thickness: 1, color: Color(0xFFEDF1F7)),
            if (!areaSelected)
              Padding(
                padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 12.h),
                child: Row(
                  children: [
                    Icon(Icons.location_on_outlined,
                        size: 19.sp, color: const Color(0xFF156172)),
                    SizedBox(width: 8.w),
                    Expanded(
                      child: Text(
                        'حدّد مدينتك أولًا لتفعيل البحث في منطقتك',
                        style: TextStyle(
                          fontSize: 11.5.sp,
                          fontWeight: FontWeight.w600,
                          color: const Color(0xFF64748B),
                          fontFamily: 'Cairo',
                        ),
                      ),
                    ),
                    TextButton(
                      onPressed: () => showHomeCityPicker(context),
                      style: TextButton.styleFrom(
                        backgroundColor: const Color(0xFFEAF6F8),
                        foregroundColor: const Color(0xFF156172),
                        padding:
                            EdgeInsets.symmetric(horizontal: 12.w, vertical: 6.h),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12.r),
                        ),
                      ),
                      child: Text(
                        'اختيار المدينة',
                        style: TextStyle(
                          fontSize: 11.sp,
                          fontWeight: FontWeight.w800,
                          fontFamily: 'Cairo',
                        ),
                      ),
                    ),
                  ],
                ),
              )
            else ...[
            if (matchingVendors.isNotEmpty) ...[
              _groupLabel('المتاجر في منطقتك'),
              ...matchingVendors.map((v) => _suggestionRow(
                    avatar: (v.logoPath ?? v.imagePath)?.isNotEmpty == true
                        ? AppNetworkImage(v.logoPath ?? v.imagePath, fit: BoxFit.cover)
                        : Icon(Icons.storefront_rounded,
                            size: 20.sp, color: const Color(0xFF156172)),
                    title: (v.name ?? 'متجر').trim(),
                    subtitle: v.address,
                    tag: 'متجر',
                    onTap: () {
                      FocusScope.of(context).unfocus();
                      Navigator.of(context).push(MaterialPageRoute(
                        builder: (_) => VendorStoreScreen(vendor: v),
                      ));
                    },
                  )),
            ],
            if (matchingServices.isNotEmpty) ...[
              _groupLabel('الخدمات'),
              ...matchingServices.map((s) => _suggestionRow(
                    avatar: s.imagePath?.isNotEmpty == true
                        ? AppNetworkImage(s.imagePath, fit: BoxFit.cover)
                        : Icon(Icons.dry_cleaning_rounded,
                            size: 20.sp, color: const Color(0xFF156172)),
                    title: (s.name ?? 'خدمة').trim(),
                    subtitle: s.description,
                    tag: 'خدمة',
                    onTap: () {
                      FocusScope.of(context).unfocus();
                      Navigator.of(context).push(MaterialPageRoute(
                        builder: (_) => ServiceVendorsScreen(service: s),
                      ));
                    },
                  )),
            ],
            if (!hasAnySuggestion)
              Padding(
                padding: EdgeInsets.symmetric(horizontal: 14.w, vertical: 12.h),
                child: Row(
                  children: [
                    if (vendorsLoading || servicesLoading)
                      const SizedBox(
                        width: 16,
                        height: 16,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Color(0xFF156172)),
                      )
                    else
                      Icon(Icons.search_off_rounded,
                          size: 18.sp, color: const Color(0xFF94A3B8)),
                    SizedBox(width: 8.w),
                    Expanded(
                      child: Text(
                        (vendorsLoading || servicesLoading)
                            ? 'جارٍ البحث في منطقتك…'
                            : 'لا توجد نتائج مطابقة في منطقتك',
                        style: TextStyle(
                          fontSize: 11.5.sp,
                          fontWeight: FontWeight.w600,
                          color: const Color(0xFF64748B),
                          fontFamily: 'Cairo',
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
            SizedBox(height: 8.h),
          ],
        ],
      ),
    );
  }
}
