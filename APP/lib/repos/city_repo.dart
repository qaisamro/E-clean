import 'package:dio/dio.dart';
import 'package:laundry_customer/services/api_service.dart';
import 'package:laundry_customer/services/location_service.dart';

class AppCity {
  const AppCity(this.id, this.name, this.districts);
  final String id;
  final String name;
  final List<String> districts;
  Map<String, dynamic> toMap() =>
      {'id': id, 'name': name, 'districts': districts};
  factory AppCity.fromMap(Map map) => AppCity(
      map['id'] as String,
      map['name'] as String,
      List<String>.from((map['districts'] as List?) ?? []));
}

abstract class ICityRepo {
  Future<List<AppCity>> cities();
  Future<String?> resolve(PickedLocation point);
}

class CityRepo implements ICityRepo {
  CityRepo({Dio? client}) : _dio = client ?? getDio();
  final Dio _dio;
  @override
  Future<List<AppCity>> cities() async {
    final response = await _dio.get('/cities');
    return (response.data['data']['cities'] as List)
        .map((e) => AppCity.fromMap(e as Map))
        .toList();
  }

  @override
  Future<String?> resolve(PickedLocation point) async {
    final response = await _dio.post('/cities/resolve', data: {
      'latitude': point.latitude,
      'longitude': point.longitude,
      'accuracy': point.accuracy,
    });
    return response.data['data']['city']?['id'] as String?;
  }
}
