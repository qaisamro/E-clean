<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum Roles :int
{
    use EnumHelpers;

    case SUPER_ADMIN = 1;
    case ADMIN = 2;

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'مطور النظام',
            self::ADMIN => 'مدير',
        };
    }
}
