import 'package:laundry_customer/models/wide_banners_model/wide_banner.dart';

class Data {
  List<WideBanner>? wideBanners;

  Data({this.wideBanners});

  @override
  String toString() => 'Data(wideBanners: $wideBanners)';

  factory Data.fromMap(Map<String, dynamic> data) => Data(
        wideBanners: (data['banners'] as List<dynamic>?)
            ?.map((e) => WideBanner.fromMap(e as Map<String, dynamic>))
            .toList(),
      );

  Map<String, dynamic> toMap() => {
        'banners': wideBanners?.map((e) => e.toMap()).toList(),
      };

  Data copyWith({
    List<WideBanner>? wideBanners,
  }) {
    return Data(
      wideBanners: wideBanners ?? this.wideBanners,
    );
  }
}