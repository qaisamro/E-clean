import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:laundry_customer/providers/address_book_provider.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/address_session_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/screens/address/add_update_address.dart';
import 'package:laundry_customer/screens/address/location_picker_screen.dart';
import 'package:laundry_customer/screens/address/manage_address_screen.dart';
import 'package:laundry_customer/services/location_service.dart';

import 'address_system_test.dart' show FakeLocationDevice, MemoryAddresses;
import 'package:laundry_customer/models/addres_list_model/address.dart';

class LocalTiles extends TileProvider {
  @override
  ImageProvider getImage(TileCoordinates coordinates, TileLayer options) =>
      const AssetImage('assets/images/app_icon.png');
}

void main() {
  Widget app(Widget child, {MemoryAddresses? repo}) => ProviderScope(
        overrides: [
          addressSessionProvider
              .overrideWith((ref) => Stream.value('test-session')),
          addresRepoProvider.overrideWithValue(repo ?? MemoryAddresses()),
          locationServiceProvider.overrideWithValue(LocationService(
              device: FakeLocationDevice(), describe: (_, __) async => [])),
        ],
        child: MaterialApp(
            theme: ThemeData(colorSchemeSeed: const Color(0xFF028090)),
            home: child),
      );

  testWidgets('map overview never enables confirmation until an explicit tap',
      (tester) async {
    await tester
        .pumpWidget(app(LocationPickerScreen(tileProvider: LocalTiles())));
    await tester.pumpAndSettle();
    expect(tester.widget<FilledButton>(find.byType(FilledButton)).onPressed,
        isNull);
    final map = find.byType(FlutterMap);
    await tester.tapAt(tester.getCenter(map));
    await tester.pump(const Duration(milliseconds: 350));
    await tester.pumpAndSettle();
    expect(tester.widget<FilledButton>(find.byType(FilledButton)).onPressed,
        isNotNull);
    expect(find.byIcon(Icons.location_pin), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  testWidgets('dragging the map selects and moves the chosen point',
      (tester) async {
    await tester
        .pumpWidget(app(LocationPickerScreen(tileProvider: LocalTiles())));
    await tester.pumpAndSettle();
    await tester.drag(find.byType(FlutterMap), const Offset(70, 0));
    await tester.pumpAndSettle();
    final map = tester.widget<FlutterMap>(find.byType(FlutterMap));
    expect(map.mapController!.camera.center.longitude, isNot(35.2));
    expect(tester.widget<FilledButton>(find.byType(FilledButton)).onPressed,
        isNotNull);
    expect(find.byIcon(Icons.location_pin), findsOneWidget);
  });

  testWidgets('saving a new address without a chosen map point is blocked',
      (tester) async {
    final repo = MemoryAddresses();
    await tester.pumpWidget(app(const AddOrEditAddress(), repo: repo));
    await tester.pumpAndSettle();
    await tester.enterText(find.byType(TextFormField).at(0), 'البيت');
    await tester.enterText(find.byType(TextFormField).at(1), 'رام الله');
    await tester.enterText(find.byType(TextFormField).at(2), 'شارع 12');
    await tester.ensureVisible(find.text('حفظ العنوان'));
    await tester.tap(find.text('حفظ العنوان'));
    await tester.pumpAndSettle();
    expect(find.text('حدد نقطة صالحة على الخريطة قبل حفظ العنوان.'),
        findsOneWidget);
    expect(repo.creates, 0);
  });

  testWidgets(
      'address selection is RTL, marks default, and requires explicit use',
      (tester) async {
    final repo = MemoryAddresses()
      ..rows = [
        Address(
            id: 1,
            addressName: 'البيت',
            area: 'رام الله',
            addressLine: 'شارع 12',
            isDefault: true),
        Address(id: 2, addressName: 'العمل', area: 'المدينة'),
      ];
    await tester
        .pumpWidget(app(const ManageAddressScreen(choose: true), repo: repo));
    await tester.pumpAndSettle();
    expect(find.text('اختر عنوان التوصيل'), findsOneWidget);
    expect(find.text('الأساسي'), findsOneWidget);
    final cardContext = tester.element(find.byType(AddressCard).first);
    expect(Directionality.of(cardContext), TextDirection.rtl);
    final container = ProviderScope.containerOf(cardContext);
    expect(container.read(addressIDProvider), '');
    await tester.ensureVisible(find.text('البيت'));
    await tester.tap(find.text('البيت'));
    await tester.pumpAndSettle();
    expect(find.text('العنوان المحدد'), findsOneWidget);
    expect(container.read(addressIDProvider), '');
    expect(
        tester
            .widget<FilledButton>(
                find.widgetWithText(FilledButton, 'استخدم هذا العنوان'))
            .onPressed,
        isNotNull);
    expect(tester.takeException(), isNull);
  });

  testWidgets(
      'delete confirmation can be cancelled without changing backend data',
      (tester) async {
    final repo = MemoryAddresses()
      ..rows = [Address(id: 1, addressName: 'البيت')];
    await tester.pumpWidget(app(const ManageAddressScreen(), repo: repo));
    await tester.pumpAndSettle();
    await tester.ensureVisible(find.text('حذف'));
    await tester.tap(find.text('حذف'));
    await tester.pumpAndSettle();
    expect(find.text('حذف العنوان؟'), findsOneWidget);
    await tester.tap(find.text('إلغاء'));
    await tester.pumpAndSettle();
    expect(repo.rows.length, 1);
  });

  testWidgets('address editor fits a narrow screen with keyboard inset',
      (tester) async {
    tester.view.physicalSize = const Size(320, 640);
    tester.view.devicePixelRatio = 1;
    tester.view.viewInsets = const FakeViewPadding(bottom: 240);
    addTearDown(tester.view.resetPhysicalSize);
    addTearDown(tester.view.resetDevicePixelRatio);
    addTearDown(tester.view.resetViewInsets);
    await tester.pumpWidget(app(const AddOrEditAddress()));
    await tester.pumpAndSettle();
    await tester.ensureVisible(find.text('حفظ العنوان'));
    expect(tester.takeException(), isNull);
  });
}
