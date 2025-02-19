<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectInfo extends Model
{
    use HasFactory;
    protected $primaryKey = "proj_id";
    
    protected $table = "project_infos";

    public function status(){
      return $this->belongsTo(JobStatus::class,'job_status','id');
    }

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','proj_Incharge_id','emp_auto_id');
    }

    public function projectWiseEmployee(){
      return $this->hasMany('App\Models\EmployeeInfo', 'project_id', 'proj_id');
    }
}
