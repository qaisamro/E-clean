<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxRateToWebSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(0)->after('currency_name');
        });
    }

    public function down()
    {
        Schema::table('web_settings', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
    }
}
