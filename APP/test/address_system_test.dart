import 'dart:async';
import 'dart:convert';
import 'dart:typed_data';

import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:geolocator/geolocator.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/models/addres_list_model/addres_list_model.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart';
import 'package:laundry_customer/providers/address_book_provider.dart';
import 'package:laundry_customer/providers/address_session_provider.dart';
import 'package:laundry_customer/providers/location_suggestion_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/repos/address_repo.dart';
import 'package:laundry_customer/screens/address/manage_address_screen.dart';
import 'package:laundry_customer/services/location_service.dart';

class FakeLocationDevice implements LocationDevice {
  bool gps = true;
  LocationPermission grant = LocationPermission.whileInUse;
  LocationPermission answer = LocationPermission.denied;
  int requests = 0;
  int readings = 0;
  double accuracy = 5;
  Object? error;
  Completer<Position>? pending;
  @override
  Future<bool> enabled() async => gps;
  @override
  Future<LocationPermission> permission() async => grant;
  @override
  Future<LocationPermission> request() async {
    requests++;
    return answer;
  }

  @override
  Future<Position> position() async {
    readings++;
    if (error != null) throw error!;
    if (pending != null) return pending!.future;
    return Position(
        latitude: 31.9,
        longitude: 35.2,
        timestamp: DateTime(2026),
        accuracy: accuracy,
        altitude: 0,
        altitudeAccuracy: 0,
        heading: 0,
        headingAccuracy: 0,
        speed: 0,
        speedAccuracy: 0);
  }
}

class MemoryAddresses implements IAddressRepo {
  List<Address> rows = [];
  int creates = 0;
  Object? failure;
  Completer<void>? hold;
  Map<String, dynamic>? lastUpdate;
  @override
  Future<AddresListModel> getAddresses() async {
    if (failure != null) throw failure!;
    return AddresListModel.fromMap({
      'data': {'addresses': rows.map((a) => a.toMap()).toList()}
    });
  }

  @override
  Future<Response> addAddress(Map<String, dynamic> fields) async {
    creates++;
    if (hold != null) await hold!.future;
    if (failure != null) throw failure!;
    if (rows.length >= 3)
      return Response(
          requestOptions: RequestOptions(), data: {'message': 'limit'});
    if (fields['is_default'] == true)
      rows = rows.map((a) => a.copyWith(isDefault: false)).toList();
    rows.add(Address.fromMap({...fields, 'id': rows.length + 1}));
    return Response(requestOptions: RequestOptions(), data: {
      'data': {'addresses': rows.map((a) => a.toMap()).toList()}
    });
  }

  @override
  Future<void> updateAddress(Map<String, dynamic> fields, String id) async {
    if (failure != null) throw failure!;
    lastUpdate = fields;
    rows = rows
        .map((a) => a.id.toString() == id
            ? Address.fromMap({...a.toMap(), ...fields})
            : a)
        .toList();
  }

  @override
  Future<void> deleteAddress(String id) async =>
      rows.removeWhere((a) => a.id.toString() == id);
  @override
  Future<void> setDefault(String id) async {
    rows =
        rows.map((a) => a.copyWith(isDefault: a.id.toString() == id)).toList();
  }
}

class CaptureAdapter implements HttpClientAdapter {
  RequestOptions? request;
  Map<String, dynamic> response = {
    'data': {'addresses': []}
  };
  int status = 200;
  @override
  Future<ResponseBody> fetch(RequestOptions options,
      Stream<Uint8List>? requestStream, Future<void>? cancelFuture) async {
    request = options;
    return ResponseBody.fromString(jsonEncode(response), status, headers: {
      Headers.contentTypeHeader: ['application/json']
    });
  }

  @override
  void close({bool force = false}) {}
}

void main() {
  group('Foreground GPS', () {
    late FakeLocationDevice device;
    late LocationService service;
    setUp(() {
      device = FakeLocationDevice();
      service = LocationService(device: device, describe: (_, __) async => []);
    });
    test('obtains valid GPS and never substitutes a region center', () async {
      final point = await service.current();
      expect(point.valid, true);
      expect(point.latitude, 31.9);
      expect(point.addressLine, isEmpty);
      expect(device.requests, 0);
    });
    test('automatic suggestion does not request denied permission', () async {
      device.grant = LocationPermission.denied;
      await expectLater(service.current(), throwsA(isA<LocationFailure>()));
      expect(device.requests, 0);
      expect(device.readings, 0);
    });
    test('explicit action requests permission once and handles rejection',
        () async {
      device.grant = LocationPermission.denied;
      await expectLater(service.current(requestPermission: true),
          throwsA(isA<LocationFailure>()));
      expect(device.requests, 1);
      expect(device.readings, 0);
    });
    test('permanently denied permission never re-prompts', () async {
      device.grant = LocationPermission.deniedForever;
      await expectLater(service.current(requestPermission: true),
          throwsA(isA<LocationFailure>()));
      expect(device.requests, 0);
    });
    test('disabled GPS fails without a substitute location', () async {
      device.gps = false;
      await expectLater(service.current(), throwsA(isA<LocationFailure>()));
      expect(device.readings, 0);
    });
    test('weak accuracy is explicitly marked approximate', () async {
      device.accuracy = 400;
      expect((await service.current()).approximate, true);
    });
    test('GPS timeout gives a readable failure and no unbounded retry',
        () async {
      device.error = TimeoutException('mock');
      await expectLater(service.current(), throwsA(isA<LocationFailure>()));
      expect(device.readings, 1);
    });
    test('invalid coordinates are rejected and not geocoded', () async {
      expect(validCoordinates(double.nan, 0), false);
      expect(validCoordinates(91, 0), false);
      await expectLater(
          service.describeLocation(0, 181), throwsA(isA<LocationFailure>()));
    });
  });

  group('Address persistence contract', () {
    test('default handles booleans and legacy responses without flag', () {
      expect(Address.fromMap({'is_default': true}).isDefault, true);
      expect(Address.fromMap({'is_default': '1'}).isDefault, true);
      expect(Address.fromMap({}).isDefault, false);
    });
    test(
        'multipart default uses existing POST endpoint with no text replacement',
        () async {
      final adapter = CaptureAdapter();
      final client = Dio(BaseOptions(baseUrl: 'https://example.test/api'))
        ..httpClientAdapter = adapter;
      await AddressRepo(client: client).setDefault('12');
      expect(adapter.request!.path, '/addresses/12');
      expect(adapter.request!.method, 'POST');
      expect(Map<String, String>.fromEntries((adapter.request!.data as FormData).fields),
          {'is_default': '1'});
      client.close();
    });
    test('HTTP 200 limit message is not treated as successful save', () async {
      final adapter = CaptureAdapter()..response = {'message': 'limit'};
      final client = Dio(BaseOptions(baseUrl: 'https://example.test/api'))
        ..httpClientAdapter = adapter;
      await expectLater(
          AddressRepo(client: client).addAddress({'address_name': 'Home'}),
          throwsA(isA<AddressSaveFailure>()));
      client.close();
    });
    test('save creates a confirmed ID and later server reload restores it',
        () async {
      final repo = MemoryAddresses();
      final book = AddressBookNotifier(repo, load: false);
      final saved = await book.save({
        'address_name': 'Home',
        'area': 'Area',
        'address_line': 'Street',
        'latitude': '31.9',
        'longitude': '35.2',
        'is_default': true
      });
      expect(saved?.id, 1);
      expect(saved?.isDefault, true);
      book.dispose();
      final reopened = AddressBookNotifier(repo, load: false);
      await reopened.refresh();
      expect(reopened.state.addresses.single.latitude, '31.9');
      reopened.dispose();
    });
    test('edit, default and delete reload the backend result', () async {
      final repo = MemoryAddresses()
        ..rows = [
          Address(id: 1, addressName: 'Home', area: 'Area'),
          Address(id: 2, addressName: 'Work', isDefault: true)
        ];
      final book = AddressBookNotifier(repo, load: false);
      await book.refresh();
      final updated = await book.save({'address_name': 'New', 'area': 'Area'},
          existing: book.state.addresses.first);
      expect(updated?.addressName, 'New');
      await book.mutate(book.state.addresses.first);
      expect(book.state.addresses.where((a) => a.isDefault).single.id, 1);
      await book.mutate(book.state.addresses.first, delete: true);
      expect(book.state.addresses.single.id, 2);
      expect(book.state.addresses.single.isDefault, false);
      book.dispose();
    });
    test('duplicate save taps send one create and select no arbitrary address',
        () async {
      final repo = MemoryAddresses()..hold = Completer<void>();
      final book = AddressBookNotifier(repo, load: false);
      final first = book.save(
          {'address_name': 'Home', 'area': 'Area', 'address_line': 'Street'});
      expect(await book.save({'address_name': 'Other'}), null);
      repo.hold!.complete();
      expect((await first)?.addressName, 'Home');
      expect(repo.creates, 1);
      book.dispose();
    });
    test('three-address limit cannot accidentally select first saved address',
        () async {
      final repo = MemoryAddresses()
        ..rows = [
          for (var i = 1; i <= 3; i++) Address(id: i, addressName: 'Saved')
        ];
      final book = AddressBookNotifier(repo, load: false);
      await book.refresh();
      expect(await book.save({'address_name': 'New', 'address_line': 'Street'}),
          null);
      expect(book.state.error, isNotNull);
      expect(repo.rows.length, 3);
      book.dispose();
    });
    test('offline refresh fails closed and invalidates cached selection',
        () async {
      final repo = MemoryAddresses()..rows = [Address(id: 1)];
      final book = AddressBookNotifier(repo, load: false);
      await book.refresh();
      repo.failure = Exception('offline');
      await book.refresh();
      expect(book.state.addresses, isEmpty);
      expect(book.state.error, isNotNull);
      book.dispose();
    });
    test('401 and 403 give readable errors without manipulating session', () {
      for (final code in [401, 403]) {
        final options = RequestOptions(path: '/addresses');
        expect(
            addressError(DioException(
                requestOptions: options,
                response: Response(requestOptions: options, statusCode: code))),
            isNotEmpty);
      }
    });
  });

  group('Checkout ownership and selection', () {
    test('saved manual ID survives reads and unrelated provider changes',
        () async {
      final container = ProviderContainer(overrides: [
        addressSessionProvider.overrideWith((ref) => Stream.value('session-a'))
      ]);
      await container.read(addressSessionProvider.future);
      container.read(addressIDProvider.notifier).state = '7';
      container.read(calculateTotalProvider.notifier).state = 123;
      expect(container.read(addressIDProvider), '7');
      container.dispose();
    });
    test('account change clears address selection', () async {
      final sessions = StreamController<String>();
      final container = ProviderContainer(overrides: [
        addressSessionProvider.overrideWith((ref) => sessions.stream)
      ]);
      final subscription = container.listen(addressIDProvider, (_, __) {});
      sessions.add('a');
      await Future<void>.delayed(Duration.zero);
      container.read(addressIDProvider.notifier).state = '7';
      sessions.add('b');
      await Future<void>.delayed(Duration.zero);
      expect(container.read(addressIDProvider), '');
      subscription.close();
      container.dispose();
      await sessions.close();
    });
    test(
        'GPS suggestion is first-class but never replaces selected ID or repeats automatically',
        () async {
      final device = FakeLocationDevice();
      final suggestion = LocationSuggestionNotifier(
          LocationService(device: device, describe: (_, __) async => []));
      await suggestion.suggest();
      await suggestion.suggest();
      expect(suggestion.state.point, isNotNull);
      expect(device.readings, 1);
      expect(device.requests, 0);
      suggestion.dispose();
    });
    test(
        'order carries only the selected saved address ID without distance pricing',
        () {
      final address = Address(id: 7, addressName: 'Home');
      expect(selectedOwnedAddress([address], '8'), null);
      final model = OrderPlaceModel(
          pick_date: '2026-12-01',
          pick_hour: '8',
          delivery_date: '2026-12-02',
          delivery_hour: '8',
          products: [],
          address_id: '7',
          additional_service_id: [],
          paymentType: 'cash');
      expect(model.toMap()['address_id'], '7');
      expect(model.toMap().keys, isNot(contains('latitude')));
      expect(model.toMap().keys, isNot(contains('distance')));
      expect(model.toMap().keys, isNot(contains('delivery_charge')));
    });
  });
}
