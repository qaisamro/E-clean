<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const BACKUP_TABLE = 'service_catalog_legacy_owners';

    public function up(): void
    {
        if (!Schema::hasTable('services') || !Schema::hasColumn('services', 'vendor_id')) {
            return;
        }

        if (!Schema::hasTable(self::BACKUP_TABLE)) {
            Schema::create(self::BACKUP_TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger('service_id')->primary();
                $table->unsignedBigInteger('vendor_id');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        DB::transaction(function () {
            DB::table('services')
                ->whereNotNull('vendor_id')
                ->orderBy('id')
                ->chunkById(100, function ($services) {
                    foreach ($services as $service) {
                        DB::table(self::BACKUP_TABLE)->updateOrInsert(
                            ['service_id' => $service->id],
                            ['vendor_id' => $service->vendor_id]
                        );
                    }
                });

            // Products retain their own vendor_id. Only the catalogue category
            // becomes global; no product, price, order, or service is deleted.
            DB::table('services')->whereNotNull('vendor_id')->update([
                'vendor_id' => null,
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP_TABLE) || !Schema::hasTable('services')) {
            return;
        }

        DB::transaction(function () {
            DB::table(self::BACKUP_TABLE)->orderBy('service_id')->chunkById(
                100,
                function ($owners) {
                    foreach ($owners as $owner) {
                        DB::table('services')
                            ->where('id', $owner->service_id)
                            ->update(['vendor_id' => $owner->vendor_id]);
                    }
                },
                'service_id'
            );
        });

        Schema::dropIfExists(self::BACKUP_TABLE);
    }
};
