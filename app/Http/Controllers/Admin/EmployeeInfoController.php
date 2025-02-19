<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EmpWorkActivityRatingEnum;
use App\Models\Enum\JobStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use App\Http\Controllers\DataServices\AccommodationDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Requests\NewEmpFormRequest;
use App\Models\EmpContactPerson;
use App\Models\EmpJobExperience;
use Illuminate\Http\Request;
use App\Models\EmployeeInfo;
use App\Models\Religion;
use App\Models\JobStatus;
use App\Models\EmployeeCategory;
use App\Models\Sponsor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


use function GuzzleHttp\Promise\all;

// ALTER TABLE `employee_infos` ADD COLUMN `emp_rating` int not null DEFAULT 0;
// ALTER TABLE `employee_infos` ADD COLUMN `salary_status` int not null DEFAULT 1;



class EmployeeInfoController extends Controller
{

    function __construct()
    {

        $this->middleware('permission:employee-add|add-salary-info', ['only' => ['add','insert','addSalaryDetails','']]);
        $this->middleware('permission:employee-list', ['only' => ['index','edit','updateEmployeeInformationData']]);
        $this->middleware('permission:employee-search', ['only' => ['loadSearchingUIForAnEmployeeDetailsByMultiTypeParameter']]);
        $this->middleware('permission:employee-status', ['only' => ['searchEmpStatus']]);
        $this->middleware('permission:multiple-employee-transfer', ['only' => ['multipleEmployeeTransferForm','multipleEmployeeTransferFormSubmit']]); // multiple emp tranfer
        $this->middleware('permission:employee_all_information_update', ['only' => ['searchEmpForUpdate','updateEmployeeAllInformation']]);  // update all including salary detail
        $this->middleware('permission:job-approve', ['only' => ['loadNewEmployeeApprovalPendingListWithUI','approvalOfNewEmployeeInsertion']]);
        $this->middleware('permission:employee_job_status_change_activity', ['only' => ['loadNewEmployeeApprovalPendingListWithUI','approvalOfNewEmployeeInsertion']]);
        $this->middleware('permission:employee_working_shift_update', ['only' => ['loadEmployeeWorkingShiftStatusUpdateUI','']]); // Employee WOrking Shift Update
        $this->middleware('permission:report-employee-list-project', ['only' => ['projectWiseEmployeeList']]); // Employee List Report Projectwise
        $this->middleware('permission:hr_report_employee_information', ['only' => ['loadHRRelatedEmployeeReportForm']]); // HR Section Report

    }

    public function searchingEmployeeAjaxRequestForAutoCompleteTextInput($empoyee_id)
    {
        $data = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpId($employee_id);
        return json_encode(['status'=>200,'success'=>true,'data'=>$data]);

    }

    public function getEmpCategory($emp_type_id)
    {
        $getEmpCatg = EmployeeCategory::where('emp_type_id', $emp_type_id)->where('catg_status', 1)->get();
        return json_encode($getEmpCatg);
    }


     /*
  |--------------------------------------------------------------------------
  |  FROM BLADE FILE REQUEST OPERATION
  |--------------------------------------------------------------------------
    */


    // ============== insert Employee Information in DATABASE ==============
    public function insert(Request $request)
    {
        $this->validate($request, [

            'emp_name' => 'required|string|max:45',
            'akama_no' => 'required|string|max:10|unique:employee_infos',
            'passfort_no' => 'required|string|max:40|unique:employee_infos',
            'mobile_no' => 'required|max:20',
            'akama_expire' => 'required',
            'passfort_expire_date' => 'required',
            'sponsor_id' => 'required',
            'project_id' => 'required',

          ], [
            'emp_name.required' => 'please enter employee name!',
          ]);


      //  dd($request->all());
        $creator = Auth::user()->id;
        $emp_auto_id = (new EmployeeDataService())->insertNewEmployee($request);
        if ($emp_auto_id > 0) {

            (new EmployeeDataService())->addEmployeeSalaryDetails($emp_auto_id, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            (new EmployeeDataService())->insertEmployeeDetailsInformation($emp_auto_id,$request->department_id, 0, $request->religion, 0,$request->country_phone_no, $request->agency,
            $request->gender, $request->maritus_status,$request->blood_group, $request->present_address,  $request->ref_employee_id, $request->remarks);

            if ($request->project_id == '' || $request->project_id == null) {
                $request->project_id = 0;
            }

            // need to check server
            (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $request->project_id, Carbon::now(), null, $creator,"");

            // profile photo
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeProfilePhoto($file, null);
                (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'profile_photo');
            }

            // Iqama File
            if ($request->hasFile('akama_photo')) {
                $file = $request->file('akama_photo');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeIqamaFile($file, null);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'akama_photo');
            }

            // pasfort_photo upload
            if ($request->hasFile('pasfort_photo')) {
                $file1 = $request->file('pasfort_photo');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeePassportFile($file1, null);
                (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'pasfort_photo');
            }

            if ($request->hasFile('covid_certificate')) {
                $file3 = $request->file('covid_certificate');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeCOVIDCertificateFile($file3, null);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'covid_certificate');
            }

            if ($request->hasFile('appoint_latter')) {
                $file3 = $request->file('appoint_latter');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeAppointmentLetterFile($file3, null);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'employee_appoint_latter');
            }

            if ($request->hasFile('educational_papers')) {
                $file3 = $request->file('educational_papers');
                $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeEducationalDocuments($file3, null);
                $update = (new EmployeeDataService())->updateEmployeeEducationalDocumentsPath($emp_auto_id, $uplodedPath, 'educational_papers');
            }

            return Redirect()->route('add-salary-info', [$emp_auto_id]);
        } else {
            Session::flash('error', 'Employee ID Already Assignedz, Please Reload and Try Again');
            return redirect()->back();
        }
    }


    // === Update Employee Basic & Salary Information ===
    public function updateEmployeeAllInformation(Request $request)
    {
        $update_by = Auth::user()->id;

        if((int)$request->operation_type == 2){

        }
        else{
            // update all information
            $emp_auto_id = $request->id;

          //  dd($request->all(),$request['staff_employee']);
          //  dd($emp_auto_id,$request->staff_employee);

            $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
            if(! $employee){
                Session::flash('error', 'Employee Not Found ');
                return redirect()->back();
            }
            $request->project_id = (int) $request->projectStatus;
            $request->emp_auto_id = (int) $request->id;
            $update = (new EmployeeDataService())->updateAnEmployeeAllInformationWithSalaryDetails($request,$update_by);
            if ($employee->project_id != $request->projectStatus) {
                $insert = (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $request->project_id, $request['asign_date'], $request['asign_date'], $update_by);
            }

            // if ($employee->job_status != $request->EmpStatus_id) {
            //     (new EmployeeRelatedDataService())->insertEmployeeJobStatusUpdateRecord($emp_auto_id, Carbon::now(), $request['EmpStatus_id']);
            // }

            (new EmployeeDataService())->updateEmployeeAsStaff((int)$emp_auto_id,$request->staff_employee);

            if ($update) {
                $salary_amount = $request->basic_amount > 0 ? $request->basic_amount : $request->hourly_rent;
                    // login user activities record
                (new AuthenticationDataService())->InsertLoginUserActivity(2,2, $update_by,$emp_auto_id,$salary_amount);

                Session::flash('success', 'Successfully Updated');
                return Redirect()->back();
            } else {
                Session::flash('error', 'Update Operation Failed. Please Try Again');
                return redirect()->back();
            }
        }

    }
    // === Update Employee Basic Information without file by edit button ===
    public function updateEmployeeInformationData(Request $request)
    {
        //dd($request);
        $update = (new EmployeeDataService())->updateEmployeeAllInformation($request);

        if ($update) {
            // login user activities record
             (new AuthenticationDataService())->InsertLoginUserActivity(4,2,Auth::user()->id, $request->id,null);

            Session::flash('success_update', 'value');
            return Redirect()->route('employee-list');
        } else {
            Session::flash('error', 'value');
            return redirect()->back();
        }
    }

    // === UI Request for Upload Employee File/Image and update database url===
    public function updateEmployeeUploadedFileImage(Request $request)
    {
        $emp_auto_id = $request->emp_auto_id;
        $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoIdForEditEmployeeInformation($emp_auto_id);

        $update = false;
        if(is_null($anEmployee)){
            Session::flash('error', 'Employee Not Found');
           return redirect()->back();
         }
        // profile photo
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $uplodedPath = (new  UploadDownloadController())->uploadEmployeeProfilePhoto($file, $anEmployee->profile_photo);
            $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'profile_photo');
        }
        // pasfort_photo upload
        if ($request->hasFile('pasfort_photo')) {
            $file = $request->file('pasfort_photo');
            $uplodedPath =  (new  UploadDownloadController())->uploadEmployeePassportFile($file, $anEmployee->pasfort_photo);
            $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'pasfort_photo');
        }
        // Iqama File
        if ($request->hasFile('akama_photo')) {
            $file = $request->file('akama_photo');
            $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeIqamaFile($file, $anEmployee->akama_photo);
            $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'akama_photo');
        }

        // covid_certificate
        if ($request->hasFile('covid_certificate')) {
            $file = $request->file('covid_certificate');
            $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeCOVIDCertificateFile($file, $anEmployee->covid_certificate);
            $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'covid_certificate');
        }

        // Medical Report
        if ($request->hasFile('medical_report')) {
            $file = $request->file('medical_report');
            $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeMedicalReportFile($file, $anEmployee->medical_report);
            $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'medical_report');
        }

        // appoint letter update

        if ($request->hasFile('appoint_latter')) {
            $file = $request->file('appoint_latter');
            $uplodedPath =  (new  UploadDownloadController())->uploadEmployeeAppointmentLetterFile($file, $anEmployee->employee_appoint_latter);
            $update =  (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'employee_appoint_latter');
        }

        if ($update) {
              // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(23,2, Auth::user()->id,$emp_auto_id,null);

            Session::flash('success_update_image', 'value');
            return Redirect()->route('employee-list');
        } else {
            Session::flash('error', 'value');
            return redirect()->back();
        }
    }

    // === Updated Employee Job Status ===
    public function approvalOfNewEmployeeInsertion($id)
    {
        //  1 = Active Employee
        $appoval = (new EmployeeDataService())->approvalOfInsertedNewEmployee($id, JobStatusEnum::Active, Auth::user()->id);
        if ($appoval) {
            Session::flash('approve', 'value');
            return redirect()->back();
        } else {
            Session::flash('error', 'value');
            return redirect()->back();
        }
    }

    // Need check more specific way =================================================
    public function getProjectAndEmpTypeWisePaidUnpaidSalaryReport(Request $request)
    {
        $company = (new CompanyDataService())->findCompanryProfile();

        $report_title[0] = "All Project";
        if ($request->proj_id != null) {
            $project = $empRelatedDSObj->findAProjectInformation($request->proj_id)->proj_name;
            $report_title[0] = " " . $project;
        }

        $empType = $request->emp_type_id;
        $hourly_employee = null;

        if ($empType == 0) {   // All Employee
            $report_title[1] = 'Employee Type: All';
            $hourly_employee = NULL;

            $emp_salary_list = EmployeeInfo::with('project', 'status')
                ->where('employee_infos.job_status', 1)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($empType == -1) {  // Basic Salary Employee
            $hourly_employee = null;
            $empType = 1;
            $report_title[1] = 'Employee Type: Direct (Basic Salary)';

            $emp_salary_list = EmployeeInfo::with('project', 'status')
                ->where('employee_infos.emp_type_id', $empType)
                ->where('employee_infos.job_status', 1)
                ->where('employee_infos.hourly_employee', $hourly_employee)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($empType == 1) { // Hourly Salary Employee

            $hourly_employee = true;
            $report_title[1] = 'Employee Type: Direct (Hourly)';

            $emp_salary_list = EmployeeInfo::with('project', 'status')
                ->where('employee_infos.emp_type_id', $empType)
                ->where('employee_infos.job_status', 1)
                ->where('employee_infos.hourly_employee', $hourly_employee)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        } else if ($empType == 2) { // Hourly Salary Employee

            $hourly_employee = true;
            $report_title[1] = 'Employee Type: Direct (Hourly)';

            $emp_salary_list = EmployeeInfo::with('project', 'status')
                ->where('employee_infos.emp_type_id', $empType)
                ->where('employee_infos.job_status', 1)
                ->where('employee_infos.hourly_employee', $hourly_employee)
                ->leftjoin('salary_details', 'employee_infos.emp_auto_id', '=', 'salary_details.emp_id')
                ->orderBy('employee_id', 'ASC')
                ->get();
        }
        // $report_title ="";
        if ($emp_salary_list->count() > 0) {
            return view('admin.employee-info.project_wise.report', compact('emp_salary_list', 'company', 'report_title'));
        } else {
            Session::flash('error', 'value');
            return redirect()->back();
        }
    }
    // Need check more specific way =================================================

    public function tradeWiseEmployeeListProcess(Request $request)
    {
        $catg_id = $request->catg_id;
        $emp_type_id = $request->emp_type_id;

        if ($emp_type_id == -1 && $catg_id == 0) {
            Session::flash('error', 'value');
            return redirect()->back();
        }
        $empTypeName = "All";
        $company = (new CompanyProfileController())->findCompanry();
        $tradeName =  "All";
        $employee = null;

        if ($emp_type_id == -1 && $catg_id >= 1) { // no type but trade
            $empTypeName = "All";
            $employee = (new EmployeeDataService())->getEmployeeListByEmpCategoryId($catg_id);
            $tradeName = EmployeeCategory::where('catg_id', $request->catg_id)->pluck('catg_name');
        } else if ($emp_type_id >= 0 && $catg_id == 0) {  // seleced type but no trade

            if ($emp_type_id == 0) {
                $empTypeName = "Direct Employee(Basic Salary)";
                $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp(-1, 1, null);
                // $employee = EmployeeInfo::where('emp_type_id', 1)->where('hourly_employee', null)->orderBy('employee_id', 'ASC')->get();
            } else if ($emp_type_id == 1) {

                $empTypeName = "Direct Employee(Hourly)";
                $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp(-1, 1, 1);
                // $employee = EmployeeInfo::where('emp_type_id', 1)->where('hourly_employee', 1)->orderBy('employee_id', 'ASC')->get();
            } else {
                $empTypeName = (new EmployeeRelatedDataService())->getAnEmployeeTypeName($request->emp_type_id);
                $employee = EmployeeInfo::where('emp_type_id', $emp_type_id)->orderBy('employee_id', 'ASC')->get();
            }
        } else {  // selected both menu

            $tradeName = EmployeeCategory::where('catg_id', $request->catg_id)->pluck('catg_name');

            if ($emp_type_id == 0) {
                $empTypeName = "Direct Employee(Basic Salary)";
                // $employee = EmployeeInfo::where('designation_id', $catg_id)->where('emp_type_id', 1)->where('hourly_employee', null)->orderBy('employee_id', 'ASC')->get();
                $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp($catg_id, 1, null);
            } else if ($emp_type_id == 1) {

                $empTypeName = "Direct Employee(Hourly)";
                $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp($catg_id, 1, 1);
                // $employee = EmployeeInfo::where('designation_id', $catg_id)->where('emp_type_id', 1)->where('hourly_employee', 1)->orderBy('employee_id', 'ASC')->get();
            } else {
                $empTypeName = (new EmployeeRelatedDataService())->getAnEmployeeTypeName($request->emp_type_id);
                $employee = (new EmployeeDataService())->getEmployeeListByCategoryIdEmpTypeAndHourlyEmp($catg_id, $emp_type_id, null);
                // $employee = EmployeeInfo::where('designation_id', $catg_id)->where('emp_type_id', $emp_type_id)->orderBy('employee_id', 'ASC')->get();
            }
        }

        if ($employee != NULL) {
            return view('admin.employee-info.trade_wise.report', compact('employee', 'company', 'tradeName', 'empTypeName'));
        } else {
            Session::flash('error', 'value');
            return redirect()->back();
        }
    }

    public function sponserWiseEmployeeListProcess(Request $request)
    {
        $company = (new CompanyProfileController())->findCompanry();
        $sponsor = Sponsor::where('spons_id', $request->spons_id)->pluck('spons_name');
        $employee = (new EmployeeDataService())->getEmployeeListWithProjectAndSponsor(0, $request->spons_id);
        // EmployeeInfo::where('sponsor_id', $request->spons_id)->orderBy('employee_id', 'ASC')->get();

        if ($employee != NULL) {
            return view('admin.employee-info.sponser_wise.report', compact('employee', 'company', 'sponsor'));
        } else {
            Session::flash('error', 'value');
            return redirect()->back();
        }
    }

    // Find An Employee update
    public function findEmployeeForUpdate(Request $request)
    {

            $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->searchValue,$request->searchType,Auth::user()->branch_office_id);
            if(count($employee) == 0){
                return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=> "Employee Not Found"]);
            }
            else if(count($employee) >1){
                return response()->json(["status" =>403, "success" => false, 'error' => 'error','message'=>  "Multiple Employee Found"]);
            }
            $employee =  $employee[0];
            $allCountry =  (new EmployeeRelatedDataService())->getAllCountry();
            $allDistrict = (new DistrictController())->getAllDistrict();
            $allDivision = (new EmployeeRelatedDataService())->getDivisions(null);
            $allEmpType = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
            $allDepartment = (new EmployeeRelatedDataService())->getAllDepartment();
            $allSponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
            $getAllProject = (new ProjectDataService())->getAllActiveProjectListForDropdown();
            $allEmployeeStatus = (new HelperController())->getEmployeeStatus();
            $designation = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
            $find_job_experience = EmpJobExperience::where('emp_id', $employee->emp_auto_id)->get();
            $find_emp_contact_person = EmpContactPerson::where('emp_id', $employee->emp_auto_id)->get();

            return json_encode([
                'success' => true,
                'status' => 200,
                'allCountry' => $allCountry,
                'allDistrict' => $allDistrict,
                'allSponsor' => $allSponsor,
                'allDepartment' => $allDepartment,
                'allEmpType' => $allEmpType,
                'allDivision' => $allDivision,
                'allEmployeeStatus' => $allEmployeeStatus,
                'getAllProject' => $getAllProject,
                'find_job_experience' => $find_job_experience,
                'find_emp_contact_person' => $find_emp_contact_person,
                'findEmployee' => $employee,
                'designation' => $designation,
            ]);

    }

    // ========###### employee list end #####=========
    public function updateAnEmployeeJobStatus(Request $request)
    {
        $emp_auto_id = $request->emp_auto_id;
        $emp_job_status = $request['empStatus'];
        $update = (new EmployeeDataService())->updateEmployeeJobStatus($emp_auto_id, $emp_job_status);
        (new EmployeeRelatedDataService())->insertEmployeeJobStatusUpdateRecord($emp_auto_id, Carbon::now(), $emp_job_status);

        if ($update) {
            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        } else {
            Session::flash('error', 'Please try again');
            return redirect()->back();
        }
    }

    // Employee Working Project Information Update
    public function udpateEmployeeWorkingProject(Request $request)
    {
        $emp_auto_id = $request->emp_auto_id;
        $project_id = $request->projectStatus;
        $assign_date = $request->date;

        if ($project_id == null) {
            Session::flash('error', 'Operation Failed, Please try again');
            return redirect()->back();
        }
        $update = (new EmployeeDataService())->updateEmployeeAssignedProject($emp_auto_id, $project_id);
        if ($update) {
            (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $project_id, $assign_date, $assign_date, Auth::user()->id);

            Session::flash('success', 'Successfully Updated Project Info');
            return redirect()->back();
        } else {
            Session::flash('error', 'Operation Failed, Please try again');
            return redirect()->back();
        }
    }

    public function udpateEmployeeSponsorInformations(Request $request){

        $emp_auto_id = $request->emp_auto_id;
        $prev_spons_id = $request->emp_prev_sponsor_id;
        $curnt_spons_id = $request->emp_current_sponsor;

        if ($curnt_spons_id == null) {
            Session::flash('error', 'Operation Failed, Please try again');
            return redirect()->back();
        }

        $update = (new EmployeeDataService())->updateEmployeeSponsorInfo($emp_auto_id, $curnt_spons_id);
        if ($update) {
             // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(7,2, Auth::user()->id, $emp_auto_id,null);

            (new EmployeeRelatedDataService())->insertNewEmployeeSponsorHistoryInfo($emp_auto_id, $prev_spons_id, $curnt_spons_id);
            Session::flash('success', 'Successfully Updated Sponsor Info');
            return redirect()->back();
        } else {
            Session::flash('error', 'Operation Failed, Please try again');
            return redirect()->back();
        }
    }

    // Search EMployee by Emp ID and Update Employee Trade/Designation Or Employee Multi Expert Trade/Designation Update
    public function updateAnEmployeeDesignationAndMultipleTradeExpertness(Request $request)
    {
        $emp_auto_id = $request->emp_auto_id;
        $emplDesig_id = $request->emplyoeeDesignation;
        $empMultiDesignations = $request->empMultiExptDesignation;



        if (is_null($emp_auto_id) || ($emplDesig_id == null && $empMultiDesignations == null && $empWorkActivityRating == null)) {
            Session::flash('error', 'Please Select Employee Trade Name');
            return redirect()->back();
        } else {
            if ($emplDesig_id != null) {
                (new EmployeeDataService())->updateEmployeeDesignationStatus($emp_auto_id, $emplDesig_id);
            }
            if ($empWorkActivityRating != null) {
                (new EmployeeDataService())->updateAnEmployeeWorkRatingInfo($request->emp_auto_id, $request->empWorkActivityRating);
            }

            if ($empMultiDesignations != null) {
                foreach ($empMultiDesignations as $trade_id) {
                    (new EmployeeDataService())->insertAnEmpMultipleTradeExpertnessInformation($emp_auto_id, $trade_id, Auth::user()->id);
                }
            }

            // login user activities record
            (new AuthenticationDataService())->InsertLoginUserActivity(11,2, Auth::user()->id, $emp_auto_id,null);


            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        }
    }
    // update employee Agency and Reference Person Information
    public function updateEmployeeAgencyInformation(Request $request)
    {
        $emp_auto_id = $request->emp_auto_id;
        $agency_id = $request->emplyoeeAgency;
        $ref_person_info = $request->ref_employee_id;
        $ref_contact_no = $request->ref_contact_no;
        $remarks = $request->remarks;

        if($agency_id != null &&  $emp_auto_id  != null){
             (new EmployeeDataService())->updateEmployeeAgencyInfo($emp_auto_id, $agency_id);
              // login user activities record
             (new AuthenticationDataService())->InsertLoginUserActivity(8,2, Auth::user()->id, $emp_auto_id,null);

        }
        if($ref_person_info != null &&  $emp_auto_id  != null ){
           (new EmployeeDataService())->updateAnEmployeeReferencePersonInformation($emp_auto_id, $ref_person_info,$ref_contact_no,$remarks);
        }
        // if ($update) {
            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        // } else {
        //     Session::flash('error', 'Please try again');
        // }
    }
    // Employee Compnay Information Update
    public function updateEmployeeCompanyInformation(Request $request)
    {
        $emp_auto_id = $request->emp_auto_id;
        $company_id = $request->company_id;

        $update = (new EmployeeDataService())->updateEmployeeCompanyInfo($emp_auto_id, $company_id);
        if ($update) {
            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        } else {
            Session::flash('error', 'Please try again');
        }
    }
    // Employee Iqama and Passport number and file update
    public function updateEmployeeIqamaInformations(Request $request)
    {

        $emp_auto_id = $request->emp_auto_id;
        $iqamaNo = $request->akama_no_up;
        $passport = $request->passport_no_up;
        $isThisIqamaExist = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($iqamaNo);
        $isThisPassportExist = (new EmployeeDataService())->getAnEmployeeInfoByEmpPassportNo($passport);

        if ($isThisIqamaExist) {
            $isThisIqamaExist = true;
        }else {
            $isThisIqamaExist = false;
            $anEmployee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
            if ($request->hasFile('akama_photo')) {
                $file = $request->file('akama_photo');
                $uplodedPath = (new  UploadDownloadController())->uploadEmployeeIqamaFile($file, $anEmployee->akama_photo);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath, 'akama_photo');
                Session::flash('success', 'Successfully Updated');
            }
            if ($iqamaNo != null) {
                (new EmployeeDataService())->updateEmployeeIqamaReletedInfo($emp_auto_id, $iqamaNo, $request->akama_expire);
                // login user activities record
                (new AuthenticationDataService())->InsertLoginUserActivity(5,2, Auth::user()->id, $emp_auto_id,null);

            }
        }
        if ($isThisPassportExist) {
            $isThisPassportExist = true;
        }else{
            $isThisPassportExist = false;
            if ($request->hasFile('passport_file')) {
                $file1 = $request->file('passport_file');
                $uplodedPath1 = (new  UploadDownloadController())->uploadEmployeePassportFile($file1, $anEmployee->pasfort_photo);
                $update = (new EmployeeDataService())->updateEmployeeUploadedFileDbPath($emp_auto_id, $uplodedPath1, 'pasfort_photo');
                Session::flash('success', 'Successfully Updated');
            }
            if ($passport != null) {
                (new EmployeeDataService())->updateEmployeePassportInformation($emp_auto_id,   $passport);
                 // login user activities record
                (new AuthenticationDataService())->InsertLoginUserActivity(6,2, Auth::user()->id, $emp_auto_id,null);

            }
        }
        if($isThisIqamaExist && $isThisPassportExist){
            Session::flash('error', 'Passport and Iqama Number both are Exist ');
            return redirect()->back();
        }
        else if($isThisIqamaExist){
            Session::flash('error', 'Iqama Number Already Exist');
            return redirect()->back();
        }else if($isThisPassportExist){
            Session::flash('error', 'Passport Number Already Exist');
            return redirect()->back();
        }
        Session::flash('success', 'Successfully Updated');
        return redirect()->back();

    }


    // Employee Accomodation Information Update
    public function updateEmployeeAccomodationInformations(Request $request)
    {

        if (is_null($request->mobile_no_up) && is_null($request->phone_no_up) &&  is_null($request->country_phone_no)  && is_null($request->emplyoeeAccommodationBuiling)) {
            Session::flash('error', 'Please Input Employee Contact Mobile Information');
            return redirect()->back();
        }

        if (is_null($request->mobile_no_up) == false  || is_null($request->phone_no_up) == false ){
             (new EmployeeDataService())->updateAnEmployeeMobileNumber($request->emp_auto_id, $request->mobile_no_up, $request->phone_no_up);

        }
        if (is_null($request->country_phone_no) == false ){
            (new EmployeeDataService())->updateAnEmployeeHomeCountryContactNumber($request->emp_auto_id, $request->mobile_no_up, $request->phone_no_up);

        }
        if (is_null($request->emplyoeeAccommodationBuiling) == false ){
            (new EmployeeDataService())->updateAnEmployeeAccommodationVilla($request->emp_auto_id, $request->emplyoeeAccommodationBuiling);

        }


            Session::flash('success', 'Successfully Updated');
            return redirect()->back();

    }

    // Employee Work Rating Information Update
    public function updateEmployeeWorkRatingInformations(Request $request)
    {
        if ($request->empWorkActivityRating == null) {
            Session::flash('error', 'Please try again');
            return redirect()->back();
        }
        $update = (new EmployeeDataService())->updateAnEmployeeWorkRatingInfo($request->emp_auto_id, $request->empWorkActivityRating);
        if ($update) {
            Session::flash('success', 'Successfully Updated');
            return redirect()->back();
        } else {
            Session::flash('error', 'Please try again');
        }
    }

    // Employee Activity Remarks/Comments Add
    public function updateEmployeeActivityRemarks(Request $request){
        if ($request->emp_act_remarks == null) {
            $request->emp_act_remarks ="";
        }
        $update = (new EmployeeDataService())->updateAnEmployeeActivityRemarks($request->emp_auto_id, $request->emp_act_remarks);

        if ($update) {
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Successfully Updated .',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Something Wrong, Please Try Again.',
            ]);
        }
    }

    public function preRelease(Request $request)
    {
        $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, JobStatusEnum::Release);
        // 5 = job status release
        return view('admin.salary-generate.pending-salary', compact('pendingSalary'));
    }

    public function preReleaseUpdateStatus($id)
    {
        $update = (new EmployeeDataService())->updateEmployeeJobStatus($id, JobStatusEnum::Vacation);
        $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, JobStatusEnum::PreRelease);
        // 4 = prerelease job status

        if ($update) {
            Session::flash('success', 'successfuly update employee status');
            return redirect()->back();
        } else {
            Session::flash('error', 'Please try again');
            return redirect()->back();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE WORK SHIFT STATUS PROCESS
    |--------------------------------------------------------------------------
    */
    public function loadEmployeeWorkingShiftStatusUpdateUI()
    {
        $project = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);
        return view('admin.employee-in-out.employee-work-shift-status', compact('project'));
    }

    // Signle Employee WOrking Shift Status Update Ajax Request
    public function updateEmployeeWorkShiftingDataChangeRequest(Request $request)
    {
        try{
            $isUpdate = false;
            $anEmp = (new EmployeeDataService())->getAnEmployeeInfoTableDataByEmployeeIdAndBranchOfficeId($request->employee_id,Auth::user()->branch_office_id);
            if ($anEmp) {
                $isUpdate = (new EmployeeDataService())->updateEmployeeWorkingShiftStatusByEmployeeAutoId($anEmp->emp_auto_id, $request->working_shift);
                return response()->json(['status' => 200,'success'=>true, 'error' =>null,'message'=> "Successfully Updated"]);
            }else{
                return response()->json(['status' => 404,'success'=>false, 'error' => 'error','message'=> "Employee Not Found"]);
            }

        }catch(Exception $ex){
            return response()->json(['status' => 404,'success'=>false, 'error' => 'error','message'=> "System Operation Failed, Please Reload and try Again"]);
        }
    }

    // Multiple Employee working shift  Update request
    public function employeeShiftStatusUpdateRequest(Request $request)
    {

        if (!$request->has('emp_auto_id')) {
            Session::flash('error', 'Operation Failed, Please Try Again.');
            return redirect()->back();
        }
        $allEmpList = $request->emp_auto_id;
        $update = false;
        foreach ($allEmpList as $emp_auto_id) {
            if ($request->has('emp_work_night_shift-' . $emp_auto_id)) {
                $update = (new EmployeeDataService())->updateEmployeeWorkingShiftStatusByEmployeeAutoId($emp_auto_id, 1);
                 // 1 night shift
                // employeeWorkingShiftUpdateWithNight($emp_auto_id);
            } else {
                $update = (new EmployeeDataService())->updateEmployeeWorkingShiftStatusByEmployeeAutoId($emp_auto_id, 0); // 0 = day shift
            }
        }
        if ($update) {
            Session::flash('success', 'Successfully Updated Employee Working Shift Status');
            return redirect()->route('employee.shift-status-update-ui');
        } else {
            Session::flash('error', 'Some Operation Failed, Please Try Again.');
            return redirect()->back();
        }
    }

    /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
    public function index()
    {
        $total_active_emp = (new EmployeeDataService())->countTotalNumberOfEmployeesInABranchOffice(1,Auth::user()->branch_office_id); // 1 = active employee
        return view('admin.employee-info.index', compact(  'total_active_emp'));
    }

    // Searching Employee From Emp List Page
    public function searchEmployeeByProjectSponerAndEmpID(Request $request)
    {
        if ($request->employee_id == null) {
            return $this->index();
        }
        $projectlist = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        $sponserList = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        $anEmp = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsJoinQueryByEmpId($request->employee_id);

        $all = array();
        if ($anEmp != null) {
            $all[0] = $anEmp;
        }
        // 2000 = page limit, 1 = active employee
        $allActive = (new EmployeeDataService())->countTotalEmployees(1); // 1 = active employee

        return view('admin.employee-info.index', compact('all', 'projectlist', 'sponserList', 'allActive'));
    }

    public function  loadNewEmployeeApprovalPendingListWithUI()
    {
        $empTypes = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $all = (new EmployeeDataService())->getListOfNewEmployeesThoseAreWaitingForApproval(-1, JobStatusEnum::ApprovalPending,Auth::user()->branch_office_id);
        // -1 = no limit, 0 = approval pending emlpoyee
        return view('admin.employee-info.job-approve', compact('all', 'empTypes'));
    }

    public function preReleaseList()
    {
        $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, JobStatusEnum::PreRelease);
        return view('admin.employee-info.pre-release', compact('all'));
    }

    public function releaseList()
    {
        $all = (new EmployeeDataService())->getAllEmployeesInformation(-1, JobStatusEnum::Vacation);
        return view('admin.employee-info.release', compact('all'));
    }

    // Multtype parameter Base Employee Searching UI
    public function loadSearchingUIForAnEmployeeDetailsByMultiTypeParameter()
    {
        return view('admin.employee-info.search-emp');
    }

    // return UI for Employee All Information Update
    public function searchEmpForUpdate()
    {
        return view('admin.employee-info.emp-update');
    }

    public function multipleEmployeeTransferForm()
    {
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        return view("admin.employee-info.employee-transfer", compact("projects"));
    }

    public function multipleEmployeeTransferFormSubmit(Request $request)
    {
        $emp_list = $request->emp_auto_id;

        $creator = Auth::user()->id;
        $asign_date = $request->asign_date;

        if ($request['assigned_project'] == null) {
            Session::flash('error', 'Please Select Any Project For Assign This Employee!');
            return redirect()->back();
        } else {
            foreach ($emp_list as $emp_auto_id) {
                if ($request->has('emp_transfer_checkbox-' . $emp_auto_id)) {

                    (new EmployeeDataService())->updateEmployeeAssignedProject($emp_auto_id, $request['assigned_project']);
                    (new EmployeeRelatedDataService())->assignEmployeeToNewProject($emp_auto_id, $request['assigned_project'], $asign_date, $asign_date, $creator,$request->remarks);
                }
            }

            Session::flash('success', 'Successfully Transfered to new Project');
            return redirect()->back();
        }
    }

    // Load User Interface for Employee Trade , Accommodation, Rating, Project Update
    public function searchEmpStatus()
    {

      //  dd(100);
        $empWorkRating = EmpWorkActivityRatingEnum::cases();
        $designationList =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        return view('admin.employee-info.search-status', compact('empWorkRating', 'designationList'));
    }

    public function getReligion()
    {
        return $all = Religion::get();
    }

    // New Employee Information Entry User Interface
    public function add()
    {
        //             $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($request->employee_id,'employee_id',Auth::user()->branch_office_id);

        $designationList =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $countryList =  (new EmployeeRelatedDataService())->getAllCountry();
        $empTypes = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $allDepart = (new EmployeeRelatedDataService())->getAllDepartment();
        $empIdGeneret = (new EmployeeDataService())->generateEmployeeId();

        $relig = $this->getReligion();
        $proj = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
        $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        $agencies = (new EmployeeRelatedDataService())->getAgencyInformationForDropdownList();
        $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
        // 2000 = page limit, 1 = active employee
        return view('admin.employee-info.add', compact('designationList', 'sponsor', 'proj', 'relig', 'countryList', 'empTypes', 'allDepart', 'empIdGeneret', 'agencies', 'accomdOfficeBuilding'));
    }
   // Load Salary Details UI
    public function addSalaryDetails($emp_auto_id)
    {
        $employee = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
        return view('admin.employee-info.add-salary-info', compact('emp_auto_id', 'employee'));
    }

    public function edit($emp_auto_id)
    {
       // dd(100);
        Cache::flush();
        cache()->flush();

        $edit = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoIdForEditEmployeeInformation($emp_auto_id);
        if($edit == null){
           return 'Update Permission Denied, You Selected an Inactive Employee';
        }
        $designationList =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $countryList =  (new EmployeeRelatedDataService())->getAllCountry();
        $empTypes = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();
        $allDepart = (new EmployeeRelatedDataService())->getAllDepartment();

        $agencies = (new EmployeeRelatedDataService())->getAgencyInformationForDropdownList();
        $relig = $this->getReligion();
        $proj = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);

        return view('admin.employee-info.edit', compact('designationList', 'sponsor', 'agencies', 'edit', 'proj', 'relig', 'countryList', 'empTypes', 'allDepart'));
    }

    public function view($emp_auto_id)
    {
        $view = (new EmployeeDataService())->getAnEmployeeInfoByEmpAutoId($emp_auto_id);
        return view('admin.employee-info.view', compact('view'));
    }

    /* ==================== Project Wise Employee List Report UI form ==================== */
    public function projectWiseEmployeeList()
    {
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown();
        $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        $category = (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $jobStatus = JobStatus::all();
        $emp_types = (new EmployeeRelatedDataService())->getEmployeeTypeForDropdown();

        return view('admin.employee-info.project_wise.employee_report_processing_form', compact('projects', 'sponser', 'category', 'jobStatus', 'emp_types'));
    }

    // UI For Creating EMployee Report
    public function sponserWiseEmployeeList()
    {
        $sponser = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);
        return view('admin.employee-info.sponser_wise.all', compact('sponser'));
    }


    /*
  |--------------------------------------------------------------------------
  |  JSON Reponse OPERATION
  |--------------------------------------------------------------------------
  */
    // Ajax Request Form Employee Insert UI checkEmployeeId
    public function checkEmployeeUniqueInformationBeforeAddNewEmployee(Request $request)
    {
        $find_emp = (new EmployeeDataService())->checkThisValueIsExistInServerDatabase($request->value, $request->dbcolum_name);
        if ($find_emp) {
            return response()->json(['status' => 200, 'success' => true, 'data' => 1, 'error' => 'Already Exist this information']);
        } else {
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => '']);
        }
    }

    public function searchNextNewEmployeeUniqueID(Request $request)
    {
        try{

            $find_next_emp_id = (new EmployeeDataService())->searchNewEmployeeUniqueEmployeeID($request->new_emp_type);

             if ($find_next_emp_id) {
                 return response()->json(['status' => 200, 'success' => true, 'data' => $find_next_emp_id]);
             } else {
                 return response()->json(['status' => 404, 'success' => false,  'error' => 'System Error']);
             }


        }catch(Exception $ex){
            return response()->json(['status' => 404, 'success' => false, 'data' => 0, 'error' => 'System Error']);
        }

    }

    // Project wise Employee List For Transfer Ajax Request
    public function getProjectWiseEmployeeListForEmployeeTransferAJAXRequest(Request $request)
    {

        if(!is_null($request->multi_emp_id)){

            $allEmplId = explode(",", $request->multi_emp_id);
            $allEmplId = array_unique($allEmplId); // remove multiple same empl ID
            $employee_list = (new EmployeeDataService())->getEmployeesInfoByMultipleEmployeeIDForEmployeeTransfer($allEmplId,Auth::user()->branch_office_id);
            return response()->json(['success' => true, 'status' => 200, "data" => $employee_list]);

        }elseif ($request->project_id >0) {
            $employee_list = (new EmployeeDataService())->getEmployeesInfoByProjectIdForEmployeeTransfer($request->project_id, 1,Auth::user()->branch_office_id);
            return response()->json(['success' => true, 'status' => 200, "data" => $employee_list]);
        }else {
            return response()->json(['success' => false, 'status' => 404, 'message' => 'Employee Not Found. Please Try Again','error' => 'error']);
        }
    }

    // Employee Shift Status Update Ui Data Ajax Request
    public function getProjectWiseActiveEmployeeListForEmployeeShiftStatusAjaxRequest(Request $request)
    {
        try{
                $projectActiveEmpList = (new EmployeeDataService())->getEmployeeInfoWithSalaryDetailsByProjectAndJobStatusForShiftUpdate($request->project_id, 1,Auth::user()->branch_office_id);
                $total_emp_day_shift = (new EmployeeDataService())->countDayShiftWorkingActiveEmployeesInAProject($request->project_id,Auth::user()->branch_office_id);
                $total_emp_night_shift = (new EmployeeDataService())->countNightShiftWorkingActiveEmployeesInAProject($request->project_id,Auth::user()->branch_office_id);

                if (count($projectActiveEmpList) > 0) {
                    return response()->json([
                        "status" => 200, "success" => true, "emp_day_shift" => $total_emp_day_shift, "emp_night_shift" => $total_emp_night_shift,
                        "employee_list" => $projectActiveEmpList
                    ]);
                } else {
                    return response()->json(["status" => "failed", "success" => false, 'error' => "Data Not Found!"]);
                }
            }catch(Exception $ex){
                return response()->json(['status' => 404,'success'=>false, 'error' => 'Data Not Found!','message'=> "Data Not Found!"]);
            }

    }

    // Searching Employee Full Details By Employee Id Or Passport or Iqama AJAX Request from search menu
    public function searchingEmployeeByEmployeeMultitypeParameter(Request $request)
    {

        if($request->search_by == 1){
            $employee = (new EmployeeDataService())->searchingEmployeeInfoByAnyColumnValueMatching($request->employee_searching_value,Auth::user()->branch_office_id);
        }else {
            $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->employee_searching_value, $request->search_by,Auth::user()->branch_office_id);
         }
        if (count($employee) > 0) {
            return json_encode([
                'success'  => true,
                'status'=> 200,
                'error' => null,
                'findEmployee' =>  $employee,
            ]);
        } else {
            return json_encode([
                'success'  => false,
                'status'=> 404,
                'error' => 'error',
                'message' => 'Employee Not Found',
            ]);
        }
    }

    // Searching Employee Details By Employee Id Or Passport or Iqama AJAX Request  from emp status menu
    public function searchingActiveEmployeeByEmployeeMultitypeParameter(Request $request)
    {


        $searchByDb_Column = $request->search_by;
        $employee_searching_value = $request->employee_searching_value;
        $employee = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($employee_searching_value, $searchByDb_Column,Auth::user()->branch_office_id);
        $accomdOfficeBuilding = (new AccommodationDataService())->getAllActiveOfficeBuildingNameIdAndCityForDropdownList();
        $getAllProject = (new ProjectDataService())->getLoginUserAssingedProjectForDropdownList(Auth::user()->id);

        $employeeStatusOBJ = new HelperController();
        $allEmployeeStatus = $employeeStatusOBJ->getEmployeeStatus();
        $designation =   (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();
        $agencies = (new CompanyDataService())->getAllAgencies();
        $sponsor = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown(Auth::user()->branch_office_id);



        if (count($employee) > 0) {
            return json_encode([
                'success'  => true,
                'status'=> 200,
                'error' => null,
                'findEmployee' =>  $employee,
                'empOfficeBuilding' => $accomdOfficeBuilding,
                'getAllProject' => $getAllProject,
                'allEmployeeStatus' => $allEmployeeStatus,
                'designation' => $designation,
                'agencies' => $agencies,
                'sponsors' => $sponsor,

            ]);
        } else {
            return json_encode([
                'success'  => false,
                'status'=> 404,
                'error' => 'error',
                'message' => 'Employee Not Found',
            ]);
        }
    }



    /* ======== end class bracket ======== */
}
