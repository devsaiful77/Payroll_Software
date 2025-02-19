@extends('layouts.admin-master')
@section('title') Transaction @endsection
@section('content')
 
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Daily Transaction</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Daily Transanction</li>
        </ol>
    </div>
</div>
<!-- Session Flash Message -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('success')}}</strong>
        </div>
        @endif
        
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
            <strong>{{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
</div>


<!-- Transaction Menu Section -->
<div class="row" id="">
    <div class="col-md-12">
             <div class="card">
                <div class="card-body card_form justify-content-center">
                    <div class="row justify-content-center">
                          
                         <div class="col-sm-1" style="overflow:hidden">
                            @can('debit_invoice_add') 
                                    <button type="button" id="invoice_report" data-toggle="modal" data-target="#daily_expense_form_modal" class="btn btn-primary waves-effect">New Expense</button> 
                            @endcan
                           &nbsp; &nbsp;
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-1" style="overflow:hidden"> 
                            @can('debit_invoice_search')
                                <button type="button" onclick="showSearchingForm()" class="btn btn-primary waves-effect">Search</button>  
                            @endcan
                        </div>
                        <div class="col-sm-1"></div>
                        <div class="col-sm-1" style="overflow:hidden">
                            @can('credit_invoice_add') 
                                <button type="button"   data-toggle="modal" data-target="#cash_received_form_modal" class="btn btn-primary waves-effect">Cash Received</button> 
                            @endcan
                        </div>                         
                        <div class="col-sm-1"> </div>
                        <div class="col-sm-2" style="overflow:hidden">
                             @can('debit_invoice_daily_report')                             
                             <button type="button" onclick="showReportProcessingSection()" class="btn btn-primary waves-effect">Report</button>  
                            @endcan
                        </div>
                        <div class="col-sm-1"> </div>
                        <div class="col-sm-2" style="overflow:hidden">                             
                         </div>
                    </div>
                </div>
            </div>
     </div>
</div>  
 
<!-- Daily Expense Modal !--> 
<div class="modal fade" id="daily_expense_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-body">
                <h5 style="color: green; text-align:center">Daily Expense Insertion Form </h5>
                <hr>
                <form class="form-horizontal" id="daily_expense_form" enctype="multipart/form-data" action="{{route('company.daily.transaction.expesne.store')}}" method="POST" >
                 @csrf
                        <div class="form-group row custom_form_group{{ $errors->has('expense_type') ? ' has-error' : '' }}">
                            {{-- <input type="hidden" class="form-control" name="request_type" value="2">                                                          --}}
                            <input type="hidden"  id ="dr_vou_auto_id"  name="dr_vou_auto_id"  >

                            <label class="col-sm-4 control-label">Expense Type <span class="req_star">*</span> </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="expense_type" id="expense_type"  required>  
                                    <option value="">Please Select One </option>   
                                    @foreach($expense_types as $r)                         
                                    <option value="{{ $r->cost_type_id }}">{{$r->cost_type_name}}</option>
                                    @endforeach 
                                </select>  
                                @if ($errors->has('expense_type'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('expense_type') }}</strong>
                                    </span>
                                @endif                               
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            

                            <label class="col-sm-4 control-label">Employee ID</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" name="employee_id" id="employee_id"   placeholder="Enter Employee ID Here...">                         
                            </div>
                        </div>                       

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Payment Method </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="expense_method" id="expense_method"  required>
                                       <option value="">Select One</option>
                                       <option value="CASH">CASH</option>
                                       <option value="BANK">BANK</option>                                    
                                </select>
                                 
                            </div>
                        </div>  
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Amount<span class="req_star">*</span></label>
                            <div class="col-sm-6"> 
                                <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount Here..."   min="0" required>                         
                            </div>
                        </div> 

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Date<span class="req_star">*</span> </label>
                            <div class="col-sm-8"> 
                                <input type="date" id="expense_date" name="expense_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                             </div>
                        </div> 

                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">Remarks</label>
                            <div class="col-md-8"> 
                                <textarea class="form-control" rows="3"  name="remarks" id="remarks"  style="resize:none" ></textarea>
                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="col-md-4 control-label">File</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="dr_invoice_path" id="imgInp4">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div> 
                        </div>
                        <button type="submit" id="dr_invoice_submit_btn"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Save</button>
                        {{-- <button type="button" id="dr_invoice_submit_btn" onclick="storeDailyTransactionExpense()"  class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Save</button> --}}
                </form> 
              </div>
          </div>
    </div>
</div>



   <!-- Daily Cash Receive Modal !--> 
<div class="modal fade" id="cash_received_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-body">
                <h5 style="color: green; text-align:center">Cash Received Form </h5>
                <hr>
                <form class="form-horizontal" id="daily_cash_received_form">
                 @csrf
                        <div class="form-group row custom_form_group">

                            {{-- <input type="hidden" class="form-control" name="request_type" value="2">                                                          --}}
                            <input type="hidden" class="form-control" id ="cr_vou_auto_id"  name="cr_vou_auto_id"  >
                            
                            <label class="col-sm-4 control-label">Cheque/Receipt No.</label>
                            <div class="col-sm-8"> 
                                <input type="text" class="form-control" name="receipt_number" id="receipt_number" placeholder="Cheque/Receipt No. Here...">                         
                            </div>
                        </div> 
                        
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Receiving Source </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="cash_receive_method" id="cash_receive_method"  required>                             
                                    <option value="CASH">CASH</option>
                                    <option value="BANK">BANK</option>                                    
                                </select>                                
                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Bank Name </label>
                            <div class="col-sm-8">
                                <select class="form-control" name="bank_id" id="bank_id">                             
                                    <option value="">Select Bank Name</option>
                                     @foreach($bank_list as $bn)
                                     <option value="{{$bn->id}}"> {{ $bn->bank_name}}-{{ substr($bn->account_no, -4)  }}</option> 
                                     @endforeach                                   
                                </select>                                
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Amount<span class="req_star">*</span></label>
                            <div class="col-sm-8"> 
                                <input type="number" class="form-control" name="receive_amount" id="receive_amount" placeholder="Enter Amount Here..."   min="0" step="1" required>                         
                            </div>
                        </div> 

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Date<span class="req_star">*</span> </label>
                            <div class="col-sm-8"> 
                                <input type="date" id="cash_receive_date" name="cash_receive_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                             </div>
                        </div> 

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Remarks</label>
                            <div class="col-sm-8"> 
                                <textarea class="form-control" rows="3"  name="cash_remarks" id="cash_remarks" ></textarea>
                            </div>
                        </div>                        
                        
                        <button type="button" id="invoice_report_button" onclick="storeDailyTransactionCashReeived()"  class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Save</button>
                    </form>    
              </div>
          </div>
    </div>
</div>

  <!-- Transaction Search Section-->
<div class="row d-none" id="search_section">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-title"> Searching Transaction Records</h5>
            <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                <label class="col-md-3 control-label d-block">Searching Records </label>
                <div class="col-md-3">
                    <select class="form-select" name="transaction_searchBy" id="transaction_searchBy" required>
                        <option value="1">Expense</option>
                        <option value="2">Cash Received </option>
                     </select>
                </div>
                <div class="col-sm-3">
                    <input type="date" class="form-control" id="searching_date" value="<?= date("Y-m-d") ?>"  required>
                    @if ($errors->has('emp_id'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('emp_id') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="col-md-2">
                    <button type="submit" onclick="searchingTransactionRecords()"
                        class="btn btn-primary waves-effect">SEARCH</button>
                </div>
                 
            </div>
        </div>
        <div class="card-body" id="searching_result_section">

            {{-- Expense Records --}}
            <div class="row d-none" id="expense_search_records_section">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="alltableinfo" class="table table-bordered table-hover custom_table mb-0">
                            <thead>
                                <tr>                                       
                                    <th>S.N</th>
                                    <th>Employee ID</th>
                                    <th>Employee Name</th>
                                    <th>Expense Type</th>
                                    <th>Paid By</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                    <th>Inserted By</th>
                                    <th>Amount</th>  
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody id="expense_records_table">                                    
                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>
            {{-- Cash Received Records --}}
            <div class="row d-none" id="cash_search_records_section">
                <div class="col-12">
                    <div class="table-responsive">                        
                        <table id="alltableinfo" class="table table-bordered table-hover custom_table mb-0">
                            <thead>
                                <tr>                                       
                                    <th>S.N</th> 
                                    <th>Received By</th>
                                    <th>Date</th>
                                    <th>Inserted By</th>
                                    <th>Remarks</th>
                                    <th>Amount</th> 
                                    <th>Manage</th>
                                </tr>
                            </thead>
                            <tbody id="cash_received_records_table">                                    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

  <!-- Transaction report Section-->
<div class="row d-none" id="report_section">
    <div class="col-md-12">
        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

            <div class="btn-group" role="group" aria-label="Third group">
                <button type="button"   data-toggle="modal" data-target="#transaction_recport_form_modal" class="btn btn-primary waves-effect">Balance</button> 
            </div>  
            <div class="btn-group" role="group" aria-label="Third group">
                <button type="button"   data-toggle="modal" data-target="#expense_by_empid_report_modal" class="btn btn-primary waves-effect">Search By Employee</button> 
            </div> 
            <div class="btn-group" role="group" aria-label="Third group">
                <button type="button"   data-toggle="modal" data-target="#trans_report_date_date" class="btn btn-primary waves-effect">Date By Date</button> 
            </div> 
            <div class="btn-group" role="group" aria-label="Third group">
                <button type="button"   data-toggle="modal" data-target="#expense_type_report" class="btn btn-primary waves-effect">Expense Type</button> 
            </div> 
            <div class="btn-group" role="group" aria-label="Third group">
                {{-- <button type="button"   data-toggle="modal" data-target="#transaction_recport_form_modal" class="btn btn-primary waves-effect">Balance Report</button>  --}}
            </div> 
            
        </div>         
    </div>    
</div>

<!-- Daily Transaction Report !--> 
<div class="modal fade" id="transaction_recport_form_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daily Transaction Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">  
                   <form   action="{{ route('company.daily.transaction.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf  
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="date" id="report_date" name="report_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>                
                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Report</button>
                 </form>    
              </div>
          </div>
    </div>
</div>
<!-- Date by Date Summary Transaction Report !--> 
<div class="modal fade" id="trans_report_date_date" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Date by Date Summary Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">                                 
                   <form   action="{{ route('company.datebydate.trans.summary.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf 
                    <div class="form-group row custom_form_group">
                        <label class="col-md-2 control-label d-block">Type </label>
                        <div class="col-md-10">
                            <select class="form-select" name="report_type" id="report_type">
                                <option value="">Cash Receive-Expense & Balance</option>
                                <option value="1">Expense</option>
                                <option value="2">Cash Receive </option>
                                <option value="5">Cash Receive & Expense Details</option>
                            </select>
                        </div>
                    </div>  
                    
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">From<span class="req_star">*</span> </label>
                        <div class="col-sm-10"> 
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div> 
                    
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-2 control-label">To<span class="req_star">*</span> </label>
                        <div class="col-sm-10"> 
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Process</button>
                 </form>    
              </div>
          </div>
    </div>
</div>

<!-- Expense by Emp ID Report !--> 
<div class="modal fade" id="expense_by_empid_report_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daily Expense By Employee Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">
                   
                   <form   action="{{ route('company.datebydate.trans.summary.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf  
                    <input type="hidden" class="form-control" name = "report_type"  value="3" >

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Employee ID<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="number" id="employee_id" name="employee_id" class="form-control" required>
                        </div>
                    </div> 
                    
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">From Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div> 
                    
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">To Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Report</button>
                 </form>    
              </div>
          </div>
    </div>
</div>

<!-- Expense Head/Type Transaction Report !--> 
<div class="modal fade" id="expense_type_report" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Expense Type Date To Date Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">                 
                           
                   <form   action="{{ route('daily.transaction.datebydate.summary.report') }}" target="_blank" onsubmit="" method="POST">
                    @csrf 
                    <input type="hidden" class="form-control" name = "report_type"  value="4" >
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">Expense Type <span class="req_star">*</span> </label>
                        <div class="col-sm-6">
                            <select class="form-control" name="expense_type" id="expense_type"  >  
                                <option value="">Please Select One </option>   
                                @foreach($expense_types as $r)                         
                                <option value="{{ $r->cost_type_id }}">{{$r->cost_type_name}}</option>
                                @endforeach 
                            </select>  
                            @if ($errors->has('expense_type'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('expense_type') }}</strong>
                                </span>
                            @endif                               
                        </div>
                         
                    </div>  
                    
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">From Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div> 
                    
                    <div class="form-group row custom_form_group">
                        <label class="col-sm-4 control-label">To Date<span class="req_star">*</span> </label>
                        <div class="col-sm-6"> 
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-4"  >Report Type</label>
                        <div class="col-sm-6">
                        <select class="form-select" name="report_type" > 
                            <option value="1">Date By Date Expense Details</option>
                            <option value="2">Date To Date All Expense Summary</option>
                            <option value="3">Expense Summary Month by Month</option>
                        </select>
                        </div>
                    </div>

                    <button type="submit" id="report_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Process</button>
                 </form>    
              </div>
          </div>
    </div>
</div>



<script>

    function showSearchingForm(){
        $('#report_section').removeClass("d-block").addClass('d-none');
        $('#search_section').removeClass("d-none").addClass('d-block');
    }
    function showReportProcessingSection(){
        $('#search_section').removeClass("d-block").addClass('d-none');
        $('#report_section').removeClass("d-none").addClass('d-block');
    }

    $(document).ready(function () {
            //  form validation
            $("#daily_expense_form").validate({
            submitHandler: function (form) {
                return false;
            },
            rules: {
                expense_type: {
                    required: true,
                },
                amount: {
                    required: true,
                },
                pay_type:{
                    required:true,
                },
                expense_date:{
                    required:true,
                }

            },
            messages: {
                expense_type: {
                    required: "You Must Be Select Expense Type",
                },
                amount: {
                    required: "You Must Be Input This Field",
                },
                pay_type:{
                    required:"You Must Be Select Payment Method",
                },
                expense_date:{
                    required:"You Must Be Select Expense Date",
                }

            },
        }); 

        // new Debir/Expense Invoice form submit
        $("#daily_expense_form").submit(function (e) {
         
                e.preventDefault();       
                var form = $("#daily_expense_form");
                var data =  new FormData($(this)[0]);  // if same name two form then o index
                var action = form.attr("action");
                 document.getElementById("dr_invoice_submit_btn").disabled = true;
                  
                $.ajax({
                        url: action,
                        method: form.attr("method"),
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend:function(){ 
                        },
                })
                .done(function(response) {  

                        document.getElementById("dr_invoice_submit_btn").disabled = false;
                        if(response.status == 200){   
                             $('#daily_expense_form')[0].reset();
                             $("#daily_expense_form_modal").modal('hide'); // hide modal
                             showMessage(response.message,'success');
                        }else {
                            showMessage(response.message,'error');
                        }                                             
                })
                .fail(function(xhr) {
                    document.getElementById("dr_invoice_submit_btn").disabled = false;
                    showMessage("Operation Failed, Please Try Aggain",'error');
                 });
        });            
    });
  
   
    function storeDailyTransactionExpense(){

        var expense_by_emp_id = $('#employee_id').val();
        var amount = parseFloat($('#amount').val());
        var expense_type = $('#expense_type').val();
        var expense_date = $('#expense_date').val();
        var remarks = $('#remarks').val();
        var pay_type = $('#expense_method').val(); 
        var dr_vou_auto_id = $('#dr_vou_auto_id').val(); 
        var dr_invoice_path = $('#dr_invoice_path').val();
        
        if(amount < 0 || amount == "" || expense_type == "" || pay_type == "" || expense_date == ""){
            showMessage("Please Input Valid Data",'error');
            return;
        }  
        $.ajax({
            type:"POST",
            url:"{{route('company.daily.transaction.expesne.store')}}",
            data:{
                expense_by_emp_id:expense_by_emp_id,
                amount:amount,
                expense_type:expense_type,
                expense_date:expense_date,
                remarks:remarks,
                pay_type:pay_type, 
                dr_vou_auto_id:dr_vou_auto_id,
                dr_invoice_path:dr_invoice_path              
            },
           // data:data,
            datatype:"json",
            success:function(response){
                 
                if(response.success ==true){
                     $("#daily_expense_form_modal").modal('hide'); // hide modal
                     showMessage(response.message,'success');                                
                }else {
                     showMessage(response.message,'error');  
                }            
            },
            error:function(response){
                showMessage('Network Error','error');  
            }
            
        })

    }

    function storeDailyTransactionCashReeived(){
       
        var receipt_number = $('#receipt_number').val();
        var amount = $('#receive_amount').val(); 
        var cash_receive_date = $('#cash_receive_date').val();
        var remarks = $('#cash_remarks').val();
        var cash_receive_method = $('#cash_receive_method').val(); 
        var bank_id = $('#bank_id').val();
        var cr_vou_auto_id = $('#cr_vou_auto_id').val();
       
        if(amount < 0 || amount == "" || expense_type == "" || cash_receive_method == "" || cash_receive_date == ""){
            showMessage("Please Input Valid Data",'error');
            return;
        }
        $.ajax({
            type:"POST",
            url:"{{route('company.daily.transaction.cash.receive')}}",
            data:{   
                receipt_number:receipt_number,              
                amount:amount, 
                cash_receive_date:cash_receive_date,
                remarks:remarks,
                cash_receive_method:cash_receive_method, 
                bank_id:bank_id,
                cr_vou_auto_id:cr_vou_auto_id

            },
            datatype:"json",
            success:function(response){
                
                if(response.success ==true){
                    $("#cash_received_form_modal").modal('hide'); // hide modal
                    showMessage(response.message,'success');                                
                }else {
                    showMessage(response.message,'error');  
                }            
            },
            error:function(response){
                showMessage('Network Error','error');  
            }
            
        })

    }


    function searchingTransactionRecords(){
        var date = $('#searching_date').val();
        var search_type = $('#transaction_searchBy').val();    // 1 = expense , 2= cash received
        $('#advance_paper_list_table').html('');
        $.ajax({
            type:"get",
            url: "{{  route('company.daily.transaction.searching') }}",
            data:{
                search_date:date,
                search_type:search_type,
            },
            success:function(response){   
                if(response.status == 200){           
                    if(search_type == 1){
                        showExpenseRecords(response)
                    }else {
                        // cash received records
                        showCashReceivedRecords(response);                 
                    }                      
                }else {
                    showMessage('Advance Record Not Found','error');
                }
            },
            error:function(response){
              //  showMessage(response.message,'error');
                showMessage('Operation Failed','error');
            }
        });
    }


    function showExpenseRecords(response){

        $("#cash_search_records_section").removeClass("d-block").addClass("d-none"); 
        $("#expense_search_records_section").removeClass("d-none").addClass("d-block"); 
        
        var rows = "";
        var counter = 1;
        $.each(response.data, function (key, value) {  
             rows += `
                <tr>
                    <td>${counter++}</td>
                    <td>${value.employee_id}</td>
                    <td>${value.employee_name}</td>
                    <td>${value.cost_type_name}</td>
                    <td>${value.PaymentType}</td>
                    <td>${value.ExpenseDate}</td>
                    <td> ${value.Remarks != null ? value.Remarks : '-'}</td> 
                    <td> ${value.name}</td> 
                    <td>${value.Amount}</td> 
                    <td> 
                        @can('debit_invoice_edit') 
                             <a href="#" onClick="searchingDailyTransactionRecordForEditing(${value.dr_vou_auto_id},1)">Edit</a>                                                 
                        @endcan 

                        @can('debit_invoice_delete') 
                            <a href="#" onClick="deleteDailyTransaction(1,${value.dr_vou_auto_id })" title="Delete"><i class="fa fa-trash fa-lg delete_icon"></i></a> 
                        @endcan

                        <a target="_blank" href="{{ url('${value.invoice_path}') }}" class="btn btn-success">View </a>
                    </td>   
                      
         
                </tr>
                ` 
        });        
        $('#expense_records_table').html(rows);
    }

    function showCashReceivedRecords(response){
      
        $("#expense_search_records_section").removeClass("d-block").addClass("d-none"); 
        $("#cash_search_records_section").removeClass("d-none").addClass("d-block"); 
        var rows = "";
        var counter = 1;
        $.each(response.data, function (key, value) {  
            rows += `
                <tr>
                    <td>${counter++}</td>
                    <td>${value.ReceiveMethod}</td> 
                    <td>${value.ReceivedDate}</td>
                    <td> ${value.name}</td> 
                    <td> ${value.Remarks != null ? value.Remarks : '-'}</td>                                                       
                    <td>${value.Amount}</td>  
                    <td> 
                        @can('credit_invoice_edit') 
                            <a href="#" onClick="searchingDailyTransactionRecordForEditing(${value.cr_vou_auto_id},2)">Edit</a>                                           
                        @endcan
                        
                        @can('credit_invoice_delete') 
                            <a href="#" onClick="deleteDailyTransaction(2,${value.cr_vou_auto_id})" title="Delete"><i class="fa fa-trash fa-lg delete_icon"></i></a>    
                        @endcan 
                    </td>       
                </tr>
                ` 
        });        
        $('#cash_received_records_table').html(rows);
    }

     
    // Delete Both Cash Recive and Expense record
    function deleteDailyTransaction(deleteOperationType,record_auto_id){
          
       swal({
           title: "Are you sure?",
           text: "Once deleted, You will not be able to recover this Record!",
           icon: "warning",
           buttons: true,
           dangerMode: true,
       })
       .then((willDelete) => {
           if (willDelete) {
               $.ajax({
                   type: 'POST', 
                    url:"{{ route('company.daily.transaction.delete') }}",
                    data:{
                        operation_type:deleteOperationType,
                        record_auto_id:record_auto_id,                                
                    },
                    datatype:"json",
                    success:function(response){
                        console.log(response);
                        debugger;
                        if(response.success ==true){
                            $("#daily_expense_form_modal").modal('hide'); // hide modal
                            showMessage(response.message,'success'); 
                            searchingTransactionRecords();                               
                        }else {
                            showMessage(response.message,'error');  
                        }  
                      //  showMessage('Operation Under Development','success');           
                    },
                    error:function(response){
                        debugger;
                        console.log(response);
                        showMessage('Network Error','error');  
                    }
               });
           }
       });
       //  window.location.reload();
    }

    // Open Expense/Debit Record for editing 
    function searchingDailyTransactionRecordForEditing(record_auto_id,record_type){

        // var record_type =   1;
      
        $.ajax({
            type:"GET",
            url: "{{  route('company.daily.transaction.edit') }}",
            data:{
                record_auto_id:record_auto_id,
                record_type:record_type,
            },
            success:function(response){   
                if(response.status == 200){ 
                              
                    var arecord = response.data;
                     if(record_type == 1){                       
                      //  expense/debit inovice record                      
                        $("#dr_vou_auto_id").val(arecord.dr_vou_auto_id);
                        $("#employee_id").val(arecord.employee_id);
                        $("#expense_method").val(arecord.PaymentType);
                        $("#amount").val(arecord.Amount);
                        $("#remarks").val(arecord.Remarks);
                        $("#expense_date").val(arecord.ExpenseDate);
                        $("#expense_type").val(arecord.DrTypeId);
                        $("#daily_expense_form_modal").modal('show'); 
                           
                    }else {
                        // cash/credit inovice records                         
                        $("#cr_vou_auto_id").val(arecord.cr_vou_auto_id);
                        $("#receipt_number").val(arecord.receipt_number);
                        $("#cash_receive_method").val(arecord.ReceiveMethod);
                        $("#receive_amount").val(arecord.Amount);
                        $("#cash_remarks").val(arecord.Remarks);
                        $("#cash_receive_date").val(arecord.ReceivedDate); 
                        $("#cash_received_form_modal").modal('show');    
                    } 
                     
                }else {
                    showMessage('Advance Record Not Found','error');
                }
            },
            error:function(response){
                 showMessage('Operation Failed','error');
            }
        });
    }


    function showTransactionReport(){
        var report_date = $('#report_date').val();
        var url = "{{ route('company.daily.transaction.report', ':parameter') }}";
        url = url.replace(':parameter', report_date);
        window.open(url, '_blank');

   }


 
    // reset expense model UI
    $('#daily_expense_form_modal').on('hidden.bs.modal', function (e) {
        $(this)
        .find("input,textarea,select").val('').end()
        .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
 
    }) 

    $('#cash_received_form_modal').on('hidden.bs.modal', function (e) {
        $(this)
        .find("input,textarea").val('').end() ;
    }) 
 
    function showMessage(message,operationType){
          
         const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
           
                Toast.fire({
                    type: operationType,
                    title: message
                })
            
    }   


</script>

@endsection
