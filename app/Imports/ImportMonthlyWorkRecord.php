<?php

namespace App\Imports;

use App\Models\MonthlyWorkHistory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;

class ImportMonthlyWorkRecord implements ToModel, WithHeadingRow,WithCalculatedFormulas
{
        public $records,$records_not_found;
        private $project_id,$month,$year;


        public function __construct($project_id,$month,$year)
        {
            $this->records = collect();
            $this->records_not_found = collect();
            $this->project_id =  $project_id;
            $this->month = (int) $month;
            $this->year = $year;
            // Remove all data form temp table
            (new EmployeeAttendanceDataService())->deleteEmployeeWorkRecordImportedExcellDataFromTable();
        }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
                $model = new MonthlyWorkHistory();
                $model->emp_id = $row['emp_id']; // emp
                $model->month_id = $this->month;
                $model->year_id = $this->year;
                $model->project_id =  $this->project_id;
                $model->total_hours =  $row['basic_hours'] == '' ? 0 : (float) $row['basic_hours']; // $row['basic_hours'];
                $model->overtime =  $row['over_time'] == '' ? 0 : (float) $row['over_time'];
                $model->total_work_day = $row['days'] == '' ? 0 : (int) $row['days']; // $row['days'];
                $model->entered_id = Auth::user()->id;
                $anEmp = (new EmployeeDataService())->getSalaryActiveEmployeeInfoByEmpolyeeIDForTimeSheetUpload($model->emp_id);

                $isOk = true;
                if($anEmp == null){
                    $isOk = false;
                    $model->upload_status = "Employee Not Found";
                }else if($model->total_work_day > 31) {
                    $isOk = false;
                    $model->upload_status = "Working Days Greater Than 31";
                }
                else if($model->total_hours > 450 ) {
                    $isOk = false;
                    $model->upload_status = "Working Hours Greater Than 450";
                }
                else if($model->overtime > 180 ) {
                    $isOk = false;
                    $model->upload_status = "Overtime Greater Than 180";
                }else if((new EmployeeAttendanceDataService())->checkAnEmployeeMonthlyWorkRecordIsExistForTimeSheetUpload( $anEmp->emp_auto_id,$model->month_id,$model->year_id)) {
                    $isOk = false;
                    $model->upload_status = "Duplicate Record";
                }
                if($isOk){
                   // insert data into table
                   $model->upload_status = "OK" ;
                    (new EmployeeAttendanceDataService())->insertEmployeeWorkRecordImportedExcellData(
                        $anEmp->emp_auto_id,$model->month_id,$model->year_id,$model->project_id,$model->total_hours,$model->overtime,$model->total_work_day,$model->entered_id);
                    $model->emp_auto_id = $anEmp->emp_auto_id;
                    $model->employee_name = $anEmp->employee_name;
                    $model->hourly_employee = $anEmp->hourly_employee;
                    $this->records->push($model);

                }else {
                    $this->records_not_found->push($model);
                }


    }
}
