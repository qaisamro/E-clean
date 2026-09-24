<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Vendor;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('logo_media_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->comment('Owner user');
            $table->boolean('is_active')->default(true);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('logo_media_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Create default vendor for existing data
        $defaultVendorId = \DB::table('vendors')->insertGetId([
            'name' => 'Elite Main',
            'slug' => 'elite-main',
            'address' => 'HEBRON - DURA',
            'phone' => '0592925567',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add vendor_id to existing tables if not exists
        $tables = ['services', 'products', 'orders', 'coupons', 'banners'];
        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && !Schema::hasColumn($tbl, 'vendor_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
                    $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
                });
                // Assign existing rows to default vendor
                \DB::table($tbl)->whereNull('vendor_id')->update(['vendor_id' => $defaultVendorId]);
            }
        }
    }

    public function down()
    {
        $tables = ['services', 'products', 'orders', 'coupons', 'banners'];
        foreach ($tables as $tbl) {
            if (Schema::hasTable($tbl) && Schema::hasColumn($tbl, 'vendor_id')) {
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropForeign(['vendor_id']);
                    $table->dropColumn('vendor_id');
                });
            }
        }
        Schema::dropIfExists('vendors');
    }
}
