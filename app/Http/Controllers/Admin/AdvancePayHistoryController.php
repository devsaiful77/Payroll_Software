<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvancePayHistory;

use Carbon\Carbon;

// this controller is not used in future its will be deleted 

class AdvancePayHistoryController extends Controller{


  // public function findId($id){
    
  //   return $all = AdvancePayHistory::where('adv_pay_id',$id)->count();
  // }

  // public function findAnEmployeeAdvancePayRecord($id,$month,$year){
    
  //   return $advancePayRecord = AdvancePayHistory::where('adv_pay_id',$id)
  //                 ->where('aph_month',$month)
  //                 ->where('aph_year',$year)->first();
  // }

  //   public function index(){
  //     return "ok";
  //   }


  //   public function saveAdvancePayment($advancePayId,$salaryMonth,$salaryYear,$amount,$createById){
  //      $insert = AdvancePayHistory::insert([
  //       'adv_pay_id' => $advancePayId,
  //       'aph_date' => Carbon::now(),
  //       'aph_month' => $salaryMonth,
  //       'aph_year' => $salaryYear,
  //       'amount' => $amount,
  //       'create_by_id' => $createById,
  //       'created_at' => Carbon::now(),
  //     ]);
  //   }


  //   public function updateAnEmployeeAdvancePayRecord($id,$amount){
    
  //     return AdvancePayHistory::where('aph_id',$id)
  //                   ->update([
  //                     'amount' => $amount,
  //                   ]);
  //   }



}
