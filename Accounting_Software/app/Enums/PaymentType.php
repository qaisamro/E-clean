<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum PaymentType: int
{
    use EnumHelpers;

    case CASH = 1;
    case DEFERRED = 2;
    case VISA = 3;
    case PARTIAL = 4;

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'نقدي',
            self::DEFERRED => 'آجل / على الحساب',
            self::VISA => 'فيزا',
            self::PARTIAL => 'دفع جزئي',
        };
    }
}
