import 'dart:convert';

class WideBanner {
  String? title;
  String? description;
  String? imagePath;

  WideBanner({this.title, this.description, this.imagePath});

  @override
  String toString() {
    return 'WideBanner(title: $title, description: $description, imagePath: $imagePath)';
  }

  factory WideBanner.fromMap(Map<String, dynamic> data) => WideBanner(
        title: data['title'] as String?,
        description: data['description'] as String?,
        imagePath: data['image_path'] as String?,
      );

  Map<String, dynamic> toMap() => {
        'title': title,
        'description': description,
        'image_path': imagePath,
      };

  /// `dart:convert`
  ///
  /// Parses the string and returns the resulting Json object as [WideBanner].
  factory WideBanner.fromJson(String data) {
    return WideBanner.fromMap(json.decode(data) as Map<String, dynamic>);
  }

  /// `dart:convert`
  ///
  /// Converts [WideBanner] to a JSON string.
  String toJson() => json.encode(toMap());

  WideBanner copyWith({
    String? title,
    String? description,
    String? imagePath,
  }) {
    return WideBanner(
      title: title ?? this.title,
      description: description ?? this.description,
      imagePath: imagePath ?? this.imagePath,
    );
  }
}