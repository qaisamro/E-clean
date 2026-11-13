import 'dart:async';
import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:laundry_customer/providers/city_providers.dart';
import 'package:laundry_customer/providers/address_session_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/repos/city_repo.dart';
import 'package:laundry_customer/repos/vendor_repo.dart';
import 'package:laundry_customer/repos/guest_repo.dart';
import 'package:laundry_customer/services/location_service.dart';
import 'package:laundry_customer/widgets/home_city_picker.dart';

const approvedCities = [
  AppCity('a', 'دورا', ['الخليل']),
  AppCity('b', 'الخليل', ['الخليل'])
];

class Choices extends CityChoiceStore {
  Choices([this.saved]);
  Map? saved;
  List<AppCity> cached = [];
  @override
  List<AppCity> readCities() => cached;
  @override
  Future<void> cacheCities(List<AppCity> cities) async => cached = cities;
  @override
  Map? read() => saved;
  @override
  Future<void> write(String id, bool manual) async {
    saved = {'id': id, 'manual': manual};
  }
}

class Cities implements ICityRepo {
  String? resolved = 'a';
  int calls = 0;
  bool offline = false;
  @override
  Future<List<AppCity>> cities() async {
    if (offline) throw Exception('offline');
    return List.of(approvedCities);
  }

  @override
  Future<String?> resolve(PickedLocation point) async {
    calls++;
    return resolved;
  }
}

class GPS extends LocationService {
  int calls = 0;
  bool? requested;
  Object? failure;
  Completer<PickedLocation>? pending;
  @override
  Future<PickedLocation> currentCoordinates(
      {bool requestPermission = false}) async {
    calls++;
    requested = requestPermission;
    if (failure != null) throw failure!;
    return pending?.future ??
        const PickedLocation(latitude: 31.5, longitude: 35.1, accuracy: 10);
  }
}

Future<void> settle() => Future<void>.delayed(const Duration(milliseconds: 20));

class Requests implements HttpClientAdapter {
  final requests = <RequestOptions>[];
  @override
  Future<ResponseBody> fetch(RequestOptions options,
      Stream<List<int>>? requestStream, Future<dynamic>? cancelFuture) async {
    requests.add(options);
    final body = options.path == '/vendors'
        ? {
            'data': {
              'vendors': {
                'data': [
                  {'id': 1, 'name': 'A', 'city_id': 'a', 'city': 'دورا'},
                  {'id': 2, 'name': 'B', 'city_id': 'b', 'city': 'الخليل'}
                ],
                'next_page_url': null
              }
            }
          }
        : {
            'data': {
              'services': [],
              'products': [],
              'offers': [],
              'banners': [],
              'promotions': []
            }
          };
    return ResponseBody.fromString(jsonEncode(body), 200, headers: {
      'content-type': ['application/json']
    });
  }

  @override
  void close({bool force = false}) {}
}

void main() {
  test('city changes leave checkout address and calculated total intact', () async {
    final container = ProviderContainer(overrides: [
      cityRepoProvider.overrideWithValue(Cities()),
      cityChoiceStoreProvider.overrideWithValue(Choices({'id': 'a', 'manual': true})),
      locationServiceProvider.overrideWithValue(GPS()),
      addressSessionProvider.overrideWith((ref) => Stream.value('session-a')),
    ]);
    await container.read(addressSessionProvider.future);
    container.read(addressIDProvider.notifier).state = '7';
    container.read(calculateTotalProvider.notifier).state = 123;
    final controller = container.read(citySelectionProvider.notifier);
    await settle();
    await controller.select('b');
    await controller.useCurrentLocation();
    expect(container.read(addressIDProvider), '7');
    expect(container.read(calculateTotalProvider), 123);
    container.dispose();
  });
  test('offline cold start restores catalog and manual city before networking',
      () async {
    final store = Choices({'id': 'b', 'manual': true})..cached = approvedCities;
    final gps = GPS();
    final repo = Cities()..offline = true;
    final controller = CitySelectionController(repo, gps, store);
    expect(controller.state.city?.name, 'الخليل');
    await settle();
    expect(controller.state.cities.length, 2);
    expect(controller.state.city?.id, 'b');
    expect(controller.state.message, contains('دون اتصال'));
    await controller.select('a');
    controller.dispose();
    final restored = CitySelectionController(repo, gps, store);
    await settle();
    expect(restored.state.city?.id, 'a');
    expect(gps.calls, 0);
    expect(store.saved?['manual'], true);
    restored.dispose();
  });
  test(
      'refresh invalidates a removed city and never silently substitutes GPS for manual choice',
      () async {
    final store = Choices({'id': 'removed', 'manual': true})
      ..cached = [const AppCity('removed', 'قديمة', [])];
    final gps = GPS();
    final controller = CitySelectionController(Cities(), gps, store);
    await settle();
    expect(controller.state.city, isNull);
    expect(controller.state.message, contains('لم تعد متاحة'));
    expect(gps.calls, 0);
    controller.dispose();
  });
  test(
      'first launch uses permitted GPS without asking for permission and persists approved identity',
      () async {
    final gps = GPS();
    final store = Choices();
    final repo = Cities();
    final controller = CitySelectionController(repo, gps, store);
    await settle();
    expect(controller.state.city?.id, 'a');
    expect(gps.requested, false);
    expect(store.saved, {'id': 'a', 'manual': false});
    controller.dispose();
  });
  test('manual selection survives restart without GPS or resolver calls',
      () async {
    final gps = GPS();
    final repo = Cities();
    final store = Choices({'id': 'b', 'manual': true});
    final controller = CitySelectionController(repo, gps, store);
    await settle();
    expect(controller.state.city?.id, 'b');
    expect(gps.calls, 0);
    expect(repo.calls, 0);
    await controller.select('a');
    expect(store.saved, {'id': 'a', 'manual': true});
    controller.dispose();
    final restored = CitySelectionController(repo, gps, store);
    await settle();
    expect(restored.state.city?.id, 'a');
    expect(gps.calls, 0);
    restored.dispose();
  });
  test('a late automatic result cannot overwrite a manual city', () async {
    final gps = GPS()..pending = Completer();
    final store = Choices();
    final controller = CitySelectionController(Cities(), gps, store);
    await settle();
    await controller.select('b');
    gps.pending!
        .complete(const PickedLocation(latitude: 31.5, longitude: 35.1));
    await settle();
    expect(controller.state.city?.id, 'b');
    expect(store.saved?['manual'], true);
    controller.dispose();
  });
  test('permission refusal and unverified GPS allow manual choice', () async {
    final gps = GPS()..failure = const LocationFailure('الموقع غير مسموح');
    final repo = Cities();
    final store = Choices();
    final controller = CitySelectionController(repo, gps, store);
    await settle();
    expect(controller.state.city, isNull);
    expect(controller.state.message, contains('المدينة يدويًا'));
    await controller.select('b');
    await controller.useCurrentLocation();
    expect(gps.requested, true);
    expect(controller.state.city?.id, 'b');
    expect(store.saved?['manual'], true);
    gps.failure = null;
    repo.resolved = 'not-approved';
    await controller.useCurrentLocation();
    expect(controller.state.city?.id, 'b');
    repo.resolved = 'a';
    await controller.useCurrentLocation();
    expect(controller.state.city?.id, 'a');
    expect(store.saved?['manual'], false);
    controller.dispose();
  });
  test(
      'all home requests include city identity; explicit store pages keep their own scope',
      () async {
    final adapter = Requests();
    final dio = Dio()..httpClientAdapter = adapter;
    final vendor = VendorRepo(client: dio, cityId: 'a');
    final data = await vendor.getVendors();
    expect(data.data!.vendors!.map((v) => v.id), [1]);
    final guest = GuestRepo(client: dio, cityId: 'a');
    await guest.getServices();
    await guest.getProducts('', '');
    await guest.getOffers();
    await guest.getPromotions();
    await guest.getWideBanners();
    expect(adapter.requests.every((r) => r.queryParameters['city_id'] == 'a'),
        true);
    await vendor.getVendorServices('2');
    expect(adapter.requests.last.queryParameters.containsKey('city_id'), false);
  });
  testWidgets(
      'city picker shows current city, supports RTL and manual change after refused location',
      (tester) async {
    final store = Choices({'id': 'a', 'manual': true});
    await tester.pumpWidget(ProviderScope(overrides: [
      cityRepoProvider.overrideWithValue(Cities()),
      cityChoiceStoreProvider.overrideWithValue(store),
      locationServiceProvider.overrideWithValue(GPS()),
    ], child: const MaterialApp(home: Scaffold(body: HomeCityPicker()))));
    await tester.pumpAndSettle();
    expect(find.text('دورا'), findsOneWidget);
    await tester.tap(find.text('دورا'));
    await tester.pumpAndSettle();
    expect(find.text('استخدام موقعي الحالي'), findsOneWidget);
    await tester.tap(find.widgetWithText(ListTile, 'الخليل').last);
    await tester.pumpAndSettle();
    expect(store.saved?['id'], 'b');
    expect(find.text('الخليل'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });
}
