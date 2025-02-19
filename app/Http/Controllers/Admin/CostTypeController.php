<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\DataServices\ExpenseDataService;
use Session;

class CostTypeController extends Controller{
  /*
  |==================================================================
  | DATABSE OPEREATION
  |==================================================================
  */


  public function insert(Request $request){
    $this->validate($request,[
      'cost_type_name' => 'required'
    ],[

    ]);

   $success = (new ExpenseDataService())->insertNewCostType($request->cost_type_name);

    if($success >0 ){
      Session::flash('success','Successfully Completed');
      return Redirect()->back();
    }else{
      Session::flash('error',"Duplicate Value Not Allow");
      return Redirect()->back();
    }

  }

  public function update(Request $request){
    $this->validate($request,[
      'cost_type_name' => 'required'
    ],[

    ]);
    $success = (new ExpenseDataService())->updateCostType($request->id,$request->cost_type_name);
    if($success >0 ){
      Session::flash('success','Successfully Updated');
      return redirect()->route('cost-type');
    }else{
      Session::flash('error',"Duplicate Value Not Allow");
      return Redirect()->back();
    }

  }

  /*
  |==================================================================
  | BLADE OPEREATION
  |==================================================================
  */
  public function create(){
    $all = (new ExpenseDataService())->getCostTypeAll();
    return view('admin.cost-type.create',compact('all'));
  }

  public function edit($id){
    $edit = (new ExpenseDataService())->findACostType($id);
    return view('admin.cost-type.edit',compact('edit'));
  }


  /*
  |==================================================================
  | API OPEREATION
  |==================================================================
  */



  /* ===============================================================*/
}
