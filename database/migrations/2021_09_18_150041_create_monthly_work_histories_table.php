<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyWorkHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_work_histories', function (Blueprint $table) {
            $table->id('month_work_id');
            $table->integer('emp_id');
            $table->integer('month_id');
            $table->integer('year');
            $table->integer('total_hours')->default(0);
            $table->integer('overtime')->default(0);
            $table->integer('total_work_day')->default(0);
            $table->boolean('status')->default(1);
            $table->integer('entered_id');
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
        Schema::dropIfExists('monthly_work_histories');
    }
}
