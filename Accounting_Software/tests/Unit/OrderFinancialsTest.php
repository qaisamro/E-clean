<?php

namespace Tests\Unit;

use App\Enums\PaymentStatus;
use App\Services\OrderFinancials;
use DomainException;
use PHPUnit\Framework\TestCase;

class OrderFinancialsTest extends TestCase
{
    public function test_it_calculates_discounted_paid_order(): void
    {
        $result = (new OrderFinancials())->calculate(80, 30, 10, 100);

        $this->assertSame(100.0, $result['total']);
        $this->assertSame(0.0, $result['remaining']);
        $this->assertSame(PaymentStatus::PAID, $result['status']);
    }

    public function test_it_calculates_partial_payment_from_the_ledger(): void
    {
        $result = (new OrderFinancials())->calculate(80, 20, 0, 25);

        $this->assertSame(75.0, $result['remaining']);
        $this->assertSame(PaymentStatus::PARTIALLY_PAID, $result['status']);
    }

    public function test_it_rejects_an_overpayment_or_invalid_discount(): void
    {
        $service = new OrderFinancials();

        $this->expectException(DomainException::class);
        $service->calculate(20, 0, 0, 20.01);
    }
}
