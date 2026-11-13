import 'dart:async';
import 'dart:math' as math;

import 'package:confetti/confetti.dart';
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:laundry_customer/constants/app_colors.dart';
import 'package:laundry_customer/constants/app_text_decor.dart';
import 'package:laundry_customer/constants/hive_contants.dart';
import 'package:laundry_customer/utils/context_less_nav.dart';
import 'package:laundry_customer/utils/routes.dart';

/// Order-placed confirmation.
///
/// Online card payment was removed from the app, so every order is cash on
/// delivery: the order is confirmed the moment it is placed.
///
/// The success screen is fully code-drawn (no illustration asset): a gradient
/// hero with an animated check badge, celebratory confetti, and a clean order
/// summary card.
class OrderSuccessScreen extends StatefulWidget {
  const OrderSuccessScreen({super.key, required this.details});
  final Map<String, dynamic> details;

  @override
  State<OrderSuccessScreen> createState() => _OrderSuccessScreenState();
}

class _OrderSuccessScreenState extends State<OrderSuccessScreen>
    with TickerProviderStateMixin {
  late final AnimationController _entrance;
  late final AnimationController _pulse;
  late final ConfettiController _confetti;
  Timer? _confettiTimer;

  late final Animation<double> _badgeScale;
  late final Animation<double> _fade;
  late final Animation<Offset> _slide;
  late final Animation<double> _cardFade;
  late final Animation<Offset> _cardSlide;

  @override
  void initState() {
    super.initState();

    _entrance = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    );
    _pulse = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 2400),
    )..repeat(reverse: true);
    _confetti = ConfettiController(
      duration: const Duration(milliseconds: 2400),
    );

    _badgeScale = Tween<double>(begin: 0.25, end: 1).animate(
      CurvedAnimation(
        parent: _entrance,
        curve: const Interval(0.05, 0.75, curve: Curves.elasticOut),
      ),
    );
    _fade = CurvedAnimation(
      parent: _entrance,
      curve: const Interval(0.15, 0.7, curve: Curves.easeOut),
    );
    _slide = Tween<Offset>(
      begin: const Offset(0, 0.16),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(
        parent: _entrance,
        curve: const Interval(0.15, 0.8, curve: Curves.easeOutCubic),
      ),
    );
    _cardFade = CurvedAnimation(
      parent: _entrance,
      curve: const Interval(0.4, 1, curve: Curves.easeOut),
    );
    _cardSlide = Tween<Offset>(
      begin: const Offset(0, 0.12),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(
        parent: _entrance,
        curve: const Interval(0.4, 1, curve: Curves.easeOutCubic),
      ),
    );

    _entrance.forward();
    _confettiTimer = Timer(const Duration(milliseconds: 250), () {
      if (mounted) _confetti.play();
    });
  }

  @override
  void dispose() {
    _confettiTimer?.cancel();
    _confetti.dispose();
    _pulse.dispose();
    _entrance.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final String rawId = '${widget.details['id'] ?? ''}'.trim();
    final String orderId = rawId.isEmpty ? '—' : rawId;
    final String amount = '${widget.details['amount'] ?? '0.00'}';
    final String currency =
        Hive.box(AppHSC.appSettingsBox).get('currency')?.toString() ?? '\$';

    return Scaffold(
      backgroundColor: AppColors.brandCream,
      body: Stack(
        children: [
          // Soft gradient hero that sits behind the top half of the content.
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            height: 400.h,
            child: Container(
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    AppColors.brandTealDark,
                    AppColors.brandTeal,
                    AppColors.brandDeepTeal,
                  ],
                  stops: [0, 0.55, 1],
                ),
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(38.r),
                  bottomRight: Radius.circular(38.r),
                ),
              ),
              child: Stack(
                children: [
                  Positioned(
                    top: -70.h,
                    right: -50.w,
                    child:
                        _glowCircle(220.w, Colors.white.withValues(alpha: .07)),
                  ),
                  Positioned(
                    bottom: 30.h,
                    left: -70.w,
                    child:
                        _glowCircle(190.w, Colors.white.withValues(alpha: .05)),
                  ),
                  Positioned(
                    top: 90.h,
                    left: 28.w,
                    child: _glowCircle(
                      70.w,
                      AppColors.brandAmber.withValues(alpha: .12),
                    ),
                  ),
                ],
              ),
            ),
          ),
          SafeArea(
            child: LayoutBuilder(
              builder: (context, constraints) {
                return SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  padding: EdgeInsets.fromLTRB(22.w, 4.h, 22.w, 22.h),
                  child: ConstrainedBox(
                    constraints: BoxConstraints(
                      minHeight: constraints.maxHeight - 26.h,
                    ),
                    child: Column(
                      children: [
                        _buildHero(),
                        SizedBox(height: 8.h),
                        FadeTransition(
                          opacity: _fade,
                          child: SlideTransition(
                            position: _slide,
                            child: Column(
                              children: [
                                Text(
                                  'تم تقديم طلبك بنجاح',
                                  textAlign: TextAlign.center,
                                  style: AppTextDecor.osBold24black.copyWith(
                                    color: Colors.white,
                                    fontSize: 23.sp,
                                    fontWeight: FontWeight.w800,
                                    height: 1.3,
                                  ),
                                ),
                                SizedBox(height: 10.h),
                                Text(
                                  'شكراً لثقتك بنا! تم استلام طلبك\nوسنبدأ بتجهيزه في أقرب وقت ممكن.',
                                  textAlign: TextAlign.center,
                                  style: AppTextDecor.osRegular14black.copyWith(
                                    color: Colors.white.withValues(alpha: .78),
                                    fontSize: 13.sp,
                                    height: 1.7,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                        SizedBox(height: 26.h),
                        FadeTransition(
                          opacity: _cardFade,
                          child: SlideTransition(
                            position: _cardSlide,
                            child: _buildSummaryCard(
                              context,
                              orderId: orderId,
                              amount: amount,
                              currency: currency,
                            ),
                          ),
                        ),
                        SizedBox(height: 16.h),
                        FadeTransition(
                          opacity: _cardFade,
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.verified_user_outlined,
                                size: 15.sp,
                                color: AppColors.gray,
                              ),
                              SizedBox(width: 6.w),
                              Text(
                                'يمكنك متابعة حالة طلبك من صفحة طلباتي',
                                style: AppTextDecor.osRegular12black.copyWith(
                                  color: AppColors.gray,
                                  fontSize: 11.sp,
                                ),
                              ),
                            ],
                          ),
                        ),
                        SizedBox(height: 26.h),
                        FadeTransition(
                          opacity: _cardFade,
                          child: SlideTransition(
                            position: _cardSlide,
                            child: _buildHomeButton(context),
                          ),
                        ),
                        SizedBox(height: 16.h),
                        FadeTransition(
                          opacity: _cardFade,
                          child: Text(
                            'E-Clean · شكراً لثقتك بنا',
                            style: AppTextDecor.osRegular12black.copyWith(
                              color: AppColors.gray.withValues(alpha: .8),
                              fontSize: 10.5.sp,
                              letterSpacing: .3,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
          Align(
            alignment: Alignment.topCenter,
            child: IgnorePointer(
              child: ConfettiWidget(
                confettiController: _confetti,
                blastDirectionality: BlastDirectionality.explosive,
                numberOfParticles: 32,
                emissionFrequency: 0.06,
                gravity: 0.28,
                maxBlastForce: 32,
                minBlastForce: 12,
                colors: const [
                  Color(0xFF7FD1DD),
                  Color(0xFFF59E0B),
                  Color(0xFFFFB703),
                  Color(0xFF4FC3F7),
                  Color(0xFFFF8A80),
                  Colors.white,
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _glowCircle(double size, Color color) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(shape: BoxShape.circle, color: color),
    );
  }

  Widget _buildHero() {
    return SizedBox(
      height: 208.h,
      child: AnimatedBuilder(
        animation: _pulse,
        builder: (context, _) {
          final double phase = _pulse.value;
          final double ringOne = 0.94 + (phase * 0.14);
          final double ringTwo = 1.04 + (((phase + 0.4) % 1) * 0.16);

          return Stack(
            alignment: Alignment.center,
            children: [
              Container(
                width: 224.w,
                height: 224.w,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: RadialGradient(
                    colors: [
                      Colors.white.withValues(alpha: .14),
                      Colors.white.withValues(alpha: 0),
                    ],
                  ),
                ),
              ),
              Transform.scale(
                scale: ringOne,
                child: Container(
                  width: 164.w,
                  height: 164.w,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: Colors.white.withValues(alpha: .20),
                      width: 1.4,
                    ),
                  ),
                ),
              ),
              Transform.scale(
                scale: ringTwo,
                child: Container(
                  width: 164.w,
                  height: 164.w,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: Colors.white.withValues(alpha: .09),
                    ),
                  ),
                ),
              ),
              ScaleTransition(
                scale: _badgeScale,
                child: Container(
                  width: 128.w,
                  height: 128.w,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: const LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [Colors.white, Color(0xFFEAF7F9)],
                    ),
                    boxShadow: [
                      BoxShadow(
                        color: AppColors.brandNavy.withValues(alpha: .35),
                        blurRadius: 34,
                        offset: const Offset(0, 16),
                        spreadRadius: 2,
                      ),
                    ],
                  ),
                  child: Center(
                    child: Container(
                      width: 96.w,
                      height: 96.w,
                      decoration: const BoxDecoration(
                        shape: BoxShape.circle,
                        gradient: LinearGradient(
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                          colors: [
                            AppColors.brandTealDark,
                            AppColors.brandDeepTeal,
                          ],
                        ),
                      ),
                      child: Icon(
                        Icons.check_rounded,
                        color: Colors.white,
                        size: 56.sp,
                      ),
                    ),
                  ),
                ),
              ),
              Positioned(
                top: 36.h,
                right: 56.w,
                child: Transform.scale(
                  scale: 0.9 + (phase * 0.2),
                  child: Container(
                    width: 32.w,
                    height: 32.w,
                    decoration: const BoxDecoration(
                      shape: BoxShape.circle,
                      color: AppColors.brandAmber,
                      boxShadow: [
                        BoxShadow(
                          color: Color(0x55F59E0B),
                          blurRadius: 16,
                          offset: Offset(0, 6),
                        ),
                      ],
                    ),
                    child: Icon(
                      Icons.star_rounded,
                      color: Colors.white,
                      size: 19.sp,
                    ),
                  ),
                ),
              ),
              _sparkle(
                top: 44.h,
                left: 62.w,
                size: 17.sp,
                color: Colors.white,
                phase: 0.1,
              ),
              _sparkle(
                top: 148.h,
                left: 34.w,
                size: 12.sp,
                color: AppColors.brandAmber,
                phase: 0.45,
              ),
              _sparkle(
                bottom: 34.h,
                right: 58.w,
                size: 13.sp,
                color: Colors.white,
                phase: 0.7,
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _sparkle({
    double? top,
    double? left,
    double? right,
    double? bottom,
    required double size,
    required Color color,
    double phase = 0,
  }) {
    final double wave =
        (math.sin((_pulse.value + phase) * math.pi * 2) + 1) / 2;
    return Positioned(
      top: top,
      left: left,
      right: right,
      bottom: bottom,
      child: Opacity(
        opacity: 0.25 + (wave * 0.7),
        child: Transform.rotate(
          angle: phase * math.pi,
          child: Icon(Icons.auto_awesome_rounded, size: size, color: color),
        ),
      ),
    );
  }

  Widget _buildSummaryCard(
    BuildContext context, {
    required String orderId,
    required String amount,
    required String currency,
  }) {
    return Container(
      width: double.infinity,
      padding: EdgeInsets.fromLTRB(18.w, 18.h, 18.w, 16.h),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24.r),
        border: Border.all(color: Colors.white, width: 1.5),
        boxShadow: [
          BoxShadow(
            color: AppColors.brandNavy.withValues(alpha: .10),
            blurRadius: 30,
            offset: const Offset(0, 14),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 34.w,
                height: 34.w,
                decoration: BoxDecoration(
                  color: AppColors.brandCream,
                  borderRadius: BorderRadius.circular(10.r),
                ),
                child: Icon(
                  Icons.receipt_long_rounded,
                  color: AppColors.brandTeal,
                  size: 19.sp,
                ),
              ),
              SizedBox(width: 10.w),
              Text(
                'تفاصيل الطلب',
                style: AppTextDecor.osSemiBold14black.copyWith(
                  color: AppColors.brandNavy,
                  fontSize: 14.sp,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
          SizedBox(height: 16.h),
          _infoRow(
            label: 'رقم الطلب',
            value: Text(
              '$orderId',
              textDirection: TextDirection.ltr,
              style: AppTextDecor.osBold14black.copyWith(
                color: AppColors.brandTeal,
                fontSize: 14.sp,
                fontWeight: FontWeight.w800,
                letterSpacing: .4,
              ),
            ),
          ),
          _dashedDivider(),
          _infoRow(
            label: 'الإجمالي',
            value: Text(
              '$currency $amount',
              style: AppTextDecor.osBold20Black.copyWith(
                color: AppColors.brandNavy,
                fontSize: 20.sp,
                fontWeight: FontWeight.w800,
              ),
            ),
          ),
          SizedBox(height: 14.h),
          Container(
            padding: EdgeInsets.symmetric(horizontal: 12.w, vertical: 11.h),
            decoration: BoxDecoration(
              color: AppColors.brandCream,
              borderRadius: BorderRadius.circular(13.r),
              border: Border.all(color: AppColors.brandCreamLight),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.payments_outlined,
                  size: 18.sp,
                  color: AppColors.brandTeal,
                ),
                SizedBox(width: 8.w),
                Expanded(
                  child: Text(
                    'الدفع عند الاستلام',
                    style: AppTextDecor.osSemiBold12black.copyWith(
                      color: AppColors.brandDeepTeal,
                      fontSize: 12.sp,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
                Container(
                  padding: EdgeInsets.symmetric(
                    horizontal: 8.w,
                    vertical: 3.h,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFFF3DE),
                    borderRadius: BorderRadius.circular(20.r),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.schedule_rounded,
                        size: 12.sp,
                        color: const Color(0xFFC27A00),
                      ),
                      SizedBox(width: 3.w),
                      Text(
                        'عند التسليم',
                        style: AppTextDecor.osSemiBold10black.copyWith(
                          color: const Color(0xFFC27A00),
                          fontSize: 10.sp,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _infoRow({required String label, required Widget value}) {
    return Row(
      children: [
        Text(
          label,
          style: AppTextDecor.osRegular12black.copyWith(
            color: AppColors.gray,
            fontSize: 12.sp,
            fontWeight: FontWeight.w500,
          ),
        ),
        const Spacer(),
        value,
      ],
    );
  }

  Widget _dashedDivider() {
    return Padding(
      padding: EdgeInsets.symmetric(vertical: 14.h),
      child: LayoutBuilder(
        builder: (context, constraints) {
          final int count = (constraints.maxWidth / 8).floor();
          return Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: List.generate(
              count,
              (_) => Container(
                width: 4.w,
                height: 1.2,
                decoration: BoxDecoration(
                  color: const Color(0xFFE4EEF1),
                  borderRadius: BorderRadius.circular(2.r),
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildHomeButton(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      height: 56.h,
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(17.r),
          onTap: () => context.nav.pushNamedAndRemoveUntil(
            Routes.homeScreen,
            (route) => false,
          ),
          child: Ink(
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [AppColors.brandTeal, AppColors.brandTealDark],
              ),
              borderRadius: BorderRadius.circular(17.r),
              boxShadow: [
                BoxShadow(
                  color: AppColors.brandTeal.withValues(alpha: .30),
                  blurRadius: 20,
                  offset: const Offset(0, 10),
                ),
              ],
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.home_rounded, color: Colors.white, size: 21.sp),
                SizedBox(width: 9.w),
                Text(
                  'العودة إلى الرئيسية',
                  style: AppTextDecor.osBold14white.copyWith(
                    color: Colors.white,
                    fontSize: 15.sp,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
