import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';

/// Cash-on-delivery payment tile — icon-only redesign.
/// No images, no logos; teal identity with a clear selected state.
class PaymentMethodCard extends StatelessWidget {
  const PaymentMethodCard({
    super.key,
    this.isSelected = false,
    this.onTap,
  });

  final bool isSelected;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(20.r),
        onTap: onTap,
        child: Ink(
          decoration: BoxDecoration(
            color: const Color(0xFFEFFAF8),
            borderRadius: BorderRadius.circular(20.r),
            border: Border.all(
              color:
                  isSelected ? const Color(0xFF028090) : const Color(0xFFE2E8F0),
              width: isSelected ? 1.8 : 1.2,
            ),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFF028090).withOpacity(isSelected ? 0.14 : 0.05),
                blurRadius: 14,
                offset: const Offset(0, 6),
              ),
            ],
          ),
          padding: EdgeInsets.all(12.w),
          child: Row(
            children: [
              // Leading artwork built with coins — no image assets.
              Container(
                width: 52.w,
                height: 52.h,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: LinearGradient(
                    colors: isSelected
                        ? const [Color(0xFF028090), Color(0xFF00A896)]
                        : const [Color(0xFFE2E8F0), Color(0xFFCBD5E1)],
                    begin: Alignment.topRight,
                    end: Alignment.bottomLeft,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF028090).withOpacity(0.25),
                      blurRadius: 10,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Icon(
                  Icons.payments_rounded,
                  size: 25.sp,
                  color: isSelected ? Colors.white : const Color(0xFF64748B),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'الدفع عند الاستلام',
                      style: TextStyle(
                        fontSize: 14.sp,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xFF0B1E2E),
                        fontFamily: 'Cairo',
                      ),
                    ),
                    const SizedBox(height: 3),
                    Text(
                      'ادفع للسائق عند استلام طلبك — بدون أي مبلغ مسبق',
                      style: TextStyle(
                        fontSize: 11.sp,
                        fontWeight: FontWeight.w600,
                        color: const Color(0xFF475569),
                        height: 1.35,
                        fontFamily: 'Cairo',
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 8),
              // Styled radio check — visible at all times, filled when selected.
              Container(
                width: 26.w,
                height: 26.h,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: isSelected
                      ? const LinearGradient(
                          colors: [Color(0xFF028090), Color(0xFF00A896)],
                          begin: Alignment.topRight,
                          end: Alignment.bottomLeft)
                      : null,
                  border: Border.all(
                    color: isSelected
                        ? Colors.transparent
                        : const Color(0xFFCBD5E1),
                    width: 2,
                  ),
                  boxShadow: isSelected
                      ? [
                          BoxShadow(
                            color: const Color(0xFF028090).withOpacity(0.35),
                            blurRadius: 8,
                            offset: const Offset(0, 3),
                          ),
                        ]
                      : null,
                ),
                alignment: Alignment.center,
                child: isSelected
                    ? const Icon(Icons.check_rounded,
                        size: 16, color: Colors.white)
                    : null,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
