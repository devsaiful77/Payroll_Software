<?php

namespace Modules\Account\Http\Services;

use Illuminate\Support\Facades\DB;


class SalesCustomerService
{
    // get customer for Sale add or edit panel
    public function getCustomersForSales(){
        return DB::table("chartofacc_sales_customers")->where('cus_status',1)->select('cus_auto_id','cus_name','cus_phone')->get();
    }

    
}
