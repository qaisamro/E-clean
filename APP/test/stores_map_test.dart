import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/widgets/busy_loader.dart';
import 'package:laundry_customer/providers/city_providers.dart';
import 'dart:async';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:laundry_customer/models/vendor_model/vendor.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/repos/vendor_repo.dart';
import 'package:laundry_customer/screens/store/stores_map_screen.dart';
import 'package:laundry_customer/screens/store/vendor_store_screen.dart';
import 'widget_test.dart' show LocalTiles;

class PagesAdapter implements HttpClientAdapter {
  final pages = <int>[];
  @override
  Future<ResponseBody> fetch(RequestOptions options,
      Stream<List<int>>? requestStream, Future<dynamic>? cancelFuture) async {
    pages.add(options.queryParameters['page'] as int);
    final page = pages.last;
    return ResponseBody.fromString(
        '{"data":{"vendors":{"data":[{"id":$page,"name":"Store $page","latitude":31.5,"longitude":35.1}],"next_page_url":${page == 1 ? '"https://unused.test/page2"' : 'null'}}}}',
        200,
        headers: {
          'content-type': ['application/json']
        });
  }

  @override
  void close({bool force = false}) {}
}

class RouteCapture extends NavigatorObserver {
  Route<dynamic>? last;
  @override
  void didPush(Route<dynamic> route, Route<dynamic>? previousRoute) {
    last = route;
  }
}

void main() {
  testWidgets('loading and network failure have explicit states',
      (tester) async {
    final pending = Completer<List<Vendor>>();
    await tester.pumpWidget(ProviderScope(
      overrides: [
        mapVendorsProvider.overrideWith((ref) => pending.future),
        currentCityIdProvider.overrideWithValue(null),
        selectedVendorAreaProvider.overrideWithValue('الكل'),
      ],
      child: ScreenUtilInit(
          builder: (_, __) => const MaterialApp(home: StoresMapScreen())),
    ));
    await tester.pump();
    expect(find.byType(AppLoadingIndicator), findsOneWidget);
    pending.completeError(Exception('offline'));
    await tester.pumpAndSettle();
    expect(find.text('تعذر تحميل المتاجر. تحقق من الإنترنت.'), findsOneWidget);
    expect(find.text('إعادة المحاولة'), findsOneWidget);
  });
  final store = Vendor(
      id: 21,
      name: 'متجر الخليل',
      address: 'الخليل',
      latitude: 31.5,
      longitude: 35.1);
  test(
      'coordinates support numbers and strings; missing and invalid are excluded',
      () {
    expect(
        Vendor.fromMap(
                {'id': 1, 'name': 'A', 'latitude': '31.5', 'longitude': 35.1})
            .hasMapLocation,
        true);
    for (final latitude in [null, 'bad', 'NaN', 'Infinity', '91']) {
      expect(
          Vendor.fromMap(
                  {'id': 1, 'name': 'A', 'latitude': latitude, 'longitude': 35})
              .hasMapLocation,
          false);
    }
    expect(store.copyWith(longitude: 181).hasMapLocation, false);
    expect(Vendor.fromMap(store.toMap()).latitude, store.latitude);
    expect(vendorMatchesArea(store, 'الخليل'), true);
    expect(vendorMatchesArea(store, 'نابلس'), false);
  });
  test('map retrieves subsequent pages through the existing endpoint',
      () async {
    final adapter = PagesAdapter();
    final dio = Dio()..httpClientAdapter = adapter;
    final result = await VendorRepo(client: dio).getMapVendors();
    expect(result.map((v) => v.id), [1, 2]);
    expect(adapter.pages, [1, 2]);
    dio.close();
  });
  Widget app(List<Vendor> vendors,
          {NavigatorObserver? observer, String? cityId}) =>
      ProviderScope(
          overrides: [
            mapVendorsProvider.overrideWith((ref) async => vendors),
            selectedVendorAreaProvider.overrideWith((ref) => 'الخليل'),
            currentCityIdProvider.overrideWithValue(cityId)
          ],
          child: ScreenUtilInit(
              builder: (_, __) => MaterialApp(
                  navigatorObservers: [if (observer != null) observer],
                  home: StoresMapScreen(tileProvider: LocalTiles()))));

  testWidgets(
      'map respects area, excludes invalid coordinates, and shows all without changing area',
      (tester) async {
    await tester.pumpWidget(app([
      store,
      Vendor(
          id: 22,
          name: 'متجر نابلس',
          address: 'نابلس',
          latitude: 32.2,
          longitude: 35.2),
      Vendor(id: 23, name: 'بدون إحداثيات', address: 'الخليل')
    ]));
    await tester.pumpAndSettle();
    expect(find.byKey(const ValueKey('vendor-pin-21')), findsOneWidget);
    expect(find.byKey(const ValueKey('vendor-pin-22')), findsNothing);
    expect(find.byKey(const ValueKey('vendor-pin-23')), findsNothing);
    await tester.tap(find.byKey(const ValueKey('vendor-pin-21')));
    await tester.pumpAndSettle();
    expect(find.text('اضغط لفتح المتجر'), findsOneWidget);
    await tester.tap(find.text('كل المتاجر'));
    await tester.pumpAndSettle();
    expect(find.byKey(const ValueKey('vendor-pin-22')), findsOneWidget);
    final container =
        ProviderScope.containerOf(tester.element(find.byType(StoresMapScreen)));
    expect(container.read(selectedVendorAreaProvider), 'الخليل');
  });
  testWidgets('name tap pushes the existing store screen with the exact vendor',
      (tester) async {
    // Inspect the real route builder without mounting unrelated Hive/cart services.
    final observer = RouteCapture();
    await tester.pumpWidget(app([store], observer: observer));
    await tester.pumpAndSettle();
    await tester.tap(find.byKey(const ValueKey('vendor-name-21')));
    final route = observer.last! as MaterialPageRoute<void>;
    final destination =
        route.builder(tester.element(find.byType(StoresMapScreen)))
            as VendorStoreScreen;
    expect(identical(destination.vendor, store), true);
    expect(destination.vendor.id, 21);
    // Remove the navigator before the new page builds its independent services.
    await tester.pumpWidget(const SizedBox());
  });
  testWidgets('missing coordinates show a useful empty state', (tester) async {
    await tester.pumpWidget(app([Vendor(id: 1, name: 'A', address: 'الخليل')]));
    await tester.pumpAndSettle();
    expect(
        find.text('لا توجد مواقع متاجر صالحة للعرض حاليًا.'), findsOneWidget);
  });
  testWidgets('map uses city identity even when address or spelling differs',
      (tester) async {
    await tester.pumpWidget(app([
      Vendor(
          id: 31,
          name: 'Correct city',
          cityId: 'a',
          city: 'الْخليل',
          cityMetadata: true,
          address: 'نابلس',
          latitude: 31.5,
          longitude: 35.1),
      Vendor(
          id: 32,
          name: 'Other city',
          cityId: 'b',
          city: 'نابلس',
          cityMetadata: true,
          address: 'الخليل',
          latitude: 32.2,
          longitude: 35.2),
    ], cityId: 'a'));
    await tester.pumpAndSettle();
    expect(find.byKey(const ValueKey('vendor-pin-31')), findsOneWidget);
    expect(find.byKey(const ValueKey('vendor-pin-32')), findsNothing);
  });
}
