<?php



namespace App\Http\Controllers\DataServices;

use App\Models\Inventory\emp_item_received_record;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class InventoryItemDistributionDataService{

     

    public function insertEmployeeReceivedItemInformation($item_auto_id,$emp_auto_id,$received_qty,$model_no,$serial_no,$store_id,$brand_id,$item_unit,$received_date,$insert_by,$received_status){
     

        return emp_item_received_record::insertGetId([

            'emp_auto_id' => $emp_auto_id,
            'item_auto_id' => $item_auto_id,
            'approved_qty' => $received_qty,
            'received_qty' => $received_qty,
            'model_no'=>$model_no,
            'serial_no'=>$serial_no,
            'store_id' => $store_id,
            'brand_id'=>$brand_id,
            'item_unit'=>$item_unit,
            'received_date' => $received_date,
            'approved_date' => $received_date,
            'approved_by' => $insert_by,
            'insert_by' => $insert_by,
            'received_status' => $received_qty,
            'created_at' => Carbon::now(),

        ]);
    }

    public function getItemReceivedRecordByItemReceivedAutoId($item_received_auto_id){
        return emp_item_received_record::where('item_received_auto_id',$item_received_auto_id)->get()->first();
    }
    public function deleteReceivedItemRecord($item_received_auto_id  ){
        return emp_item_received_record::where('item_received_auto_id',$item_received_auto_id )->delete();
    }

    public function updateItemReceivedPaperLocation($item_received_auto_id,$received_paper,$update_by ){
        return emp_item_received_record::whereIn('item_received_auto_id',$item_received_auto_id )->update([
            'received_paper' => $received_paper,
            'update_by'=>$update_by,          

        ]);
    }

    public function getItemReceivedRecords($limit = 20){
        return  DB::select('call getEmployeesItemReceivedRecords1(?)',array($limit));
    }

    public function searchAnEmployeeReceivedItemInfoRecords($emp_auto_id){
        return  DB::select('call getAnEmployeeItemReceivedRecords1(?)',array($emp_auto_id));
    }
    public function searchItemRecivedEmployeeForUploadPaper($from_date,$to_date,$inserted_by){
        return  DB::select('call searchItemReceivedEmployeeForUploadReceivedPaper1(?,?,?)',array($inserted_by,$from_date,$to_date));
    }



    /*
     ==========================================================================
     ==================== Inventory Item Distribution Report ==================
     ==========================================================================
    */
    public function searchEmployeeReceivedInventoryItemReport($from_date,$to_date,$store_id){
        return  DB::select('call getEmployeeReceivedInvenItemRecordsByStoreId1(?,?,?)',array($from_date,$to_date,$store_id));
    }
    


}