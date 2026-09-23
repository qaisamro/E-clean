<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->unsignedInteger('basic_salary')->nullable()->after('value');
            $table->unsignedInteger('deductions')->default(0)->after('basic_salary');
            $table->unsignedInteger('bonuses')->default(0)->after('deductions');
            $table->date('payment_date')->nullable()->after('bonuses');
            $table->string('payment_status')->default('paid')->after('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['basic_salary', 'deductions', 'bonuses', 'payment_date', 'payment_status']);
        });
    }
};
