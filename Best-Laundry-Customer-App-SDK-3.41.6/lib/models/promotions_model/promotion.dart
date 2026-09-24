import 'dart:convert';

class Promotion {
  int? id;
  String? title;
  String? description;
  String? imagePath;
  int? vendorId;

  Promotion(
      {this.id, this.title, this.description, this.imagePath, this.vendorId});

  @override
  String toString() {
    return 'Promotion(title: $title, description: $description, imagePath: $imagePath)';
  }

  factory Promotion.fromMap(Map<String, dynamic> data) => Promotion(
        id: data['id'] is int
            ? data['id'] as int
            : int.tryParse('${data['id']}'),
        title: data['title'] as String?,
        description: data['description'] as String?,
        imagePath: data['image_path'] as String?,
        vendorId: data['vendor_id'] is int
            ? data['vendor_id'] as int
            : int.tryParse('${data['vendor_id']}'),
      );

  Map<String, dynamic> toMap() => {
        'id': id,
        'title': title,
        'description': description,
        'image_path': imagePath,
        'vendor_id': vendorId,
      };

  /// `dart:convert`
  ///
  /// Parses the string and returns the resulting Json object as [Promotion].
  factory Promotion.fromJson(String data) {
    return Promotion.fromMap(json.decode(data) as Map<String, dynamic>);
  }

  /// `dart:convert`
  ///
  /// Converts [Promotion] to a JSON string.
  String toJson() => json.encode(toMap());

  Promotion copyWith({
    int? id,
    String? title,
    String? description,
    String? imagePath,
    int? vendorId,
  }) {
    return Promotion(
      id: id ?? this.id,
      title: title ?? this.title,
      description: description ?? this.description,
      imagePath: imagePath ?? this.imagePath,
      vendorId: vendorId ?? this.vendorId,
    );
  }
}
