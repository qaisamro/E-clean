import 'dart:convert';

class Vendor {
  int? id;
  String? name;
  dynamic nameBn;
  String? address;
  String? phone;
  String? email;
  String? description;
  dynamic descriptionBn;
  String? imagePath;
  String? logoPath;

  Vendor({
    this.id,
    this.name,
    this.nameBn,
    this.address,
    this.phone,
    this.email,
    this.description,
    this.descriptionBn,
    this.imagePath,
    this.logoPath,
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
    return Vendor(
      id: parsedId,
      name: map['name']?.toString() ?? map['shop_name']?.toString() ?? map['vendor_name']?.toString(),
      nameBn: map['name_bn'],
      address: addr,
      phone: map['phone']?.toString() ?? map['mobile']?.toString(),
      email: map['email']?.toString(),
      description: map['description']?.toString() ?? map['desc']?.toString(),
      descriptionBn: map['description_bn'],
      imagePath: img,
      logoPath: rawLogo?.toString(),
    );
  }

  Map<String, dynamic> toMap() => {
        'id': id,
        'name': name,
        'name_bn': nameBn,
        'address': address,
        'phone': phone,
        'email': email,
        'description': description,
        'description_bn': descriptionBn,
        'image_path': imagePath,
        'logo_path': logoPath,
      };

  factory Vendor.fromJson(String source) =>
      Vendor.fromMap(json.decode(source) as Map<String, dynamic>);

  String toJson() => json.encode(toMap());

  Vendor copyWith({
    int? id,
    String? name,
    dynamic nameBn,
    String? address,
    String? phone,
    String? email,
    String? description,
    dynamic descriptionBn,
    String? imagePath,
    String? logoPath,
  }) {
    return Vendor(
      id: id ?? this.id,
      name: name ?? this.name,
      nameBn: nameBn ?? this.nameBn,
      address: address ?? this.address,
      phone: phone ?? this.phone,
      email: email ?? this.email,
      description: description ?? this.description,
      descriptionBn: descriptionBn ?? this.descriptionBn,
      imagePath: imagePath ?? this.imagePath,
      logoPath: logoPath ?? this.logoPath,
    );
  }

  @override
  String toString() =>
      'Vendor(id: $id, name: $name, address: $address, imagePath: $imagePath)';

  /// Returns display image - prefers imagePath, then logoPath
  String get displayImage => imagePath ?? logoPath ?? '';

  /// Returns display address - fallback to empty string
  String get displayAddress => address ?? '';

  /// Returns display name - fallback to id if name missing
  String get displayName => name ?? 'Store #$id';
}
