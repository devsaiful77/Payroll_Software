<?php

namespace App\Http\Controllers\DataServices;

use App\Models\CateringMonthlyRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CateringDataService{

    public function insertAnEmployeeMonthlyCateringRecord($emp_auto_id,$month,$year,$total_days,$amount,$inserted_by,$approved_by,$remarks){
        
         return CateringMonthlyRecord::insertGetId([
            'emp_auto_id'=>$emp_auto_id ,
            'month'=>$month,
            'year'=>$year,
            'total_days'=>$total_days ,
            'amount' =>$amount,
            'inserted_by'=>$inserted_by ,
            'approved_by'=>$approved_by ,
            'remarks'=>$remarks ,
         ]);

    }
    public function updateAnEmployeeMonthlyCateringRecord($emcr_auto_id,$month,$year,$total_days,$amount,$inserted_by,$approved_by,$remarks){
        
        return CateringMonthlyRecord::where('emcr_auto_id',$emcr_auto_id)->update([         
           'month'=>$month,
           'year'=>$year,
           'total_days'=>$total_days ,
           'amount' =>$amount,
           'inserted_by'=>$inserted_by ,
           'approved_by'=>$approved_by ,
           'remarks'=>$remarks ,
        ]);

   }

   
    public function checkAnEmployeeCateringMonthRecordAlreadyExist($emp_auto_id,$month,$year){
        return (CateringMonthlyRecord::where('emp_auto_id',$emp_auto_id)->where('month',$month)->where('year',$year)->count()) > 0 ? true:false;
    }
    public function searchingAnEmployeeCateringMonthRecordByAutoId($emcr_auto_id){
        return CateringMonthlyRecord::where('emcr_auto_id',$emcr_auto_id)
                ->leftjoin('employee_infos', 'catering_monthly_records.emp_auto_id', '=', 'employee_infos.emp_auto_id')
                ->leftjoin('project_infos', 'employee_infos.project_id', '=', 'project_infos.proj_id')
                ->leftjoin('sponsors', 'employee_infos.sponsor_id', '=', 'sponsors.spons_id')
                ->leftjoin('employee_categories', 'employee_infos.designation_id', '=', 'employee_categories.catg_id')
                ->first();
    }
    public function getAnEmployeeAMonthCateringRecordForSalaryProcessingByEmployeeAutoId($emp_auto_id,$month,$year){
        return  CateringMonthlyRecord::select('emcr_auto_id','emp_auto_id','year','month','total_days','amount','status')->where('emp_auto_id',$emp_auto_id)->where('month',$month)->where('year',$year)->first();
    }

   public function deleteAnEmployeeCateringMonthRecordByAutoId($emcr_auto_id){
           return CateringMonthlyRecord::where('emcr_auto_id',$emcr_auto_id)->delete();
   }

   public function searcAnEmployeeCateringRecordForListView($empoyee_id,$month,$year){
        return DB::select('call getAnEmployeeCateringRecordsForListView1(?,?,?)',array($empoyee_id,$month,$year));
   }
   public function getAnEmployeeCateringServiceReport($empoyee_id,$month,$year){ 
        return DB::select('call getAnEmployeeCateringServiceReport1(?,?)',array($empoyee_id,$year));
   }
   public function getCateringServiceRecordByMonthAndYearReport($month,$year){ 
    return DB::select('call getEmployeeCateringServiceRecordByMonthAndYearForReport1(?,?)',array($month,$year));
   }

   

    public function insertCateringMonthlyImportedRecordInTemporaryTable($emp_auto_id,$month,$year,$days,$amount,$inserted_by){
       return DB::insert('insert into catering_monthly_records_upload(emp_auto_id,month,year,total_days,amount,inserted_by) values (?,?,?,?,?,?)', [$emp_auto_id,$month,$year,$days,$amount,$inserted_by]);
    }
    
    public function deleteCateringImportedTemporaryTableRecordsForExcelUpload(){
        return DB::select('call removeAllRecordsFromCateringUploadTemporaryTable1()');
    }

    public function getCateringImportedTemporaryTableRecordsForFinalUpload(){
        return DB::select('call getAllRecordsFromCateringUploadTemporaryTable1()');
     }

   
   
    
}

