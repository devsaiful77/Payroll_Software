<?php

namespace App\Http\Controllers\DataServices;


use App\Models\SupplierInfo; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class SupplierDataService{

    public function getAllActiveSuppliersForDropdownList(){
        return SupplierInfo::where('sup_status',1)->orderBy('sup_trade_name','ASC')->get();
    }

    public function insertNewSupplierInformation($sup_trade_name,$sup_vat,$sup_address,$sup_mobile,$sup_type,$created_by,$create_at)
    {
         
        return SupplierInfo::insertGetId([
            'sup_trade_name' => $sup_trade_name,
            'sup_vat' => $sup_vat,
            'sup_address' => $sup_address,
            'sup_mobile' => $sup_mobile,
            'sup_type' => $sup_type,
            'created_by' => $created_by,
            'created_at' => $create_at,
        ]);
    }

    public function updateSupplierInformation($sup_auto_id,$sup_trade_name,$sup_vat,$sup_address,$sup_mobile,$sup_type,$created_by,$update_at){
        return SupplierInfo::where('sup_auto_id',$sup_auto_id)->update([
            'sup_trade_name' => $sup_trade_name,
            'sup_vat' => $sup_vat,
            'sup_address' => $sup_address,
            'sup_mobile' => $sup_mobile,
            'sup_type' => $sup_type,
            'created_by' => $created_by,
            'updated_at' => $update_at,
        ]);
    }

}
   