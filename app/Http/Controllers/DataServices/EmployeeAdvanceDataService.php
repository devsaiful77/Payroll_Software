<?php

namespace App\Http\Controllers\DataServices;

use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Models\AdvanceInfo;
use App\Models\AdvancePayRecord;
use App\Models\AdvancePurpose;
use App\Models\AnualFeesDetails;
use App\Models\IqamaRenewalDetails;
use App\Models\upload_advance_paper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;


class EmployeeAdvanceDataService
{


    public function getAdvancePurposeAll()
    {
        return AdvancePurpose::get();
    }

    public function processAnEmployeeAdvance($anEmployee)
    {
        try {

            // Advance Given to Employee
           $iqama_renewal_cost_total_amount = $this->getAnEmployeeIqamaTotalCost($anEmployee->emp_auto_id);
          // Advance Paid By Employee
           $cashReceiveTotalPaidAmount = $this->getAnEmployeeAdvancePaidTotalAmount($anEmployee->emp_auto_id, null, 100); // 100 = cash receive from employee
           $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaExpenseDeductionFromSalary($anEmployee->emp_auto_id,null);

            $diffAmount =  $iqama_renewal_cost_total_amount   - ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount);
            $anEmployee->iqaamadiffAmount = $diffAmount;

          if($diffAmount <= 0){
                $anEmployee->iqama_adv_inst_amount = 0;
          }
          else if ($diffAmount <= $anEmployee->iqama_adv_inst_amount) {
                $anEmployee->iqama_adv_inst_amount = $diffAmount;
          }
            $anEmployee->iqama_renewal_cost_total_amount = $iqama_renewal_cost_total_amount;
            $anEmployee->iqama_renewal_total_paid_Amount = ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount);

            (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($anEmployee->emp_auto_id, $anEmployee->iqama_adv_inst_amount, 1);

            // Other Advance Calculation
            // Advance Given to Employee
            $otherAdvanceTotalAmount = $this->getAnEmployeeOthersAdvanceTotalAmount($anEmployee->emp_auto_id, null, 0);
            // Advance Collection from Employee
             $total_other_advace_deduction_from_salary =(new SalaryProcessDataService())->getTotalAmountOfOtherAdvanceDeductionFromSalary($anEmployee->emp_auto_id,null);
             $diffAmount =  $otherAdvanceTotalAmount -  $total_other_advace_deduction_from_salary;
            $anEmployee->diffAmount = $diffAmount;
            if($diffAmount <= 0 ){
                $anEmployee->other_adv_inst_amount = 0;
            }
            else if ($diffAmount <= $anEmployee->other_adv_inst_amount) {
                 $anEmployee->other_adv_inst_amount = 0;
            }

            $anEmployee->other_advance_total_amount = $otherAdvanceTotalAmount;
            $anEmployee->other_advance_total_paid_Amount = $total_other_advace_deduction_from_salary;

            (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($anEmployee->emp_auto_id, $anEmployee->other_adv_inst_amount, 2);
             // 1= set iqama_adv_inst_amount
             // = 2 set other advance install amount
            return $anEmployee;
        }catch (Exception $ex) {
            dd($ex);
            return "Exception Occurred, Operation Failed";
        }
    }


    public function processAnEmployeeAdvanceForFiscalYear($anEmployee)
    {
        try {

            $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($anEmployee->emp_auto_id);
           // Advance Collection from employee
            $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            $cashReceiveTotalPaidAmount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            // Advance Given to Employee
            $iqama_renewal_cost_total_amount = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);

            $diffAmount =  $iqama_renewal_cost_total_amount   - ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount);
            $anEmployee->iqaamadiffAmount = $diffAmount;

          if($diffAmount <= 0){
                $anEmployee->iqama_adv_inst_amount = 0;
          }
          else if ($diffAmount <= $anEmployee->iqama_adv_inst_amount) {
                $anEmployee->iqama_adv_inst_amount = $diffAmount;
          }
            $anEmployee->iqama_renewal_cost_total_amount = $iqama_renewal_cost_total_amount;
            $anEmployee->iqama_renewal_total_paid_Amount = ($toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount);

            // Other Advance Calculation
            // Advance Given to Employee
            $otherAdvanceTotalAmount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            // Advance Collection from Employee
            $total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($anEmployee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
            if(($otherAdvanceTotalAmount -  $total_other_advace_deduction_from_salary) <= 0 ){
                $anEmployee->other_adv_inst_amount = 0;
            }
            else if (($otherAdvanceTotalAmount -  $total_other_advace_deduction_from_salary) <= $anEmployee->other_adv_inst_amount) {
                 $anEmployee->other_adv_inst_amount = 0;
            }

            $anEmployee->other_advance_total_amount = $otherAdvanceTotalAmount;
            $anEmployee->other_advance_total_paid_Amount = $total_other_advace_deduction_from_salary;

            (new EmployeeDataService())->updateAnEmployeeIqamaAndOtherAdvaceInstallAmount($anEmployee->emp_auto_id,$anEmployee->iqama_adv_inst_amount, $anEmployee->other_adv_inst_amount);

            return $anEmployee;
        }catch (Exception $ex) {
            dd($ex);
            return false;// "Exception Occurred, Operation Failed";
        }
    }


  // Project wise Employee Advance Setting
    public function processAnEmployeeAdvanceForSettingAdvanceDeductionAmount($anEmployee,$iqama_deduction_amount,$other_deduction_amount)
    {
        try {

          $anUpdatedEmp =  $this->processAnEmployeeAdvanceForFiscalYear($anEmployee);
          if ($anUpdatedEmp->iqama_adv_inst_amount > $iqama_deduction_amount) {
                $anUpdatedEmp->iqama_adv_inst_amount = $iqama_deduction_amount;
          }

          if ($anUpdatedEmp->other_adv_inst_amount > $other_deduction_amount) {
            $anUpdatedEmp->other_adv_inst_amount = $other_deduction_amount;
          }
          (new EmployeeDataService())->updateAnEmployeeIqamaAndOtherAdvaceInstallAmount($anUpdatedEmp->emp_auto_id,$anUpdatedEmp->iqama_adv_inst_amount, $anUpdatedEmp->other_adv_inst_amount);
           return $anUpdatedEmp;
        }catch (Exception $ex) {
            return "Exception Occurred, Operation Failed";
        }
    }


    public function getAnEmployeeAdvanceSummaryReport($anEmployee)
    {

        try {
            // find Employee Iqama Advance Payment
            $anEmployee->iqama_advance_total = $this->getAnEmployeeIqamaTotalCost($anEmployee->emp_auto_id);
            $anEmployee->iqama_paid_total = $this->getAnEmployeeAdvancePaidTotalAmount($anEmployee->emp_auto_id, null, 1);
            $anEmployee->iqama_cash_paid_total = $this->getAnEmployeeAdvancePaidTotalAmount($anEmployee->emp_auto_id, null, 100);
            $anEmployee->other_advance_total= $this->getAnEmployeeOthersAdvanceTotalAmount($anEmployee->emp_auto_id,null, 0);
          ///  $anEmployee->other_advance_paid_total = $this->getAnEmployeeAdvancePaidTotalAmount($anEmployee->emp_auto_id, null, 2);
            $anEmployee->other_advance_paid_total = (new SalaryProcessDataService())->getTotalAmountOfOtherAdvanceDeductionFromSalary($anEmployee->emp_auto_id,null);
            return $anEmployee;

        } catch (Exception $ex) {
              dd($anEmployee, $ex);
        }
    }

    public function getEmployeeOtherAdvanceSummaryReportByProjectId($project_id)
    {

        try {
             return  DB::select('CALL process_employee_other_advance_summary1(?)',array($project_id));

        } catch (Exception $ex) {
             return $ex;
        }
    }

    // public function getEmployeeAdvanceInsertedByUserIdReport($user_id,$from_date,$to_date){
    //     return  DB::select('CALL getEmpAdvanceInsertedByUserIdReport1(?,?,?)',array($user_id,$from_date,$to_date));
    // }
    public function processEmployeeAdvanceReportByInsertedUser($user_id,$from_date,$to_date,$branch_office_id){
        return  DB::select('CALL processEmployeeAdvanceReportByInsertedUser(?,?,?,?)',array($user_id,$from_date,$to_date,$branch_office_id));
    }



    /*
     ==========================================================================
     =========================EMployee Expense Anual Fees Details  ============
     ==========================================================================
    */

    public function saveEmployeeIqamaRenewalExpense($emp_auto_id,$jawazat_fee,$maktab_al_ama_fee,$bd_amount,$medical_insurance,$others,$year,$jawazat_penalty,$duration,$renewal_date,$remarks,$total_amount,$payment_number,$payment_date,$reference_emp_id,$renewal_status,$expense_paid_by,$iqama_expire_date,$payment_purpose_id,$branch_office_id){

       return IqamaRenewalDetails::insert([
            'EmplId' => $emp_auto_id,
            'jawazat_fee' => $jawazat_fee,
            'maktab_alamal_fee' => $maktab_al_ama_fee,
            'bd_amount' => $bd_amount,
            'medical_insurance' => $medical_insurance,
            'others_fee' => $others,
            'Year' => $year,// Carbon::now()->format('Y'),
            'jawazat_penalty' => $jawazat_penalty,
            'duration' => $duration,
            'renewal_date' => $renewal_date,
            'remarks' => $remarks ?? '',
            'total_amount' =>$total_amount,
            'payment_number' =>$payment_number,
            'payment_date' => $payment_date,
            'reference_emp_id' =>$reference_emp_id,
            'renewal_status' =>$renewal_status,
            'expense_paid_by' =>$expense_paid_by,
            'iqama_expire_date' =>$iqama_expire_date,
            'created_at' => Carbon::now(),
            'inserted_by' =>Auth::user()->id,
            'payment_purpose_id' => $payment_purpose_id,
            'branch_office_id' => $branch_office_id
        ]);
    }

    public function updateEmployeeIqamaRenewalExpenseByRecordId($iqamaRenewId,$jawazat_fee,$maktab_al_ama_fee,$bd_amount,$medical_insurance,$others,$year,$jawazat_penalty,
    $duration,$renewal_date,$remarks,$total_amount,$payment_number,$payment_date,$reference_emp_id,$renewal_status,$expense_paid_by,$iqama_expire_date,$payment_purpose_id){

       return  IqamaRenewalDetails::where('IqamaRenewId',$iqamaRenewId)->update([
            'jawazat_fee' => $jawazat_fee,
            'maktab_alamal_fee' => $maktab_al_ama_fee,
            'bd_amount' => $bd_amount,
            'medical_insurance' => $medical_insurance,
            'others_fee' => $others,
            'Year' => Carbon::now()->format('Y'),
            'jawazat_penalty' => $jawazat_penalty,
            'duration' => $duration,
            'renewal_date' => $renewal_date,
            'remarks' => $remarks ?? '',
            'total_amount' =>$total_amount,
            'payment_number' =>$payment_number,
            'payment_date' => $payment_date,
            'reference_emp_id' =>$reference_emp_id,
            'renewal_status' =>$renewal_status,
            'expense_paid_by' =>$expense_paid_by,
            'iqama_expire_date' =>$iqama_expire_date,
            'updated_at' => Carbon::now(),
            'update_by' => Auth::user()->id,
            'payment_purpose_id' => $payment_purpose_id
        ]);
    }

    public function saveEmployeeIqamaRenewalExpenseByFileUpload($emp_auto_id,$jawazat_fee,$maktab_al_ama_fee,$bd_amount,$medical_insurance,$others,$year,$jawazat_penalty,
    $duration,$renewal_date,$remarks,$total_amount,$payment_number,$payment_date,$reference_emp_id,$renewal_status,$expense_paid_by){

        return IqamaRenewalDetails::insert([
             'EmplId' => $emp_auto_id,
             'jawazat_fee' => $jawazat_fee,
             'maktab_alamal_fee' => $maktab_al_ama_fee,
             'bd_amount' => $bd_amount,
             'medical_insurance' => $medical_insurance,
             'others_fee' => $others,
             'Year' => $year,
             'jawazat_penalty' => $jawazat_penalty,
             'duration' => $duration,
             'renewal_date' => $renewal_date,
             'remarks' => $remarks ?? '',
             'total_amount' =>$total_amount,
             'payment_number' =>$payment_number,
             'payment_date' => $payment_date,
             'reference_emp_id' =>$reference_emp_id,
             'renewal_status' =>$renewal_status,
             'expense_paid_by' =>$expense_paid_by,
             'created_at' => Carbon::now(),
             'inserted_by' =>Auth::user()->id,
             'payment_purpose_id' => 2

         ]);
     }

    // public function getAllEmployeeIqamaExpenseApprovalPendingRecords(){
    //     return IqamaRenewalDetails::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no'
    //     ,'employee_infos.hourly_employee','iqama_renewal_details.IqamaRenewId','iqama_renewal_details.jawazat_fee','iqama_renewal_details.maktab_alamal_fee','iqama_renewal_details.bd_amount',
    //             'iqama_renewal_details.medical_insurance','iqama_renewal_details.others_fee','iqama_renewal_details.jawazat_penalty','iqama_renewal_details.total_amount',
    //             'iqama_renewal_details.duration','iqama_renewal_details.renewal_date','iqama_renewal_details.iqama_expire_date','iqama_renewal_details.remarks')
    //             ->where('approved_status', 0)
    //             ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
    //             ->orderBy('IqamaRenewId', 'ASC')->get();
    // }

    public function coutTotalNumberOfIqamaExpenseApprovalPendingRecords($login_user_branch_office_id){
        return IqamaRenewalDetails::where('approved_status', 0)->where('branch_office_id',$login_user_branch_office_id)->count();
    }


    public function getTotalAmountOfIqamaRenewalExpensedBySponsor($sponsor_id,$month,$year)
    {

                    return $dd = IqamaRenewalDetails::select(
                        DB::raw("DISTINCT(COUNT(EmplId)) as total_emp"),
                        DB::raw("SUM(iqama_renewal_details.total_amount) as total_amount"),
                        )
                        ->whereYear('iqama_renewal_details.renewal_date',$year)
                        ->whereMonth('iqama_renewal_details.renewal_date',$month)
                        ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
                        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                        ->where('employee_infos.sponsor_id', $sponsor_id)
                        ->where('iqama_renewal_details.approved_status', 1)
                        ->get();

    }

    public function getListOfUserThoseInsertedIqamaRenwalTableRecords($branch_office_id){
          return  IqamaRenewalDetails::select('iqama_renewal_details.inserted_by as id','users.name')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->where('users.branch_office_id',$branch_office_id)
            ->groupBy('iqama_renewal_details.inserted_by','users.name')->orderBy('users.name', 'ASC')->get();
    }

    public function approvedPendingIqamaExpenseRecordForSingleEmpl($iqamaRenualId){
        return IqamaRenewalDetails::where('IqamaRenewId' ,$iqamaRenualId)->update([
            'approved_status' => 1,
            'approved_by' => Auth::user()->id,
            'update_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
            'approval_date' => Carbon::now(),
        ]);
    }

    public function singleEmployeePendingIqamaExpenseRecord($iqamaRenualId){
        return IqamaRenewalDetails::where('IqamaRenewId' ,$iqamaRenualId)
            ->where('approved_status', 0)
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->first();
    }

    public function getpendingIqamaExpenseDataCreateDateWise($start_date, $end_date){
        return IqamaRenewalDetails::where('approved_status', 0)
                                ->whereBetween('created_at', [$start_date, $end_date])
                                ->get();
    }

    public function deleteAnEmployeeIqamaExpenseRecordByRecordId($id){
        return IqamaRenewalDetails::where('IqamaRenewId', $id)->delete();
    }

    public function getEmployeeIqamaExpenseRecords($limit)
    {
        return  IqamaRenewalDetails::orderBy('IqamaRenewId', 'DESC')->take($limit)->get();
    }
    public function findAnEmployeeIqamaExpenseRecordByRecordId($id)
    {
        return  IqamaRenewalDetails::where('IqamaRenewId', $id)->first();
    }

    // Iqama Renewal Details Edit Info Data Check For Testing Purpose

    // public function findAnEmployeeIqamaExpenseRecordWithEmployeeInfoByRecordId($id){
    //     return IqamaRenewalDetails::select('employee_infos.employee_id', 'employee_infos.employee_name',
    //      'employee_infos.akama_no', 'employee_infos.akama_expire_date', 'iqama_renewal_details.EmplId', 'iqama_renewal_details.iqama_expire_date',
    //      'iqama_renewal_details.IqamaRenewId', 'iqama_renewal_details.jawazat_fee', 'iqama_renewal_details.maktab_alamal_fee',
    //      'iqama_renewal_details.bd_amount', 'iqama_renewal_details.medical_insurance', 'iqama_renewal_details.others_fee',
    //      'iqama_renewal_details.Cost6','iqama_renewal_details.Cost7', 'iqama_renewal_details.Cost8', 'iqama_renewal_details.Year',
    //      'iqama_renewal_details.jawazat_penalty', 'iqama_renewal_details.duration', 'iqama_renewal_details.renewal_date',
    //      'iqama_renewal_details.remarks', 'iqama_renewal_details.total_amount', 'iqama_renewal_details.expense_paid_by',
    //      'iqama_renewal_details.reference_emp_id', 'iqama_renewal_details.update_by', 'iqama_renewal_details.approved_by',
    //      'iqama_renewal_details.approved_status', 'iqama_renewal_details.payment_number', 'iqama_renewal_details.payment_date', 'iqama_renewal_details.renewal_status')
    //      ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
    //      ->where('IqamaRenewId', $id)->first();
    // }

    public function getAnEmployeeIqamaAnnualExpenseAllRecords($emoloyeeAutoId)
    {
        return    IqamaRenewalDetails::where('EmplId', $emoloyeeAutoId)->where('approved_status', 1)->where('expense_paid_by', 1)->get();
    }

    public function getAnEmployeeIqamaAnnualExpenseSelfAndCompanyAllRecords($emoloyeeAutoId)
    {
        return    IqamaRenewalDetails::where('EmplId', $emoloyeeAutoId)->where('approved_status', 1)->get();
    }

    public function searchAnEmployeeIqamaAnnualExpenseAllRecords($emoloyeeAutoId)
    {
        return   IqamaRenewalDetails::where('EmplId', $emoloyeeAutoId)->get();
    }

    public function getAnEmployeeIqamaAnnualExpenseApprovalPendingRecords($emp_auto_id)
    {
        return DB::select('CALL getAnEmpIqamaRenewalApprovalPendingRecords1(?)',array($emp_auto_id));
    }
    public function getIqamaRenewalExpenseApprovalPendingAllRecords($login_user_branch_office_id)
    {
        return DB::select('CALL getIqamaRenewalApprovalPendingAllRecords(?)',array($login_user_branch_office_id));
    }

    // public function coutTotalNumberOfIqamaExpenseApprovalPendingRecords($login_user_branch_office_id){
    //     return IqamaRenewalDetails::where('approved_status', 0)->where('branch_office_id',$login_user_branch_office_id)->count();
    // }

    // public function searchAnEmployeeIqamaAnnualExpenseRecords($emoloyeeAutoId)
    // {
    //     return   IqamaRenewalDetails::where('EmplId', $emoloyeeAutoId)->get();
    // }

    public function getAnEmployeeIqamaTotalCost($emoloyeeAutoId)
    {
       $allRecords =  IqamaRenewalDetails::where('EmplId', $emoloyeeAutoId)->where('approved_status', 1)->where('expense_paid_by', 1)->get();
       // expensed_by = 1 means paid by employee, 2 = paid by company

       $total_expense = 0;
       foreach ($allRecords as $arecord) {
        $total_expense += $arecord->jawazat_fee + $arecord->maktab_alamal_fee + $arecord->bd_amount + $arecord->medical_insurance + $arecord->others_fee + $arecord->Cost6 + $arecord->jawazat_penalty;
       }
       return $total_expense;

    }

     // Report Method Section
     // iqama and other renewal expesne  report
     public function generateIqamaRenewalExpenseReport($sponsor_ids,$inserted_by, $approval_status,$expense_by,$from_date,$to_date,$purpose_id){

            if($purpose_id){
                return IqamaRenewalDetails::where('payment_purpose_id',(int)$purpose_id)->where('iqama_renewal_details.inserted_by', $inserted_by)
               ->whereBetween('renewal_date', [$from_date,$to_date])
                ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
                ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
                ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();
            }

        if(is_null( $inserted_by)  && $approval_status == null && $expense_by == null) {
           // 000
            return IqamaRenewalDetails::whereIn('employee_infos.sponsor_id',$sponsor_ids)->whereBetween('renewal_date', [$from_date,$to_date])
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();
        }
        else if( is_null( $inserted_by)  && $approval_status == null && $expense_by != null) {
            // 001
            return IqamaRenewalDetails::whereIn('employee_infos.sponsor_id',$sponsor_ids)->where('expense_paid_by', $expense_by)
            ->whereBetween('renewal_date', [$from_date,$to_date])
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();

        }else if( is_null( $inserted_by)  && $approval_status != null && $expense_by == null) {
            // 010
             return IqamaRenewalDetails::whereIn('employee_infos.sponsor_id',$sponsor_ids)->where('approved_status',$approval_status)
            ->whereBetween('renewal_date', [$from_date,$to_date])
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();

        }else if( is_null( $inserted_by)  && $approval_status != null && $expense_by != null) {
            // 011
            return IqamaRenewalDetails::whereIn('employee_infos.sponsor_id',$sponsor_ids)->where('approved_status', $approval_status)
            ->where('expense_paid_by', $expense_by)
            ->whereBetween('renewal_date', [$from_date,$to_date])
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();
        }else if( !is_null( $inserted_by)  && is_null($approval_status) && is_null($expense_by) ) {
            // 100
            return IqamaRenewalDetails::whereIn('employee_infos.sponsor_id',$sponsor_ids)->where('iqama_renewal_details.inserted_by', $inserted_by)
            ->whereBetween('renewal_date', [$from_date,$to_date])
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();
        }else if( !is_null( $inserted_by)  && is_null($approval_status) && !is_null($expense_by) ) {
            // 101

            return IqamaRenewalDetails::whereIn('employee_infos.sponsor_id',$sponsor_ids)->where('iqama_renewal_details.inserted_by', $inserted_by)
            ->where('expense_paid_by', $expense_by)
            ->whereBetween('renewal_date', [$from_date,$to_date])
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();
        }
        else if( !is_null( $inserted_by)  && !is_null($approval_status) && is_null($expense_by) ) {
            // 110

            return IqamaRenewalDetails::whereIn('employee_infos.sponsor_id',$sponsor_ids)->where('approved_status', $approval_status)
            ->where('iqama_renewal_details.inserted_by', $inserted_by)
            ->whereBetween('renewal_date', [$from_date,$to_date])
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();
        }else if( !is_null( $inserted_by)  && !is_null($approval_status) && !is_null($expense_by) ) {
            // 111

            return IqamaRenewalDetails::whereIn('employee_infos.sponsor_id',$sponsor_ids)->where('approved_status', $approval_status)
            ->where('expense_paid_by', $expense_by)
            ->where('iqama_renewal_details.inserted_by', $inserted_by)
            ->whereBetween('renewal_date', [$from_date,$to_date])
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'iqama_renewal_details.EmplId')
            ->leftjoin('users','iqama_renewal_details.inserted_by', '=','users.id')
            ->orderBy('employee_infos.employee_id', 'ASC')->orderBy('IqamaRenewId', 'ASC')->get();
        }

     }


    // public function getAnEmployeeIqamaRenewAnnualTotalCost($emoloyeeAutoId, $year)
    // {
    //     $iqamaDetails = IqamaRenewalDetails::where('EmplId', $emoloyeeAutoId)->where('approved_status', 1)->where('Year', $year)->first();
    //     if ($iqamaDetails != null) {
    //         return $iqamaDetails->jawazat_fee + $iqamaDetails->maktab_alamal_fee + $iqamaDetails->bd_amount + $iqamaDetails->medical_insurance + $iqamaDetails->others_fee + $iqamaDetails->Cost6 + $iqamaDetails->jawazat_penalty;
    //     }
    //     return 0.00;
    // }



    /*
     ==========================================================================
     ============================= Advance Info  ==============================
     ==========================================================================
    */

    public function insertEmployeeAdvance($empAutoId, $advPurposeId, $advAmount, $installMonth, $remarks, $date, $insertById,$advance_paper,$project_id)
    {
        $insert = new AdvanceInfo();
        $insert->emp_id = $empAutoId;
        $insert->adv_purpose_id = $advPurposeId;
        $insert->adv_amount = $advAmount;
        $insert->installes_month = $installMonth;
        $insert->adv_remarks = $remarks;
        $insert->year = date('Y', strtotime($date));
        $insert->date = $date;
        $insert->create_by = $insertById;
        $insert->advance_paper = $advance_paper;
        $insert->project_id = $project_id;
        $insert->created_at = Carbon::now();
        return  $insert->save();
    }

    public function getEmployeeAdvanceAllRecords($pageLimit = null)
    {
        if ($pageLimit == null || $pageLimit <= 0) {
            $pageLimit = 10;
        }
        return  AdvanceInfo::where('status', 1)
            ->leftjoin('employee_infos', 'advance_infos.emp_id', '=', 'employee_infos.emp_auto_id')
              ->where('adv_purpose_id','!=', 1)
            ->orderBy('id', 'DESC')->paginate($pageLimit);
    }

    public function getAnEmployeeFiscalYearAdvanceAllRecordsByEmpoyeeAutoID($employee_auto_id,$start_date,$end_date)
    {
        return    DB::select('call getAnEmployeeAdvanceTakenDateToDateAllRecords(?,?,?)',array($employee_auto_id,$start_date,$end_date));

    }

    // Advance Payment Datewise Record for Advance Report
    public function getEmployeesAdvancePaymentDateRecordsProjectWise($project_id, $start_date, $end_date){
          return  $reports = DB::select('call getEmpAdvancePaymentDatewiseReport(?,?,?)',array($project_id,$start_date,$end_date));
    }

    // Advance Payment INsert datewise Record for Advance Report
    public function getEmployeesAdvancePaymentRecordWithInsertDateAndProjectWise($project_id, $start_date, $end_date){

           return  $reports = DB::select('call getEmpAdvancePaymentInsertDatewiseReport(?,?,?)',array($project_id,$start_date,$end_date));

    }

    public function getAdvancePaymentInsertedRecordByInsertDateWiseForUploadAdvancePaper($start_date, $end_date,$login_user_id){

        return  $reports = DB::select('call getAdvancePaymentInsertDatewiseRecordsForPaperUpload(?,?,?)',array($start_date,$end_date,$login_user_id));

 }



    public function getEmployeesAdvanceInsertionRecordsProjectWise($project_id, $month,$year){

        if(is_null($project_id)){

            return  AdvanceInfo::leftJoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'advance_infos.emp_id')
            ->whereMonth('date', $month)->where('year', $year)
            ->where('adv_purpose_id','!=', 1)
            ->orderBy('date', 'ASC')->get();
        }else {
            return AdvanceInfo::leftJoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'advance_infos.emp_id')
                    ->where('project_id', $project_id)
                    ->where('adv_purpose_id','!=', 1)
                    ->whereBetween('advance_infos.date', [$start_date, $end_date])
                    ->orderBy('date', 'ASC')->get();
        }
    }



    public function getSummationOfAdvancePayToEmployeeTotalAmount($start_date, $end_date)
    {
           return AdvanceInfo::whereBetween('date', [$start_date, $end_date])
                            ->where('adv_purpose_id','!=', 1)
                            ->sum('adv_amount');
    }
    public function getTotalAmountOfAdvanceToEmployeeByYearToYear($year)
    {
           return AdvanceInfo::where('year', $year)
                            ->where('adv_purpose_id','!=', 1)
                            ->sum('adv_amount');
    }

    public function getAdvanceRecordById($id)
    {
        return $all = AdvanceInfo::where('status', 1)->where('id', $id)->first();
    }
    public function getAdvanceRecordByEmpId($emp_id)
    {
        return $all = AdvanceInfo::where('status', 1)->where('emp_id', $emp_id)->first();
    }

    public function findLastAdvanceRecordByEmpAutoIdAndAdvancePurposeId($emp_auto_id, $purposeTypeId)
    {
        return $all = AdvanceInfo::where('status', 1)->where('emp_id', $emp_auto_id)->where('adv_purpose_id', $purposeTypeId)->latest()->first();
    }

    public function deleteAdvanceRecordById($id)
    {
        return $all = AdvanceInfo::where('status', 1)->where('id', $id)->delete();
    }


    public function updateAdvanceRecordById($recordId, $emp_auto_id, $adv_purpose_id, $adv_amount, $installes_month,$adv_date, $adv_remarks)
    {

        return AdvanceInfo::where('status', 1)->where('id', $recordId)->update([
          //  'emp_id' => $emp_auto_id,
            'adv_purpose_id' => $adv_purpose_id,
            'adv_amount' => $adv_amount,
            'installes_month' => $installes_month,
            'adv_remarks' => $adv_remarks,
            'date' => $adv_date,
            'year' => date('Y', strtotime($adv_date)),
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateGroupOfEmployeeAdvanceTakenPaperPath($adv_auto_id_list, $file_path, $updated_by)
    {
        return AdvanceInfo::whereIn('id', $adv_auto_id_list)->update([
            'advance_paper' => $file_path,
            'updated_by' => $updated_by,
            'updated_at' => Carbon::now(),
        ]);
    }



    // public function getAnEmployeeIqamaAdvanceTotalAmount($employeeAutoId, $year)
    // {
    //     // Iqama Advance
    //     $totalAdvanceAmount = AdvanceInfo::where('emp_id', $employeeAutoId)
    //         //->where('year',$year)
    //         ->where('adv_purpose_id', 1)->sum('adv_amount');
    //     if ($totalAdvanceAmount == null) {
    //         return 0;
    //     } else {
    //         return $totalAdvanceAmount;
    //     }
    // }

    //getAnEmployeeTotalAdvanceAmount
    public function getAnEmployeeOthersAdvanceTotalAmount($employeeAutoId, $year, $advanceTypeId)
    {
        // other Advance
       return AdvanceInfo::where('emp_id', $employeeAutoId)
            ->where('adv_purpose_id', '!=', 1)->sum('adv_amount');
    }


    /*
     ==========================================================================
     ============================= Advance Payment ============================
     ==========================================================================
    */


    public function getAdvancePaidRecordWithPagination($advanceTypeId)
    {
        // 100 = Cash Received from Employee
        return  AdvancePayRecord::where('adv_purpose_id', $advanceTypeId)
            ->leftjoin('employee_infos', 'advance_pay_records.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->orderBy('id', 'DESC')->paginate(100);
    }

    public function getAdvancePaidAllRecordNoPagination($advanceTypeId)
    {
        // 100 = Cash Received from Employee
        return  AdvancePayRecord::where('adv_purpose_id', $advanceTypeId)
            ->leftjoin('employee_infos', 'advance_pay_records.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->get();
    }

    public function insertAdvancePaidRecord($employeeAutoId, $amount, $year, $month, $advancePurposeId, $createById)
    {

        $insert = new AdvancePayRecord();
        $insert->emp_id = $employeeAutoId;
        $insert->adv_purpose_id = $advancePurposeId;
        $insert->adv_amount = $amount;
        $insert->month = $month;
        $insert->year = $year;
        $insert->date = Carbon::now();
        $insert->create_by = $createById;
        $insert->adv_remarks = "";
        $insert->created_at = Carbon::now();
        return $insert->save();
    }
    public function deleteEmployeeCashPaymentRecord($id)
    {
        return AdvancePayRecord::where('id', $id)->delete();
    }


    public function updateAdvancePaidRecord($paidRecordId, $employeeAutoId, $amount, $year, $month, $advancePurposeId, $createById)
    {
        // advanceTypeId  1= Iqama advance,  2= Other Advance
        $update = AdvancePayRecord::where('id', $paidRecordId)
            ->where('emp_id', $employeeAutoId)->where('month', $month)->where('year', $year)->update([
                'adv_purpose_id' => $advancePurposeId,
                'adv_amount' => $amount,
                'date' => Carbon::now(),
                'create_by' => $createById,
                'updated_at' => Carbon::now(),
            ]);

    }


    public function deleteEmployeeAdvancePaidRecord($emp_auto_id, $month, $year)
    {
        AdvancePayRecord::where('emp_id', $emp_auto_id)->where('month', $month)
            ->where('year', $year)->delete();
    }

    public function getAnEmployeeAdvancePaidTotalAmount($employeeAutoId, $year, $advanceTypeId)
    {

        // advanceTypeId  1 = Iqama advance,  2 = Other Advance

        if(is_null($year)){
             return AdvancePayRecord::where('emp_id', $employeeAutoId)
            ->where('adv_purpose_id', $advanceTypeId)->sum('adv_amount');
        }else {
            return AdvancePayRecord::where('emp_id', $employeeAutoId)
                ->where('year',$year)  //  1= Iqama advance,  2= Other Advance
                ->where('adv_purpose_id', $advanceTypeId)->sum('adv_amount');
        }

    }



    public function getThisMonthAdvancePaidRecord($employeeAutoId, $year, $month, $advanceTypeId)
    {
      //  dd($employeeAutoId, $year, $month, $advanceTypeId);
        return AdvancePayRecord::where('emp_id', $employeeAutoId)
            ->where('year', (int) $year)->where('month', (int) $month)
            ->where('adv_purpose_id', $advanceTypeId)->first();

    }


    public function getAllAdvancePaidRecord($advanceTypeId)
    {
        // 100 = Cash Received from Employee
        return  AdvancePayRecord::where('adv_purpose_id', $advanceTypeId)
            ->leftjoin('employee_infos', 'advance_pay_records.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->get();
    }


      /*
     ==========================================================================
     =============== Employee Fiscal Year Process Related Methods =============
     ==========================================================================
    */

    public function getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($emp_auto_id,$start_date,$end_date)
    {
        return  AdvancePayRecord::where('emp_id', $emp_auto_id)->whereBetween('date', [$start_date,$end_date])
                ->where('adv_purpose_id', 100)->sum('adv_amount');
                // 100 Cach Paid
    }

    public function getAnEmployeeIqamaExpenseSelfAndCompanyAllRecordsByFiscalYear($emp_auto_id,$start_date,$end_date)
    {
        return IqamaRenewalDetails::where('EmplId', $emp_auto_id)->whereBetween('renewal_date',[$start_date,$end_date])->where('approved_status', 1)->get();
    }

    public function getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($emp_auto_id, $start_date,$end_date)
    {
        $allRecords =  IqamaRenewalDetails::where('EmplId', $emp_auto_id)->whereBetween('renewal_date',[$start_date,$end_date])->where('expense_paid_by', 1)->where('approved_status', 1)->get();
          $total_expense = 0;
          foreach ($allRecords as $arecord) {
           $total_expense += $arecord->jawazat_fee + $arecord->maktab_alamal_fee + $arecord->bd_amount + $arecord->medical_insurance + $arecord->others_fee + $arecord->Cost6 + $arecord->jawazat_penalty;
          }
          return $total_expense;
    }

    public function getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($emp_auto_id, $start_date,$end_date)
    {
        // other Advance
       return AdvanceInfo::where('emp_id', $emp_auto_id)
            ->where('adv_purpose_id', '!=', 1)
            ->whereBetween('date',[$start_date,$end_date])
            ->sum('adv_amount');
    }



     /*
     ==========================================================================
     ============================= Advance Payment Report =====================
     ==========================================================================
    */


    public function getEmployeesThoseAreNotPaidAdvanceFromSalaryDeduction($project_id,$month,$year){

        return  $employees = DB::select('call get_advance_unpaid_employees1(?,?,?)',array($project_id,$month,$year));

    }
    public function getanEmployeeAdvanceMonlthlySummaryAllRecordsByEmpoyeeID($emp_auto_id)
    {
        return  DB::select('call getAnEmpAdvanceMonthWiseSummaryAllRecords1(?)',array($emp_auto_id ));

    }


    public function getAnEmployeeAdvanceLastRecordsReportByEmpoyeeID($employee_id,$branch_office_id)
    {
        return   AdvanceInfo::select('employee_infos.employee_id','employee_infos.employee_name','employee_infos.akama_no','employee_infos.passfort_no',
                'project_infos.proj_name', 'advance_infos.adv_amount', 'advance_infos.created_at', 'advance_infos.date', 'advance_infos.adv_remarks', 'users.name')
                ->where('advance_infos.status', 1)
                ->leftjoin('employee_infos', 'advance_infos.emp_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('advance_purposes', 'advance_infos.adv_purpose_id', '=', 'advance_purposes.id')
                ->leftjoin('project_infos', 'advance_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('users', 'users.id', '=', 'advance_infos.create_by')
                ->where('adv_purpose_id','!=', 1)
                ->where('employee_infos.employee_id', $employee_id)
                ->where('employee_infos.branch_office_id', $branch_office_id)
                ->latest('advance_infos.id')->first();
     }


     /*
     ==========================================================================
     ======================= Advance Payment Paper Upload =====================
     ==========================================================================
    */
    public function insertUploadedAdvancePaperInformation($month,$year,$advance_date,$file_path,$remark,$insert_by){
      return upload_advance_paper::insertGetId([
        'month' =>$month,
        'year' =>$year,
        'advance_date' =>$advance_date,
        'file_path' =>$file_path,
        'remark' =>$remark,
        'insert_by' =>$insert_by,
      ]);

    }
    public function searchUploadedAdvancePaper($advance_date,$month,$year){
       return upload_advance_paper::where('month',$month)->where('year',$year)
                    //->whereBetween('advance_date',[$advance_date,])
                    ->orderBy('advance_date','DESC')
                    ->get();
    }

    public function deleteUploadedAdvancePaper($auto_id){
        return upload_advance_paper::where('uap_auto_id',$auto_id)
                     ->delete();
     }


}
