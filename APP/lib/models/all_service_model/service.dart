import 'dart:convert';

class Service {
  int? id;
  String? name;
  dynamic nameBn;
  String? description;
  dynamic descriptionBn;
  String? imagePath;

  Service({
    this.id,
    this.name,
    this.nameBn,
    this.description,
    this.descriptionBn,
    this.imagePath,
  });

  @override
  String toString() {
    return 'Service(id: $id, name: $name, nameBn: $nameBn, description: $description, descriptionBn: $descriptionBn, imagePath: $imagePath)';
  }

  factory Service.fromMap(Map<String, dynamic> data) {
    String? img;
    for (final key in [
      'image_path',
      'imagePath',
      'thumbnail',
      'thumbnail_path',
      'thumbnailPath',
      'image',
      'icon',
      'photo'
    ]) {
      if (data[key] != null && data[key].toString().isNotEmpty) {
        img = data[key].toString();
        break;
      }
    }
    return Service(
      id: data['id'] is int ? data['id'] as int : int.tryParse(data['id']?.toString() ?? ''),
      name: data['name']?.toString() ?? data['service_name']?.toString(),
      nameBn: data['name_bn'],
      description: data['description']?.toString(),
      descriptionBn: data['description_bn'],
      imagePath: img,
    );
  }

  Map<String, dynamic> toMap() => {
        'id': id,
        'name': name,
        'name_bn': nameBn,
        'description': description,
        'description_bn': descriptionBn,
        'image_path': imagePath,
      };

  /// `dart:convert`
  ///
  /// Parses the string and returns the resulting Json object as [Service].
  factory Service.fromJson(String data) {
    return Service.fromMap(json.decode(data) as Map<String, dynamic>);
  }

  /// `dart:convert`
  ///
  /// Converts [Service] to a JSON string.
  String toJson() => json.encode(toMap());

  Service copyWith({
    int? id,
    String? name,
    dynamic nameBn,
    String? description,
    dynamic descriptionBn,
    String? imagePath,
  }) {
    return Service(
      id: id ?? this.id,
      name: name ?? this.name,
      nameBn: nameBn ?? this.nameBn,
      description: description ?? this.description,
      descriptionBn: descriptionBn ?? this.descriptionBn,
      imagePath: imagePath ?? this.imagePath,
    );
  }
}
