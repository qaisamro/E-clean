<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum OrderStatus :int
{
    use EnumHelpers;

    case NEW = 1;
    case RECEIVED = 2;
    case UNDER_CLEANING = 3;
    case UNDER_IRONING = 4;
    case READY = 5;
    case OUT_FOR_DELIVERY = 6;
    case DELIVERED = 7;
    case CANCELLED = 8;

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'جديد',
            self::RECEIVED => 'تم الاستلام',
            self::UNDER_CLEANING => 'قيد التنظيف',
            self::UNDER_IRONING => 'قيد الكي',
            self::READY => 'جاهز',
            self::OUT_FOR_DELIVERY => 'خرج للتسليم',
            self::DELIVERED => 'تم التسليم',
            self::CANCELLED => 'ملغي'
        };
    }
}
