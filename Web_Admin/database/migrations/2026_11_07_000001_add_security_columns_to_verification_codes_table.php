<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('verification_codes', 'purpose')) {
                $table->string('purpose', 32)->default('generic')->after('contact')->index();
            }
            if (!Schema::hasColumn('verification_codes', 'attempts')) {
                $table->unsignedTinyInteger('attempts')->default(0)->after('token');
            }
            if (!Schema::hasColumn('verification_codes', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('attempts');
            }
            if (!Schema::hasColumn('verification_codes', 'last_sent_at')) {
                $table->timestamp('last_sent_at')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('verification_codes', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('last_sent_at');
            }
        });

        // Legacy rows have no expiry and could otherwise be replayed forever.
        // Expire them on deploy; users can request a fresh code.
        DB::table('verification_codes')
            ->whereNull('expires_at')
            ->update(['expires_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('verification_codes', function (Blueprint $table) {
            foreach (['verified_at', 'last_sent_at', 'expires_at', 'attempts', 'purpose'] as $column) {
                if (Schema::hasColumn('verification_codes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
