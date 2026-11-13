import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/providers/address_session_provider.dart';
import 'package:laundry_customer/services/location_service.dart';

class LocationSuggestion {
  const LocationSuggestion(
      {this.point, this.loading = false, this.attempted = false, this.message});
  final PickedLocation? point;
  final bool loading;
  final bool attempted;
  final String? message;
}

final locationSuggestionProvider =
    StateNotifierProvider<LocationSuggestionNotifier, LocationSuggestion>(
        (ref) {
  ref.watch(addressSessionProvider);
  return LocationSuggestionNotifier(ref.watch(locationServiceProvider));
});

class LocationSuggestionNotifier extends StateNotifier<LocationSuggestion> {
  LocationSuggestionNotifier(this.service) : super(const LocationSuggestion());
  final LocationService service;
  Future<void> suggest() async {
    if (state.attempted || state.loading) return;
    state = const LocationSuggestion(loading: true, attempted: true);
    try {
      final point = await service.current();
      if (mounted) state = LocationSuggestion(point: point, attempted: true);
    } on LocationFailure catch (e) {
      if (mounted)
        state = LocationSuggestion(attempted: true, message: e.message);
    } catch (_) {
      if (mounted)
        state = const LocationSuggestion(
            attempted: true,
            message:
                'تعذر تحديد موقعك. اختر عنوانًا محفوظًا أو حدد نقطة على الخريطة.');
    }
  }
}
