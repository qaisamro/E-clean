<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteOrderWithRelations extends Command
{
     protected $signature = 'orders:delete-relations';
    protected $description = 'Delete all related tables: rating, order_products, additional_order, payment, driver_orders, driver_history';

    public function handle()
    {
        // Optionally wrap in transaction
        DB::beginTransaction();
        try {

            DB::table('ratings')->delete();
            DB::table('order_products')->delete();
            DB::table('additional_orders')->delete();
            DB::table('payments')->delete();
            DB::table('driver_orders')->delete();
            DB::table('driver_histories')->delete();
            DB::table('transactions')->delete();
            DB::table('orders')->delete();
            DB::commit();
            $this->info('All related order data deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to delete related data: ' . $e->getMessage());
        }
    }
}
