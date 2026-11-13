import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/services/api_state.dart';

/// City changes dispose old feeds while their HTTP requests may still finish.
abstract class SafeApiNotifier<T> extends StateNotifier<ApiState<T>> {
  SafeApiNotifier(ApiState<T> initial) : super(initial);
  void emit(ApiState<T> value) {
    if (mounted) state = value;
  }
}
