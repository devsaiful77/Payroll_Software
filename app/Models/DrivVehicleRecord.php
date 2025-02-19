<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrivVehicleRecord extends Model
{
    use HasFactory;

    public function Driver(){
        return $this->belongsTo(DriverInfo::class, 'driv_auto_id', 'dri_auto_id');
      }

    public function Vehicle(){
        return $this->belongsTo(Vehicle::class, 'veh_auto_id', 'veh_id');
      }
}
