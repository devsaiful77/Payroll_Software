<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IqamaRenewalDetails extends Model
{
    use HasFactory;
    protected $primaryKey = 'IqamaRenewId';
    protected $guarded = [];

    public function employee(){
      return $this->belongsTo('App\Models\EmployeeInfo','EmplId','emp_auto_id');
    }

}
