<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartOfAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id('chart_of_acct_id');
            $table->string('chart_of_acct_name');
            $table->string('chart_of_acct_number')->nullable();

            $table->integer('account_id')->default(0); // sub account id
            $table->unsignedBigInteger('acct_balance')->default(0);
            $table->date('opening_date');
            $table->boolean('active_status')->default(true);
            $table->boolean('is_transaction')->default(false);
            $table->boolean('is_predefined')->default(false);
            $table->boolean('is_closed')->default(false);
            $table->string('bank_acct_number')->nullable();

            $table->unsignedBigInteger('acct_type_id'); // Asset, Liability, OE, Rev, Expense
            $table->unsignedBigInteger('bank_id');
            $table->unsignedBigInteger('bank_acct_type_id'); // Bank account type id for bank part

            $table->foreign('bank_id')->references('bn_auto_id')->on('bank_names');
            $table->foreign('acct_type_id')->references('acct_type_id')->on('chartof_account_types');
            $table->foreign('bank_acct_type_id')->references('bank_acct_type_id')->on('bank_account_types');

            $table->smallInteger('created_by_id');
            $table->smallInteger('updated_by_id')->nullable();
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
        Schema::dropIfExists('chart_of_accounts');
    }
}
