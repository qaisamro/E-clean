<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use App\Models\NotificationManage;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusNotificationService;
use Illuminate\Support\Facades\Hash;
use Tests\Support\RefreshCityDatabase;
use Tests\TestCase;

class OrderStatusNotificationServiceTest extends TestCase
{
    use RefreshCityDatabase;

    public function test_status_notification_is_saved_for_customer_without_device_token(): void
    {
        $user = User::create([
            'first_name' => 'Notification',
            'last_name' => 'Customer',
            'mobile' => '0599000000',
            'email' => 'notification-customer@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $customer = Customer::create(['user_id' => $user->id]);
        $address = Address::create([
            'customer_id' => $customer->id,
            'address_name' => 'Home',
            'area' => 'Dura',
        ]);
        $order = Order::create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_code' => 'STATUS-NOTIFY-1',
            'prefix' => 'LM',
            'pick_date' => today()->toDateString(),
            'delivery_date' => today()->addDay()->toDateString(),
            'pick_hour' => '08:00:00',
            'delivery_hour' => '08:00:00',
            'amount' => 20,
            'total_amount' => 20,
            'payment_status' => config('enums.payment_status.pending'),
            'order_status' => config('enums.order_status.delivered'),
            'payment_type' => config('enums.payment_types.cash_on_delivery'),
        ]);
        NotificationManage::create([
            'name' => 'order_delivered',
            'title' => 'تم توصيل الطلب',
            'message' => 'تم توصيل طلبك بنجاح',
            'is_active' => true,
        ]);

        $result = app(OrderStatusNotificationService::class)->notify($order, 'delivered');

        $this->assertSame('stored', $result['status']);
        $this->assertSame('missing_device_token', $result['push']['reason']);
        $this->assertDatabaseHas('notifications', [
            'customer_id' => $customer->id,
            'title' => 'تم توصيل الطلب',
            'message' => 'تم توصيل طلبك بنجاح',
            'isRead' => 0,
        ]);
    }

    public function test_explicitly_disabled_status_template_is_respected(): void
    {
        $user = User::create([
            'first_name' => 'Disabled',
            'last_name' => 'Customer',
            'mobile' => '0599000001',
            'email' => 'disabled-notification@example.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $customer = Customer::create(['user_id' => $user->id]);
        $address = Address::create([
            'customer_id' => $customer->id,
            'address_name' => 'Home',
            'area' => 'Dura',
        ]);
        $order = Order::create([
            'customer_id' => $customer->id,
            'address_id' => $address->id,
            'order_code' => 'STATUS-NOTIFY-2',
            'prefix' => 'LM',
            'pick_date' => today()->toDateString(),
            'delivery_date' => today()->addDay()->toDateString(),
            'pick_hour' => '08:00:00',
            'delivery_hour' => '08:00:00',
            'amount' => 20,
            'total_amount' => 20,
            'payment_status' => config('enums.payment_status.pending'),
            'order_status' => config('enums.order_status.delivered'),
            'payment_type' => config('enums.payment_types.cash_on_delivery'),
        ]);
        NotificationManage::create([
            'name' => 'order_delivered',
            'title' => 'تم توصيل الطلب',
            'message' => 'تم توصيل طلبك بنجاح',
            'is_active' => false,
        ]);

        $result = app(OrderStatusNotificationService::class)->notify($order, 'delivered');

        $this->assertSame('skipped', $result['status']);
        $this->assertSame('disabled', $result['reason']);
        $this->assertDatabaseMissing('notifications', ['customer_id' => $customer->id]);
    }
}
