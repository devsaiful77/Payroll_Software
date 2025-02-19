@extends('layouts.admin-master')
@section('title') Pending Salary list @endsection
@section('content')



<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Advance Paper Upload</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Upload</li>
        </ol>
    </div>
</div>


<style>
    .overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 999;
        /* background: rgba(255, 255, 255, 0.8) url('{{ asset("animation/Loading.gif")}}') center no-repeat; */
    }

    /* Turn off scrollbar when body element has the loading class */
    body.loading {
        overflow: hidden;
    }

    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay {
        display: block;
    }
</style>
 
<!-- Session Flash Message -->
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{Session::get('success')}} </strong> 
        </div>
        @elseif(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{Session::get('error')}} </strong>  
        </div>
        @endif        
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Advance Record Search UI !-->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body card_form">
                <div class="form-group row custom_form_group">
                    <label class="col-sm-2 control-label"> Advance Records From</label>
                    <div class="col-sm-2">
                        <input type="date" name="from_date" value="<?= date("Y-m-d") ?>" max="{{date('Y-m-d') }}" class="form-control">
                    </div>                      
                    <label class="col-sm-2 control-label">To</label>
                    <div class="col-sm-2">
                        <input type="date" name="to_date" value="<?= date("Y-m-d") ?>" max="{{date('Y-m-d') }}" class="form-control">
                    </div>                    
                    <div class="col-sm-2"> 
                        <button type="button"  id ="emp_search_button"  onclick="searchAdvanceInsertedEmployees()" class="btn btn-primary waves-effect">Search</button>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>


<!-- Employee Advance Record Table !-->
<div class="row" id="advance_paper_list_section">
    <div class="col-lg-12">
        <div class="card">
            <form method="post" action="{{ route('advance.paper.upload.request') }}" id="advance_paper_upload_form" enctype="multipart/form-data" 
             onsubmit="attendance_submit_button.disabled = true;">
                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="form-group row custom_form_group"> 
                            <label class="col-sm-1 control-label">File:</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="advance_paper" id="imgInp4">
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <img id='img-upload4' class="upload_image" />
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" id="attendance_submit_button" name="attendance_submit_button" class="btn btn-primary waves-effect">Submit </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <span id="data_not_found" class="d-none">Data Not Found!</span>
                                <table id="alltableinfo" class="table table-bordered table-hover custom_table mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.N</th>
                                            <th>Emp.ID</th>
                                            <th>Name</th>
                                            <th>Iqama/Passport</th>
                                            <th>Salary Type</th>
                                            <th>Adv. Amount</th>
                                            <th>Inserted At</th>
                                            <th>Remarks</th>
                                            <th>Inserted By</th>
                                            <th>Selection</th>
                                            <th colspan="2" >Action</th> 
                                        </tr>
                                    </thead>
                                    <tbody id="advance_paper_list_table"></tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>  
            </form>
        </div>
    </div>
</div>
 
<div class="overlay"></div>

<script> 


  // loading animation
  $(document).on({
        ajaxStart: function () {
            $("body").addClass("loading");
        },
        ajaxStop: function () {
            $("body").removeClass("loading");
        }
    });
    // form validation
    $(document).ready(function () {

        // attendance submit form validation
        $("#employee-entry-in-form").validate({
            submitHandler: function (form) {
                return true;
            },
            rules: {
                date: {
                    required: true,
                },
                entry_in_time: {
                    required: true,
                    number: true,
                    max: 23,
                    min: 1,
                },
            },

            messages: {

                date: {
                    required: "You Must Be Select This Field!",
                },
                entry_in_time: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum 23!",
                },
            },


        });

         
    });
   

     // Check uncheck 
    function checkUnCheckAllEmployeeForAttendanceIn(operationType){

        let myTable = document.getElementById('advance_paper_list_table');
            for (let row of myTable.rows) {
                allCell = row.cells;          
                var chkboxId = "adv_paper_checkbox-" + allCell[10].innerText;         
                document.getElementById(chkboxId).checked = operationType; 
            }
    }
 
    function showMessage(message,operationType){
         //  start message
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

    $('#advance_paper_upload_form').submit(function(event) {

            function advancePaperUploadRequest( ){
                e.preventDefault();
                let formData = new FormData(this);    
                          
                $.ajax({
                        type:"POST",
                        url:"{{route('advance.paper.upload.request')}}",    
                        dataType:"multipart/form-data",
                        data: formData,
                        success:function(response){
        
                                if(response.status == 200){        
                                    reset();
                                    showMessage(response.message,'success');
                                }else {
                                    showMessage(response.message,'error');
                                }                       
                            },
                        error:function(reponse){
                                showMessage("Operation Failed, Please Try Aggain",'error');
                        }
                    });


            }
    });

      function searchAdvanceInsertedEmployees(){
        var from_date = $('input[name="from_date"]').val();
        var to_date = $('input[name="to_date"]').val();   

        $.ajax({
            type:"POST",
            url: "{{  route('advance.paper.upload.formult.employee') }}",
            data:{
                from_date:from_date,
                to_date:to_date,
            },
            success:function(response){   
                if(response.status == 200){          

                    $("#advance_paper_list_section").removeClass("d-none").addClass("d-block");  
                    console.log(response.data);
                    var rows = "";
                    var counter = 1;
                    $.each(response.data, function (key, value) {                        
                        rows += `
                                    <tr>
                                        <td>${counter++}</td>
                                        <td>${value.employee_id}</td>
                                        <td>${value.employee_name}</td>
                                        <td>${value.akama_no}, ${value.passfort_no}</td>
                                        <td>${value.hourly_employee == 1 ? 'Hourly' : 'Basic Salary' }</td>
                                        <td> ${value.adv_amount}</td>                                        
                                        <td> ${value.created_at}</td>    
                                        <td> ${value.adv_remarks == null ? '-' : value.adv_remarks  }</td>                              
                                        <td> ${value.inserted_by}</td>                                  
                                        <td>
                                            <input type="hidden" id="adv_auto_id${value.adv_auto_id}" name="adv_auto_id_list[]" value="${value.adv_auto_id}">                                                             
                                           <input type="checkbox" name="adv_paper_checkbox-${value.adv_auto_id}" id="adv_paper_checkbox-${value.adv_auto_id}" value="0">
                                        </td> 
                                        <td style="color:white"> ${value.adv_auto_id}</td>  
                                        <td>
                                            <a target="_blank" href="{{ url('${value.advance_paper}') }}" class="btn btn-success">View </a>
                                         </td>                                      
                                    </tr>
                                    `
                    });
                    $('#advance_paper_list_table').html(rows);
                }else {
                    showMessage('Data Not Found','error');
                }
            },
            error:function(response){
              //  showMessage(response.message,'error');
                showMessage('Operation Failed','error');
            }
        });
      }


      function getMonthName(monthid){
          if(monthid == 1)
          return "January";
          else if(monthid == 2)
          return "February";
          else if(monthid == 3)
          return "March";
          else if(monthid == 4)
          return "April";
          else if(monthid == 5)
          return "May";
          else if(monthid == 6)
          return "June";
          else if(monthid == 7)
          return "July";
          else if(monthid == 8)
          return "August";
          else if(monthid == 9)
          return "September";
          else if(monthid == 10)
          return "October";
          else if(monthid == 11)
          return "November";
          else if(monthid == 12)
          return "December";
      }

</script>
@endsection