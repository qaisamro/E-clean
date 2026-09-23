<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum ExpensesType: int
{
    use EnumHelpers;

    case RENT = 1;
    case ELECTRICITY = 2;
    case WATER = 3;
    case CLEANING_MATERIALS = 4;
    case MAINTENANCE = 5;
    case TRANSPORTATION = 6;
    case SALARY = 7;
    case OTHERS = 8;

    public function label(): string
    {
        return match ($this) {
            self::RENT => 'إيجار',
            self::ELECTRICITY => 'كهرباء',
            self::WATER => 'مياه',
            self::CLEANING_MATERIALS => 'مواد تنظيف',
            self::MAINTENANCE => 'صيانة',
            self::TRANSPORTATION => 'مواصلات',
            self::SALARY => 'رواتب',
            self::OTHERS => 'مصاريف أخرى'
        };
    }
}
