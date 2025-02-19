<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\AccommodationDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Expr\Cast\Double;

class AdvancePayController extends Controller
{
    /*
      =================================
      ========== DATABASE OPERATION ===
      =================================
    */

    // ALTER TABLE `advance_infos` ADD `project_id` SMALLINT(5) NULL AFTER `adv_remarks`;


    function __construct()
    {
         $this->middleware('permission:set-advance', ['only' => ['index']]);
         $this->middleware('permission:employee_advance_insert', ['only' => ['index','insert','getEmployeeListForMultipleEmployeeAdvancePayment','multipleEmployeeAdvanceInsertRequest']]);
         $this->middleware('permission:employee_advance_edit', ['only' => ['edit','update','updateEmployeeAdvanceInformation']]);
         $this->middleware('permission:employee_advance_delete', ['only' => ['delete','deleteCashDepositAdvancePayment']]);

         $this->middleware('permission:employee-advance-processing', ['only' => ['employeeAdvanceProcessingUI','employeeAdvanceProcessingRequest','updateAdvanceInstallAmount']]);
         $this->middleware('permission:summary-adjuget', ['only' => ['emplpyeeCashDepositFormRequest','emplpyeeCashDepositFormRequestWithAllRecords']]);
         $this->middleware('permission:advance-adjuget', ['only' => ['employeeMonthlyPaymentSetting']]);
         $this->middleware('permission:emp_advance_paid_report', ['only' => ['loadEmployeeAdvanceEntryReportProcessForm']]);
         $this->middleware('permission:advance_paper_upload_insert',['only'=>'loadAdvancePaperUploadForm','uploadAdvancePaperToServer']);
         $this->middleware('permission:advance_paper_upload_search',['only'=>'searchAdvanceInsertedEmployeesForUploadAdvancePaper']);
        // $this->middleware('permission:advance_paper_uploaded_delete',['only'=>'deleteAdvanceUploadedPaper']);

    }

    // Single employee advance
    public function insert(Request $request)
    {
            $this->validate($request, [
                'emp_id' => 'required',
                'adv_purpose_id' => 'required',
                'project_id' => 'required',
                'adv_amount' => 'required',
                'installes_month' => 'required',
            ], []);

            $employee = (new EmployeeDataService())->getAnyJobStatusEmployeeWithSalaryDetailsByEmpId($request->emp_id);
            if ($request->adv_purpose_id == 1 || $employee ==  null) {
                return response()->json(['status' =>404,'success'=>false,'message' => "Iqama Advance Payment Not Allowed Here",'error'=>'error']);
            } else if ($request->installes_month <= 0 ||  $request->adv_amount <= 0) {
                 return response()->json(['status' =>404,'success'=>false,'message' => "Please Input Advance Amount",'error'=>'error']);
            }else if((new  FiscalYearDataService())->checkThisOperationIsAllowInTheRunningFiscalYear($employee->emp_auto_id,$request->adv_date) == false){
                return response()->json(['status' =>404,'success'=>false,'message'=>"The Employee Fiscal Year is Closed for the Advance Date"]);
            }


            $installes_amount = ($request->adv_amount / $request->installes_month);
            $creator = Auth::user()->id;
            $emp_auto_id = $employee->emp_auto_id;
            $other_adv_inst_amount = $installes_amount + $employee->salarydetails->other_adv_inst_amount;
            $file_path = null;
            if ($request->hasFile('advance_paper')) {
                $file = $request->file('advance_paper');
                $file_path = (new UploadDownloadController())->uploadAdvancePaper($file,null);
            }
            $insert =  (new EmployeeAdvanceDataService())->insertEmployeeAdvance($emp_auto_id, $request->adv_purpose_id, $request->adv_amount,
            $request->installes_month, $request->adv_remarks, $request->adv_date, $creator,$file_path,$request->project_id,$employee->branch_office_id);
            (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($emp_auto_id, $other_adv_inst_amount, 2);

            if ($insert) {
                return response()->json(['status' =>200,'success'=>true,'message'=>"Successfully Saved",'data'=>$request->all()]);
            } else {
                return response()->json(['status' =>404,'success'=>false,'message' => "Operation Failed Please try Again",'error'=>'error']);
            }

    }

    // Multiple employee advance insertion request
    public function multipleEmployeeAdvanceInsertRequest(Request $request)
    {
     //  dd($request->all());
        $this->validate($request, [
            'adv_purpose_id' => 'required',
        ], []);

        $creator = Auth::user()->id;
        $emp_auto_id_list = $request->emp_auto_id;
        $counter = 0;

        $file_path = null;
        if ($request->hasFile('advance_paper')) {
            $file = $request->file('advance_paper');
            $file_path = (new UploadDownloadController())->uploadAdvancePaper($file,null);
        }

        foreach($emp_auto_id_list as $aemp_id){

            if($request->has('adv_checkbox-' . $aemp_id) && $request->has('adv_amount-' . $aemp_id)) {

                $adv_amount = $request->get('adv_amount-' . $aemp_id);
                $employee = (new EmployeeDataService())->getAnEmployeeIqamaAdvanceAndOtherAdvanceInstallAmountByEmpAutoId($aemp_id);
                $other_adv_inst_amount = $adv_amount + $employee->other_adv_inst_amount;
                $project_id = $request->get('project_id_'.$aemp_id);

                (new EmployeeAdvanceDataService())->insertEmployeeAdvance($aemp_id, $request->adv_purpose_id, $adv_amount, 1, $request->adv_remarks,
                $request->adv_date, $creator, $file_path,$project_id);
                $counter += 1 ;
                (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($aemp_id, $other_adv_inst_amount, 2);
            }
        }
        if ($counter > 0) {
            Session::flash('success', 'Successfully Saved '.$counter.' Employees Advance Payment');
            return redirect()->back();
        } else {
            // advance not insert that's why delete the uploaded file
            (new UploadDownloadController())->deleteUploadedAdvancePaper($file_path);
            Session::flash('error', 'Operation Failed, Please Try Again!');
            return redirect()->back();
        }
    }

    //  Employee search For multiple emp Advance Payment Ajax Request
    public function getEmployeeListForMultipleEmployeeAdvancePayment(Request $request)
    {

        $project_id = $request->project_id;
        $buildingId = $request->acomdOfbId;
        $multi_emp_id = $request->multi_emp_id;
        $emplist = array();
        if(is_null($multi_emp_id) == false){

            $allEmplId = explode(",", $multi_emp_id);
            $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
            $emplist = (new EmployeeDataService())->searchListOfEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId($allEmplId,Auth::user()->branch_office_id);

        }else{
            $emplist = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailForMultipleEmployeeAdvancePayment($project_id, $buildingId,Auth::user()->branch_office_id);
        }

        if (count($emplist) > 0) {
          return response()->json(['status' =>200,'success'=>true, "empList" => $emplist]);
        } else {
          return response()->json(['status' =>404,'success'=>false,'error' => "Data Not Found!"]);
        }

    }




    // Create Employee Advance Paper OR Employee Cash Received FORM
    public function createEmployeeAdvancePaper(Request $request)
    {

        if($request->request_type == 2){
            // cash receive form
            $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->employee_id,'employee_id',Auth::user()->branch_office_id);
             if(!$employee){
                return "Employee Not Found ";
            }

            $declaration_text = "";
            $form_title = "";

            $payment_method = $request->payment_method;
            $receiver_type = $request->receiver_type;
            $remarks = $request->remarks;
            $received_date = $request->received_date;

            if($request->form_type == 1){
                // cash receive form
                $declaration_text = "I hereby acknowledge that I received the above mentioned amount by ".$payment_method." I am responsible to pay the amount.
                If I unable to pay the mentioned amount, The Authority Reserved Rights to take any legal actions.";
                $form_title = "CASH RECEIVED FORM";

                 $amount = $request->amount;
                $in_word = (new HelperController())->numberToWord($amount);

                $company = (new CompanyDataService())->findCompanryProfile();
                $prepared_by = Auth::user()->name;
                return view('admin.emp-loan.cash_received_form',compact('employee','company','amount','in_word','prepared_by','payment_method','receiver_type','received_date','remarks','declaration_text','form_title'));


            }else if($request->form_type == 2){
                // cash receipt
                $declaration_text =  "";
                $form_title = "CASH RECEIPT FORM";
                $amount = $request->amount;
                $in_word = (new HelperController())->numberToWord($amount);

                $company = (new CompanyDataService())->findCompanryProfile();
                $prepared_by = Auth::user()->name;
                $received_date = date('F jS, Y', strtotime($received_date)) ;
                return view('admin.emp-loan.cash_receipt_form',compact('employee','company','amount','in_word','prepared_by','payment_method','receiver_type','received_date','remarks','declaration_text','form_title'));

            }


        }else {
            // multiple employee advance form for food purpose from advance report page
            $project_id = $request->proj_name;
            $buildingId = $request->acomdOfbId;
            $adv_amount = $request->adv_amount;
            $adv_date = $request->adv_date;
            if(is_null($request->adv_emp_ids) == false){
                $allEmplId = explode(",", $request->adv_emp_ids);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                $records = (new EmployeeDataService())->getMultipleEmpIDWiseInformationWithSalaryDetailForEmployeeAdvancePaper($allEmplId);
                $project_name = "";
            }else {
                $records = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailForEmployeeAdvancePaper($project_id, $buildingId);
                $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
            }
            $company = (new CompanyDataService())->findCompanryProfile();
            $prepared_by = Auth::user()->name;
            return view('admin.emp-loan.multi_emp_advance_paper',compact('records','company','project_name','adv_amount','adv_date','prepared_by'));
        }


    }

    // Edit Employee Already Taken Advance INFormation
    public function updateEmployeeAdvanceInformation(Request $request)
    {
        $this->validate($request, [
            'modal_adv_audo_id' =>'required',
            'modal_emp_audo_id' => 'required',
            'modal_adv_purpose_id' => 'required',
            'modal_adv_amount' => 'required',
            'modal_installes_month' => 'required',
            'modal_adv_date' => 'required',
        ], []);

        if ($request->modal_adv_purpose_id == 1) {
            return response()->json(['status' =>404,'success'=>false,'message' => "No Advance for Iqama Renewal",'error'=>'error']);
        }

        $fiscal_year =

        $update = (new EmployeeAdvanceDataService())->updateAdvanceRecordById(
            $request->modal_adv_audo_id,
            $request->modal_emp_audo_id,
            $request->modal_adv_purpose_id,
            $request->modal_adv_amount,
            $request->modal_installes_month,
            $request->modal_adv_date,
            $request->modal_adv_remarks
        );

        if ($update) {
            return response()->json(['status' =>200,'success'=>true,'message'=>"Successfully Updated"]);
          } else {
            return response()->json(['status' =>404,'success'=>false,'message' => "Update Operation Failed",'error'=>'error']);
          }
    }

    // Employee Advance Setting Json Reponse
    // Single Employee Advance Setting
    public function findanEmployeeWithAdvanceSetting(Request $request)
    {

        $anEmployee  = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsInformationByEmployeeID($request->emp_id,Auth::user()->branch_office_id);
        if ($anEmployee) {
            $anEmployee  = (new EmployeeAdvanceDataService())->processAnEmployeeAdvanceForFiscalYear($anEmployee);
            $salaryDetatils = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($anEmployee->emp_auto_id);

            return response()->json([
                'status' =>200, 'success' => true,
                'totalIqama' => $anEmployee->iqama_renewal_cost_total_amount,
                'totalPaidIqama' => $anEmployee->iqama_renewal_total_paid_Amount,
                'totalOthers' => $anEmployee->other_advance_total_amount,
                'totalPaidOthers' => $anEmployee->other_advance_total_paid_Amount,
                'empAutoId' => $anEmployee->emp_auto_id,
                'salaryDetatils' => $salaryDetatils,
                'employee' => ['employee_name'=> $anEmployee->employee_name,'employee_id'=> $anEmployee->employee_id,
                'akama_no'=> $anEmployee->akama_no,'hourly_employee'=> $anEmployee->hourly_employee,'passfort_no'=>$anEmployee->passfort_no],
            ]);
        } else
            return response()->json(['status' =>404, 'success' => false, 'error'=>'error', 'message' => 'Employee Not Found']);
    }


    /* Update Employee Advance and Food ammount Setting Request  */
    public function updateAdvanceInstallAmount(Request $request)
    {
       // dd($request->all());
        if((int) $request->operation_type == 2){

                // 2 =  Employee Food Setting
                $emp_auto_ids = (new EmployeeAttendanceDataService())->getEmployeesAutoIdsThoseAreInMonthlyWorkRecord($request->project_id,$request->month,$request->year);
                $update_status = (new EmployeeDataService())->updateMultipleEmployeesFoodAmountByMultipleEmplID($emp_auto_ids,$request->amount);
                Session::flash('success',  'Successfully Update Employee Food Amount');
                return redirect()->back();

        } else if((int) $request->operation_type == 1){
            // 1 = single emp advance setting
            $nextPayIqama = (float) $request->nextPayIqama;
            $nextPayOthers = (float) $request->nextPayOthers;
            if ($nextPayOthers == "") {
                $nextPayOthers = 0;
            } elseif ($nextPayOthers == "" && $nextPayIqama == "") {
                $nextPayIqama = 0;
                $nextPayOthers = 0;
            } elseif ($nextPayIqama == "") {
                $nextPayIqama = 0;
            } else {
                $nextPayIqama = (float) $request->nextPayIqama;
                $nextPayOthers =  (float) $request->nextPayOthers;
            }
              // Here id means Employee Auto Id
              $update1 = (new EmployeeDataService())->updateEmployeeIqamaAdvaceInstallAmount($request->id, $nextPayIqama);
              $update = (new EmployeeDataService())->updateEmployeeOtherAdvaceInstallAmount($request->id, $nextPayOthers);

            if ($update || $update1) {
                Session::flash('success', 'Successfully Update Advance Setting');
                return redirect()->back();
            } else {
                Session::flash('error', 'Update Operation Failed, Please Try Again!');
                return redirect()->back();
            }
        }else {
            return redirect()->back();
        }


    }


    /*
      =================================
      ====== BLADE OPERATION ==========
      =================================
    */
    public function index()
    {

        $all = (new EmployeeAdvanceDataService())->getEmployeeAdvanceAllRecords(10);
        $purpose = (new EmployeeAdvanceDataService())->getAdvancePurposeAll();
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
        return view('admin.emp-loan.all', compact('purpose', 'projects', 'accomdOfficeBuilding','all'));
    }

    // Employee Advance Search AJAX Request
    public function employeeAdvanceListSearch(Request $request)
    {
        $empInfo = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->employee_searching_value,$request->search_by,Auth::user()->branch_office_id);
        if (count($empInfo) >= 2) {
            return json_encode(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Multiple Employee Not found']);
        }
        else if (count($empInfo) == 0) {
            return json_encode(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Employee Not found']);
        }else{
            $empInfo =  $empInfo[0];
            $adv_records = (new EmployeeAdvanceDataService())->getAnEmployeeFiscalYearAdvanceAllRecordsByEmpoyeeAutoID($empInfo->emp_auto_id,null,null);
            if ($adv_records) {
                return json_encode(['success' => true, 'status' => 200, 'data' => $adv_records]);
            } else {
                return json_encode(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Advance Record Not found']);
            }

        }

    }

    public function edit($id)
    {

        $record = (new EmployeeAdvanceDataService())->getAdvanceRecordById($id);
        $purpose = (new EmployeeAdvanceDataService())->getAdvancePurposeAll();
        return response()->json(['status' =>200,'success'=>true,'data'=>$record,'adv_purpose' =>$purpose]);

    }


    public function delete($id)
    {

        $advaceRecord =  (new EmployeeAdvanceDataService())->getAdvanceRecordById($id);

        $timestamp = strtotime($advaceRecord->date);
        $month = (int) date('m', $timestamp);
        $salaryRecord = (new SalaryProcessDataService())->getAnEmployeeInfoWithSalaryHistory($advaceRecord->emp_id, $month, $advaceRecord->year);
        if ($salaryRecord != null) {
            if ($salaryRecord->Status == 0) {
                (new EmployeeAdvanceDataService())->deleteEmployeeAdvancePaidRecord($advaceRecord->emp_id, $salaryRecord->slh_month, $advaceRecord->year);
                (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($advaceRecord->emp_id, 0, $advaceRecord->adv_purpose_id);

                (new EmployeeAdvanceDataService())->deleteAdvanceRecordById($id);

                Session::flash('success', 'Delete Operation Completed');
                return redirect()->back();
            } else {
                Session::flash('error', 'Permission Denied');
                return redirect()->back();
            }
        } else {
            (new EmployeeAdvanceDataService())->deleteAdvanceRecordById($id);

            (new EmployeeDataService())->updateEmployeeAdvaceInstallAmount($advaceRecord->emp_id, 0, $advaceRecord->adv_purpose_id);
            Session::flash('success', 'Delete Operation Completed');
            return redirect()->back();
        }
    }

    // Return Advance Setting UI
    public function employeeMonthlyPaymentSetting()
    {
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        return view('admin.emp-loan.advance-adjust',compact('projects'));
    }

    public function employeeAdvanceProcessingUI()
    {

        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $sponsors = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        return view('admin.emp-loan.advance-processing', compact('projects','sponsors'));
    }

    // WOrking Project wise Employee Advance Processing WITH IQAMA AND OTHER ADVANCE SETTING AJAX Request
    public function employeeAdvanceProcessingRequest(Request $request)
    {

        if ($request->has('mul_emp_id') && $request->mul_emp_id != "") {
            $allEmplId = explode(",", $request->mul_emp_id);
            $allEmplId = array_unique($allEmplId);
            $empList = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailThoseAreExistInThisListForAdvanceProcess($allEmplId, 1);

        } else if ($request->has('project_id') && $request->project_id > 0) {

            $sponsor_ids = $request->has('sponsor_ids') ? $request->sponsor_ids : (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();

            $empIds = (new EmployeeAttendanceDataService())->getEmployeesAutoIdsThoseAreInMonthlyWorkRecordForAdvanceProcessing((int)$request->project_id, $sponsor_ids,(int)$request->month,(int)$request->year);
            $empList =  (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailThoseAreExistInThisListForAdvanceProcess($empIds);
        } else {
            $empList = array();
        }
        if (count($empList) <= 0) {
            return json_encode(['status' => '405', 'error' => "Employee Not Found", 'success' => false]);
        }

        foreach ($empList as $anEmployee) {
            $anEmp = (new EmployeeAdvanceDataService())->processAnEmployeeAdvanceForSettingAdvanceDeductionAmount($anEmployee, (int) $request->iqama_amount, (int) $request->other_amount);
        }
        $message = 'Total ' . count($empList) . 'Employees Advance Processing Completed ';
        return json_encode(['status' => 200, 'error' => count($empIds),'info'=>$request->all(), 'message' => $message, 'success' => true]);
    }



    // UI FOrm for Cash Received From Employee
    public function emplpyeeCashDepositFormRequest()
    {
        $cashPaymentlist =[];// (new EmployeeAdvanceDataService())->getAdvancePaidRecordWithPagination(100);
        return view('admin.cash-deposit.employee-cash-deposit-form', compact('cashPaymentlist'));
    }

    public function emplpyeeCashDepositFormRequestWithAllRecords()
    {
        $cashPaymentlist = (new EmployeeAdvanceDataService())->getAdvancePaidAllRecordNoPagination(100);
        return view('admin.cash-deposit.employee-cash-deposit-form', compact('cashPaymentlist'));
    }

    //

    // Advance Paper Upload Form Request
    public function loadAdvancePaperUploadForm(){
        return view('admin.emp-loan.advance_paper_upload_form');
    }

        // Employee Advance Report Processing UI
    public function loadEmployeeAdvanceEntryReportProcessForm()
    {
            $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
            $emp_status = (new EmployeeRelatedDataService())->getEmployeeStatus();
            $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
            $users = (new AuthenticationDataService())->getUsersThoseAreInsertedEmployeeAdvanceInformation(Auth::user()->branch_office_id);

            return view('admin.report.iqama.employee_advance_report_process_form', compact('projects', 'emp_status','accomdOfficeBuilding','users'));
    }

    public function deleteCashDepositAdvancePayment($id)
    {

        $result = (new EmployeeAdvanceDataService())->deleteEmployeeCashPaymentRecord($id);
        if ($result) {
            Session::flash('success', 'Record is Deleted');
            return redirect()->route('employee.search.with.cash-payment');
        } else {
            Session::flash('error', 'Record Did not Delete. Try Again');
            return redirect()->back();
        }
    }

    // cash received from employee
    public function advancePaymentReceivedFromEmployee(Request $request)
    {

        $userId = Auth::user()->id;
        $empInfo = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpId($request->emp_id);

        $month = (new HelperController())->getMonthFromDateValue($request->payment_date);
        $year = (new HelperController())->getYearFromDateValue($request->payment_date);
        if ($empInfo == null) {
            Session::flash('error', 'Employee Not Found, Please Try Again0!');
            return redirect()->back();
        }
        // 100 = cash payment
        $insertion = (new EmployeeAdvanceDataService())->insertAdvancePaidRecord($empInfo->emp_auto_id, $request->pay_amount, $year, $month, 100, $userId);
        if ($insertion) {
            Session::flash('success', 'Successfully Completed Cash Payment ');
            return redirect()->back();
        } else {
            Session::flash('error', 'Error Occured, Please Input Correct Value');
            return redirect()->back();
        }
    }

   public function uploadAdvancePaperToServer(Request $request)
    {
        try{

             $file_path = null;
             if ($request->hasFile('advance_paper')) {
                 $file = $request->file('advance_paper');
                 $file_path = (new UploadDownloadController())->uploadAdvancePaper($file,null);
             }

             if($file_path){

                $isSuccess = false;
                $counter = 0;
                $adv_auto_id_select_list = array();
                foreach ($request->adv_auto_id_list as $adv_auto_id) {
                  if ($request->has('adv_paper_checkbox-' . $adv_auto_id)) {
                    $adv_auto_id_select_list[$counter++] = $adv_auto_id;
                  }
                }
                $isSuccess =  (new EmployeeAdvanceDataService())->updateGroupOfEmployeeAdvanceTakenPaperPath($adv_auto_id_select_list,$file_path,Auth::user()->id);
                if ($isSuccess) {
                    Session::flash('success', 'Successfully Uploaded');
                    return redirect()->back();
                } else {
                    Session::flash('error', 'Operation Failed, Please Try Again');
                    return redirect()->back();
                }

            }else {
                Session::flash('error', 'Operation Failed, Please Try Again');
                return redirect()->back();
            }
        }catch(Exception $ex){
            Session::flash('error', $ex);
            return redirect()->back();
        }
    }


    public function searchAdvanceInsertedEmployeesForUploadAdvancePaper(Request $request)
    {
         try{
                 $records = (new EmployeeAdvanceDataService())->getAdvancePaymentInsertedRecordByInsertDateWiseForUploadAdvancePaper($request->from_date,$request->to_date,Auth::user()->id);
                 return response()->json(['status'=>200,'success'=>true,'data'=>$records]);

         }catch(Exception $ex){
             return response()->json(['status'=>404,'success'=>false,'message'=>$ex]);
         }
     }

     // not used right now
    //  public function deleteAdvanceUploadedPaper($advance_date)
    // {
    //      try{

    //              $month = (new HelperController())->getMonthFromDateValue($advance_date);
    //              $year = (new HelperController())->getYearFromDateValue($advance_date);
    //              $records = (new EmployeeAdvanceDataService())->searchUploadedAdvancePaper($advance_date,$month,$year);
    //              return response()->json(['status'=>200,'success'=>true,'data'=>$records,'adv_date'=>[$advance_date,$month,$year]]);

    //      }catch(Exception $ex){
    //          return response()->json(['status'=>404,'success'=>false,'message'=>$ex]);
    //      }
    //  }




    /*
     ==========================================================================
     ========================= Employee Advance Related Report ================
     ==========================================================================
    */
    //
    public function employeeAdvanceSummaryReportProcess(Request $request)
    {


        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $projectId = $request->proj_id;
        if($request->report_type == 1){
            return $this->processEmployeeAdvancePaymentDateWiseReport($projectId,$start_date,$end_date);

        }else if($request->report_type == 2){
          return  $this->processReportOfMonthlyAdvancePaidAndDeductionSummaryReport($request->start_date, $request->end_date);
        }
        else if($request->report_type == 3){
            return $this->getProjectWiseEmployeeAdvanceSummaryReport($projectId);
        }
        else if($request->report_type == 4){
            $from_year= (new HelperController())->getYearFromDateValue($request->start_date);
            $to_year =  (new HelperController())->getYearFromDateValue($request->end_date);
            return  $this->processReportOfYearToYearAdvancePaidAndDeductionSummaryReport($from_year, $to_year);
        }
        else if($request->report_type == 5){
            $month= (new HelperController())->getMonthFromDateValue($request->start_date);
            $year= (new HelperController())->getYearFromDateValue($request->start_date);
             return  $this->processMonthlyAdvanceUnpaidEmployeeList($projectId, $month,$year);
        }
        else if($request->report_type == 6){
           return $this->processEmployeeAdvanceInsertionDatewiseReport($projectId,$start_date,$end_date);
        }
        else if($request->report_type == 7){
            return $this->getProjectWiseEmployeeOtherAdvanceSummaryReport($projectId);
        }

    }

    // Employee Advance payment datewise Report, Report_type   1
    private function processEmployeeAdvancePaymentDateWiseReport($projectId, $start_date, $end_date){

       $advanceRecords = (new EmployeeAdvanceDataService())->getEmployeesAdvancePaymentDateRecordsProjectWise($projectId, $start_date, $end_date);
       $totalAdvanceAmount = 0;// $advanceRecords->sum('adv_amount');
       $company = (new CompanyDataService())->findCompanryProfile();
        $projectName = "All";
        if (!is_null($projectId)) {
            $projectName = (new  ProjectDataService())->getProjectNameByProjectId($projectId);
        }
        $report_title = "Advance Payment Date From ".$start_date." To ".$end_date;
        return view('admin.report.iqama.employee_advance_entry_report', compact('advanceRecords', 'company', 'projectName', 'start_date', 'end_date', 'totalAdvanceAmount','report_title'));

    }
     // Advance Paid and Deduction Summary Report , Report_type   2
    private function processReportOfMonthlyAdvancePaidAndDeductionSummaryReport($from_date, $to_date){

          $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);
          $report_data = array();
          $counter = 0;
          $gross_advance =0;
          $gross_deduction = 0;
          foreach ($monthwithYears as $my) {

               $last_day = (new HelperController())->getNumberOfDaysInMonthAndYear($my['month'],$my['year']);
               $from_date = $my['year']. '-' . $my['month'] .'-'. '1';
               $to_date =  $my['year']. '-' . $my['month'] .'-'. $last_day;

              $total_amount_adv_to_emp = (new EmployeeAdvanceDataService())->getSummationOfAdvancePayToEmployeeTotalAmount($from_date,$to_date);
              $total_amount_dediction = (new SalaryProcessDataService())->getTotalAmountOfAdvanceDeductionFromSalary($my['year'],$my['month']);

              $gross_advance += $total_amount_adv_to_emp;
              $gross_deduction += $total_amount_dediction;

              $arecord = array(
                'year' => $my['year'],
                'month_name' => (new HelperController())->getMonthName($my['month']),
                'month' => $my['month'],
                'total_amount_adv_to_emp' => (float) $total_amount_adv_to_emp,
                'total_amount_dediction' => (float) $total_amount_dediction,

              );
            $report_data[$counter++] = $arecord;

          }
         $company = (new CompanyDataService())->findCompanryProfile();
         return view('admin.report.iqama.monthly_adv_paid_deduction_summary_report', compact('report_data','gross_advance','gross_deduction', 'company'));

    }

    // Employee Advance Taken and Paid Report , Report_type   3
    public function getProjectWiseEmployeeAdvanceSummaryReport($projectId){
          $projectWiseEmp = (new EmployeeDataService())->getEmployeesInformationRecordsByProjectId($projectId);

          if ($projectWiseEmp == NULL) {
              Session::flash('no_record', 'Opps! No Records Found With This Informations');
              return redirect()->back();
          }
          $counter = 0;
          foreach ($projectWiseEmp as $anEmployee) {
              $projectWiseEmp[$counter++] = (new EmployeeAdvanceDataService())->getAnEmployeeAdvanceSummaryReport($anEmployee);
          }
          $company = (new CompanyDataService())->findCompanryProfile();
          $projectName = (new ProjectDataService())->getProjectNameByProjectId($projectId);
          return view('admin.report.iqama.emp_advance_summary_report', compact('projectWiseEmp', 'company', 'projectName'));

    }


      // Year to Year Adance and Deduction Summary Report, Report_type   4
    private function processReportOfYearToYearAdvancePaidAndDeductionSummaryReport($from_year, $to_year){

        $report_data = array();
        $counter = 0;
        $gross_advance =0;
        $gross_deduction = 0;

        for($from_year = $from_year; $from_year <= $to_year ; $from_year++) {

            $total_amount_adv = (new EmployeeAdvanceDataService())->getTotalAmountOfAdvanceToEmployeeByYearToYear($from_year);
            $total_amount_deduction = (new SalaryProcessDataService())->getTotalAmountOfAdvanceDeductionFromSalaryByYearToYear($from_year);
            $gross_advance += $total_amount_adv;
            $gross_deduction += $total_amount_deduction;
            $arecord = array(
              'year' =>  $from_year,
              'total_amount_adv_to_emp' => (float) $total_amount_adv,
              'total_amount_deduction' => (float) $total_amount_deduction,
            );
          $report_data[$counter++] = $arecord;

        }
      //  dd($report_data);
       $company = (new CompanyDataService())->findCompanryProfile();
       return view('admin.report.iqama.year_to_year_adv_summary', compact('report_data','gross_advance','gross_deduction', 'company'));

    }

    // Monthly Advance Unpaid Employee List Report, Report_Type = 5
    private function processMonthlyAdvanceUnpaidEmployeeList($projectId,$month_value,$year){

         $advanceRecords = (new EmployeeAdvanceDataService())->getEmployeesThoseAreNotPaidAdvanceFromSalaryDeduction($projectId,$month_value,$year);
         $company = (new CompanyDataService())->findCompanryProfile();
         $projectName = "All";
         if (!is_null($projectId)) {
             $projectName = (new  ProjectDataService())->getProjectNameByProjectId($projectId);
         }
         $month_name = (new HelperController())->getMonthName($month_value);
         return view('admin.report.iqama.monthly_advance_unpaid_emps_report', compact('advanceRecords', 'company', 'projectName', 'month_name','year' ));
    }

     // Employee Advance Insertion datewise Report, Report_type   6
     private function processEmployeeAdvanceInsertionDatewiseReport($projectId, $start_date, $end_date){

         $advanceRecords = (new EmployeeAdvanceDataService())->getEmployeesAdvancePaymentRecordWithInsertDateAndProjectWise($projectId, $start_date, $end_date);
         $totalAdvanceAmount = 0;
         $company = (new CompanyDataService())->findCompanryProfile();
         $projectName = "All";
         if (!is_null($projectId)) {
             $projectName = (new  ProjectDataService())->getProjectNameByProjectId($projectId);
         }
         $report_title = "Advance Inserted From ".$start_date." To ".$end_date;
         return view('admin.report.iqama.employee_advance_entry_report', compact('advanceRecords', 'company', 'projectName', 'start_date', 'end_date', 'totalAdvanceAmount','report_title'));

    }

    // Employee Other Advance Taken and Paid Report , Report_type   7
    public function getProjectWiseEmployeeOtherAdvanceSummaryReport($projectId)
    {
        $records = (new EmployeeAdvanceDataService())->getEmployeeOtherAdvanceSummaryReportByProjectId($projectId);
        $company = (new CompanyDataService())->findCompanryProfile();
        $report_title[0] = (new ProjectDataService())->getProjectNameByProjectId($projectId);
        return view('admin.report.iqama.emp_other_adv_summary_report', compact('records', 'company', 'report_title'));

    }


     // Single Employee Advance Search For Report
    public function singleEmployeeAdvanceListSearchForReport(Request $request)
    {
        try{

            if($request->report_type == 1){
                // an employee all avance record
                $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->empID,'employee_id',Auth::user()->branch_office_id);
                if(!$employee){
                    return 'Employee Not Found';
                }
                $advance_records = (new EmployeeAdvanceDataService())->getAnEmployeeFiscalYearAdvanceAllRecordsByEmpoyeeAutoID($employee->emp_auto_id,null,null);
                $report_title = " ID Employee Advance Records";
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

               $prepared_by = Auth::user()->name;
               return view('admin.report.emp_advance.singleEmp_advace_report' , compact('employee','prepared_by','company', 'advance_records', 'report_title'));

            }else{

                $allEmplId = explode(",", $request->empID);
                $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
                $advance_records = [];
                $counter =0;
                foreach($allEmplId as $emp){
                    $anEmRecord = (new EmployeeAdvanceDataService())->getAnEmployeeAdvanceLastRecordsReportByEmpoyeeID($emp,Auth::user()->branch_office_id);
                    if($anEmRecord){
                        $advance_records[$counter++] = $anEmRecord;
                    }
                }
                $report_title = " Multiple Employee Last Advance";
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                $prepared_by = Auth::user()->name;
               return view('admin.report.emp_advance.multi_emp_last_adv_report' , compact('prepared_by','company', 'advance_records', 'report_title'));
            }

        }catch(Exception $ex){
            Session::flash('error', 'System Operation Failed!');
            return redirect()->back();
        }

    }

    //4 Employee Advance Inserted By User Report
    public function processEmployeeAdvanceInsertedByUserReport(Request $request){

      try{
        $advance_records = (new EmployeeAdvanceDataService())->processEmployeeAdvanceReportByInsertedUser($request->user_id,$request->start_date,$request->end_date,Auth::user()->branch_office_id);
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $report_title = "Advance Inserted \n From".$request->start_date." To ".$request->end_date;
        return view('admin.report.emp_advance.emp_advance_inserted_by_user_report', compact('advance_records', 'company','report_title'));

      }catch(Exception $ex){
        return $ex;
      }


    }






    }
