<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 16)->default('pending')->index(); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->json('request_metadata')->nullable(); // stores registration source, device info, etc.
            $table->string('tracking_token_hash', 64)->nullable()->index(); // SHA-256 hash of tracking token
            $table->timestamp('tracking_token_expires_at')->nullable(); // token expiry
            $table->timestamp('tracking_token_revoked_at')->nullable(); // manual revocation
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_join_requests');
    }
};
