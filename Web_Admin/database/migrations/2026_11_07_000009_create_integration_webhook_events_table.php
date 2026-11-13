<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 32)->index();
            $table->string('event_type', 80)->nullable();
            $table->string('external_id', 191)->nullable()->index();
            $table->json('payload');
            $table->boolean('signature_valid')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_webhook_events');
    }
};
