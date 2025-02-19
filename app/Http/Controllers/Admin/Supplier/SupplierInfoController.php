<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\SupplierDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SupplierInfoController extends Controller
{
     public function index(){
        return view('admin/supplier.index');
     }

     public function storeNewSupplierInformation(Request $request){
       // dd($request->all());
        (new SupplierDataService())->insertNewSupplierInformation($request->supplier_name ,$request->vat_no,$request->supplier_address,$request->mobile_no,1,Auth::user()->id,Carbon::now());
        return redirect()->back();
    }
}
