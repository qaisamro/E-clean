<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Resolves an order's store from the products it actually contains.
 *
 * A client-supplied vendor_id is never an ownership source. Products that
 * belong to different stores (or mix known and unknown ownership) cannot be
 * combined because orders have a single vendor_id column.
 */
class OrderVendorOwnership
{
    public static function resolveProductVendor(array $productIds, ?int $requiredVendorId = null, bool $includeTrashed = false): ?int
    {
        $ids = collect($productIds)->map(fn ($id) => (int) $id)->unique()->values();
        $query = Product::with('service');
        if ($includeTrashed) {
            $query->withTrashed();
        }
        $products = $query->whereIn('id', $ids)->get()->keyBy('id');

        if ($ids->isEmpty() || $products->count() !== $ids->count()) {
            throw ValidationException::withMessages([
                'products' => 'The selected products are unavailable.',
            ]);
        }

        $owners = [];
        foreach ($ids as $id) {
            $product = $products->get($id);
            $productVendorId = $product->vendor_id ? (int) $product->vendor_id : null;
            $serviceVendorId = $product->service?->vendor_id
                ? (int) $product->service->vendor_id
                : null;

            if ($productVendorId && $serviceVendorId && $productVendorId !== $serviceVendorId) {
                throw ValidationException::withMessages([
                    'products' => 'The selected products have inconsistent store ownership.',
                ]);
            }

            $owners[] = $productVendorId ?? $serviceVendorId;
        }

        if (count(array_unique($owners, SORT_REGULAR)) !== 1) {
            throw ValidationException::withMessages([
                'products' => 'Products from different or unknown stores cannot be combined.',
            ]);
        }

        $owner = $owners[0];
        if ($owner === null) {
            // Every product is global (no store ownership anywhere);
            // the selling store, when supplied, claims the order.
            return $requiredVendorId;
        }
        if ($requiredVendorId !== null && $owner !== $requiredVendorId) {
            throw ValidationException::withMessages([
                'products' => 'The selected products do not belong to this store.',
            ]);
        }

        return $owner;
    }

    public static function assertProductsMatchOrder(Order $order, array $productIds, bool $includeTrashed = true): void
    {
        $productVendorId = self::resolveProductVendor($productIds, null, $includeTrashed);
        if ($productVendorId === null) {
            // Every product is global (no store ownership anywhere):
            // order.vendor_id is the governing attribution.
            return;
        }
        if ($productVendorId !== ($order->vendor_id ? (int) $order->vendor_id : null)) {
            throw ValidationException::withMessages([
                'products' => 'The selected products do not belong to this order.',
            ]);
        }
    }

    public static function productsMatchOrder(Order $order): bool
    {
        $productIds = $order->relationLoaded('products')
            ? $order->products->pluck('id')->all()
            : $order->products()->pluck('products.id')->all();
        // An order with no attached products has nothing to verify against;
        // ownership is governed by order.vendor_id (already checked by the
        // caller). Treat only orders that *do* have pivot rows but whose
        // products can no longer be resolved as unverifiable.
        $hasPivotRows = DB::table('order_products')
            ->where('order_id', $order->id)
            ->exists();
        if (!$hasPivotRows) {
            return true;
        }
        if (!$productIds) {
            return false;
        }

        try {
            self::assertProductsMatchOrder($order, $productIds, true);
            return true;
        } catch (ValidationException) {
            return false;
        }
    }

    public static function filterConsistentOrders(Collection $orders): Collection
    {
        return $orders
            ->filter(fn (Order $order) => self::productsMatchOrder($order))
            ->values();
    }
}
