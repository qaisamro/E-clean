import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'dart:convert';
import 'dart:typed_data';
import 'package:laundry_customer/models/vendor_appointments.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/repos/vendor_appointments_repo.dart';
import 'package:laundry_customer/screens/payment/vendor_appointment_picker.dart';

const pickup = AppointmentSlot(
    date: '2026-11-11', hour: '09:00', title: '09:00 - 11:00', available: true);
const delivery = AppointmentSlot(
    date: '2026-11-11', hour: '15:00', title: '15:00 - 17:00', available: true);
const later = AppointmentSlot(
    date: '2026-11-12', hour: '15:00', title: '15:00 - 17:00', available: true);
const full = AppointmentSlot(
    date: '2026-11-11',
    hour: '12:00',
    title: '12:00 - 14:00',
    available: false,
    reason: 'الفترة ممتلئة');

class FakeAppointments extends VendorAppointmentsRepo {
  FakeAppointments() : super(client: Dio());
  bool enabled = true;
  bool fail = false;
  bool empty = false;
  final vendors = <String>[];
  @override
  Future<VendorAppointments> load(String vendor,
      {AppointmentSlot? pickup}) async {
    vendors.add(vendor);
    if (fail) throw Exception('offline');
    if (empty)
      return const VendorAppointments(
          enabled: true, pickup: [], delivery: [], processingMinutes: 120);
    return VendorAppointments(
        enabled: enabled,
        pickup: [
          full,
          pickup ??
              const AppointmentSlot(
                  date: '2026-11-11',
                  hour: '09:00',
                  title: '09:00 - 11:00',
                  available: true)
        ],
        delivery: pickup == null ? [] : [full, delivery, later],
        processingMinutes: 120);
  }
}

class AppointmentAdapter implements HttpClientAdapter {
  AppointmentAdapter(this.status);
  final int status;
  RequestOptions? request;
  @override
  Future<ResponseBody> fetch(RequestOptions options,
      Stream<Uint8List>? requestStream, Future<void>? cancelFuture) async {
    request = options;
    return ResponseBody.fromString(
        jsonEncode({'message': 'Unavailable'}), status,
        headers: {
          Headers.contentTypeHeader: ['application/json']
        });
  }

  @override
  void close({bool force = false}) {}
}

void main() {
  setUpAll(() async {
    await initializeDateFormatting('en');
  });
  test('parses real server days, closure reasons and clock strings', () {
    final data = VendorAppointments.fromMap({
      'enabled': true,
      'processing_minutes': 1440,
      'days': [
        {
          'date': '2026-11-11',
          'pickup': [
            {
              'date': '2026-11-11',
              'hour': '09:30',
              'title': '09:30 - 11:30',
              'is_booked': true,
              'reason': 'إجازة'
            }
          ],
          'delivery': []
        },
      ]
    });
    expect(data.pickup.single.hour, '09:30');
    expect(data.pickup.single.available, isFalse);
    expect(data.pickup.single.reason, 'إجازة');
  });

  test(
      'older backend 404 falls back, service failure never bypasses scheduling',
      () async {
    final adapter = AppointmentAdapter(404);
    final client = Dio(BaseOptions(baseUrl: 'http://localhost/api'))
      ..httpClientAdapter = adapter;
    final repo = VendorAppointmentsRepo(client: client);
    expect((await repo.load('6', pickup: pickup)).enabled, isFalse);
    expect(adapter.request?.path, '/vendors/6/appointments');
    expect(adapter.request?.queryParameters['pick_date'], '2026-11-11');
    client.httpClientAdapter = AppointmentAdapter(503);
    await expectLater(repo.load('6'), throwsA(isA<DioException>()));
  });

  Future<ProviderContainer> mount(
      WidgetTester tester, FakeAppointments repo) async {
    final container = ProviderContainer(overrides: [
      vendorAppointmentsRepoProvider.overrideWithValue(repo),
      addressIDProvider.overrideWith((ref) => 'owned-address')
    ]);
    container.read(activeVendorIdProvider.notifier).state = '6';
    await tester.pumpWidget(UncontrolledProviderScope(
        container: container,
        child: const MaterialApp(
            home: Scaffold(
                body: SingleChildScrollView(
                    child: VendorAppointmentPicker(
                        legacyPicker: Text('legacy schedule')))))));
    await tester.pumpAndSettle();
    addTearDown(container.dispose);
    return container;
  }

  testWidgets(
      'selects pickup, suggests first valid delivery and publishes legacy fields',
      (tester) async {
    final container = await mount(tester, FakeAppointments());
    expect(find.text('legacy schedule'), findsNothing);
    await tester.tap(find.widgetWithText(ChoiceChip, '09:00 - 11:00'));
    await tester.pumpAndSettle();
    expect(container.read(scheduleProvider('Pick Up'))?.schedule.hour, '09:00');
    expect(
        container.read(scheduleProvider('Delivery'))?.schedule.hour, '15:00');
    expect(container.read(scheduleProvider('Delivery'))?.vendorId, '6');
    expect(find.textContaining('ملخص الحجز'), findsOneWidget);
    final disabled = tester
        .widgetList<ChoiceChip>(find.byType(ChoiceChip))
        .where((chip) => chip.onSelected == null);
    expect(disabled, isNotEmpty);
  });

  testWidgets('allows another delivery day without altering pickup or address',
      (tester) async {
    final container = await mount(tester, FakeAppointments());
    final originalAddress = container.read(addressIDProvider);
    await tester.tap(find.widgetWithText(ChoiceChip, '09:00 - 11:00'));
    await tester.pumpAndSettle();
    await tester.ensureVisible(find.widgetWithText(ChoiceChip, 'Thu 12/11'));
    await tester.tap(find.widgetWithText(ChoiceChip, 'Thu 12/11'));
    await tester.pumpAndSettle();
    await tester.tap(find.widgetWithText(ChoiceChip, '15:00 - 17:00'));
    await tester.pumpAndSettle();
    expect(container.read(scheduleProvider('Delivery'))?.dateTime.day, 12);
    expect(container.read(scheduleProvider('Pick Up'))?.dateTime.day, 11);
    expect(container.read(addressIDProvider), originalAddress);
  });

  testWidgets(
      'vendor change clears previous selections and fetches correct store',
      (tester) async {
    final repo = FakeAppointments();
    final container = await mount(tester, repo);
    await tester.tap(find.widgetWithText(ChoiceChip, '09:00 - 11:00'));
    await tester.pumpAndSettle();
    container.read(activeVendorIdProvider.notifier).state = '7';
    await tester.pumpAndSettle();
    expect(repo.vendors.last, '7');
    expect(container.read(scheduleProvider('Pick Up')), isNull);
    expect(container.read(scheduleProvider('Delivery')), isNull);
  });

  testWidgets('disabled vendor uses legacy UI', (tester) async {
    await mount(tester, FakeAppointments()..enabled = false);
    expect(find.text('legacy schedule'), findsOneWidget);
  });

  testWidgets(
      'empty availability is explicit and a narrow screen does not overflow',
      (tester) async {
    tester.view.physicalSize = const Size(320, 640);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.resetPhysicalSize);
    addTearDown(tester.view.resetDevicePixelRatio);
    await mount(tester, FakeAppointments()..empty = true);
    expect(find.textContaining('لا توجد مواعيد متاحة'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  testWidgets(
      'network failure clears dates and offers retry, never fabricates availability',
      (tester) async {
    final repo = FakeAppointments();
    final container = await mount(tester, repo);
    await tester.tap(find.widgetWithText(ChoiceChip, '09:00 - 11:00'));
    await tester.pumpAndSettle();
    repo.fail = true;
    await tester.ensureVisible(find.text('تحديث التوفر'));
    await tester.tap(find.text('تحديث التوفر'));
    await tester.pumpAndSettle();
    expect(find.text('إعادة المحاولة'), findsOneWidget);
    expect(container.read(scheduleProvider('Pick Up')), isNull);
    expect(container.read(scheduleProvider('Delivery')), isNull);
  });
}
