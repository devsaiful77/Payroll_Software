<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $primaryKey = 'veh_id';
    protected $guarded = [];
    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','driver_id','emp_auto_id');
    }
    public function user(){
      return $this->belongsTo('App\Models\User','create_by_id','id');
    }

}
