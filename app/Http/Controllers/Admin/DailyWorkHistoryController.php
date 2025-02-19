<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\EmployeeMultiProjectWorkHistoryController;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\Admin\InOut\EmployeeInOutController;
use App\Http\Controllers\Admin\Permission\SalaryProcessPermissionController;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Decade;
use App\Imports\ImportMonthlyWorkRecord;
use Illuminate\Http\Request;
use App\Models\MonthlyWorkHistory;
use App\Models\EmployeeInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DailyWorkHistoryController extends Controller
{


    function __construct(){
        $this->middleware('permission:month-work-history',['only' => ['index','store','getMultipleEmpMonthlyRecordInsertForm','insertMultipleEmployeeMonthlyRecord']]);
        $this->middleware('permission:month-work-report',['only' => ['getEmployeMonthlyWorkHistoryRecordReportUI']]);
    }

  /*

  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
    public function getAll()
    {
        return $all = MonthlyWorkHistory::where('status', 1)->orderBy('month_work_id', 'desc')->take(100)->get();
    }
    public function getMonthlyWorkRecords($month, $year)
    {

        $previousMonth = $month;
        if ($month == 1) {
            $previousMonth = 12;
        } else {
            $previousMonth = $month - 1;
        }

        return $all = MonthlyWorkHistory::where('month_id', $month)->orWhere('month_id', $previousMonth)->where('status', 1)->orderBy('month_work_id', 'desc')->take(20)->get();
    }

    public function getfindId($id)
    {
        return $find = MonthlyWorkHistory::with('employee')->where('status', 1)->where('month_work_id', $id)->firstOrFail();
    }

    public function deleteIdWiseMonthlyWorkHistory($month_work_id)
    {
        return $find = MonthlyWorkHistory::where('status', 1)->where('month_work_id', $month_work_id)->delete();
    }


  // Insert Monthly WOrk Record for Single Employee
  public function store(Request $request)
  {
      $hasAccessPermission = (new SalaryProcessPermissionController())->checkSalaryProcessPermission($request->month, $request->year);

      if ($hasAccessPermission) {
          if ($request->total_work_day == null || $request->work_hours == null || $request->total_work_day == 0) {
              Session::flash('error_null_0', 'value');
              return Redirect()->back();
          }

          $month = (int) $request->month;
          $year = (int) $request->year;
          $emp_auto_id = $request->emp_id;
          $project_id = (int) $request->project_id;

              if ( (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp_auto_id, $month, $year) != null) {
                Session::flash('duplicate_data_error', 'This Month Work Record Exist');
                return Redirect()->back();
             }
            else {

              $totalHourTime =  $request->work_hours == null ? 0 : $request->work_hours;

              $startDate = Carbon::now();
              $endDate = Carbon::now()->addDay($request->total_work_day);
              (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd(
                  $emp_auto_id,
                  $month,
                  $year,
                  $totalHourTime,
                  $request->overtime,
                  $request->total_work_day,
                  $project_id
              );
              $empInOutConObj = new EmployeeInOutController();
              $empInOutConObj->insrtEmpMultiProjectWorkRecord($startDate, $endDate, $emp_auto_id, $project_id, $month, $year, $request->total_work_day, $totalHourTime, $request->overtime);

              Session::flash('success', 'Successfully Saved Data');
              return Redirect()->back();
          }
      } else {
          Session::flash('error_date', 'Permission Date over!');
          return redirect()->back();
      }
  }



  public function insertMultipleEmployeeMonthlyRecord($emp_auto_id,$year, $month,$total_work_hours,$over_time,$total_work_day,$project_id) {

    if ( (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($emp_auto_id, $month, $year) != null) {
        return false;
    }else {

        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDay($total_work_day);

        (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd(
          $emp_auto_id,
          $month,
          $year,
          $total_work_hours,
          $over_time,
          $total_work_day,
          $project_id
        );
        $empInOutConObj = new EmployeeInOutController();
        $empInOutConObj->insrtEmpMultiProjectWorkRecord(
          $startDate,
          $endDate,
          $emp_auto_id,
          $project_id,
          $month,
          $year,
          $total_work_day,
          $total_work_hours,
          $over_time
        );

    }

  }

// AJAX Request Response
  public function projectWiseEmployeeListRequestForMultipleEmpWorkRecordInsert(Request $request)
  {

    $project_id = $request->project_id;
    $year = $request->year;
    $month = $request->month;

    $emplist = (new EmployeeDataService())->getAllEmployeeInfoWithSalaryDetailsThoseAreNotInMonthlyWorkRecords($project_id, 1, $year, $month);
    if (count($emplist) > 0) {
      return response()->json(["entryList" => $emplist]);
    } else {
      return response()->json(['error' => "Data Not Found!"]);
    }
  }

  public function getMultipleEmpMonthlyRecordInsertForm()
  {
    $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
    $months = (new CompanyDataService())->getAllMonth();
    return view('admin.month-work.multiple-employe-montly-record-add', compact('projects', 'months'));
  }

  // multiple employee work record add , this function will be removed now off in server
//   public function multiEmployeMonthRecordInsertFormSubmit(Request $request)
//   {

//       $hasAccessPermission = (new SalaryProcessPermissionController())->checkSalaryProcessPermission($request->month, $request->year);

//       if (!$hasAccessPermission) {
//           Session::flash('error', 'Access Permission Denied');
//           return Redirect()->back();
//       }
//       $emp_list = $request->emp_auto_id;
//       $month = $request->month;
//       $year = $request->year;


//     foreach ($emp_list as $empid) {
//         foreach ($emp_list as $empid1) {
//             foreach ($emp_list as $empid2) {
//                 foreach ($emp_list as $empid3) {

//                 }
//             }
//         }
//     }
//       $counter = 0;
//       foreach ($emp_list as $emp_auto_id) {

//           if ($request->has('checkbox_' . $emp_auto_id)) {

//               $counter++;
//               $total_work_hours = $request->get('total_hours_' . $emp_auto_id);
//               $over_time = $request->get('ot_hours_' . $emp_auto_id);
//               $total_work_day = $request->get('total_day_' . $emp_auto_id);
//               $project_id = $request->get('project_id_' . $emp_auto_id);

//               if ($total_work_hours == null || $total_work_hours == "") {
//                   $total_work_hours = 0;
//               } else if ($over_time == null || $over_time == "") {
//                   $over_time = 0;
//               } else if ($total_work_day == null || $total_work_day == "") {
//                   $total_work_day = 0;
//               }
//               if($total_work_day > 0){
//                   (new EmployeeAttendanceDataService())->insertAnEmployeeMonthlyWorkRecordDetailsInformation($emp_auto_id, $month, $year,$project_id,$total_work_hours,$over_time, $total_work_day,null,null,Auth::user()->branch_office_id);
//               }

//           }
//       }
//           if ($counter > 0 ) {
//               Session::flash('success', 'Successfully Saved Data');
//               return Redirect()->back();
//           } else {
//               Session::flash('error', 'Data Error');
//               return Redirect()->back();
//           }
//   }

    public function update(Request $request)
    {


        $this->validate($request, [
            'emp_id' => 'required',
            'total_work_day' => 'required|integer',
        ], []);

        if ($request->total_work_day == null || $request->work_hours == null || $request->total_work_day == 0 || $request->work_hours == 0) {
            Session::flash('error_null_0', 'value');
            return Redirect()->back();
        }

        $monhWorkConObj = new MonthlyWorkHistoryController();
        $update = $monhWorkConObj->monthliWorkHistRecrdUpdate($request->id, $request->month, $request->year, $request->work_hours, $request->overtime, $request->total_work_day);

        if ($update) {
            Session::flash('success_update', 'value');
            return Redirect()->route('add-daily-work');
        } else {
            Session::flash('error', 'value');
            return Redirect()->back();
        }
    }

    /*


    |--------------------------------------------------------------------------
  |  AJAX OPERATION
  |--------------------------------------------------------------------------
  */
    // salary Project Emp ID input
    public function autocomplete(Request $request)
    {
        $data = (new EmployeeDataService())->getEmployeeByEmpIdWithLikeQuery($request->empId, 1);
        return view('admin.month-work.search', compact('data'));
    }

    public function conditionAutocomplete(Request $request)
    {
        $data = (new EmployeeDataService())->getEmployeeByEmpIdAndEmpTypeWithLikeQuery($request->empId, 1, 2);
        return view('admin.month-work.search', compact('data'));
    }

    public function findDirectEmployee(Request $request)
    {
        $data = (new EmployeeDataService())->getEmployeeByEmpIdAndEmpTypeWithLikeQuery($request->empId, 1, 1);
        return view('admin.month-work.search2', compact('data'));
    }

    public function findEmployeeTypeId(Request $request)
    {
        $findType = EmployeeInfo::where('emp_type_id', $request->emp_type_id)->where('job_status', 1)->first();
        return json_encode($findType);
    }


    public function deleteDailyWorkHistory($id)
    {

        $empMonthWorkHistory = $this->getfindId($id);
        $emp_id = $empMonthWorkHistory->emp_id;
        $month_id = $empMonthWorkHistory->month_id;
        $year_id = $empMonthWorkHistory->year_id;

        $month_work_id = $empMonthWorkHistory->month_work_id;

        $this->deleteIdWiseMonthlyWorkHistory($month_work_id);

        $empMultiProjConObj = new EmployeeMultiProjectWorkHistoryController();
        $empMultiProjConObj->deleteMonthAndYearWiseAnEmpMultiprojectWorkHistory($emp_id, $month_id, $year_id);

        return response()->json();
    }



    /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
    public function index()
    {
        $emp_type_id = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $currentMonth =  Carbon::now()->format('m');
        $currentYear =  Carbon::now()->format('y');
        $all = $this->getMonthlyWorkRecords((int)$currentMonth, (int)$currentYear);

        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $month = (new CompanyDataService())->getAllMonth();
        return view('admin.month-work.create', compact('all', 'emp_type_id', 'month', 'currentMonth', 'projects'));
    }


    public function edit($id)
    {

        $month = (new CompanyDataService())->getAllMonth();
        $currentMonth =  Carbon::now()->format('m');

        $edit = $this->getfindId($id);
        return view('admin.month-work.edit', compact('edit', 'currentMonth', 'month'));
    }




    // Udpate EMployee Monthly WOrk Record Details From Multiproject Work Menu

    public function searchAnEmployeeMonthWorkRecordDetails(Request $request)
    {


        $empInfo = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpId($request->emp_id);
        $months = (new CompanyDataService())->getAllMonth();
        $projects =  (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);

        if ($empInfo) {
            $monthWorkRecord = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($empInfo->emp_auto_id, $request->month, $request->year);

            if ($monthWorkRecord == null) {
                return response()->json(["status" => "error", "error" => "Employee Work Record Not Found"]);
            }
            return response()->json(["monthWorkRecord" => $monthWorkRecord, 'emp' => $empInfo, "month" => $months, "projects" => $projects, "error" => null]);
        }
        return response()->json(["status" => "error", "error" => "Work Record Not Found"]);
    }

     // multi project work record update using modal  this method transfer to inoutcontroller

    // public function updateAnEmployeeMultipleProjectWorkRecordRequest(Request $request){
    //     $findEmployee =  (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($request->modal_emp_auto_id);

    //     if ($findEmployee == null) {
    //            Session::flash('error', 'Employee Not Found Failed');
    //           return Redirect()->back();
    //     }
    //     $salary_is_paid = (new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid( $request->modal_emp_auto_id, $request->modal_month, $request->modal_year,);

    //     if($salary_is_paid){
    //         Session::flash('error', 'This Month Salary Already Paid , Update Not Possible');
    //         return Redirect()->back();
    //     }else if ($request->modal_total_hour == null || $request->modal_total_hour == "") {
    //         Session::flash('error', 'Data Erro, Updat Operation Failed');
    //         return Redirect()->back();
    //     } else  if ($request->modal_total_overtime == null || $request->modal_total_overtime == "") {
    //         Session::flash('error', 'Data Erro, Updat Operation Failed');
    //         return Redirect()->back();
    //     } else if ($request->modal_total_day == null || $request->modal_total_day == "") {
    //         Session::flash('error', 'Data Erro, Updat Operation Failed');
    //         return Redirect()->back();
    //     }

    //      $existing_record = (new EmployeeAttendanceDataService())->findAnEmpMultiProjectWorkRecordByRecordAutoId($request->modal_empwh_auto_id);
    //      $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
    //      $update_days = $record_total->total_days + $request->modal_total_day - $existing_record->total_day;
    //      $update_hours = $record_total->total_hour + $request->modal_total_hour - $existing_record->total_hour;

    //      if($update_days > 31 ||  $update_hours > 350){
    //         Session::flash('error', 'Total Working Days or  Hours Invalid');
    //         return Redirect()->back();
    //      }

    //         $update =  (new EmployeeAttendanceDataService())->updateAnEmployeeMultipleProjectWorkRecordWithAllColum(
    //         $request->modal_empwh_auto_id, $request->modal_project_name, $request->modal_month, $request->modal_year, $request->modal_total_day,
    //         $request->modal_total_hour, $request->modal_total_overtime,$request->modal_start_date,$request->modal_end_date,Auth::user()->id);

    //         // (new EmployeeAttendanceDataService())->calculateAnEmployeeMontlyTotalWorkFromMultiProjectWorkAndUpdateMonthlyRecord(
    //         //     $request->modal_emp_auto_id,
    //         //     $request->modal_month,
    //         //     $request->modal_year,
    //         //     $request->modal_project_name,
    //         //     Auth::user()->id,
    //         // );

    //         $record_total =(new EmployeeAttendanceDataService())->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
    //         $monthlyWorkHist = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyWorkRecord($request->modal_emp_auto_id, $request->modal_month, $request->modal_year);
    //         if ($monthlyWorkHist != null) {
    //           (new EmployeeAttendanceDataService())->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $request->modal_project_name);
    //         } else {
    //           (new EmployeeAttendanceDataService())->saveAnEmployeeMonthlyWorkRecrd($request->modal_emp_auto_id, $request->modal_month, $request->modal_year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$request->modal_project_name);
    //         }

    //         Session::flash('success', 'Successfully Updated');
    //         return Redirect()->back();

    // }



    /* ================================================================
         Upload Employee Monthly Work Record Excel File
       ================================================================
    */

// Upload Employee Work Records Excel File
public function checkUploadedFileProperties($extension, $fileSize)
{
        $valid_extension = array("csv", "xlsx"); //Only want csv and excel files
        $maxFileSize = 5242888; // Uploaded file size limit is 5mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
                return true;
            } else {
                 return false;
            }
        } else {
           return false;
        }
}
// Import Work Record Excel File to Temporary Table
public function importEmployeeMonthlyWorkRecordsFromExcel(Request $request)
{
            if ($request->file && $request->month) {

                    $file = $request->file;
                    $project_id = $request->proj_name;
                    $month = $request->month;
                    $year = $request->year;
                    $extension = $file->getClientOriginalExtension(); //Get extension of uploaded file
                    $fileSize = $file->getSize(); //Get size of uploaded file in bytes
                    if($this->checkUploadedFileProperties($extension, $fileSize)){

                                $import = new ImportMonthlyWorkRecord($project_id,$month,$year);
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
                            'message' =>  " Invalid File Format Or Large file size"
                        ]);
                    }

            } else {
                return response()->json([
                    'status' => 403,
                    'success'=> false,
                    'message' =>  " file not found"
                ]);
            }
}
// Submit Imported Excell Data To Final Table
public function submitEmployeeMonthlyWorkRecordsImportFromExcel(Request $request){

    try{

        $result = (new EmployeeAttendanceDataService())->getEmployeeWorkRecordImportedExcellDataFromTable();
        $startDate = Carbon::now();
        DB::beginTransaction();
        foreach ($result as $arecord){
            $endDate = Carbon::now()->addDay($arecord->working_days);
            $isExist = (new EmployeeAttendanceDataService())->checkAnEmployeeThisProjectWorkRecordsIsExist($arecord->emp_auto_id,$arecord->month_id,$arecord->year_id,$arecord->project_id);
            if(!$isExist)
            (new EmployeeAttendanceDataService())->insertAnEmployeeMonthlyWorkRecordDetailsInformation($arecord->emp_auto_id,$arecord->month_id,$arecord->year_id,$arecord->project_id,
            $arecord->basic_hours,$arecord->over_time,$arecord->working_days,$startDate,$endDate,Auth::user()->branch_office_id);
        }
        DB::commit();
        // remove all records from table
        (new EmployeeAttendanceDataService())->deleteEmployeeWorkRecordImportedExcellDataFromTable();
        return response()->json([
            'status' =>  200,
            'success' => true,
            'message' => 'Successfully Uploaded'
        ]);
    }catch(Exception $ex){
        DB::rollBack();
        return response()->json([
            'error' =>  "Data Upload Failed",
            'status' => 500,
            'success' => false,
        ]);
    }

}


    /* ================================================================
         Monthly Work Records Report
       ================================================================
    */
    // User Interface for Monthly Work Record Report Processing
    public function getEmployeMonthlyWorkHistoryRecordReportUI()
    {
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $month = (new CompanyDataService())->getAllMonth();
        $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        $emplyoyeeStatus = (new HelperController())->getEmployeeStatus();
        return view('admin.month-work.report.month_work_report_process_ui', compact('projects', 'month', 'sponser', 'emplyoyeeStatus'));
    }

    /* +++++++++++++++ Project and sponser wise month and year base work records report +++++++++++++++ */
    public function getEmployeMonthlyWorkHistoryProcess(Request $request)
    {
       // dd(100);
        try{

            $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);

            $year = (int) $request->year;
            $month = (int) $request->month;
            $project_id = (int) $request->proj_id;
            $sponsor_id = (int) $request->SponsId;

            $report_title[0]  = (new ProjectDataService())->getProjectNameByProjectId($project_id);
            $report_title[1]  = (new HelperController())->getMonthName($month);
            $report_title[2]  = $year;

            if($request->data_source == 1){
                // monthly work record report
                $records =  (new EmployeeAttendanceDataService())->getEmployeeMonthlyWorkRecordsReport($project_id,$sponsor_id,$month,$year);
                return view('admin.month-work.report.emp_month_work_report', compact('records', 'company', 'report_title'));
            }
            else if($request->data_source == 2){
                // work record from multi project records
                $sponsor_id =  $sponsor_id == 0 ? null: $sponsor_id;
                $work_records =  (new EmployeeAttendanceDataService())->getEmployeesMultiProectWorkRecordsByProjectIdSponsorIdMonthYear($project_id,$sponsor_id,$month,$year);
                return view('admin.month-work.report.emp_multi_project_work_record_report', compact('work_records', 'company', 'report_title'));

            }
            else if($request->data_source == 3){
                    // multiproject work records from attendance inout
                $emp_ids = (new EmployeeDataService())->getAllEmployeesIdAsArrayInTheProject($project_id);
                $inout_summary_records =  (new EmployeeAttendanceDataService())->getEmployeesAttendanceInOutSummaryOfAMonthByProjectId($project_id,$month,$year);
                return view('admin.month-work.report.emp_attendance_inout_summary_report', compact('inout_summary_records', 'company', 'report_title'));

            }

        }catch(Exception $ex){
            return "Data Validation Error";
        }

    }

    // EMPLOYEE THOSE ARE NOT IN WORK RECROD REPORT
    public function processEmployeNotPresentInMonthlyWorkHistory(Request $request)
    {

        $company = (new CompanyDataService())->findCompanryProfile();
        $month = $request->month;

        $project_id = $request->proj_id;
        $sponsor_id = $request->SponsId;
        $emp_status_id = $request->emp_status_id;
        $year = $request->year;

        $report_title[0]  = (new ProjectDataService())->getProjectNameByProjectId($project_id);
        $report_title[1]  = (new HelperController())->getMonthName($month);
        $report_title[2]  = $year;
        $records =  (new EmployeeAttendanceDataService())->getEmployeesThoseAreNotInMonthlyWorkRecords($project_id,$sponsor_id,$emp_status_id,$month,$year);

        return view('admin.month-work.report.emps_those_not_in_work_record', compact('records', 'company', 'report_title'));

    }


    public function processAllEmployeMonthlyWorkStatus(Request $request)
    {
        $year = (new HelperController())->getYear();
        $project = "All";
        $sponser = 'All';
        $month = (new HelperController())->getMonthName($request->month);
        $company = (new CompanyDataService())->findCompanryProfile();
        $totalActiveEmployee = (new  EmployeeDataService())->countTotalEmployees(1);
        $totalEmployee = (new EmployeeDataService())->countTotalEmployees(0);
        $totalWokringEmp = (new EmployeeAttendanceDataService())->countTotalWorkingEmployees($request->month, $year);

        return view('admin.month-work.report.employee-work-status-summary', compact('company', 'project', 'month', 'year', 'totalActiveEmployee', 'totalEmployee', 'totalWokringEmp'));
    }


    /* +++++++++++++++ Project Wise Month Work History +++++++++++++++ */

    public function displayEmployeMonthlyWorkHistoryReport()
    {

        $emp_type_id = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $all = $this->getAll();
        $month = (new CompanyDataService())->getAllMonth();
        $currentMonth =  Carbon::now()->format('m');
        return view('admin.month-work.report-employe-monthwork', compact('all', 'emp_type_id', 'month', 'currentMonth'));
    }





}
