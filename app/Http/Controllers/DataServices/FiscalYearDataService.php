<?php

namespace App\Http\Controllers\DataServices;

use App\Models\EmployeeFiscalYearDuration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FiscalYearDataService{


    /*
     =====================================================================================
     ======================= Employee Fiscal Year Section ================================
     =====================================================================================
    */

    public function checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id){

        return EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)->count() >= 1 ? true : false;
     }
     public function checkAnEmployeeRunningFiscalYearIsAlreadyExist($emp_auto_id){
         return EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)->where('closing_status',false)->count() >= 1 ? true : false;
     }

     public function deleteAnEmployeeFiscalYearRecordByFiscalYearAutoId($efcr_auto_id){
        return EmployeeFiscalYearDuration::where('efcr_auto_id',$efcr_auto_id)->delete();
    }

     public function getAnEmployeeOpenCloseFiscalYearAllRecords($employee_id,$branch_office_id){
         return EmployeeFiscalYearDuration::where('employee_infos.employee_id',$employee_id)
                         ->where('employee_infos.branch_office_id',$branch_office_id)
                         ->leftjoin('employee_infos', 'employee_fiscal_closing_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')->get();

     }
     public function getAnEmployeeRunningFiscalYearRecord($emp_auto_id){

        if(!$this->checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id)){
            $this->setAnEmployeeFiscalYearDuration($emp_auto_id,1,2021,'2021-12-31',0,2);
         }

         $record = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
                         ->where('closing_status',false)
                         ->latest('efcr_auto_id')
                         ->first();

         if(is_null($record)){
             $record = EmployeeFiscalYearDuration::make();   // create object
             $record->balance_amount = 0.0;

             $record->start_year = (int) Carbon::now()->format('Y');
             $record->start_month = (int) Carbon::now()->format('m');
             $record->start_date =  Carbon::now()->format('Y-m-d');
         }
         $record->end_year = (int) Carbon::now()->format('Y');
         $record->end_month = (int) Carbon::now()->format('m');
         $record->end_date =  Carbon::now()->format('Y-m-d');
         return $record;
     }

     public function getAnEmployeeLastClosingFiscalYearRecord($emp_auto_id){

         $record = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
                         ->where('closing_status',true)
                         ->latest('efcr_auto_id')
                         ->first();

         if(is_null($record)){
             $record = EmployeeFiscalYearDuration::make();   // create object
             $record->balance_amount = 0.0;
         }
         return $record;
     }

     public function getAnEmployeeFiscalYearRecordByFiscalYearAutoId($efcr_auto_id){
         return EmployeeFiscalYearDuration::where('efcr_auto_id',$efcr_auto_id)->first();
     }

     public function updateAnEmployeeFiscalYear($emp_fis_year_auto_id,$emp_auto_id,$end_month,$end_year,$end_date,$balance_amount,$closing_status ,$remarks,$updated_by){

      return  EmployeeFiscalYearDuration::where('efcr_auto_id',$emp_fis_year_auto_id)->where('emp_auto_id',$emp_auto_id)->update([
             'end_month' =>$end_month,
             'end_year' =>$end_year,
             'end_date' =>$end_date,
             'balance_amount' =>$balance_amount,
             'closing_status' => $closing_status ,
             'remarks' =>$remarks,
             'updated_by' =>$updated_by,
         ]);
     }

     public function updateAnEmployeeFiscalYearClosingBalanceOnly($emp_fis_year_auto_id,$balance_amount){

         return  EmployeeFiscalYearDuration::where('efcr_auto_id',$emp_fis_year_auto_id)->update([
                'balance_amount' =>$balance_amount
            ]);
        }


     public function setAnEmployeeFiscalYearDuration($emp_auto_id,$start_month,$start_year,$start_date,$balance_amount,$created_by){

       return  $autoId =   EmployeeFiscalYearDuration::insertGetId([
             'emp_auto_id' =>$emp_auto_id,
             'start_month' =>$start_month,
             'start_year' =>$start_year,
             'start_date' =>$start_date,
             'balance_amount' =>$balance_amount,
             'created_by' =>$created_by,
         ]);

     }

     public function checkThisOperationIsAllowInTheRunningFiscalYear($emp_auto_id,$operation_date){

        if(!$this->checkAnEmployeeFiscalYearIsAlreadyExist($emp_auto_id)){
            $this->setAnEmployeeFiscalYearDuration($emp_auto_id,1,2021,'2021-12-31',0,2);
         }
         $record = EmployeeFiscalYearDuration::where('emp_auto_id',$emp_auto_id)
                         ->where('closing_status',false)
                         ->latest('efcr_auto_id')
                         ->first();
         if($record == null){
             return false;
         }
         if( $record->start_date <= $operation_date)
              return true;
         else
            return false;
      }

      public function salaryClosingEmployeeListDateToDateReport($from_date,$to_date,$branch_office_id){

            return  DB::select('CALL salaryClosingEmployeelistDateToDateReport1(?,?,?)', array($from_date,$to_date,$branch_office_id));

            // CREATE PROCEDURE `salaryClosingEmployeelistMonthlyReport`(IN `month` INT(3), IN `year` INT(5)) NOT DETERMINISTIC CONTAINS SQL SQL SECURITY DEFINER select
            // fcr.start_month,fcr.start_year, fcr.start_date,fcr.end_month,fcr.end_year,fcr.end_date,fcr.balance_amount,fcr.closing_status,fcr.remarks,
            // ei.employee_id,ei.employee_name,ei.akama_no,ei.akama_expire_date,ei.akama_photo,ei.hourly_employee,ei.mobile_no,ei.phone_no,sp.spons_name,ec.catg_name,ct.country_name,uu.name as 'updated_by'

            // from employee_fiscal_closing_records  fcr
            // left join employee_infos ei on fcr.emp_auto_id = ei.emp_auto_id
            // left join sponsors sp on ei.sponsor_id = sp.spons_id
            // left join employee_categories ec on ei.designation_id = ec.catg_id
            // left join countries ct on ei.country_id = ct.id
            // left join users uu on fcr.updated_by = uu.id
            // where fcr.end_month = month and fcr.end_year = year and fcr.closing_status = 1

      }


    public function salaryClosingDateToDateSummaryReport($month,$year,$branch_office_id){
        return  DB::select('CALL getSalaryClosingMonthlySummaryReportByMonthAndYear1(?,?,?)', array($month,$year,$branch_office_id));
    }












}
