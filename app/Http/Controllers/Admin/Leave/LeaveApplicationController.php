<?php

namespace App\Http\Controllers\Admin\Leave;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\LeaveApplicationDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Session;
use DateTime;
use Auth;

class LeaveApplicationController extends Controller
{


  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */

  public function index()
  {
    $leave_reasons = (new LeaveApplicationDataService())->getLeaveReasonRecordsForDropdown();
    $application_status = (new LeaveApplicationDataService())->getLeaveApplicationStatusForDropdown();
    return view('admin.leave.leave_application', compact('leave_reasons','application_status'));
  }

  public function getLeaveApplicationPendingList()
  {
    $leave_reasons =  (new LeaveApplicationDataService())->getLeaveReasonRecordsForDropdown();
    $application_status = (new LeaveApplicationDataService())->getLeaveApplicationStatusForDropdown();
    $records = (new LeaveApplicationDataService())->getLeaveApplicationPendingRecordsForLisView(Auth::user()->branch_office_id);
   // dd($records);
    return view('admin.leave.submitted_application_list', compact('records','leave_reasons','application_status'));
  }

  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */

  public function insert(Request $request)
  {
       // dd($request->all());

        $this->validate($request, [
          'leave_reason_id' => 'required',
          'start_date' => 'required',
          'end_date' => 'required',
          'leave_paper' => 'required',
        ], []);



        $first = new DateTime($request->start_date);
        $last = new DateTime($request->end_date);

        $leave_days = $last->diff($first);
        $leave_days = $leave_days->format('%a');
        $login_id = Auth::user()->id;
        $anEmployee = (new EmployeeDataService())->getAnEmployeeInformationWithAllReferenceTableByEmpAutoId($request->app_employee_id);

         if($anEmployee){

            $uplodedPath  = "";
            if ($request->hasFile('leave_paper')) {
              $file = $request->file('leave_paper');
              $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeLeaveApplicationPaper($file, null);

            }

            $application_id = (new LeaveApplicationDataService())->insertLeaveApplicationInformation($anEmployee->emp_auto_id,1,$request->leave_reason_id,$leave_days,$request->app_date
            ,$request->start_date,$request->end_date,$login_id,$request->application_status,$request->remarks,$request->reference_by,$uplodedPath);

            if ($application_id) {
              Session::flash('success', 'Application Successfully Submitted');
              return redirect()->back();
            } else {
              Session::flash('error', 'Error Found, Try again');
              return redirect()->back();
            }

        }


  }

  public function updateALeaveApplicationRecord(Request $request)
  {

    try{

           // dd($request->all());
            $first = new DateTime($request->leave_start_date);
            $last = new DateTime($request->end_date);
            $leave_days = $last->diff($first);
            $leave_days = $leave_days->format('%a');
            $login_id = Auth::user()->id;

            $uplodedPath  = "";
            if ($request->hasFile('exit_paper')) {
              $file = $request->file('exit_paper');
              $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeLeaveApplicationPaper($file, null);
              (new LeaveApplicationDataService())->updateLeaveApplicationExitPaperPath($request->modal_leave_auto_id,$uplodedPath);
            }

            $application_id = (new LeaveApplicationDataService())->updateLeaveApplicationInformationByAdmin($request->modal_leave_auto_id,$request->leave_reason_id,$leave_days
            ,$request->leave_start_date,$request->end_date,$login_id,$request->application_status,$request->admin_comments);

            if ($application_id) {
              Session::flash('success', 'Successfully Updated');
              return redirect()->back();
            } else {
              Session::flash('error', 'Error Found, Try again');
              return redirect()->back();
            }

    }catch(Exception $ex){
        Session::flash('error', 'Error Found, Try again');
        return redirect()->back();
    }

  }

  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */
  public function getALeaveApplicationRecord(Request $request){
    try{

      $record = (new LeaveApplicationDataService())->getLeaveApplicationDetailsByLeaveAutoId((int)$request->leav_auto_id);
      if(count($record)){
        $record = $record[0];
      }
      return response()->json(['success'=>true,'status'=>200,'message'=>'',"data"=>$record,'a'=>$request->leav_auto_id]);

    }catch(Exception $ex){
      return response()->json(['success'=>false,'status'=>404,'message'=>'Operation Failed, Please Try Again','error'=>"error"]);

    }
  }

  public function rejectALeaveApplication($leav_auto_id){

    try{

      $isSuccess = (new LeaveApplicationDataService())->rejectALeaveApplication((int)$leav_auto_id,Auth::user()->id);
      if($isSuccess){
        return response()->json(['success'=>true,'status'=>200,'message'=>'Successfully Deleted',"error"=>'error','a'=>$leav_auto_id]);
      }else{
        return response()->json(['success'=>false,'status'=>404,'message'=>'Delete Operation Failed, Please Try Again',"error"=>'error','a'=>$leav_auto_id]);
      }

    }catch(Exception $ex){
      return response()->json(['success'=>false,'status'=>404,'message'=>'Operation Failed, Please Try Again','error'=>"error"]);

    }
  }









  /* ======================================================================== */
}
