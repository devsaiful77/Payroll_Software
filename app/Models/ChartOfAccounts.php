<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccounts extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';

    protected $fillable = ['chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number', 'account_id', 'acct_balance', 'opening_date', 'active_status', 'is_transaction', 'is_predefined', 'bank_acct_number', 'bank_id', 'acct_type_id', 'bank_acct_type_id', 'created_by_id', 'updated_by_id', 'created_at', 'updated_at'];


    public function chartofAccountType(){
        return $this->belongsTo(ChartofAccountType::class, 'acct_type_id', 'acct_type_id');
    }

    public function bankName(){
        return $this->belongsTo(BankName::class, 'bank_id', 'bn_auto_id');
    }

    public function bankAccountType(){
        return $this->belongsTo(BankAccountType::class, 'bank_acct_type_id', 'bank_acct_type_id');
    }

}
