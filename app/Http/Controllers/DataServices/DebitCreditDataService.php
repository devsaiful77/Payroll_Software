<?php 

namespace App\Http\Controllers\DataServices;
 
use App\Models\AccountsModule\Transactions;
use App\Models\AccountsModule\DebitCredit;
use App\Models\AccountsModule\CrVoucher;
use App\Models\AccountsModule\DrVoucher;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use function PHPUnit\Framework\isNull;

 class DebitCreditDataService{

    private function createNewTransaction($amount,$date,$transaction_type){

        return Transactions::insertGetId([
            'TranAmount'=> $amount, 
            'TranDate'=> $date,
            'TranTypeId'=> $transaction_type,
          //  'created_at'=>Carbon::now('Asia/Dhaka')->toDateTimeString(),
        ]);
        
    }
    
    public function insertNewDebitCreditTransaction($debit_account,$credit_account, $amount,$transaction_id){
  

       $dr_cr_id[0] =  DebitCredit::insertGetId([
            'Amount'=> $amount, 
            'TranId'=> $transaction_id, 
            'ChartOfAcctId'=>$debit_account,  
            'DrCrTypeId'=>1,  // debit 
        ]);

        $dr_cr_id[1]  = DebitCredit::insertGetId([
          'Amount'=> $amount, 
          'TranId'=> $transaction_id, 
          'ChartOfAcctId'=>$credit_account,  
          'DrCrTypeId'=>2,  // credit
      ]);
      return true;
         
    }

    public function updateDebitCreditTransaction( $amount,$transaction_id,$date){
  
        Transactions::where('TranId',$transaction_id)->update([
          'TranAmount'=>$amount, 
          'TranDate'=> $date,           
        ]); 
         DebitCredit::where('TranId',$transaction_id)->update([
           'Amount'=> $amount,           
         ]); 
        return true;        
    }



    public function deleteTransactionRecord($transaction_id){
  
      Transactions::where('TranId',$transaction_id)->delete();
      DebitCredit::where('TranId',$transaction_id)->delete();
      return true;
        
   }


   // all expense invoice
    public function insertDailyExpenseInvoiceInformation($debit_by,$amount,$expense_type_id,$date,$remarks,$pay_type,$inserted_by,$invoice_path){

     // $emp_auto_id,$re->amount,$re->expense_type,$re->expense_date,$re->remarks,$re->expense_method,Auth::user()->id,  $uplodedPath);
        $transaction_id = $this->createNewTransaction($amount,$date,1);  // Expense
        if($transaction_id>0){
            $isSuccess = $this->insertNewDebitCreditTransaction(210,200,$amount,$transaction_id);  // 210 = chart of accout petty cash
            if($isSuccess){
              return  DrVoucher::insertGetId([
                  'TransactionId'=>$transaction_id,
                  'DrTypeId'=> $expense_type_id ,  
                  'ExpenseDate'=> Carbon::now() ,//$date,
                  'Amount'=>$amount,
                  'DebitedTold'=>210,
                  'CreditedFromId'=>200,
                  'debit_by' =>$debit_by,
                  'Remarks'=>$remarks,
                  'CreateById'=>$inserted_by,
                  'PaymentType' => $pay_type,
                  'created_at'=>Carbon::now(), 
                  "invoice_path"=>$invoice_path,             
              ]);      
            }           
        }
        return false;
    }

    public function updateDailyExpenseInvoiceInformation($dr_vou_auto_id,$transaction_id,$debit_by,$amount,$expense_type_id,$date,$remarks,$pay_type,$updated_by){
 
        $this->updateDebitCreditTransaction($amount,$transaction_id,$date);
        return  DrVoucher::where('dr_vou_auto_id',$dr_vou_auto_id)->update([
            'DrTypeId'=> $expense_type_id ,  
            'ExpenseDate'=>$date,
            'Amount'=>$amount,              
            'debit_by' =>$debit_by,
            'Remarks'=>$remarks,
            // 'CreateById'=>$inserted_by,
            'PaymentType' => $pay_type,
            'updated_at'=>Carbon::now(),                  
        ]);  
  }

  public function searchDailyExpenseRecordsByDate($date){
    return  DrVoucher::where('ExpenseDate',$date)
            ->leftjoin('users', 'users.id', '=', 'dr_vouchers.CreateById')
            ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'dr_vouchers.debit_by')
            ->leftjoin('cost_types', 'dr_vouchers.DrTypeId', '=', 'cost_types.cost_type_id')
            ->get();     
  }

  public function searchDailyExpenseRecordsByAutoId($DrVoucId){
      return  DrVoucher::select('dr_vou_auto_id','ExpenseDate','Amount','Remarks','PaymentType','TransactionId','DrTypeId','DebitedTold','CreditedFromId','employee_infos.employee_id','DrTypeId')->where('dr_vou_auto_id',$DrVoucId)
              ->leftjoin('employee_infos', 'employee_infos.emp_auto_id', '=', 'dr_vouchers.debit_by')
              ->first();
  }

  public function deleteDailyExpenseRecordsByAutoId($CrVoucId,$transactionId ){
       // $this->searchDailyExpenseRecordsByAutoId($CrVoucId ); 
       $this->deleteTransactionRecord($transactionId);
       return DrVoucher::where('dr_vou_auto_id',$CrVoucId)->delete();     
  }



 
    // all income invoice 
  public function insertDailyTransactionCashReceive($amount,$receipt_number,$pay_method,$date,$inserted_by,$remarks,$bank_id){


      $transaction_id = $this->createNewTransaction($amount,$date,2); // 2 = Income
      if($transaction_id>0){
          $isSuccess = $this->insertNewDebitCreditTransaction(200,210,$amount,$transaction_id);  // 200 = chart of accout petty cash
          if($isSuccess){
            return  CrVoucher::insertGetId([
                'TransactionId'=>$transaction_id,
                'DrTypeId'=> 1 ,   
                'receipt_number'=>$receipt_number,
                'ReceivedDate'=>$date,
                'Amount'=>$amount,
                'DebitedTold'=>205,
                'CreditedFromId'=>210,
                'Remarks'=>$remarks,
                'CreateById'=>$inserted_by,
                'ReceiveMethod' => $pay_method,
                'BankId' => $bank_id,
                'created_at'=>Carbon::now(),
            ]);      
          }           
      } // dr_vouchers
      return false;
  }

  public function updateDailyTransactionCashReceive($cr_vou_auto_id,$transaction_id,$amount,$receipt_number,$pay_method,$date,$updated_by,$remarks){

      return  CrVoucher::where('cr_vou_auto_id',$cr_vou_auto_id)->update([
             // 'TransactionId'=>$transaction_id,  
              'receipt_number'=>$receipt_number,
              'ReceivedDate'=>$date,
              'Amount'=>$amount,
              'Remarks'=>$remarks,
             // 'CreateById'=>$updated_by,
              'ReceiveMethod' => $pay_method,
              'updated_at'=>Carbon::now(),
          ]);   
}


    public function searchDailyCachReceiveRecordsByDate($date){
        return  CrVoucher::where('ReceivedDate',$date)
               ->leftjoin('users', 'users.id', '=', 'cr_vouchers.CreateById')              
               ->get();     
    }
    public function searchDailyCashReceivedRecordByAutoId($crVoucId ){
      return  CrVoucher::where('cr_vou_auto_id',$crVoucId )->first();     
    }


    public function calculateDailyTransactionCurrentBalanceOfDate($date){
          $total_cash_in = CrVoucher::whereBetween('ReceivedDate',["2024-01-01",$date])->get()->sum('Amount');   
          $total_cash_out = DrVoucher::whereBetween('ExpenseDate',["2024-01-01",$date])->get()->sum('Amount'); 
         return $total_cash_in -   $total_cash_out; 
    }

    public function deleteDailyCashReceiveRecordByAutoId($DrVoucId,$transactionId ){
      // $this->searchDailyExpenseRecordsByAutoId($CrVoucId ); 
       $this->deleteTransactionRecord($transactionId);
      return CrVoucher::where('cr_vou_auto_id',$DrVoucId)->delete();  
      
    }



    public function getDrInvoiceDateByDateTransactionSummaryReport($from_date,$to_date){
       return DB::select("CALL getDrInvoiceDateByDateTransactionSummary(?,?)",array($from_date,$to_date));     
    }

    
    public function getCrInvoiceDateByDateTransactionReport($from_date,$to_date){
      return DB::select("CALL getCrInvoiceDateByDateTransactionReport(?,?)",array($from_date,$to_date));     
    }

    public function getCrInvoiceDateByDateTransactionSummaryReport($from_date,$to_date){
      return DB::select("CALL getCrInvoiceDateByDateTransactionSummary(?,?)",array($from_date,$to_date));     
    }

    public function getDrInvoiceDateByDateTransactionReportByExpenseBy($expense_by,$from_date,$to_date){
      return DB::select("CALL getDrInvoiceDateByDateTransactionReportByEmployeeId(?,?,?)",array($from_date,$to_date,$expense_by));   
    }

    public function getDrInvoiceDateByDateTransactionReportByExpenseType($expense_type_id,$from_date,$to_date){
      if(is_null($expense_type_id)){
        $expense_type_id = 0;
      }
      return DB::select("CALL getDrInvoiceDateByDateTransactionReportByExpenseTypeId(?,?,?)",array($expense_type_id,$from_date,$to_date));   
    }
    public function getDrInvoiceDateToDateAllHeadBaseSummaryReport($from_date,$to_date){
      return DB::select("CALL getDrInvoiceExpenseHeadBaseDateToDateSummaryReport(?,?)",array($from_date,$to_date));   
    }
    public function getDrInvoiceDateToDateExpenseHeadBaseMonthByMonthSummaryReport($expense_type_id, $from_date,$to_date){
      return DB::select("CALL getDrInvoiceExpenseHeadBaseMonthByMonthSummaryReport(?,?,?)",array($expense_type_id, $from_date,$to_date));   
    }


    



    
 

 }
