<?php

namespace Modules\Account\Http\Services;

use Illuminate\Support\Facades\DB;


class SalesProductInfoService
{
    // get Product for Sale add or edit panel
    public function getProductsForSales(){
        return DB::table("sales_product_infos")->where('spi_status',1)->select('spi_auto_id','spi_name_en','spi_name_en','spi_name_ab','spi_code')->get();
    }

    
}
