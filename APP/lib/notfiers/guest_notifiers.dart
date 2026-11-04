import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/repos/guest_repo.dart';
import 'package:laundry_customer/services/api_state.dart';
import 'package:laundry_customer/services/network_exceptions.dart';

class GuestRepoNotifier<T> extends StateNotifier<ApiState<T>> {
  GuestRepoNotifier({required this.repo, required Future<T> Function() fetch}) : super(const ApiState.initial()) {
    _fetch = fetch;
    load();
  }
  final IGuestRepo repo;
  late final Future<T> Function() _fetch;

  Future<void> load() async {
    state = const ApiState.loading();
    try {
      final result = await _fetch();
      state = ApiState.loaded(data: result);
    } catch (e) {
      state = ApiState.error(error: NetworkExceptions.errorText(e));
    }
  }
  Future<void> refresh() => load();
}
