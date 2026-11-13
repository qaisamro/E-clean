<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum StockMovementType: string
{
    use EnumHelpers;

    case INITIAL = 'initial';
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case CONSUME = 'consume';
    case ADJUST_IN = 'adjust_in';
    case ADJUST_OUT = 'adjust_out';
    case DAMAGE = 'damage';
    case RETURN_CUSTOMER = 'return_customer';
    case RETURN_SUPPLIER = 'return_supplier';

    public function label(): string
    {
        return match ($this) {
            self::INITIAL => 'رصيد افتتاحي',
            self::PURCHASE => 'شراء (وارد)',
            self::SALE => 'بيع (كاشير)',
            self::CONSUME => 'استهلاك داخلي',
            self::ADJUST_IN => 'تسوية / جرد (وارد)',
            self::ADJUST_OUT => 'تسوية / جرد (صارف)',
            self::DAMAGE => 'تالف',
            self::RETURN_CUSTOMER => 'مرتجع من عميل',
            self::RETURN_SUPPLIER => 'مرتجع إلى مورد',
        };
    }

    public function isDeduction(): bool
    {
        return match ($this) {
            self::SALE, self::CONSUME, self::ADJUST_OUT, self::DAMAGE, self::RETURN_SUPPLIER => true,
            default => false,
        };
    }

    /** Types that may never push a stock balance negative */
    public function requiresStock(): bool
    {
        return $this->isDeduction();
    }
}
