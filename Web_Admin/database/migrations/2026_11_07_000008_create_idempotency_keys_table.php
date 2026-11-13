<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->nullable()->constrained('api_keys')->cascadeOnDelete();
            $table->string('idempotency_key', 191);
            $table->string('request_hash', 64);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->longText('response_body')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->unique(['api_key_id', 'idempotency_key'], 'idempotency_api_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
