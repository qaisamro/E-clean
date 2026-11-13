import 'dart:convert';

class Vendor {
  int? id;
  String? name;
  dynamic nameBn;
  String? address;
  String? city;
  String? cityId;
  bool cityMetadata;
  bool showInPartners;
  String? phone;
  String? email;
  String? description;
  dynamic descriptionBn;
  String? imagePath;
  String? logoPath;
  String? backgroundPath;
  double? latitude;
  double? longitude;
  double? rating;
  int? ratingCount;

  Vendor({
    this.id,
    this.name,
    this.nameBn,
    this.address,
    this.city,
    this.cityId,
    this.cityMetadata = false,
    this.showInPartners = true,
    this.phone,
    this.email,
    this.description,
    this.descriptionBn,
    this.imagePath,
    this.logoPath,
    this.backgroundPath,
    this.latitude,
    this.longitude,
    this.rating,
    this.ratingCount,
  });

  factory Vendor.fromMap(Map<String, dynamic> map) {
    // Handle multiple possible keys for id
    int? parsedId;
    final dynamic rawId = map['id'] ?? map['vendor_id'] ?? map['_id'];
    if (rawId is int) {
      parsedId = rawId;
    } else if (rawId is String) {
      parsedId = int.tryParse(rawId);
    }

    // Handle multiple possible image keys (يدعم snake_case و camelCase من API)
    String? img;
    for (final key in [
      'logoPath',
      'logo_path',
      'image_path',
      'imagePath',
      'image',
      'logo',
      'logo_url',
      'image_url',
      'photo',
    ]) {
      if (map[key] != null && map[key].toString().isNotEmpty) {
        img = map[key].toString();
        break;
      }
    }

    // Handle multiple possible address keys
    String? addr;
    for (final key in [
      'address',
      'full_address',
      'location',
      'shop_address',
      'vendor_address',
    ]) {
      if (map[key] != null && map[key].toString().isNotEmpty) {
        addr = map[key].toString();
        break;
      }
    }

    // logoPath يدعم camelCase و snake_case
    final rawLogo = map['logoPath'] ?? map['logo_path'] ?? map['logo'] ?? img;
    final rawBackground =
        map['backgroundPath'] ?? map['background_path'] ?? map['background'];

    final rawRating = map['rating'] ?? map['avg_rating'] ?? map['rate'];
    double? parsedRating;
    if (rawRating != null) {
      parsedRating = double.tryParse(rawRating.toString());
    }

    final rawRatingCount =
        map['rating_count'] ?? map['ratings_count'] ?? map['reviews_count'];
    int? parsedRatingCount;
    if (rawRatingCount != null) {
      parsedRatingCount = int.tryParse(rawRatingCount.toString());
    }

    return Vendor(
      id: parsedId,
      name: map['name']?.toString() ??
          map['shop_name']?.toString() ??
          map['vendor_name']?.toString(),
      nameBn: map['name_bn'],
      address: addr,
      city: map['city']?.toString(),
      cityId: map['city_id']?.toString(),
      cityMetadata: map.containsKey('city_id'),
      showInPartners: !map.containsKey('show_in_partners') ||
          map['show_in_partners'] == null ||
          map['show_in_partners'] == true ||
          map['show_in_partners'] == 1 ||
          map['show_in_partners'].toString() == '1' ||
          map['show_in_partners'].toString().toLowerCase() == 'true',
      phone: map['phone']?.toString() ?? map['mobile']?.toString(),
      email: map['email']?.toString(),
      description: map['description']?.toString() ?? map['desc']?.toString(),
      descriptionBn: map['description_bn'],
      imagePath: img,
      logoPath: rawLogo?.toString(),
      backgroundPath: rawBackground?.toString(),
      latitude: double.tryParse(map['latitude']?.toString() ?? ''),
      longitude: double.tryParse(map['longitude']?.toString() ?? ''),
      rating: parsedRating,
      ratingCount: parsedRatingCount,
    );
  }

  Map<String, dynamic> toMap() => {
        'id': id,
        'name': name,
        'name_bn': nameBn,
        'address': address,
        'city': city,
        'city_id': cityId,
        'show_in_partners': showInPartners,
        'phone': phone,
        'email': email,
        'description': description,
        'description_bn': descriptionBn,
        'image_path': imagePath,
        'logo_path': logoPath,
        'background_path': backgroundPath,
        'latitude': latitude,
        'longitude': longitude,
        'rating': rating,
        'rating_count': ratingCount,
      };

  factory Vendor.fromJson(String source) =>
      Vendor.fromMap(json.decode(source) as Map<String, dynamic>);

  String toJson() => json.encode(toMap());

  Vendor copyWith({
    int? id,
    String? name,
    dynamic nameBn,
    String? address,
    String? city,
    String? cityId,
    String? phone,
    String? email,
    String? description,
    dynamic descriptionBn,
    String? imagePath,
    String? logoPath,
    String? backgroundPath,
    double? latitude,
    double? longitude,
    double? rating,
    int? ratingCount,
  }) {
    return Vendor(
      id: id ?? this.id,
      name: name ?? this.name,
      nameBn: nameBn ?? this.nameBn,
      address: address ?? this.address,
      city: city ?? this.city,
      cityId: cityId ?? this.cityId,
      cityMetadata: cityMetadata,
      phone: phone ?? this.phone,
      email: email ?? this.email,
      description: description ?? this.description,
      descriptionBn: descriptionBn ?? this.descriptionBn,
      imagePath: imagePath ?? this.imagePath,
      logoPath: logoPath ?? this.logoPath,
      backgroundPath: backgroundPath ?? this.backgroundPath,
      latitude: latitude ?? this.latitude,
      longitude: longitude ?? this.longitude,
      rating: rating ?? this.rating,
      ratingCount: ratingCount ?? this.ratingCount,
    );
  }

  @override
  String toString() =>
      'Vendor(id: $id, name: $name, address: $address, imagePath: $imagePath, rating: $rating)';

  /// Returns display image - prefers imagePath, then logoPath
  String get displayImage => imagePath ?? logoPath ?? '';

  /// Returns display address - fallback to empty string
  String get displayAddress => address ?? '';

  /// Returns display name - fallback to id if name missing
  String get displayName => name ?? 'Store #$id';

  /// Returns display rating - defaults to 5.0
  double get displayRating => rating ?? 5.0;

  /// Returns total reviews count
  int get displayRatingCount => ratingCount ?? 0;

  bool get hasMapLocation =>
      id != null &&
      name?.trim().isNotEmpty == true &&
      latitude != null &&
      longitude != null &&
      latitude!.isFinite &&
      longitude!.isFinite &&
      latitude!.abs() <= 90 &&
      longitude!.abs() <= 180;
}
