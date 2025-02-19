<?php

namespace Modules\Account\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesProductInfo extends Model
{
    use HasFactory;
    protected $table = "sales_product_infos";
    protected $fillable = [];
}
