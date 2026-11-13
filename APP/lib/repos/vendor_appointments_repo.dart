import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:laundry_customer/models/vendor_appointments.dart';
import 'package:laundry_customer/services/api_service.dart';

final vendorAppointmentsRepoProvider =
    Provider((ref) => VendorAppointmentsRepo());

class VendorAppointmentsRepo {
  VendorAppointmentsRepo({Dio? client}) : _client = client ?? getDio();
  final Dio _client;
  Future<VendorAppointments> load(String vendor,
      {AppointmentSlot? pickup}) async {
    try {
      final response =
          await _client.get('/vendors/$vendor/appointments', queryParameters: {
        if (pickup != null) ...{
          'pick_date': pickup.date,
          'pick_hour': pickup.hour
        }
      });
      return VendorAppointments.fromMap(
          Map<String, dynamic>.from(response.data['data'] as Map));
    } on DioException catch (error) {
      // A backend predating this additive endpoint keeps its existing checkout.
      if (error.response?.statusCode == 404) {
        return const VendorAppointments(
            enabled: false, pickup: [], delivery: [], processingMinutes: 0);
      }
      rethrow;
    }
  }
}
