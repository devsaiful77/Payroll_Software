<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
        });

        DB::table('employee_types')->insert(
            array(
                   
                  'name' => 'Direct Employee'   
            )
          );
          DB::table('employee_types')->insert(
            array(
                   
                  'name' => 'Indirect Employee'   
            )
          );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_types');
    }
}
