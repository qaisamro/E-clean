<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();

            $table->enum('payment_type', \App\Enums\PaymentType::values());
            $table->unsignedInteger('total_price')->nullable();
            $table->unsignedInteger('paid_money')->nullable();
            $table->unsignedInteger('remaining_money')->nullable();

            $table->boolean('has_deferred_payment')->default(false);

            $table->enum('status', OrderStatus::values())->default(OrderStatus::NEW->value);
            $table->enum('payment_status', PaymentStatus::values())->default(PaymentStatus::UNPAID->value);

            $table->foreignId('customer_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->boolean('created_by_customer')->default(false);

            $table->date('deliver_date')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

