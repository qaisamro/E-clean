import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/addres_list_model/address.dart';
import 'package:laundry_customer/providers/address_provider.dart';
import 'package:laundry_customer/providers/address_session_provider.dart';
import 'package:laundry_customer/repos/address_repo.dart';

String addressError(Object error) {
  if (error is AddressSaveFailure) return error.message;
  if (error is DioException) {
    if (error.response?.statusCode == 401)
      return 'انتهت صلاحية الجلسة. سجّل الدخول مجددًا.';
    if (error.response?.statusCode == 403)
      return 'لا يمكن الوصول إلى العناوين حاليًا. تحقق من حالة حسابك.';
    if (error.response?.statusCode == 404)
      return 'هذا العنوان لم يعد متاحًا. حدّث القائمة.';
    if (error.response?.statusCode == 422)
      return 'تحقق من بيانات العنوان ثم حاول مجددًا.';
  }
  return 'تعذر الاتصال. تحقق من الإنترنت وحاول مجددًا.';
}

class AddressBookState {
  const AddressBookState(
      {this.addresses = const [],
      this.loading = false,
      this.busy = false,
      this.error});
  final List<Address> addresses;
  final bool loading;
  final bool busy;
  final String? error;
}

final addressBookProvider =
    StateNotifierProvider<AddressBookNotifier, AddressBookState>((ref) {
  final session = ref.watch(addressSessionProvider).valueOrNull ?? '';
  return AddressBookNotifier(ref.watch(addresRepoProvider),
      load: session.isNotEmpty);
});

class AddressBookNotifier extends StateNotifier<AddressBookState> {
  AddressBookNotifier(this.repo, {bool load = true})
      : super(const AddressBookState()) {
    if (load) refresh();
  }
  final IAddressRepo repo;
  int _request = 0;

  Future<void> refresh({bool afterMutation = false}) async {
    if (state.busy && !afterMutation) return;
    final request = ++_request;
    state = AddressBookState(
        addresses: state.addresses, loading: true, busy: state.busy);
    try {
      final data = await repo.getAddresses();
      if (!mounted || request != _request) return;
      state = AddressBookState(
          addresses: data.data?.addresses ?? [], busy: state.busy);
    } catch (e) {
      if (mounted && request == _request) {
        // Fail closed: cached addresses cannot be used after an auth/load error.
        state = AddressBookState(error: addressError(e), busy: state.busy);
      }
    }
  }

  Future<Address?> save(Map<String, dynamic> fields,
      {Address? existing}) async {
    if (state.busy) return null;
    _request++;
    state = AddressBookState(addresses: state.addresses, busy: true);
    try {
      Address? saved;
      if (existing == null) {
        final before = state.addresses.map((a) => a.id).toSet();
        final response = await repo.addAddress(fields);
        final body = response?.data;
        if (body is! Map ||
            body['data'] is! Map ||
            body['data']['addresses'] is! List) {
          throw const AddressSaveFailure('لم يتم حفظ العنوان. حاول مرة أخرى.');
        }
        final all = (body['data']['addresses'] as List)
            .map((e) => Address.fromMap(Map<String, dynamic>.from(e as Map)))
            .toList();
        // The server echoes the full list after a write; the newly created
        // address is whatever id wasn't present before the call.
        final newOnes = all.where((a) => !before.contains(a.id)).toList();
        if (newOnes.isEmpty) {
          // Fallback: a stale local list (or duplicate id echo) must not
          // mask a successful save. If the list grew, the newest address
          // (highest id) is the one just added.
          if (all.length > before.length) {
            saved = all.reduce((a, b) => (a.id ?? 0) >= (b.id ?? 0) ? a : b);
          } else {
            throw const AddressSaveFailure('لم يتم حفظ العنوان. حاول مرة أخرى.');
          }
        } else {
          saved = newOnes.length == 1
              ? newOnes.single
              : newOnes.reduce((a, b) => (a.id ?? 0) >= (b.id ?? 0) ? a : b);
        }
        if (!mounted) return null;
        state = AddressBookState(addresses: all);
      } else {
        await repo.updateAddress(fields, existing.id.toString());
        if (!mounted) return null;
        await refresh(afterMutation: true);
        if (!mounted) return null;
        for (final item in state.addresses) {
          if (item.id == existing.id) saved = item;
        }
        if (saved == null)
          throw const AddressSaveFailure(
              'تم إرسال التعديل، لكن تعذر تأكيده. حدّث القائمة قبل المحاولة مجددًا.');
      }
      return saved;
    } catch (e) {
      if (mounted)
        state = AddressBookState(
            addresses: state.addresses, error: addressError(e));
      return null;
    } finally {
      if (mounted)
        state =
            AddressBookState(addresses: state.addresses, error: state.error);
    }
  }

  Future<bool> mutate(Address address, {bool delete = false}) async {
    if (state.busy || address.id == null) return false;
    _request++;
    state = AddressBookState(addresses: state.addresses, busy: true);
    try {
      if (delete) {
        await repo.deleteAddress(address.id.toString());
      } else {
        await repo.setDefault(address.id.toString());
      }
      if (!mounted) return false;
      await refresh(afterMutation: true);
      return mounted && state.error == null;
    } catch (e) {
      if (mounted)
        state = AddressBookState(
            addresses: state.addresses, error: addressError(e));
      return false;
    } finally {
      if (mounted)
        state =
            AddressBookState(addresses: state.addresses, error: state.error);
    }
  }
}
