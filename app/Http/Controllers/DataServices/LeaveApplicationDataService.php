<?php

namespace App\Http\Controllers\DataServices;

use App\Models\LeaveApplication;
use App\Models\LeaveReason;
use App\Models\LeaveType;
use Illuminate\Support\Facades\DB;

class LeaveApplicationDataService {

    public function getLeaveTypeRecordsForDropdown(){
        return   LeaveType::get();
    }

    public function getLeaveReasonRecordsForDropdown(){
        return LeaveReason::select('lev_reas_id','lev_reas_name')->get();
    }

    public function getLeaveApplicationStatusForDropdown(){
        return DB::table('leave_status')->select('leav_sta_auto_id', 'status_title')->get();
    }



    public function checkThisRecordAlreadyExist(){
        return true;
    }

    public function insertLeaveApplicationInformation($emp_auto_id,$leave_type_id,$leave_reason_id,$leav_days,$application_date,$start_date,$end_date,$inserted_by,$app_status,$description,$reference_by,$leave_paper){
        if($this->checkThisRecordAlreadyExist()){
            return  LeaveApplication::insertGetId([
                'emp_auto_id' => $emp_auto_id,
                'leave_type_id' => $leave_type_id,
                'leave_reason_id' => $leave_reason_id,
                'leav_days' => $leav_days,
                'appl_date' => $application_date,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'inserted_by' => $inserted_by,
                'appl_status' => $app_status,
                'description' => $description,
                'reference_by'=>$reference_by,
                'leave_paper' => $leave_paper
            ]);
        }
    }

    public function updateLeaveApplicationExitPaperPath($leav_auto_id ,$file_path){

        return  LeaveApplication::where('leav_auto_id',$leav_auto_id)->update([
            'exit_paper' => $file_path ,

        ]);
    }

    public function updateLeaveApplicationInformationByAdmin($leav_auto_id,$leave_reason_id,$leave_days,$leave_start_date,$end_date,$updated_by,$app_status,$admin_comments){

            return  LeaveApplication::where('leav_auto_id',$leav_auto_id)->update([
                'leave_reason_id' => $leave_reason_id,
                'leave_start_date' => $leave_start_date,
                'end_date' => $end_date,
                'leav_days' => $leave_days,
                'approve_by' =>$updated_by,
                'updated_by' => $updated_by,
                'appl_status' => $app_status,
                'admin_comments' => $admin_comments,
            ]);
    }

    public function rejectALeaveApplication($leav_auto_id,$updated_by){
        return  LeaveApplication::where('leav_auto_id',$leav_auto_id)->update([
              'appl_status' => 2, // application rejected
              'updated_by' => $updated_by,
            ]);
    }

    public function getLeaveApplicationDetailsByLeaveAutoId($leav_auto_id){

        return DB::select('CALL getALeaveApplicationRecordDetailsByLeaveAutoId1(?)',array($leav_auto_id));

      // return LeaveApplication::where('leav_auto_id',$leav_auto_id)->first();
//        CREATE PROCEDURE `getALeaveApplicationRecordDetailsByLeaveAutoId`(IN `leav_auto_id` INT(11))
// select * from leave_applications la
// left join employee_infos ei on ei.emp_auto_id = la.emp_auto_id
// left join leave_types lt on la.leave_type_id = lt.lev_type_id
// left join leave_reasons lr on la.leave_reason_id = lr.lev_reas_id
// left join users us on la.inserted_by = us.id;

    }

    public function getLeaveApplicationPendingRecordsForLisView($branch_office_id){
        return DB::select('CALL getLeaveApplicationRecordbyMultipleApplicationStatus(?,?,?)',array(1,3,$branch_office_id));


        // return LeaveApplication::whereIn('appl_status',[1,5])  // submitted , processing and completed application
        //     ->leftjoin('employee_infos', 'leave_applications.emp_auto_id', '=', 'employee_infos.emp_auto_id')
        //     ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
        //     ->leftjoin('employee_details', 'employee_infos.emp_auto_id', '=', 'employee_details.emp_auto_id')
        //     ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
        //     ->leftjoin('office_buildings', 'employee_infos.accomd_ofb_id', '=', 'office_buildings.ofb_id')
        //     ->leftjoin('leave_reasons', 'leave_applications.leave_reason_id', '=', 'leave_reasons.lev_reas_id')
        //     ->leftjoin('leave_types', 'leave_applications.leave_type_id', '=', 'leave_types.lev_type_id')
        //     ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
        //     ->get();

    }

}
