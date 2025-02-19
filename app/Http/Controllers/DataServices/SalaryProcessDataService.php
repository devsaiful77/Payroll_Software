<?php

namespace App\Http\Controllers\DataServices;

use Carbon\Carbon;
use App\Models\EmployeeInfo;
use App\Models\EmployeeBonus;
use App\Models\EmpMobileBill;
use App\Models\SalaryHistory;
use App\Models\SalarySheetUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\EmployeeFiscalYearDuration;
use App\Models\EmployeeMultiProjectWorkHistory;

class SalaryProcessDataService
{

    /*
     ==========================================================================
     =============== Employee Fiscal Year Process Related Methods =============
     ==========================================================================
    */

    public function getAnEmployeeSalaryRecordsByFiscalYear($emp_auto_id,$start_date,$end_date)
    {
        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
                    ->whereBetween('slh_salary_date', [$start_date,$end_date])
                    ->orderBy('slh_year', 'ASC')
                    ->orderBy('slh_month', 'ASC')->get();

    }


    public function getAnEmployeeUnpaidSalaryTotalAmountByFiscalYear($emp_auto_id,$start_date,$end_date)
    {
        return SalaryHistory::where('emp_auto_id', $emp_auto_id)
                ->whereBetween('slh_salary_date', [$start_date,$end_date])
                ->where('Status', 0)
                ->sum('slh_total_salary');
    }

    public function getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
        ->whereBetween('slh_salary_date', [$start_date,$end_date])
        ->sum('slh_iqama_advance');

    }
    public function getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
        ->whereBetween('slh_salary_date', [$start_date,$end_date])
        ->sum('slh_other_advance');

    }

    public function getAnEmployeeTotalAmountContributeToCPFByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
        ->whereBetween('slh_salary_date', [$start_date,$end_date])
        ->sum('slh_cpf_contribution');

    }

    public function getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($emp_auto_id,$start_date,$end_date)
    {

        return  SalaryHistory::where('emp_auto_id', $emp_auto_id)
        ->whereBetween('slh_salary_date', [$start_date,$end_date])
        ->sum('slh_saudi_tax');

    }




    /*
     ==========================================================================
     ============================= Employee Salary Single Record ==============
     ==========================================================================
    */

    public function checkAnEmployeeSalaryIsAlreadyPaid($emp_auto_id, $month, $year)
    {
        return SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('slh_month', $month)->where('slh_year', $year)
                              ->where('Status', 1)->count() == 1 ? true : false;
    }

    public function getAnEmployeeSalaryRecordBySalaryHistoryAutoId($slh_auto_id)
    {


        return   SalaryHistory::select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','salary_histories.basic_amount','salary_histories.hourly_rent','sponsors.spons_name','countries.country_name','employee_categories.catg_name','project_infos.proj_name',
        'salary_histories.multProject','salary_histories.slh_auto_id','salary_histories.slh_total_hours','salary_histories.slh_total_working_days','salary_histories.slh_total_overtime','salary_histories.slh_overtime_amount','salary_histories.food_allowance','salary_histories.slh_total_salary','salary_histories.slh_cpf_contribution',
        'salary_histories.slh_iqama_advance','salary_histories.slh_all_include_amount','salary_histories.mobile_allowance','salary_histories.medical_allowance','salary_histories.slh_saudi_tax','salary_histories.slh_other_advance','salary_histories.slh_food_deduction','salary_histories.Status','salary_histories.slh_month','salary_histories.slh_year')
        ->where("salary_histories.slh_auto_id", $slh_auto_id)
        ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
        ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
        ->first();
    }
    public function getAnEmployeeBankInfoWithSalaryRecordForUpdateSalaryPaidMethodBySalaryHistoryAutoId($slh_auto_id)
    {

        return   SalaryHistory:: where("salary_histories.slh_auto_id", $slh_auto_id)
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('employee_bank_details', 'employee_infos.emp_auto_id', '=', 'employee_bank_details.emp_auto_id')
            ->first();
    }

    public function getAnEmployeeSalaryRecord($emp_auto_id, $month, $year)
    {
        return SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('slh_month', $month)->where('slh_year', $year)->first();
    }
    public function getAnEmployeeInfoWithSalaryHistory($emp_auto_id, $month, $year)
    {
        return SalaryHistory::with('employee')->where('emp_auto_id', $emp_auto_id)->where('slh_month', $month)->where('slh_year', $year)->first();
    }
    public function getAnEmployeeLastMonthSalaryRecord($emp_auto_id)
    {

        return  EmployeeInfo::
             select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','salary_histories.basic_amount','salary_histories.hourly_rent','sponsors.spons_name','countries.country_name','employee_categories.catg_name','project_infos.proj_name',
             'salary_histories.multProject','salary_histories.slh_total_hours','salary_histories.slh_total_working_days','salary_histories.slh_all_include_amount','salary_histories.slh_total_overtime','salary_histories.slh_overtime_amount','salary_histories.food_allowance','salary_histories.slh_total_salary','salary_histories.slh_cpf_contribution',
             'salary_histories.slh_iqama_advance','salary_histories.slh_saudi_tax','salary_histories.slh_other_advance','salary_histories.slh_food_deduction','salary_histories.house_rent','salary_histories.mobile_allowance','salary_histories.medical_allowance',
            'salary_histories.local_travel_allowance','salary_histories.conveyance_allowance','salary_histories.others','salary_histories.slh_bonus_amount','salary_histories.Status','salary_histories.slh_month','salary_histories.slh_year')
            ->where("salary_histories.emp_auto_id", $emp_auto_id)
            ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('salary_histories.slh_auto_id', 'DESC')
            ->first();


    }

    public function deleteAnEmployeeUnpaidSalaryRecordBySalaryHistoryAutoId($slh_auto_id){
        return SalaryHistory::where('slh_auto_id',$slh_auto_id)->where('Status',0)->delete();
    }




    /*
     ==========================================================================
     ====================== Employee Salary Multiple Record ===================
     ==========================================================================
    */
    public function getAnEmployeeSalaryHistorySummary($employeeId, $year)
    {
        if($year != null){
            return  SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->orderBy('slh_month', 'ASC')->get();

        }else {
            return  SalaryHistory::where('emp_auto_id', $employeeId)->orderBy('slh_month', 'ASC')->get();
        }
    }

    public function getAnEmployeeSalaryRecordsByPaidUnpaidStatus($emp_auto_id, $year,$salary_status)
    {

        if(!is_null($year) &&  !is_null($salary_status)){
            return  SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('slh_year', $year)->where('Status', $salary_status)->orderBy('slh_month', 'ASC')->get();
        }else if(!is_null($year) && is_null($salary_status)){
            return  SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('slh_year', $year)->orderBy('slh_month', 'ASC')->get();
        }
        else if(is_null($year) && !is_null($salary_status)){
            return  SalaryHistory::where('emp_auto_id', $emp_auto_id)->where('Status', $salary_status)->orderBy('slh_year','ASC')->orderBy('slh_month', 'ASC')->get();
        }
        else {
            return  SalaryHistory::where('emp_auto_id', $emp_auto_id)->orderBy('slh_year','ASC')->orderBy('slh_month', 'ASC')->get();
           }
    }




     // Employee Details Information With Monthly Working Record for Salary Processing
     public function getAnEmployeeDetailsWithMonthlyWorkRecordForSalaryProcessing($employee_id,$user_branch_office_id,$month, $year)
     {
             return    EmployeeInfo::  where("employee_infos.employee_id", $employee_id)
                 ->where("employee_infos.branch_office_id", $user_branch_office_id)
                 ->where('monthly_work_histories.month_id', $month)
                 ->where('monthly_work_histories.year_id', $year)
                 ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                 ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                 ->first();

     }

     public function getListOfEmployeesWithWorkRecordsForProcessingSalary($month, $year, $project_id,$sponsor_ids,$user_branch_office_id)
     {
             return EmployeeInfo:: where('monthly_work_histories.month_id', $month)
                 ->where("employee_infos.branch_office_id", $user_branch_office_id)
                 ->where('monthly_work_histories.year_id', $year)
                 ->where('monthly_work_histories.work_project_id', $project_id)
                 ->whereIn('employee_infos.sponsor_id',$sponsor_ids)
                 ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                 ->leftjoin('monthly_work_histories', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
                 ->get();

     }


     public function searchAnEmployeesInfoWithInAFiscalYearPaidSalaryRecordsForListView($employee_id, $start_date, $end_date, $branch_office_id)
     {
             return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                 ->where('salary_histories.Status', 1) // salary status paid
                 ->where('salary_histories.branch_office_id', $branch_office_id)
                 ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                 ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                 ->whereBetween('slh_salary_date', [$start_date, $end_date])
                 ->where('employee_infos.employee_id',$employee_id)
                 ->orderBy('slh_auto_id', 'DESC')
                 ->get();
     }


     // Salary Pending Or Paid Emp list for Paid Unpaid

    // public function searchEmployeesSalaryRecordsWithPaidUnpaidStatus($SponsId, $proj_id, $fromMonth, $toMonth, $fromYear,$toYear,$salary_status)
    // {
    //     if ($SponsId != 0 && $proj_id != 0) {

    //         return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', $salary_status)
    //             ->where('sponsor_id', $SponsId)
    //             ->where('salary_histories.project_id', $proj_id)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->whereIn('slh_month', [$fromMonth, $toMonth])
    //             ->whereIn('slh_year', [$fromYear, $toYear])
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }
    //     else  if ($SponsId != 0 && $proj_id == 0) {
    //         return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //         ->where('salary_histories.Status', $salary_status)
    //         ->where('sponsor_id', $SponsId)
    //         ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //         ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //         ->whereIn('slh_month', [$fromMonth, $toMonth])
    //         ->whereIn('slh_year', [$fromYear, $toYear])
    //         ->orderBy('employee_id', 'ASC')
    //         ->get();
    //     }
    //     else  if ($SponsId == 0 && $proj_id != 0) {
    //         return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', $salary_status)
    //             ->where('salary_histories.project_id', $proj_id)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->whereIn('slh_month', [$fromMonth, $toMonth])
    //             ->whereIn('slh_year', [$fromYear, $toYear])
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }
    //     else {
    //         return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
    //             ->where('salary_histories.Status', $salary_status)
    //             ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
    //             ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
    //             ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
    //             ->whereIn('slh_month', [$fromMonth, $toMonth])
    //             ->whereIn('slh_year', [$fromYear, $toYear])
    //             ->orderBy('employee_id', 'ASC')
    //             ->get();
    //     }
    // }

    public function searchListOfEmployeesThoseSalaryAlreadyPaidForListViewByProjectAndSponsor($SponsId, $proj_id, $fromMonth, $toMonth, $fromYear,$toYear,$branch_office_id)
    {
        if ($SponsId != 0 && $proj_id != 0) {

            return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                ->where('salary_histories.Status', 1) // salary status paid
                ->where('salary_histories.branch_office_id', $branch_office_id)
                ->where('sponsor_id', $SponsId)
                ->where('salary_histories.project_id', $proj_id)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
        else  if ($SponsId != 0 && $proj_id == 0) {
            return  SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
            ->where('salary_histories.Status', 1) // salary status paid
            ->where('salary_histories.branch_office_id', $branch_office_id)
            ->where('sponsor_id', $SponsId)
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
            ->whereIn('slh_month', [$fromMonth, $toMonth])
            ->whereIn('slh_year', [$fromYear, $toYear])
            ->orderBy('employee_id', 'ASC')
            ->get();
        }
        else  if ($SponsId == 0 && $proj_id != 0) {
            return  SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                ->where('salary_histories.Status', 1) // salary status paid
                ->where('salary_histories.branch_office_id', $branch_office_id)
                ->where('salary_histories.project_id', $proj_id)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
        else {
            return SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                ->where('salary_histories.Status', 1) // salary status paid
                ->where('salary_histories.branch_office_id', $branch_office_id)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
    }

    public function getAnEmployeeSalaryRecordWithJoinQuery($employee_id, $status,$branch_office_id)
    {

        return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type')
            ->where('salary_histories.Status', $status)
            ->where('employee_infos.employee_id', $employee_id)
            ->where('salary_histories.branch_office_id',$branch_office_id)
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
            ->orderBy('employee_id', 'ASC')
            ->get();
    }

    public function searchAnEmployeeUnPaidSalaryRecordsWithEmloyeeInfoForListView($employee_id,$branch_office_id)
    {
       return   SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type')
       ->where('salary_histories.Status', 0) // salary status = 0 = pending
       ->where('employee_infos.employee_id', $employee_id)
       ->where('salary_histories.branch_office_id',$branch_office_id)
       ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
       ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
       ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
       ->orderBy('employee_id', 'ASC')
       ->get();

    }


         // Salary Pending Or Paid Emp list for Paid Unpaid
    public function searchListOfEmployeesThoseSalaryRecordsStatusIsUnpaidForListViewByProjectAndSponsor($SponsId, $proj_id, $fromMonth, $toMonth, $fromYear,$toYear,$branch_office_id)
    {
        if ($SponsId != 0 && $proj_id != 0) {

            return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
                ->where('salary_histories.Status', 0) // salary status unpaid
                ->where('salary_histories.branch_office_id', $branch_office_id)
                ->where('sponsor_id', $SponsId)
                ->where('salary_histories.project_id', $proj_id)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
        else  if ($SponsId != 0 && $proj_id == 0) {
            return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
            ->where('salary_histories.Status', 0) // salary status unpaid
            ->where('salary_histories.branch_office_id', $branch_office_id)
            ->where('sponsor_id', $SponsId)
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
            ->whereIn('slh_month', [$fromMonth, $toMonth])
            ->whereIn('slh_year', [$fromYear, $toYear])
            ->orderBy('employee_id', 'ASC')
            ->get();
        }
        else  if ($SponsId == 0 && $proj_id != 0) {
            return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
            ->where('salary_histories.Status', 0) // salary status unpaid
            ->where('salary_histories.branch_office_id', $branch_office_id)
                ->where('salary_histories.project_id', $proj_id)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
        else {
            return $pendingSalary = SalaryHistory::with('employee.category', 'month', 'employee.sponsor',  'employee.type', 'employee.project')
            ->where('salary_histories.Status', 0) // salary status unpaid
            ->where('salary_histories.branch_office_id', $branch_office_id)
                ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->whereIn('slh_month', [$fromMonth, $toMonth])
                ->whereIn('slh_year', [$fromYear, $toYear])
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
    }



    public function getEmployeeSalaryHistory($projectId, $sponserId, $month, $salaryYear, $salaryStatus)
    {

        if ($projectId  <= 0 && $sponserId <= 0) {

            return  $salaryReport = EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();

        } else if ($projectId  <= 0 && $sponserId > 0) {

            return  $salaryReport = EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
              //  dd($projectId, $sponserId, $month, $salaryYear, $salaryStatus,count($salaryReport));

        } else if ($projectId  > 0 && $sponserId <= 0) {
            return $salaryReport = EmployeeInfo::where('salary_histories.project_id', $projectId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
        } else if ($projectId  > 0 && $sponserId > 0) {
            return $salaryReport = EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
                ->where('salary_histories.project_id', $projectId)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                ->orderBy('employee_id')
                ->get();
        }
    }
    public function getSponsorwiseEmployeeSalaryReportForSaudi($sponserIdList, $month, $salaryYear)
    {
            return  $salaryReport = EmployeeInfo::whereIn("employee_infos.sponsor_id", $sponserIdList)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                 ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();


    }


    public function getEmployeeSalaryProjectAndTrade($projectId, $trades, $month, $salaryYear, $salaryStatus)
    {

        return $salaryReport = EmployeeInfo:://where("employee_infos.sponsor_id", $sponserId)
        whereIn('salary_histories.project_id', $projectId)
        ->whereIn('employee_infos.designation_id', $trades)
        ->where('salary_histories.slh_month', $month)
        ->where('salary_histories.slh_year', $salaryYear)
        ->whereIn('salary_histories.Status', $salaryStatus)
        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
        ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
        ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
        ->orderBy('employee_id')
        ->get();

    }

    public function getEmployeeSalaryHistoryWithProjectAndEmployeeType($projectId, $empType, $isHourlyEmp, $month, $salaryYear, $salaryStatus)
    {

        if ($projectId  > 0 && $empType > 0) {
            return $salaryReport = EmployeeInfo::where("salary_histories.project_id", $projectId)
                ->where('employee_infos.emp_type_id', $empType)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->where('employee_infos.hourly_employee', $isHourlyEmp)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();
        } else if ($empType > 0) {
            return $salaryReport = EmployeeInfo::where('employee_infos.emp_type_id', $empType)
                ->where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->where('employee_infos.hourly_employee', $isHourlyEmp)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();
        }
    }


  // Basic Salary Direct and Basic Salary Hourly and Indirect Employee Salary Report
  public function getEmployeeSalaryHistoryWithProjectAndEmployeeTypeBasicAndHourlyEmployee($projectId, $empType, $hourly_employee, $month, $salaryYear, $salaryStatus)
  {

            if ($projectId  != null && $empType != null)
            {
                if($empType == 3){
                    return  EmployeeInfo:: where('employee_infos.hourly_employee', null)
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->where('salary_histories.project_id', $projectId)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->orderBy('employee_id')
                    ->get();
                }
                 return   EmployeeInfo:: where('employee_infos.hourly_employee', $hourly_employee)
                    ->where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->where('employee_infos.emp_type_id', $empType)
                    ->where('salary_histories.project_id', $projectId)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->orderBy('employee_id')
                    ->get();
            }
            else if ($projectId  != null && $empType == null){
                 return $salaryReport = EmployeeInfo::
                    where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->where('salary_histories.project_id', $projectId)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->orderBy('employee_id')
                    ->get();
            }
            else if ($projectId  == null && $empType != null)
            {
                    if($empType == 3){
                        return  EmployeeInfo:: where('employee_infos.hourly_employee', null)
                        ->where('salary_histories.slh_month', $month)
                        ->where('salary_histories.slh_year', $salaryYear)
                        ->whereIn('salary_histories.Status', $salaryStatus)
                        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                        ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                        ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                        ->orderBy('employee_id')
                        ->get();
                    }
                    return   EmployeeInfo:: where('employee_infos.hourly_employee', $hourly_employee)
                        ->where('salary_histories.slh_month', $month)
                        ->where('salary_histories.slh_year', $salaryYear)
                        ->whereIn('salary_histories.Status', $salaryStatus)
                        ->where('employee_infos.emp_type_id', $empType)
                        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                        ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                        ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                        ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                        ->orderBy('employee_id')
                        ->get();
            }
            else   {
                 return $salaryReport = EmployeeInfo::
                    where('salary_histories.slh_month', $month)
                    ->where('salary_histories.slh_year', $salaryYear)
                    ->whereIn('salary_histories.Status', $salaryStatus)
                    ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                    ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                    ->orderBy('employee_id')
                    ->get();
            }


  }

    public function getEmployeeSalaryHistoryWithProjectAndEmployeeJobStatus($projectId,$month, $salaryYear, $salaryStatus)
    {

        if(is_null($salaryStatus)){
            return   $salaryReport = EmployeeInfo::where("salary_histories.project_id", $projectId)
           ->where('salary_histories.slh_month', $month)
           ->where('salary_histories.slh_year', $salaryYear)
           ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
           ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
           ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
           ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
           ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
           ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
           ->orderBy('employee_id')
           ->get();
       }else {

       return   $salaryReport = EmployeeInfo::where("salary_histories.project_id", $projectId)
           ->where('salary_histories.slh_month', $month)
           ->where('salary_histories.slh_year', $salaryYear)
           ->where('salary_histories.Status', $salaryStatus)
           ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
           ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
           ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
           ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
           ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
           ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
           ->orderBy('employee_id')
           ->get();
       }
    }



    public function getEmployeeSalaryReportThoseAreSalaryHold($project_ids, $month, $year)
    {

        if(is_null($project_ids)  ){
            return   EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year)
                ->where('employee_infos.salary_status','>=', 2)  // 1 = active salary otherwise hold salary
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();

        }else {
            return   EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year)
                ->whereIn('salary_histories.project_id', $project_ids)
                ->where('employee_infos.salary_status','>=', 2)
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();
        }



    }




    public function getOfficeStaffEmployeeSalaryReport($project_ids, $month, $year)
    {

        if(is_null($project_ids)  ){
            return   EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year)
                ->where('employee_details.staff_employee',1)  // 1 = active salary otherwise hold salary
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();

        }else {
            return   EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $year)
                ->whereIn('salary_histories.project_id', $project_ids)
                ->where('employee_details.staff_employee',1)  // 1 = active salary otherwise hold salary
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();

        }



    }

    public function getAnEmployeeDetailsWithSalaryRecordForMultiProjectSalaryProcess($emp_auto_id, $month, $salaryYear)
    {

        return  EmployeeInfo::where("employee_infos.emp_auto_id", $emp_auto_id)
            ->where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $salaryYear)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->first();
    }





    public function getMultipleEmployeeIdBaseSalaryHistory($allEmplId, $month, $salaryYear, $salaryStatus,$branch_office_id)
    {

        if(is_null($salaryStatus)){
            return  $salaryReport = EmployeeInfo::where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $salaryYear)
            ->whereIn('employee_infos.employee_id', $allEmplId)
            ->where('salary_histories.branch_office_id',$branch_office_id)
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->orderBy('employee_id')
            ->get();
        }else {
            return  $salaryReport = EmployeeInfo::where('salary_histories.slh_month', $month)
                ->where('salary_histories.slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)
                ->whereIn('employee_infos.employee_id', $allEmplId)
                ->where('salary_histories.branch_office_id',$branch_office_id)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
                ->orderBy('employee_id')
                ->get();
            }
    }


    /*
     ==========================================================================
     ================ Employee Salary Related Summation =======================
     ==========================================================================
    */


    public function getAnEmployeeJoiningMonthAllIncludedTotalSalaryAmount($emp_auto_id,$is_hourly_emp)
    {
       $arecord = DB::select('CALL getAnEmployeeJoiningSalaryAllIncludedByEmpAutoId(?)',array($emp_auto_id));
       return count($arecord) > 0 ? ($is_hourly_emp ? $arecord[0]->hourly_rent : $arecord[0]->total_salary) : 0;
    }

    public function getAnEmployeeSalaryTotalAmount($employeeId, $year)
    {
        return  SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_total_salary');
    }
    public function getAnEmployeeMonltySalaryTotalAmount($emp_auto_id, $month, $salaryYear)
    {
        $record =  SalaryHistory::where('slh_month', $month)->where('emp_auto_id', $emp_auto_id)->where('slh_year', $salaryYear)->sum('slh_total_salary');
        if ($record == null) {
            return 0;
        } else {
            $record->slh_total_salary;
        }
    }
    public function getAnEmployeeMonltySalaryIqamaAmount($emp_auto_id, $month, $salaryYear)
    {
        return SalaryHistory::where('slh_month', $month)->where('emp_auto_id', $emp_auto_id)->where('slh_year', $salaryYear)->sum('slh_iqama_advance');

    }

    public function getAnEmployeeTotalUnPaidSalary($employeeId, $year)
    {
        if ($year == null || $year == '') {
            return SalaryHistory::where('emp_auto_id', $employeeId)
                ->where('Status', 0)
                ->sum('slh_total_salary');
        } else {
            return SalaryHistory::where('emp_auto_id', $employeeId)
                ->where('slh_year', $year)
                ->where('Status', 0)
                ->sum('slh_total_salary');
        }
    }

    public function totalAdvance($employeeId, $year)
    {
        return $employeeTotalSalarySummary = SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_saudi_tax');
    }

    public function TotalIqamaRenewal($employeeId, $year)
    {
        return $employeeTotalSalarySummary = SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_iqama_advance');
    }

    public function getAnEmployeeTotalAmountOfSautiTaxDeductionFromSalaryTotalAdvance($employeeId,$year)
    {
        if( is_null($year) == true){
            return   SalaryHistory::where('emp_auto_id', $employeeId)
            ->sum('slh_saudi_tax');
        }else {
        return  SalaryHistory::where('emp_auto_id', $employeeId)
                            ->where('slh_year', $year)
                            ->sum('slh_saudi_tax');
        }
    }

    public function getTotalAmountOfIqamaExpenseDeductionFromSalary($employeeId,$salary_status)
    {

        if( is_null($salary_status) == false){
            return   SalaryHistory::where('emp_auto_id', $employeeId)->where('salary_histories.Status', $salary_status)->sum('slh_iqama_advance');
        }else {
         return   SalaryHistory::where('emp_auto_id', $employeeId)->sum('slh_iqama_advance');
        }
    }

    public function getTotalAmountOfIqamaRenewalDeductionFromSalaryBySponsorId($sponsor_id,$month,$year)
    {

        if( is_null($sponsor_id)){
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $year)
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
             ->sum('slh_iqama_advance');

        }else {
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $year)
             ->where('employee_infos.sponsor_id', $sponsor_id)
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
             ->sum('slh_iqama_advance');
         }
    }

    public function getSalaryIqamaAdvanceTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return  $iqamaAmount = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_iqama_advance');
    }

    public function getTotalAmountOfAdvanceDeductionFromSalary($salaryYear,$month){
        return $amount = SalaryHistory::
         where('slh_month', $month)->where('slh_year', $salaryYear)
         ->sum('slh_other_advance');
     }

     public function getTotalAmountOfAdvanceDeductionFromSalaryByYearToYear($fromYear){
        return SalaryHistory::where('slh_year',$fromYear)->sum('slh_other_advance');
     }

    public function getTotalAmountOfCPFContributionFromSalary($employeeId,$year)
    {
        if(is_null($year)){
             return  SalaryHistory::where('emp_auto_id', $employeeId)->sum('slh_cpf_contribution');
         }else {
            return  SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_cpf_contribution');
         }

    }

    public function TotalHours($employeeId, $year)
    {
        return $employeeTotalSalarySummary = SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_total_hours');
    }

    public function slh_saudi_tax($employeeId, $year)
    {
        return $employeeTotalSalarySummary = SalaryHistory::where('emp_auto_id', $employeeId)->where('slh_year', $year)->sum('slh_saudi_tax');
    }

    public function getSalarySaudiTaxTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return  $totalSaudiTax = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_saudi_tax');
    }

    // Month, Yearly Paid and Unpaid Salary Total Amount

    public function getSalaryTotalAmount($project_id, $month, $salaryYear, $salaryStatus)
    {
        if ($project_id <= 0) {
            return $allSalaryAmount = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_total_salary');
        } else {
            return $allSalaryAmount = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)->where('project_id', $project_id)->sum('slh_total_salary');
        }
    }




    public function getSalaryMonthTotalHours($project_id, $month, $salaryYear, $salaryStatus)
    {
        if ($project_id <= 0) {

            return     $totalHours = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)->sum('slh_total_hours');
        } else {
            return     $totalHours = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)->where('project_id', $project_id)->sum('slh_total_hours');
        }
    }

    public function getMonthlyTotalHoursByProject($project_id, $month, $salaryYear)
    {
        if ($project_id <= 0) {

            return     $totalHours = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                        ->sum('slh_total_hours');
        } else {
            return     $totalHours = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                        ->where('project_id', $project_id)->sum('slh_total_hours');
        }
    }

    public function getMonthlyTotalOverTimeHoursByProject($project_id, $month, $salaryYear)
    {
        if ($project_id <= 0) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->sum('slh_total_overtime');
        } else {
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('project_id', $project_id)->sum('slh_total_overtime');
        }
    }
    public function getSalaryMonthTotalOvertimeHours($project_id, $month, $salaryYear, $salaryStatus)
    {
        if ($project_id <= 0) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_total_overtime');
        } else {
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.Status', $salaryStatus)->where('project_id', $project_id)->sum('slh_total_overtime');
        }
    }


    public function getSalaryMonthTotalOvertimeAmount($month, $salaryYear, $salaryStatus)
    {
        return SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_overtime_amount');
    }
    public function getSalaryMonthFoodAllowanceTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return   SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('food_allowance');
    }
    public function getSalaryMonthEmployeeCPFTotalAmount($month, $salaryYear, $salaryStatus)
    {
        return     $totalContribution = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_company_contribution');
    }

    public function getSalaryMonthOtherAdvanceTotalAmount($month, $salaryYear, $salaryStatus)
    {
        if( is_null($salaryStatus) == false){
            return  $totalOtherAdvance = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->sum('slh_other_advance');
        }else {
            return  $totalOtherAdvance = SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)->where('salary_histories.Status', $salaryStatus)->sum('slh_other_advance');
        }
    }
    public function getTotalAmountOfOtherAdvanceDeductionFromSalary($employeeId,$salary_status)
    {
        if( is_null($salary_status) == false){
            return   SalaryHistory::where('emp_auto_id', $employeeId)->where('salary_histories.Status', $salary_status)->sum('slh_other_advance');
        }else {
            return   SalaryHistory::where('emp_auto_id', $employeeId)->sum('slh_other_advance');
        }
    }

    public function getAProjectTotalAmountOfSalary($project_id, $month, $salaryYear)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                    ->count('slh_all_include_amount');
        } else {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('project_id', $project_id)
            ->sum('slh_all_include_amount');
        }
    }




    /*
     ==========================================================================
     ================ Employee Salary History Count Section ===================
     ==========================================================================
    */


    public function countMonthlySalryTotalEmployees($project_id, $month, $salaryYear)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                 ->count('salary_histories.emp_auto_id');
        } else {
            return   SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                 ->where('project_id', $project_id)->count('salary_histories.emp_auto_id');
        }
    }
    public function countMonthlySalryTotalBasicSalaryOrHourlyEmployes($project_id, $month, $salaryYear,$is_hourly_employee)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                    ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                    ->where('employee_infos.hourly_employee',$is_hourly_employee)
                    ->count('salary_histories.emp_auto_id');
        } else {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
            ->leftjoin('employee_infos', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->where('employee_infos.hourly_employee',$is_hourly_employee)
            ->where('salary_histories.project_id', $project_id)
            ->count('salary_histories.emp_auto_id');

        }
    }

    public function countMonthlySalryPaidTotalEmployes($project_id, $month, $salaryYear)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                    ->where('salary_histories.Status', 1)
                    ->count('salary_histories.emp_auto_id');
        } else {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('Status', 1)
            ->where('project_id', $project_id)
            ->count('emp_auto_id');
        }
    }
    public function countMonthlySalryUnPaidTotalEmployes($project_id, $month, $salaryYear)
    {
        if (is_null($project_id)) {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
                    ->where('salary_histories.Status', 0)
                    ->count('salary_histories.emp_auto_id');
        } else {
            return  SalaryHistory::where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('Status', 0)
            ->where('project_id', $project_id)
            ->count('emp_auto_id');
        }
    }



    /*
     ==========================================================================
     ================  Salary Sheet Upload Related ============================
     ==========================================================================
    */
    public function getAllSalarySheet()
    {
        return $allSheet =  SalarySheetUpload::orderBy('ss_auto_id', 'DESC')->get();
    }

    /*
     ==========================================================================
     ================ Udpate Operation ========================================
     ==========================================================================
    */
    public function updateAnEmployeeMonthlySalaryRecord($salaryHistoryAutoId, $anEmployee, $month, $salaryYear)
    {

        if ($salaryHistoryAutoId > 0) {

            return  $insert = SalaryHistory::where('slh_auto_id', $salaryHistoryAutoId)->update([
                'emp_auto_id' => $anEmployee->emp_auto_id,
                'basic_amount' => $anEmployee->basic_amount,
                'basic_hours' => $anEmployee->basic_hours,
                'house_rent' => $anEmployee->house_rent,
                'hourly_rent' => $anEmployee->hourly_rent,
                'mobile_allowance' => $anEmployee->mobile_allowance,
                'medical_allowance' => $anEmployee->medical_allowance,
                'local_travel_allowance' => $anEmployee->local_travel_allowance,
                'conveyance_allowance' => $anEmployee->conveyance_allowance,
                'food_allowance' =>  $anEmployee->food_allowance,
                'others' => $anEmployee->others1,
                'slh_total_overtime' => $anEmployee->overtime,
                'slh_overtime_amount' =>  $anEmployee->slh_overtime_amount,
                /* New field in Salary History */
                'slh_total_salary' => $anEmployee->gross_salary,
                'slh_total_hours' => $anEmployee->total_hours,
                'slh_total_working_days' => $anEmployee->total_work_day,
                'slh_month' => $month,
                'slh_year' => $salaryYear,
                'slh_cpf_contribution' => $anEmployee->cpf_contribution,
                'slh_saudi_tax' => $anEmployee->saudi_tax,
                'slh_company_contribution' => 0,
                'slh_iqama_advance' => $anEmployee->iqama_adv_inst_amount,
                'slh_other_advance' => $anEmployee->other_adv_inst_amount,
                'slh_bonus_amount' => $anEmployee->bonus_amount,
                'slh_food_deduction' => $anEmployee->slh_food_deduction,
                'slh_all_include_amount' => $anEmployee->slh_all_include_amount,
                'project_id' => $anEmployee->work_project_id,
                'multProject' => $anEmployee->work_multi_project,
                'slh_salary_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ]);
        } else {
            return $insert = SalaryHistory::insertGetId([
                'emp_auto_id' => $anEmployee->emp_auto_id,
                'basic_amount' => $anEmployee->basic_amount,
                'basic_hours' => $anEmployee->basic_hours,
                'house_rent' => $anEmployee->house_rent,
                'hourly_rent' => $anEmployee->hourly_rent,
                'mobile_allowance' => $anEmployee->mobile_allowance,
                'medical_allowance' => $anEmployee->medical_allowance,
                'local_travel_allowance' => $anEmployee->local_travel_allowance,
                'conveyance_allowance' => $anEmployee->conveyance_allowance,
                'food_allowance' => $anEmployee->food_allowance,
                'others' => $anEmployee->others1,
                'slh_total_overtime' => $anEmployee->overtime,
                'slh_overtime_amount' => $anEmployee->slh_overtime_amount,
                'slh_total_salary' => $anEmployee->gross_salary,
                'slh_total_hours' => $anEmployee->total_hours,
                'slh_total_working_days' => $anEmployee->total_work_day,
                'slh_month' => $month,
                'slh_year' => $salaryYear,
                'slh_cpf_contribution' => $anEmployee->cpf_contribution,
                'slh_saudi_tax' => $anEmployee->saudi_tax,
                'slh_company_contribution' => 0,
                'slh_iqama_advance' => $anEmployee->iqama_adv_inst_amount,
                'slh_other_advance' => $anEmployee->other_adv_inst_amount,
                'slh_bonus_amount' => $anEmployee->bonus_amount,
                'slh_food_deduction' => $anEmployee->slh_food_deduction,
                'slh_all_include_amount' => $anEmployee->slh_all_include_amount,
                'slh_salary_date' => Carbon::now(),
                'Status' => 0,
                'project_id' => $anEmployee->work_project_id,
                'multProject' => $anEmployee->work_multi_project,
                'branch_office_id' => $anEmployee->branch_office_id,
                'created_at' => Carbon::now(),

            ]);
        }
    }

    public function updateAnEmployeeMonthlySalaryRecordUpdateSalaryStatusAsPaidBySalaryHistoryAutoId($slh_auto_id,$emp_auto_id,$slh_all_include_amount,$new_food_amount,
    $saudi_tax,$new_other_advance,$new_iqama_advance,$new_receivable_total_salary,$updated_by,$salary_paid__status)
    {
            return  SalaryHistory::where('slh_auto_id', $slh_auto_id)->where('emp_auto_id', $emp_auto_id)->update([
                'food_allowance' =>  $new_food_amount,
                'slh_total_salary' => $new_receivable_total_salary,
                'slh_saudi_tax' => $saudi_tax,
                'slh_iqama_advance' => $new_iqama_advance,
                'slh_other_advance' => $new_other_advance,
                'slh_all_include_amount' => $slh_all_include_amount,
                'Status' => $salary_paid__status,
                'updated_at' => Carbon::now(),
                'updated_by' => $updated_by

            ]);
    }



    public function calculateFoodAllowance($totalWorkingDay, $monthlyFoodAllowance)
    {

        if ($monthlyFoodAllowance > 0) {

            if($totalWorkingDay >= 1 && $totalWorkingDay <= 30)
              return (($monthlyFoodAllowance / 30) * $totalWorkingDay);
            else if($totalWorkingDay > 30)
              return 300;
            else
            return 0;
            /*
            if ($totalWorkingDay >= 24) {
                return $monthlyFoodAllowance;
            } elseif ($totalWorkingDay >= 18) {
                return (($monthlyFoodAllowance / 30) * ($totalWorkingDay + 3));
            } elseif ($totalWorkingDay >= 12) {
                return (($monthlyFoodAllowance / 30) * ($totalWorkingDay + 2));
            } elseif ($totalWorkingDay >= 6) {
                return (($monthlyFoodAllowance / 30) * ($totalWorkingDay + 1));
            } else {
                return (($monthlyFoodAllowance / 30) * $totalWorkingDay);
            } */

        } else
            return 0;
    }
    public function calculateOvertimeHoursAndAmount($anEmployee)
    {

        $over_amount = 0;
        $total_amount = 0;
        if ($anEmployee->hourly_employee == true && $anEmployee->emp_type_id == 1) { // direct emp hourly
            $over_amount = ($anEmployee->overtime * $anEmployee->hourly_rent);
            $total_amount = ($anEmployee->total_hours * $anEmployee->hourly_rent) + $over_amount;
        } elseif ($anEmployee->emp_type_id == 1 ){ // $anEmployee->basic_hours > 0 && $anEmployee->basic_amount > 0) { // direct emp basic
            $over_amount = ($anEmployee->overtime * ($anEmployee->hourly_rent * 1.5));
            $total_amount = (($anEmployee->basic_amount / 30) * $anEmployee->total_work_day)  + $over_amount;
        } elseif ( $anEmployee->emp_type_id == 2 ){ //$anEmployee->basic_amount > 0) {  // indirect emp
            $anEmployee->allOthers = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->others1);
            $over_amount = ($anEmployee->overtime * $anEmployee->hourly_rent);
            $total_amount = (($anEmployee->basic_amount / 30)) * ($anEmployee->total_work_day) + $over_amount;
        }

        $anEmployee->slh_overtime_amount =  $over_amount;
        $anEmployee->tem_total_amount =  $total_amount;
        return $anEmployee;
    }

    public function calculateAnEmployeeMultipleProjectSalaryForAMonth($anEmployee,$arecord)
    {

        $arecord->ot_amount = 0;
        $arecord->other_amount = 0;

        if ($anEmployee->hourly_employee == true && $anEmployee->emp_type_id == 1) {

            $arecord->ot_amount = $arecord->total_overtime * $anEmployee->hourly_rent;
            $arecord->total_amount = ($arecord->total_hour * $anEmployee->hourly_rent) + $arecord->ot_amount;

        } elseif ( $anEmployee->emp_type_id == 1 ) {
            $arecord->ot_amount = ($arecord->total_overtime * ($anEmployee->hourly_rent * 1.5));
            $arecord->total_amount = (($anEmployee->basic_amount / 30) * $arecord->total_day)  + $arecord->ot_amount;

        } elseif ( $anEmployee->emp_type_id == 2) {

         // $other_amount = ($anEmployee->house_rent + $anEmployee->mobile_allowance + $anEmployee->others1);
          $other_amount = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others1 + $anEmployee->local_travel_allowance + $anEmployee->bonus_amount);

          if($other_amount > 0 && $anEmployee->total_work_day > 0){
            $arecord->other_amount = round((($other_amount / $anEmployee->total_work_day) * $arecord->total_day),2);
          }
            $arecord->ot_amount = ($arecord->total_overtime * $anEmployee->hourly_rent);
            $arecord->total_amount = (($anEmployee->basic_amount / 30)) * ($arecord->total_day) + $arecord->ot_amount + $arecord->other_amount;
        }
         return $arecord;

    }


    public function calculateEmployeeSalaryTotalAmount($projectId, $month, $salaryYear)
    {
        if ($projectId == null || $projectId <= 0) {
            return  $total_records = SalaryHistory::select(
                'Status',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )->groupBy("Status")->where('slh_month', $month)->where('slh_year', $salaryYear)->get();
        } else {
            return $total_records = SalaryHistory::select(
                'Status',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )->groupBy("Status")->where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.project_id', $projectId)->get();
        }
    }

    public function calculateSalaryTotalAmountByProjectMonthAndYear($projectId, $month, $salaryYear)
    {
        if ($projectId == null || $projectId <= 0) {
            return  $total_records = SalaryHistory::select(
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )->where('slh_month', $month)->where('slh_year', $salaryYear)->get();
        } else {
            return $total_records = SalaryHistory::select(
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )->where('slh_month', $month)->where('slh_year', $salaryYear)
                ->where('salary_histories.project_id', $projectId)->get();
        }
    }



    public function calculateAProjectAllIncludedSalaryTotalAmountForAMonthAndYear($projectId, $month, $salaryYear)
    {
            return SalaryHistory::select(
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution"),
                DB::raw("SUM(slh_all_include_amount) as all_included_total_amount")
            )->where('slh_month', $month)->where('slh_year', $salaryYear)
            ->where('salary_histories.project_id', $projectId)->first();

    }

    public function getSalarytSheetWiseTotalSalarySummaryByMultipleProjectMonthAndYear($project_id_list, $month, $salaryYear)
    {
        if ($project_id_list == null) {
            return  SalaryHistory::select(
                'status',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_all_include_amount) as total_slh_all_include_amount"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )->groupBy("Status")->where('slh_month', $month)->where('slh_year', $salaryYear)->get();
        } else {
            return SalaryHistory::select(
                'status',
                DB::raw("COUNT(slh_auto_id) as total_emp"),
                DB::raw("SUM(slh_all_include_amount) as total_slh_all_include_amount"),
                DB::raw("SUM(slh_total_salary) as total_salary"),
                DB::raw("SUM(slh_saudi_tax) as total_saudi_tax"),
                DB::raw("SUM(slh_iqama_advance) as total_iqama_adv"),
                DB::raw("SUM(slh_other_advance) as total_other_adv"),
                DB::raw("SUM(slh_cpf_contribution) as total_contribution")
            )->groupBy("Status")->where('slh_month', $month)->where('slh_year', $salaryYear)
                ->whereIn('salary_histories.project_id', $project_id_list)->get();
        }
    }

    public function updateEmployeeSalaryStatusAsPaidAndPaymentMethod($slh_auto_id,$slh_paid_method,$updated_by)
    {
        return SalaryHistory::where('slh_auto_id', $slh_auto_id)->update([
            'Status' => 1,
            'slh_paid_method' => $slh_paid_method,
          //   'slh_salary_date' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'updated_by' =>$updated_by
        ]);
    }

    public function updateEmployeeSalaryStatusAsPaid($slh_auto_id,$updated_by)
    {
        return SalaryHistory::where('slh_auto_id', $slh_auto_id)->update([
            'Status' => 1,
            'updated_at' => Carbon::now(),
            'updated_by' =>$updated_by
        ]);
    }

    public function updateEmployeeSalaryStatusAsUnPaid($slh_auto_id,$updated_by)
    {
        return SalaryHistory::where('slh_auto_id', $slh_auto_id)->update([
            'Status' => 0,
            'updated_at' => Carbon::now(),
            'updated_by' => $updated_by
        ]);
    }



    /*
     =====================================================================================
     ======== Employee Multiple Project Salary Calculation update in a month =============
     =====================================================================================
    */
    public function updateEmployeeMultipleProjectWorkSalaryAmount($empwh_auto_id, $total_amount, $food_amount, $other_amount,$ot_amount,$update_by)
    {
           // total_amount = basic amount + ot_amount + other_amount + food_amount;
        return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->update([
            'total_amount' => $total_amount,
            'food_amount' =>  $food_amount,
            'other_amount' => $other_amount,
            'ot_amount' => $ot_amount,
            'updated_at' => Carbon::now(),
            'update_by_id' => $update_by
        ]);
    }

    // Project Expense report from multiple project work records
    public function getOnlyThisProjectTotalSalaryAmountForMultipleProjectWorkByProjectMonthAndYear($projectId, $month, $salaryYear)
    {

            return $total_records = EmployeeMultiProjectWorkHistory::select(
                DB::raw("COUNT(DISTINCT(emp_id)) as total_emp"),
                DB::raw("SUM(total_amount) as total_salary"),
                DB::raw("SUM(total_hour) as total_hour"),
                DB::raw("SUM(total_overtime) as total_overtime"),
            )->where('month', $month)->where('year', $salaryYear)
                ->where('project_id', $projectId)->get();

    }

    public function getTotalBasicAndOTHoursForAMonthForMultipleProjectByProjectMonthAndYear($project_id_list, $month, $salaryYear)
    {

            return EmployeeMultiProjectWorkHistory::select(
                DB::raw("COUNT(DISTINCT(emp_id)) as total_emp"),
                // DB::raw("SUM(total_amount) as total_salary"),
                DB::raw("SUM(total_hour) as total_hour"),
                DB::raw("SUM(total_overtime) as total_overtime"),
            )->where('month', $month)->where('year', $salaryYear)
                ->whereIn('project_id', $project_id_list)->get();

    }



     /*
     =====================================================================================
     ======================= Employee Bonus System Section ==============================
     =====================================================================================
    */

    public function checkThisEmployeeBonusRecordIsExist($emp_auto_id,$month,$year){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)->where('month',$month)->where('year',$year)->count() > 0 ? true:false;
    }
    public function insertEmployeeBonusRecord($emp_auto_id,$bonus_amount,$bonus_type,$month,$year,$remarks,$created_by){

        if($this->checkThisEmployeeBonusRecordIsExist($emp_auto_id,$month,$year)){
            return -1;
        }
        return EmployeeBonus::insertGetId([
            "emp_auto_id" => $emp_auto_id,
            "bonus_type" => $bonus_type,
            "amount" => $bonus_amount,
            "month" => $month,
            "year" => $year,
            "remarks" => $remarks,
            "created_by" =>$created_by,
            "updated_by" =>$created_by,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function getAnEmployeeBonusRecordByBonusAudoId($bonus_auto_id){
        return EmployeeBonus::where('bonus_auto_id',$bonus_auto_id)->first();
    }
    public function getAnEmployeeBonusRecordsWithEmployeeDetails($emp_auto_id,$month,$year){
        return EmployeeBonus::where('employee_bonus_records.emp_auto_id',$emp_auto_id)
                         ->leftjoin('employee_infos', 'employee_bonus_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                         ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                         ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                         ->get() ;

    }

    public function getAnEmployeeBonusAmountByEmpAutoIdAndMonthYear($emp_auto_id,$month,$year){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)
                              ->where('month',$month)->where('year',$year)->sum('amount');
    }
    public function getAnEmployeeBonusRecordsByEmpAutoIdAndMonthYear($emp_auto_id,$month,$year){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)
                              ->where('month',$month)->where('year',$year)->get();
    }

    public function getAnEmployeeAllBonusRecordByEmpAutoId($emp_auto_id){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)->get();
    }
    public function getAnEmployeeBonusRecordByFiscalYear($emp_auto_id, $start_date,$end_date){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)->whereBetween('created_at',[$emp_auto_id, $start_date,$end_date])->get();
    }
    public function getAnEmployeeTotalBonusAmountByEmpAutoIdAndMonthYear($emp_auto_id,$month,$year){
        return EmployeeBonus::where('emp_auto_id',$emp_auto_id)->sum('amount');
    }

    public function deleteAnEmployeeBonusRecordByBonusAutoId($bonus_auto_id){
        return EmployeeBonus::where('bonus_auto_id',$bonus_auto_id)->delete();
    }

    public function processAnEmployeeBonusRecordsDetailsReportByEmployeeID($employee_id,$branch_office_id){

        return EmployeeBonus::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.mobile_no','employee_infos.hourly_employee','employee_categories.catg_name','countries.country_name',
        'employee_bonus_records.month','employee_bonus_records.year','employee_bonus_records.amount','employee_bonus_records.remarks','users.name')
                    ->leftjoin('employee_infos', 'employee_bonus_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('users', 'employee_bonus_records.created_by', '=', 'countries.id')
                    ->orderBy('employee_infos.employee_id', 'ASC')
                    ->where('employee_infos.branch_office_id',$branch_office_id)
                    ->where('employee_infos.employee_id',$employee_id)
                    ->get();
    }
    public function processEmployeesBonusdetailsReport($bonus_type, $from_date,$to_date,$branch_office_id){

        return EmployeeBonus::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.mobile_no','employee_infos.hourly_employee','employee_categories.catg_name','countries.country_name',
        'employee_bonus_records.month','employee_bonus_records.year','employee_bonus_records.amount','employee_bonus_records.remarks','users.name')
                    ->leftjoin('employee_infos', 'employee_bonus_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                    ->leftjoin('users', 'employee_bonus_records.created_by', '=', 'countries.id')
                    ->orderBy('employee_infos.employee_id', 'ASC')
                    ->where('employee_infos.branch_office_id',$branch_office_id)
                 //   ->where('employee_bonus_records.bonus_type',$bonus_type)
                    ->whereBetween('employee_bonus_records.created_by',[$from_date,$to_date])
                    ->get();
    }


    /*
     =====================================================================================
     ======================= Employee Salary Report Section ==============================
     =====================================================================================
    */

    public function getEmployeesSalaryReportThoseAreWorkingThisProject($project_id,$month,$year,$salary_status)
    {
        if(is_null($salary_status)){
            return  EmployeeInfo::
             select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','salary_histories.basic_amount','salary_histories.hourly_rent','sponsors.spons_name','countries.country_name','employee_categories.catg_name','project_infos.proj_name',
             'salary_histories.multProject','salary_histories.slh_total_hours','salary_histories.slh_total_working_days','salary_histories.slh_all_include_amount','salary_histories.slh_total_overtime','salary_histories.slh_overtime_amount','salary_histories.food_allowance','salary_histories.slh_total_salary','salary_histories.slh_cpf_contribution',
             'salary_histories.slh_iqama_advance','salary_histories.slh_saudi_tax','salary_histories.slh_other_advance','salary_histories.slh_food_deduction','salary_histories.Status','salary_histories.slh_month','salary_histories.slh_year')

             ->where("employee_infos.project_id", $project_id)
            ->where("salary_histories.slh_month", $month)
            ->where("salary_histories.slh_year", $year)
            ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('employee_infos.employee_id', 'ASC')
            ->get();


        }else {
            return  EmployeeInfo::
             select('employee_infos.employee_id','employee_infos.emp_auto_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.salary_status','salary_histories.basic_amount','salary_histories.hourly_rent','sponsors.spons_name','countries.country_name','employee_categories.catg_name','project_infos.proj_name',
            'salary_histories.multProject','salary_histories.slh_total_hours','salary_histories.slh_total_working_days','salary_histories.slh_total_overtime','salary_histories.slh_overtime_amount','salary_histories.food_allowance','salary_histories.slh_total_salary','salary_histories.slh_cpf_contribution',
           'salary_histories.slh_iqama_advance','salary_histories.slh_saudi_tax','salary_histories.slh_other_advance','salary_histories.Status','salary_histories.slh_month','salary_histories.slh_year')
            ->where("employee_infos.project_id", $project_id)
            ->where("salary_histories.slh_month", $month)
            ->where("salary_histories.slh_year", $year)
            ->where("salary_histories.Status", $salary_status)
            ->leftjoin('salary_histories', 'salary_histories.emp_auto_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->orderBy('employee_infos.employee_id', 'ASC')
            ->get();
        }


    }

    public function findAnEmployeeThoseAreNotReceivedSalary($emp_id,$month,$year){

       return SalaryHistory::select('slh_month','slh_year','salary_histories.Status','slh_total_salary','project_infos.proj_name')->where('emp_auto_id',$emp_id)
                            ->whereBetween("salary_histories.slh_month", [1,$month])
                            ->whereBetween("salary_histories.slh_year", [2022,$year])
                            ->where("salary_histories.Status", 0)
                            ->leftjoin('project_infos', 'salary_histories.project_id', '=', 'project_infos.proj_id')
                            ->get();

    }

    public function getProjectBaseBasicAndHourlyEmployeeSalarySummaryProject($project_id_list,$from_month,$to_month,$from_year,$to_year){
      //  dd($project_id_list,$from_month,$to_month,$from_year,$to_year);
        return  $reports = DB::select('call getBasicAndHourlyEmpSalarySummary1(?,?,?,?,?)',array($project_id_list,$from_month,$to_month,$from_year,$to_year));
    }

    public function getAProjectDirectBasicAndHourlyEmployeesSalarySummaryReport($project_id,$month,$year){

          return  DB::select('call getAProjectDirectBasicAndHourlEmployeesSalaryInfoSummaryReport(?,?,?)',array($project_id,$month,$year));
    }
    public function getAProjectDirectBasicAndHourlyEmployeesSalarySummaryWithOutCateringServiceReport($project_id,$month,$year){

        return  DB::select('call getAProjectDirectEmpSalarySummaryWithOutCateringServiceReport(?,?,?)',array($project_id,$month,$year));
  }

    public function getAProjectInirectEmployeesSalarySummaryReport($project_id,$month,$year){

        return  DB::select('call getAProjectIndirectEmployeesSalaryInfoSummaryReport(?,?,?)',array($project_id,$month,$year));
    }
    public function getAProjectInirectEmployeesSalarySummaryWithOutCateringServiceReport($project_id,$month,$year){

        return  DB::select('call getAProjectIndirectEmpSalaryWithOutCateringServiceSummaryReport(?,?,?)',array($project_id,$month,$year));
    }

    public function getAProjectDirectHourslyEmployeesSalarySummaryReport($project_id,$month,$year){
        return  DB::select('call getAProjectDirectHourlyEmployeesSalaryInfoSummaryReport(?,?,?)',array($project_id,$month,$year));
    }

    public function getAProjectActualSalaryExpenseOfDirectEmployeesSalarySummaryReport($project_id,$month,$year){
        return  DB::select('call getAProjectDirectEmpActualSalaryExpSummaryReport(?,?,?)',array($project_id,$month,$year));
    }
    public function getAProjectActualSalaryExpenseOfInDirectEmployeesSalarySummaryReport($project_id,$month,$year){
        return  DB::select('call getAProjectIndirectEmpActualSalaryExpSummaryReport(?,?,?)',array($project_id,$month,$year));
    }



    public function getAProjectBasicAndHourlyEmployeeSalarySummaryReportByProjectId1($project_id,$month,$year){

        $dbrecord = DB::select('call getAProjecBasicAndHourlyEmpSalaryAndDeductionSummaryRerport(?,?,?)',array($project_id,$month,$year));
        $salary = new SalaryHistory();
        if(count($dbrecord) == 2){

         $abc =(array)  $dbrecord[0];   // basic emp salary
         $salary->basic_emp = $abc['total_emp'];
         $salary->basic_salary = $abc['total_salary'];
         $salary->basic_iqama_deduction = $abc['total_iqama_deduction'];
         $salary->basic_other_deduction = $abc['total_other_deduction'];
         $salary->basic_saudi_deduction = $abc['total_saudi_deduction'];
         $salary->basic_food_deduction = $abc['total_food_deduction'];

         $salary->all_incl_total_basic_salary = $abc['all_incl_total_salary'];
         $salary->basic_hours = $abc['total_hours'];


         $abc =(array)  $dbrecord[1];  // hourly emp salary
         $salary->hourly_emp = $abc['total_emp'];
         $salary->hourly_salary = $abc['total_salary'];
         $salary->hourly_iqama_deduction = $abc['total_iqama_deduction'];
         $salary->hourly_other_deduction = $abc['total_other_deduction'];
         $salary->hourly_saudi_deduction = $abc['total_saudi_deduction'];
         $salary->hourly_food_deduction = $abc['total_food_deduction'];
         $salary->all_incl_total_hourly_salary = $abc['all_incl_total_salary'];
         $salary->hourly_hours = $abc['total_hours'];

        }
       elseif(count($dbrecord) == 1){

             $abc =(array)  $dbrecord[0];
             if( $dbrecord[0]->salary_type == 1){

                 $salary->hourly_emp = $abc['total_emp'];
                 $salary->hourly_salary = $abc['total_salary'];
                 $salary->hourly_iqama_deduction = $abc['total_iqama_deduction'];
                 $salary->hourly_other_deduction = $abc['total_other_deduction'];
                 $salary->hourly_saudi_deduction = $abc['total_saudi_deduction'];
                 $salary->hourly_food_deduction = $abc['total_food_deduction'];
                 $salary->all_incl_total_hourly_salary = $abc['all_incl_total_salary'];
                 $salary->hourly_hours = $abc['total_hours'];


             }else {
                 $salary->basic_emp = $abc['total_emp'];
                 $salary->basic_salary = $abc['total_salary'];
                 $salary->basic_iqama_deduction = $abc['total_iqama_deduction'];
                 $salary->basic_other_deduction = $abc['total_other_deduction'];
                 $salary->basic_saudi_deduction = $abc['total_saudi_deduction'];
                 $salary->basic_food_deduction = $abc['total_food_deduction'];

                 $salary->all_incl_total_basic_salary = $abc['all_incl_total_salary'];
                 $salary->basic_hours = $abc['total_hours'];

             }
       }
       return $salary;
    }


    public function getAProjectBasicAndHourlyEmployeeSalarySummaryReportByProjectId($project_id,$month,$year){

       return $dd = DB::select('call getAProjecWorkedEmpSalaryPaidUnpaidAndDeductionSummaryRerport(?,?,?)',array($project_id,$month,$year));
      //  dd($dd);
    }


   // Employee Working Project record base salary inform
    public function getOnlyThisProjectWorkingEmployeesRecordFromMultiProjectTable($month,$year,$project_id,$emp_type,$hourly_employee){
        return    DB::select('call getOnlyThisProjectWorkingEmployeeSalaryInformation1(?,?,?,)',array($month,$year,$project_id));
    }


    public function calculateAProjectTotalPaidUnpaidSalaryInAMonth($project_id, $salary_month, $salary_year)
    {

        $result = DB::select('call calculateAProjectBasicAndHourlySalaryPaidEmployeesReport(?,?,?)',array($project_id,$salary_month,$salary_year));
        //   dd($result);

        $salary = new SalaryHistory();
        if(count($result) == 2){
            if($result[0]->salary_type == 1){

                $salary->hourly_paid_emp =   $result[0]->total_emp;
                $salary->hourly_paid_amount = $result[0]->total_salary;

                $salary->basic_paid_emp =   $result[1]->total_emp;
                $salary->basic_paid_amount = $result[1]->total_salary;
            }
            else {
                $salary->hourly_paid_emp =   $result[1]->total_emp;
                $salary->hourly_paid_amount = $result[1]->total_salary;

                $salary->basic_paid_emp =   $result[0]->total_emp;
                $salary->basic_paid_amount = $result[0]->total_salary;;
            }
        }else if(count($result) == 1){

            if($result[0]->salary_type == 1){
                $salary->hourly_paid_emp =   $result[0]->total_emp;
                $salary->hourly_paid_amount = $result[0]->total_salary;
            }
            else {
                $salary->basic_paid_emp =   $result[0]->total_emp;
                $salary->basic_paid_amount = $result[0]->total_salary;;
            }
        }
        return $salary;

    }

    public function calculateAProjectBasicAndHourlyEmpUnpaidSalarySummaryInAMonth($project_id, $salary_month, $salary_year)
    {

        $result = DB::select('call calculateAProjectBasicAndHourlyUnpaidSalaryReport(?,?,?)',array($project_id,$salary_month,$salary_year));
        //   dd($result);

        $salary = new SalaryHistory();
        if(count($result) == 2){
            if($result[0]->salary_type == 1){

                $salary->hourly_unpaid_emp =   $result[0]->total_emp;
                $salary->hourly_unpaid_amount = $result[0]->total_salary;

                $salary->basic_unpaid_emp =   $result[1]->total_emp;
                $salary->basic_unpaid_amount = $result[1]->total_salary;
            }
            else {
                $salary->hourly_unpaid_emp =   $result[1]->total_emp;
                $salary->hourly_unpaid_amount = $result[1]->total_salary;

                $salary->basic_unpaid_emp =   $result[0]->total_emp;
                $salary->basic_unpaid_amount = $result[0]->total_salary;;
            }
        }else if(count($result) == 1){

            if($result[0]->salary_type == 1){
                $salary->hourly_unpaid_emp =   $result[0]->total_emp;
                $salary->hourly_unpaid_amount = $result[0]->total_salary;
            }
            else {
                $salary->basic_unpaid_emp =   $result[0]->total_emp;
                $salary->basic_unpaid_amount = $result[0]->total_salary;;
            }
        }
         return $salary;

    }




    public function getASponsorYearlySalarySummaryMonthByMonthReport($sponsor_id,$month,$year){
        return    DB::select('call getASponsorYearlySalarySummaryMonthByMonthReport(?,?,?)',array($sponsor_id,$month,$year));
    }
    public function getASponsorSingleMonthSalarySummaryProjecBaseDetailsReport($sponsor_id,$month,$year){
        return    DB::select('call getASponsorSingleMonthSalarySummaryProjectBaseDetailsReport(?,?,?)',array($sponsor_id,$month,$year));
    }

    public function salaryPaidByBankEmployeesSalaryReport($project_ids, $month,$year,$branch_office_id){

            return   EmployeeInfo::where('salary_histories.slh_month', $month)
            ->where('salary_histories.slh_year', $year)
            ->whereNotNull('salary_histories.slh_paid_method')
           // ->whereIn('salary_histories.project_id', $project_ids)  // now  parameter value not provide from caller
            ->where('salary_histories.branch_office_id',$branch_office_id)
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('salary_histories', 'employee_infos.emp_auto_id', '=', 'salary_histories.emp_auto_id')
            ->orderBy('employee_id')
            ->get();
    }


     /*
     ==========================================================================
     ================ uploadEmployeeSalarySheet ===============================
     ==========================================================================
    */
    public function getUploadedSalarySheetInformation(){
      return  SalarySheetUpload::orderBy('month','DESC')->orderBy('year','DESC')->get();
    }

    public function getAnUploadedSalarySheetInformation($salary_uploaded_info_auto_id){
        return  SalarySheetUpload::where('ss_auto_id',$salary_uploaded_info_auto_id)->first();
      }

    public function deleteAnUploadedSalarySheetInformation($salary_uploaded_info_auto_id){
        return  SalarySheetUpload::where('ss_auto_id',$salary_uploaded_info_auto_id)->delete();
      }


      public function insertUploadedSalaryShetInformation($allData){

        return SalarySheetUpload::create($allData);

      }


    /*
     ==========================================================================
     ================ Employee Mobile Bill Information ===============================
     ==========================================================================
    */

    public function storeEmployeeMobileBillInformation($bill_month, $bill_year, $bill_project_id,$bill_payment_paper){
        return EmpMobileBill::insertGetId([
            'month' => $bill_month,
            'year' => $bill_year,
            'project_id' => $bill_project_id,
            'bill_payment_paper' => $bill_payment_paper,
            'create_by_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
    }

    public function searchEmployeeMobileBillRecordsForListViewByYearMonthProject($month, $year,$project_ids,$branch_office_id){
        return EmpMobileBill::select('project_infos.proj_name','users.name as created_by','emp_mobile_bills.*')
                            ->where('year', $year)->whereIn('month', $month)->whereIn('project_id',$project_ids)
                           // ->where('project_infos.branch_office_id',$branch_office_id)
                            ->leftjoin('project_infos', 'emp_mobile_bills.project_id', '=', 'project_infos.proj_id')
                            ->leftjoin('users', 'emp_mobile_bills.create_by_id', '=', 'users.id')
                            ->get();
    }
}
