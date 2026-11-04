<?php

use App\Models\Banner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannerSectionToBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Banner())->getTable(), function (Blueprint $table) {
            $table->string('banner_section')->nullable()->after('is_banner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new Banner())->getTable(), function (Blueprint $table) {
            $table->dropColumn('banner_section');
        });
    }
}