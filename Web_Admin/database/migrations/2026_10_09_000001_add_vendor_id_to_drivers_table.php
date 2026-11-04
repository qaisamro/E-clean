<?php

use App\Models\Driver;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorIdToDriversTable extends Migration
{
    /**
     * Link each driver to the store (vendor) that owns them so drivers
     * can only see and handle their own store's data.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Driver())->getTable(), function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('user_id')
                ->constrained((new Vendor())->getTable())->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new Driver())->getTable(), function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendor_id');
        });
    }
}
