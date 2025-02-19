<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\EmployeeInfo;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
 

class EmployeeHRInfoExport implements FromCollection, WithHeadings, WithMapping
{

   
    protected $projectIdList, $sponserId, $jobStatus, $empCategory, $empTypeId;

    function __construct($projectIdList, $sponserId, $empCategory, $jobStatus, $empTypeId)
    {
        $this->projectIdList = $projectIdList;
        $this->sponserId = $sponserId;
        $this->empCategory = $empCategory;
        $this->jobStatus = $jobStatus;
        $this->empTypeId = $empTypeId;
    }

    public function headings(): array
    {
        return [
            'Employee ID', 'Employee Name', 'Passport No',
            'Passport Expire Date', 'Iqama No', 'Iqama Expire Date',
            'Sponsor Name', 'Project Name', 'Mobile No', 'Email', 'Date of Birth',
            'Nationality', 'Hourly Employee', 'Trade Name', 'Employee Type', 'Employee Status'  
        ];
    }
 
    public function map($emp): array
    {
     
        $fromday = 1;
        $numberOfDaysInThisMonth = 30;
        $month = 5;
        $year = 2023;        
        $attendence = (new EmployeeAttendanceDataService())->getAnEmployeeMonthlyAttendanceDateToDateRecords(2897,$fromday,$numberOfDaysInThisMonth, $month, $year);
        //  dd($attendence);
        $records =  array_fill(0, $numberOfDaysInThisMonth + 2, 0);
        $counter= 0;
        for($i = 1; $i <= $numberOfDaysInThisMonth;$i++){
            if($counter < $attendence->count() )
               $arecord = $attendence[$counter];
            if($i == (int) $arecord->emp_io_date ){
                $records[$i] = "".$arecord->daily_work_hours."/".$arecord->over_time;
                $counter +=1;
              //  dd($records);
            }
        }
      //  dd($records);
        return $records;
        return [
            $emp->employee_id,
            $emp->employee_name,
            $emp->akama_no,
        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
       
        $empllist = (new EmployeeDataService())->exportEmployeeHRSectionInformation($this->projectIdList,
        $this->sponserId,
        $this->jobStatus,
        $this->empCategory,
        $this->empTypeId);
        return collect($empllist);
    }
}
