<?php

namespace App\Http\Controllers\Admin\Promosion;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeePromotionDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Models\EmpJobExperience;
use App\Models\EmpContactPerson;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class PromosionController extends Controller
{
    protected $employee_searching_by_employee_id = 'employee_id';

  function __construct()  {

       $this->middleware('permission:employee-promotion', ['only' =>
        ['index','insertPromosion','approvalRequestOfPromotedEmployees','getPromotedEmployeesWaitingForApprovalRecords',
        'promotedEmployeePromotionPaperUploadRequest','showEmployeePromotionDetailsDateToDateReport']]);
       $this->middleware('emp_promotion_record_delete',['only'=>'deleteAPromotionRecordInsertedRecord']);
  }


  public function findEmployee(Request $request)
  {
    $employee_id = $request->emp_id;
    $emp = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);
    $findEmployee =  (new EmployeeDataService())->getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($emp->emp_auto_id);
    $find_job_experience = EmpJobExperience::where('emp_id', $emp->emp_auto_id)->get();
    $find_emp_contact_person = EmpContactPerson::where('emp_id', $emp->emp_auto_id)->get();

    // return json_encode($findEmployee);
    return json_encode([
      'find_job_experience' => $find_job_experience,
      'find_emp_contact_person' => $find_emp_contact_person,
      'findEmployee' => $findEmployee,
    ]);
  }


  // Find An Employee Details
  public function findEmployeeDetails(Request $request)
  {

    $employee_id = $request->emp_id;
    $iqamaNo = $request->iqamaNo;

    if ($employee_id != "") {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);
    } else {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
    }

    if ($employee) {

      $findEmployee = (new EmployeeDataService())->getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($employee->emp_auto_id);
      $salary = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($employee->emp_auto_id);
      $designation =   (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
      $find_job_experience = EmpJobExperience::where('emp_id', $employee->emp_auto_id)->get();
      $find_emp_contact_person = EmpContactPerson::where('emp_id', $employee->emp_auto_id)->get();


      return json_encode([
        'find_job_experience' => $find_job_experience,
        'find_emp_contact_person' => $find_emp_contact_person,
        'findEmployee' =>  $findEmployee,
        'salary' => $salary,
        'designation' => $designation,
      ]);
    } else {
      return json_encode([
        'status' => "error",
      ]);
    }
  }


  // Find An Employee advance adjustment now calling from Cash Recive From Empl MEnu
  public function findEmployeeadjustment(Request $request)
  {

    $employee_id = $request->emp_id;
    $year = date('Y', strtotime(Carbon::now()));
    $iqamaNo = $request->iqamaNo;

    if ($employee_id != "") {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);
    } else {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
    }

    if ($employee) {
      /* ========== Employee Salary info ========== */

      $findEmployee = (new EmployeeDataService())->getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($employee->emp_auto_id);

      $salary = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($employee->emp_auto_id);


      $designation =   (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();

      $find_job_experience = EmpJobExperience::where('emp_id', $employee->emp_auto_id)->get();

      $find_emp_contact_person = EmpContactPerson::where('emp_id', $employee->emp_auto_id)->get();

      /* ====== return json ====== */

      return json_encode([
        'find_job_experience' => $find_job_experience,
        'find_emp_contact_person' => $find_emp_contact_person,
        'findEmployee' =>  $findEmployee,
        'salary' => $salary,
        'designation' => $designation,
      ]);
    } else {
      return json_encode([
        'status' => "error",
      ]);
    }
  }


  // Find An Employee for update employee Project and job Status
  public function findEmployeeStatus(Request $request)
  {
    $employee_id = $request->emp_id;
    $iqamaNo = $request->iqamaNo;

    if ($employee_id != "") {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($employee_id);
    } else {
      $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
    }
    if ($employee) {
      $getAllProject = (new ProjectDataService())->getAllActiveProjectListForDropdown();

      $employeeStatusOBJ = new HelperController();
      $allEmployeeStatus = $employeeStatusOBJ->getEmployeeStatus();


      $findEmployee = (new EmployeeDataService())->getEmployeeInformationWithCountryDivDistEmpTypeEmpDepartCateProject($employee->emp_auto_id);
      $salary = (new EmployeeDataService())->getAnEmployeeSalaryDetailsByEmpAutoId($employee->emp_auto_id);
      $designation =   (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
      $agencies = (new CompanyDataService())->getAllAgencies();
      $find_job_experience = EmpJobExperience::where('emp_id', $employee->emp_auto_id)->get();
      $find_emp_contact_person = EmpContactPerson::where('emp_id', $employee->emp_auto_id)->get();

      /* ====== return json ====== */
      return json_encode([
        'getAllProject' => $getAllProject,
        'allEmployeeStatus' => $allEmployeeStatus,
        'find_job_experience' => $find_job_experience,
        'find_emp_contact_person' => $find_emp_contact_person,
        'findEmployee' =>  $findEmployee,
        'salary' => $salary,
        'designation' => $designation,
        'agencies' => $agencies
      ]);
    } else {
      return json_encode([
        'status' => "error",
      ]);
    }
  }



  /* ++++++++++++++++ Insert Promoted Salary Info ++++++++++++++++ */
  public function insertPromosion(Request $request)
  {
       try{
              $this->validate($request, [
                'emp_id' =>'required',
                'prom_date' => 'required',
                'promotion_by' => 'required',
                'hourly_rent' => 'required',
            ], []);

            $emp_auto_id = $request->emp_id;
            $increment_amount = 0;
            $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoId($emp_auto_id);

              if($anEmployee == null){
                Session::flash('error', 'Employee Not Found, Please Refresh Page and Try Again');
                return Redirect()->back();
              }

              if($anEmployee->hourly_employee){
                $increment_amount =  $request->hourly_rent - $anEmployee->salarydetails->hourly_rent;
              }else {
                $total_amount =  $request->basic_amount + $request->house_rent + $request->mobile_allowance + $request->food_allowance + $request->medical_allowance +
                $request->local_travel_allowance + $request->conveyance_allowance + $request->others1;

                $increment_amount =  $total_amount - (new EmployeeDataService())->getAnBasicEmployeeTotalSalaryAmountByEmpAutoId($emp_auto_id);
              }
            $prom_apprv_documents = "";
            if ($request->hasFile('prom_approve_documents')) {
                $file = $request->file('prom_approve_documents');
                $prom_apprv_documents = (new UploadDownloadController())->uploadEmployeePromotionApprovedPhoto($file, null);
            }

            $insertPromosion = (new EmployeePromotionDataService())->insertAnEmployeePromotionDetailsInfo($emp_auto_id, $request->designation_id, $request->emplyoeeDesignation,
            $request->basic_amount, $request->hourly_rent, $request->mobile_allowance, $request->food_allowance, $request->medical_allowance,$request->house_rent, $request->local_travel_allowance,
            $request->conveyance_allowance, $request->others1, $request->promotion_by, $request->prom_date, $request->prom_remarks,$prom_apprv_documents,$increment_amount);

            if ($insertPromosion) {

               // login user activities record
              $salary_amount = $request->basic_amount > 0 ? $request->basic_amount : $request->hourly_rent;
              (new AuthenticationDataService())->InsertLoginUserActivity(13,1, Auth::user()->id,$emp_auto_id,$salary_amount);

               Session::flash('success', 'Employee Promotion Successfully Completed');
              return Redirect()->back();
            } else {
              Session::flash('error', 'Something went wrong, Please try again');
              return Redirect()->back();
            }

       }catch(Exception $ex){
          Session::flash('error', 'System Exception Occured');
          return Redirect()->back();
       }

  }

  public function deleteAPromotionRecordInsertedRecord(Request $request){
    try{
          $arecord =  (new EmployeePromotionDataService())->getAnEmployeePromotionRecordInfoByPromotionAutoId($request->promotion_auto_id);
          if($arecord){
            if($arecord->approval_status){
                return response()->json(['status'=>405,'success'=>false,'message'=>"Already Approved, Deletion Not Allowed",'error'=>'error']); // 405 Mehtod not allowed
            }else{
                $isDeleted =  (new EmployeePromotionDataService())->deleteAPromotionRecordThatApprovelPendingByPromotionAutoId($request->promotion_auto_id);
                if($isDeleted){

                   // login user activities record
                  (new AuthenticationDataService())->InsertLoginUserActivity(28,3, Auth::user()->id,$arecord->emp_id,null);

                  return response()->json(['status'=>200,'success'=>true,'message'=>"Successfully Deleted" ]);
                } else{
                  return response()->json(['status'=>405,'success'=>false,'message'=>"delete Operation Failed",'error'=>'error']);
                }
            }
          }else{
                return response()->json(['status'=>405,'success'=>false,'message'=>"Record Not Found",'error'=>'error']);
          }
    }catch(Exception $ex){
          return response()->json(['status'=>405,'success'=>false,'message'=>"System Operation Failed",'error'=>'error']);
    }

  }

  public function approvalRequestOfPromotedEmployees(Request $request){

      $emp_prom_ids = $request->emp_prom_auto_id;
      $counter = 0;
      foreach ($emp_prom_ids as $prom_id){
          if($request->has('promoted_emp_checkbox-'.$prom_id)){

              $promotion_record =  (new EmployeePromotionDataService())->getAnEmployeePromotionRecordInfoByPromotionAutoId($prom_id);
              $update = (new EmployeePromotionDataService())->approveAnEmployeePromotionRecord($prom_id,Carbon::now(),Auth::user()->id);
              if($update && $promotion_record){

                $updateSalaryDetails =  (new EmployeeDataService())->updateEmployeeSalaryDetailsInformationAsPromotionByEmpAutoId(
                  $promotion_record->emp_id,
                  $promotion_record->basic_amount,
                  $promotion_record->hourly_rent,
                  $promotion_record->house_rent,
                  $promotion_record->mobile_allowance,
                  $promotion_record->local_travel_allowance,
                  $promotion_record->conveyance_allowance,
                  $promotion_record->medical_allowance,
                  $promotion_record->increment_no,
                  $promotion_record->increment_amount,
                  $promotion_record->others1,
                  $promotion_record->food_allowance
                );
              }
              $counter++;
            (new EmployeeDataService())->updateEmployeeDesignation($promotion_record->emp_id, $promotion_record->new_designation_id);

             // login user activities record
             $salary_amount = $promotion_record->basic_amount > 0 ? $promotion_record->basic_amount : $promotion_record->hourly_rent;
             (new AuthenticationDataService())->InsertLoginUserActivity(27,2, Auth::user()->id,$promotion_record->emp_id,$salary_amount);

          }
      }

      if ($counter>0) {
        Session::flash('success', 'Successfully Completed');
        return Redirect()->back();
      } else {
        Session::flash('error', 'Something went wrong, Please try again');
        return Redirect()->back();
      }

  }

  public function getPromotedEmployeesWaitingForApprovalRecords(Request $request){
      try{
          $records = (new EmployeePromotionDataService())->getEmployeePromotionApprovalWaitingRecords($request->from_date, $request->to_date,Auth::user()->banch_office_id);
          return response()->json(['status'=>200,'success'=>true,'data'=>$records]);
      }catch(Exception $ex){
        return response()->json(['status'=>405,'success'=>false,'message'=>$ex,'error'=>'error']); // 405 Mehtod not allowed

      }
  }

     /* =============== Multiple EMPLOYEE Promotion Paper Upload=============== */
  public function promotedEmployeePromotionPaperUploadRequest(Request $request){

      try{

            $file_path = null;
            if ($request->hasFile('upload_paper') && $request->has('emp_prom_auto_id')) {
                $file = $request->file('upload_paper');
                $file_path = (new UploadDownloadController())->uploadEmployeePromotionApprovedPhoto($file, null);
            }
          if($file_path){

               $isSuccess = false;
               $counter = 0;
               $id_select_list = array();
               foreach ($request->emp_prom_auto_id as $emp_prom_id) {

                   if ($request->has('promoted_emp_checkbox-' . $emp_prom_id)) {
                   $id_select_list[$counter++] = $emp_prom_id;
                 }
               }
               $isSuccess =  (new EmployeePromotionDataService())->updatePromotedMultipleEmployeePromotionPaper($id_select_list,$file_path,Carbon::now(),Auth::user()->id);
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





     // Show Promoted Employee Report
  public function showEmployeePromotionDetailsDateToDateReport(Request $request){

   //  dd($request->all());
        if((int)$request->report_type == 1){

            if(!is_null($request->employee_ids)){
                $employee = (new EmployeeDataService)->searchingAnEmployeeIsExistInSystemByMultitypeParameter($request->employee_ids, $this->employee_searching_by_employee_id);
                $records = (new EmployeePromotionDataService())->getAnEmployeePromotionDetailsRecords($employee->employee_id);
              }else {
                $records = (new EmployeePromotionDataService())->getEmployeePromotionDetailsDateToDate(0, $request->from_date, $request->to_date);
              }

              $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
               return view('admin.report.emp_increment.employee_promotion_report', compact('records', 'company'));

        }else if($request->report_type == 2){


             if($request->employee_ids != null){
                $multiple_employee_id = explode(",", $request->employee_ids);
                $multiple_employee_id = array_unique($multiple_employee_id); // remove multiple same empl ID
                $employee_list = (new EmployeeDataService)->getAnEmployeesInfoWithSalaryDetailForEmployeePromotionDetailsReport($multiple_employee_id,Auth::user()->branch_office_id);

            }else{
                
                if ($request->project_ids == null) {
                    $request->project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
                }else{
                    $request->project_ids = [ $request->project_ids];
                }

                if ($request->designation_heads == null) {
                    $request->designation_heads = (new EmployeeRelatedDataService())->getAllActiveDesignationHeadIDs();
                }
                else {
                    $request->designation_heads = [ $request->designation_heads ];
                }
                $employee_list = (new EmployeeDataService)->getListOfEmployeesInfoWithSalaryDetailForEmployeePromotionDetailsReport($request->project_ids,$request->designation_heads,Auth::user()->branch_office_id);

            }


            $counter = 0;
            $employees = array();
            foreach($employee_list as $emp){
                $emp->joining_salary = (new SalaryProcessDataService())->getAnEmployeeJoiningMonthAllIncludedTotalSalaryAmount($emp->emp_auto_id,$emp->hourly_employee);
               // $emp->current_salary = (new EmployeeDataService())->getAnBasicEmployeeTotalSalaryAmountByEmpAutoId($emp->emp_auto_id);
                $records = (new EmployeePromotionDataService())->getAnEmployeePromotedAllRecordsAmountRelatedDetailsInformation($emp->emp_auto_id);
                if(count($records) >0){
                    $emp->promotion_records = $records;
                    $employees[$counter++] = $emp;
                }

            }

          //  dd($request->all(),$employees);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
            $login_user_name= Auth::user()->name;
            return view('admin.report.emp_increment.promoted_emp_history_report', compact('employees', 'company','login_user_name'));
        }

  }




  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
  public function index()
  {
      $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
      $designation_heads = (new EmployeeRelatedDataService())->getDesignationHeadRecordsForDropdown();
      return view('admin.employee-promosion.all',compact('projects','designation_heads'));
  }




}
