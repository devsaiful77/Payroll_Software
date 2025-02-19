<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;

class EmployeeDetails extends Model
{
    use HasFactory;

     public function agency(){
      return $this->belongsTo(AgencyInfo::class,'agc_info_auto_id','agc_info_auto_id');
    }
}
