<?php


namespace App\Http\Controllers\Admin\Emp_Bonus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Requests\EmpBonusFormRequest;
use Illuminate\Support\Facades\Auth;


class EmployeeBonusController extends Controller
{
    //

    function __construct()
    {
        $this->middleware('permission:salary_bonus_add', ['only' => ['index','storeNewEmployeeBonusSalaryInformation','createBonusSalarySheet']]);
        $this->middleware('permission:salary_bonus_edit', ['only' => ['getAnEmployeeBonusSalaryRecords']]);
        $this->middleware('permission:salary_bonus_delete', ['only' => ['deleteAnEmployeeBonusSalaryRecordByBonusId']]);

    }

    public function index(){
        return view('admin.emp_bonus.index');
    }
    // Ajax request to store new bouns record
    public function storeNewEmployeeBonusSalaryInformation(EmpBonusFormRequest $request){


        $emp_auto_id = $request->emp_auto_id;
        $month = $request->month;
        $year = $request->year;
        $bonus_amount = $request->bonus_amount;
        $remarks = $request->remarks;
        $bonus_type =  $request->bonus_type;

        $isSaved = (new SalaryProcessDataService())->insertEmployeeBonusRecord($emp_auto_id,$bonus_amount,$bonus_type,$month,$year,$remarks,Auth::user()->id);
        if($isSaved == -1){
            return response()->json(['status'=>203,'success'=>false,'error'=>'This Employee Bonus Record Already Exist']);
        }else if($isSaved <=0 ){
            return response()->json(['status'=>409,'success'=>false,'error'=>'Data Missing and Operation Failed']);
        }else {
            $arecord = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByBonusAudoId($isSaved);
            return response()->json(['status'=>200,'success'=>true,'data'=>$arecord]);
        }

    }
    public function getAnEmployeeBonusSalaryRecords(Request $request){


        $emp = (new EmployeeDataService())->searchingAnEmployeeInfoByMultitypeParameter($request->searchValue,$request->searchType);
        if(count($emp) == 0){
            return response()->json(['status'=>404,'success'=>false,'error'=> 'Employee Not Found']);
        }else if(count($emp) >1){
            return response()->json(['status'=>404,'success'=>false,'error'=> 'Multiple Employees Found']);
        }else {
             $records = (new SalaryProcessDataService())->getAnEmployeeBonusRecordsWithEmployeeDetails($emp[0]->emp_auto_id,null,null);
             return response()->json(['status'=>200,'success'=>true,'records'=>$records]);
        }



    }

    public function createBonusSalarySheet($bonus_auto_id){

       $record  = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByBonusAudoId($bonus_auto_id);
       $employee = (new EmployeeDataService())->getAnEmployeeInfoWithSalaryDetailsByEmpAutoId($record->emp_auto_id);
       $month = (new HelperController())->getMonthName($record ->month);
       $year = $record ->year;
       $company = (new CompanyDataService())->findCompanryProfile();
       return view('admin.salary-generate.single_employee.salary_bonus_sheet', compact('employee','record', 'month','year', 'company'));
    }

   public function deleteAnEmployeeBonusSalaryRecordByBonusId($bonus_auto_id){

        $arecord = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByBonusAudoId($bonus_auto_id);
        $isPaidSalary = (new SalaryProcessDataService())->checkAnEmployeeSalaryIsAlreadyPaid($arecord->emp_auto_id,$arecord->month,$arecord->year);
            if(!$isPaidSalary){
                $isDeleted = (new SalaryProcessDataService())->deleteAnEmployeeBonusRecordByBonusAutoId($bonus_auto_id);
                return response()->json(['status'=>200,'success'=>true,'message'=>'Successfully Deleted','arecord'=>$arecord]);
            }else {
                return response()->json(['status'=>401,'success'=>true,'message'=>'Selected Month Of Salary Already Paid,Operation Denied','arecord'=>$arecord]);
            }

   }



   // ========================== Bonus Report ===============================
   public function processEmployeeBonusDetailsReport(Request $request){
        try{
            if($request->bonus_report_type == 1){
                if($request->employee_id){
                    $records  = (new SalaryProcessDataService())->processAnEmployeeBonusRecordsDetailsReportByEmployeeID(
                       $request->employee_id ,Auth::user()->branch_office_id);
                }else{
                    $records  = (new SalaryProcessDataService())->processEmployeesBonusdetailsReport($request->bonus_type,$request->from_date,$request->to_date,Auth::user()->branch_office_id);
                }
                $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
                $login_name  = Auth::user()->name;
                return view('admin.report.salary.bonus.emp_bonus_details_report', compact('records', 'login_name','company'));
            }
        }catch(Exception $ex){
            return 'Operation Failed, Please Try Again  '.$ex;
        }
   }

}



