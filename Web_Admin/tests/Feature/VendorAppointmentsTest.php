<?php
namespace Tests\Feature;

use App\Models\{Address, Customer, Order, OrderAppointment, Product, Service, User, Variant, Vendor};
use App\Services\VendorAppointments;
use Carbon\CarbonImmutable as Time;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\Support\RefreshCityDatabase;
use Tests\TestCase;

class VendorAppointmentsTest extends TestCase
{
    use RefreshCityDatabase;
    private Vendor $store;
    private User $owner;
    private User $buyer;
    private Address $address;
    private Product $product;
    private VendorAppointments $service;

    protected function setUp(): void
    {
        parent::setUp();
        Time::setTestNow(Time::parse('2026-11-09 07:00', 'Asia/Hebron'));
        $this->service = app(VendorAppointments::class);
        $this->owner = $this->user('vendor', 'vendor_admin');
        $this->store = Vendor::create(['name'=>'متجر دورا', 'slug'=>'appointments-dura', 'user_id'=>$this->owner->id, 'is_active'=>true]);
        $this->buyer = $this->user('buyer', 'customer');
        $customer = Customer::create(['user_id'=>$this->buyer->id]);
        $this->address = Address::create(['customer_id'=>$customer->id, 'address_name'=>'Home']);
        $variant = Variant::create(['name'=>'Regular']);
        $s = Service::create(['name'=>'Laundry', 'vendor_id'=>$this->store->id, 'is_active'=>true]);
        $this->product = Product::create(['name'=>'Shirt', 'service_id'=>$s->id, 'vendor_id'=>$this->store->id,
            'variant_id'=>$variant->id, 'price'=>20, 'discount_price'=>15, 'is_active'=>true]);
        $this->service->save($this->store->id, $this->settings());
        config(['app.stripe_key'=>null, 'app.stripe_secret'=>null]);
    }

    protected function tearDown(): void
    {
        Time::setTestNow();
        parent::tearDown();
    }

    private function user(string $name, string $role): User
    {
        $u = User::create(['first_name'=>$name, 'last_name'=>'Test', 'mobile'=>$name.'-phone',
            'email'=>$name.'@example.test', 'password'=>bcrypt('password'), 'is_active'=>true]);
        $u->assignRole(Role::findOrCreate($role, 'web'));
        return $u;
    }

    private function settings(): array
    {
        $s = $this->service->defaults();
        $s['enabled'] = true;
        $s['processing_minutes'] = 120;
        for ($i=0; $i<7; $i++) {
            $s['weekly']['pickup'][] = ['day'=>$i, 'start'=>'09:00', 'end'=>'11:00', 'capacity'=>1];
            $s['weekly']['delivery'][] = ['day'=>$i, 'start'=>'12:00', 'end'=>'14:00', 'capacity'=>1];
            $s['weekly']['delivery'][] = ['day'=>$i, 'start'=>'15:00', 'end'=>'17:00', 'capacity'=>1];
        }
        return $s;
    }

    private function payload(): array
    {
        return ['vendor_id'=>$this->store->id, 'products'=>[['id'=>$this->product->id, 'quantity'=>2]],
            'pick_date'=>'2026-11-09', 'pick_hour'=>'09:00', 'delivery_date'=>'2026-11-09', 'delivery_hour'=>'15:00',
            'address_id'=>$this->address->id, 'payment_type'=>'cash'];
    }

    private function book(array $changes = [])
    {
        Passport::actingAs($this->buyer);
        return $this->postJson('/api/orders', array_replace($this->payload(), $changes));
    }

    public function test_full_checkout_keeps_prices_address_and_legacy_response(): void
    {
        $before = $this->address->fresh()->getAttributes();
        $response = $this->book()->assertOk()->assertJsonStructure(['data'=>['orders'=>[
            'id','pick_date','pick_hour','delivery_date','delivery_hour','amount','total_amount','delivery_charge','payment_type']]]);
        $order = Order::findOrFail($response->json('data.orders.id'));
        $this->assertEquals(30, $order->amount);
        $this->assertEquals(30, $order->total_amount);
        $this->assertEquals(15, $order->products->first()->pivot->price);
        $this->assertSame($before, $this->address->fresh()->getAttributes());
        $this->assertDatabaseHas('order_appointments', ['order_id'=>$order->id, 'pickup_start'=>'2026-11-09 07:00:00', 'delivery_start'=>'2026-11-09 13:00:00']);
    }

    public function test_scheduling_and_legacy_checkout_have_identical_delivery_fees_and_prices(): void
    {
        \App\Models\DeliveryCost::create(['cost'=>7,'fee_cost'=>100,'minimum_cost'=>0]);
        $this->book()->assertOk()->assertJsonPath('data.orders.amount',30)
            ->assertJsonPath('data.orders.delivery_charge',7)->assertJsonPath('data.orders.total_amount',37);
        $s=$this->settings();$s['enabled']=false;$this->service->save($this->store->id,$s);
        $this->book()->assertOk()->assertJsonPath('data.orders.amount',30)
            ->assertJsonPath('data.orders.delivery_charge',7)->assertJsonPath('data.orders.total_amount',37);
    }

    public function test_capacity_is_checked_again_at_confirmation_and_cancel_releases_it(): void
    {
        $this->getJson('/api/vendors/'.$this->store->id.'/appointments')->assertOk()->assertJsonPath('data.days.0.pickup.0.is_booked', false);
        $this->book()->assertOk();
        $this->book()->assertUnprocessable()->assertJsonValidationErrors('pick_hour');
        $this->assertDatabaseCount('orders', 1);
        $this->getJson('/api/vendors/'.$this->store->id.'/appointments')->assertJsonPath('data.days.0.pickup.0.reason', 'الفترة ممتلئة');
        Order::first()->update(['order_status'=>'Cancelled']);
        $this->book()->assertOk();
    }

    public function test_delivery_must_follow_end_of_pickup_and_processing(): void
    {
        $this->book(['delivery_hour'=>'12:00'])->assertUnprocessable()->assertJsonValidationErrors('delivery_hour');
        $this->getJson('/api/vendors/'.$this->store->id.'/appointments?pick_date=2026-11-09&pick_hour=09:00')
            ->assertOk()->assertJsonPath('data.days.0.delivery.0.reason', 'قبل انتهاء تجهيز الغسيل')
            ->assertJsonPath('data.days.0.delivery.1.is_booked', false);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_holiday_partial_closure_and_expired_slot_are_rejected(): void
    {
        $s = $this->settings(); $s['exceptions'] = [['date'=>'2026-11-09','type'=>'pickup','start'=>'10:00','end'=>'10:30']];
        $this->service->save($this->store->id, $s);
        $this->book()->assertUnprocessable();
        $s['exceptions'] = [['date'=>'2026-11-10','type'=>'both']];
        $this->service->save($this->store->id, $s);
        $this->book(['pick_date'=>'2026-11-10','delivery_date'=>'2026-11-10'])->assertUnprocessable();
        Time::setTestNow(Time::parse('2026-11-09 09:01', 'Asia/Hebron'));
        $this->book()->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_schedule_edits_keep_bookings_and_overlap_still_uses_capacity(): void
    {
        $this->book()->assertOk();
        $order = Order::first(); $before = $order->getAttributes(); $snapshot = OrderAppointment::first()->getAttributes();
        $s = $this->settings(); $s['weekly']['pickup'][1]['start']='10:00'; $s['weekly']['pickup'][1]['end']='12:00';
        $this->service->save($this->store->id, $s);
        $this->assertSame($before, $order->fresh()->getAttributes());
        $this->assertSame($snapshot, OrderAppointment::first()->getAttributes());
        $this->book(['pick_hour'=>'10:00'])->assertUnprocessable();
    }

    public function test_vendor_editor_isolated_and_reschedule_requires_customer_agreement(): void
    {
        $this->book()->assertOk(); $order = Order::first();
        auth('api')->forgetUser();
        $this->actingAs($this->owner, 'web')->get('/vendor/appointments')->assertOk()->assertSee('ap-config')->assertSee('vendor-appointments.js');
        $s = $this->settings();
        $this->postJson('/vendor/appointments', ['settings'=>$s, 'vendor_id'=>999])->assertOk();
        $this->assertDatabaseCount('vendor_appointment_settings', 1);
        $new = ['pick_date'=>'2026-11-10','pick_hour'=>'09:00','delivery_date'=>'2026-11-10','delivery_hour'=>'15:00','reason'=>'اتفاق مع العميل'];
        $this->postJson('/vendor/appointments/orders/'.$order->id, $new)->assertUnprocessable()->assertJsonValidationErrors('customer_agreed');
        $this->post('/vendor/appointments/orders/'.$order->id, $new + ['customer_agreed'=>1])->assertRedirect();
        $this->assertSame('2026-11-10', $order->fresh()->pick_date->format('Y-m-d'));
        $this->assertCount(1, OrderAppointment::first()->reschedule_history);
        $other = $this->user('other', 'vendor_admin');
        Vendor::create(['name'=>'Other','slug'=>'other','user_id'=>$other->id,'is_active'=>true]);
        $this->actingAs($other)->post('/vendor/appointments/orders/'.$order->id, $new + ['customer_agreed'=>1])->assertNotFound();
        $this->actingAs($this->buyer)->get('/vendor/appointments')->assertForbidden();
    }

    public function test_disabled_store_and_no_vendor_schedule_api_keep_legacy_contract(): void
    {
        $s = $this->settings(); $s['enabled']=false; $this->service->save($this->store->id, $s);
        $this->getJson('/api/vendors/'.$this->store->id.'/appointments')->assertOk()->assertJsonPath('data.enabled', false);
        $this->getJson('/api/pick-schedules/2026-11-09')->assertOk()->assertJsonStructure(['data'=>['schedules']]);
        $this->book()->assertOk();
        $this->assertDatabaseCount('order_appointments', 0);
    }

    public function test_bad_dates_and_unknown_or_full_slots_do_not_create_orders(): void
    {
        $this->getJson('/api/vendors/'.$this->store->id.'/appointments?date=2026-02-30')->assertUnprocessable();
        $this->book(['pick_hour'=>'garbage'])->assertUnprocessable();
        $this->book(['pick_hour'=>'09:01'])->assertUnprocessable();
        $this->book(['delivery_date'=>'2026-11-08'])->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_schedule_overlap_is_rejected_and_other_vendors_capacity_is_separate(): void
    {
        $this->book()->assertOk();
        $other = Vendor::create(['name'=>'Other', 'slug'=>'other-capacity', 'user_id'=>$this->owner->id, 'is_active'=>true]);
        $this->service->save($other->id, $this->settings());
        $this->getJson('/api/vendors/'.$other->id.'/appointments')->assertJsonPath('data.days.0.pickup.0.is_booked', false);
        $s = $this->settings(); $s['weekly']['pickup'][]=$s['weekly']['pickup'][0];
        $this->actingAs($this->owner,'web')->postJson('/vendor/appointments', ['settings'=>$s])->assertUnprocessable();
    }

    public function test_legacy_order_consumes_capacity_and_legacy_range_is_supported(): void
    {
        $this->book(['pick_hour'=>'9-10','delivery_hour'=>'15-16'])->assertOk();
        OrderAppointment::query()->delete();
        $this->book()->assertUnprocessable();
    }

    public function test_dst_gap_and_ambiguous_fold_are_not_offered(): void
    {
        foreach (['2026-03-28'=>'02:00', '2026-10-24'=>'01:00'] as $date=>$clock) {
            $day=Time::parse($date, 'Asia/Hebron');
            Time::setTestNow($day->subDay());
            $s=$this->settings(); $s['weekly']['pickup']=[['day'=>$day->dayOfWeek,'start'=>$clock,'end'=>'04:00','capacity'=>1]];
            $this->service->save($this->store->id,$s);
            $this->assertSame([], $this->service->slots($this->store->id,'pickup',$date));
        }
    }

    public function test_delivery_capacity_is_independent_and_invalid_delivery_rolls_back_pickup(): void
    {
        $s=$this->settings(); $s['weekly']['pickup'][1]['capacity']=2;
        $this->service->save($this->store->id,$s);
        $this->book()->assertOk();
        $this->book()->assertUnprocessable()->assertJsonValidationErrors('delivery_hour');
        $this->assertDatabaseCount('orders',1);
        $this->assertDatabaseCount('order_appointments',1);
        $this->book(['delivery_date'=>'2026-11-10'])->assertOk();
    }

    public function test_availability_uses_a_bounded_number_of_queries_and_keeps_old_response_times(): void
    {
        $this->book()->assertOk()->assertJsonPath('data.orders.pick_hour','09:00 - 11:00')
            ->assertJsonPath('data.orders.delivery_hour','15:00 - 17:00');
        DB::enableQueryLog(); DB::flushQueryLog();
        $this->service->availability($this->store->id,'2026-11-09',null,null);
        $this->assertLessThanOrEqual(3,count(DB::getQueryLog()));
        DB::disableQueryLog();
    }

    public function test_foreign_address_and_mixed_vendor_products_are_rejected(): void
    {
        $other = $this->user('foreign-buyer','customer');
        $c=Customer::create(['user_id'=>$other->id]);
        $a=Address::create(['customer_id'=>$c->id,'address_name'=>'Private']);
        $this->book(['address_id'=>$a->id])->assertUnprocessable()->assertJsonValidationErrors('address_id');
        $v=Vendor::create(['name'=>'Other','slug'=>'mixed','is_active'=>true]);
        $service=Service::create(['name'=>'Other service','vendor_id'=>$v->id,'is_active'=>true]);
        $p=$this->product->replicate();$p->vendor_id=$v->id;$p->service_id=$service->id;$p->save();
        $this->book(['products'=>[['id'=>$this->product->id,'quantity'=>1],['id'=>$p->id,'quantity'=>1]]])->assertUnprocessable();
        $this->assertDatabaseCount('orders',0);
    }
}
