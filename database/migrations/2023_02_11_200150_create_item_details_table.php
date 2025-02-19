<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_details', function (Blueprint $table) {
            $table->id('item_deta_id');
            $table->integer('itype_id');
            $table->integer('icatg_id');
            $table->integer('iscatg_id');
            $table->string('item_deta_code',10);
            $table->integer('ibrand_id');
            $table->integer('store_id'); // Sub store informations
            $table->integer('item_comp_id');
            $table->integer('item_det_unit');
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->date('received_date');
            $table->string('model_no')->nullable();
            $table->string('serial_no')->nullable();
            $table->integer('quantity');
            $table->integer('create_by_id');
            $table->integer('update_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_details');
    }
}
