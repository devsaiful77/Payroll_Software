<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Models\EmployeeInformation;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;


class CreditVoucherController extends Controller{

    public function getAll(){
      //  return $data= CrVoucher::orderBy('CrVoucId','DESC')->get();
      }

      
    public function index(){
      //  $allDrVouchar = $this->getAll();

       // $TType=CrType::orderBy('CrTypeName','ASC')->get();
      //  $getAllEmployees= EmployeeInformation::orderBy('EmplInfoId','DESC')->get();
       return view('admin.accounts_module.petty_cash_inout.form');
      }


    //   public function edit($id){
    //     $allDrVouchar = $this->getAll();
    //     $data=$allDrVouchar->where('CrVoucId',$id)->first();

    //     $TType=CrType::orderBy('CrTypeName','ASC')->get();
    //     $getAllEmployees= EmployeeInformation::orderBy('EmplInfoId','DESC')->get();
    //    return view('admin.voucher.credit.add', compact('getAllEmployees', 'TType', 'allDrVouchar', 'data'));
    //   }


    public function store(Request $request){
        

        // $request['TranAmount'] = $request->Amount;
        // $request['TranTypeId'] = 1;

        // $transObj = new  TransactionsController();
        // $transId = $transObj->createNewTransaction($request); 
        

        // $request['Amount'] = $request->Amount;
        // $request['TranId'] = $transId;
        // $request['ChartOfAcctId'] = 1;
        // $request['DrCrTypeId'] = 1;

        // $decrObj = new  DebitCreditController();
        // $drcrId = $decrObj->insertNewDebitCreditTransaction($request); 

        // $insert= CrVoucher::insertGetId([
        //     'TransactionId'=>$transId,
        //     'CrTypeId'=>$request['CrTypeId'],
        //     'ExpenseDate'=>$request['Date'],
        //     'Amount'=>$request['Amount'],
        //     'DebitedTold'=>1,
        //     'CreditedFromId'=>$request['Credited'],
        //     'VoucherId'=>$request['VoucharNo'],
        //     'CreateById'=>1,
        // ]);

        // if ($insert) {
        //     Session::flash('success', 'successfully store data information');
        //     return redirect()->back();
        // }else{
        //     Session::flash('error', 'Please try again');
        //     return redirect()->back();
        // }

    }
}
