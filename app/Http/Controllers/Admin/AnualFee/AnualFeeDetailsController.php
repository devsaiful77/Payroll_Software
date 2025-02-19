<?php

namespace App\Http\Controllers\Admin\AnualFee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Imports\ImportIqamaRenewalExpenseRecord;
use App\Imports\ImportEmployeeIqamaExpire;
use App\Imports\ImportEmployeeSponsor;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Session;



class AnualFeeDetailsController extends Controller
{

    function __construct(){

        $this->middleware('permission:employee-anualsfee',['only'=>['index','insert']]);
        $this->middleware('permission:iqama-renewal-expense-edit',['only'=>['getIqamaExpenseRecordSearchingUI','edit','update']]);
        $this->middleware('permission:iqama_renewal_expense_delete',['only'=>['deleteAnEmployeeIqamaAnualExpenseBeforeApproval']]);
        $this->middleware('permission:iqama_renewal_expense_search',['only'=>['searchAnEmployeeIqamaRenewalExpenseRecordsAJAXRequest','searchAnEmployeeIqamaRenewalExpenseApprovalPendingRecordsAJAXRequest']]);
        $this->middleware('permission:iqama-renewal-expense-approval',['only'=>['getAllPendingIqamaExpenseRecordForApproved','approveOfIqamaRenewalExpenseRecord']]);
        $this->middleware('permission:multi_emp_iqama_expiration_date_update_by_excel_upload',['only'=>['uploadIqamaRenewalExpenseExcelFileWithPreview','storeIqamaRenewalExpenseExcelImportedRecordsInMainTable']]);

    }

    // Employee Anual Iqama Renewal UI
    public function index()
    {
      return view('admin.iqamarenewal.index');
    }

    public function edit($id)
    {
        $data = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($id);
        return view('admin.iqamarenewal.edit', compact('data'));
    }

    public function insert(Request $request)
    {

        $this->validate($request, [
            'emp_auto_id' => 'required',
            'jawazat_fee' => 'required',
            'maktab_alamal_fee' => 'required',
            'bd_amount' => 'required',
            'medical_insurance' => 'required',
            'others_fee' => 'required',
            'jawazat_penalty' => 'required',
            'payment_purpose_id' => 'required'
        ], []);


        $findEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->emp_auto_id);
        if (!$findEmployee) {
            Session::flash('error', 'Employee ID Not Found');
            return redirect()->back();
        }else if((new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($request->emp_auto_id,$request->renewal_date) == false){
            Session::flash('error', 'Employee Account is Closed. Please Open Account and Try Again');
            return redirect()->back();
        }
        else {

            $newInsertId = (new EmployeeAdvanceDataService())->saveEmployeeIqamaRenewalExpense(
                $findEmployee->emp_auto_id,
                $request->jawazat_fee,
                $request->maktab_alamal_fee,
                $request->bd_amount,
                $request->medical_insurance,
                $request->others_fee,
                (new HelperController())->getYearFromDateValue($request->renewal_date),
                $request->jawazat_penalty,
                $request->duration,
                $request->renewal_date,
                $request->remarks ?? '',
                $request->total_amount,
                $request->payment_number,
                $request->payment_date,
                $request->reference_emp_id,
                $request->renewal_status,
                $request->expense_paid_by,
                $request->iqama_expire_date,
                $request->payment_purpose_id,
                $findEmployee->branch_office_id
            );

            if ($newInsertId > 0) {
                (new EmployeeDataService())->updateAnEmployeeIqamaExpireDate($findEmployee->emp_auto_id, $request->iqama_expire_date);
                Session::flash('success', 'Operation Successfully Completed');
            } else {
                Session::flash('error', 'Operation Failed');
            }
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'jawazat_fee' => 'required',
            'maktab_alamal_fee' => 'required',
            'bd_amount' => 'required',
            'medical_insurance' => 'required',
            'others_fee' => 'required',
            'jawazat_penalty' => 'required',
            'payment_purpose_id' => 'required',
            'id' => 'required'  // iqama reneal table auto id
        ], []);
        $data = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($request->id);
        if((new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($data->EmplId,$request->renewal_date) == false){
            Session::flash('error', 'Employee Account is Closed. Please Open Account and Try Again');
            return redirect()->back();
        }
        $update = (new EmployeeAdvanceDataService())->updateEmployeeIqamaRenewalExpenseByRecordId(
            $request->id,
            $request->jawazat_fee,
            $request->maktab_alamal_fee,
            $request->bd_amount,
            $request->medical_insurance,
            $request->others_fee,
            (new HelperController())->getYearFromDateValue($request->renewal_date),//Carbon::now()->format('Y'),
            $request->jawazat_penalty,
            $request->duration,
            $request->renewal_date,
            $request->remarks ?? '',
            $request->total_amount,
            $request->payment_number,
            $request->payment_date,
            $request->reference_emp_id,
            $request->renewal_status,
            $request->expense_paid_by,
            $request->iqama_expire_date,
            $request->payment_purpose_id ,
        );

        if($request->has('update_approved_chk')){

            $iqama_renewal_record = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($request->id);
            (new EmployeeAdvanceDataService())->approvedPendingIqamaExpenseRecordForSingleEmpl( $request->id);
            (new EmployeeDataService())->updateAnEmployeeIqamaExpireDate($iqama_renewal_record->EmplId, $request->iqama_expire_date);
        }
        if ($update > 0 && $request->has('update_approved_chk')) {

            Session::flash('success', 'Update Successfully Completed');
            return $this->getAllPendingIqamaExpenseRecordForApproved();
        }
        else if ($update > 0) {

            Session::flash('success', 'Update Successfully Completed');
            return redirect()->route('iqama.renewal.expense.record.search.ui');
        } else {

            Session::flash('error', 'Update Operation Failed');
            return redirect()->back();
        }
    }
   //  // iqama renewal expense searching
    public function getIqamaExpenseRecordSearchingUI()
    {
         return view('admin.iqamarenewal.search');
    }
    // Iqama Pending Approval Request Shown at approval menu
    public function getAllPendingIqamaExpenseRecordForApproved()
    {
        $no_of_records =(new EmployeeAdvanceDataService())->coutTotalNumberOfIqamaExpenseApprovalPendingRecords(Auth::user()->branch_office_id);
         return view('admin.iqamarenewal.iqama_renewal_approval_pending',compact('no_of_records'));
    }

    // pendingIqamaExpenseRecordForApproval
    public function approveOfIqamaRenewalExpenseRecord($iqamaRenualId)
    {
        try
        {
            $iqama_renewal_record = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($iqamaRenualId);
            if((new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($iqama_renewal_record->EmplId,$iqama_renewal_record->renewal_date) == false){
                return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Employee Account is Closed. Please Update Renewal Date & Try Again", 'error'=> "error"]);
            }
            if ($iqama_renewal_record) {
                $updateData = (new EmployeeAdvanceDataService())->approvedPendingIqamaExpenseRecordForSingleEmpl($iqamaRenualId);
                (new EmployeeDataService())->updateAnEmployeeIqamaExpireDate($iqama_renewal_record->EmplId, $iqama_renewal_record->iqama_expire_date);

                return  response()->json(['status'=>200, 'success' =>true,'message'=>"Successfully Updated"]);
            } else {
                return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Record Not Found ", 'error'=> "error"]);
            }
        }catch(Exception $ex){
            return  response()->json(['status'=>404, 'success' =>false, 'message'=>"System Operation Failed", 'error'=> "error"]);
        }
    }
    public function deleteAnEmployeeIqamaAnualExpenseBeforeApproval($id)
    {
        try
        {
            $iqama_renewal_record = (new EmployeeAdvanceDataService())->findAnEmployeeIqamaExpenseRecordByRecordId($id);
            if((new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($iqama_renewal_record->EmplId,$iqama_renewal_record->renewal_date) == false){
                return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Employee Account is Closed. Delete is not Possible", 'error'=> "error"]);
            }
            $delete = (new EmployeeAdvanceDataService())->deleteAnEmployeeIqamaExpenseRecordByRecordId($id);
            if ($delete) {
                return  response()->json(['status'=>200, 'success' =>true,'message'=>"Successfully Deleted"]);
            } else {
                return  response()->json(['status'=>404, 'success' =>false, 'message'=>"Record Not Found ", 'error'=> "error"]);
            }
        }catch(Exception $ex){
            return  response()->json(['status'=>404, 'success' =>false, 'message'=>"System Operation Failed", 'error'=> "error"]);
        }


    }

    public function getPendingIqamaExpenseRecordForSingleEmployee($iqamaRenualId)
    {
        $view = (new EmployeeAdvanceDataService())->singleEmployeePendingIqamaExpenseRecord($iqamaRenualId);
        return view('admin.iqamarenewal.view', compact('view'));
    }


    public function searchAnEmployeeIqamaRenewalExpenseRecordsAJAXRequest(Request $request)
    {
        try{

            $find_employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->searchValue,$request->searchType,Auth::user()->branch_office_id);
            if ($find_employee) {
                $records = (new EmployeeAdvanceDataService())->searchAnEmployeeIqamaAnnualExpenseAllRecords($find_employee->emp_auto_id);
                return  response()->json(['status'=>200, 'success' =>true,'employee'=>$find_employee, 'data'=> $records]);
            } else {
                return  response()->json(['status'=>403, 'success' =>false,'error'=>'error', 'message'=>'Employee Not Found']);
            }
        }catch(Exception $ex){
            return  response()->json(['status'=>403, 'success' =>false,'error'=>'error', 'message'=>'System Exception, Please Reload and Try Again']);
        }

    }

    public function searchAnEmployeeIqamaRenewalExpenseApprovalPendingRecordsAJAXRequest(Request $request)
    {
        try
        {
            $find_employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->searchValue,$request->searchType,Auth::user()->branch_office_id);
            if ($find_employee) {
                $records = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaAnnualExpenseApprovalPendingRecords($find_employee->emp_auto_id);
            } else {
                $records =  (new EmployeeAdvanceDataService())->getIqamaRenewalExpenseApprovalPendingAllRecords(Auth::user()->branch_office_id); // 100 records
            }
            return  response()->json(['status'=>200, 'success' =>true, 'data'=> $records]);
        }catch(Exception $ex){
            return  response()->json(['status'=>404, 'success' =>false, 'message'=>"System Operation Failed", 'error'=> "error"]);
        }


    }


   // Import Work Record Excel File to Temporary Table
   public function uploadIqamaRenewalExpenseExcelFileWithPreview(Request $request)
   {
            try{
                if ($request->file ) {

                       $file = $request->file;
                       $upload_file_type = $request->upload_type_ddl;
                       $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
                       $fileSize = $file->getSize(); //Get size of uploaded file in bytes


                        if((new HelperController())->checkUploadedFileFormatAndUploadFileSize($extension, $fileSize)){
                            if($upload_file_type == 1){
                                // iqama expire date update
                                $import = new ImportEmployeeIqamaExpire();
                            } else if($upload_file_type == 2){
                                 // renewal expense record
                                $import = new ImportIqamaRenewalExpenseRecord();
                            }else  if($upload_file_type == 3){
                                $import = new ImportEmployeeSponsor();
                            }
                            Excel::import($import, $request->file('file'));
                            return response()->json([
                                'status' => 200,
                                'success'=> true,
                                'records_not_found' => $import->records_not_found,
                                'records' => $import->records,
                                'message' => $import->records->count() ." Records  Added for Uploading"
                            ]);
                        }else {
                            return response()->json([
                                'status' => 404,
                                'success'=> false,
                                'message' =>  "Invalid File Format"
                            ]);
                        }

                } else {
                    return response()->json([
                        'status' => 403,
                        'success'=> false,
                        'message' =>  "Please Upload an Excel File"
                    ]);
                }
            }
            catch(Exception $ex){
                return response()->json([
                    'error' => $request->all(),
                    'status' => 500,
                    'success' => false,
                    'message' =>  $ex." Upload Failed, Please check Excel Header Name & Data",
                ]);
            }
   }


   // Submit Imported Excell Data To Final Table
   public function storeIqamaRenewalExpenseExcelImportedRecordsInMainTable(Request $request){

       // dd($request->all());
       try{

           $result = (new EmployeeRelatedDataService())->getIqamaExpireUploadedAllDataFromTemporaryTable();
          // DB::beginTransaction();
           foreach ($result as $arecord){
             $is_hourly_emp = (new EmployeeDataService())->checkThisEmployeSalaryIsHourlyByEmployeeAutoId($arecord->emp_auto_id);
           //  dd($is_hourly_emp);
            if($request->upload_file_type == 1){
                  (new EmployeeDataService())->updateEmployeeIqamaReletedInfo($arecord->emp_auto_id, $arecord->iqama_no, $arecord->expire_date);
            }else  if($request->upload_file_type == 2){
                  (new EmployeeAdvanceDataService())->saveEmployeeIqamaRenewalExpenseByFileUpload($arecord->emp_auto_id, $arecord->jawazat_fee, $arecord->maktab_alamal_fee
                  ,$arecord->bd_amount, $arecord->medical_insurance, $arecord->others_fee,(new HelperController())->getYearFromDateValue($request->renewal_date),
                   $arecord->jawazat_penalty, $arecord->duration, $arecord->renewal_date, $arecord->remarks, $arecord->total_amount,
                   "",$arecord->renewal_date, $arecord->reference_emp_id,1, $is_hourly_emp == 1 ? 1: 2 );
                   // basic employee paid by company = 2 and hourly emp self =  1
            }

           }
          // DB::commit();
           // remove all records from table
           (new EmployeeRelatedDataService())->removeIqamaExpireUploadedAllDataFromTemporaryTable();
           return response()->json([
               'status' =>  200,
               'success' => true,
               'message' => 'Successfully Updated'
           ]);
       }catch(Exception $ex){
          // DB::rollBack();
           return response()->json([
               'error' =>  "error",
               'status' => 500,
               'success' => false,
               'message' => 'System Operation Failed',
           ]);
       }

   }


    /*
     ==========================================================================
                 Iqama Advace Payment report Processing UI
     ==========================================================================
    */

    // Iqama Pending Approval Request Create Report (Date wise)
    public function loadEmployeeRenewalExpenseReportProcessForm()
    {
        $inserted_by_users = (new EmployeeAdvanceDataService())->getListOfUserThoseInsertedIqamaRenwalTableRecords(Auth::user()->branch_office_id);
        $sponsors = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        return view('admin.report.iqama.iqama_renewal_report_process_form',compact('inserted_by_users','sponsors','projects'));
    }
    // report Iqama Renewal  Date base
    public function processEmployeeIqamaRenewalExpenseDateByDateReport(Request $request)
    {
        $company = (new CompanyDataService())->findCompanryProfile();
        $employee = (new EmployeeAdvanceDataService())->generateIqamaRenewalExpenseReport( $request->sponsor_id,$request->inserted_by, $request->approval_status,
         $request->expense_by, $request->start_date, $request->end_date,$request->payment_purpose_id);
        return view('admin.report.iqama.iqama_renewal_date_to_date_inserted_report', compact('employee', 'company'));
    }



    // Iqama Expense and Deduction Base Report
    public function projectAndSponsorWiseIqamaReport(Request $request)
    {

        if((int) $request->report_type == 1){
            return $this->processEmployeeBaseIqamaRenewalAndDeductionSummaryReport($request);
        }
        else if((int) $request->report_type == 2){

            return $this->processSponsorBaseIqamaRenewalAndDeductionSummaryReport($request);
        }


    }

    // 1 Iqama Expense and Deduction EMployee base Summary
    private function processEmployeeBaseIqamaRenewalAndDeductionSummaryReport(Request $request)
    {

        $company = (new CompanyDataService())->findCompanryProfile();

        $projId = $request->proj_id;
        $sponsId = $request->spons_id;
        $year = $request->year;
        $month = $request->month;

        $reportingMonth = (new HelperController())->getMonthName($request->month); // (new CompanyDataService())->getMonthById($request->month);
        $projAndSponsWiseEmp = (new EmployeeDataService())->getEmployeeListWithProjectAndSponsor($projId, $sponsId);
        $projectName = 'All';
        $sponsorName = 'All';

        if ($projId != 0) {
            $projectName = (new EmployeeRelatedDataService())->findAProjectInformation($projId)->proj_name;
        }
        if ($sponsId != 0) {
            $sponsorName = (new EmployeeRelatedDataService())->findASponser($sponsId)->spons_name;
        }

        $totalIqmaAdvAmount = 0;
        $totalPaidAmount = 0;
        $totalDueAmount = 0;
        $counter = 0;

        foreach ($projAndSponsWiseEmp as $anEmployee) {

            $iqama_renewal_cost_total_amount = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaTotalCost($anEmployee->emp_auto_id);
            // Advance Collection from Employee
            $cashReceiveTotalPaidAmount = (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidTotalAmount($anEmployee->emp_auto_id, null, 100); // 100 = cash receive from employee
            $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaExpenseDeductionFromSalary($anEmployee->emp_auto_id, null);
            $iqamaDeductionAmount = (new SalaryProcessDataService())->getAnEmployeeMonltySalaryIqamaAmount($anEmployee->emp_auto_id, $month, $year);

            $totalPaidAmount = $toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount;
            $anEmployee->iqama_renewal_cost_total_amount = $iqama_renewal_cost_total_amount;
            $anEmployee->iqama_renewal_total_paid_amount = $totalPaidAmount;
            $anEmployee->iqamaDeductionAmount = $iqamaDeductionAmount;

            $totalIqmaAdvAmount += $iqama_renewal_cost_total_amount;
            $totalPaidAmount += $totalPaidAmount;
            $totalDueAmount += ($iqama_renewal_cost_total_amount - $totalPaidAmount);
            $projAndSponsWiseEmp[$counter++] = $anEmployee;

        }
        return view('admin.report.iqama.iqama_expen_and_deduction_summary_report', compact('projAndSponsWiseEmp', 'reportingMonth','year', 'sponsorName', 'projectName', 'company', 'totalIqmaAdvAmount', 'totalPaidAmount', 'totalDueAmount'));
    }

    // 2 Iqama Expense and Deduction Summary
    private function processSponsorBaseIqamaRenewalAndDeductionSummaryReport($request){


        $projId = $request->proj_id;
        $sponsId = $request->spons_id;
        $year = $request->year;
        $month = $request->month;
        $sponsor_list = $request->sponsor_id;
        $isSelectedAll = false;

        if(!$sponsor_list){
            $sponsor_list = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();
        }else{
            $sponsor_list = (new EmployeeRelatedDataService())->getAllActiveSponserInfoByMultipleId($sponsor_list);
        }
        $counter = 0;
       // dd($sponsor_list);
        foreach ($sponsor_list as $sp) {

                $records = (new EmployeeAdvanceDataService())->getTotalAmountOfIqamaRenewalExpensedBySponsor($sp->spons_id,$month,$year);
                $toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getTotalAmountOfIqamaRenewalDeductionFromSalaryBySponsorId($sp->spons_id,$month,$year);
                $sp->total_deduction = $toal_iqama_expense_deduction_from_salary;
               if(count($records)>0){
                    $record = $records[0];
                    $sp->total_emp = $record->total_emp;
                    $sp->total_expense = $record->total_amount;
                    $sponsor_list[$counter++] = $sp;
               }


        }

      //  dd( $sponsor_list);
        $company = (new CompanyDataService())->findCompanryProfile();
        $month = (new HelperController())->getMonthName($request->month);
        return view('admin.report.iqama.sponsor_base_expense_and_deduction_summary', compact('sponsor_list', 'month','year', 'company'));
    }




}
