<?php

namespace App\Http\Controllers\DataServices;


use App\Models\EmployeeInfo;
use App\Models\EmployeeInOut;
use App\Models\EmployeeMultiProjectWorkHistory;
use App\Models\AttendanceApprovalRecord;
use App\Models\AttendanceInoutPermission;
use App\Models\MonthlyWorkHistory;
use App\Models\DailyAttendanceSummary;
use App\Enums\MonthlyAttendanceStatusEnum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;



class EmployeeAttendanceDataService
{



    /*
     ==========================================================================
     ===================Import Employee Montly WOrk Records Excell File========
     ==========================================================================
    */
     // Insert Data To Temporary Table
    public function insertEmployeeWorkRecordImportedExcellData($emp_auto_id,$month,$year,$project_id,$basic_hours,$over_time,$working_days,$insert_by){
        DB::insert('call import_emp_work_record_excell_data1(?,?,?,?,?,?,?,?)',array($emp_auto_id,$month,$year,$project_id,$basic_hours,$over_time,$working_days,$insert_by));
    }
    //Get Data To Temporary Table
    public function getEmployeeWorkRecordImportedExcellDataFromTable(){
       return DB::select('call get_imported_emp_work_record_excell_data1');
    }
    // Remove  Data To Temporary Table
    public function deleteEmployeeWorkRecordImportedExcellDataFromTable(){
        DB::delete('call delete_imported_emp_work_record_excell_data1');
    }


    /*
     ==========================================================================
     ============================= Employee Attendance Records Monthly ========
     ==========================================================================
    */


    public function saveAnEmployeeMonthlyWorkRecrd($emp_auto_id, $month, $year, $total_works_hours, $total_overtime, $total_days, $project_id)
    {

        return MonthlyWorkHistory::insertGetId([
            'emp_id' => $emp_auto_id,
            'month_id' => $month,
            'year_id' => $year,
            'work_project_id' => $project_id,
            'total_hours' => $total_works_hours,
            'overtime' =>  $total_overtime,
            'total_work_day' =>  $total_days,
            'branch_office_id' => Auth::user()->branch_office_id,
            'entered_id' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);
    }


    public function updateEmployeeMonthlyWorkRecord($month_work_id, $total_works_hours, $total_overtime, $total_days, $project_id)
    {
        return   MonthlyWorkHistory::where('month_work_id', $month_work_id)->update([
            'total_hours' => $total_works_hours,
            'overtime' =>  $total_overtime,
            'total_work_day' =>  $total_days,
            'work_project_id' => $project_id,
            'entered_id' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);
    }

    public function updateAnEmployeeMonthlyWorkHoursOTAndDays($month_work_auto_id, $total_works_hours, $total_overtime, $total_days,$update_by)
    {
        return   MonthlyWorkHistory::where('month_work_id', $month_work_auto_id)->update([
            'total_hours' => $total_works_hours,
            'overtime' =>  $total_overtime,
            'total_work_day' =>  $total_days,
            'entered_id' => $update_by,
            'updated_at' => Carbon::now(),
         ]);
    }

    public function getAnEmployeeMonthlyWorkRecord($emp_auto_id, $month, $year)
    {
        return  MonthlyWorkHistory::where('emp_id', $emp_auto_id)->where('month_id', $month)->where('year_id', $year)->first();
    }

    public function getEmployeesAutoIdsThoseAreInMonthlyWorkRecordForAdvanceProcessing($project_id,$sponsor_ids, $month, $year)
    {
       return MonthlyWorkHistory::select('emp_id')
       ->where('work_project_id', $project_id)->where('month_id', $month)->where('year_id', $year)
       ->whereIn('employee_infos.sponsor_id',$sponsor_ids)
       ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'monthly_work_histories.emp_id')
       ->get();
    }


    public function deleteAnEmployeeMonthlyWorkRecordByEmpAutoIdMonthAndYear($emp_auto_id, $month, $year)
    {
      return  MonthlyWorkHistory::where('status', 1)
            ->where('emp_id', $emp_auto_id)
            ->where('month_id', $month)
            ->where('year_id', $year)
            ->delete();
    }


    public function checkAnEmployeeMonthlyWorkRecordIsExistForTimeSheetUpload($emp_auto_id, $month, $year)
    {
        return  (MonthlyWorkHistory::where('emp_id', $emp_auto_id)->where('month_id', $month)->where('year_id', $year)->count()) > 0 ? true : false;
    }

    public function countTotalWorkingEmployees($month, $year)
    {
        return MonthlyWorkHistory::where('month_id', $month)->Where('year_id', $year)->count();
    }


    /* ================================================================
         Monthly Work Records Report
       ================================================================
    */


     public function getEmployeeMonthlyWorkRecordsReport($project_id,$sponsor_id,$month_id,$year_id){
          return  DB::select('CALL getMonthlyWorkRecords(?,?,?,?)',array($project_id, $sponsor_id,$year_id,$month_id));
     }


     public function getEmployeesThoseAreNotInMonthlyWorkRecords($projectId,$sponserId,$emp_status_id,$month, $year){

        $list =  $this->getListOfEmployeeAutoIdExistInMultiProectWorkRecord($month, $year);
        if ($projectId == 0 && $sponserId == 0) {
          return EmployeeInfo::leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->whereNotIn('employee_infos.emp_auto_id', $list)
                ->where('employee_infos.job_status',$emp_status_id)
                ->get();

        } else if ( $projectId == 0 && $sponserId > 0) {
            return EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->whereNotIn('employee_infos.emp_auto_id', $list)
                ->where('employee_infos.job_status',$emp_status_id)
                ->get();
        } else if ($projectId > 0 && $sponserId == 0) {
            return EmployeeInfo::where("employee_infos.project_id", $projectId)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->whereNotIn('employee_infos.emp_auto_id', $list)
                ->where('employee_infos.job_status',$emp_status_id)
                ->get();
        } else  {
            return EmployeeInfo::where("employee_infos.sponsor_id", $sponserId)
                ->where("employee_infos.project_id", $projectId)
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('countries', 'employee_infos.country_id', '=', 'countries.id')
                ->leftjoin('job_statuses', 'employee_infos.job_status', '=', 'job_statuses.id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->whereNotIn('employee_infos.emp_auto_id', $list)
                ->where('employee_infos.job_status',$emp_status_id)
                ->get();
        }
     }


    /* ================================================================
         Multi Project and Monthly Work Record Database Opereation
       ================================================================
    */

    public function insertAnEmployeeMonthlyWorkRecordDetailsInformation($emp_auto_id, $month, $year,$project_id,$total_hours,$total_ot,$total_day,$start_date,$end_date,$branch_office_id=1){

        $multiProjWorkRecord =  $this->getAnEmployeeMultipleProjectWorkRecords($emp_auto_id, $month, $year,$project_id);

        if ($multiProjWorkRecord != null) {
            $updatedTotal_work_day = $multiProjWorkRecord->total_day + $total_day;
            $updatedTotal_work_hours = $multiProjWorkRecord->total_hour + $total_hours;
            $updatedOver_time = $multiProjWorkRecord->total_overtime + $total_ot;
            $this->updateEmployeeMultipleProjectWorkRecord( $multiProjWorkRecord->empwh_auto_id,$updatedTotal_work_hours,$updatedOver_time, $updatedTotal_work_day );
        } else {
            $this->saveAnEmployeeMultipleProjectWorkRecrd( $emp_auto_id, $month,  $year,$total_hours,$total_day, $project_id,$total_ot );
        }
        $record_total = $this->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($emp_auto_id, $month, $year);
        $monthlyWorkHist = $this->getAnEmployeeMonthlyWorkRecord($emp_auto_id, $month, $year);
        if ($monthlyWorkHist != null) {

            $this->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $project_id );
        } else {
            $this->saveAnEmployeeMonthlyWorkRecrd($emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$project_id,$branch_office_id);
        }

     }


     public function calculateAnEmployeeMontlyTotalWorkFromMultiProjectWorkAndUpdateMonthlyRecord($emp_auto_id, $month, $year,$project_id,$update_by){

        $record_total = $this->calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($emp_auto_id, $month, $year);
        $monthlyWorkHist = $this->getAnEmployeeMonthlyWorkRecord($emp_auto_id, $month, $year);
        if ($monthlyWorkHist != null) {
            $this->updateEmployeeMonthlyWorkRecord( $monthlyWorkHist->month_work_id,$record_total->total_hours, $record_total->total_over_time,$record_total->total_days, $project_id );
        } else {
            $this->saveAnEmployeeMonthlyWorkRecrd($emp_auto_id,$month,$year, $record_total->total_hours, $record_total->total_over_time, $record_total->total_days,$project_id);
        }

     }

    /*
     ==========================================================================
     ============ Employee Attendance in Multiple Project   ===================
     ==========================================================================
    */

      //  Last 10 Work Record
      public function getMultiProjectWorkRecords($limit)
      {
          return  EmployeeMultiProjectWorkHistory::with('projectName', 'employee')->latest()->take($limit)->get();
      }

    public function saveAnEmployeeMultipleProjectWorkRecrd($emp_auto_id, $month, $year, $total_works_hours, $total_days, $project_id, $total_overtime)
    {
        $insert = EmployeeMultiProjectWorkHistory::insert([
            'emp_id' => $emp_auto_id,
            'project_id' => $project_id,
            'month' => $month,
            'year' => $year,
            'total_day' => $total_days,
            'total_hour' => $total_works_hours,
            'total_overtime' => $total_overtime,
            'branch_office_id' => Auth::user()->branch_office_id,
            'created_at' => Carbon::now()
        ]);
    }


    public function saveAnEmployeeMultipleProjectWorkRecordWithAllColum($startDate, $endDate, $emp_id, $proj_id, $month, $year, $totalDays, $totalHourTime, $totalOverTime)
    {
      return $insert = EmployeeMultiProjectWorkHistory::insertGetId([
        'emp_id' => $emp_id,
        'project_id' => $proj_id,
        'month' => $month,
        'year' => $year,
        'total_day' => $totalDays,
        'total_hour' => $totalHourTime,
        'total_overtime' => $totalOverTime,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'branch_office_id' => Auth::user()->branch_office_id,
        'created_at' => Carbon::now()
      ]);
    }

    public function updateAnEmployeeMultipleProjectWorkRecordWithAllColum($empwh_auto_id, $proj_id, $month, $year, $totalDays, $totalHourTime, $totalOverTime,$startDate, $endDate,$update_by_id)
    {
      return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->update([
        'project_id' => $proj_id,
        'month' => $month,
        'year' => $year,
        'total_day' => $totalDays,
        'total_hour' => $totalHourTime,
        'total_overtime' => $totalOverTime,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'updated_at' => Carbon::now(),
        'update_by_id' => $update_by_id,
      ]);
    }

    public function updateEmployeeMultipleProjectWorkRecord($empwh_auto_id, $total_hour, $total_overtime, $total_day)
    {

        return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->update([
            'total_day' => $total_day,
            'total_hour' =>  $total_hour,
            'total_overtime' => $total_overtime,
            'updated_at' => Carbon::now(),
            'update_by_id' => Auth::user()->id,
        ]);
    }

    public function updateEmployeeMultipleProjectWorkRecordWithProjectName($empwh_auto_id, $total_hour, $total_overtime, $total_day,$project_id,$update_by_id)
    {

        return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->update([
            'total_day' => $total_day,
            'total_hour' =>  $total_hour,
            'total_overtime' => $total_overtime,
            'project_id' => $project_id,
            'updated_at' => Carbon::now(),
            'update_by_id' => $update_by_id,
        ]);
    }


    public function findAnEmpMultiProjectWorkRecordByRecordAutoId($empwh_auto_id)
    {
        return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->first();
    }

    public function checkAnEmployeeThisProjectWorkRecordsIsExist($emp_auto_id, $month, $year, $project_id)
    {
        return  EmployeeMultiProjectWorkHistory::where('emp_id', $emp_auto_id)->where('month', $month)->where('year', $year)->where('project_id', $project_id)->count() == 0 ? false: true;
    }


    public function getAnEmployeeMultipleProjectWorkRecords($emp_auto_id, $month, $year, $project_id)
    {
        return  EmployeeMultiProjectWorkHistory::where('emp_id', $emp_auto_id)->where('month', $month)->where('year', $year)->where('project_id', $project_id)->first();
    }



    public function calculateAnEmployeeMonthlyTotalWorkDataFromMultiProjectWorkRecordByMonthYear($emp_auto_id, $month, $year){
        return EmployeeMultiProjectWorkHistory::select(
            DB::raw('sum(total_hour) as total_hours'),
            DB::raw('sum(total_day) as total_days'),
            DB::raw('sum(total_overtime) as total_over_time'),
        )->where('emp_id', $emp_auto_id)->where('month', $month)->where('year', $year)->first();
    }


    public function searchAnEmployeeMultiprojectWorkRecordsForListViewByEmployeeIdMonthYear($employee_id, $month, $year,$branch_office_id)
    {
        return EmployeeMultiProjectWorkHistory::select('employee_infos.emp_auto_id', 'employee_infos.employee_name', 'employee_infos.employee_id', 'employee_infos.akama_no',
                'employee_infos.isNightShift', 'employee_infos.job_status','project_infos.proj_name','employee_infos.hourly_employee','employee_categories.catg_name',
                'spons_name','users.name as created_by','emp_multi_proj_work_hist.*'
                )
            ->leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'emp_multi_proj_work_hist.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('users', 'emp_multi_proj_work_hist.update_by_id', '=', 'users.id')
            ->where('employee_infos.employee_id', $employee_id)->where('month', $month)
            ->where('emp_multi_proj_work_hist.branch_office_id',$branch_office_id)
            ->where('year', $year)->get();
    }


    public function getAnEmployeeMultiprojectWorkRecordByEmployeeIdForAjaxRespnse($employee_id, $month, $year)
    {
        return EmployeeMultiProjectWorkHistory::leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'emp_multi_proj_work_hist.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->where('employee_infos.employee_id', $employee_id)->where('month', $month)
            ->where('emp_multi_proj_work_hist.branch_office_id',Auth::user()->branch_office_id)
            ->where('year', $year)->get();
    }

// Multiple Wworking Project ALl Records
    public function getAnEmployeeMultiprojectWorkRecordsOnly($emp_auto_id, $month, $year)
    {
        return EmployeeMultiProjectWorkHistory::where('emp_id', $emp_auto_id)->where('month', $month)
            ->where('year', $year)->get();
    }

    public function getEmployeesMultiProectWorkRecordsByProjectIdSponsorIdMonthYear($project_id,$sponsor_id,$month, $year)
    {
        if(is_null($sponsor_id)){
            return EmployeeMultiProjectWorkHistory::leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'emp_multi_proj_work_hist.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->where('emp_multi_proj_work_hist.project_id', $project_id)->where('month', $month)->where('year', $year)->get();
        }else {
            return EmployeeMultiProjectWorkHistory::leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'emp_multi_proj_work_hist.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->where('emp_multi_proj_work_hist.project_id', $project_id)
            ->where('employee_infos.sponsor_id', $sponsor_id)
            ->where('month', $month)->where('year', $year)->get();
        }
    }

    public function searchEmployeeMultiprojectWorkRecord($month, $year)
    {
        return EmployeeMultiProjectWorkHistory::leftjoin('employee_infos', 'emp_multi_proj_work_hist.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'emp_multi_proj_work_hist.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->where('month', $month)->where('year', $year)->get();
    }

    public function getListOfEmployeeAutoIdExistInMultiProectWorkRecord($month, $year)
    {
        return EmployeeMultiProjectWorkHistory::select('emp_id')
            ->where('month', $month)->where('year', $year)->get();
    }

    public function getListOfEmployeeAutoIdWorkInMultiProectByProjectIdMonthYear($project_id,$month, $year)
    {
        return EmployeeMultiProjectWorkHistory::select('emp_id')
        ->where('project_id', $project_id) ->where('month', $month)->where('year', $year)->orderBy('emp_id','ASC')->get();
    }


    public function deleteAnEmpMultiprojectWorkRecordByRecordAutoId($empwh_auto_id)
    {

        return EmployeeMultiProjectWorkHistory::where('empwh_auto_id', $empwh_auto_id)->delete();
    }

    public function countAnEmployeeWorkingProjectThisMonth($emp_auto_id, $month, $year)
    {
        return EmployeeMultiProjectWorkHistory::where('emp_id', $emp_auto_id)->where('month', $month)->where('year', $year)->count();
    }


    /*
     ==========================================================================
     ============================= Employee Attendance IN/OUT =================
     ==========================================================================
    */

    public function insertAnEmployeeAttendanceInInformation($emp_auto_id,$project_id,$working_day,$month,$year,$shift,$entry_in_time,
    $attendance_type,$attendance_status,$insertBy,$attnDate,$insertDate,$branch_office_id)
    {
        if($this->checkAnEmployeeThisDayAlreadyAttendanceINOrNot($emp_auto_id,$working_day,$month,$year) == false)
           return EmployeeInOut::insert([
                'emp_id' => $emp_auto_id,
                'proj_id' => $project_id,
                'emp_io_date' => $working_day,
                'emp_io_month' => $month,
                'emp_io_year' => $year,
                'emp_io_shift' => $shift,
                'emp_io_entry_time' => $entry_in_time,
                'emp_io_out_time' => 0,
                'daily_work_hours' => 0,
                'attendance_type' => $attendance_type,
                'attendance_status' => $attendance_status,
                'create_by_id' => $insertBy,
                'emp_io_entry_date' => $attnDate,
                'created_at' => $insertDate,
                'branch_office_id' => $branch_office_id
            ]);
    }

    public function checkAnEmployeeThisDayAlreadyAttendanceINOrNot($emp_auto_id,$working_day,$month,$year){

        return EmployeeInOut::where('emp_io_date', $working_day)->where('emp_io_month', $month)->where('emp_io_year', $year)->where('emp_id', $emp_auto_id)->get()->count() > 0 ? true:false;

    }

    public function getAnEmployeeAttendanceInOutRecord($emp_io_id)
    {
        return EmployeeInOut::where('emp_io_id', $emp_io_id)->first();
    }

    public function searchEmployeeAttendanceRecord($project_id, $employee_id, $attendance_day, $month, $year)
    {

        return EmployeeInOut::where('employee_infos.employee_id', $employee_id)
            ->where('employee_in_outs.proj_id', $project_id)
            ->where('employee_in_outs.emp_io_date', $attendance_day)
            ->where('employee_in_outs.emp_io_month', $month)
            ->where('employee_in_outs.emp_io_year', $year)
            ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
            ->first();
    }

    public function getTodayAlreadyAttendedEmployeeIdListByDayMonthAndYear($attendance_day, $month, $year)
    {
        return EmployeeInOut::select('emp_id')
            ->where('employee_in_outs.emp_io_date', $attendance_day)
            ->where('employee_in_outs.emp_io_month', $month)
            ->where('employee_in_outs.emp_io_year', $year)
            ->get();
    }
    // public function getListOfEmplyeeIdThoseAreNotPresentyAttendedEmployeeIdListByDayMonthAndYear($attendance_day, $month, $year)
    // {
    //     return EmployeeInOut::select('emp_id')
    //         ->where('employee_in_outs.emp_io_date', $attendance_day)
    //         ->where('employee_in_outs.emp_io_month', $month)
    //         ->where('employee_in_outs.emp_io_year', $year)
    //         ->get();
    // }

    public function getAttendanceINEmployeeListForAttendanceOutByProjectIdDayMonthYear($project_id,$day,$month,$year,$isNightShift){
        return EmployeeInOut::select(
                                'employee_infos.emp_auto_id',
                                'employee_infos.employee_id',
                                'employee_infos.employee_name',
                                'employee_infos.hourly_employee',
                                'employee_in_outs.proj_id',
                                'employee_in_outs.emp_io_date',
                                'employee_in_outs.emp_io_month',
                                'employee_in_outs.emp_io_year',
                                'employee_in_outs.emp_io_id',
                                'employee_in_outs.attendance_type',
                                'employee_in_outs.emp_io_entry_time',
                                'employee_in_outs.emp_io_shift',
                                'employee_categories.catg_name')
                    ->where('employee_in_outs.proj_id', $project_id)
                    ->where('employee_in_outs.emp_io_date', $day)
                    ->where('employee_in_outs.emp_io_month', $month)
                    ->where('employee_in_outs.emp_io_year', $year)
                    ->where('employee_in_outs.attendance_type', 1)
                    ->where('employee_in_outs.emp_io_shift', $isNightShift)
                    ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->orderBy('employee_infos.employee_id')
                    ->get();
    }

    public function deleteEmployeeDailyAttendanceRecord($emp_io_id)
    {
        return EmployeeInOut::where('emp_io_id', $emp_io_id)->delete();
    }


    public function getProjectWiseTotalWorkHoursSummaryForMonthlyWorkRecordApproval($project_id_list,$month, $year){
        return  EmployeeInOut::select(
            DB::raw('sum(daily_work_hours) as basic_hours'),
            DB::raw('sum(over_time) as over_time'),
            DB::raw('count(DISTINCT emp_id) as total_emp'),
            'emp_io_month',
            'emp_io_year',
             'project_infos.proj_name',
             'project_infos.proj_id',
            )
            ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
            ->whereIn('project_infos.proj_id',$project_id_list)
            ->where('emp_io_month', $month)
            ->where('emp_io_year', $year)
            ->groupBy('emp_io_month')
            ->groupBy('emp_io_year')
            ->groupBy('proj_name')
            ->groupBy('proj_id')
            ->get();
    }

    /* =============================  Employee Attendance IN/OUT Report Export Methods =================  */
    public function getListOfEmployeesThoseWorkedInThisProjectForExportAttendanceInOut($project_id,$month,$year,$working_shift)
    {
        if(is_null($working_shift)){
            return EmployeeInOut::select(
                'employee_infos.emp_auto_id',
                'employee_infos.employee_id',
                'employee_infos.employee_name',
                'employee_infos.akama_no',
                'employee_infos.hourly_employee',
                'employee_categories.catg_name',
                'employee_in_outs.proj_id')
                ->where('employee_in_outs.proj_id', $project_id)
                ->where('employee_in_outs.emp_io_month', $month)
                ->where('employee_in_outs.emp_io_year', $year)
                ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->orderBy('employee_infos.employee_id')
                ->distinct()
                ->get();

        }else{
                return EmployeeInOut::select(
                    'employee_infos.emp_auto_id',
                    'employee_infos.employee_id',
                    'employee_infos.employee_name',
                    'employee_infos.akama_no',
                    'employee_infos.hourly_employee',
                    'employee_categories.catg_name',
                    'employee_in_outs.proj_id')
                    ->where('employee_in_outs.proj_id', $project_id)
                    ->where('employee_in_outs.emp_io_month', $month)
                    ->where('employee_in_outs.emp_io_year', $year)
                    ->where('employee_in_outs.emp_io_shift', $working_shift)
                    ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                    ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                    ->orderBy('employee_infos.employee_id')
                    ->distinct()
                    ->get();
        }
    }

    public function getAnEmployeeMonthlyAttendanceDateToDateRecordsByProjectIdForExportExcell($emp_auto_id,$project_id,$fromday,$today, $month, $year,$working_shift_list)
    {
        return  $report = EmployeeInOut::select('emp_io_date','daily_work_hours','over_time','attendance_status','proj_id')
                            ->where('emp_id', $emp_auto_id)
                            ->whereBetween('emp_io_date',[$fromday,$today])
                            ->where('proj_id', $project_id)
                            ->whereIn('emp_io_shift', $working_shift_list)
                            ->where('emp_io_year', $year)->where('emp_io_month', $month)->orderBy('emp_io_date', 'ASC')->get();
    }

    public function getAnEmployeeMonthlyAttendanceDateToDateRecordsForAttendancePreview($emp_auto_id,$project_id,$fromday,$today, $month, $year,$working_shift)
    {

        return  $report = EmployeeInOut::select('emp_io_date','daily_work_hours','over_time','attendance_status','proj_id')
                            ->where('emp_id', $emp_auto_id)
                            ->whereBetween('emp_io_date',[$fromday,$today])
                            ->where('proj_id', $project_id)
                            ->where('emp_io_shift', $working_shift)
                            ->where('emp_io_year', $year)->where('emp_io_month', $month)->orderBy('emp_io_date', 'ASC')->get();
    }

    public function getEmployeeWorkingListOfProjectWithColorCode($emp_auto_id_list,$from_date,$to_date){
        return  EmployeeInOut::select(
             'employee_in_outs.proj_id',
             'project_infos.proj_name',
             'project_infos.color_code'
            )
            ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
            ->whereIn('employee_in_outs.emp_id',$emp_auto_id_list)
            ->whereBetween('employee_in_outs.emp_io_entry_date',[$from_date,$to_date])
            ->distinct()->get();

    }
    public function getEmployeeWorkingListOfProjectWithColorCodeByWorkingMonthYear($emp_auto_id_list,$month,$year){

        return  EmployeeInOut::select(
             'employee_in_outs.proj_id',
             'project_infos.proj_name',
             'project_infos.color_code'
            )
            ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
            ->whereIn('employee_in_outs.emp_id',$emp_auto_id_list)
            ->where('employee_in_outs.emp_io_month',$month)
            ->where('employee_in_outs.emp_io_year',$year)
            ->distinct()->get();

    }

    public function getEmployeeWorkingListOfProjectWithColorCodeByProjectSponsorShiftDayMonthYear($project_ids,$sponsor_ids,$working_shift,$day,$month,$year){

      return  EmployeeInOut::select(
        'employee_in_outs.proj_id',
        'project_infos.proj_name',
        'project_infos.color_code'
       )
       ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
       ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
       ->where('employee_in_outs.emp_io_date',$day)
       ->where('employee_in_outs.emp_io_month',$month)
       ->where('employee_in_outs.emp_io_year',$year)
       ->whereIn('employee_in_outs.proj_id',$project_ids)
       ->whereIn('employee_in_outs.emp_io_shift',$working_shift)
       ->whereIn('employee_infos.sponsor_id',$sponsor_ids)
       ->distinct()->get();

    }


   /* ============================= Employee Attendance IN/OUT Report =================  */

    public function getAnEmployeeMonthlyAttendanceAllRecords($emp_auto_id, $month, $year)
    {
        return EmployeeInOut::where('emp_id', $emp_auto_id)->where('emp_io_year', $year)->where('emp_io_month', $month)->orderBy('emp_io_date', 'ASC')->get();
    }
    public function getAnEmployeeMonthlyAttendanceLastWorkingRecordInAMonth($emp_auto_id, $month, $year)
    {
        return EmployeeInOut::where('emp_id', $emp_auto_id)->where('emp_io_year', $year)->where('emp_io_month', $month)->orderBy('emp_io_date', 'DESC')->first();
    }

    public function getAnEmployeeMonthlyAttendanceDateToDateRecords($emp_auto_id,$fromday,$today, $month, $year)
    {
        return $report = EmployeeInOut::select('emp_io_id','proj_id','emp_id','emp_io_date','emp_io_month','emp_io_year','emp_io_shift','daily_work_hours','over_time','attendance_status')
                    ->where('emp_id', $emp_auto_id)
                    ->whereBetween('emp_io_date',[$fromday,$today])
                    ->where('emp_io_year', $year)->where('emp_io_month', $month)
                    ->orderBy('emp_io_date', 'ASC')->get();
    }

    public function getAnEmployeeMonthlyAttendanceDateToDateRecordsWithProjecColorCodeForShowingAllMonthReport($emp_auto_id,$fromday,$today, $month, $year)
    {

        return $report = EmployeeInOut::select('employee_in_outs.emp_io_id','employee_in_outs.proj_id','employee_in_outs.emp_id','employee_in_outs.emp_io_date','employee_in_outs.emp_io_month','employee_in_outs.emp_io_year','employee_in_outs.emp_io_shift','employee_in_outs.daily_work_hours','employee_in_outs.over_time','employee_in_outs.attendance_status','project_infos.color_code')
                            ->where('emp_id', $emp_auto_id)
                            ->whereBetween('emp_io_date',[$fromday,$today])
                            ->where('emp_io_year', $year)->where('emp_io_month', $month)
                            ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
                            ->orderBy('emp_io_date', 'ASC')->get();
    }


    public function getDateToDateTotalWorkHourseSummary($project_id_list,$fromday,$today, $monthList, $yearList){
        return  EmployeeInOut::select(
            DB::raw('sum(daily_work_hours) as basic_hours'),
            DB::raw('sum(over_time) as over_time'),
            DB::raw('count(DISTINCT emp_id) as total_emp'),
            'emp_io_month',
            'emp_io_year',
             'project_infos.proj_name',
            )
            ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
            ->whereIn('project_infos.proj_id',$project_id_list)
            ->whereIn('emp_io_month', $monthList)
            ->whereIn('emp_io_year', $yearList)
            ->groupBy('emp_io_month')
            ->groupBy('emp_io_year')
            ->groupBy('proj_name')
            ->get();

    }

    public function getAnEmployeeMonthlyAttendanceSummaryDateToDateRecords($project_id,$fromday,$today, $month, $year,$working_shift)
    {

        if($working_shift == null){
            return  EmployeeInOut::select(
                DB::raw('sum(daily_work_hours) as total_work_hours'),
                DB::raw('count(emp_io_id) as total_days'),
                DB::raw('sum(over_time) as overtime'),
                'employee_infos.employee_name' ,
                'employee_infos.employee_id',
                'employee_categories.catg_name',
                'employee_infos.akama_no' ,
                )
                ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('employee_categories', 'employee_categories.catg_id' ,'=','employee_infos.designation_id')
                ->whereBetween('emp_io_date',[$fromday,$today])
                ->where('emp_io_month', $month)
                ->where('emp_io_year', $year)
                ->where('proj_id', $project_id)
                ->groupBy('employee_infos.employee_name')
                ->groupBy('employee_infos.employee_id')
                ->groupBy('employee_categories.catg_name')
                ->groupBy('employee_infos.akama_no')
                ->get();
        }else {
            return              EmployeeInOut::select(
                                DB::raw('sum(daily_work_hours) as total_work_hours'),
                                DB::raw('count(emp_io_id) as total_days'),
                                DB::raw('sum(over_time) as overtime'),
                                'employee_infos.employee_name' ,
                                'employee_infos.employee_id',
                                'employee_categories.catg_name',
                                'employee_infos.akama_no' ,
                                )
                                ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                                ->leftjoin('employee_categories', 'employee_categories.catg_id' ,'=','employee_infos.designation_id')
                                ->whereBetween('emp_io_date',[$fromday,$today])
                                ->where('emp_io_month', $month)
                                ->where('emp_io_year', $year)
                                ->where('proj_id', $project_id)
                                ->where('emp_io_shift',$working_shift)
                               // ->groupBy('emp_id')
                                ->groupBy('employee_infos.employee_name')
                                ->groupBy('employee_infos.employee_id')
                                ->groupBy('employee_categories.catg_name')
                                ->groupBy('employee_infos.akama_no')
                                ->get();
            }
    }


    public function getAnEmployeeMonthlyAttendanceTotalHoursOTAndDaysDateToDate($emp_auto_id,$fromday,$today, $month, $year)
    {
            return  EmployeeInOut::select(
                DB::raw('sum(daily_work_hours) as total_work_hours'),
                DB::raw('count(emp_io_id) as total_days'),
                DB::raw('sum(over_time) as total_overtime')
                )
                ->whereBetween('emp_io_date',[$fromday,$today])
                ->where('emp_io_month', $month)
                ->where('emp_io_year', $year)
                ->where('emp_id', $emp_auto_id)
                ->get();

    }

    public function countAnEmployeeTotalWorkingHoursFromDateToDate($emp_auto_id,$fromday,$today,$month, $year)
    {
        return EmployeeInOut::where('emp_id', $emp_auto_id)
                        ->whereBetween('emp_io_date',[$fromday,$today])
                        ->where('emp_io_month', $month)->Where('emp_io_year', $year)->sum('daily_work_hours');
    }

    public function countAnEmployeeTotalOverTimeHoursFromDateToDate($emp_auto_id,$fromday,$today,$month, $year)
    {
        return EmployeeInOut::where('emp_id', $emp_auto_id)
                        ->whereBetween('emp_io_date',[$fromday,$today])
                        ->where('emp_io_month', $month)->Where('emp_io_year', $year)->sum('over_time');
    }


    public function countTotalOverTimeHoursFromDateToDate($fromday,$today,$month, $year)
    {
        return EmployeeInOut::whereBetween('emp_io_date',[$fromday,$today])
                        ->where('emp_io_month', $month)->Where('emp_io_year', $year)->sum('over_time')->get();
    }

    public function countTotalWorkingHoursFromDateToDate($fromday,$today,$month, $year)
    {
        return EmployeeInOut::whereBetween('emp_io_date',[$fromday,$today])
                        ->where('emp_io_month', $month)->Where('emp_io_year', $year)->sum('daily_work_hours') ;
    }


    public function countTotalNumberOfWorkingDaysForAnEmployeeFromDateToDate($emp_auto_id,$fromday,$today,$month, $year)
    {
        return EmployeeInOut::where('emp_id', $emp_auto_id)
                        ->whereBetween('emp_io_date',[$fromday,$today])
                        ->where('emp_io_month', $month)->Where('emp_io_year', $year)->count();
    }




    public function countTotalNumberOfWorkingDaysForAnEmployeeInaMonth($emp_auto_id,$month, $year)
    {
        return EmployeeInOut::where('emp_id', $emp_auto_id)
                        ->where('emp_io_month', $month)->Where('emp_io_year', $year)->count();
    }

    public function countTotalNumberOfWorkersPresentInADay($day,$month, $year)
    {
        return EmployeeInOut::where('emp_io_date', $day)
                        ->where('emp_io_month', $month)
                        ->Where('emp_io_year', $year)->count();
    }

    public function countTotalNumberOfWorkersPresentInADayOfABranchOffice($day,$month, $year,$branch_office_id)
    {
        return EmployeeInOut::where('emp_io_date', $day)
                        ->where('emp_io_month', $month)
                        ->Where('emp_io_year', $year)
                        ->Where('branch_office_id', $branch_office_id)
                        ->count();
    }

    public function countTotalNumberOfWorkersWorkedInAMonthInTheProject($project_id,$month, $year,$working_shift)
    {
         // 0 = dayshift , 1 = nightshif , 2 = bothshift
        if(is_null($working_shift) || $working_shift == 2){
            return EmployeeInOut::where('proj_id', $project_id)
            ->where('emp_io_month', $month)
            ->Where('emp_io_year', $year)->get()->unique('emp_id')->count();
        }else {
            return EmployeeInOut::where('proj_id', $project_id)
            ->where('emp_io_month', $month)
            ->Where('emp_io_year', $year)
            ->where('emp_io_shift', $working_shift)
            ->get()->unique('emp_id')->count();
        }

    }

    public function countNumberOfEmployeesPresentInTheProject($project_id, $day,$month,$year,$working_shift){
        return  EmployeeInOut::where('emp_io_month', $month)
            ->where('emp_io_year', $year)
            ->where('emp_io_date', $day)
            ->where('emp_io_shift', $working_shift)
            ->where('proj_id', $project_id)
            ->count();
}


    public function updateEmployeeDailyAttendanceRecord($emp_io_id, $emp_io_entry_time, $emp_io_out_time, $daily_work_hours,$isFriday)
    {

        $overtime = 0;
        if($isFriday){
            $overtime = $daily_work_hours;
            $daily_work_hours = 0;
        }
        else if ($daily_work_hours > 10) {

            $overtime = $daily_work_hours - 10.0;
            $daily_work_hours = 10;
        }
        $attendance_type = 3;
        if ($daily_work_hours >= 5 || $isFriday) {
            $attendance_type = 2;
        }
        return EmployeeInOut::where('emp_io_id', $emp_io_id)->update([
            'emp_io_entry_time' => $emp_io_entry_time,
            'emp_io_out_time' => $emp_io_out_time,
            'daily_work_hours' => (float) $daily_work_hours,
            'over_time' => $overtime,
            'attendance_type' => $attendance_type,
            'updated_at' => Carbon::now()
        ]);
    }

    public function updateEmployeeDailyAttendanceByAttendanceOut($emp_io_id, $emp_io_out_time, $daily_work_hours,$isFriday)
    {
        $overtime = 0;
        if($isFriday){
            $overtime = $daily_work_hours;
            $daily_work_hours = 0;
        }
        else if ($daily_work_hours > 10) {
            $overtime = $daily_work_hours - 10.0;
            $daily_work_hours = 10;
        }
        $attendance_type = 3;
        if ($daily_work_hours >= 5 || $isFriday) {
            $attendance_type = 2;
        }
        return EmployeeInOut::where('emp_io_id', $emp_io_id)->update([
            'emp_io_out_time' => $emp_io_out_time,
            'daily_work_hours' => (float) $daily_work_hours,
            'over_time' => $overtime,
            'attendance_type' => $attendance_type,
            'updated_at' => Carbon::now()
        ]);
    }

    public function updateAnEmployeeDailyAttendanceRecordFromMultiEmployeeUpdateRequest($emp_io_id,$emp_io_entry_time, $emp_io_out_time, $daily_work_hours,$updated_by,$isFriday)
    {
        $overtime = 0;
        if($isFriday){
            $overtime = $daily_work_hours;
            $daily_work_hours = 0;
        }
        else  if ($daily_work_hours > 10) {
            $overtime = $daily_work_hours - 10.0;
            $daily_work_hours = 10;
        }
        $attendance_type = 1;
        if ($daily_work_hours >= 5) {
            $attendance_type = 2;
        }

        return EmployeeInOut::where('emp_io_id', $emp_io_id)->update([
            'emp_io_entry_time' => $emp_io_entry_time,
            'emp_io_out_time' => $emp_io_out_time,
            'daily_work_hours' => (float) $daily_work_hours,
            'over_time' => $overtime,
            'attendance_type' => $attendance_type,
            'updated_at' => Carbon::now()
        ]);
    }

    public function getAnEmployeeAttendanceInOutRecords($emp_auto_id, $month, $year)
    {

        static $EmployeeInOutTable = "employee_in_outs";

        return  EmployeeInOut::select(
            DB::raw('sum(daily_work_hours) as total_work_hours'),
            DB::raw('count(emp_io_id) as total_days'),
            DB::raw('sum(over_time) as overtime'),
            'emp_id',
            'proj_id'
        )   ->where('emp_io_month', $month)
            ->where('emp_io_year', $year)
            ->where('emp_id', $emp_auto_id)
            ->groupBy('proj_id', 'emp_id')
            ->get();
    }
   // total basic and ot hours worked project, for Processing Attenance INOUT
    public function getAnEmployeeAttendanceInOutTotalBasicAndOvertimeByProjectId($emp_auto_id,$project_id, $month, $year)
    {

        return  EmployeeInOut::select(
            DB::raw('sum(daily_work_hours) as total_work_hours'),
            DB::raw('count(emp_io_id) as total_days'),
            DB::raw('sum(over_time) as overtime'),
            'emp_id',
            'proj_id',
        )   ->where('emp_io_month', $month)
            ->where('emp_io_year', $year)
            ->where('emp_id', $emp_auto_id)
            ->where('proj_id', $project_id)
            ->groupBy('emp_id','proj_id')
            ->get();
    }

    public function getListOfEmployeesAttendanceInOutSummaryOfAMonth($employee_ids,$month,$year){
        return  EmployeeInOut::select(
            DB::raw('count(emp_io_id)'),
            DB::raw('sum(daily_work_hours) as basic_hours'),
            DB::raw('count(emp_io_id) as working_days'),
            DB::raw('sum(over_time) as overtime'),
            'employee_infos.employee_name' ,
            'employee_infos.employee_id',
            'employee_categories.catg_name',
            'employee_infos.akama_no' ,
            'project_infos.proj_name',
            )
            ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_categories.catg_id' ,'=','employee_infos.designation_id')
            ->where('emp_io_month', $month)
            ->where('emp_io_year', $year)
            ->whereIn('employee_infos.employee_id', $employee_ids)
            ->groupBy('proj_name')
            ->groupBy('employee_infos.employee_name')
            ->groupBy('employee_infos.employee_id')
            ->groupBy('employee_categories.catg_name')
            ->groupBy('employee_infos.akama_no')
            ->orderBy('employee_infos.employee_id')
            ->get();
    }

    public function getEmployeesAttendanceInOutSummaryOfAMonthByProjectId($proj_id,$month,$year){
        return  EmployeeInOut::select(
            DB::raw('count(emp_io_id)'),
            DB::raw('sum(daily_work_hours) as basic_hours'),
            DB::raw('count(emp_io_id) as working_days'),
            DB::raw('sum(over_time) as overtime'),
            'employee_infos.employee_name' ,
            'employee_infos.employee_id',
            'employee_categories.catg_name',
            'employee_infos.akama_no' ,
            'project_infos.proj_name',
            )
            ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
            ->leftjoin('project_infos', 'employee_in_outs.proj_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_categories.catg_id' ,'=','employee_infos.designation_id')
            ->where('emp_io_month', $month)
            ->where('emp_io_year', $year)
            ->where('project_infos.proj_id', $proj_id)
            ->groupBy('proj_name')
            ->groupBy('employee_infos.employee_name')
            ->groupBy('employee_infos.employee_id')
            ->groupBy('employee_categories.catg_name')
            ->groupBy('employee_infos.akama_no')
            ->orderBy('employee_infos.employee_id')
            ->get();
    }


    /*
     ==========================================================================
     ============================= Daily Attendance Summary ===================
     ==========================================================================
    */


    public function insertDailyAttendanceSummaryRecord($project_id,$shift,$day,$month,$year,$total_emp,$total_present,$attend_date,$branch_office_id){

        $record = (DailyAttendanceSummary::where('project_id',$project_id)->where('is_night_shift',$shift)
        ->where('day',$day)->where('month',$month)->where('year',$year)->count() ) >0 ? true:false;

        if(!$record){
                return  DailyAttendanceSummary::insert([
                'project_id' => $project_id,
                'total_emp' => $total_emp,
                'total_present' => $total_present,
                'total_absent' =>$total_emp - $total_present,
                'attendance_date' =>  $attend_date,
                'day' => $day,
                'month' => $month,
                'year' => $year,
                'is_night_shift' => $shift,
                'branch_office_id' => $branch_office_id
            ]);
        }else {
            return  DailyAttendanceSummary::where('project_id',$project_id)->where('is_night_shift',$shift)
            ->where('day',$day)->where('month',$month)->where('year',$year)->update([
                'total_emp' => $total_emp,
                'total_present' => $total_present,
                'total_absent' => $total_emp - $total_present,
            ]);
        }

  }

    /*
     ==========================================================================
     ============================= Employee Attendance Approval ===============
     ==========================================================================
    */

     public function insertAttendanceApprovalRecord($project_id,$month,$year,$approval_status,$date){
           if(AttendanceApprovalRecord::where('project_id',$project_id)->where('month',$month)->where('year',$year)->count() > 0)
            { return false;}
           return  AttendanceApprovalRecord::insertGetId([
            'project_id' => $project_id,
            'month' => $month,
            'year' => $year,
            'approval_status' =>$approval_status,
            'created_at' => Carbon::now(),
            'updated_at' => $date,
          ]);
     }

     public function getAttendanceApprovalRecord($project_id,$month,$year){
        return AttendanceApprovalRecord::where('project_id',$project_id)->where('month',$month)->where('year',$year)->first();
     }

    public function getAttendanceApprovalPendingRecords($project_id,$month,$year){

        return AttendanceApprovalRecord::select('attendance_approval_records.atten_appro_auto_id','attendance_approval_records.month','attendance_approval_records.year','attendance_approval_records.approved_by_id',
        'attendance_approval_records.approval_status','project_infos.proj_name')->where('project_id',$project_id)->where('month',$month)
                            ->where('year',$year)->where('approval_status',MonthlyAttendanceStatusEnum::Pending)
                            ->leftjoin('project_infos', 'attendance_approval_records.project_id', '=', 'project_infos.proj_id')
                            ->get();
    }

    public function checkIsMonthlyAttendanceRecordApprovalStatus($project_id,$month,$year){
         if($month == null || $year == null || $project_id == null || $project_id <= 0)
         return false;
         $record = AttendanceApprovalRecord::where('project_id',$project_id)->where('month',$month)
                            ->where('year',$year)->where('approval_status',MonthlyAttendanceStatusEnum::Approved)
                             ->get();
                             if(count($record) == 0){
                                return false;
                             }else{ return true;}
    }

    public function approvedMontlyAttendanceRecords($pending_record_id,$approved_by){

        return AttendanceApprovalRecord::where('atten_appro_auto_id',$pending_record_id)
                ->update([
                    'approval_status'=> MonthlyAttendanceStatusEnum::Approved->value,
                    'approved_date' => date('Y-m-d'),
                    'approved_by_id' => $approved_by,
                    ]);
    }
    public function updateMonthlyAttendanceWorkRecordsByPendingOrApproved($pending_record_id,$approved_status,$approved_by){

        return AttendanceApprovalRecord::where('atten_appro_auto_id',$pending_record_id)
                ->update([
                    'approval_status'=> $approved_status,
                    'approved_date' => date('Y-m-d'),
                    'approved_by_id' => $approved_by,
                    ]);
    }



   /*
     ==========================================================================
     ============================= Employee Attendance Report =================
     ==========================================================================
    */


    public function getEmployeeDayNightDetailsAttendanceSummaryByProject($day, $month, $year,$project_id_list)
    {

                $all_records = array();
                $counter = 0;
                foreach($project_id_list as $aproject_id){

                    $day_record = new EmployeeInOut();
                    $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($aproject_id);
                    $total_records = EmployeeInOut::select(
                            DB::raw("COUNT(emp_io_id ) as total_present_emp"),
                            DB::raw("SUM(daily_work_hours) as total_daily_work_hours"),
                            DB::raw("SUM(over_time) as total_over_time"),
                        )->where('emp_io_date', $day)->where('emp_io_month', $month)->where('emp_io_year', $year)->where('proj_id',$aproject_id)
                        ->where('employee_in_outs.emp_io_shift' ,0)
                        ->get();

                    $day_record->total_present_emp = $total_records[0]->total_present_emp == null ? 0 : $total_records[0]->total_present_emp;
                    $day_record->total_daily_work_hours = $total_records[0]->total_daily_work_hours == null ? 0 : $total_records[0]->total_daily_work_hours;
                    $day_record->total_over_time = $total_records[0]->total_over_time == null ? 0 : $total_records[0]->total_over_time;
                    $day_record->emp_io_shift = 0;
                    $day_record->project_name =  $project_name ;
                    $day_record->total_active_emp = (new EmployeeDataService())->countDayShiftWorkingActiveEmployeesInAProject($aproject_id,Auth::user()->branch_office_id);
                    $day_record->total_absent =  $day_record->total_active_emp - $day_record->total_present_emp;
                    if($day_record->total_active_emp >0 ){
                        $all_records[$counter++] = $day_record;
                    }


                    $night_record = new EmployeeInOut();
                    $total_records = EmployeeInOut::select(
                            DB::raw("COUNT(emp_io_id ) as total_present_emp"),
                            DB::raw("SUM(daily_work_hours) as total_daily_work_hours"),
                            DB::raw("SUM(over_time) as total_over_time"),
                        )->where('emp_io_date', $day)->where('emp_io_month', $month)->where('emp_io_year', $year)->where('proj_id',$aproject_id)
                        ->where('employee_in_outs.emp_io_shift' ,1)
                        ->get();

                    $night_record->total_present_emp = $total_records[0]->total_present_emp == null ? 0 : $total_records[0]->total_present_emp;
                    $night_record->total_daily_work_hours = $total_records[0]->total_daily_work_hours == null ? 0 : $total_records[0]->total_daily_work_hours;
                    $night_record->total_over_time = $total_records[0]->total_over_time == null ? 0 : $total_records[0]->total_over_time;
                    $night_record->emp_io_shift = 1;
                    $night_record->project_name =  $project_name ;
                    $night_record->total_active_emp = (new EmployeeDataService())->countNightShiftWorkingActiveEmployeesInAProject($aproject_id,Auth::user()->branch_office_id);
                    $night_record->total_absent =  $night_record->total_active_emp - $night_record->total_present_emp;
                    if($night_record->total_active_emp >0 ){
                        $all_records[$counter++] = $night_record;
                    }

                }

                return $all_records;

    }

    public function getPrevioudayNightAndSelectedDateDayShiftAttendanceSummaryByProject($day, $month, $year,$project_id_list)
    {
            $all_records = array();
            $counter = 0;
            foreach($project_id_list as $aproject_id){

                $project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($aproject_id);
                // previous day Night Shift
                $night_record = new EmployeeInOut();
                $total_records = EmployeeInOut::select(
                        DB::raw("COUNT(emp_io_id ) as total_present_emp"),
                        DB::raw("SUM(daily_work_hours) as total_daily_work_hours"),
                        DB::raw("SUM(over_time) as total_over_time"),
                    )->where('emp_io_date', ($day-1))->where('emp_io_month', $month)->where('emp_io_year', $year)->where('proj_id',$aproject_id)
                    ->where('employee_in_outs.emp_io_shift' ,1)
                    ->get();

                $night_record->total_present_emp = $total_records[0]->total_present_emp == null ? 0 : $total_records[0]->total_present_emp;
                $night_record->total_daily_work_hours = $total_records[0]->total_daily_work_hours == null ? 0 : $total_records[0]->total_daily_work_hours;
                $night_record->total_over_time = $total_records[0]->total_over_time == null ? 0 : $total_records[0]->total_over_time;
                $night_record->emp_io_shift = 1;
                $night_record->project_name =  $project_name ;
                $night_record->total_active_emp = (new EmployeeDataService())->countNightShiftWorkingActiveEmployeesInAProject($aproject_id,Auth::user()->branch_office_id);
                $night_record->total_absent =  $night_record->total_active_emp - $night_record->total_present_emp;
                if($night_record->total_active_emp >0 ){
                    $all_records[$counter++] = $night_record;
                }
                 // Selected Date Dayshift
                $day_record = new EmployeeInOut();
                $total_records = EmployeeInOut::select(
                        DB::raw("COUNT(emp_io_id ) as total_present_emp"),
                        DB::raw("SUM(daily_work_hours) as total_daily_work_hours"),
                        DB::raw("SUM(over_time) as total_over_time"),
                      )->where('emp_io_date', $day)->where('emp_io_month', $month)->where('emp_io_year', $year)->where('proj_id',$aproject_id)
                      ->where('employee_in_outs.emp_io_shift' ,0)
                      ->get();

                $day_record->total_present_emp = $total_records[0]->total_present_emp == null ? 0 : $total_records[0]->total_present_emp;
                $day_record->total_daily_work_hours = $total_records[0]->total_daily_work_hours == null ? 0 : $total_records[0]->total_daily_work_hours;
                $day_record->total_over_time = $total_records[0]->total_over_time == null ? 0 : $total_records[0]->total_over_time;
                $day_record->emp_io_shift = 0;
                $day_record->project_name =  $project_name ;
                $day_record->total_active_emp = (new EmployeeDataService())->countDayShiftWorkingActiveEmployeesInAProject($aproject_id,Auth::user()->branch_office_id);
                $day_record->total_absent =  $day_record->total_active_emp - $day_record->total_present_emp;
                if($day_record->total_active_emp >0 ){
                    $all_records[$counter++] = $day_record;
                }

            }

            return $all_records;

    }

    public function getEmployeeDailyAttendanceSummaryByProject($day, $month, $year,$project_id_list)
    {


        $total_records  = Redis::get('attendance_'.$day);
        if(isset($total_records)) {
            dd('redis cache data records ', $total_records);
            $total_records = json_decode($total_records, true);
            return $total_records;
        }else {

            $total_records = EmployeeInOut::select(
                'proj_id',
                 DB::raw("COUNT(emp_io_id ) as total_present_emp"),
                 DB::raw("SUM(daily_work_hours) as total_daily_work_hours"),
                 DB::raw("SUM(over_time) as total_over_time"),
                  )->where('emp_io_date', $day)->where('emp_io_month', $month)->where('emp_io_year', $year)->whereIn('proj_id',$project_id_list)
                 ->groupBy('employee_in_outs.proj_id')->get();

                foreach($total_records as $record){
                    $record->emp_io_shift = 2;  // 2 = day +night
                    $record->total_emp_in_project = (new EmployeeDataService())->countTotalEmployeesInAProject($record->proj_id,1);
                    $record->project_name = (new EmployeeRelatedDataService())->getProjectNameByProjectId($record->proj_id);
                    $record->total_active_emp = (new EmployeeDataService())->countTotalEmployeesInAProject($record->proj_id,1);
                }

            Redis::set('attendance_'.$day, $total_records);
            return $total_records;
        }

    }

    public function getMonthlyAttendanceHoursSummaryReportByProject($month, $year,$project_id,$working_shift)
    {
                 return EmployeeInOut::select(
                        'proj_id',
                        DB::raw("SUM(daily_work_hours) as basic_hours"),
                        DB::raw("SUM(over_time) as over_time"),
                        )->where('emp_io_month', $month)->where('emp_io_year', $year)
                        ->where('emp_io_shift',$working_shift)
                        ->where('proj_id',$project_id)
                        ->groupBy('employee_in_outs.proj_id')
                        ->first();

    }



    public function getEmployeeDailyAttendanceManpowerSummaryByProject($day, $month, $year,$project_id,$working_shift)
    {
            $total_records = (new EmployeeDataService())->countTradewiseTotalActiveEmployeeInProject([$project_id],$working_shift);
            foreach($total_records as $record){

                $record->total_present_emp = EmployeeInOut::select(
                     DB::raw("COUNT(emp_io_id ) as total_present_emp"),
                      )
                      ->leftjoin('employee_infos', 'employee_in_outs.emp_id', '=', 'employee_infos.emp_auto_id')
                      ->where('employee_in_outs.emp_io_date', $day)
                      ->where('employee_in_outs.emp_io_month', $month)
                      ->where('employee_in_outs.emp_io_year', $year)
                      ->where('employee_in_outs.proj_id',$project_id)
                      ->where('employee_in_outs.emp_io_shift',$working_shift)
                      ->where('employee_infos.designation_id' , $record->designation_id)
                      ->count();
            }
            return $total_records;

    }



    public function getTodayAbsentEmployeeDetailsReport($project_id,$day,$month,$year,$isNightShift){

      //  dd($project_id,$day,$month,$year,$isNightShift,200);
        $todayAttendEmpList = $this->getTodayAlreadyAttendedEmployeeIdListByDayMonthAndYear($day,$month,$year);

        if($project_id == null && $isNightShift == null){
                return EmployeeInfo::
                        select(
                            'employee_infos.employee_id',
                            'employee_infos.employee_name',
                            'isNightShift',
                            'akama_no',
                            'mobile_no',
                            'project_infos.proj_name',
                            'employee_categories.catg_name',
                            'sponsors.spons_name',
                            'salary_details.basic_amount',
                            'salary_details.basic_hours',
                            'salary_details.hourly_rent',
                            'salary_details.food_allowance','office_buildings.ofb_name')
                ->where('employee_infos.job_status', 1)
                ->whereNotIn('emp_auto_id',$todayAttendEmpList)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->orderBy('employee_infos.employee_id')
                ->get();
        }
        else if($project_id == null && $isNightShift != null){
                        return EmployeeInfo::
                        select(
                            'employee_infos.employee_id',
                            'employee_infos.employee_name',
                            'isNightShift',
                            'akama_no' ,
                            'mobile_no',
                            'project_infos.proj_name',
                            'employee_categories.catg_name',
                            'sponsors.spons_name',
                            'salary_details.basic_amount',
                            'salary_details.basic_hours',
                            'salary_details.hourly_rent',
                            'salary_details.food_allowance','office_buildings.ofb_name')
                ->where('employee_infos.job_status', 1)
                ->whereIn('employee_infos.isNightShift', $isNightShift)
                ->whereNotIn('emp_auto_id',$todayAttendEmpList)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->orderBy('employee_infos.employee_id')
                ->get();
        }
        else if($project_id != null && $isNightShift == null){
                    return EmployeeInfo::
                    select(
                        'employee_infos.employee_id',
                            'employee_infos.employee_name',
                            'isNightShift',
                            'akama_no' ,
                            'mobile_no',
                            'project_infos.proj_name',
                            'employee_categories.catg_name',
                            'sponsors.spons_name',
                            'salary_details.basic_amount',
                            'salary_details.basic_hours',
                            'salary_details.hourly_rent',
                            'salary_details.food_allowance','office_buildings.ofb_name')
            ->whereIn('project_id', $project_id)
            ->where('employee_infos.job_status', 1)
            ->whereNotIn('emp_auto_id',$todayAttendEmpList)
            ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
            ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
            ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
            ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
            ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
            ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
            ->orderBy('employee_infos.employee_id')
            ->get();
        }
        else if($project_id != null && $isNightShift != null){
            return EmployeeInfo::
                        select(
                            'employee_infos.employee_id',
                            'employee_infos.employee_name',
                            'isNightShift',
                            'akama_no' ,
                            'mobile_no',
                            'project_infos.proj_name',
                            'employee_categories.catg_name',
                            'sponsors.spons_name',
                            'salary_details.basic_amount',
                            'salary_details.basic_hours',
                            'salary_details.hourly_rent',
                            'salary_details.food_allowance','office_buildings.ofb_name')
                ->whereIn('project_id', $project_id)
                ->where('employee_infos.job_status', 1)
                ->whereIn('employee_infos.isNightShift', $isNightShift)
                ->whereNotIn('emp_auto_id',$todayAttendEmpList)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
                ->orderBy('employee_infos.employee_id')
                ->get();
        }


    }
    public function getListOfEmployeesThoseAreNotPresentInAttendanceRecordByProjectSponsorWorkingshiftDayMonthYear($project_ids,$sponsor_ids,$working_shift,$day,$month,$year){

        $todayAttendEmpList = $this->getTodayAlreadyAttendedEmployeeIdListByDayMonthAndYear($day,$month,$year);
                return EmployeeInfo::
                select(
                    'employee_infos.emp_auto_id',
                    'employee_infos.employee_id',
                    'employee_infos.employee_name',
                    'isNightShift',
                    'akama_no',
                    'mobile_no',
                    'project_infos.proj_name',
                    'employee_categories.catg_name', )
        ->where('employee_infos.job_status', 1)
        ->whereNotIn('emp_auto_id',$todayAttendEmpList)
        ->whereIn('employee_infos.project_id',$project_ids)
        ->whereIn('employee_infos.sponsor_id',$sponsor_ids)
        ->whereIn('employee_infos.isNightShift',$working_shift)
        ->leftjoin('employee_types', 'employee_infos.emp_type_id', '=', 'employee_types.id')
        ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
        ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
        ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
        ->orderBy('employee_infos.employee_id')
        ->get();


    }

    public function getListOfEmployeesThoseAreNotPresentMinimumDaysInAMonthAttendanceReport($project_id, $month,$year,$working_days){

       if(is_null($project_id) ||  (int)$project_id == 0 ){
            return  DB::select('CALL getEmployeesThoseAreNotPresentRegularly(?,?,?)',array($month,$year,$working_days));
       }else{
            return  DB::select('CALL getEmployeesThoseAreNotPresentRegularlyInAProject(?,?,?,?)',array( $month,$year,$working_days,$project_id));
        }

    }

    public function getMonthlyAbsentEmployeeRerpot($project_id, $month,$year,$working_days,$join_date){
        if(is_null($project_id)){
            return  DB::select('CALL getMonthlyAbsentEmployeeList(?,?,?,?)',array( $month,$year,$working_days,$join_date));
        }else {
            return  DB::select('CALL getMonthlyAbsentEmployeesByProjectId(?,?,?,?,?)',array( $month,$year,$working_days,$join_date,$project_id));
        }
    }
    // dashboard attendance summary report
    public function getOnlyAttendanceSummaryRerpot($day,$month,$year,$working_shift){
        return  DB::select('CALL getOnlyAttendanceSummary1(?,?,?,?)',array($day,$month,$year,$working_shift));
    }

    public function getTodayAttendanceSummaryReport($working_shift,$day,$month,$year){

        return  DB::select('CALL getDailyProjectAttendanceSummary(?,?,?,?)',array($working_shift,$day,$month,$year));
    }

     public function getTodayAttendanceSummaryReportInABranchOffice($working_shift,$day,$month,$year,$branch_office_id){
        return  DB::select('CALL getDailyProjectAttendanceSummaryInABranchOffice(?,?,?,?,?)',array($working_shift,$day,$month,$year,$branch_office_id));
    }

    public function getYesterdayNightshiftAttendanceSummaryReport($working_shift,$day,$month,$year){
        return []; //  DB::select('CALL getDailyProjectAttendanceSummary(?,?,?,?)',array(1,$day,$month,$year));
    }

    public function getYesterdayNightshiftAttendanceSummaryReportOfABranchOffice($working_shift,$day,$month,$year,$branch_office_id){
        return []; //  DB::select('CALL getDailyProjectAttendanceSummaryInABranchOffice(?,?,?,?,?)',array(1,$day,$month,$year,$branch_office_id));
    }

    public function getYesterdayDayshiftAttendanceSummaryReport($working_shift,$day,$month,$year){
        return  DB::select('CALL getDailyProjectAttendanceSummary(?,?,?,?)',array(0,$day,$month,$year));
    }

    public function getYesterdayDayshiftAttendanceSummaryReportOfABranchOffice($working_shift,$day,$month,$year,$branch_office_id){
        return []; //  DB::select('CALL getDailyProjectAttendanceSummaryInABranchOffice(?,?,?,?,?)',array(0,$day,$month,$year,$branch_office_id));
    }

    // public function getTodayAbsentEmployeeList($project_id,$working_shift,$day,$month,$year){
    //     return  DB::select('CALL getTodayAbsentEmployeeListAllOrByProjectId(?,?,?,?,?)',array($month,$year,$day,$project_id,$working_shift));
    // }

    public function getTodayPresentHourlyAndBasicEmployeeSummary($project_id,$working_shift,$day,$month,$year){
        return  DB::select('CALL  getTodayPresentHourlyAndBasicEmpSummaryAllOrByProjectId1(?,?,?,?,?)',array($project_id,$day,$month,$year,$working_shift));
    }



    /*
        |--------------------------------------------------------------------------
        |  Attendance IN OUT Permission
        |--------------------------------------------------------------------------
    */

    public function updateAttendanceINOUTPermissionDaysValueByAutoId($aiop_auto_id,$allow_days ){
       return AttendanceInoutPermission::where('aiop_auto_id',$aiop_auto_id)->update([
            "allow_days"=> $allow_days,
        ]);

   }


    public function getAttendanceINOUTPermissionDaysValueByAutoId($aiop_auto_id ){
         $record = AttendanceInoutPermission::where('aiop_auto_id',$aiop_auto_id)->first();
         if($record){
           return $record->allow_days;
         }else {
           return 2;
         }
    }

















}
