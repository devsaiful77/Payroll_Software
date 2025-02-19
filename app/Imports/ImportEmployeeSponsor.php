<?php

namespace App\Imports; 

 
//use App\Models\Sponsor;
use App\Models\EmployeeInfo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
//use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DataServices\EmployeeDataService;
// use App\Http\Controllers\DataServices\EmployeeRelatedDataService; 
use App\Http\Controllers\Admin\Helper\HelperController;
use PhpOffice\PhpSpreadsheet\Shared\File;


 

class ImportEmployeeSponsor implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    public $records,$records_not_found;
    
    public function __construct( )
    {
        $this->records = collect();
        $this->records_not_found = collect();             
       // (new EmployeeRelatedDataService())->removeIqamaExpireUploadedAllDataFromTemporaryTable(); 
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {                 
        $model = new EmployeeInfo(); 
        try{

            $model->akama_no = (int) $row['iqama_number'];  
            $model->sponsor_id =  $row['sponsor_id'];    
            $model->entered_id = Auth::user()->id;                     
            $anEmp = (new EmployeeDataService())->getAnEmployeeInfoByEmpIqamaNo($model->akama_no); 
                    
            $isOk = true;
            if($anEmp == null){
                $isOk = false;
                $model->employee_id = "";
                $model->employee_name = "Employee Not Found";
                $model->hourly_employee = ""; 
                $model->upload_status = "Employee Not Found";
                $this->records_not_found->push($model);

            }else{

            // $emp_auto_id  = $anEmp->emp_auto_id;
                $model->employee_id  = $anEmp->employee_id;
                $model->employee_name = $anEmp->employee_name;
                $model->hourly_employee = $anEmp->hourly_employee;
                $anEmp = (new EmployeeDataService())->updateEmployeeSponsorInfo((int)$anEmp->emp_auto_id, $model->sponsor_id); 
            
                $model->upload_status = "Updated" ;                                                                                                         
                $this->records->push($model);
            }  

        }catch(Exception $ex){
            
        }
        


    }
}
