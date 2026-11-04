<?php

use App\Models\Media;
use App\Models\Offer;
use App\Models\Vendor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Offer())->getTable(), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreignId('thumbnail_id')->nullable()->constrained((new Media())->getTable());
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->float('discount_value')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on((new Vendor())->getTable())->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new Offer())->getTable());
    }
}