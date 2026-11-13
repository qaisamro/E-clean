<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'background_media_id')) {
                return;
            }
            $table->unsignedBigInteger('background_media_id')->nullable()
                ->after('logo_media_id');
            $table->foreign('background_media_id')
                ->references('id')->on('media')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['background_media_id']);
            $table->dropColumn('background_media_id');
        });
    }
};
