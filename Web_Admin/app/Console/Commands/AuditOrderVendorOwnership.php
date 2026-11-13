<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Support\OrderVendorOwnership;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditOrderVendorOwnership extends Command
{
    protected $signature = 'orders:audit-vendor-ownership
                            {--fix : Apply the only ownership assignments that can be proven from every product/service}
                            {--order= : Limit the audit to one order id}
                            {--vendor= : Limit proposed repairs to one vendor id}';

    protected $description = 'Audit and (optionally) repair unresolved order store ownership without guessing.';

    public function handle(): int
    {
        $orders = Order::query()->with(['products' => fn ($q) => $q->withTrashed()->with('service')]);
        if ($this->option('order')) {
            $orders->whereKey((int) $this->option('order'));
        }
        $orders = $orders->orderBy('id')->get();

        $rows = [];
        $repairable = [];
        foreach ($orders as $order) {
            $ids = $order->products->pluck('id')->all();
            $derived = null;
            $status = 'unverifiable';
            $reason = 'No attached products';

            if ($ids) {
                try {
                    $derived = OrderVendorOwnership::resolveProductVendor($ids, null, true);
                    $status = 'consistent';
                    $reason = 'All attached products/services agree on one store';
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $status = 'inconsistent';
                    $reason = implode('; ', array_merge(...array_values($e->errors())));
                }
            }

            $current = $order->vendor_id ? (int) $order->vendor_id : null;
            $action = 'none';
            if ($derived !== null && $current === null) {
                $action = 'repairable';
                if ($this->option('vendor') && (int) $this->option('vendor') !== $derived) {
                    $action = 'skipped (vendor filter)';
                }
                $repairable[] = [$order, $derived];
            } elseif ($derived !== null && $current !== null && $current !== $derived) {
                $action = 'conflict';
            } elseif ($derived === null && $current !== null) {
                $action = 'blocked (unverifiable)';
            }

            $rows[] = [
                $order->id,
                $order->order_code,
                $current ?? 'NULL',
                $derived ?? 'NULL',
                $status,
                $action,
                $reason,
            ];
        }

        $this->table(
            ['order', 'code', 'current vendor', 'derived vendor', 'products', 'action', 'reason'],
            $rows
        );

        $this->newLine();
        $this->info('Summary: ' . count($orders) . ' orders, ' . count($repairable) . ' repairable, ' .
            count(array_filter($rows, fn ($r) => $r[5] === 'conflict')) . ' conflicts.');

        if (!$this->option('fix')) {
            $this->comment('Dry run only. Re-run with --fix to apply proven assignments.');
            return self::SUCCESS;
        }

        $applied = 0;
        foreach ($repairable as [$order, $vendorId]) {
            if ($this->option('vendor') && (int) $this->option('vendor') !== $vendorId) {
                continue;
            }
            DB::transaction(function () use ($order, $vendorId, &$applied) {
                $order->update(['vendor_id' => $vendorId]);
                $applied++;
            });
            $this->info("Repaired order {$order->id} -> vendor {$vendorId}");
        }

        $this->info("Applied {$applied} repair(s).");
        return self::SUCCESS;
    }
}