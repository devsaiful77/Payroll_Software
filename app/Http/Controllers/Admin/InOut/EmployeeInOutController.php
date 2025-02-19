<?php

namespace App\Http\Controllers\Admin\InOut;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\EmployeeMultiProjectWorkHistoryController;
use App\Http\Controllers\Admin\MonthlyWorkHistoryController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Enums\AttendanceTypeEnum;
use Illuminate\Http\Request;
use App\Models\EmployeeInOut;
use App\Models\EmployeeInfo;
use App\Models\MonthlyWorkHistory;
use App\Models\EmployeeMultiProjectWorkHistory;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Exports\EmpAttendanceExport;
use App\Exports\EmpAttnInOutInAProjectExport;
use Maatwebsite\Excel\Facades\Excel;


use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;


class EmployeeInOutController extends Controller
{

   function __construct(){

    $this->middleware('permission:attendence-in',['only'=>['index','getListOfEmployeeWorkingInProjectForAttendanceIN']]);  // monthly attendance approved by project manager
    $this->middleware('permission:attendence-out',['only'=>['loadAttendanceOutForm','getAttendanceINAllEmployeeListForAttendaceOutAjaxRequest','multipleEmployeeAttendanceOutRequest']]);  // monthly attendance approved by project manager
    $this->middleware('permission:attendence-edit',['only'=>['employeeAttendanceInOutEditUI','employeeAttendanceInOutUpdate']]);
    $this->middleware('permission:attendance-processing',['only'=>['employeeAttendanceProcessForm','employeeAttendanceProcess']]);
    // $this->middleware('permission:project-work-report',['only'=>['projectWiseList']]);  this functiona tranfer to attendance report
    $this->middleware('permission:attendance-records-approval',['only'=>['loadMonthlyWorkRecordApprovalUI','searchMonthlyWorkRecordForApprovalAJaxRequest','approveOfMonthlyWorkRecords']]);  // monthly attendance approved by project manager
    $this->middleware('permission:month-work-history',['only' => ['employeeMultipleProjectWorkRecordSearchUI','insertAnEmployeeMultipleProjectWorkRecord','loadMonltyMultiProjectWorkRecordInsertForm','updateAnEmployeeMultipleProjectWorkRecordRequest']]); // Monlty work record searching
    $this->middleware('permission:month_work_record_approval_edit',['only'=>['approveOfMonthlyWorkRecords','']]);  // monthly attendance approved by project manager


  }


    /*
    |--------------------------------------------------------------------------
    |  DATABASE OPERATION
    |--------------------------------------------------------------------------
    */

  public function getAnEmployeeMultiProjectWorkRecord(Request $request)
  {

    $EmpMultiProjWorkHisConObj = new EmployeeMultiProjectWorkHistoryController();
    $workRecord = $EmpMultiProjWorkHisConObj->findIdWiseAnEmpMultiprojectWorkHistory($request->id);

    $month = (new CompanyDataService())->getAllMonth();
    $project = (new ProjectDataService())->getAllActiveProjectListForDropdown();

    $error = "data not Found!";
    return response()->json(['workRecord' => $workRecord, 'month' => $month, 'project' => $project, 'error' => $error]);
  }



  /* Ajax method */
  /* =============== Employee Monthly Multiproject Work Record Insert AJax Request =============== */

  public function insertAnEmployeeMultipleProjectWorkRecord(Request $request)
  {
      try{



          $project_id = $request->proj_name;
          $startDate = Carbon::parse($request->startDate);
          $endDate =  Carbon::parse($request->endDate);
          $creator = Auth::user()->id;
          $month =  $request->month;
          $year =  $request->year;
          $new_hours =  $request->totalHourTime;
          $new_ot =  $request->totalOverTime;
          $new_days =  $request->total_days;

            $findEmployee =  (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($request->emp_id,Auth::user()->branch_office_id);

            if ($findEmployee == null) {
              return response()->json(['status' =>404, 'success' =>false,'message'=>'Employee Not Found Failed', 'error' => 'error']);
            }
            else if((new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $findEmployee->emp_auto_id,$month, $year)){
              return response()->json(['status' =>404, 'success' =>false,'message'=>'This Month Salary Already Paid', 'error' => 'error']);
            }
            $multiProjWorkRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMultipleProjectWorkRecords($findEmployee->emp_auto_id, $month, $year,$project_id);
            $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($findEmployee->emp_auto_id, $month, $year);

            if ($multiProjWorkRecord) {
              return response()->json(['status' =>404, 'success' =>false,'message'=>'This Project Work Record Exist', 'error' => 'error']);
            }else if(($record_total->total_days + $new_days) > 31 ||  ($record_total->total_hours + $new_hours > 350)){
              return response()->json(['status' =>404, 'success' =>false,'message'=>'Total Working Days or  Hours Invalid', 'error' => 'error']);
            }

            (new EmployeeAttendanceDataService())->saveAnEmployeeMultipleProjectWorkRecrd( $findEmployee->emp_auto_id, $month,  $year,$new_hours,$new_days, $project_id,$new_ot );
            $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($findEmployee->emp_auto_id, $month, $year);
            $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($findEmployee->emp_auto_id, $month, $year);
            if ($monthlyWorkHist != null) {
              (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $project_id);
            } else {
              (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($findEmployee->emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$project_id);
            }
            return response()->json(['status' =>200, 'success' =>true,'message'=> 'Successfully Added Work Record']);

      }catch(Exception $ex){
          return response()->json(['status' =>404, 'success' =>false,'message'=>'Exception Occured '.$ex, 'error' => 'System Exception Occured']);
      }
  }


    // multi project work record update using modal
  public function updateAnEmployeeMultipleProjectWorkRecordRequest(Request $request){



      $findEmployee =  (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->modal_emp_auto_id);

      if ($findEmployee == null) {
             Session::flash('error', 'Employee Not Found Failed');
            return Redirect()->back();
      }
      $salary_is_paid = (new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $request->modal_emp_auto_id, $request->modal_month, $request->modal_year,);

      if($salary_is_paid){
          Session::flash('error', 'This Month Salary Already Paid , Update Not Possible');
          return Redirect()->back();
      }else if ($request->modal_total_hour == null || $request->modal_total_hour == "") {
          Session::flash('error', 'Data Erro, Updat Operation Failed');
          return Redirect()->back();
      } else  if ($request->modal_total_overtime == null || $request->modal_total_overtime == "") {
          Session::flash('error', 'Data Erro, Updat Operation Failed');
          return Redirect()->back();
      } else if ($request->modal_total_day == null || $request->modal_total_day == "") {
          Session::flash('error', 'Data Erro, Updat Operation Failed');
          return Redirect()->back();
      }

       $existing_record = (new EmployeeAttendanceDataService())->findAnEmpMultiProjectWorkRecordByRecordAutoId($request->modal_empwh_auto_id);
       $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
       $update_days = $record_total->total_days + $request->modal_total_day - $existing_record->total_day;
       $update_hours = $record_total->total_hour + $request->modal_total_hour - $existing_record->total_hour;

       if($update_days > 31 ||  $update_hours > 350){
          Session::flash('error', 'Total Working Days or  Hours Invalid');
          return Redirect()->back();
       }

          $update =  (new EmployeeAttendanceDataService())->updateAnEmployeeMultipleProjectWorkRecordWithAllColum(
          $request->modal_empwh_auto_id, $request->modal_project_name, $request->modal_month, $request->modal_year, $request->modal_total_day,
          $request->modal_total_hour, $request->modal_total_overtime,$request->modal_start_date,$request->modal_end_date,Auth::user()->id);

          $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
          $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
          if ($monthlyWorkHist != null) {
            (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $request->modal_project_name);
          } else {
            (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($request->modal_emp_auto_id, $request->modal_month, $request->modal_year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$request->modal_project_name);
          }

          Session::flash('success', 'Successfully Updated');
          return Redirect()->back();

  }

  /* =============== Employee Multiproject Monthly Work Update By AJAX IN Modal View=============== */

  public function EmployeeMultiprojectMonthlyWorkRecordUpdate(Request $request)
  {


    $emp_auto_id = $request->emp_id;
    $empwh_auto_id = $request->empwh_auto_id;
    $proj_name = $request->proj_name;
    $startDate = Carbon::parse($request->startDate);
    $endDate =  Carbon::parse($request->endDate);
    $creator = Auth::user()->id;

    $fromDate = $request->startDate;


    $totalHourTime = $request->totalHourTime;
    $totalOverTime = $request->totalOverTime;

    $totalDays = $request->total_day;

    $proj_exit = EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->first();


    $findEmployee = EmployeeInfo::where('emp_auto_id', $proj_exit->emp_id)->first();
    if ($findEmployee) {
      if ($totalDays != 0) {


        $update = EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->update([
          'total_day' => $totalDays,
          'total_hour' => $totalHourTime,
          'total_overtime' => $totalOverTime,
          'month' => $proj_exit->month,
          'year' => $proj_exit->year,
          'project_id' => $proj_name,
          'start_date' => $startDate,
          'end_date' => $endDate,
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);


        $total_hour = EmployeeMultiProjectWorkHistory::where('emp_id', $proj_exit->emp_id)
          ->where('month', $proj_exit->month)
          ->where('year', $proj_exit->year)
          ->sum('total_hour');

        $total_day = EmployeeMultiProjectWorkHistory::where('emp_id', $proj_exit->emp_id)
          ->where('month', $proj_exit->month)
          ->where('year', $proj_exit->year)
          ->sum('total_day');

        $total_overtime = EmployeeMultiProjectWorkHistory::where('emp_id', $proj_exit->emp_id)
          ->where('month', $proj_exit->month)
          ->where('year', $proj_exit->year)
          ->sum('total_overtime');


        $update = MonthlyWorkHistory::where('emp_id', $findEmployee->emp_auto_id)->where('month_id', $proj_exit->month)
          ->where('year_id', $proj_exit->year)->update([
            'total_hours' => $total_hour,
            'overtime' => $total_overtime,
            'total_work_day' => $total_day,
            'month_id' => $proj_exit->month,
            'year_id' => $proj_exit->year,
            'project_id' => $proj_name,
            'updated_at' => Carbon::now()
          ]);
        /* update multi project work history */
        return response()->json(['success' => 'Successfully Updated Employee Multi Project Work Recors']);
      } else {
        return response()->json(['error' => 'Days Not Found!']);
      }
    } else {
      return response()->json(['error' => 'Employee Not Found!']);
    }
  }


  //  Employee Daily Attendance IN
  public function employeeAttendanceTimeINInsertRequest(Request $request)
  {

    $emp_list = $request->emp_auto_id;
    $creator = Auth::user()->id;
    $catchDate = $request->date;

    if($catchDate == null || $request->entry_in_time == null){
      Session::flash('error', 'Please Input All Required Data');
      return redirect()->back();
    }

    $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($catchDate);
    $working_day = $dayMonthYear[0] ;
    $month = $dayMonthYear[1] ;
    $year = $dayMonthYear[2] ;
    $project_id = $request->attn_project_id;
    $in_time =  number_format((float)$request->entry_in_time, 1, '.', '');

    $attendance_status = $request->attendance_status;
    $attendance_type = $attendance_status == AttendanceTypeEnum::Working->value ? 1:3 ;

    $isSuccess = false;
    $shift = $request->has('night_shift') ? true : false;

    foreach ($emp_list as $emp_auto_id) {

      if ($request->has('entry_in_checkbox-' . $emp_auto_id)) {
          $isSuccess = true;
          (new EmployeeAttendanceDataService())->insertAnEmployeeAttendanceInInformation($emp_auto_id,$project_id,$working_day,$month,$year,$shift
            ,$in_time,$attendance_type,$attendance_status,$creator,$catchDate,Carbon::now(),Auth::user()->branch_office_id);
      }

    }

    $total_emp = (new EmployeeDataService())->countTotalActiveEmployeesInAProject($project_id,$shift);
    $total_present =  (int) (new EmployeeAttendanceDataService())->countNumberOfEmployeesPresentInTheProject($project_id,$working_day,$month,$year,$shift);
    (new EmployeeAttendanceDataService())->insertDailyAttendanceSummaryRecord($project_id,$shift,$working_day,$month,$year,$total_emp,$total_present, $catchDate,Auth::user()->branch_office_id);
    if($isSuccess){
      Session::flash('success', 'Successfully Added Employee Attendance');
      return redirect()->back();
    }else {
      Session::flash('error', 'Please Select Attendant Employee ');
      return redirect()->back();
    }


  }


   /* =============== Employee Attendence Out Insert By AJAX FOR MULTIPLE EMPLOYEE =============== */
  public function multipleEmployeeAttendanceOutRequest(Request $request){

     if(!$request->has('emp_io_id_list')){
      Session::flash('error', 'Operation Failed, Please Try Again.');
      return redirect()->back();
    }


   // dd($request->all());
    $attendance_inout_id_list = $request->emp_io_id_list;
    $creator = Auth::user()->id;

    $isSuccess = false;
    $counter = 0;
    foreach ($attendance_inout_id_list as $emp_io_id) {

      if ($request->has('entry_out_checkbox-' . $emp_io_id)) {

        $attend_record = (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutRecord($emp_io_id);
        $out_time = (float)  $request->atten_out_time;   // multiple employee out time in one input field
        $daily_work_hours = ($out_time - $attend_record->emp_io_entry_time);
        $isFriday = (new HelperController())->checkThisDayIsFriday($request->selected_atten_date);

        if ($daily_work_hours < 0) {
          $daily_work_hours += 24;
        }
         (new EmployeeAttendanceDataService())->updateEmployeeDailyAttendanceByAttendanceOut($emp_io_id, $out_time, $daily_work_hours,$isFriday);
         $counter++;
      }

    }
    if($counter){
      Session::flash('success','Successfully Updated');
      return redirect()->back();
    }else {
      Session::flash('error','Some Operation Failed, Try Again ');
      return redirect()->back();
    }



  }

  public function employeeAttendanceProcessForm()
  {

    $project = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    $month = (new CompanyDataService())->getAllMonth();
    return view('admin.employee-in-out.employee-attendance-process', compact('project', 'month'));
  }


  /* Ajax method */
  // Attendance Processing AJAX Request
  public function employeeAttendanceProcess(Request $request)
  {

     try{


        $month = $request->month_id;
        $year = $request->year;
        $working_shift = $request->working_shift;
        $project_id = $request->project_id;
        if($request->process_type == 1){
          // emp id wise process
          return  $this->processEmployeeAttendanceInOutByEmployeeID($request->employee_id, $month,$year);
        }else if($request->process_type == 2){
          // project wise project
         return $this->processEmployeeAttendanceByProjectName($project_id,$month,$year,$working_shift);
        }
        return json_encode(['success' => false,'status'=>403,'message'=>"Operation Failed, Please Try Again"]);

    }catch(Exception $ex){
        return json_encode(['success' => false,'status'=>403,'message'=>"System Exception Found, Reload and Try Again",'errpr'=>'error','system_error'=>$ex]);
    }

  }

  public function processEmployeeAttendanceInOutByEmployeeID($multiple_emp_Id,$month,$year){

      $allEmplId = explode(",", $multiple_emp_Id);
      $allEmplId = array_unique($allEmplId); // remove multiple same empl ID

        $emp_list = (new EmployeeDataService())->getEmployeesInfoWithSalaryDetailForEmployeeAdvanceByMultipleEmpId( $allEmplId);

        foreach ($emp_list as $emp) {

            $workInOutRecords =  (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutRecords($emp->emp_auto_id, $month, $year);
            if (count($workInOutRecords) > 0) {

                  foreach ($workInOutRecords as $iorecord) {

                    $proj_exit = (new EmployeeAttendanceDataService())->getAnEmployeeMultipleProjectWorkRecords($emp->emp_auto_id, $month, $year, $iorecord->proj_id);
                    if ($proj_exit != null) {
                      (new EmployeeAttendanceDataService())->updateEmployeeMultipleProjectWorkRecord($proj_exit->empwh_auto_id,$iorecord->total_work_hours,$iorecord->overtime,$iorecord->total_days);
                    } else {
                      (new EmployeeAttendanceDataService())->saveAnEmployeeMultipleProjectWorkRecrd($emp->emp_auto_id,$month,$year,$iorecord->total_work_hours,$iorecord->total_days,$iorecord->proj_id, $iorecord->overtime);
                    }

                  }

                  $last_working_project =  $emp->project_id;
                  $last_record = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceLastWorkingRecordInAMonth($emp->emp_auto_id, $month, $year);
                  if($last_record != null){
                      $last_working_project =  $last_record->proj_id;
                  }

                $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($emp->emp_auto_id, $month, $year);
                $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp->emp_auto_id, $month, $year);
                if ($monthlyWorkHist != null) {
                  (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $last_working_project);
                } else {
                  (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($emp->emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$last_working_project);
                }

            }
        }
        return json_encode(['success' => true,'status'=>200,'message'=>"Successfully Completed"]);
  }

  public function processEmployeeAttendanceByProjectName($project_id,$month,$year, $working_shift){

        // $aproval_status = (new EmployeeAttendanceDataService())->checkIsMonthlyAttendanceRecordApprovalStatus($request->project_id,$month,$year);
        // if($aproval_status == false){
        //   return json_encode(['success' => false,'status'=>403,'message'=>"Work Record Not Verified",'error'=>'error']);
        //   }
        $emp_list = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseWorkedInThisProjectForExportAttendanceInOut($project_id,$month,$year, $working_shift);

        foreach ($emp_list as $emp) {
          $workInOutRecords =  (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutTotalBasicAndOvertimeByProjectId($emp->emp_auto_id,$project_id, $month, $year);

          if (count($workInOutRecords) > 0) {
              foreach ($workInOutRecords as $iorecord) {
                $proj_exit = (new EmployeeAttendanceDataService())->getAnEmployeeMultipleProjectWorkRecords($emp->emp_auto_id, $month, $year, $iorecord->proj_id);
                if ($proj_exit != null) {
                   (new EmployeeAttendanceDataService())->updateEmployeeMultipleProjectWorkRecord($proj_exit->empwh_auto_id,$iorecord->total_work_hours, $iorecord->overtime,$iorecord->total_days);
                } else {
                  (new EmployeeAttendanceDataService())->saveAnEmployeeMultipleProjectWorkRecrd($emp->emp_auto_id,  $month,$year, $iorecord->total_work_hours, $iorecord->total_days, $iorecord->proj_id, $iorecord->overtime );
                }
              }
              $last_working_project =  $project_id;
              $last_record = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceLastWorkingRecordInAMonth($emp->emp_auto_id, $month, $year);
              if($last_record != null){
                  $last_working_project =  $last_record->proj_id;
              }

            $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($emp->emp_auto_id, $month, $year);
            $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp->emp_auto_id, $month, $year);
            if ($monthlyWorkHist != null) {
              (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $last_working_project);
            } else {
              (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($emp->emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$last_working_project);
            }

          }
        }
        return json_encode(['success' => true,'status'=>200,'message'=>"Successfully Completed "]);
  }





  // Attendance Record Process With Multiple Projects For Report
  public function employeeAttendanceProcessWithMultipleProjectsForReport(Request $request){
   // dd($request->all());
  }

  // Attendance Record Searching Ajax Request From Attendance Edit Form
  public function employeeAttendanceInOutRecordSearch(Request $request)
  {

    $day =  (int)date('d', strtotime($request->date));
    $month = (int) date('m', strtotime($request->date));
    $year = (int) date('Y', strtotime($request->date));


    $attendance_record = (new EmployeeAttendanceDataService())->searchEmployeeAttendanceRecord($request->project_id, $request->employee_id, $day, $month, $year);
    if ($attendance_record) {
      $attendance_record->emp_io_entry_date = Carbon::parse($attendance_record->emp_io_entry_date)->format('Y-m-d');

      return response()->json(['success' => true, 'status' => 200, 'data' => $attendance_record]);
    } else {
      return response()->json(['success' => false, 'status' => 404, 'error' => 'Attendance Record Not Found']);
    }
  }

  public function employeeAttendanceInOutRecordDelete(Request $request)
  {

    // dd('calllll');
    $emp_io_id = $request->emp_io_id;

    $isSuccess = (new EmployeeAttendanceDataService())->deleteEmployeeDailyAttendanceRecord($emp_io_id);

    if ($isSuccess) {
      return response()->json(['success' => true, 'status' => 200]);
    } else {
      return response()->json(['success' => false, 'status' => 404, 'error' => 'Opps! Please Try Again']);
    }
  }

  // AJAX Request for update employee attendance record
  public function employeeAttendanceInOutUpdate(Request $request)
  {
     try{

          $emp_io_id  = $request->emp_io_id;
          $emp_io_entry_time = $request->emp_io_entry_time;
          $emp_io_out_time = $request->emp_io_out_time;
          $daily_work_hours = $request->emp_io_out_time - $request->emp_io_entry_time;
          $emp_io_date = $request->emp_io_date;

          $daily_work_hours = $daily_work_hours < 0 ? $daily_work_hours+24 : $daily_work_hours; // nightshit duty if hours <0

          $attend_record = (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutRecord($emp_io_id);
          if($attend_record == null){
            return response()->json(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Attendance Record Not Found ']);
          }
         // $emp_io_day = (new HelperController())->getDayFromDateValue($request->emp_io_date);
          $is_friday = (new HelperController())->checkThisDayIsFriday($attend_record->emp_io_year.'-'. $attend_record->emp_io_month.'-'.$attend_record->emp_io_date);
          //dd($request->all());
          $isSuccess = (new EmployeeAttendanceDataService())->updateEmployeeDailyAttendanceRecord($emp_io_id, $emp_io_entry_time, $emp_io_out_time, $daily_work_hours,$is_friday);

          if ($isSuccess) {
            return response()->json(['success' => true, 'status' => 200,'message'=>'Successfully Updated']);
          } else {
            return response()->json(['success' => false, 'status' => 404, 'error' => 'error','message'=>'Update Operation Failed ']);
          }

     }catch(Exception $ex){
           return response()->json(['success' => false, 'status' => 404, 'error' => 'error','message'=>'System Operation Failed '.$ex]);
     }
  }

  public function outTimeProcessEntryList(Request $request)
  {


    $day = (new HelperController())->getDayFromDateValue($request->date);
    $month = (new HelperController())->getMonthFromDateValue($request->date);
    $year = (new HelperController())->getYearFromDateValue($request->date);

    $getAll = EmployeeInOut::where('employee_in_outs.emp_io_date', $day)
      ->where('employee_in_outs.emp_io_month', $month)
      ->where('employee_in_outs.emp_io_year', $year)
      ->where('employee_in_outs.emp_io_out_time', 0)
      ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
      ->get();

    if ($getAll == true) {
      return response()->json(["entryList" => $getAll]);
    } else {
      return response()->json(['error' => "Data Not Found!"]);
    }
  }


  public function projectWiseInOutList(Request $request){

    $project = (new ProjectDataService())->getAllActiveProjectListForDropdown();


    $proj_id = $request->proj_name;

    $catchDate = $request->date;
    $date = date('d', strtotime($catchDate));
    $month = date('m', strtotime($catchDate));
    $year = date('Y', strtotime($catchDate));

    $getAll = EmployeeInOut::where('employee_infos.project_id', $proj_id)
      ->where('employee_in_outs.emp_io_date', $date)
      ->where('employee_in_outs.emp_io_month', $month)
      ->where('employee_in_outs.emp_io_year', $year)
      ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
      ->get();

    if ($getAll) {
      return view('admin.employee-in-out.proj-wise-list', compact('project', 'getAll'));
    } else {
      return view('admin.employee-in-out.proj-wise-list', compact('project'));
    }
  }


  // Muti Employee Attendance IN OUT RECORD AJAX Request FOR UPDATE MULTIPLE EMPLOYEE
  public function searchMultipleEmpAttendanceRecordForUpdate(Request $request){


      $proj_id =   $request->proj_id;

      $attendance_date = $request->attendance_date;
      $day = (int)  date('d', strtotime($attendance_date));
      $month = (int)  date('m', strtotime($attendance_date));
      $year = (int)  date('Y', strtotime($attendance_date));

     // $records = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByProjectIdDayMonthYear($proj_id,$day,$month,$year,0);

      $records1 = EmployeeInOut::where('proj_id', $proj_id)
                 ->where('employee_in_outs.emp_io_date', $day)
                 ->where('employee_in_outs.emp_io_month', $month)
                 ->where('employee_in_outs.emp_io_year', $year)
                 ->where('employee_in_outs.emp_io_shift', $request->working_shift)
                ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_infos.employee_id')
                ->get();

    if (count($records1) > 0) {
      return response()->json(["status" =>200,"success" =>true,"data" => $records1]);
    } else {
      return  response()->json(["status"=>403,"success" =>true,"error" =>"Record Not Found","data"=>null]);
    }
  }
  // MULTIPLE EMPLOYEE IN OUT ATTENDANCE RECORD UPDATE  REQUEST
  public function multipleEmployeeAttendanceUpdateRequest(Request $request){
      try{

        if(!$request->has('emp_io_id_list')){
          Session::flash('error', 'Operation Failed, Please Try Again.');
          return redirect()->back();
        }
        $attendance_inout_id_list = $request->emp_io_id_list;

        $updated_by = Auth::user()->id;
        $counter = 0;
        $isFriday = (new HelperController())->checkThisDayIsFriday($request->multiple_attn_selected_date);
        foreach ($attendance_inout_id_list as $emp_io_id) {

          if ($request->has('entry_out_checkbox-' . $emp_io_id)) {

           // $attend_record = (new EmployeeAttendanceDataService())->getAnEmployeeAttendanceInOutRecord($emp_io_id);
            $out_time = (float)  $request->atten_out_time;   // multiple employee out time in one input field
            $in_time = (float)  $request->atten_in_time;   // multiple employee out time in one input field
            $daily_work_hours = ($out_time - $in_time);
            if ($daily_work_hours < 0) {
              $daily_work_hours += 24;
            }
            (new EmployeeAttendanceDataService())->updateAnEmployeeDailyAttendanceRecordFromMultiEmployeeUpdateRequest($emp_io_id, $in_time, $out_time, $daily_work_hours,$updated_by,$isFriday);
            $counter++;
          }

        }
        if($counter){
          Session::flash('success','Successfully Updated');
          return redirect()->back();
        }else {
          Session::flash('error','Some Operation Failed, Try Again ');
          return redirect()->back();
        }

      }catch(Exception $ex){
        Session::flash('error','Some Operation Failed, Try Again ');
        return redirect()->back();
      }

  }


  public function loadMonthlyWorkRecordApprovalUI(){


      $user = Auth::user();
      $current_month = (new HelperController())->getCurrentMonthIntValue();
      $year = date('Y');
      if($user->hasRole('Admin')){
        $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
      }else {
        $project_ids = (new ProjectDataService())->getLoginUserAccessPermissionProjectIDs($user->id);
      }

      $records =  (new EmployeeAttendanceDataService())->getProjectWiseTotalWorkHoursSummaryForMonthlyWorkRecordApproval($project_ids,$current_month ,$year);

      foreach($records as $arecord){

        $saved = (new EmployeeAttendanceDataService())->insertAttendanceApprovalRecord($arecord->proj_id, $current_month, $year,0,Carbon::now());
        $db_record = (new EmployeeAttendanceDataService())->getAttendanceApprovalRecord($arecord->proj_id, $current_month, $year);
        $arecord->atten_appro_auto_id = $db_record->atten_appro_auto_id;
        $arecord->approval_status = $db_record->approval_status;
        $arecord->approved_by_id = $db_record->approved_by_id;

      }
      return view('admin.employee-in-out.montly_atten_work_record_approval', compact('records'));

  }

  public function searchMonthlyWorkRecordForApprovalAJaxRequest(Request $request){
      try{

        $user = Auth::user();
        $month =  $request->month;
        $year = $request->year;
        if($user->hasRole('Admin')){
          $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
        }else {
          $project_ids = (new ProjectDataService())->getLoginUserAccessPermissionProjectIDs($user->id);
        }
        $records =  (new EmployeeAttendanceDataService())->getProjectWiseTotalWorkHourseSummaryForMonthlyWorkRecordApproval($project_ids,$month ,$year);
       // return response()->json(["status" =>404,"success" =>false,'message'=>'Operation Failed with Exception','error'=>'error','ex'=> $records]);

        foreach($records as $arecord){
          $saved = (new EmployeeAttendanceDataService())->insertAttendanceApprovalRecord($arecord->proj_id, $month, $year,0,Carbon::now());
          $db_record = (new EmployeeAttendanceDataService())->getAttendanceApprovalRecord($arecord->proj_id, $month, $year);
          $arecord->atten_appro_auto_id = $db_record->atten_appro_auto_id;
          $arecord->approval_status = $db_record->approval_status;
          $arecord->approved_by_id = $db_record->approved_by_id;
        }
        return response()->json(["status" =>200,"success" =>true,'data'=>$records]);


      }catch(Exception $ex){
        return response()->json(["status" =>404,"success" =>false,'message'=>'Operation Failed with Exception','error'=>'error','ex'=>$ex]);
      }
  }
  public function approveOfMonthlyWorkRecords(Request $request){
        try{

            $saved = (new EmployeeAttendanceDataService())->updateMonthlyAttendanceWorkRecordsByPendingOrApproved($request->atten_appro_auto_id,$request->approved_status,Auth::user()->id);
            if($saved){
              return response()->json(["status" =>200,"success" =>true,'message'=>'Successfully Completed']);
            }else {
              return response()->json(["status" =>404,"success" =>false,'message'=>'Operation Failed','error'=>'error']);
            }

        }catch(Exception $ex){
          return response()->json(["status" =>404,"success" =>false,'message'=>'Operation Failed with Exception','error'=>'error']);
        }

  }



    /*
    |--------------------------------------------------------------------------
    |  BLADE OPERATION
    |--------------------------------------------------------------------------
    */

  public function index()
  {
      $project = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
      $allow_days = (new EmployeeAttendanceDataService())->getAttendanceINOUTPermissionDaysValueByAutoId(1); // attendance IN
      return view('admin.employee-in-out.employee-attendance-in', compact('project','allow_days'));
  }


  public function editAnEmployeeMultiProjectWorkRecord($recordId)
  {
    $EmpMultiProjWorkHisConObj = new EmployeeMultiProjectWorkHistoryController();
    $multyProjInfoAnEmp = $EmpMultiProjWorkHisConObj->findIdWiseAnEmpMultiprojectWorkHistory($recordId);
    $month = (new CompanyDataService())->getAllMonth();
    $project = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    return view('admin.employee-in-out.edit-multi-project-in-out', compact('month', 'project', 'multyProjInfoAnEmp'));
  }

    // Employee Attendance Edit UI Form
    public function employeeAttendanceInOutEditUI()
    {
      $projects = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
      $allow_days_single_emp = (new EmployeeAttendanceDataService())->getAttendanceINOUTPermissionDaysValueByAutoId(3); // Single Employee Attendance Edit
      $allow_days_multi_emp = (new EmployeeAttendanceDataService())->getAttendanceINOUTPermissionDaysValueByAutoId(4); // Multi Employee Attendance Edit
      return view('admin.employee-in-out.employee-attendance-edit', compact('projects','allow_days_single_emp','allow_days_multi_emp'));
    }

  public function employeeMultipleProjectWorkRecordSearchUI()
  {
    $months = (new CompanyDataService())->getAllMonth();
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    $currentMonth = Carbon::now()->format('m');
    return view('admin.month-work.search-work-record', compact('months', 'currentMonth','projects'));
  }

    // Employee Multiple Record Searching Ajax Request
    public function searchEmployeeMultipleProjectWorkRecord(Request $request)
    {
            try{
                    $empMulProjWorkRecord = (new EmployeeAttendanceDataService())->searchAnEmployeeMultiprojectWorkRecordsForListViewByEmployeeIdMonthYear($request->emp_id, $request->month, $request->year,Auth::user()->branch_office_id);
                    return response()->json(['status' =>200,'success'=> true,"data" => $empMulProjWorkRecord, "error" =>null]);
            }catch(Exception $ex){
                return response()->json(['status' =>404,'success'=> false, "error" =>'error' , 'message' => "System Operation Failed"]);
            }
    }

     // An employee mutliple project working record delete request
    public function deleteAnEmployeeMultiProjectWorkRecordRequest($empwh_auto_id)
     {
       try{

             $updated_by = Auth::user()->id;
             $multyProjWorkRecord =(new EmployeeAttendanceDataService())->findAnEmpMultiProjectWorkRecordByRecordAutoId($empwh_auto_id);
             $monthWorkRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($multyProjWorkRecord->emp_id, $multyProjWorkRecord->month, $multyProjWorkRecord->year);

             if(  $multyProjWorkRecord == null   ){
                 return response()->json(['status' =>404, 'success' =>false,'message'=>'Record Not Found, Please Reload', 'error' => 'error']);
             }
             else if( (new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $multyProjWorkRecord->emp_id,$multyProjWorkRecord->month, $multyProjWorkRecord->year)){
                 return response()->json(['status' =>404, 'success' =>false,'message'=>'This Month Salary Already Paid', 'error' => 'error']);
             }
             else if($monthWorkRecord == null){
               $isSuccess = (new EmployeeAttendanceDataService())->deleteAnEmpMultiprojectWorkRecordByRecordAutoId($empwh_auto_id);
               return response()->json(["status" =>404,"success" =>false, 'error'=>'error',"message" =>  "Operation Failed, Please Try Again"]);
             }
             $noOfProWorkInThisMonth = (new EmployeeAttendanceDataService())->countAnEmployeeWorkingProjectThisMonth($multyProjWorkRecord->emp_id, $multyProjWorkRecord->month, $multyProjWorkRecord->year);

             $isSuccess = (new EmployeeAttendanceDataService())->deleteAnEmpMultiprojectWorkRecordByRecordAutoId($empwh_auto_id);

             if ($noOfProWorkInThisMonth == 1) {
                 (new EmployeeAttendanceDataService())->deleteAnEmployeeMonthlyWorkRecordByEmpAutoIdMonthAndYear($multyProjWorkRecord->emp_id, $multyProjWorkRecord->month, $multyProjWorkRecord->year);
                 return response()->json(["status" =>200,"success" =>true,"message" =>"Successfully Completed"]);
             } else {

                 $all_records = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordsOnly($multyProjWorkRecord->emp_id, $multyProjWorkRecord->month, $multyProjWorkRecord->year);

                 $salary_project_id = 0;
                 $max_working_days = -1;
                 foreach($all_records as $arecord){
                         if($max_working_days < $arecord->total_day){
                           $max_working_days = $arecord->total_day;
                           $salary_project_id = $arecord->project_id;
                         }
                   }
               (new EmployeeAttendanceDataService())->calculateAnEmployeeMontlyTotalWorkFromMultiProjectWorkAndUpdateMonthlyRecord($multyProjWorkRecord->emp_id,$multyProjWorkRecord->month,$multyProjWorkRecord->year,$salary_project_id,$updated_by);
               return response()->json(["status" =>200,"success" =>true,"message" =>  "Successfully Completed",'dd'=>$salary_project_id ]);
             }
           }catch(Exception $ex){
             return response()->json(["status" =>404,"success" =>false, 'error'=>'error',"message" =>  "Operation Failed with System Exception"]);
           }

   }

  // Employee Attendance OUT Form
  public function loadAttendanceOutForm() //entryList
  {
    $project = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
    $allow_days = (new EmployeeAttendanceDataService())->getAttendanceINOUTPermissionDaysValueByAutoId(2); // attendance OUT
    return view('admin.employee-in-out.employee-attendance-out', compact('project','allow_days'));
  }




  public function projectWiseList()
  {

    $project = (new EmployeeRelatedDataService())->getAllProjectInformation();
    return view('admin.employee-in-out.proj-wise-list', compact('project'));
  }

  public function loadMultipleEmployeeAttendanceEditForm(){
    $project = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);

    return view('admin.employee-in-out.multi_emp_attendance_inout_edit', compact('project'));
  }

  public function searchAnEmployeeWorkRecordByMonth(Request $request)
  {

    $emp_work_Record = (new EmployeeAttendanceDataService())->getAnEmployeeMultiprojectWorkRecordByEmployeeIdForAjaxRespnse($request->emp_id, $request->month, $request->year);
    if (count($emp_work_Record) == 0) {
      return response()->json(['status' =>404,'success'=> false, "error" => "Employee Work Record Not Found"]);
    }
    return response()->json(['status' =>200,'success'=> true,"empMulProjWorkRecord" => $emp_work_Record, "error" => null]);

  }






















    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE ATTENDANCE REPORT SECTION
    |--------------------------------------------------------------------------
    */

  public function employeeAttendanceReportProcessingUI()
  {

    $project = (new ProjectDataService())->getAllActiveProjectListForDropdown();
    $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();

    $month = (new CompanyDataService())->getAllMonth();
    return view('admin.report.employee_attendance.attendance_report_process_ui', compact('month', 'project', 'sponser'));
  }


  // Employee Day & Night shift Attendance Summary report
  public function showEmployeeDailyAttendanceSumarryReport(Request $request){



      $catchDate = $request->date;
      $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);

      $day = $dayMonthYear[0];
      $month = $dayMonthYear[1];
      $year = $dayMonthYear[2];
      $project_id_list = $request->project_name_id != null ? $request->project_name_id : [] ;
     $company = (new CompanyDataService())->findCompanryProfile();
    //  dd($request->all());
     if($request->working_shift == 0){
      // Day Shift Only
      return "Report Generation Under Processing";

     }else if($request->working_shift == 1){
      // Night Shift
      return "Report Generation Under Processing";
     }
     else if($request->working_shift == 2){
         // Day&Nisht Shift
        // $attendance_summary_records = (new EmployeeAttendanceDataService())->getEmployeeDayNightDetailsAttendanceSummaryByProject($day, $month, $year,$project_id_list);
        $attendance_summary_records = (new EmployeeAttendanceDataService())->getEmployeeDailyAttendanceSummaryByProject($day, $month, $year,$project_id_list);

     }else if($request->working_shift == 3){
        // Previousday Nightshift And Selected Date Dayshift
        $attendance_summary_records = (new EmployeeAttendanceDataService())->getPrevioudayNightAndSelectedDateDayShiftAttendanceSummaryByProject($day, $month, $year,$project_id_list);
        $report_title[0] = $request->date;
        return view('admin.report.employee_attendance.previousday_today_attendance_summary_report', compact('attendance_summary_records','report_title', 'company'));

     }else {
      $attendance_summary_records = (new EmployeeAttendanceDataService())->getEmployeeDailyAttendanceSummaryByProject($day, $month, $year,$project_id_list);
     }

     $report_title[0] = $request->date;

     return view('admin.report.employee_attendance.project_wise_daily_attendance_summary', compact('attendance_summary_records','report_title', 'company'));

  }


    // Employee Daily Attendance Trade WIse  manpower Summary report
  public function showEmpDailyAttendanceManpowerSumarryReport(Request $request){

        $catchDate = $request->date;
        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
        $day = $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];

      $atten_day_manpower_records = (new EmployeeAttendanceDataService())->getEmployeeDailyAttendanceManpowerSummaryByProject($day, $month, $year,$request->project_id,0);
      $atten_night_manpower_records = (new EmployeeAttendanceDataService())->getEmployeeDailyAttendanceManpowerSummaryByProject($day, $month, $year,$request->project_id,1);

      $company = (new CompanyDataService())->findCompanryProfile();

      $report_title[0] = $request->date;
      $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id);

      return view('admin.report.employee_attendance.project_wise_daily_attn_manpower_summary', compact('atten_day_manpower_records','atten_night_manpower_records','report_title', 'company','project_name'));

  }




   // 2 Employee Monthly working hours Summary or Date to Date Attendance Details report
  public function employeeAttendanceMonthlyReportProcessAndShow(Request $request){
      try{
        if($request->monthly_summary){
          // Monthly summary report
          return $this->employeeAttendanceMonthlySummaryReportBaseOnInOutProjectId($request);
       }else {
         // Employee Date to Date Attendance Details report
         return $this->processProjectBaseEmployeeAttendanceDateToDateReport($request->project_id,$request->date,$request->sponserId,$request->working_shift,$request->page_offset);
       }
      }catch(Exception $ex){
        return "System Operation Error ".$ex;
      }
  }

  //2.1 Monthly Attendance Summary IN OUT project ID Wise all employees
  private function employeeAttendanceMonthlySummaryReportBaseOnInOutProjectId(Request $request)
  {

       $catchDate = $request->date;
       $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);

       $fromday = 1;
       $today = $dayMonthYear[0];
       $month = $dayMonthYear[1];
       $year = $dayMonthYear[2];
       $project_id = (int) $request->project_id;
       $working_shift = $request->working_shift;

       $monthName = (new HelperController())->getMonthName($month);
       $numberOfDaysInThisMonth = $today    ; // (new HelperController())->getNumberOfDaysInMonthAndYear($month, $year);
       $company = (new CompanyDataService())->findCompanryProfile();
       $projectName = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
       $sponserName = null;

        $records = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceSummaryDateToDateRecords($project_id,$fromday,$today, $month, $year,$working_shift);
        $prepared_by = Auth::user()->name;
        return view('admin.report.employee_attendance.project_emp_monthly_work_summary',compact('records','year','monthName','company','sponserName','projectName','working_shift','prepared_by'));

  }

   // 2.2 Employee Monthly Date to Date Attendance Details report preview
  private function processProjectBaseEmployeeAttendanceDateToDateReport($project_id,$date,$sponsor_id,$working_shift,$page_offset)
  {
        // dd($working_shift);
        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($date);
        $fromday = 1;
        $day_name_in_month = array();
        $numberOfDaysInThisMonth = (new HelperController())->getDayFromDateValue($date); // $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];
        $monthName = (new HelperController())->getMonthName($month);
        $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
        $company = (new CompanyDataService())->findCompanryProfile();
        $projectName = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
        $sponserName = null;
        if ($sponsor_id != null) {
          $sponserName = (new EmployeeRelatedDataService())->getASponserNameBySponerId($sponsor_id);
        }

        $total_emp_list  = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseWorkedInThisProjectForExportAttendanceInOut($project_id,$month,$year,$working_shift);

        $count = 0;
        $attendent_emp_list = array();
        ini_set('max_execution_time', 600);
        $page_limit = $page_offset+ 500;
        if($page_offset >= count($total_emp_list)){
          return "No Data Found";
        }else if(count($total_emp_list) < $page_limit) {
          $page = count($total_emp_list) - $page_offset;
          $page_limit = $page_offset + $page;
        }

       //$working_shift_list = $working_shift == "" ? [0,1]:[$working_shift];
        for($i = $page_offset; $i <$page_limit; $i++){
          $emp = $total_emp_list[$i];

        //  $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsForAttendancePreview($emp->emp_auto_id,$project_id,$fromday,$numberOfDaysInThisMonth, $month, $year,$working_shift_list);
          $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecords($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);

              if(count($Attendence) >0){
                $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                foreach($Attendence as $attend) {
                  $allAttend[(int) $attend->emp_io_date] = $attend;
                }
                $emp->attendace_records = $allAttend;
                $emp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                $emp->total_daily_work_hours = $emp->total_over_time + (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                $emp->total_working_days = count($Attendence);
                $attendent_emp_list[$count] = $emp;
                $count++;
              }
        }

        $daily_total_hours_array = array_fill(1, $numberOfDaysInThisMonth + 1, 0);
        list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($month,$year);
        $prepared_by = Auth::user()->name;
        return view('admin.report.employee_attendance.project_date_to_date_attend_report_preview', compact('numberOfDaysInThisMonth','day_name_in_month', 'holidayArray', 'sponserName', 'projectName', 'company', 'attendent_emp_list', 'monthName', 'year', 'daily_total_hours_array', 'totalHolidays','working_shift','prepared_by','page_limit','page_offset'));

  }

   //3.2 Attendance Summary Report
  public function showEmployeeMonthlyAttendanceSumarryReport(Request $request){

       $project_id_list = $request->project_name_id  ;
     //  $month = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
      // $year = $request->year_id;

       $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
       $day = $dayMonthYear[0];
       $month = $dayMonthYear[1];
       $year = $dayMonthYear[2];


      if($request->report_type == 1){

        if($project_id_list == null){
          $project_records = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        }else {
          $project_records = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);
        }
         $company = (new CompanyDataService())->findCompanryProfile();
          $counter = 0;
          foreach($project_records as $pid){

              $day_shift_record = (new EmployeeAttendanceDataService())->getMonthlyAttendanceHoursSummaryReportByProject($month, $year,$pid->proj_id,0);
              if($day_shift_record){
                $day_shift_record->total_emp_worked = (new EmployeeAttendanceDataService())->countTotalNumberOfWorkersWorkedInAMonthInTheProject($pid->proj_id,$month, $year, 0);
                $project_records[$counter]->day_shift_record = $day_shift_record;
              }
              $night_shift_record = (new EmployeeAttendanceDataService())->getMonthlyAttendanceHoursSummaryReportByProject($month, $year,$pid->proj_id,1);
              if($night_shift_record){
                $night_shift_record->total_emp_worked = (new EmployeeAttendanceDataService())->countTotalNumberOfWorkersWorkedInAMonthInTheProject($pid->proj_id,$month, $year, 1);
                $project_records[$counter]->night_shift_record = $night_shift_record;
              }
              $counter++;
          }
          $report_title[0] = (new HelperController())->getMonthName($month);
          $report_title[1] = $year;
          return view('admin.report.employee_attendance.all_project_monthly_hours_summary', compact('project_records','report_title', 'company'));
      }else if($request->report_type == 2){
          // Today Present Hourly and Basic Employee Summary
           return  $this->processAndShowTodayPresentHourlyAndBasicEmployeeSummary($project_id_list,0,$day,$month,$year);
      }


  }


   // Today Present Hourly and Basic Employee Summary
  private function processAndShowTodayPresentHourlyAndBasicEmployeeSummary($project_id_list,$working_shift,$day,$month,$year){

      if($project_id_list == null){
        $project_records = (new ProjectDataService())->getAllActiveProjectListForDropdown();
      }else {
        $project_records = (new ProjectDataService())->getProjectListByMultipleProjectId($project_id_list);
      }

      //  $summary_records = (new EmployeeAttendanceDataService())->getTodayPresentHourlyAndBasicEmployeeSummary($project_id_list[0],20,$month,$year,$working_shift);

     $counter = 0;
     foreach($project_records as $pid){
        $day_shift_record = (new EmployeeAttendanceDataService())->getTodayPresentHourlyAndBasicEmployeeSummary($pid->proj_id,0,$day,$month,$year);
        if(count($day_shift_record) == 2){
          $project_records[$counter]->day_shift_record  =  [
            'total_basic_emp' => $day_shift_record[0]->total_emp,
            'total_hourly_emp' => $day_shift_record[1]->total_emp,];
         }else if(count($day_shift_record) == 1){
            $project_records[$counter]->day_shift_record  =  [
            'total_basic_emp' => $day_shift_record[0]->total_emp,
            'total_hourly_emp' => 0,
          ];

        }else{
          $project_records[$counter]->day_shift_record  = null;
        }
         $night_shift_record = (new EmployeeAttendanceDataService())->getTodayPresentHourlyAndBasicEmployeeSummary($pid->proj_id,1,$day,$month,$year);
         if( $night_shift_record == null){
          $project_records[$counter]->night_shift_record  =  null;
         }
         if(count($night_shift_record) == 2){
            $project_records[$counter]->night_shift_record  =  [
              'total_basic_emp' => $night_shift_record[0]->total_emp,
              'total_hourly_emp' => $night_shift_record[1]->total_emp,];
           }else if(count($night_shift_record) == 1){
              $project_records[$counter]->night_shift_record  =  [
              'total_basic_emp' => $night_shift_record[0]->total_emp,
              'total_hourly_emp' => 0];
          }else {
            $project_records[$counter]->night_shift_record  =  null;
          }
         $counter++;
     }
     $total_active_emps = (new EmployeeDataService())->countTotalEmployees(1);
     $company = (new CompanyDataService())->findCompanryProfile();
     $report_title[0] =  $year.'-'.$month.'-'.$day;
     return view('admin.report.employee_attendance.today_present_basic_hourly_emp_summary', compact('project_records','total_active_emps','report_title', 'company'));



  }




  // 4 Employee Attendance Excel Download
  public function downloadEmployeeAttendanceRecordAsExcel(Request $request)
  {
      try{

        $catchDate = $request->date;
        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);

        $fromday = 1;
        $day_name_in_month = array();
        $numberOfDaysInThisMonth = $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];
        $project_id = (int) $request->project_id;
        $working_shift = $request->working_shift;

        $monthName = (new HelperController())->getMonthName($month);
        $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
        $company = (new CompanyDataService())->findCompanryProfile();
        $projectName = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
        $project_color_codes = (new ProjectDataService())->getAllProjectColorCodeArray();
        ini_set('max_execution_time', 300);

       // dd($request->all());
        if($request->report_type ==1){
          // project base report
          $list_of_emp  = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseWorkedInThisProjectForExportAttendanceInOut($project_id,$month,$year,$working_shift);
          $projectName  = is_null($projectName) == true ? 'attendance.xlsx' : $projectName.' Month of '.$monthName.'-'.$year.' Timesheet'.".xlsx";

          return Excel::download(new EmpAttnInOutInAProjectExport($working_shift,$fromday,$numberOfDaysInThisMonth,$numberOfDaysInThisMonth,$month,$year,$list_of_emp,$project_color_codes),$projectName);

        }else if($request->report_type == 2){
            // employee base report
            $list_of_emp = (new EmployeeDataService())->getEmployeeListForAttendanceReportWithProjectAndSponsorAndJobStatus($project_id,$request->sponserId,$working_shift);

            return Excel::download(new EmpAttendanceExport(null,null,$working_shift,$fromday,$numberOfDaysInThisMonth,$numberOfDaysInThisMonth,$month,$year,$list_of_emp,$project_color_codes), 'attendance.xlsx');
         }

      }catch(Exception $ex){
        return $ex."System Error Found , Please Try Again";
      }


  }


 // 1  Multiple Employee ID Attendance Report
   public function getAnEmployeeDayByDateMonthlyAttendanceReport(Request $request){

          if($request->report_type == 1){
              // single month multiple employee attendance report
              return $this->getSingleMonthMultEmployeeAttendanceReport($request->employee_id,$request->month_id,$request->year_id);
          }else if($request->report_type == 2){
              // single employee all attendnace report
              return $this->getAnEmployeeAttendanceAllRecords($request->employee_id,$request->year_id);
          }
   }

  //1 Employees Single Month Atten History
   private function getSingleMonthMultEmployeeAttendanceReport($employee_ids,$month,$year){


          $fromday = 1;
          $count = 0;
          $day_name_in_month = array();
          $attendent_emp_list = array();

          $numberOfDaysInThisMonth = (new HelperController())->getNumberOfDaysInMonthAndYear($month,$year);
          $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
          $monthName = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->findCompanryProfile();


          $allEmplId = explode(",", $employee_ids);
          $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
          $emp_auto_id_list = (new EmployeeDataService())->getListOfEmployeeEmpAutoIdAsArrayByEmployeeIdList( $allEmplId);

          $working_proj_list = (new EmployeeAttendanceDataService())->getEmployeeWorkingListOfProjectWithColorCodeByWorkingMonthYear($emp_auto_id_list,$month,$year);


          $directEmp =(new EmployeeDataService())->getMultipleEmployeeInfoByMultipleEmployeeIdForAttendanceReport($allEmplId);


          foreach ($directEmp as $emp) {
                $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsWithProjecColorCodeForShowingAllMonthReport($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
               // $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecords($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);

                if(count($Attendence) >0){
                    $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                    $total_daily_work_hours = 0;
                    $total_over_time = 0;
                    $total_working_days = 0;
                    foreach($Attendence as $attend) {
                      $attendday = (int) $attend->emp_io_date;
                      $allAttend[$attendday] = $attend;
                    }
                    $emp->attendace_records = $allAttend;
                    $emp->last_working_project_name = (new ProjectDataService())->getProjectNameByProjectId($Attendence[(count($Attendence)-1)]->proj_id);
                    $emp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                    $emp->total_daily_work_hours = (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                    $emp->total_working_days = count($Attendence);
                    $attendent_emp_list[$count] = $emp;
                    $count++;

                }else {
                    $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                    $total_daily_work_hours = 0;
                    $total_over_time = 0;
                    $total_working_days = 0;

                    $emp->attendace_records = $allAttend;

                    $emp->last_working_project_name = '';
                    $emp->total_over_time = 0;
                    $emp->total_daily_work_hours = 0;
                    $emp->total_working_days = 0;
                    $attendent_emp_list[$count] = $emp;
                    $count++;
                }
          }

          $daily_total_hours_array = array_fill(1, $numberOfDaysInThisMonth + 1, 0);
          list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($month,$year);
          $prepared_by = Auth::user()->name;
          $working_shift = '';

          return view('admin.report.employee_attendance.multi_emp_id_datewise_attendance', compact('day_name_in_month','numberOfDaysInThisMonth', 'holidayArray' ,  'company',
          'attendent_emp_list', 'monthName', 'year', 'daily_total_hours_array', 'totalHolidays','working_shift','prepared_by','working_proj_list'));
   }
   //1 An Employee All Month Atten History
   private function getAnEmployeeAttendanceAllRecords($employee_id,$year){


          $fromday = 1;
          $count = 0;
          $attendent_emp_list = array();
         // $emp = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($employee_id,'employee_id');
          $emp = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($employee_id,'employee_id',Auth::user()->branch_office_id);

          // (new EmployeeDataService())->getEmployeesInformationWithAllReferenceTable($employee_id);
          if(count($emp) > 1){
            return 'Employee Not Found ';
          }
          $emp = $emp[0];
          dd($emp);

          $working_proj_list = (new EmployeeAttendanceDataService())->getEmployeeWorkingListOfProjectWithColorCode([$emp->emp_auto_id],date('Y-m-d', strtotime('-1 year')),date('Y-m-d'));
          $listofmonth_year = (new HelperController())->getMonthsInRangeOfDate(date('Y-m-d', strtotime('-1 year')),date('Y-m-d'));
          foreach($listofmonth_year as $arecord){

                $aemp = new EmployeeInfo();
                $aemp->working_month = (new HelperController())->getMonthName($arecord['month']);
                $aemp->working_year =  $arecord['year'];
                $aemp->number_of_day_this_month = (new HelperController())->getNumberOfDaysInMonthAndYear($arecord['month'],$arecord['year']);
                $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsWithProjecColorCodeForShowingAllMonthReport($emp->emp_auto_id,$fromday,31, $arecord['month'],$arecord['year']);
                list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($arecord['month'],$arecord['year']);
                $aemp->holiday_array = $holidayArray;
                $aemp->total_holiday = $totalHolidays;
                $allAttend = array_fill(0, 33, null);

                if(count($Attendence) >0){
                  foreach($Attendence as $attend) {
                    $allAttend[(int) $attend->emp_io_date] = $attend;
                  }
                  $aemp->attendace_records = $allAttend;
                  $aemp->last_working_project_name = (new ProjectDataService())->getProjectNameByProjectId($Attendence[(count($Attendence)-1)]->proj_id);
                  $aemp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$aemp->number_of_day_this_month ,$arecord['month'],$arecord['year']);
                  $aemp->total_daily_work_hours =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$aemp->number_of_day_this_month ,$arecord['month'],$arecord['year']);
                  $aemp->total_working_days = count($Attendence);
                  $attendent_emp_list[$count] = $aemp;
                  $count++;

                }else {

                  $aemp->attendace_records =   $allAttend;
                  $aemp->last_working_project_name = '';
                  $aemp->total_over_time = 0;
                  $aemp->total_daily_work_hours = 0;
                  $aemp->total_working_days = 0;
                  $attendent_emp_list[$count] = $aemp;
                  $count++;

                }

          }
          $company = (new CompanyDataService())->findCompanryProfile();
          $prepared_by = Auth::user()->name;
          $working_shift = '';
          return view('admin.report.employee_attendance.anemp_attn_all_records', compact('emp','company',
          'attendent_emp_list','totalHolidays','working_shift','prepared_by','working_proj_list'));

   }



   // 3-Projectwise Month By Month Work Hours Summary
   public function getProjectwiseTotalWorkHoursSummaryReport(Request $request)
   {


        if(!$request->has('project_id')){
          Session::flash('error','Please Select Project');
          return redirect()->back();
        }
        $fromday = $request->fromdate;
        $today = $request->todate;
        $company = (new CompanyDataService())->findCompanryProfile();
        $monthwithYears =  (new HelperController())->getMonthsInRangeOfDate($fromday, $today);
          $monthList = array();
          $yearList = array();
          $counter = 0;
          foreach ($monthwithYears as $my) {
            $monthList[$counter] = $my['month'];
            $yearList[$counter] =  $my['year'];
            $counter++;
          }
         $records = (new EmployeeAttendanceDataService())->getDateToDateTotalWorkHourseSummary($request->project_id, $fromday, $today, $monthList, $yearList);
         $prepared_by = Auth::user()->name;

         return view('admin.report.employee_attendance.month_by_month_total_work_summary', compact('records', 'company','prepared_by'));


   }



     // 7 Daily Absent Manpower Report Project WIse showDailyAbsentManpowerReportDetails
  public function processAndShowAbsenceEmployeeDetailsOrAttendanceRecordsDetails(Request $request){

    try{

        $dayMonthYear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
        $day = $dayMonthYear[0];
        $month = $dayMonthYear[1];
        $year = $dayMonthYear[2];

        $day_night_shifts = $request->day_night_shift;
       // dd($request->all(), $day_night_shifts );
       if($request->report_type == 2) // absent emp details report
        {
          $absent_manpower_records = (new EmployeeAttendanceDataService())->getTodayAbsentEmployeeDetailsReport($request->project_ids,$day, $month, $year,
          $day_night_shifts);
          $company = (new CompanyDataService())->findCompanryProfile();
          $report_title[0] = $request->date;
          $project_name = "All ";
          if($request->project_id == null){
           }else if(count($request->project_id) == 1){
            $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id[0]);
           }else {
            $project_name = "Multiple Projects ";
           }
          return view('admin.report.employee_attendance.absent_emp_details', compact('absent_manpower_records','report_title', 'company','project_name','report_title'));

        }else if($request->report_type == 1){

          return $this->processAndShowAbsenceEmployeesDayByDayAttendanceDetailsReport($request->project_ids,$request->sponsor_ids,$day_night_shifts,$day, $month, $year);
        }

    }catch(Exception){

    }


  }
   // 7.1  today absent emp attendance date by date report
  private function processAndShowAbsenceEmployeesDayByDayAttendanceDetailsReport($project_ids,$sponsor_ids,$working_shift,$day,$month,$year){

          $numberOfDaysInThisMonth = (new HelperController())->getNumberOfDaysInMonthAndYear($month,$year);
          $fromday = 1;
          $count = 0;
          $day_name_in_month = array();
          $attendent_emp_list = array();
          if(is_null($project_ids)){
             $project_ids = (new ProjectDataService())->getAllActiveProjectIDs();
          }
          if(is_null($sponsor_ids)){
            $sponsor_ids = (new EmployeeRelatedDataService())->getAllActiveSponsorIdAsArray();
          }
          if(is_null($working_shift)){
            $working_shift = [0,1];
          }

          $directEmp  = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseAreNotPresentInAttendanceRecordByProjectSponsorWorkingshiftDayMonthYear( $project_ids,  $sponsor_ids ,$working_shift, $day, $month, $year);
          $working_proj_list = (new EmployeeAttendanceDataService())->getEmployeeWorkingListOfProjectWithColorCodeByProjectSponsorShiftDayMonthYear($project_ids,$sponsor_ids,$working_shift,$day,$month,$year);

          ini_set('max_execution_time', 900);
          foreach ($directEmp as $emp) {

                $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecordsWithProjecColorCodeForShowingAllMonthReport($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);

                if(count($Attendence) >0){
                    $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                    $total_daily_work_hours = 0;
                    $total_over_time = 0;
                    $total_working_days = 0;
                    foreach($Attendence as $attend) {
                      $attendday = (int) $attend->emp_io_date;
                      $allAttend[$attendday] = $attend;
                    }
                    $emp->attendace_records = $allAttend;

                    $emp->last_working_project_name = (new ProjectDataService())->getProjectNameByProjectId($Attendence[(count($Attendence)-1)]->proj_id);
                    $emp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                    $emp->total_daily_work_hours =   (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
                    $emp->total_working_days = count($Attendence);
                    $attendent_emp_list[$count] = $emp;
                    $count++;

                }else {
                    $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
                    $total_daily_work_hours = 0;
                    $total_over_time = 0;
                    $total_working_days = 0;
                    $emp->attendace_records = $allAttend;
                    $emp->last_working_project_name = '';
                    $emp->total_over_time = 0;
                    $emp->total_daily_work_hours = 0;
                    $emp->total_working_days = 0;
                    $attendent_emp_list[$count] = $emp;
                    $count++;
                }
          }
          $daily_total_hours_array = array_fill(1, $numberOfDaysInThisMonth + 1, 0);
          list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($month,$year);
          $prepared_by = Auth::user()->name;
          $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
          $monthName = (new HelperController())->getMonthName($month);
          $company = (new CompanyDataService())->findCompanryProfile();
          $working_shift = '';

          return view('admin.report.employee_attendance.multi_emp_id_datewise_attendance', compact('day_name_in_month','numberOfDaysInThisMonth', 'holidayArray' ,  'company',
          'attendent_emp_list', 'monthName', 'year', 'daily_total_hours_array', 'totalHolidays','working_shift','prepared_by','working_proj_list'));
  }

   // 8 Monthly Absent Manpower Report Project WIse
public function showMonthlyAbsentManpowerReportDetails(Request $request){

    $month =  $request->month_id;
    $year = $request->year_id;

   // dd($request->all());
    if($request->report_type == 1){
       // attendance date by date report
      $emp_list = (new EmployeeAttendanceDataService())->getListOfEmployeesThoseAreNotPresentMinimumDaysInAMonthAttendanceReport($request->project_id, $month, $year, $request->working_day);
      $dayofthis_month =  (new HelperController())->getNumberOfDaysInMonthAndYear($month,$year);
      //dd($dayofthis_month);
      if((new HelperController())->getCurrentMonthIntValue() == (int) $month){
        $dayofthis_month = (new HelperController())->getTodayDayFromCurrentMonth();
      }

      return $this->showAbsetEmployeesAttendanceReport($emp_list,1,$dayofthis_month,$month,$year,$request->project_id);

    }else if($request->report_type == 2){
        // Rrregular attendnace Employee  date by date report
      $absent_manpower_records = (new EmployeeAttendanceDataService())->getMonthlyAbsentEmployeeRerpot($request->project_id, $month, $year, $request->working_day,$request->join_date);
      $company = (new CompanyDataService())->findCompanryProfile();
      $report_title[0] = (new HelperController())->getMonthName($month);
      $report_title[1] = $year;
      $report_title[2] = $request->working_day;
      $project_name = "All ";
      if($request->project_id != null){
        $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($request->project_id);
      }

      return view('admin.report.employee_attendance.emp_absent_monthly_report', compact('absent_manpower_records','report_title', 'company','project_name'));


    }


}

  //  Rrregular attendnace Employee  date by date report
  private function showAbsetEmployeesAttendanceReport($emp_list,$fromday,$numberOfDaysInThisMonth, $month, $year,$project_id){

    $day_name_in_month = array();
    $monthName = (new HelperController())->getMonthName($month);
    $day_name_in_month = (new HelperController())->getAllDaysNameInMonth($month,$year);
    $company = (new CompanyDataService())->findCompanryProfile();
    $projectName = (new EmployeeRelatedDataService())->getProjectNameByProjectId($project_id);
    $sponserName = '';

    $count = 0;
    $attendent_emp_list = array();
      for($i = 0; $i < count($emp_list); $i++){
        $emp = $emp_list[$i];
        $Attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecords($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
            if(count($Attendence) >0){
              $allAttend = array_fill(0, $numberOfDaysInThisMonth + 2, null);
              foreach($Attendence as $attend) {
                $allAttend[(int) $attend->emp_io_date] = $attend;
              }
              $emp->attendace_records = $allAttend;
              $emp->total_over_time =  (new EmployeeAttendanceDataService())->countAnEmployeeTotalOverTimeHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
              $emp->total_daily_work_hours = $emp->total_over_time + (new EmployeeAttendanceDataService())->countAnEmployeeTotalWorkingHoursFromDateToDate($emp->emp_auto_id,$fromday,$numberOfDaysInThisMonth, $month, $year);
              $emp->total_working_days =  count($Attendence);
              $attendent_emp_list[$count] = $emp;
              $count++;
            }
      }

      $daily_total_hours_array = array_fill(1, $numberOfDaysInThisMonth + 1, 0);
      list($totalHolidays,$holidayArray) = (new HelperController())->countTotalHolidayInThisMonth($month,$year);
      $prepared_by = Auth::user()->name;
      $working_shift = '2';
      $page_limit = $count;
      $page_offset = 0;
      return view('admin.report.employee_attendance.project_date_to_date_attend_report_preview', compact('numberOfDaysInThisMonth','day_name_in_month', 'holidayArray',
       'sponserName', 'projectName', 'company', 'attendent_emp_list', 'monthName', 'year', 'daily_total_hours_array', 'totalHolidays','working_shift','prepared_by','page_limit'
       ,'page_offset'));

  }
























    /*
    |--------------------------------------------------------------------------
    |   AJAX REQUEST METHODS
    |--------------------------------------------------------------------------
    */



  // Search Employee list For Attendance IN
  public function getListOfEmployeeWorkingInProjectForAttendanceIN(Request $request)
  {
    try{
        $daymonthyear = (new HelperController())->getDayMonthAndYearFromDateValue($request->search_date);
        $attendentedEmpLst = (new EmployeeAttendanceDataService())->getTodayAlreadyAttendedEmployeeIdListByDayMonthAndYear($daymonthyear[0],$daymonthyear[1],$daymonthyear[2]);
        $emplist = (new EmployeeDataService())->getEmployeeLisForDailyAttendanceINByProjectId($request->project_id, 1,$attendentedEmpLst,$request->isNightShift);
        if (count($emplist) > 0) {
          return response()->json([ 'status' =>200, 'success'=>true, "data" => $emplist]);
        } else {
          return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'Employee Not Found']);
        }
    }catch(Exception $ex){
      return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'System Error Found, Reload Page & Try Again']);
    }

  }


  // Searching ALREADY Attendance IN  Records for   Attendance Out
  public function getAttendanceINAllEmployeeListForAttendaceOutAjaxRequest(Request $request)
  {
        try{
              $daymonthyear = (new HelperController())->getDayMonthAndYearFromDateValue($request->date);
              $isNightShift = $request->isNightShift;
              $attendentedEmpLst = (new EmployeeAttendanceDataService())->getAttendanceINEmployeeListForAttendanceOutByProjectIdDayMonthYear($request->proj_name,
              $daymonthyear[0],$daymonthyear[1],$daymonthyear[2],$request->isNightShift);

              if (count($attendentedEmpLst) > 0) {
                return response()->json([ 'status' =>200, 'success'=>true, "data" => $attendentedEmpLst]);
              } else {
                return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'Employee Not Found']);
              }
        }catch(Exception $ex){
          return response()->json(['status' =>404, 'success'=>false,'error' => "error",'message'=>'System Error Found, Reload Page & Try Again']);
        }
  }





}
