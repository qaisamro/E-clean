<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum Roles :int
{
    use EnumHelpers;

    case SUPER_ADMIN = 1;
    case ADMIN = 2;
    case EMPLOYEE = 3;
    case CASHIER = 4;

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'مدير التطبيق',
            self::ADMIN => 'مدير',
            self::EMPLOYEE => 'موظف',
            self::CASHIER => 'كاشير'
        };
    }
}
