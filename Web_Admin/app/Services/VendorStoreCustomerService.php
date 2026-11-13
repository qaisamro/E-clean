<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Derives a store's customer base from successful (Delivered) orders only.
 *
 * No customer/store membership table is needed: the relation is recomputed
 * from orders, so historical customers appear automatically and repeated
 * orders never create duplicates. Orders whose products belong to another
 * store (or whose ownership cannot be resolved to exactly one store) are
 * excluded so one store can never see another store's buyers.
 */
class VendorStoreCustomerService
{
    public const DUPLICATE_WINDOW_MINUTES = 10;

    public const COOLDOWN_SECONDS = 20;

    public const HOURLY_LIMIT = 30;

    public function vendorForUser(User $user): Vendor
    {
        $vendor = \App\Support\VendorAccount::vendorFor($user);
        abort_unless($vendor, 403);
        return $vendor;
    }

    /**
     * Successful orders attributed to one store.
     *
     * An order qualifies when it is Delivered and:
     *  - it has an explicit vendor_id equal to the store with no conflicting
     *    product ownership (orders without pivot rows are allowed), or
     *  - it is a legacy order (vendor_id = null) whose products all resolve to
     *    exactly this store.
     */
    public function eligibleOrdersQuery(int $vendorId): QueryBuilder
    {
        $orders = (new Order())->getTable();
        $orderProducts = (new OrderProduct())->getTable();
        $products = (new Product())->getTable();
        $services = (new Service())->getTable();

        return DB::table($orders . ' as o')
            ->where('o.order_status', config('enums.order_status.delivered'))
            ->where(function (QueryBuilder $query) use ($vendorId) {
                $query->where('o.vendor_id', $vendorId)->orWhereNull('o.vendor_id');
            })
            ->where(function (QueryBuilder $query) use (
                $vendorId,
                $orderProducts,
                $products,
                $services
            ) {
                $query->where(function (QueryBuilder $inner) use ($vendorId, $orderProducts) {
                    // Explicit store order with no product rows: ownership is
                    // already established by orders.vendor_id.
                    $inner->where('o.vendor_id', $vendorId)
                        ->whereNotExists(function (QueryBuilder $exists) use ($orderProducts) {
                            $exists->selectRaw('1')
                                ->from($orderProducts . ' as op')
                                ->whereColumn('op.order_id', 'o.id');
                        });
                })->orWhere(function (QueryBuilder $inner) use (
                    $vendorId,
                    $orderProducts,
                    $products,
                    $services
                ) {
                    $inner->whereExists(function (QueryBuilder $exists) use ($orderProducts) {
                        $exists->selectRaw('1')
                            ->from($orderProducts . ' as op')
                            ->whereColumn('op.order_id', 'o.id');
                    })->whereNotExists(function (QueryBuilder $bad) use (
                        $vendorId,
                        $orderProducts,
                        $products,
                        $services
                    ) {
                        $bad->selectRaw('1')
                            ->from($orderProducts . ' as op')
                            ->leftJoin($products . ' as p', 'p.id', '=', 'op.product_id')
                            ->leftJoin($services . ' as s', 's.id', '=', 'p.service_id')
                            ->whereColumn('op.order_id', 'o.id')
                            ->where(function (QueryBuilder $condition) use ($vendorId) {
                                $condition
                                    // Product no longer resolvable.
                                    ->whereNull('p.id')
                                    // Product and service both have no store.
                                    ->orWhere(function (QueryBuilder $unknown) {
                                        $unknown->whereNull('p.vendor_id')
                                            ->whereNull('s.vendor_id');
                                    })
                                    // Product and service disagree on the store.
                                    ->orWhere(function (QueryBuilder $conflict) {
                                        $conflict->whereNotNull('p.vendor_id')
                                            ->whereNotNull('s.vendor_id')
                                            ->whereColumn('p.vendor_id', '<>', 's.vendor_id');
                                    })
                                    // Products from a different store.
                                    ->orWhereRaw('COALESCE(p.vendor_id, s.vendor_id) <> ?', [$vendorId]);
                            });
                    });
                });
            });
    }

    public function eligibleCustomerIdsQuery(int $vendorId): QueryBuilder
    {
        return $this->eligibleOrdersQuery($vendorId)
            ->select('o.customer_id')
            ->distinct();
    }

    /**
     * The full set of a store's customers: clients with at least one
     * successfully delivered order, UNION the explicit link registered by
     * the store staff (owner or driver) in the POS. Without a dedicated
     * pivot, a customer created in the POS would be invisible until they
     * place a delivered order — which is not what the store expects.
     */
    public function storeCustomerIdsUnion(int $vendorId): QueryBuilder
    {
        return $this->eligibleCustomerIdsQuery($vendorId)
            ->union(
                DB::table('store_customers')
                    ->select('customer_id')
                    ->where('vendor_id', $vendorId)
            );
    }

    public function storeCustomerIdsSubquery(int $vendorId): QueryBuilder
    {
        return DB::query()
            ->fromSub($this->storeCustomerIdsUnion($vendorId), 'store_customer_ids')
            ->select('customer_id');
    }

    public function customerStatsQuery(int $vendorId): QueryBuilder
    {
        return $this->eligibleOrdersQuery($vendorId)
            ->select([
                'o.customer_id',
                DB::raw('COUNT(*) as delivered_orders'),
                DB::raw('MAX(o.created_at) as last_order_at'),
                DB::raw('SUM(o.total_amount) as total_spent'),
            ])
            ->groupBy('o.customer_id');
    }

    /**
     * Customers with per-store order statistics. Used by the vendor page.
     * Includes clients explicitly registered to the store in the POS
     * (source: store_customers), not only delivered-order clients.
     */
    public function customersQuery(Vendor $vendor): EloquentBuilder
    {
        $vendorId = (int) $vendor->id;

        return Customer::query()
            ->whereIn('customers.id', $this->storeCustomerIdsSubquery($vendorId))
            ->leftJoinSub(
                $this->customerStatsQuery($vendorId),
                'vendor_customer_stats',
                'vendor_customer_stats.customer_id',
                '=',
                'customers.id'
            )
            ->select([
                'customers.*',
                'vendor_customer_stats.delivered_orders',
                'vendor_customer_stats.last_order_at',
                'vendor_customer_stats.total_spent',
            ])
            ->with('user');
    }

    public function isCustomerOfVendor(Vendor $vendor, int $customerId): bool
    {
        return $this->storeCustomerIdsUnion((int) $vendor->id)
            ->where('customer_id', $customerId)
            ->exists();
    }

    public function notificationDisabledCount(Vendor $vendor): int
    {
        return Customer::query()
            ->whereIn('customers.id', $this->storeCustomerIdsSubquery((int) $vendor->id))
            ->whereHas('user', function (EloquentBuilder $query) {
                $query->where('vendor_notifications_enabled', false);
            })
            ->count();
    }

    /**
     * Customers of this store that currently accept vendor notifications.
     */
    public function recipients(Vendor $vendor, ?int $customerId = null): Collection
    {
        return Customer::query()
            ->whereIn('customers.id', $this->storeCustomerIdsSubquery((int) $vendor->id))
            ->when($customerId !== null, fn (EloquentBuilder $query) => $query->whereKey($customerId))
            ->whereHas('user', function (EloquentBuilder $query) {
                $query->where(function (EloquentBuilder $inner) {
                    $inner->where('vendor_notifications_enabled', true)
                        ->orWhereNull('vendor_notifications_enabled');
                });
            })
            ->with('user')
            ->get();
    }

    public function allCustomersForSelection(Vendor $vendor): Collection
    {
        return Customer::query()
            ->whereIn('customers.id', $this->storeCustomerIdsSubquery((int) $vendor->id))
            ->with('user')
            ->get()
            ->filter(fn (Customer $customer) => $customer->user !== null)
            ->sortBy(fn (Customer $customer) => $customer->user->name)
            ->values();
    }

    /**
     * Total number of this store's customers: delivered-order clients
     * UNION the clients registered in the POS (store_customers).
     */
    public function eligibleCustomerCount(Vendor $vendor): int
    {
        return DB::query()
            ->fromSub($this->storeCustomerIdsUnion((int) $vendor->id), 'store_customer_ids')
            ->count();
    }

    public function summary(Vendor $vendor): array
    {
        $vendorId = (int) $vendor->id;
        $base = $this->eligibleOrdersQuery($vendorId);

        return [
            'customers' => $this->eligibleCustomerCount($vendor),
            'orders' => (clone $base)->count(),
            'recent_customers' => DB::query()
                ->fromSub(
                    (clone $base)
                        ->select('o.customer_id')
                        ->distinct()
                        ->where('o.created_at', '>=', now()->subDays(30)),
                    'recent_customers'
                )
                ->count(),
            'disabled_customers' => $this->notificationDisabledCount($vendor),
        ];
    }
}
