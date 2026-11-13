<?php
namespace App\Services;

use App\Models\{Order, OrderAppointment, Vendor, VendorAppointmentSetting};
use Carbon\CarbonImmutable as Time;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VendorAppointments
{
    public const ZONE = 'Asia/Hebron';
    private ?array $availabilityOrders = null;

    public function defaults(): array
    {
        return ['enabled' => false, 'processing_minutes' => 1440, 'horizon_days' => 30,
            'weekly' => ['pickup' => [], 'delivery' => []], 'exceptions' => []];
    }

    public function settings(int $vendor): array
    {
        $query = VendorAppointmentSetting::where('vendor_id', $vendor);
        if (DB::transactionLevel() > 0) $query->lockForUpdate();
        return $query->first()?->settings ?? $this->defaults();
    }

    public function validateSettings(array $input): array
    {
        $s = Validator::make($input, [
            'enabled' => 'required|boolean', 'processing_minutes' => 'required|integer|min:1|max:43200',
            'horizon_days' => 'required|integer|min:1|max:60', 'weekly' => 'required|array:pickup,delivery',
            'weekly.pickup' => 'present|array|max:70', 'weekly.delivery' => 'present|array|max:70',
            'weekly.*.*.day' => 'required|integer|between:0,6',
            'weekly.*.*.start' => 'required|date_format:H:i', 'weekly.*.*.end' => 'required|date_format:H:i',
            'weekly.*.*.capacity' => 'required|integer|min:1|max:1000',
            'exceptions' => 'present|array|max:180', 'exceptions.*.date' => 'required|date_format:Y-m-d',
            'exceptions.*.type' => 'required|in:pickup,delivery,both',
            'exceptions.*.start' => 'nullable|date_format:H:i', 'exceptions.*.end' => 'nullable|date_format:H:i',
        ])->validate();
        foreach (['pickup', 'delivery'] as $type) {
            $rows = $s['weekly'][$type];
            foreach ($rows as $i => $row) {
                if ($row['start'] >= $row['end']) $this->fail('weekly', 'نهاية الفترة يجب أن تكون بعد بدايتها، ضمن اليوم نفسه.');
                foreach (array_slice($rows, $i + 1) as $other) {
                    if ($row['day'] == $other['day'] && $row['start'] < $other['end'] && $row['end'] > $other['start']) {
                        $this->fail('weekly', 'فترات اليوم الواحد يجب ألا تتداخل.');
                    }
                }
            }
        }
        foreach ($s['exceptions'] as $row) {
            if ((!empty($row['start']) !== !empty($row['end'])) ||
                (!empty($row['start']) && $row['start'] >= $row['end'])) $this->fail('exceptions', 'أدخل بداية ونهاية صحيحتين للإغلاق أو اتركهما لإغلاق اليوم كاملاً.');
        }
        // Persist only known keys, never trust arbitrary client settings.
        $s['weekly'] = array_map(fn ($rows) => array_map(fn ($r) => array_intersect_key($r, array_flip(['day','start','end','capacity'])), $rows), $s['weekly']);
        $s['exceptions'] = array_map(fn ($r) => array_intersect_key($r, array_flip(['date','type','start','end'])), $s['exceptions']);
        return $s;
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    public function save(int $vendor, array $settings): void
    {
        $settings = $this->validateSettings($settings);
        DB::transaction(function () use ($vendor, $settings) {
            Vendor::whereKey($vendor)->lockForUpdate()->firstOrFail();
            VendorAppointmentSetting::updateOrCreate(['vendor_id' => $vendor], ['settings' => $settings]);
        }, 3);
    }

    private function date(string $date): Time
    {
        try {
            $t = Time::createFromFormat('!Y-m-d', $date, self::ZONE);
            if ($t && $t->format('Y-m-d') === $date) return $t;
        } catch (\Throwable) {}
        $this->fail('date', 'التاريخ غير صالح.');
    }

    private function localTime(string $date, string $clock): ?Time
    {
        // A spring DST gap must not silently turn 02:30 into 03:30.
        $t = Time::parse($date.' '.$clock, self::ZONE);
        if ($t->format('Y-m-d H:i') !== $date.' '.$clock) return null;
        // Reject ambiguous autumn wall times: old clients cannot identify a fold.
        foreach ([-3600, 3600] as $seconds) {
            if ($t->setTimezone('UTC')->addSeconds($seconds)->setTimezone(self::ZONE)->format('Y-m-d H:i') === $date.' '.$clock) return null;
        }
        return $t;
    }

    public function slots(int $vendor, string $type, string $date, ?Time $earliest = null, ?int $excludeOrder = null, ?array $settings = null): array
    {
        $day = $this->date($date);
        $s = $settings ?? $this->settings($vendor);
        $now = Time::now(self::ZONE);
        if (!$s['enabled'] || $day->lt($now->startOfDay()) || $day->gte($now->startOfDay()->addDays($s['horizon_days']))) return [];
        $result = [];
        foreach ($s['weekly'][$type] as $row) {
            if ((int) $row['day'] !== $day->dayOfWeek) continue;
            $start = $this->localTime($date, $row['start']);
            $end = $this->localTime($date, $row['end']);
            if (!$start || !$end) continue;
            $reason = null;
            if ($start->lte($now)) $reason = 'انتهت صلاحية الموعد';
            elseif ($earliest && $start->lt($earliest)) $reason = 'قبل انتهاء تجهيز الغسيل';
            foreach ($s['exceptions'] as $closed) {
                if ($closed['date'] === $date && in_array($closed['type'], [$type, 'both'], true) &&
                    (empty($closed['start']) || ($row['start'] < $closed['end'] && $row['end'] > $closed['start']))) $reason = 'إجازة أو فترة مغلقة';
            }
            $used = $this->used($vendor, $type, $start, $end, $excludeOrder);
            if (!$reason && $used >= $row['capacity']) $reason = 'الفترة ممتلئة';
            $result[] = ['date' => $date, 'hour' => $row['start'], 'title' => $row['start'].' - '.$row['end'],
                'starts_at' => $start->toIso8601String(), 'ends_at' => $end->toIso8601String(),
                'is_booked' => $reason !== null, 'reason' => $reason, 'remaining' => max(0, $row['capacity'] - $used)];
        }
        usort($result, fn ($a, $b) => strcmp($a['hour'], $b['hour']));
        return $result;
    }

    private function used(int $vendor, string $type, Time $start, Time $end, ?int $exclude): int
    {
        $prefix = $type === 'pickup' ? 'pick' : 'delivery';
        $utcStart = $start->setTimezone('UTC')->format('Y-m-d H:i:s');
        $utcEnd = $end->setTimezone('UTC')->format('Y-m-d H:i:s');
        if ($this->availabilityOrders !== null) {
            $used = 0;
            foreach ($this->availabilityOrders as $order) {
                if ($order->id == $exclude) continue;
                if ($order->pickup_start !== null) {
                    if ($order->{$type.'_start'} < $utcEnd && $order->{$type.'_end'} > $utcStart) $used++;
                } elseif (substr((string) $order->{$prefix.'_date'}, 0, 10) === $start->format('Y-m-d') &&
                    $order->{$prefix.'_hour'} >= $start->format('H:i:s') && $order->{$prefix.'_hour'} < $end->format('H:i:s')) $used++;
            }
            return $used;
        }
        // Snapshot intervals survive later schedule edits. Legacy orders also occupy capacity.
        $query = Order::leftJoin('order_appointments as booking', 'booking.order_id', '=', 'orders.id')
            ->where('orders.vendor_id', $vendor)->where('orders.order_status', '!=', 'Cancelled')
            ->when($exclude, fn ($q) => $q->where('orders.id', '!=', $exclude))
            ->where(function ($q) use ($type, $prefix, $start, $end, $utcStart, $utcEnd) {
                $q->where(fn ($b) => $b->where('booking.'.$type.'_start', '<', $utcEnd)->where('booking.'.$type.'_end', '>', $utcStart))
                    ->orWhere(function ($old) use ($prefix, $start, $end) {
                        $old->whereNull('booking.id')
                            ->whereDate($prefix.'_date', $start->format('Y-m-d'))
                            ->where($prefix.'_hour', '>=', $start->format('H:i:s'))->where($prefix.'_hour', '<', $end->format('H:i:s'));
                    });
            });
        // InnoDB REPEATABLE READ may have an older snapshot from product reads.
        // A locking read sees the latest committed bookings after the vendor lock.
        if (DB::transactionLevel() > 0) return $query->lockForUpdate()->get(['orders.id'])->count();
        return $query->count();
    }

    private function selected(int $vendor, string $type, string $date, string $hour, ?Time $earliest = null, ?int $exclude = null): array
    {
        // Legacy applications send hour ranges such as 8-9. Resolve their exact start only.
        if (preg_match('/^(\d{1,2})-\d{1,2}$/', $hour, $m)) $hour = sprintf('%02d:00', $m[1]);
        if (preg_match('/^\d{2}:\d{2}:00$/', $hour)) $hour = substr($hour, 0, 5);
        foreach ($this->slots($vendor, $type, $date, $earliest, $exclude) as $slot) {
            if ($slot['hour'] === $hour && !$slot['is_booked']) return $slot;
        }
        $this->fail($type === 'pickup' ? 'pick_hour' : 'delivery_hour', 'الموعد لم يعد متاحاً. يرجى اختيار فترة أخرى.');
    }

    // Caller MUST hold the vendor row lock through order creation and snapshot saving.
    public function validatePair(int $vendor, array $input, ?int $exclude = null): array
    {
        $s = $this->settings($vendor);
        $pickup = $this->selected($vendor, 'pickup', $input['pick_date'], $input['pick_hour'], null, $exclude);
        $ready = Time::parse($pickup['ends_at'])->setTimezone('UTC')->addMinutes($s['processing_minutes']);
        $delivery = $this->selected($vendor, 'delivery', $input['delivery_date'], $input['delivery_hour'], $ready, $exclude);
        return ['pickup' => $pickup, 'delivery' => $delivery, 'processing_minutes' => $s['processing_minutes']];
    }

    public function snapshot(Order $order, array $pair, ?array $history = null): void
    {
        $values = ['vendor_id' => $order->vendor_id, 'processing_minutes' => $pair['processing_minutes']];
        foreach (['pickup', 'delivery'] as $type) foreach (['start' => 'starts_at', 'end' => 'ends_at'] as $key => $source) {
            $values[$type.'_'.$key] = Time::parse($pair[$type][$source])->setTimezone('UTC')->format('Y-m-d H:i:s');
        }
        if ($history !== null) $values['reschedule_history'] = $history;
        OrderAppointment::updateOrCreate(['order_id' => $order->id], $values);
    }

    public function availability(int $vendor, string $date, ?string $pickupDate, ?string $pickupHour, ?int $exclude = null): array
    {
        $s = $this->settings($vendor);
        $start = $this->date($date);
        $earliest = null;
        if ($pickupDate && $pickupHour) {
            $pick = $this->selected($vendor, 'pickup', $pickupDate, $pickupHour, null, $exclude);
            $earliest = Time::parse($pick['ends_at'])->setTimezone('UTC')->addMinutes($s['processing_minutes']);
        }
        $days = [];
        // One bounded availability read, not one SQL query for every displayed card.
        $from = $start->subDay()->format('Y-m-d');
        $to = $start->addDays($s['horizon_days'] + 1)->format('Y-m-d');
        $this->availabilityOrders = DB::table('orders')->leftJoin('order_appointments as b', 'b.order_id', '=', 'orders.id')
            ->where('orders.vendor_id', $vendor)->where('orders.order_status', '!=', 'Cancelled')
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('orders.pick_date', [$from, $to])->orWhereBetween('orders.delivery_date', [$from, $to])
                    ->orWhere(fn ($b) => $b->where('b.pickup_start', '<=', $to)->where('b.pickup_end', '>=', $from))
                    ->orWhere(fn ($b) => $b->where('b.delivery_start', '<=', $to)->where('b.delivery_end', '>=', $from));
            })->select(['orders.id', 'pick_date', 'pick_hour', 'delivery_date', 'delivery_hour', 'pickup_start', 'pickup_end', 'delivery_start', 'delivery_end'])->get()->all();
        try { for ($i = 0; $i < $s['horizon_days']; $i++) {
            $d = $start->addDays($i)->format('Y-m-d');
            $days[] = ['date' => $d, 'pickup' => $this->slots($vendor, 'pickup', $d, null, $exclude, $s),
                'delivery' => $earliest ? $this->slots($vendor, 'delivery', $d, $earliest, $exclude, $s) : []];
        }} finally { $this->availabilityOrders = null; }
        return ['enabled' => (bool) $s['enabled'], 'vendor_id' => $vendor, 'timezone' => self::ZONE,
            'processing_minutes' => $s['processing_minutes'], 'days' => $days];
    }
}
