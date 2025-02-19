<?php

namespace App\Http\Controllers\Admin\Inventory_Module\ItemSetup;

use App\Enums\InventoryItemsUnit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\InventoryItemSetupDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Requests\InvItemDetailsFormRequest;
use Illuminate\Support\Facades\Session;

class ItemDetailsController extends Controller
{

    
    function __construct(){
        $this->middleware('permission:inventory_item_details_add',['only'=>['index','insert']]);
        $this->middleware('permission:inventory_item_details_edit',['only'=>['edit','update']]);
        $this->middleware('permission:inventory_item_details_delete',['only'=>['categItemInActive']]);
    }

    public function index(){

        $all = (new InventoryItemSetupDataService())->getItemDetailsInvItemAllRecords();
        $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
        $allBrand = (new InventoryItemSetupDataService())->getAllBrandItemRecordsForDropDownList();
        $allCompany = (new CompanyDataService())->getSubCompanyListForDropdown();

        $storeList = (new InventoryItemSetupDataService())->getAllSubStoreRecordsForDropDownList();
        $itemUnitList = InventoryItemsUnit::cases();

        return view('admin.inventory_module.item-details.all',compact('all', 'allType', 'itemUnitList', 'allBrand', 'allCompany', 'storeList'));
    }

    public function insert(InvItemDetailsFormRequest $request){
        $insert = (new InventoryItemSetupDataService())->insertNewItemDetailsInformationsWithItemDetailsID($request->itype_id, $request->icatg_id, $request->iscatg_id, $request->item_deta_code, $request->item_brand_id, $request->model_no, $request->quantity, $request->invoice_no, $request->invoice_date, $request->recieved_date, $request->serial_no, $request->item_det_unit, $request->item_company_id, $request->store_id);

        if( $insert > 0 ){
            Session::flash('success','Successfully Added New Items Details Infos');
            return redirect()->back();
        } else{
            Session::flash('error','Opeation Failed, Please Try Again');
            return redirect()->back();
        }
    }

    public function edit($id){
        $allType = (new InventoryItemSetupDataService())->getInventoryItemRecordsForDropDownList();
        $invItems = InventoryItemsUnit::cases();
        $allBrand = (new InventoryItemSetupDataService())->getAllBrandItemRecordsForDropDownList();
        $allCompany = (new InventoryItemSetupDataService())->getAllItemCompnayRecordsForDropDownList();
        $allSubStore = (new InventoryItemSetupDataService())->getAllSubStoreRecordsForDropDownList();
        $edit = (new InventoryItemSetupDataService())->getAnItemDetailsInformationsByItemDetailsAutoId($id);
        return view('admin.inventory_module.item-details.edit', compact('allType', 'edit', 'invItems', 'allBrand', 'allCompany', 'allSubStore'));
    }

    public function update(InvItemDetailsFormRequest $request){
        $update = (new InventoryItemSetupDataService())->updateItemDetailsInformations( $request->item_deta_id, $request->itype_id, $request->icatg_id, $request->iscatg_id, $request->item_deta_code, $request->quantity, $request->model_no, $request->item_brand_id, $request->invoice_no, $request->invoice_date, $request->recieved_date, $request->serial_no, $request->item_det_unit, $request->item_company_id, $request->store_id);

        if( $update){
            Session::flash('success','Successfully Updated Items Details Infos');
            return redirect()->route('inventory-item-details-name');
        } else{
            Session::flash('error','Opeation Failed, Please Try Again');
            return redirect()->back();
        }
    }



















}
