<?php

namespace App\Models\AccountsModule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrVoucher extends Model
{
    use HasFactory;
    protected $primaryKey = 'cr_vou_auto_id';
    protected $table = "cr_vouchers";
    protected $guarded = [];
     
}
