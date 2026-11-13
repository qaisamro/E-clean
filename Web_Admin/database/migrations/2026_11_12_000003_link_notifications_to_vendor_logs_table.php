<?php

use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Keeps the source store on each inbox row. Nullable so existing admin and
 * system notifications keep working unchanged.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('customer_id')
                ->constrained((new Vendor())->getTable())->nullOnDelete();
            $table->foreignId('vendor_notification_log_id')->nullable()->after('vendor_id')
                ->constrained('vendor_notification_logs')->nullOnDelete();

            $table->index(['vendor_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['vendor_id', 'customer_id']);
            $table->dropConstrainedForeignId('vendor_notification_log_id');
            $table->dropConstrainedForeignId('vendor_id');
        });
    }
};
