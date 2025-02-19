<?php

namespace App\Http\Controllers\DataServices;

use Carbon\Carbon;
use App\Models\JournalInfo;
use App\Models\JournalType;
use App\Models\BankAccountType;
use App\Models\ChartOfAccounts;
use App\Models\ChartofAccountType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChartOfAccountDataService
{

    /*
     ==========================================================================
     ======================== Chart Of Accounts Module ========================
     ==========================================================================
    */

    public function getChartOfAccountAllChartofAccountType()
    {
        return ChartofAccountType::select('acct_type_id', 'acct_type_name')->get();
    }

    public function getChartOfAccountAllBankAccountType()
    {
        return BankAccountType::where('ban_acc_type_status', 1)->select('bank_acct_type_id', 'ban_acc_type_name')->get();
    }

    public function storeChartOfAccountDetailsInformation($acct_type_id, $chart_of_acct_name, $chart_of_acct_number, $account_id, $acct_balance, $opening_date, $active_status, $created_by)
    {
        return ChartOfAccounts::insertGetId([
            'acct_type_id' => $acct_type_id,
            'chart_of_acct_name' => $chart_of_acct_name,
            'chart_of_acct_number' => $chart_of_acct_number,
            'account_id' => $account_id,
            'acct_balance' => $acct_balance,
            'opening_date' => $opening_date,
            'active_status' => $active_status ?? 1,
            // 'bank_id' => $bank_id,
            // 'bank_acct_type_id' => $bank_acct_type_id,
            // 'bank_acct_number' => $bank_acct_number,
            'created_by_id' => $created_by,
            'created_at' => Carbon::now()
        ]);
    }

    public function getAllChartOfAccountInformation()
    {
        return DB::table('chart_of_accounts')
        ->leftJoin('chartof_account_types', 'chartof_account_types.acct_type_id', '=', 'chart_of_accounts.acct_type_id')
      //  ->leftJoin('bank_names', 'bank_names.bn_auto_id', '=', 'chart_of_accounts.bank_id')
      //  ->leftJoin('bank_account_types', 'bank_account_types.bank_acct_type_id', '=', 'chart_of_accounts.bank_acct_type_id')
        ->select(
            'chartof_account_types.acct_type_name',
           // 'bank_names.bn_name',
          //  'bank_account_types.ban_acc_type_name',
            'chart_of_accounts.*'
        ) // Add specific columns as required here
        ->where('chart_of_accounts.is_closed', 0)
        ->get();
    }

    public function closeSingleChartOfAccountInformation($id)
    {
        return ChartOfAccounts::where('chart_of_acct_id', $id)->update([
            'is_closed' => 1,
        ]);
    }

    public function chartOfAccountInformationById($id){
        return ChartOfAccounts::where('chart_of_acct_id', $id)->first();
    }

    public function getListOfChartOfAccountInformationByAccountTypeId($acct_type_id){
        return ChartOfAccounts::select('chart_of_acct_name','chart_of_acct_number','chart_of_acct_id')->where('acct_type_id', $acct_type_id)->get();
    }

    public function updateChartOfAccountDetailsInformation($chart_of_acct_id, $acct_type_id, $chart_of_acct_name, $chart_of_acct_number, $account_id, $acct_balance, $opening_date, $active_status, $update_by){
        return ChartOfAccounts::where('chart_of_acct_id', $chart_of_acct_id)->update([
            'acct_type_id' => $acct_type_id,
            'chart_of_acct_name' => $chart_of_acct_name,
            'chart_of_acct_number' => $chart_of_acct_number,
            'account_id' => $account_id,
            'acct_balance' => $acct_balance,
            'opening_date' => $opening_date,
            'active_status' => $active_status == null ? 0 : 1,
            'updated_by_id' => $update_by,
            'updated_at' => Carbon::now()
        ]);
    }


    /*
     ==========================================================================
     ============================= Journal module =============================
     ==========================================================================
    */

    public function getAllJournalTypeInformationForDropDown(){
        return JournalType::select('jour_type_id', 'jour_type_name')->get();
    }

    public function getChartOfAccountsInformationForDropdown(){
        return ChartOfAccounts::where('is_closed', 0)->select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number')->get();;
    }

    public function getChartOfAccountsInformationByJournalTypeIdForDropdown($id){
        return ChartOfAccounts::where('is_closed', 0)->select('chart_of_acct_id', 'chart_of_acct_name', 'chart_of_acct_number')->get();;
    }

    public function getAllJournalInformation(){
        return JournalInfo::
          leftJoin('journal_types', 'journal_types.jour_type_id', '=', 'journal_infos.jour_type_id')
        ->leftJoin('chart_of_accounts', 'chart_of_accounts.chart_of_acct_id', '=', 'journal_infos.chart_of_acct_id')
        ->select(
            'journal_types.jour_type_name',
            'chart_of_accounts.chart_of_acct_name',
            'chart_of_accounts.chart_of_acct_number',
            'journal_infos.*'
        )->where('jour_status',1)
        ->get();
    }

    public function getAccountJournalInformationById($jourInfoId){
        return JournalInfo::where('jour_id', $jourInfoId)->first();
    }

    public function storeAllRequestedJournalInformation($jour_type_id, $chart_of_acct_id, $jour_name, $jour_code){
        if ($this->CheckJournalInfoIsExist($jour_type_id, $chart_of_acct_id, $jour_name)->count() > 0 && $this->CheckJournalCodeIsExist($jour_code) > 0) {
            return 0;
        } else {
            return JournalInfo::insert([
                'jour_type_id' => $jour_type_id,
                'chart_of_acct_id' => $chart_of_acct_id,
                'jour_name' => $jour_name,
                'jour_code' => $jour_code,
                'created_by_id' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }
    }


    public function checkJournalCodeIsExist($jour_code){
        return JournalInfo::where('jour_code', $jour_code)->get();
    }


    public function CheckJournalInfoIsExist($jour_type_id, $chart_of_acct_id, $jour_name, $exclude_jour_id = null){
        $query = JournalInfo::where('jour_type_id', $jour_type_id)
                ->where('chart_of_acct_id', $chart_of_acct_id)
                ->where('jour_name', $jour_name);

        if ($exclude_jour_id) {
            $query->where('jour_id', '!=', $exclude_jour_id);
        }
        return $query->get();
    }

    public function updateAllRequestedJournalInformation($jour_id, $jour_type_id, $chart_of_acct_id, $jour_name, $jour_code){
        if ($this->CheckJournalInfoIsExist($jour_type_id, $chart_of_acct_id, $jour_name, $jour_id)->count() > 0 && $this->CheckJournalCodeIsExist($jour_code) > 1) {
            return 0;
        } else {
            return JournalInfo::where('jour_id', $jour_id)->update([
                'jour_type_id' => $jour_type_id,
                'chart_of_acct_id' => $chart_of_acct_id,
                'jour_name' => $jour_name,
                'jour_code' => $jour_code,
                'updated_by_id' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
        }
    }


    public function deActivateRequestedJournalInfo($id){
        return JournalInfo::where('jour_id', $id)->update([
            'jour_status' => 0,
        ]);
    }












}
