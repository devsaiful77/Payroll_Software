<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id('TranTypeId');
            $table->string('TranTypeName');
        });

        DB::table('transaction_types')->insert([
            'TranTypeName' => 'Expence' ,
        ]);
        DB::table('transaction_types')->insert([
            'TranTypeName' => 'Income' ,
        ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_types');
    }
}
