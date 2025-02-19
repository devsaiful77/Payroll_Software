<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Helper\HelperController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\EmployeeAdvanceDataService;
use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\FiscalYearDataService;
use App\Http\Controllers\DataServices\EmpActivityDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryReportController extends Controller
{

    protected $searchby_employee_id = 'employee_id';
    protected $searchby_iqama_no = 'akama';

  public function loadSalaryReportGenerationForm(){

      $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(Auth::user()->branch_office_id);
      return view('admin.report.salary.salary_report_generation_form',compact('projects'));
  }

  public function processAndShowSalaryClosingReport(Request  $request){

    try{

      if($request->report_type == 1){
        // closing report month by month summary
        $report_records = (new FiscalYearDataService())->salaryClosingEmployeeListDateToDateReport($request->from_date,$request->to_date,Auth::user()->branch_office_id);
        $login_name = Auth::User()->name;
        $report_title = "Salary Closing Employees Report";
        $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
        return view('admin.report.salary.salary_closing_emp_list_report',compact('login_name','company','report_records','report_title'));

      }else if($request->report_type == 2){
          // closing report employee list
          $month_year = (new HelperController())->getMonthsInRangeOfDate($request->from_date,$request->to_date);
          $report_records = [] ;
          $counter = 0;
          foreach($month_year as $obj){
            $records = (new FiscalYearDataService())->salaryClosingDateToDateSummaryReport($obj["month"],$obj["year"],Auth::user()->branch_office_id);
            if($records[0]->month_name != null){
              $report_records[$counter++] = $records[0];
            }
          }
          $login_name = Auth::User()->name;
          $report_title = "Salary Closing Month by Month Summary";
          $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
          return view('admin.report.salary.salary_closing_monthly_summary_report',compact('login_name','company','report_records','report_title'));
      }



    }catch(Exception $ex){
        return view("System Operation Error. Please Input Valid Data & Try Again ");
    }


  }



  public function processAndShowEmployeeIDBaseEmployeeReport(Request  $request){
      try{

      //  dd($request->all());

        // if($request->report_type == 1){
        //   // employee details
        //       $company =  (new CompanyDataService())->findCompanryProfile();
        //       $employees = (new EmployeeDataService())->getEmployeeDetailsWitFileDownloadReportByMultipleEmpID($allEmplId);
        //       $project = "-";
        //       $report_title = "Multiple Employee ID";
        //       return view('admin.report.hr_section.multiple_id_employee_details', compact('employees', 'company', 'project', 'report_title'));

        // }else if($request->report_type == 2){
        //     // employee activities details
        //     $company =  (new CompanyDataService())->findCompanryProfile();
        //     $records = (new EmployeeDataService())->getAnEmployeeActivitiesReport($allEmplId[0],0);
        //     $loggedInUser = Auth::user()->name;
        //     return view('admin.report.hr_section.emp_activity_report', compact('records', 'company', 'loggedInUser'));
        // }else if($request->report_type == 3){

        //     // Employee working project history
        //     $employee = (new EmployeeDataService())->getAnyJobStatusEmployeeWithSalaryDetailsByEmpId($allEmplId[0]);
        //     return $this->processAndDisplayAnEmployeeWorkingPrjectHisotry($employee);
        // }else

        if($request->report_type == 4){
            // multiple employee prevacation salary statement
            return $this->prevacationSalaryStatementReport($request);
        }
        else {
             return view("Data Processing Error. Please Input Valid Data & Try Again ");
        }
      }
      catch(Exception $ex){
        return view("System Operation Error. Please Input Valid Data & Try Again ");
      }

  }



  // multiple employee prevacation salary statement
  private function prevacationSalaryStatementReport(Request  $request){

      $allEmplId = explode(",", $request->multiple_employee_Id);
      $employee_id_array = array_unique($allEmplId); // remove multiple same empl ID

      $report_records = [];
      $counter = 0;
      foreach($employee_id_array as $anEmp){
           $employee = (new EmployeeDataService())->searchAnEmployeeWithImportantInformationAsShowByMultitypeParameter($anEmp,$this->searchby_employee_id,Auth::user()->branch_office_id);

          if($employee == null || !(new FiscalYearDataService())->checkAnEmployeeRunningFiscalYearIsAlreadyExist($employee->emp_auto_id)){
              continue;
          }
          $fiscal = (new FiscalYearDataService())->getAnEmployeeRunningFiscalYearRecord($employee->emp_auto_id);
          $employee->closed_fiscal_record = (new FiscalYearDataService())->getAnEmployeeLastClosingFiscalYearRecord($employee->emp_auto_id);
          $employee->total_unpaid_salary_amount = (new SalaryProcessDataService())->getAnEmployeeUnpaidSalaryTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
          // Advance Collection
          $employee->toal_iqama_expense_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfIqamaExpenseDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
          $employee->total_other_advace_deduction_from_salary = (new SalaryProcessDataService())->getAnEmployeeTotalAmountOfOtherAdvanceDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
          $employee->cash_receive_total_paid_amount =  (new EmployeeAdvanceDataService())->getAnEmployeeAdvancePaidByCachTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
          // Advance Give to Employee
          $employee->iqama_renewal_total_expence = (new EmployeeAdvanceDataService())->getAnEmployeeIqamaRenewalTotalExpenseAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
          $employee->other_advance_total_Amount = (new EmployeeAdvanceDataService())->getAnEmployeeOthersAdvanceGivenTotalAmountByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
          $employee->toal_CPF_contribution_from_salary =  (new SalaryProcessDataService())->getAnEmployeeTotalAmountContributeToCPFByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
          $employee->total_saudi_tax = (new SalaryProcessDataService())->getAnEmployeeTotalAmountSaudiTaxDeductionFromSalaryByFiscalYear($employee->emp_auto_id,$fiscal->start_date,$fiscal->end_date);
          $employee->empl_last_activity = (new EmpActivityDataService())->getAnEmployeeLastActivityComments($employee->emp_auto_id);
        // $bonus_records = (new SalaryProcessDataService())->getAnEmployeeBonusRecordByFiscalYear($employee->emp_auto_id, $fiscal->start_year,$fiscal->end_year);
          $employee->all_others_balance_amount =   ($employee->iqama_renewal_total_expence + $employee->other_advance_total_Amount + $employee->closed_fiscal_record->balance_amount) - ($employee->toal_iqama_expense_deduction_from_salary +
                            $employee->total_other_advace_deduction_from_salary+$employee->cash_receive_total_paid_amount );
          $report_records[$counter++] =     $employee;

      }
      $company = (new CompanyDataService())->getABranchDetailsInformationByBranchAutoId(Auth::user()->branch_office_id);
      $login_name = Auth::User()->name;
      $report_title = "Pre-Vacation Salary Summary Report";
      return view('admin.report.salary.multi_emp_prevacation_salary_statement',compact('login_name','company','report_records','report_title'));
  }


}
