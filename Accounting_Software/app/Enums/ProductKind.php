<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum ProductKind: string
{
    use EnumHelpers;

    case RETAIL = 'retail';
    case SUPPLY = 'supply';
    case LAUNDRY = 'laundry';

    public function label(): string
    {
        return match ($this) {
            self::RETAIL => 'منتج للبيع',
            self::SUPPLY => 'مستلزمات المغسلة',
            self::LAUNDRY => 'صنف المغسلة',
        };
    }
}
