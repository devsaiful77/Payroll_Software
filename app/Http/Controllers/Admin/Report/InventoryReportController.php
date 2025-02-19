<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Controllers\DataServices\InventoryItemDistributionDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Http\Request;
use App\Models\ItemPurchase;
use App\Models\ItemSubCategory;
use App\Models\PurchaseRecord;

class InventoryReportController extends Controller
{
  public function index()
  {
    
    $sub_store = (new InventoryItemSetupDataService())->getAllSubStoreRecordsForDropDownList();
    $item_category = (new InventoryItemSetupDataService())->getCategoryItemsForDropDownList();
    
    return view('admin.report.inventory.index', compact('sub_store','item_category'));
  }

  public function showEmployeeInventoryItemReceivedReport(Request $request){
     
    $records = (new InventoryItemDistributionDataService())->searchEmployeeReceivedInventoryItemReport($request->start_date,$request->end_date,$request->sub_store_id);
    // dd($records);
    $company = (new CompanyDataService())->findCompanryProfile();
    return view('admin.report.inventory.emp_item_received_report', compact('records', 'company'));

  }

  // public function process(Request $request)
  // {
  //   $start_date = $request->start_date;
  //   $end_date = $request->end_date;

  //   $itype_id = $request->itype_id;
  //   $icatg_id = $request->icatg_id;
  //   $iscatg_id = $request->iscatg_id;

  //   $companyObj = new CompanyProfileController();
  //   $company = $companyObj->findCompanry();


  //   $ItemPurchase = ItemPurchase::whereBetween('date', [$start_date, $end_date])->get();
     
  //   $sum = ItemPurchase::whereBetween('item_purchases.date', [$start_date, $end_date])
  //     ->leftjoin('purchase_records', 'item_purchases.item_pur_id', '=', 'purchase_records.item_purchase_id')->sum("amount");
 

  //   return view('admin.report.item-purchase.report', compact('all', 'company', 'sum'));
  // }
  // /* ======================== item stock amount report ======================== */
  // public function itemStock()
  // {
  //   /* ==== call itemType controller ==== */
  //   $itemType = new ItemTypeController();
  //   $allType = $itemType->getAll();
  //   return view('admin.report.item-stock.all', compact('allType'));
  // }

  // public function itemStockProcess(Request $request)
  // {
  //   $itype_id = $request->itype_id;


  //   /* Company Controller Call */
  //   $companyObj = new CompanyProfileController();
  //   $company = $companyObj->findCompanry();
  //   $all = ItemSubCategory::where('itype_id', $itype_id)->get();

  //   return view('admin.report.item-stock.report', compact('all', 'company'));
  // }

  
}
