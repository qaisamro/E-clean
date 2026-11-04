<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum PaymentStatus: int
{
    use EnumHelpers;

    case PAID = 1;
    case UNPAID = 2;
    case PARTIALLY_PAID = 3;

    public function label(): string
    {
        return match ($this) {
            self::PAID => 'مدفوع بالكامل',
            self::UNPAID => 'غير مدفوع',
            self::PARTIALLY_PAID => 'مدفوع جزئيًا'
        };
    }
}
