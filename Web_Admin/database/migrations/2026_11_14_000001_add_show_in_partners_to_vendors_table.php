<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowInPartnersToVendorsTable extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('show_in_partners')->default(true)->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('show_in_partners');
        });
    }
}
