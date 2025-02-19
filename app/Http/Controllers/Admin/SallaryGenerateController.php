<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Permission\SalaryProcessPermissionController;
use App\Http\Controllers\Admin\CPF\ContributionController;
use App\Http\Controllers\Admin\EmployeeMultiProjectWorkHistoryController;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\DataServices\CateringDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Exports\SalaryReport1ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\SalarySheetUpload;
use App\Enums\SalaryPaymentMethodEnum;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SallaryGenerateController extends Controller
{

 // ALTER TABLE `salary_histories` ADD `slh_bonus_amount` FLOAT(6) NOT NULL DEFAULT 0 AFTER `slh_other_advance`;

function __construct()
{
     $this->middleware('permission:salary-processing', ['only' => ['create','','employeeSalaryProcessAJAXRequest','singleEmployeeMonthWiseSalaryReport',
     'deleteAnEmployeeUnpaidSalaryRecord','updateAnEmployeeSalaryRecordBySalaryHistoryAutoId','SalaryPendingList']]);
     $this->middleware('permission:salary-pending', ['only' => ['loadSalaryPendingEmployeeListWithUI','SalaryPendingList','loadSalarySheetUploadUI','SalaryPaymentToUnPay','SalaryPayment','SalarypaidList',
     'Salarypaid','SalarySheet']]);
}


  /* ===================== All Employee Store Salary Record ===================== */
  /* ============== Salary Processing With Form SUBMIT Not Use IT ============== */
  public function employeeSalaryProcessAJAXRequest(Request $request)
  {



    try {
      $month = $request->month;
      $year = $request->year;
      $project_id = $request->proj_id;
      $loginUserId = Auth::user()->id;

      $sponsor_ids = $request->has('sponsor_ids') ? $request->sponsor_ids : (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();


      $hasAccessPermission = (new SalaryProcessPermissionController())->checkSalaryProcessPermission($month, $year);

      if ($hasAccessPermission) {

        $allEmployeeMontlySalaryDetails = (new SalaryProcessDataService())->getListOfEmployeesWithWorkRecordsForProcessingSalary($month, $year, $project_id,$sponsor_ids,Auth::user()->branch_office_id);


        if ($allEmployeeMontlySalaryDetails == NULL) {
          return json_encode(['error' => 'Error', 'message' => 'No Record Found', 'success' => false, 'status' => 404]);
        }

        foreach ($allEmployeeMontlySalaryDetails as $anEmployee) {

          $this->processAnEmployeeSalaryCalculation($anEmployee, $month, $year, $loginUserId);
        }
         // login user activities record
        (new AuthenticationDataService())->InsertLoginUserActivity(16,1, Auth::user()->id,Auth::user()->emp_auto_id,null);

        return json_encode(['success' => true, 'message' => 'Processing Completed', 'status' => 200]);
      } else {
        return json_encode(['error' => 'Error', 'message' => 'Access Permission Not Allowed', 'success' => false, 'status' => 404]);
      }
    } catch (Exception $ex) {
        return json_encode(['error' => 'Error', 'message' => $ex, 'success' => false, 'status' => 404]);
    }

  }



  private function processAnEmployeMultipleProjectWorkSalary($anEmployee, $month, $salaryYear){

      $records = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordsOnly($anEmployee->emp_auto_id,$month,$salaryYear);
      foreach($records as $arecord){
       // dd($anEmployee,$records);
        $total_work_day = $arecord->total_day;
        $overtime = $arecord->total_overtime;
        $total_hour =  $arecord->total_hour;
        $new_record  = (new SalaryProcessDataService())->calculateAnEmployeeMultipleProjectSalaryForAMonth($anEmployee,$arecord);
        $food_amount = 0;
        if( ((float)$anEmployee->food_allowance) > 0 && ((int)$anEmployee->total_work_day) > 0) {
          $food_amount = round((($anEmployee->food_allowance / $anEmployee->total_work_day) * $arecord->total_day),2);
        }
        (new SalaryProcessDataService())->updateEmployeeMultipleProjectWorkSalaryAmount($arecord->empwh_auto_id,
        ($new_record->total_amount + $food_amount),$food_amount, $new_record->other_amount,$new_record->ot_amount,Auth::user()->id);
      }

  }

  private function processAnEmployeeSalaryCalculation($anEmployee, $month, $salaryYear, $loginUserId)
  {
    try {

      $duplicateSalary  = (new SalaryProcessDataService())->getAnEmployeeSalaryRecord($anEmployee->emp_auto_id, $month, $salaryYear);
      // Salary Already Paid so  Processing is not Possible
      $thisMonthIqamaPaidAmount = 0;
      if ($duplicateSalary != null) {
        if ($duplicateSalary->Status == 1) {
          return $duplicateSalary;
        }
        $thisMonthIqamaPaidAmount = $duplicateSalary->slh_iqama_advance;
      }

      $anEmployee->bonus_amount = (new SalaryProcessDataService())->getAnEmployeeBonusAmountByEmpAutoIdAndMonthYear($anEmployee->emp_auto_id, $month, $salaryYear);

      $totalOthers = ($anEmployee->house_rent + $anEmployee->conveyance_allowance + $anEmployee->mobile_allowance  + $anEmployee->medical_allowance + $anEmployee->others1 + $anEmployee->local_travel_allowance + $anEmployee->bonus_amount);

      $tem_anEmployee  = (new SalaryProcessDataService())->calculateOvertimeHoursAndAmount($anEmployee);
      $anEmployee->slh_overtime_amount = $tem_anEmployee->slh_overtime_amount;

      $anEmployee->food_allowance = (new SalaryProcessDataService())->calculateFoodAllowance($anEmployee->total_work_day, $anEmployee->food_allowance);
      $anEmployee->slh_all_include_amount = $tem_anEmployee->tem_total_amount + $totalOthers + $anEmployee->food_allowance;

      $catering_food = (new CateringDataService())->getAnEmployeeAMonthCateringRecordForSalaryProcessingByEmployeeAutoId($anEmployee->emp_auto_id, (int) $month, $salaryYear);
      $anEmployee->slh_food_deduction = 0;
      if($catering_food){
        $anEmployee->slh_food_deduction = $catering_food->amount;
      }

      $total_deduct_amount = $anEmployee->slh_food_deduction + $anEmployee->saudi_tax + $anEmployee->iqama_adv_inst_amount + $anEmployee->other_adv_inst_amount + $anEmployee->cpf_contribution ;
      $anEmployee->gross_salary =  $anEmployee->slh_all_include_amount -  $total_deduct_amount ;

      $anEmployee->work_multi_project = (new  EmployeeMultiProjectWorkHistoryController())->getIsAnEmployeeMultiprojectWorkHistory($anEmployee->emp_auto_id, $month, $salaryYear);
      // calculate an employee multiple project working salary for single month
      $this->processAnEmployeMultipleProjectWorkSalary($anEmployee,$month,$salaryYear);

      // ================ End Calculation =================

     // dd($anEmployee);
      if ($duplicateSalary) {
        if ($duplicateSalary->Status == 0) {
            // if salary is not paid yet update salary record
           (new SalaryProcessDataService())->updateAnEmployeeMonthlySalaryRecord($duplicateSalary->slh_auto_id, $anEmployee, $month, $salaryYear);
        }
      } else {
         // Insert Salary Record as First Time Insert
         (new SalaryProcessDataService())->updateAnEmployeeMonthlySalaryRecord(-1, $anEmployee, $month, $salaryYear);
      }

      return $anEmployee;
    } catch (Exception $ex) {
        dd("System Exception",$ex,$anEmployee);
    }
  }

  /* ===================== Single Employee Salary Processing & Report ===================== */
  public function singleEmployeeMonthWiseSalaryReport(Request $request)
  {

        $salaryYear = $request->year;
        $month = $request->month;
        $anEmployee = (new SalaryProcessDataService())->getAnEmployeeDetailsWithMonthlyWorkRecordForSalaryProcessing( $request->emp_id,Auth::user()->branch_office_id,$month, $salaryYear);
        $loginUserId = Auth::user()->id;

        if ($anEmployee == NULL ) {
          Session::flash('error', 'Employee Information or Working Record Not Found');
          return redirect()->back();
        }else if (!(new SalaryProcessPermissionController())->checkSalaryProcessPermission($month, $salaryYear )) {
          Session::flash('error', 'Salary Processing System Already Locked');
          return redirect()->back();
        }
        else if ($anEmployee->total_work_day == NULL) {
          Session::flash('error', 'Employee Monthly Work Record Not Found');
          return redirect()->back();
        }

        $anEmp  = (new EmployeeAdvanceDataService())->processAnEmployeeAdvanceForFiscalYear($anEmployee);
        $anEmployee->iqama_adv_inst_amount = $anEmp->iqama_adv_inst_amount;
        $anEmployee->other_adv_inst_amount = $anEmp->other_adv_inst_amount;

        $empSalaryUpdated =  $this->processAnEmployeeSalaryCalculation($anEmployee, $month, $salaryYear, $loginUserId);
        if ($empSalaryUpdated) {

           // login user activities record
          (new AuthenticationDataService())->InsertLoginUserActivity(16,1, Auth::user()->id,$anEmployee->emp_auto_id,null);

          $year = $request->salaryYear;
          $monthName = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
          $salary = (new SalaryProcessDataService())->getAnEmployeeInfoWithSalaryHistory($anEmployee->emp_auto_id, $month, $salaryYear);

          if ($salary == NULL) {
            $allSalaryAmount = 0;
            $iqamaAmount = 0;
            $totalHours = 0;
          } else {
            $allSalaryAmount =  $salary->slh_total_salary;
            $iqamaAmount =  $salary->slh_iqama_advance;
            $totalHours =  $salary->slh_total_hours;
          }
          if ($salary) {
            $login_name = Auth::user()->name;
            return view('admin.salary-generate.single_employee.salary', compact('login_name', 'salary', 'monthName', 'salaryYear', 'company', 'allSalaryAmount', 'iqamaAmount', 'totalHours'));
          }
        } else {
          Session::flash('error', 'Single Employee Salary Processing Error');
          return redirect()->back();
        }
  }


  /* ==================== 2 Projet & Employee Status base Salary Report ==================== */
  public function projectEmployeeStatusMonthWiseSalaryReport(Request $request)
  {
     //  dd($request->all());
      if($request->salary_report_type == 1){
          // now working this project salary
           return $this->salaryReportThoseAreWorkingNowInthisProject($request->proj_id,$request->month,$request->year,$request->salary_status);
      }else if($request->salary_report_type == 2){
         // last month salary
          return $this->projectWiseEployeeLastSalaryHistory($request->proj_id);
      }else if($request->salary_report_type == 3 ){
          return $this->processAndDisplayProjectwiseEmployeeSalaryReportWithPaidUnpaidStatus($request->proj_id,$request->month,$request->salary_status,$request->year,$request->emp_status_id);
      }
      else if($request->salary_report_type == 4 || $request->salary_report_type == 5){
          return $this->processAndDisplayEmployeesThoseAreNotReceivedSalary($request->month,$request->year,$request->salary_report_type);
      }
       else {
          return "Please Select Report Type and Try Again.";
      }


  }

    // salary_report_type  1 now working this project salary
    private function salaryReportThoseAreWorkingNowInthisProject($project_id,$month,$year,$salary_status){

      $salaryReport =  (new SalaryProcessDataService())->getEmployeesSalaryReportThoseAreWorkingThisProject($project_id,$month,$year,$salary_status);
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
      $monthName = (new HelperController())->getMonthName($month);
      $reportLeftTitle = ['Salary Month of '.$monthName.', '.$year,('Project: '.$project_name),''];
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.employee-salary-details', compact('login_name','salaryReport', 'reportLeftTitle', 'company'));

    }

    // salary_report_type 2  last month salary
    private function projectWiseEployeeLastSalaryHistory($project_id){

        $employeeList = (new EmployeeDataService())->getAllEmployeesInformationByProjectId($project_id,1);
        $salaryReport =  array();
        $counter = 0;
        foreach($employeeList as $emp){
          $srecord =  (new SalaryProcessDataService())->getAnEmployeeLastMonthSalaryRecord($emp->emp_auto_id);
          if($srecord){
            $salaryReport[$counter++] = $srecord;
          }
        }
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
        $reportLeftTitle = ['Employees Last Month Salary Details',('Project: '.$project_name),''];
        $login_name = Auth::user()->id;
        return view('admin.salary-generate.projectwise.employee-salary-details', compact('login_name','salaryReport', 'reportLeftTitle', 'company'));

    }

    // salary_report_type 3 projec base paid unpaid salary
    private function processAndDisplayProjectwiseEmployeeSalaryReportWithPaidUnpaidStatus($projectId, $month, $salaryStatus, $salaryYear, $empStatusId){

        $monthName = (new HelperController())->getMonthName($month);
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $project = (new EmployeeRelatedDataService())->findAProjectInformation($projectId);
        $employeeStatus = 'All';
        $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryHistoryWithProjectAndEmployeeJobStatus($projectId, $month, $salaryYear, $salaryStatus);
        $login_name = Auth::user()->id;
        return view('admin.salary-generate.projectwise.statuswise-salary-report', compact('login_name','salaryReport', 'monthName', 'salaryYear', 'company', 'project',  'employeeStatus'));
    }
    // salary_report_type 4 & 5  salary_report_type 4&5 those are not received salary
    private function processAndDisplayEmployeesThoseAreNotReceivedSalary($month,$year,$salary_report_type){

        $no_of_month = $salary_report_type == 4 ? 1 : 2;
        $employeeList = (new EmployeeDataService())->getAllEmployeesForFindingEmployeeThoseAreNotReceivedSalary();
        $report =  array();
        $counter = 0;
        foreach($employeeList as $emp){
          $salary_record1 =  (new SalaryProcessDataService())->findAnEmployeeThoseAreNotReceivedSalary($emp->emp_auto_id,$month,$year);
          if($salary_record1->count() >= $no_of_month){
              $emp->salary_record = $salary_record1;
              $report[$counter++] = $emp;
          }
        }

        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $reportLeftTitle = ['Employee Those Not Received Salary','Project: All',''];
        $login_name = Auth::user()->name;
        return view('admin.salary-generate.projectwise.salary_unpaid_emp_with_month', compact('login_name','report', 'reportLeftTitle', 'company'));

    }



  /* ==================== 3 Projet & Employee Type (Basic Salary, Hourly,Direct) salary report ==================== */
  public function projectEmployeeMonthWiseSalaryReport(Request $request)
  {

    $projectId = $request->proj_id;
    $month = $request->month;
    $salaryYear = $request->year;
    $salaryStatus = $request->salary_status; // 0 = Unpaid 1= Paid
    $empType = $request->emp_type_id;
    $monthName = (new HelperController())->getMonthName($month);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

      $project = 'All';
      if($projectId != null){
          $project = (new EmployeeRelatedDataService())->getProjectNameByProjectId($projectId);
      }

      $employeeType = 'All';
      $hourly_employee = null;
      $emp_type = null;

      if ($empType == -1) {  // Basic Salary direct Employee
        $hourly_employee = null;
        $emp_type = 1;
        $employeeType = "Direct Employee-Basic Salary" ;
      } else if ($empType == 1) { // Hourly salary direct Employee
        $hourly_employee = true;
        $employeeType = "Direct Employee-Hourly" ;
        $emp_type = 1;
      }else if ($empType == 2) {   // Indirect Employee
        $employeeType = 'Indirect Employee';
        $hourly_employee = NULL;
        $emp_type = 2;
      } else if ($empType == 3) {
        $employeeType = "Basic Salary(Indirect & Direct) Employees";
        $hourly_employee = NULL;
        $emp_type = 3;
      }

        // only this projectwork salary  report
      if($request->projec_cost_check){
        return $this->processAndDisplayOnlyThisProjectWorkingEmployeesSalaryFromMultiProjectRecord($request->month,$request->year,$request->proj_id,$emp_type == null?0:$emp_type,$hourly_employee);
      }

      // salary status show/hinde
      $paid_unpaid_show = $request->paid_unpaid_show == null ? false: true;
      $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryHistoryWithProjectAndEmployeeTypeBasicAndHourlyEmployee($projectId, $emp_type, $hourly_employee, $month, $salaryYear, $salaryStatus);
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.projectwise-salary-report', compact('login_name','paid_unpaid_show','salaryReport', 'monthName', 'salaryYear', 'company', 'project', 'employeeType'));

  }


  // salary report 3.1
  public function showSalaryReportProjectAndEmpTradeBase(Request $request)
  {

            $projectId = $request->proj_idss;
            $trades = $request->trade_idss;
            $month = $request->month;
            $salaryYear = $request->year;
            $salaryStatus = $request->salary_statuss == null ? [0,1] : $request->salary_statuss; // 0 = Unpaid 1= Paid

            // "proj_idss" => array:2 [▶]
            // "trade_idss" => array:2 [▶]
            // "month" => "8"
            // "year" => "2024"
            // "salary_statuss" => array:2 [▶]

          //  dd($request->all());
            $monthName = (new HelperController())->getMonthName($month);
            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

            $sponser = "";
            $project_name = "";

            if($trades == null){
                $sponser = (new EmployeeRelatedDataService())->getAllActiveCategoryIDAsArrayWithRankingSequence();
            }
            if($projectId == null){
                $projectId = (new EmployeeRelatedDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);
            }

            $report_title = array();
            $report_title[0]  = 'Salary Report Month of '.$monthName.', '.$salaryYear;
            $report_title[1]  = 'Trade: ';
            $report_title[2]  = 'Project: ';
            $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryProjectAndTrade($projectId, $trades, $month, $salaryYear, $salaryStatus);
            $login_name = Auth::user()->name;
            return view('admin.salary-generate.spnserwise.sponserwise-salary-report', compact('login_name','report_title', 'salaryReport', 'company'));

  }



   // only this projectwork salary  report
  private function processAndDisplayOnlyThisProjectWorkingEmployeesSalaryFromMultiProjectRecord($month,$year,$project_id,$emp_type,$hourly_employee){

    $employees = (new SalaryProcessDataService())->getOnlyThisProjectWorkingEmployeesRecordFromMultiProjectTable($month,$year,$project_id,$emp_type,$hourly_employee);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $month = (new HelperController())->getMonthName($month);
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.onlythis_project_work_salary_emp_list', compact('login_name','employees', 'company','month','year'));

  }

  /* ==================== 4 Sponsor Wise Employee Salary Report ==================== */
  public function sponserWiseMonthlySalaryReport(Request $request)
  {

        $projectId = $request->proj_id;
        $sponserId = $request->SponsId;
        $month = $request->month;
        $salaryYear = $request->year;
        $salaryStatus = $request->salary_status; // 0 = Unpaid 1= Paid

        $monthName = (new HelperController())->getMonthName($month);
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

        $sponser = "All";
        $project_name = "All";

        if($sponserId > 0){
            $sponser = (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponserId);
        }
        if($projectId > 0){
            $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($projectId);
        }

        $report_title = array();
        $report_title[0]  = 'Salary Report Month of '.$monthName.', '.$salaryYear;
        $report_title[1]  = 'Sponsor: '.$sponser;
        $report_title[2]  = 'Project: '.$project_name;
        $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryHistory($projectId, $sponserId, $month, $salaryYear, $salaryStatus);
        $login_name = Auth::user()->name;
    return view('admin.salary-generate.spnserwise.sponserwise-salary-report', compact('login_name','report_title', 'salaryReport', 'company'));
  }


    /* ==================== 5 Report For Saudi By Sponsor ==================== */
    public function getSponserWiseMonthlySalaryReportForSaudi(Request $request)
    {


          $sponsor_id_list = $request->sponsor_id_list;
          $month = $request->month;
          $salaryYear = $request->year;

          $monthName = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

          $sponser = "";
          foreach($sponsor_id_list as $spId){
                $sponser = $sponser."\n* ".(new EmployeeRelatedDataService())->getASponserNameBySponerId($spId);
          }
          $report_title = array();
          $report_title[0]  = 'Salary Report Month of '.$monthName.', '.$salaryYear;
          $report_title[1]  = 'Sponor: '.$sponser;


          $salaryReport = (new SalaryProcessDataService())->getSponsorwiseEmployeeSalaryReportForSaudi($sponsor_id_list, $month, $salaryYear);
          $login_name = Auth::user()->name;
        return view('admin.salary-generate.spnserwise.sponsor_wise_salary_for_saudi_report', compact('login_name','report_title', 'salaryReport', 'company'));
    }


  /* ===================6 Monthwise All Employee Salary with PAID/Unpaid =================== */
  public function monthWiseSalary(Request $request)
  {


    $month = $request->month;
    $salaryYear = $request->year;
    $salaryStatus = $request->salary_status; // 0 = Unpaid 1= Paid
    $monthName = (new HelperController())->getMonthName($month);
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);


    $salaryReport = (new SalaryProcessDataService())->getEmployeeSalaryHistory(-1, -1, $month, $salaryYear, $salaryStatus);
    $allSalaryAmount = (new SalaryProcessDataService())->getSalaryTotalAmount(-1, $month, $salaryYear, $salaryStatus);
    $iqamaAmount =  (new SalaryProcessDataService())->getSalaryIqamaAdvanceTotalAmount($month, $salaryYear, $salaryStatus);
    $totalHours =  (new SalaryProcessDataService())->getSalaryMonthTotalHours(-1, $month, $salaryYear, $salaryStatus);
    $totalSaudiTax = (new SalaryProcessDataService())->getSalarySaudiTaxTotalAmount($month, $salaryYear, $salaryStatus);
    $totalOverTimeHours = (new SalaryProcessDataService())->getSalaryMonthTotalOvertimeHours(-1, $month, $salaryYear, $salaryStatus);
    $totalOverTimeAmount = (new SalaryProcessDataService())->getSalaryMonthTotalOvertimeAmount($month, $salaryYear, $salaryStatus);
    $totalFoodAllowance = (new SalaryProcessDataService())->getSalaryMonthFoodAllowanceTotalAmount($month, $salaryYear, $salaryStatus);
    $totalContribution = (new SalaryProcessDataService())->getSalaryMonthEmployeeCPFTotalAmount($month, $salaryYear, $salaryStatus);
    $totalOtherAdvance = (new SalaryProcessDataService())->getSalaryMonthOtherAdvanceTotalAmount($month, $salaryYear, $salaryStatus);

    $login_name = Auth::user()->name;

    if ($salaryReport) {

      return view('admin.salary-generate.all-employee.all-employees-salary', compact('login_name','salaryReport', 'month', 'monthName', 'salaryYear', 'company', 'allSalaryAmount', 'iqamaAmount', 'totalHours', 'totalSaudiTax', 'totalOverTimeHours', 'totalOverTimeAmount', 'totalFoodAllowance', 'totalContribution', 'totalOtherAdvance'));
    } else {
      Session::flash('salaryRecordNotFound', 'value');
      return redirect()->back();
    }
  }





  /* =================== 7. Project Wise All Employee Salary Paid and Unpaid Summary  =================== */
  public function monthAndYearWiseSalarySummary(Request $request)
  {
    $month = (int) $request->month;
    $salaryYear = (int) $request->year;
    $projectId = (int) $request->proj_id;


   // Only The Project Expenses Report For Cost Controll
    if($request->projec_cost_check)
    {
     return $this->processEmployeeMultiProjectSalaryTemporary($projectId,$month,$salaryYear);
    }
    // 0 = Unpaid, 1= Paid

    $monthName = (new HelperController())->getMonthName($month);

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

    $projectName = "All Project";
    if ($projectId > 0) {
      $projectName = (new EmployeeRelatedDataService())->findAProjectInformation($projectId)->proj_name;
    }
    $total_records = (new SalaryProcessDataService())->calculateEmployeeSalaryTotalAmount($projectId, $month, $salaryYear);
    $login_name = Auth::user()->name;
    if (count($total_records) >= 1) {
      return view('admin.salary-generate.all-employee.salary-summary', compact('login_name','total_records', 'monthName', 'salaryYear', 'company', 'projectName'));
    } else {
      Session::flash('salaryRecordNotFound', 'value');
      return redirect()->back();
    }

  }

    /* ===================
    Project Wise All Employee Salary Paid and Unpaid Summary ,
    Only The Project Expenses Report For Cost Controll
    =================== */
    private function processEmployeeMultiProjectSalaryTemporary($project_id,$month,$year)
    {


      $emp_auto_id_list =  (new EmployeeAttendanceDataService())->getListOfEmployeeAutoIdWorkInMultiProectByProjectIdMonthYear($project_id,$month,$year);
      // dd($emp_auto_id_list->count());
      $pre_emp_id = null;
      $counter = 0 ;
      foreach($emp_auto_id_list as $emp_auto_id){
          $emp_auto_id = $emp_auto_id->emp_id;
          if($pre_emp_id == null){
            $pre_emp_id = $emp_auto_id;
          }else if($pre_emp_id == $emp_auto_id){
            continue;
          }else {
            $pre_emp_id = $emp_auto_id;
          }

          $anEmployee = (new SalaryProcessDataService())->getAnEmployeeDetailsWithSalaryRecordForMultiProjectSalaryProcess($emp_auto_id,$month,$year);
         // dd($anEmployee);
          if($anEmployee != null){
            $this->processAnEmployeMultipleProjectWorkSalary($anEmployee,$month,$year);
            $counter++;
          }
      }

      $records = (new SalaryProcessDataService())->getOnlyThisProjectTotalSalaryAmountForMultipleProjectWorkByProjectMonthAndYear($project_id,$month,$year);
      $counter = 0;
     // dd($records);
      foreach($records as $arecord){

          $arecord->project_name = (new ProjectDataService())->getProjectNameByProjectId($project_id);
          $arecord->month_name = (new HelperController())->getMonthName($month);
          $arecord->year = $year;
          $records[$counter++] = $arecord;
      }
     // dd($records);
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.thisproject_salary_cost_only', compact('login_name','records', 'company'));

    }

 /* =================== 8. Project,month,year Wise Salary, TOtal OT , Hours, Iqama Adv, Others Adv. Summary  =================== */

  public function projectwiseTotalWorkingHoursAndTotalSalary(Request $request)
  {
      //  dd($request->all());
      if($request->report_type == 1){
          // Monthly Salary base Total Hours and Salary Summary
        return  $this->processProjectwiseMonthlyTotalWorkingHoursAndTotalSalaryReport($request->project_id_list,$request->from_date, $request->to_date);
      }else if($request->report_type == 2){
        // Manpower Summary for Monthly Salary
        return  $this->processProjectwiseMonthlySalaryManpowerSummaryReport($request->project_id_list[0],$request->from_date, $request->to_date);
      }else if($request->report_type == 3){
        // Only THis Project Salary Summary
        return $this->processOnlyThisProjectMonthlyTotalWorkingHoursAndTotalSalaryReport($request->project_id_list[0],$request->from_date, $request->to_date);
      }else if($request->report_type == 4){
        // only single month all project salary and deduction summmary
        return $this->processProjectwiseAMonthlySalaryAndDeductionSummaryReport($request->project_id_list,$request->from_date, $request->to_date);

      }

  }

  private function processProjectwiseMonthlyTotalWorkingHoursAndTotalSalaryReport($project_id_list,$from_date,$to_date){


    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $project_names['proj_name'] = "All Project"; ;
    if ($project_id_list == null ) {
      $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
    }else{
      $project_names = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);
    }


    $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);

    $salaryReport = array();
    $counter = 0;
    foreach ($monthwithYears as $my) {

     $summaryRecords = (new SalaryProcessDataService())->getSalarytSheetWiseTotalSalarySummaryByMultipleProjectMonthAndYear($project_id_list, $my['month'], $my['year']);

     $totalUnPaidSalaryAllIncluded = 0;
     $grossTotalUnPaidSalary = 0;
     $total_iqama_advance = 0;
     $total_other_advance = 0;

     $grossTotalPaidSalary = 0;
     $totalPaidSalaryAllIncluded = 0;
     $total_emp = 0;

        if (count($summaryRecords) == 2) {
            $total_emp = (int) $summaryRecords[0]->total_emp + (int) $summaryRecords[1]->total_emp;
            if($summaryRecords[0]->Status == 0){ // unpaid salary
              $unpaidRecord = $summaryRecords[0];
              $paidRecord = $summaryRecords[1];  // paid salary record
            }else {

              $paidRecord = $summaryRecords[0];  // paid salary
              $unpaidRecord = $summaryRecords[1];
            }

          $totalUnPaidSalaryAllIncluded = $unpaidRecord['total_salary'] + $unpaidRecord['total_saudi_tax'] + $unpaidRecord['total_iqama_adv'] + $unpaidRecord['total_other_adv'] + $unpaidRecord['total_contribution'];
          $grossTotalUnPaidSalary = $unpaidRecord['total_salary'];

          $total_iqama_advance += $paidRecord['total_iqama_adv'] + $unpaidRecord['total_iqama_adv'];
          $total_other_advance += $paidRecord['total_other_adv'] +  $unpaidRecord['total_other_adv'] + $paidRecord['total_saudi_tax'] +  $unpaidRecord['total_saudi_tax'];


          $totalPaidSalaryAllIncluded = $paidRecord['total_salary'] + $paidRecord['total_saudi_tax'] + $paidRecord['total_iqama_adv'] + $paidRecord['total_other_adv'] + $paidRecord['total_contribution'];
          $grossTotalPaidSalary = $paidRecord['total_salary'];

        }
        else  if (count($summaryRecords) == 1) {

          $total_emp = $summaryRecords[0]->total_emp ;
          $total_iqama_advance += $summaryRecords[0]->total_iqama_adv;
          $total_other_advance +=  $summaryRecords[0]->total_other_adv + $summaryRecords[0]->total_saudi_tax;;

          if($summaryRecords[0]->Status == 1){ // paid salary record
            $paidRecord = $summaryRecords[0];
            $totalPaidSalaryAllIncluded = $paidRecord['total_salary'] + $paidRecord['total_saudi_tax'] + $paidRecord['total_iqama_adv'] + $paidRecord['total_other_adv'] + $paidRecord['total_contribution'];
            $grossTotalPaidSalary = $paidRecord['total_salary'];
          }else {
            // uppaid salary record
            $unpaidRecord = $summaryRecords[0];
            $totalUnPaidSalaryAllIncluded = $unpaidRecord['total_salary'] + $unpaidRecord['total_saudi_tax'] + $unpaidRecord['total_iqama_adv'] + $unpaidRecord['total_other_adv'] + $unpaidRecord['total_contribution'];
            $grossTotalUnPaidSalary = $unpaidRecord['total_salary'];
          }
        }
        $info = array(
          'year' => $my['year'],
          'month_name' => (new HelperController())->getMonthName($my['month']),
          'month' => $my['month'],
          'total_emp' => $total_emp,
          'total_iqama_advance' => round($total_iqama_advance),
          'total_other_advance' => round($total_other_advance),
          'unpaid_gross_salary' => round($grossTotalUnPaidSalary),
          'paid_gross_salary' => round($grossTotalPaidSalary),
          'total_salary' => round($totalUnPaidSalaryAllIncluded + $totalPaidSalaryAllIncluded),
          'gross_total_salary' => round($grossTotalPaidSalary + $grossTotalUnPaidSalary),
        );
      $salaryReport[$counter++] = $info;
    }
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.salarysheet_monthlywise_salary_summary', compact('login_name','salaryReport', 'company', 'project_names'));

  }

  private function processProjectwiseMonthlySalaryManpowerSummaryReport($proj_id,$from_date,$to_date){

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $projectName = "All Project";
    if ($proj_id > 0) {
      $projectName = (new ProjectDataService())->getProjectNameByProjectId($proj_id);
    }else
      $proj_id = null;

      $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);
      $salary_report = array();
      $counter = 0;
      foreach ($monthwithYears as $my) {

      $total_emp = (new SalaryProcessDataService())->countMonthlySalryTotalEmployees($proj_id, $my['month'], $my['year']);
      $total_basic_emp = (new SalaryProcessDataService())->countMonthlySalryTotalBasicSalaryOrHourlyEmployes($proj_id, $my['month'], $my['year'],null);
      $total_hourly_emp = (new SalaryProcessDataService())->countMonthlySalryTotalBasicSalaryOrHourlyEmployes($proj_id, $my['month'], $my['year'],1);
      $salary_paid_total_emp = (new SalaryProcessDataService())->countMonthlySalryUnPaidTotalEmployes($proj_id, $my['month'], $my['year']);
      $salary_unpaid_total_emp = (new SalaryProcessDataService())->countMonthlySalryPaidTotalEmployes($proj_id, $my['month'], $my['year']);

          $arecord = array(
            'year' => $my['year'],
            'month_name' => (new HelperController())->getMonthName($my['month']),
            'month' => $my['month'],
            'total_emp' => $total_emp,
            'total_basic_emp' => $total_basic_emp,
            'total_hourly_emp' => $total_hourly_emp,
            'salary_paid_total_emp' => $salary_paid_total_emp,
            'salary_unpaid_total_emp' => $salary_unpaid_total_emp,
          );
        $salary_report[$counter++] = $arecord;
        //dd($arecord);
      }
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.monthly_salary_emp_summary_report', compact('login_name','salary_report', 'company', 'projectName'));
  }

  private function processOnlyThisProjectMonthlyTotalWorkingHoursAndTotalSalaryReport($proj_id,$from_date,$to_date){


    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $projectName = "All Project";
    if ($proj_id > 0) {
      $projectName = (new ProjectDataService())->getProjectNameByProjectId($proj_id);
    }

    $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);

    $summary_records = array();
    $counter = 0;
    foreach ($monthwithYears as $my) {

      $arecord = (new SalaryProcessDataService())->getOnlyThisProjectTotalSalaryAmountForMultipleProjectWorkByProjectMonthAndYear($proj_id, $my['month'], $my['year']);
      $arecord = $arecord[0];
      $arecord->year = $my['year'];
      $arecord->month_name = (new HelperController())->getMonthName($my['month']);
      $arecord->month = $my['month'];
      $summary_records[$counter++] = $arecord;
    }
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.only_projects_salary_summary', compact('login_name','summary_records', 'company', 'projectName'));

  }

   // only single month all project salary and deduction summmary
  private function processProjectwiseAMonthlySalaryAndDeductionSummaryReport($project_id_list,$from_date,$to_date){


    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    if ($project_id_list == null ) {
      $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);
    $month =  (new HelperController())->getMonthFromDateValue($from_date);
    $year =  (new HelperController())->getYearFromDateValue($from_date);

      $counter = 0;
     foreach($project_list as $aproject){
      $summaryRecords = (new SalaryProcessDataService())->getSalarytSheetWiseTotalSalarySummaryByMultipleProjectMonthAndYear([$aproject->proj_id], $month,$year);
       $totalUnPaidSalaryAllIncluded = 0;
      $grossTotalUnPaidSalary = 0;
      $total_iqama_advance = 0;
      $total_other_advance = 0;
      $grossTotalPaidSalary = 0;
      $totalPaidSalaryAllIncluded = 0;
      $total_emp = 0;
         if (count($summaryRecords) == 2) {
             $total_emp = (int) $summaryRecords[0]->total_emp + (int) $summaryRecords[1]->total_emp;
             if($summaryRecords[0]->Status == 0){ // unpaid salary
               $unpaidRecord = $summaryRecords[0];
               $paidRecord = $summaryRecords[1];  // paid salary record
             }else {

               $paidRecord = $summaryRecords[0];  // paid salary
               $unpaidRecord = $summaryRecords[1];
             }
           $totalUnPaidSalaryAllIncluded =  $unpaidRecord['total_salary'] + $unpaidRecord['total_saudi_tax'] + $unpaidRecord['total_iqama_adv'] + $unpaidRecord['total_other_adv'] + $unpaidRecord['total_contribution'];
           $grossTotalUnPaidSalary = $unpaidRecord['total_salary'];
           $total_iqama_advance += $paidRecord['total_iqama_adv'] + $unpaidRecord['total_iqama_adv'];
           $total_other_advance += $paidRecord['total_other_adv'] +  $unpaidRecord['total_other_adv'] + $paidRecord['total_saudi_tax'] +  $unpaidRecord['total_saudi_tax'];


           $totalPaidSalaryAllIncluded =   $paidRecord['total_salary'] + $paidRecord['total_saudi_tax'] + $paidRecord['total_iqama_adv'] + $paidRecord['total_other_adv'] + $paidRecord['total_contribution'];
           $grossTotalPaidSalary = $paidRecord['total_salary'];

         }
         else  if (count($summaryRecords) == 1) {

           $total_emp = $summaryRecords[0]->total_emp ;
           $total_iqama_advance += $summaryRecords[0]->total_iqama_adv;
           $total_other_advance += $summaryRecords[0]->total_other_adv + $summaryRecords[0]->total_saudi_tax;;

           if($summaryRecords[0]->Status == 1){ // paid salary record
             $paidRecord = $summaryRecords[0];
             $totalPaidSalaryAllIncluded = $paidRecord['total_salary'] + $paidRecord['total_saudi_tax'] + $paidRecord['total_iqama_adv'] + $paidRecord['total_other_adv'] + $paidRecord['total_contribution'];
             $grossTotalPaidSalary = $paidRecord['total_salary'];

           }else {
             // uppaid salary record
             $unpaidRecord = $summaryRecords[0];
             $totalUnPaidSalaryAllIncluded = $unpaidRecord['total_salary'] + $unpaidRecord['total_saudi_tax'] + $unpaidRecord['total_iqama_adv'] + $unpaidRecord['total_other_adv'] + $unpaidRecord['total_contribution'];
             $grossTotalUnPaidSalary = $unpaidRecord['total_salary'];

           }
         }
        $aproject->total_emp = $total_emp;
        $aproject->total_iqama_advance = round($total_iqama_advance);
        $aproject->total_other_advance = round($total_other_advance);
        $aproject->unpaid_gross_salary = round($grossTotalUnPaidSalary);
        $aproject->paid_gross_salary = round($grossTotalPaidSalary);
        $aproject->total_salary = round($totalUnPaidSalaryAllIncluded + $totalPaidSalaryAllIncluded);
        $aproject->gross_total_salary = round($grossTotalPaidSalary + $grossTotalUnPaidSalary);

        $project_list[$counter++] = $aproject;

     }

    $month_name = (new HelperController())->getMonthName($month);
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.salarysheet_single_month_salary_summary', compact('login_name','project_list', 'company', 'month_name','year'));

  }


/* =================== 9. Project wise Basic Empl and Hourly Emp Salary Summary =================== */
public function projectwiseBasicAndHourlyEmployeeSalarySummary(Request $request){

  if($request->report_type == 1){
     return $this->processProjectwiseBasicAndHourlyEmployeeSalarySummary($request);
  }else if($request->report_type == 2){
     return $this->processProjectBaseBasicAndHourlySalaryAndNoOfEmployeeSummary($request);
  }else if($request->report_type == 3){
    return $this->processProjectwiseMonthByMonthSalarySummaryReport($request);
  } else if($request->report_type == 4){
    return $this->processProjectBaseBasicAndHourlyAndStaffSalarySummaryrReport($request);
  } else if($request->report_type == 5){
    return $this->processProjectBaseBasicAndHourlyAndStaffSalarySummaryrWithOutCateringServiceReport($request);
  }else if($request->report_type == 6){
    return $this->processAProjectActualSalaryExpenseReportOfDirectAndIndirectEmployee($request);
  }




}

  // 9.1  salary summary
public function processProjectwiseBasicAndHourlyEmployeeSalarySummary(Request $request){



    if (! $request->has('proj_id')) {
      $request->proj_id = (new ProjectDataService())->getAllActiveProjectIDs();
    }
    $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($request->proj_id);
    $month =  (new HelperController())->getMonthFromDateValue($request->from_date);
    $year =  (new HelperController())->getYearFromDateValue($request->from_date);


    $records = array();
    $counter = 0;

    foreach($project_list as $pd){
        $basic_hourly_salary = (new SalaryProcessDataService())->getAProjectBasicAndHourlyEmployeeSalarySummaryReportByProjectId($pd->proj_id,
        $month,$year);

        $project_list[$counter]->basic_emp = $basic_hourly_salary->basic_emp;
        $project_list[$counter]->basic_salary = $basic_hourly_salary->basic_salary;
        $project_list[$counter]->basic_iqama_deduction = $basic_hourly_salary->basic_iqama_deduction;
        $project_list[$counter]->basic_other_deduction = $basic_hourly_salary->basic_other_deduction;
        $project_list[$counter]->all_incl_total_basic_salary = $basic_hourly_salary->all_incl_total_basic_salary;
        $project_list[$counter]->basic_hours = $basic_hourly_salary->basic_hours;

        $project_list[$counter]->hourly_emp =  $basic_hourly_salary->hourly_emp;
        $project_list[$counter]->hourly_salary = $basic_hourly_salary->hourly_salary;
        $project_list[$counter]->hourly_iqama_deduction = $basic_hourly_salary->hourly_iqama_deduction;
        $project_list[$counter]->hourly_other_deduction = $basic_hourly_salary->hourly_other_deduction;
        $project_list[$counter]->all_incl_total_hourly_salary = $basic_hourly_salary->all_incl_total_hourly_salary;
        $project_list[$counter]->hourly_hours = $basic_hourly_salary->hourly_hours;


        $paid_result = (new SalaryProcessDataService())->calculateAProjectTotalPaidUnpaidSalaryInAMonth($pd->proj_id,
        $month,$year);

        $project_list[$counter]->hourly_paid_emp = $paid_result->hourly_paid_emp;
        $project_list[$counter]->hourly_paid_amount = $paid_result->hourly_paid_amount;
        $project_list[$counter]->basic_paid_emp =  $paid_result->basic_paid_emp;
        $project_list[$counter]->basic_paid_amount = $paid_result->basic_paid_amount;

        $unpaid_result = (new SalaryProcessDataService())->calculateAProjectBasicAndHourlyEmpUnpaidSalarySummaryInAMonth($pd->proj_id,
        $month,$year);

        $project_list[$counter]->hourly_unpaid_emp = $unpaid_result->hourly_unpaid_emp;
        $project_list[$counter]->hourly_unpaid_amount = $unpaid_result->hourly_unpaid_amount;
        $project_list[$counter]->basic_unpaid_emp =  $unpaid_result->basic_unpaid_emp;
        $project_list[$counter]->basic_unpaid_amount = $unpaid_result->basic_unpaid_amount;

        $counter++;


    }

    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $report_title = "Month of ". (new HelperController())->getMonthName($month).", ".$year;
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.projectwise.projec_paidunpaid_basichourly_salary_summary', compact('login_name','project_list', 'company', 'report_title'));


}

  // 9.1  salary summary update in progress
  public function processProjectwiseBasicAndHourlyEmployeeSalarySummary1(Request $request){
   // try{

          if (! $request->has('proj_id')) {
            $request->proj_id = (new ProjectDataService())->getAllActiveProjectIDs();
          }
          $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($request->proj_id);
          $month =  (new HelperController())->getMonthFromDateValue($request->from_date);
          $year =  (new HelperController())->getYearFromDateValue($request->from_date);


          $records = array();
          $counter = 0;

          foreach($project_list as $pd){
              $apSalary = (new SalaryProcessDataService())->getAProjectBasicAndHourlyEmployeeSalarySummaryReportByProjectId($pd->proj_id,
              $month,$year);

              if(count($apSalary)){
                $project_list[$counter]->total_emp =  $apSalary[0]->total_emp ;// $basic_hourly_salary->basic_emp;
                $project_list[$counter]->total_hours =$apSalary[0]->total_hours ;
                $project_list[$counter]->total_salary = $apSalary[0]->total_salary ;
                $project_list[$counter]->total_iqama_deduction = $apSalary[0]->total_iqama_deduction ;
                $project_list[$counter]->total_other_deduction = $apSalary[0]->total_other_deduction ;
                $project_list[$counter]->total_saudi_deduction = $apSalary[0]->total_saudi_deduction ;
                $project_list[$counter]->total_food_deduction = $apSalary[0]->total_food_deduction ;
                $project_list[$counter]->all_incl_total_salary = $apSalary[0]->all_incl_total_salary ;
              }else{
                $project_list[$counter]->total_emp = 0;
                $project_list[$counter]->total_hours = 0;
                $project_list[$counter]->total_salary =  0;
                $project_list[$counter]->total_iqama_deduction = 0;
                $project_list[$counter]->total_other_deduction = 0;
                $project_list[$counter]->total_saudi_deduction = 0;
                $project_list[$counter]->total_food_deduction = 0;
                $project_list[$counter]->all_incl_total_salary = 0;
              }
             // dd($project_list);

              // $project_list[$counter]->basic_emp = $basic_hourly_salary->basic_emp;
              // $project_list[$counter]->basic_salary = $basic_hourly_salary->basic_salary;
              // $project_list[$counter]->basic_iqama_deduction = $basic_hourly_salary->basic_iqama_deduction;
              // $project_list[$counter]->basic_other_deduction = $basic_hourly_salary->basic_other_deduction;
              // $project_list[$counter]->all_incl_total_basic_salary = $basic_hourly_salary->all_incl_total_basic_salary;
              // $project_list[$counter]->basic_hours = $basic_hourly_salary->basic_hours;

              // $project_list[$counter]->hourly_emp =  $basic_hourly_salary->hourly_emp;
              // $project_list[$counter]->hourly_salary = $basic_hourly_salary->hourly_salary;
              // $project_list[$counter]->hourly_iqama_deduction = $basic_hourly_salary->hourly_iqama_deduction;
              // $project_list[$counter]->hourly_other_deduction = $basic_hourly_salary->hourly_other_deduction;
              // $project_list[$counter]->all_incl_total_hourly_salary = $basic_hourly_salary->all_incl_total_hourly_salary;
              // $project_list[$counter]->hourly_hours = $basic_hourly_salary->hourly_hours;


              $paid_result = (new SalaryProcessDataService())->calculateAProjectTotalPaidUnpaidSalaryInAMonth($pd->proj_id,
              $month,$year);

              $project_list[$counter]->hourly_paid_emp = $paid_result->hourly_paid_emp;
              $project_list[$counter]->hourly_paid_amount = $paid_result->hourly_paid_amount;
              $project_list[$counter]->basic_paid_emp =  $paid_result->basic_paid_emp;
              $project_list[$counter]->basic_paid_amount = $paid_result->basic_paid_amount;

              $unpaid_result = (new SalaryProcessDataService())->calculateAProjectBasicAndHourlyEmpUnpaidSalarySummaryInAMonth($pd->proj_id,
              $month,$year);

              $project_list[$counter]->hourly_unpaid_emp = $unpaid_result->hourly_unpaid_emp;
              $project_list[$counter]->hourly_unpaid_amount = $unpaid_result->hourly_unpaid_amount;
              $project_list[$counter]->basic_unpaid_emp =  $unpaid_result->basic_unpaid_emp;
              $project_list[$counter]->basic_unpaid_amount = $unpaid_result->basic_unpaid_amount;

              $counter++;
          }

          $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
          $report_title = "Month of ". (new HelperController())->getMonthName($month).", ".$year;
          $login_name = Auth::user()->name;
          return view('admin.salary-generate.projectwise.projec_paidunpaid_basichourly_salary_summary', compact('login_name','project_list', 'company', 'report_title'));

        // }catch(Exception $ex){
        //   return "System Exception Found, Please Try Again ".$ex;

        // }

}


// 9.2  monthly statement
public function processProjectBaseBasicAndHourlySalaryAndNoOfEmployeeSummary($request){

    try{
        $proj_id = $request->proj_id;
        $from_date =$request->from_date;
        $to_date=$request->to_date;
        $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);

        $records = array();
        $counter = 0;
        foreach($proj_id as $pid){

            $dbrecord = (new SalaryProcessDataService())->getProjectBaseBasicAndHourlyEmployeeSalarySummaryProject($pid,
            $monthwithYears[0]['month'],$monthwithYears[0]['month'], $monthwithYears[0]['year'], $monthwithYears[0]['year']);
            $arecord = array();
            if(count($dbrecord) == 2){

              $abc =(array)  $dbrecord[0];
              $abc['total_basic_salary'] = round($abc['total_salary']);
              $abc['total_basic_emp'] =  round($abc['total_emp']);

              $ab = (array) $dbrecord[1];
              $abc['total_hourly_salary'] = round($ab['total_salary']);
              $abc['total_hourly_emp'] =  round($ab['total_emp']);
              $records[$counter++] = $abc;
            }
            elseif(count($dbrecord) == 1){

                  $abc = (array)  $dbrecord[0];
                  $abc['total_hourly_salary'] = 0;
                  $abc['total_basic_salary'] = 0;
                  $abc['total_basic_emp'] =  0;
                  $abc['total_hourly_emp'] = 0;

                  if( $abc['hourly_employee'] == 1){
                    $abc['total_hourly_salary'] = round($abc['total_salary']);
                    $abc['total_hourly_emp'] =  round($abc['total_emp']);
                  }
                  else {
                    $abc['total_basic_salary'] = round($abc['total_salary']);
                    $abc['total_basic_emp'] =  round($abc['total_emp']);
                  }
                  $records[$counter++] = $abc;

            }

        }

        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $report_title = "Month of ". (new HelperController())->getMonthName($monthwithYears[0]['month']).", ".$monthwithYears[0]['year'];
        $login_name = Auth::user()->name;
        return view('admin.salary-generate.projectwise.project_basic_hourly_emp_sal_summary', compact('login_name','records', 'company', 'report_title'));
      } catch (Exception $ex) {

        return "Operation Failed, System Exception  ".$ex->getMessage();
      }

}

 // 9.3 All Project Month By Month Summary
public function processProjectwiseMonthByMonthSalarySummaryReport(Request $request){

      if ($request->proj_id == null ) {
        $project_list = (new ProjectDataService())->getAllActiveProjectIDs();
      }
      $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($request->proj_id);
      $my_list =  (new HelperController())->getMonthsInRangeOfDate($request->from_date,$request->to_date);

      $main_counter = 0;
      foreach($project_list as $pd){

        $counter = 0;
        $project_salary = array();
        foreach($my_list as $my){

              $total_slh_record = (new SalaryProcessDataService())->calculateAProjectAllIncludedSalaryTotalAmountForAMonthAndYear($pd->proj_id,
              $my["month"],$my["year"]);
              $project_salary[$counter++] = Round( $total_slh_record->all_included_total_amount,2);
          }
        $project_list[$main_counter++]->salary_record = $project_salary;
      }

      $counter = 0;
      foreach($my_list as $my){
          $my['month'] = (new HelperController())->getMonthName($my["month"]);
          $my_list[$counter++] = $my;
      }

      if($request->report_format == 1){
        // show as print preview
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $login_name = Auth::user()->name;
        return view('admin.salary-generate.projectwise.all_project_month_by_month_report', compact('login_name','project_list','my_list', 'company'));
      }else if($request->report_format == 2){
        // download as excel format
        return Excel::download(new SalaryReport1ExcelExport( $project_list, $my_list), 'salary_report1.xlsx');
      }

}


// 9.4 Project base Office Staff and Direct Employees Hours and Salary Summary
public function processProjectBaseBasicAndHourlyAndStaffSalarySummaryrReport($request){

      $project_id_list = $request->proj_id;
      if ($project_id_list == null ) {
        $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
      }
      $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);

      $from_date =$request->from_date;
      $month =  (new HelperController())->getMonthFromDateValue($from_date);
      $year =  (new HelperController())->getYearFromDateValue($from_date);
      $counter = 0;

      foreach($project_list as $ap){

          $basic_hourly_record = (new SalaryProcessDataService())->getAProjectDirectBasicAndHourlyEmployeesSalarySummaryReport((int)$ap->proj_id,$month,$year);
          if(count($basic_hourly_record) == 1){

                $abc = (array)  $basic_hourly_record[0];
                if($abc["hourly_employee"] == null){
                  $ap->total_basic_emp = round($abc['total_emp']);
                  $ap->total_basic_salary = round($abc['total_salary']);
                  $ap->total_basic_hours =  round($abc['total_hours']);
                }else{
                  $ap->total_hourly_emp = round($abc['total_emp']);
                  $ap->total_hourly_salary = round($abc['total_salary']);
                  $ap->total_hourly_hours =  round($abc['total_hours']);
                }

          }elseif(count($basic_hourly_record) == 2){


              $abc = (array)  $basic_hourly_record[0];
              $ap->total_hourly_emp = round($abc['total_emp']);
              $ap->total_hourly_salary = round($abc['total_salary']);
              $ap->total_hourly_hours =  round($abc['total_hours']);

              $abc = (array)  $basic_hourly_record[1];

              $ap->total_basic_emp = round($abc['total_emp']);
              $ap->total_basic_salary = round($abc['total_salary']);
              $ap->total_basic_hours =  round($abc['total_hours']);


          }



          $staff_record = (new SalaryProcessDataService())->getAProjectInirectEmployeesSalarySummaryReport((int)$ap->proj_id,$month,$year);
            if(count($staff_record) == 1){

                  $abc = (array)  $staff_record[0];
                  $ap->total_indirect_emp = round($abc['total_emp']);
                  $ap->total_indirect_salary = round($abc['total_salary']);
                  $ap->total_indirect_hours =  round($abc['total_hours']);
            }
            $project_list[$counter++] = $ap;
      }

      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $report_title = "Salary Month of ". (new HelperController())->getMonthName($month).", ".$year;
      $login_name = Auth::user()->name;
      return view('admin.salary-generate.projectwise.office_staff_and_direct_emp_salary_summary', compact('login_name','project_list', 'company', 'report_title'));


}

// 9.5 Project base Office Staff and Direct Employees Hours and Salary Summary
public function processProjectBaseBasicAndHourlyAndStaffSalarySummaryrWithOutCateringServiceReport($request){

  $project_id_list = $request->proj_id;
  if ($project_id_list == null ) {
    $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
  }
  $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);

  $from_date =$request->from_date;
  $month =  (new HelperController())->getMonthFromDateValue($from_date);
  $year =  (new HelperController())->getYearFromDateValue($from_date);
  $counter = 0;

  foreach($project_list as $ap){

      $basic_hourly_record = (new SalaryProcessDataService())->getAProjectDirectBasicAndHourlyEmployeesSalarySummaryWithOutCateringServiceReport((int)$ap->proj_id,$month,$year);
      if(count($basic_hourly_record) == 1){

            $abc = (array)  $basic_hourly_record[0];
            if($abc["hourly_employee"] == null){
              $ap->total_basic_emp = round($abc['total_emp']);
              $ap->total_basic_salary = round($abc['total_salary']);
              $ap->total_basic_hours =  round($abc['total_hours']);
            }else{
              $ap->total_hourly_emp = round($abc['total_emp']);
              $ap->total_hourly_salary = round($abc['total_salary']);
              $ap->total_hourly_hours =  round($abc['total_hours']);
            }

      }elseif(count($basic_hourly_record) == 2){

          $abc = (array)  $basic_hourly_record[0];

          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);

          $abc = (array)  $basic_hourly_record[1];
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);


      }

      $staff_record = (new SalaryProcessDataService())->getAProjectInirectEmployeesSalarySummaryWithOutCateringServiceReport((int)$ap->proj_id,$month,$year);
        if(count($staff_record) == 1){

              $abc = (array)  $staff_record[0];
              $ap->total_indirect_emp = round($abc['total_emp']);
              $ap->total_indirect_salary = round($abc['total_salary']);
              $ap->total_indirect_hours =  round($abc['total_hours']);
        }
        $project_list[$counter++] = $ap;
  }

  $company = (new CompanyDataService())->findCompanryProgetABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
  $report_title = "Salary Month of ". (new HelperController())->getMonthName($month).", ".$year;
  $login_name = Auth::user()->name;
  return view('admin.salary-generate.projectwise.staff_direct_emp_salary_summary_without_catering', compact('login_name','project_list', 'company', 'report_title'));


}

// 9.6 Project base Office Staff and Direct Employees Hours and Salary Actual Expense for a Project
public function processAProjectActualSalaryExpenseReportOfDirectAndIndirectEmployee($request){

  $project_id_list = $request->proj_id;
  if ($project_id_list == null ) {
    $project_id_list = (new ProjectDataService())->getAllActiveProjectIDs();
  }
  $project_list = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);

  $from_date =$request->from_date;
  $month =  (new HelperController())->getMonthFromDateValue($from_date);
  $year =  (new HelperController())->getYearFromDateValue($from_date);
  $counter = 0;

  foreach($project_list as $ap){

      $basic_hourly_record = (new SalaryProcessDataService())->getAProjectActualSalaryExpenseOfDirectEmployeesSalarySummaryReport((int)$ap->proj_id,$month,$year);
      if(count($basic_hourly_record) == 1){

            $abc = (array)  $basic_hourly_record[0];
            if($abc["hourly_employee"] == null){
              $ap->total_basic_emp = round($abc['total_emp']);
              $ap->total_basic_salary = round($abc['total_salary']);
              $ap->total_basic_hours =  round($abc['total_hours']);
            }else{
              $ap->total_hourly_emp = round($abc['total_emp']);
              $ap->total_hourly_salary = round($abc['total_salary']);
              $ap->total_hourly_hours =  round($abc['total_hours']);
            }

      }elseif(count($basic_hourly_record) == 2){

          $abc = (array)  $basic_hourly_record[0];

          $ap->total_basic_emp = round($abc['total_emp']);
          $ap->total_basic_salary = round($abc['total_salary']);
          $ap->total_basic_hours =  round($abc['total_hours']);

          $abc = (array)  $basic_hourly_record[1];
          $ap->total_hourly_emp = round($abc['total_emp']);
          $ap->total_hourly_salary = round($abc['total_salary']);
          $ap->total_hourly_hours =  round($abc['total_hours']);


      }
    //  dd($project_list);

      $staff_record = (new SalaryProcessDataService())->getAProjectActualSalaryExpenseOfInDirectEmployeesSalarySummaryReport((int)$ap->proj_id,$month,$year);
        if(count($staff_record) == 1){

              $abc = (array)  $staff_record[0];
              $ap->total_indirect_emp = round($abc['total_emp']);
              $ap->total_indirect_salary = round($abc['total_salary']);
              $ap->total_indirect_hours =  round($abc['total_hours']);
        }
        $project_list[$counter++] = $ap;
  }
 // dd($project_list);
  $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
  $report_title = "Salary Month of ". (new HelperController())->getMonthName($month).", ".$year;
  $login_name = Auth::user()->name;
  return view('admin.salary-generate.projectwise.aproject_direct_indirect_actual_salary_expense', compact('login_name','project_list', 'company', 'report_title'));


}




// 10 Sponsor Salary Summary
public function processSponsorSalarySummaryReport(Request $request){

    if($request->report_type == 1){
      // Summary Month by Month
      return $this->processASponsorYearlyMonthByMonthSalarySummaryReport($request);
    }
    else if($request->report_type == 2){
      // Single Month Project Details
      return $this->processASponsorSingleMonthSalaryProjectBaseSummaryReport($request);
    }

}

// 10.1 Sponsor Base Salary Summary Month by Month
public function processASponsorYearlyMonthByMonthSalarySummaryReport(Request $request){

    $sponsor_id = $request->sponsor_id;
    $from_date =$request->from_date;
    $to_date=$request->to_date;
    $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($from_date, $to_date);
    $summary_records = array();
    $counter = 0;
    foreach($monthwithYears as $my){
      $records = (new SalaryProcessDataService())->getASponsorYearlySalarySummaryMonthByMonthReport($sponsor_id,$my['month'],  $my['year']);
      if(count($records) >0){
        $summary_records[$counter++] = $records[0];
      }
    }
    $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
    $sponsor_name =   (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponsor_id);
    $report_title = "";
    $login_name = Auth::user()->name;
    return view('admin.salary-generate.spnserwise.asponsor_month_bymonth_salary_summary', compact('login_name','summary_records', 'company','sponsor_name', 'report_title'));

}

// 10.2 Sponsor base single month salary summary Project Details

public function processASponsorSingleMonthSalaryProjectBaseSummaryReport(Request $request){

  $sponsor_id = $request->sponsor_id;
  $month =  (new HelperController())->getMonthFromDateValue($request->from_date);
  $year =  (new HelperController())->getYearFromDateValue($request->from_date);
  $select_project_ids = $request->proj_id;

  if ($select_project_ids == null ) {
        $select_project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
  }
  $summary_records1 = (new SalaryProcessDataService())->getASponsorSingleMonthSalarySummaryProjecBaseDetailsReport($sponsor_id,$month,  $year);
  $summary_records = array();
  $counter = 0;
  foreach($summary_records1 as $arecord){
    if(in_array($arecord->proj_id, $select_project_ids)){
      $summary_records[$counter++] = $arecord;
    }
  }


  $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
  $sponsor_name =   (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponsor_id);
  $report_title = "Month of ". (new HelperController())->getMonthName($month).", ".$year;
  $login_name = Auth::user()->name;
  return view('admin.salary-generate.spnserwise.asponsor_single_month_salary_project_details_report', compact('login_name','summary_records', 'company','sponsor_name', 'report_title'));

}


/* =================== 11. Multiple Emplyees ID Salary   report =================== */
public function multipleEmployeeIdBaseSalaryProcess( Request $request)
{
    try{

        $month = $request->month;
        $salaryYear = $request->year;
        $salaryStatus =  $request->salary_status; // 0 = Unpaid 1= Paid

        $allEmplId = explode(",", $request->multiple_emp_Id);
        $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
        $monthName = (new HelperController())->getMonthName($month);
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $salaryReport = (new SalaryProcessDataService())->getMultipleEmployeeIdBaseSalaryHistory($allEmplId, $month, $salaryYear, $salaryStatus,Auth::user()->branch_office_id);
        $login_name = Auth::user()->name;
        return view('admin.salary-generate.all-employee.all-employees-salary', compact('login_name','salaryReport', 'month', 'monthName', 'salaryYear', 'company'));

    }catch(Exception $ex){
        return "System Operation Failed ".$ex;
    }

}


//12 Salary Paid Bank Employees Salary Report
public function processSalaryPaidByBankEmployeesListSalaryReport(Request $request){
    try{

        $project_id_list = $request->project_id_list;
        $month = $request->month;
        $year = $request->year;
        $salaryReport = (new SalaryProcessDataService())->salaryPaidByBankEmployeesSalaryReport($project_id_list,$month,  $year,Auth::user()->branch_office_id);

        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        $report_left_title =['Bank Paid Salary Report',(new HelperController())->getMonthName($month),$year];
        $login_name = Auth::user()->name;
        return view('admin.salary-generate.projectwise.bank_paid_emp_salary_report', compact('login_name', 'salaryReport','report_left_title', 'company'));
    }catch(Exception $ex){
        return view("System Operation Failed ".$ex);
    }
}

  /* ==================== 13 Salary Hold Employees Salary Report ==================== */
  public function showEmployeeSalarySheetReportPrintPreviewByReportType(Request $request)
  {

       try{

       // dd($request->all());
            if($request->salary_report_type == 1){
              // Salary Hold Employee Salary Sheet
              return $this->processSalaryHoldEmployeeSalaryReport1($request);
            }elseif($request->salary_report_type == 2){
              // Office Staff Employee Salary Sheet
              return $this->processOfficeStaffEmployeeSalarySheetReport($request);
            }

        }catch(Exception $ex){
          return "System Data Processsing Error ".$ex;
       }
  }


 // Salary Hold Employee Salary Sheet
  public function processSalaryHoldEmployeeSalaryReport1(Request $request)
  {

       try{


          $month = $request->month;
          $year = $request->year;
          $project_ids = $request->project_ids;
          $salary_records = (new SalaryProcessDataService())->getEmployeeSalaryReportThoseAreSalaryHold($project_ids,$month, $year);
          $month_name = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
          $login_name = Auth::user()->name;
          return view('admin.report.salary.salary_sheet.salary_hold_emp_salary_report', compact('salary_records','month_name','month', 'year', 'company' ,'login_name'));
       }catch(Exception $ex){
          return "System Data Processsing Error ".$ex;
       }
  }

  // Office Staff Employee Salary Sheet
  public function processOfficeStaffEmployeeSalarySheetReport(Request $request)
  {

       try{

          $month = $request->month;
          $year = $request->year;
          $project_ids = $request->project_ids;
          $salary_records = (new SalaryProcessDataService())->getOfficeStaffEmployeeSalaryReport($project_ids,$month, $year);
          $month_name = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
          $login_name = Auth::user()->name;
          return view('admin.report.salary.salary_sheet.office_staff_salary_sheet_report', compact('salary_records','month_name','month', 'year', 'company' ,'login_name'));
       }catch(Exception $ex){
          return "System Data Processsing Error ".$ex;
       }
  }





  public function uploadEmpSalarySheet(Request $request)
  {

    $data = $request->all();
    $insert = 0;
    if ($request->hasFile('file_name')) {

      $filePath = (new UploadDownloadController())->uploadEmployeeSalarySheet($request->file('file_name'),null);
      $data['file_name'] = $filePath[0];
      $data['file_path'] =  $filePath[1];
      $insert = (new SalaryProcessDataService())->insertUploadedSalaryShetInformation($data);

    }

    if ($insert) {
      Session::flash('success', 'Salary Sheet Save');
      return Redirect()->back();
    } else {
      Session::flash('error', 'Please Upload File ');
      return Redirect()->back();
    }

  }




  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
  public function index()
  {
    return null;
  }

  public function create()
  {
    $currentMonth = Carbon::now()->format('m');

    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);

    $month = (new CompanyDataService())->getAllMonth();
    $emp_types = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
    $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
    $allEmployeeStatus = (new HelperController())->getEmployeeStatus();

    $trades = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
    return view('admin.salary-generate.salary-proccessing', compact('trades', 'allEmployeeStatus', 'month', 'currentMonth', 'projects', 'emp_types', 'sponser'));
  }


  // Employee Pending Salary UI Loading
  public function loadSalaryPendingEmployeeListWithUI()
  {
    //SalaryPending

    $projectlist = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $sponserList = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
    return view('admin.salary-generate.salary_pending.pending-salary', compact('projectlist', 'sponserList'));
  }
  // Employee Paid Salary UI Loading
  public function Salarypaid()
  {
    $projectlist = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $sponserList = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
    return view('admin.salary-generate.salary-paid.paid-salary', compact('projectlist', 'sponserList'));
  }


  // Employee Salary sheet UI Loading
  public function loadSalarySheetUploadUI()
  {
    $allSheet =  (new SalaryProcessDataService())->getUploadedSalarySheetInformation();
    $month = (new CompanyDataService())->getAllMonth();
    $projectlist = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $sponserList = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);

    return view('admin.salary-generate.salary_related_file_upload', compact('projectlist', 'sponserList', 'month', 'allSheet'));
  }

  public function deleteUploadedSalarySheet($salary_uploaded_info_auto_id)
  {

    $record =  (new SalaryProcessDataService())->getAnUploadedSalarySheetInformation($salary_uploaded_info_auto_id);
    (new UploadDownloadController())->deleteUploadedSalarySheet($record->file_path);
    (new SalaryProcessDataService())->deleteAnUploadedSalarySheetInformation($salary_uploaded_info_auto_id);
    return $this->loadSalarySheetUploadUI();

  }



  //  Ajax Calling Request For Load Salary Pending List Date to Date
public function SalaryPendingList(Request $request)
  {

    try {

        $dayMonthYear1 = (new HelperController())->getDayMonthAndYearFromDateValue($request->fromDate);
        $fromMonth = $dayMonthYear1[1]; // 1 index value as month
        $fromYear = $dayMonthYear1[2]; // 2 index value as Year

        $dayMonthYear2 = (new HelperController())->getDayMonthAndYearFromDateValue($request->toDate);
        $toMonth = $dayMonthYear2[1]; // 1 index value as month
        $toYear = $dayMonthYear2[2]; // 2 index value as Year

        $sponser_id = (int) $request->SponsId;
        $proj_id = (int) $request->proj_id;
        $employee_id = (int) $request->employee_id;

          if ($employee_id != null) {
            (int)$employee_id = $request->employee_id;
            $pendingSalary =  (new SalaryProcessDataService())->searchAnEmployeeUnPaidSalaryRecordsWithEmloyeeInfoForListView($employee_id,Auth::user()->branch_office_id);
          } else {
            $pendingSalary = (new SalaryProcessDataService())->searchListOfEmployeesThoseSalaryRecordsStatusIsUnpaidForListViewByProjectAndSponsor($sponser_id, $proj_id, $fromMonth, $toMonth,$fromYear,$toYear,Auth::user()->branch_office_id);
          }
          if ($pendingSalary->count() >0){
            return response()->json(['status'=>200,'success'=>true, 'fromMonth' => $fromMonth, 'toMonth' => $toMonth, 'pendingSalary' => $pendingSalary]);
          }else {
            return response()->json(['status'=>404,'success'=>false, 'message'=>'Records Not Found','error' =>'']);
          }

        }catch(Exception $ex){
          return response()->json(['status'=>404,'success'=>false, 'message'=>'Server Operation Failed','error' => $ex]);
       }

  }


  // Ajax Calling for  An Employee Salary Record Searching By Salary Hisotry Auto Id
  public function getAnEmployeeSalaryRecordBySalaryHistoryAutoId(Request $request){

     $salary_record = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordBySalaryHistoryAutoId($request->slh_auto_id);
     return response()->json(['arecord' => $salary_record, 'status' => 200, 'success' => true,'slh_auto_id' => $request->slh_auto_id]);

  }
   // Salary  Update From Pending Salary Emp List  AJAX Request
  public function updateAnEmployeeSalaryRecordBySalaryHistoryAutoId(Request $request){


    $total_work_day = (int) $request->working_days;
    $total_amount = (float) $request->total_amount;
    $new_food_amount =(float) $request->new_food_amount;
    $saudi_tax =(float) $request->saudi_tax;
    $new_other_advance =(float) $request->new_other_advance;
    $new_iqama_advance =(float) $request->new_iqama_advance;
    $new_receivable_total_salary = $request->new_receivable_total_salary;
    $salary_month = (int) $request->salary_month;
    $salary_year = (int) $request->salary_year;
    $emp_auto_id = $request->emp_auto_id;
    $slh_auto_id = $request->slh_auto_id;
    $slh_all_include_amount = $total_amount + $new_food_amount;
    $salary_paid__status = (int) $request->salary_paid__status;
   // dd($request->all());
    $update = (new SalaryProcessDataService())->updateAnEmployeeMonthlySalaryRecordUpdateSalaryStatusAsPaidBySalaryHistoryAutoId($slh_auto_id,$emp_auto_id,$slh_all_include_amount,$new_food_amount,$saudi_tax,$new_other_advance,$new_iqama_advance,$new_receivable_total_salary,Auth::user()->id,$salary_paid__status);

    if($update){

        $records = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordsOnly($emp_auto_id,$salary_month,$salary_year);
         foreach($records as $arecord){
           $food_amount = 0;
           if( $new_food_amount > 0 && ((int)$total_work_day) > 0) {
             $food_amount = round((($new_food_amount / $total_work_day) * $arecord->total_day),2);
           }
           (new SalaryProcessDataService())->updateEmployeeMultipleProjectWorkSalaryAmount($arecord->empwh_auto_id,
           ($arecord->total_amount + $food_amount),$food_amount,$arecord->other_amount,$arecord->ot_amount,Auth::user()->id);
                 // new_record->total_amount = basic amount + ot_amount + other_amount;
         }
         return response()->json(['success' => true,'status' => 200,'message'=>'Successfully Updated']);
    }else {
      return response()->json(['success' => false,'status' => 404, 'error' =>'error' ,'message'=>'Update Operation Failed, Please Try Again']);

    }

  }
  // Delete Employee Unpaid Salary Record
  public function deleteAnEmployeeUnpaidSalaryRecord($slh_auto_id){

    $salary_record = (new SalaryProcessDataService())->getAnEmployeeSalaryRecordBySalaryHistoryAutoId($slh_auto_id);
    if($salary_record->Status == 0){
       $isDeleted = (new SalaryProcessDataService())->deleteAnEmployeeUnpaidSalaryRecordBySalaryHistoryAutoId($slh_auto_id);
       return response()->json(['success' => true,'status' => 200,'message' => "Successfully Completed", 'data' => $slh_auto_id]);
    }else {
      return response()->json(['success' => false,'status' => 404, 'error' => 'Salary Already Paid, Not Possible to Delete', 'data' => $slh_auto_id]);
    }
  }

  // Ajax Calling Request For searching Salary Paid Emp List Date to Date
  public function SalarypaidList(Request $request)
  {
      try {

          $dayMonthYear1 = (new HelperController())->getDayMonthAndYearFromDateValue($request->fromDate);
          $fromMonth = $dayMonthYear1[1]; // 1 index value as month
          $fromYear = $dayMonthYear1[2]; // 2 index value as Year

          $dayMonthYear2 = (new HelperController())->getDayMonthAndYearFromDateValue($request->toDate);
          $toMonth = $dayMonthYear2[1]; // 1 index value as month
          $toYear = $dayMonthYear2[2]; // 2 index value as Year

          $sponser_id = (int) $request->SponsId;
          $proj_id = (int) $request->proj_id;
          if($request->employee_id){

            $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpId($request->employee_id);
            if($employee){
               $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($employee->emp_auto_id);
               $pendingSalary = (new SalaryProcessDataService())->searchAnEmployeesInfoWithInAFiscalYearPaidSalaryRecordsForListView($request->employee_id, $fiscal->start_date,$fiscal->end_date,Auth::user()->branch_office_id);
               return response()->json(['status'=>200,'success'=>true,'pendingSalary' => $pendingSalary]);

            }else {
              return response()->json(['status'=>404,'success'=>true, 'message'=>'Employee Not Found','error' =>'error']);
            }
          }else {
          //  searchListOfEmployeesThoseSalaryAlreadyPaidForListViewByProjectAndSponsor
            $pendingSalary = (new SalaryProcessDataService())->searchListOfEmployeesThoseSalaryAlreadyPaidForListViewByProjectAndSponsor($sponser_id, $proj_id, $fromMonth, $toMonth,$fromYear,$toYear,Auth::user()->branch_office_id);
            return response()->json(['status'=>200,'success'=>false, 'pendingSalary' => $pendingSalary, 'fromMonth' => $fromMonth, 'toMonth' => $toMonth]);

          }

        }catch(Exception $ex){
          return response()->json(['status'=>603,'success'=>false, 'message'=>'Server Operation Failed','error' => $ex]);
      }
  }


  // Pending Salary Update to Paid Salary  Confirmation
  public function SalaryPayment(Request $request)
  {

    $slh_auto_id_list = $request->slh_auto_id;
    foreach ($slh_auto_id_list as $slh_auto_id) {
      if ($request->has('emp_slh_paid_checkbox-' . $slh_auto_id)) {
        $emp_salary_record = (new SalaryProcessDataService())->getAnEmployeeBankInfoWithSalaryRecordForUpdateSalaryPaidMethodBySalaryHistoryAutoId($slh_auto_id);

        if($emp_salary_record->ebd_auto_id != null && $emp_salary_record->payment_method == SalaryPaymentMethodEnum::Bank->value){
          (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsPaidAndPaymentMethod((int) $slh_auto_id,(int) $emp_salary_record->ebd_auto_id,Auth::user()->id);
        }else {
           (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsPaid((int) $slh_auto_id,Auth::user()->id);
        }
      }
    }

    Session::flash('success', 'Successfully Updated');
    return redirect()->back();
  }


  public function SalaryPaymentToUnPay(Request $request)
  {
    $slh_auto_id = $request->id;
    $payment = (new SalaryProcessDataService())->updateEmployeeSalaryStatusAsUnPaid($slh_auto_id,Auth::user()->id);
    if ($payment == true) {
      return response()->json(["success" => "Salary Status Updated"]);
    } else {
      return response()->json(['error' => "Data Not Found!"]);
    }
  }


  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */









    /*
    =======================================================================
    Employee Mobile Bill Information
    =======================================================================
  */
    public function manageEmployeeMobileBillRelatedInformation(Request $request)
    {
        try{



                    if( (int) $request->operation_type == 1){
                        if ($request->bill_month != null && $request->bill_year != null && $request->bill_project_id != null && $request->hasFile('bill_paper')) {
                            $uploadedPath = (new  UploadDownloadController())->uploadEmployeeMobileBillPaper($request->file('bill_paper'), null);
                            $bill_id = (new SalaryProcessDataService())->storeEmployeeMobileBillInformation($request->bill_month, $request->bill_year, $request->bill_project_id,$uploadedPath);
                            return response()->json([
                                'status' => 200,
                                'success' => true,
                                'message' => 'Successfully Uploaded',
                            ]);

                        }
                    }
                    else if ( (int) $request->operation_type == 2 )
                    {
                        $months =   is_null($request->month) ? [1,2,3,4,5,6,7,8,9,11,12]:[$request->month];
                        $project_ids = is_null($request->project_id) ? (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id) : [$request->project_id];
                     //   $project_ids =   (new ProjectDataService())->getAllActiveProjectIDOfABranchOfficeAsArray(Auth::user()->branch_office_id);

                        $year = $request->year;
                        $search_records = (new SalaryProcessDataService())->searchEmployeeMobileBillRecordsForListViewByYearMonthProject($months,$year ,$project_ids,Auth::user()->user_branch_office);
                        return response()->json([
                            'status' => 200,
                            'success' => true,
                            'data' => $search_records,
                            'message' => '',
                        ]);
                    }
                    else {
                        return response()->json([
                            'success' => false,
                            'status' => 200,
                            'error' => 'error',
                            'message' => 'Please Try Again With All Information',
                            'd'=> $request->all()
                        ]);
                    }

            }catch(Exception $ex){
                return response()->json([
                    'success' => false,
                    'status' => 200,
                    'error' => 'error',
                    'message' => 'Operation Failed. Please Try Again With All Information ',
                    'ee' =>$ex,
                ]);
            }
    }


}
