import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/repos/city_repo.dart';
import 'package:laundry_customer/services/location_service.dart';

abstract class CityChoiceStore {
  Map? read();
  Future<void> write(String id, bool manual);
  List<AppCity> readCities() => const [];
  Future<void> cacheCities(List<AppCity> cities) async {}
}

class HiveCityChoiceStore extends CityChoiceStore {
  static const key = 'home_city_choice_v1';
  static const catalogKey = 'home_city_catalog_v1';
  @override
  List<AppCity> readCities() {
    try {
      final raw = Hive.box(AppHSC.appSettingsBox).get(catalogKey);
      if (raw is! List) return const [];
      return raw.map((e) => AppCity.fromMap(e as Map)).toList();
    } catch (_) {
      return const [];
    }
  }

  @override
  Future<void> cacheCities(List<AppCity> cities) =>
      Hive.box(AppHSC.appSettingsBox)
          .put(catalogKey, cities.map((c) => c.toMap()).toList());
  @override
  Map? read() {
    if (!Hive.isBoxOpen(AppHSC.appSettingsBox)) return null;
    final value = Hive.box(AppHSC.appSettingsBox).get(key);
    return value is Map ? value : null;
  }

  @override
  Future<void> write(String id, bool manual) =>
      Hive.box(AppHSC.appSettingsBox).put(key, {'id': id, 'manual': manual});
}

class CitySelection {
  const CitySelection(
      {this.cities = const [],
      this.city,
      this.loading = true,
      this.manual = false,
      this.message});
  final List<AppCity> cities;
  final AppCity? city;
  final bool loading;
  final bool manual;
  final String? message;
}

final cityRepoProvider = Provider<ICityRepo>((ref) => CityRepo());
final cityChoiceStoreProvider =
    Provider<CityChoiceStore>((ref) => HiveCityChoiceStore());
final citySelectionProvider =
    StateNotifierProvider<CitySelectionController, CitySelection>((ref) =>
        CitySelectionController(
            ref.watch(cityRepoProvider),
            ref.watch(locationServiceProvider),
            ref.watch(cityChoiceStoreProvider)));
final currentCityIdProvider =
    Provider<String?>((ref) => ref.watch(citySelectionProvider).city?.id);

class CitySelectionController extends StateNotifier<CitySelection> {
  CitySelectionController(this.repo, this.location, this.store)
      : super(const CitySelection()) {
    unawaited(initialize());
  }
  final ICityRepo repo;
  final LocationService location;
  final CityChoiceStore store;
  int _generation = 0;
  Future<void> _writes = Future.value();

  AppCity? _find(String? id) {
    for (final city in state.cities) {
      if (city.id == id) return city;
    }
    return null;
  }

  Future<void> initialize() async {
    final generation = ++_generation;
    final saved = store.read();
    final cached = store.readCities();
    AppCity? cachedChoice;
    for (final city in cached) {
      if (city.id == saved?['id']) cachedChoice = city;
    }
    // Restore before any network await. Delivery addresses and cart boxes are untouched.
    state = CitySelection(
        cities: cached,
        city: cachedChoice,
        loading: cached.isEmpty,
        manual: saved?['manual'] == true);
    try {
      final cities = await repo.cities();
      if (!mounted || generation != _generation) return;
      try {
        await store.cacheCities(cities);
      } catch (_) {/* Selection still works in memory. */}
      if (!mounted || generation != _generation) return;
      state = CitySelection(cities: cities, loading: false);
      final selected = _find(saved?['id'] as String?);
      if (selected != null) {
        state = CitySelection(
            cities: cities,
            city: selected,
            loading: false,
            manual: saved?['manual'] == true);
      } else if (saved?['manual'] == true) {
        state = CitySelection(
            cities: cities,
            loading: false,
            message: 'المدينة المحفوظة لم تعد متاحة. اختر مدينة أخرى.');
      } else {
        await useCurrentLocation(requestPermission: false);
      }
    } catch (_) {
      if (mounted && generation == _generation) {
        state = CitySelection(
            cities: state.cities,
            city: state.city,
            manual: state.manual,
            loading: false,
            message: state.cities.isEmpty
                ? 'تعذر تحميل المدن المعتمدة. تحقق من الاتصال وحاول مجددًا.'
                : 'أنت دون اتصال. يمكنك استخدام قائمة المدن المحفوظة؛ تحديث المتاجر يحتاج إلى الإنترنت.');
      }
    }
  }

  Future<void> _save(AppCity city, bool manual) async {
    // Serialize writes so the last manual tap is also the persisted choice.
    _writes = _writes
        .catchError((Object _) {})
        .then((_) => store.write(city.id, manual));
    try {
      await _writes;
    } catch (_) {
      if (mounted && state.city?.id == city.id) {
        state = CitySelection(
            cities: state.cities,
            city: state.city,
            loading: false,
            manual: state.manual,
            message: 'تم تغيير المدينة، لكن تعذر حفظها على الجهاز.');
      }
    }
  }

  Future<void> select(String id) async {
    final city = _find(id);
    if (city == null) return;
    ++_generation;
    state = CitySelection(
        cities: state.cities, city: city, manual: true, loading: false);
    await _save(city, true);
  }

  Future<void> useCurrentLocation({bool requestPermission = true}) async {
    if (state.cities.isEmpty) return;
    final generation = ++_generation;
    state = CitySelection(
        cities: state.cities,
        city: state.city,
        manual: state.manual,
        loading: true);
    try {
      final point = await location.currentCoordinates(
          requestPermission: requestPermission);
      if (!mounted || generation != _generation) return;
      final id = await repo.resolve(point);
      if (!mounted || generation != _generation) return;
      final city = _find(id);
      if (city == null)
        throw const LocationFailure(
            'تعذر التحقق من مدينة موقعك بدقة. اختر المدينة يدويًا.');
      state = CitySelection(cities: state.cities, city: city, loading: false);
      await _save(city, false);
    } catch (error) {
      if (mounted && generation == _generation) {
        state = CitySelection(
            cities: state.cities,
            city: state.city,
            manual: state.manual,
            loading: false,
            message: error is LocationFailure && error.settings
                ? 'خدمة الموقع متوقفة. فعّل GPS أو اختر المدينة يدويًا.'
                : error is LocationFailure && error.permission
                    ? 'إذن الموقع مغلق. فعّله من إعدادات التطبيق أو اختر المدينة يدويًا.'
                    : 'تعذر التحقق من مدينة موقعك. تحقق من إذن الموقع والاتصال، أو اختر المدينة يدويًا.');
      }
    }
  }
}
