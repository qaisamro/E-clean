<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum UserStatus :int
{
    use EnumHelpers;

    case ACTIVE = 1;
    case SUSPENDED = 2;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'نشط',
            self::SUSPENDED => 'موقوف',
        };
    }
}
