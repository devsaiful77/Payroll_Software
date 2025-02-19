<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EmpSalaryStatusEnum;

class EmployeeInfo extends Model
{
    use HasFactory;
    protected $primaryKey = 'emp_id';

    // protected $casts = [
    //   'salary_status' =>EmpSalaryStatusEnum::class  
    // ];

    public function salarydetails(){
      return $this->belongsTo(SalaryDetails::class, 'emp_auto_id', 'emp_id');
    }

    public function country(){
      return $this->belongsTo('App\Models\Country','country_id','id');
    }

    public function employeeType(){
      return $this->belongsTo('App\Models\EmployeeType','emp_type_id','id');
    }

    public function type(){
      return $this->belongsTo('App\Models\EmployeeType','emp_type_id','id');
    }

    public function category(){
      return $this->belongsTo('App\Models\EmployeeCategory','designation_id','catg_id');
    }


    public function division(){
      return $this->belongsTo('App\Models\Division','division_id','division_id');
    }

    public function district(){
      return $this->belongsTo('App\Models\District','district_id','district_id');
    }

    public function department(){
      return $this->belongsTo('App\Models\Department','department_id','dep_id');
    }

    public function project(){
      return $this->belongsTo(ProjectInfo::class, 'project_id','proj_id');
    }

    public function sponsor(){
      return $this->belongsTo(Sponsor::class,'sponsor_id', 'spons_id');
    }

    public function status(){
      return $this->belongsTo(JobStatus::class, 'job_status', 'id');
    }

    public function religion(){
      return $this->belongsTo('App\Models\Religion','religion','relig_id');
    }

    public function sponser(){
      return $this->belongsTo('App\Models\Sponsor','sponsor_id','spons_id');
    }

    public function agency(){
      return $this->belongsTo(AgencyInfo::class,'agc_info_auto_id','agc_info_auto_id');
    }

    public function advancePayInfo(){
      return $this->hasMany(AdvancePay::class,'emp_id','emp_auto_id');
    }

}
