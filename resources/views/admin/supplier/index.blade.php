@extends('layouts.admin-master')
@section('title') Electric Voucher @endsection
@section('content')



 <!-- Session alert part start -->
 <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
            <strong> {{ Session::get('success') }}</strong> 
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
            <strong> {{ Session::get('error') }}</strong> 
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>


<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Invoice Summary Form </li>
        </ol>
    </div>
</div>

<!-- Menu  -->
<div class="row">
    <div class="card">
        <div class="card-header">
            <div class="form-group row custom_form_group">
                <div class="col-sm-4">                   
                    <button type="submit"  data-toggle="modal" onclick="openNewInvoiceSummaryForm()" data-target="#invoice_summary_update_modal_form" class="btn btn-primary waves-effect" style="border-radius: 15px;
                    width: 150px; height: 40px; letter-spacing: 1px;">New Invoice</button> 
                </div>
                <div class="col-sm-4">
                    <button type="submit"  onclick="openInvoiceSummarySearchingForm()"  class="btn btn-primary waves-effect" style="border-radius: 15px;
                    width: 150px; height: 40px; letter-spacing: 1px;">Searching</button>
                </div>
                <div class="col-sm-4">                   
                <button type="button" id="invoice_report" data-toggle="modal" data-target="#invoice_summary_report_modal" class="btn btn-primary waves-effect" style="border-radius: 15px;
                width: 150px; height: 40px; letter-spacing: 1px;">Report</button> 

                </div>
            </div>
        </div>
    </div>
</div>
 
<!-- Invoice Summary Search Section-->
<div class="row d-none" id="invoice_search_form">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-title"> Searching Invoice Records</h5>
            <div class="form-group row custom_form_group{{ $errors->has('emp_id') ? ' has-error' : '' }}">
                <label class="col-md-3 control-label d-block">Project </label>
                <div class="col-sm-7">
                    <select class="form-select" name="search_project_id" id="search_project_id" required>
                        {{-- <option value="">Select Project</option> --}}
                        @foreach($projects as $proj)
                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('search_project_id'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('search_project_id') }}</strong>
                    </span>
                    @endif
                </div>                
                <div class="col-md-2">
                    <button type="submit" onclick="searchInvoiceSummary()"
                        class="btn btn-primary waves-effect">SEARCH</button>
                </div>
                 
            </div>
        </div>
        <div class="card-body d-none" id="searching_result_section">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                            <thead>
                                <tr>                                       
                                    <th>S.N</th>
                                    <th>Project Name</th>
                                    <th>Month,Year</th>
                                    <th>Invoice Date</th>
                                    <th>Inv. Amount</th>
                                    <th>VAT</th>
                                    <th>Total with VAT</th>
                                    <th>Retention</th>
                                    <th>Receivable Amount</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Manage</th>
                                    <th>Paper</th>
                                 </tr>
                            </thead>
                            <tbody id="invoice_summary_records_table">                                    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

<!-- Invoice Summary Report Modal -->
{{-- <div class="row">   
    <div class="modal fade" id="invoice_summary_report_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-body">
                    <form class="form-horizontal" id="invoice_summary_report_form" method="post" target="_blank" action="{{ route('invoice.summary.report.process') }}">
                     @csrf 

                            <div class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Project:<span class="req_star">*</span></label>

                                <div class="col-sm-6">
                                    <select class="selectpicker" name="project_id_list[]" id="project_id_list[]" multiple required>                                
                                        @foreach($projects as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                        @endforeach
                                    </select>
                                     
                                </div>
                            </div> 
                            
                            <div class="form-group row custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Invoice Status:<span class="req_star">*</span></label>

                                <div class="col-sm-6">
                                    <select class="form-control" name="invoice_status" id="invoice_status">
                                        <option value="">All </option>                                       
                                        <option value="0"> Pending </option>
                                        <option value="1"> Received </option>                                        
                                    </select>
                                     
                                </div>
                            </div>
                            
                            <button type="submit" id="invoice_summary_report_button"  class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Show Report</button>
                    </form>                  
                  </div>
              </div>
        </div>
    </div>
</div> --}}

 <!-- Supplier insert/Update Modal-->
<div class="modal fade" id="supplier_modal_form" tabindex="-1" role="dialog" aria-labelledby="supplier_modal_form" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="form-horizontal company-form" id="invoice_summary_form_modal" method="post" enctype="multipart/form-data"
                action="{{ route('invoice.summary.store.request') }}">
                @csrf
                 <div class="modal-content"> 
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">New Supplier Information <span class="text-danger" id="errorData"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>              
                    <div class="modal-body">
                        
                            <div class="form-group row custom_form_group{{ $errors->has('supplier_name') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Supplier Name<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="custom-form-control form-control" id="supplier_name" name="supplier_name"
                                        placeholder="Enter Supplier Name"  required>
                                    @if ($errors->has('supplier_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('supplier_name') }}</strong>
                                    </span>
                                    @endif
                                </div>                                 
                            </div>
                            <div class="form-group row custom_form_group{{ $errors->has('vat_no') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">VAT Number<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="custom-form-control form-control" id="vat_no" name="vat_no"
                                        placeholder="Enter VAT Number"   required>
                                    @if ($errors->has('supplier_name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('vat_no') }}</strong>
                                    </span>
                                    @endif
                                </div>                                 
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('mobile_no') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Mobile </label>
                                <div class="col-sm-9">
                                    <input type="text" class="custom-form-control form-control" id="mobile_no" name="mobile_no"
                                      placeholder="Enter Supplier Mobile Number"  >
                                    @if ($errors->has('mobile_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mobile_no') }}</strong>
                                    </span>
                                    @endif
                                </div>                                 
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('supplier_address') ? ' has-error' : '' }}">
                                <label class="col-sm-3 control-label">Address</label>
                                <div class="col-sm-9">
                                    <input type="text" class="custom-form-control form-control" id="supplier_address" name="supplier_address"
                                         placeholder="Enter Supplier Address" min="1">
                                    @if ($errors->has('supplier_address'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('supplier_address') }}</strong>
                                    </span>
                                    @endif
                                </div>                                 
                            </div> 
                            <div class="form-group row custom_form_group">
                                <div class="col-sm-8"></div>
                                <div class="col-sm-4">
                                    <button type="submit" id="submit_button"   class="btn btn-primary waves-effect"  style="border-radius: 15px;
                                    width: 150px; height: 40px; letter-spacing: 1px;">Save</button>
                                </div>                               
                            </div>
 
                        </div>
                    </div>                                        
                </div>                
        </form>
    </div>
</div>
 
 
<script>
   

        function openNewInvoiceSummaryForm(){ 
 
            document.getElementById("invoice_summary_form_modal").reset();
            $('#invoice_details').text('');         
            document.getElementById('submit_button').textContent = 'Save';     
            $('#invoice_search_form').removeClass('d-block').addClass('d-none');
          //  $('#new_invoice_summary_form').removeClass('d-none').addClass('d-block');
        }

        function openInvoiceSummarySearchingForm(){ 
            $('#new_invoice_summary_form').removeClass('d-block').addClass('d-none');
            $('#invoice_search_form').removeClass('d-none').addClass('d-block');

        }

        function openInvoiceSummaryReportForm(){ 
            $('#new_invoice_summary_form').removeClass('d-block').addClass('d-none');
            $('#invoice_search_form').removeClass('d-block').addClass('d-none');

        } 

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

    // Employee Searching for Advance Paper Upload 
        function searchInvoiceSummary(){

            var project_id = $('select[name="search_project_id"]').val();   
            $('#invoice_summary_records_table').html('');
            
            $.ajax({
                type:"GET",
                url: "{{  route('invoice.summary.search') }}",
                data:{
                    project_id:project_id, 
                },
                success:function(response){   

                            if(response.status != 200){   
                                showMessage(response.message,'error');
                                return;
                            }    
                                                                    
                            var rows = "";
                            var counter = 1;
                            $.each(response.data, function (key, value) { 
                                rows += `
                                        <tr>
                                            <td>${counter++}</td>
                                            <td>${value.proj_name}</td>
                                            <td>${value.month},${value.year}</td>
                                            <td>${value.submit_date}</td>
                                            <td>${value.invoice_amount}</td>
                                            <td>${value.vat_amount}</td>
                                            <td>${(parseFloat(value.vat_amount) + parseFloat(value.invoice_amount))}</td> 
                                            <td>${value.retention_amount}</td>
                                            <td> ${value.receivable_amount}</td>  
                                            <td> ${value.invoice_status == 0 ? 'Pending' :'Received'}</td> 
                                            <td> ${value.remarks == null ? '-' : value.remarks  }</td>                                        
                                             
                                            <td> 
                                                <a href="" id="invoice_summary_edit_button" data-toggle="modal" onclick="openInvoiceSummaryUpdateModal(${value.inv_sum_auto_id})" data-target="#invoice_summary_update_modal_form" data-id="${value.inv_sum_auto_id}">Edit</a>
                                               
                                                <a href="#" onClick="deleteInvoiceSummary(${value.inv_sum_auto_id})" title=""><i id="" class="fa fa-trash fa-lg delete_icon"></i></a> 
                                                
                                            </td>                                    
                                            <td> <a target="_blank" href="{{ url('${value.invoice_file}') }}" >download </a></td>                                                                                              
                                        </tr>
                                        `
                             });
                            $('#invoice_summary_records_table').html(rows);
                            if(counter > 1){
                                $('#searching_result_section').removeClass('d-none').addClass('d-block');
                            }else{
                                $('#searching_result_section').removeClass('d-block').addClass('d-none');
                                showMessage(response.message,'error');
                            }
                    
                },
                error:function(response){
                    showMessage(response.message,'error');
                }
            });

        }
  

        //  Modal View Hidden Event, Reset Modal Previous Data 
        $('#invoice_summary_update_modal_form').on('hidden.bs.modal', function (e) {
            $(this)
            .find("input,textarea,select").val('').end()
            .find("input[type=checkbox],input[type=radio]").prop("checked", "").end();
        
        })

        function openInvoiceSummaryUpdateModal(inv_sum_auto_id){           
            $.ajax({
                type: "GET",
                url: "{{ route('invoice.summary.search.by.invoicesummaryid') }}",
                data: {inv_sum_auto_id: inv_sum_auto_id},
                datatype:"json",
                success: function(response){
                    if(response.status == 200){  

                            submit_button = document.getElementById('submit_button').textContent = "Update";
                          
                            var arecord = response.data;                              
                           
                            document.querySelector('#project_id').value = arecord.project_id;
                            document.querySelector('#month').value = arecord.month;
                            document.querySelector('#year').value = arecord.year;  
                            document.querySelector('#modal_invoice_status').value = arecord.invoice_status;                            
                            document.getElementById('submit_date').setAttribute("value", (arecord.submit_date));
                        

                            $('#inv_sum_auto_id').val(arecord.inv_sum_auto_id);
                            $('#modal_invoice_amount').val(arecord.invoice_amount);
                            $('#modal_vat_amount').val(arecord.vat_amount);
                            $('#modal_total_with_vat').val((parseFloat(arecord.vat_amount) + parseFloat(arecord.invoice_amount)));
                            $('#modal_vat').val(arecord.vat);
                           $('#modal_retention').val(arecord.retention);
                           $('#modal_retention_amount').val(arecord.retention_amount);         
                           $('#modal_receivable_amount').val(arecord.receivable_amount);
                           $('#modal_remarks').val(arecord.remarks);
                           $('#invoice_details').text(arecord.proj_name+", Invoice Date- "+arecord.submit_date +"," +arecord.month_name+", "+arecord.year+",Status: "+(arecord.invoice_status == 0? 'Pending':'Received'));
                           
                           
                    
                    }else{

                    }                   
                },
                error:function(response){
                    showSweetAlert('Operation Failed ','error');  
                }
            })
        }

    function deleteInvoiceSummary(inv_sum_auto_id){
       
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
                   type: 'delete',
                  // url: "{{  url('admin/employee/salary/delete') }}/" + salary_auto_id,
                  url:"{{ route('invoice.summary.delete.by.invoicesummaryid')}}",
                  data:{
                    inv_sum_auto_id:inv_sum_auto_id
                  },
                   dataType: 'json',
                   success: function (response) {
                       if(response.status == 200){                            
                        showMessage("Successfully Deleted",'success');
                        searchInvoiceSummary();
                       }else {
                        showMessage("Operation Failed",'error');
                       }                     
                   },
                   error:function(response){
                    showMessage("Operation Failed",'error');
                   }
               });
           }
       });
       //  window.location.reload();
    }
   

</script>



<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css"
    integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js"
    integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>


@endsection
