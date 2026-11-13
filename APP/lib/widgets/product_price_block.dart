import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';

/// Modern price block for product cards: current price in bold teal, with an
/// optional old price shown with a red strikethrough and a small «خصم» badge
/// carrying the saving amount. Shared by the store page and the service
/// items list so every product card looks identical.
class ProductPriceBlock extends StatelessWidget {
  const ProductPriceBlock({
    super.key,
    required this.currency,
    required this.currentPrice,
    this.oldPrice,
    this.compact = false,
  });

  final String currency;
  final double currentPrice;
  final double? oldPrice;

  /// Tighter variant for very tight rows.
  final bool compact;

  @override
  Widget build(BuildContext context) {
    final double? old = oldPrice;
    final bool discounted = old != null && old > currentPrice;
    final double diff = discounted ? old - currentPrice : 0;

    return Wrap(
      crossAxisAlignment: WrapCrossAlignment.center,
      spacing: 6.w,
      runSpacing: 4.h,
      children: [
        if (discounted)
          Text(
            '$currency${old.toStringAsFixed(2)}',
            style: TextStyle(
              fontSize: (compact ? 10.5 : 11).sp,
              color: const Color(0xFFF43F5E),
              decoration: TextDecoration.lineThrough,
              decorationColor: const Color(0xFFF43F5E),
              decorationThickness: 1.6,
              fontFamily: 'Cairo',
            ),
          ),
        Text(
          '$currency${currentPrice.toStringAsFixed(2)}',
          style: TextStyle(
            fontSize: (compact ? 12.5 : 14).sp,
            fontWeight: FontWeight.w900,
            color: const Color(0xFF028090),
            fontFamily: 'Cairo',
          ),
        ),
        if (discounted)
          Container(
            padding: EdgeInsets.symmetric(horizontal: 6.w, vertical: 2.h),
            decoration: BoxDecoration(
              color: const Color(0xFFFFE4E6),
              borderRadius: BorderRadius.circular(20.r),
              border: Border.all(color: const Color(0xFFFCA5A5)),
            ),
            child: Text(
              'خصم ${diff.toStringAsFixed(2)}',
              style: TextStyle(
                fontSize: 9.sp,
                fontWeight: FontWeight.w900,
                color: const Color(0xFFE11D48),
                fontFamily: 'Cairo',
              ),
            ),
          ),
      ],
    );
  }
}
