import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:laundry_customer/constants/app_colors.dart';

/// Premier branded loading indicator with a rotating gradient arc and pulsing brand icon.
class AppLoadingIndicator extends StatefulWidget {
  const AppLoadingIndicator({
    super.key,
    this.size = 56,
    this.strokeWidth = 3.5,
    this.showText = false,
    this.caption,
  });

  final double size;
  final double strokeWidth;
  final bool showText;
  final String? caption;

  @override
  State<AppLoadingIndicator> createState() => _AppLoadingIndicatorState();
}

class _AppLoadingIndicatorState extends State<AppLoadingIndicator>
    with TickerProviderStateMixin {
  late final AnimationController _spinController;
  late final AnimationController _pulseController;
  late final Animation<double> _pulseAnimation;

  @override
  void initState() {
    super.initState();
    _spinController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    )..repeat();

    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    )..repeat(reverse: true);

    _pulseAnimation = Tween<double>(begin: 0.93, end: 1.05).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _spinController.dispose();
    _pulseController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final double s = widget.size;
    final double innerSize = s * 0.58;

    return Column(
      mainAxisSize: MainAxisSize.min,
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        SizedBox(
          width: s,
          height: s,
          child: Stack(
            alignment: Alignment.center,
            children: [
              // Spinning gradient arc
              AnimatedBuilder(
                animation: _spinController,
                builder: (context, child) {
                  return Transform.rotate(
                    angle: _spinController.value * 2 * math.pi,
                    child: CustomPaint(
                      size: Size(s, s),
                      painter: _GradientSpinnerPainter(
                        strokeWidth: widget.strokeWidth,
                      ),
                    ),
                  );
                },
              ),
              // Pulsing central logo badge
              ScaleTransition(
                scale: _pulseAnimation,
                child: Container(
                  width: innerSize,
                  height: innerSize,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: Colors.white,
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFF028090).withValues(alpha: 0.20),
                        blurRadius: 10,
                        offset: const Offset(0, 3),
                      ),
                    ],
                  ),
                  padding: EdgeInsets.all(innerSize * 0.16),
                  child: Image.asset(
                    'assets/images/app_icon.png',
                    fit: BoxFit.contain,
                  ),
                ),
              ),
            ],
          ),
        ),
        if (widget.showText) ...[
          SizedBox(height: 14.h),
          Text(
            widget.caption ?? 'جاري التحميل...',
            style: TextStyle(
              fontFamily: 'Cairo',
              fontSize: 12.5.sp,
              fontWeight: FontWeight.w700,
              color: const Color(0xFF0B1E2E),
            ),
          ),
          SizedBox(height: 6.h),
          _AnimatedDots(controller: _spinController),
        ],
      ],
    );
  }
}

class _GradientSpinnerPainter extends CustomPainter {
  const _GradientSpinnerPainter({this.strokeWidth = 3.5});
  final double strokeWidth;

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = (math.min(size.width, size.height) - strokeWidth) / 2;

    // Track circle
    final trackPaint = Paint()
      ..color = const Color(0xFF028090).withValues(alpha: 0.10)
      ..style = PaintingStyle.stroke
      ..strokeWidth = strokeWidth;
    canvas.drawCircle(center, radius, trackPaint);

    // Dynamic gradient arc
    final rect = Rect.fromCircle(center: center, radius: radius);
    const gradient = SweepGradient(
      colors: [
        Color(0x00028090),
        Color(0x44028090),
        Color(0xFF00A896),
        Color(0xFF028090),
      ],
      stops: [0.0, 0.35, 0.75, 1.0],
    );

    final arcPaint = Paint()
      ..shader = gradient.createShader(rect)
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round
      ..strokeWidth = strokeWidth;

    canvas.drawArc(rect, 0, math.pi * 1.55, false, arcPaint);
  }

  @override
  bool shouldRepaint(covariant _GradientSpinnerPainter oldDelegate) =>
      oldDelegate.strokeWidth != strokeWidth;
}

class _AnimatedDots extends StatelessWidget {
  const _AnimatedDots({required this.controller});
  final AnimationController controller;

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: controller,
      builder: (context, _) {
        final double v = controller.value;
        final int activeIndex = (v * 3).floor() % 3;
        return Row(
          mainAxisSize: MainAxisSize.min,
          children: List.generate(3, (index) {
            final bool isActive = index == activeIndex;
            return AnimatedContainer(
              duration: const Duration(milliseconds: 250),
              margin: EdgeInsets.symmetric(horizontal: 2.5.w),
              width: isActive ? 14.w : 6.w,
              height: 5.h,
              decoration: BoxDecoration(
                color: isActive
                    ? const Color(0xFF028090)
                    : const Color(0xFF028090).withValues(alpha: 0.25),
                borderRadius: BorderRadius.circular(3.r),
              ),
            );
          }),
        );
      },
    );
  }
}

/// The updated, high-end BusyLoader used across screens for inline & modal loading.
class BusyLoader extends StatelessWidget {
  const BusyLoader({
    super.key,
    this.size = 130,
    this.showbackground = false,
    this.message,
    this.showText = true,
  });

  final double size;
  final bool showbackground;
  final String? message;
  final bool showText;

  @override
  Widget build(BuildContext context) {
    final double indicatorSize = (size * 0.55).clamp(44.0, 84.0);

    final content = AppLoadingIndicator(
      size: indicatorSize,
      showText: showText,
      caption: message,
    );

    if (!showbackground) {
      return Center(child: content);
    }

    return Center(
      child: Container(
        padding: EdgeInsets.symmetric(horizontal: 28.w, vertical: 24.h),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(22.r),
          border: Border.all(color: const Color(0xFFE2E8F0)),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF0B1E2E).withValues(alpha: 0.08),
              blurRadius: 28,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: content,
      ),
    );
  }
}

// Backward compatibility alias
class AniMatedItem extends StatelessWidget {
  const AniMatedItem({
    super.key,
    required this.active,
    required this.size,
  });

  final bool active;
  final double size;

  @override
  Widget build(BuildContext context) {
    return AnimatedContainer(
      height: active ? size * 0.3 : size * 0.1,
      width: size * 0.1,
      decoration: BoxDecoration(
        color: AppColors.primary,
        borderRadius: BorderRadius.circular(size * 0.05),
      ),
      duration: const Duration(milliseconds: 500),
    );
  }
}
