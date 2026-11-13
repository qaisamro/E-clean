import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/models/all_service_model/service.dart';
import 'package:laundry_customer/providers/city_providers.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/screens/store/vendor_store_screen.dart';
import 'package:laundry_customer/widgets/store_card.dart';

class ServiceVendorsScreen extends ConsumerWidget {
  const ServiceVendorsScreen({super.key, required this.service});

  final Service service;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final city = ref.watch(citySelectionProvider).city;
    final serviceId = service.id?.toString() ?? '';
    final vendors = ref.watch(serviceVendorsProvider(serviceId));

    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: const Color(0xFFF8FAFC),
        appBar: AppBar(
          title: Text(service.nameBn?.toString().trim().isNotEmpty == true
              ? service.nameBn.toString()
              : (service.name ?? 'المتاجر')),
        ),
        body: vendors.when(
          loading: () => GridView.builder(
            padding: EdgeInsets.all(16.w),
            gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              crossAxisSpacing: 12.w,
              mainAxisSpacing: 12.h,
              childAspectRatio: .82,
            ),
            itemCount: 6,
            itemBuilder: (_, __) => const StoreGridCardShimmer(),
          ),
          error: (_, __) => Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text('تعذر تحميل المتاجر. تحقق من الاتصال.'),
                TextButton(
                  onPressed: () =>
                      ref.invalidate(serviceVendorsProvider(serviceId)),
                  child: const Text('إعادة المحاولة'),
                ),
              ],
            ),
          ),
          data: (response) {
            final list = response.data?.vendors ?? [];
            if (list.isEmpty) {
              return Center(
                child: Padding(
                  padding: EdgeInsets.all(24.w),
                  child: Text(
                    city == null
                        ? 'لا توجد متاجر تقدم هذه الخدمة حاليًا.'
                        : 'لا توجد متاجر تقدم هذه الخدمة في ${city.name} حاليًا.',
                    textAlign: TextAlign.center,
                  ),
                ),
              );
            }

            return RefreshIndicator(
              onRefresh: () async {
                ref.invalidate(serviceVendorsProvider(serviceId));
                await ref.read(serviceVendorsProvider(serviceId).future);
              },
              child: GridView.builder(
                padding: EdgeInsets.all(16.w),
                gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  crossAxisSpacing: 12.w,
                  mainAxisSpacing: 12.h,
                  childAspectRatio: .82,
                ),
                itemCount: list.length,
                itemBuilder: (context, index) => StoreGridCard(
                  vendor: list[index],
                  onTap: () => Navigator.of(context).push(MaterialPageRoute(
                    builder: (_) => VendorStoreScreen(vendor: list[index]),
                  )),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}
