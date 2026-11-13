import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

/// Monochrome laundry-hamper glyph (basket outline with handle and slots).
/// Rendered and tinted via [color] so the same asset works for the active
/// (teal) and inactive (slate) states everywhere — bottom navigation, cart
/// header and cart empty states.
class LaundryBasketIcon extends StatelessWidget {
  const LaundryBasketIcon({super.key, this.size = 24, this.color});

  final double size;
  final Color? color;

  @override
  Widget build(BuildContext context) {
    return SvgPicture.asset(
      'assets/svgs/icon_laundry_basket.svg',
      width: size,
      height: size,
      colorFilter:
          color == null ? null : ColorFilter.mode(color!, BlendMode.srcIn),
    );
  }
}
