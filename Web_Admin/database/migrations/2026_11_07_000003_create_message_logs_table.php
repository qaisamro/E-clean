<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 32)->index();
            $table->string('provider', 64)->nullable();
            $table->string('template_key', 64)->nullable();
            $table->string('recipient_masked', 120);
            $table->string('recipient_hash', 64)->index();
            $table->string('status', 24)->default('pending')->index();
            $table->string('provider_message_id')->nullable();
            $table->string('error_code', 64)->nullable();
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
