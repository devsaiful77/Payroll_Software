<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chartofacc_sales_records', function (Blueprint $table) {
            $table->increments('sr_auto_id');
            $table->unsignedBigInteger('cus_auto_id');
            $table->bigInteger('account_credit_id')->default(1);
            $table->bigInteger('account_debit_id')->default(1);
       //    $table->foreign('cus_auto_id')->references('cus_auto_id')->on('chartofacc_sales_customers');

            $table->string('sr_invoice_no');
            $table->string('sr_invoice_description');
            $table->bigInteger('sr_tnx_id');
            $table->date('sr_issue_date');
            $table->string('sr_payment_terms');
            $table->date('sr_due_date');
            $table->date('sr_supply_date');
            $table->string('sr_payment_mean');
            $table->float('sr_total_amount',11,2)->default(0);
            $table->float('sr_discount_amount',11,2)->default(0);
            $table->float('sr_vat_amount',11,2)->default(0);
            $table->float('sr_grand_total_amount',11,2)->default(0);
            $table->boolean('sr_status')->default(1);
            $table->integer('created_by_id');
            $table->integer('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chartofacc_sales_records');
    }
};
