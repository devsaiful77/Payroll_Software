@extends('layouts.admin-master')
@section('title') Employee Entry & Out @endsection
@section('content')

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

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Attendence (IN)</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Attendence</li>
        </ol>
    </div>
</div>

<!-- Message Display Section !-->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{ Session::get('success') }}</strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{ Session::get('error') }}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Employee Search UI !-->
<div class="row">

    <div class="col-md-12">
        <div class="card">
            <form class="form-horizontal" id="emp_list_search_form" action="" method="">
                <div class="card-body card_form">
                <div class="form-group row custom_form_group">
                    <?php $today = date("Y-m-d");
                      $next_date = date('Y-m-d',strtotime((-1*$allow_days).' days'));                      
                    ?>
                    <label class="col-sm-2 control-label"> Attendance Date:</label>
                    <div class="col-sm-2">   
                        <input type="hidden" id="allows_days" name="allows_days" value="{{$allow_days}}" >                    
                        <input type="date" name="search_date" value="<?= date("Y-m-d")  ?>"  min="{{ $next_date }}" max="{{date('Y-m-d',strtotime('1 days')) }}" class="form-control">
                    </div>
                    <div class="col-sm-1">
                        <input name="night_shift" id="night_shift" type="checkbox" value="0">
                        <label for="saldetlabel">Night Shift</label>
                    </div>
                    <label class="col-sm-2 control-label">Project Name:</label>
                    <div class="col-sm-3">
                        <select class="form-control" name="proj_name">
                            <option value="0">Select Project</option>
                            @foreach($project as $proj)
                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" onclick="searchProjectWiseEmployeeList()"
                            class="btn btn-primary waves-effect">Search Employee</button>
                    </div>
                </div>

                </div>
            </form>
        </div>
    </div>

</div>



<!-- Employee Entry IN List Table !-->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="employee-entry-in-form" action="{{ route('employee-entry-time-insert') }}" onsubmit="attendance_submit_button.disabled = true;" method="post">
                @csrf
                <div class="card-header">
                    <div class="row">

                        <div class="form-group row custom_form_group" > 
                            <div class="col-sm-1">
                                <input type="date" name="date" id="attn_date" value="<?= date("Y-m-d") ?>" hidden class="form-control" required>
                                <input name="night_shift" id="attn_night_shift" type="checkbox" value="0" hidden>
                                <input type="hidden"  name="attn_project_id" id="attn_project_id" required>
                            </div>

                            <label class="col-sm-1 control-label">IN Time:</label>
                            <div class="col-sm-3">
                                <input type="number" name="entry_in_time" id="entry_in_time" class="form-control"
                                    value="" placeholder="Attendance Time (24 Format) "
                                    max="23" step="0.5" min="1" required>
                            </div>
                            <div class="col-sm-3">
                                <select class="form-control" name="attendance_status">
                                    <option value="AW">Working</option>
                                    <option value="TL">Travel Leave</option>
                                    <option value="SL">Sick Leave</option>
                                    <option value="PH">Holiday</option>
                                    <option value="BW">Bad Weather</option>
                                    <option value="NW">No_Work_In_Project</option>
                                </select>
                            </div>
                           <div class="col-sm-3">
                             <button type="button" id="select_button" onclick="checkUnCheckAllEmployeeForAttendanceIn()" class="btn btn-primary waves-effect" >Check/UnCheck</button> &nbsp; &nbsp;
                            </div>
                            <div class="col-sm-1">
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
                                            <th>Iqama No.</th>
                                            <th>Salary Type</th>
                                            <th>Trade</th>
                                            <th>Emp.ID</th>
                                            <th colspan="2" >Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employee_entry_in_table_list"></tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer card_footer_expode">
                </div>
            </form>
        </div>
    </div>
</div>

 <div class="overlay"></div>

<script type="text/javascript">
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
         // employee searching form validation
        $('#emp_list_search_form').validate({
            submitHandler:function(form){
                return true;
            },
            rules:{
                search_date:{
                    required:true,
                },
                proj_name:{
                    required:true,
                }
            },
            messages:{
                search_date:{
                    required: "Please inputer valid date",
                },
                proj_name:{
                    required:"Please Select Project Name",
                }
            }
        });

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

                search_date: {
                    required: "You Must Be Select This Field!",
                },
                entry_in_time: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum 23!",
                },
            },


        });

        // initially hidden button
        document.getElementById("select_button").style.display='none'; 
    });

    // any changes from keystrokes to arrow clicks and keyboard/mouse paste, not supported in IE <9.
    $('#entry_in_time').on('input', function() {
        empoyeeAttendanceInTimeValidation(); 
    });

    function empoyeeAttendanceInTimeValidation() {
         
        document.getElementById("attendance_submit_button").disabled = false;        
        var outTime = parseFloat($('#entry_in_time').val());
        if (outTime > 23 || outTime < 0) {
            $('#entry_in_time').val('');
        }
  
    }

    // Get Employee List by Project Name
    function searchProjectWiseEmployeeList() {
 
        var project_id =parseInt($('select[name="proj_name"]').val());
        var search_date = $('input[name="search_date"]').val();
        var isNightShift = document.getElementById("night_shift").checked;
        isNightShift = isNightShift == true ? 1: 0;
        
        $('#employee_entry_in_table_list').html('');  
        document.getElementById("attn_date").value = '' ;
        document.getElementById("attn_project_id").value = ''; 
        document.getElementById("attn_night_shift").checked = 0;
        allows_days =  $('input[name="allows_days"]').val();

        
        var currentDate = new Date();
        search_date =new Date(search_date);
        var day_diff = currentDate.getDate()-search_date.getDate()

        if(day_diff >allows_days || day_diff < -1){           
            showSweetAlertMessage('error',"Select Valid Date ");
            return;
        }
       
           if(search_date.getDate() == search_date.getDate())
 
            if (project_id <= 0  ||  search_date.length == 0 )  {
                showSweetAlertMessage('error',"Please Select Project and Date");
                return;
            }
            $.ajax({

                type: 'POST',
                dataType: 'json',
                data: {
                    project_id: project_id,
                    search_date: search_date,
                    isNightShift: isNightShift
                },
                url: "{{ route('employee.list.ajax.request.for.attendance.in') }}",
                success: function (response) {

                        if(response.status != 200){
                            showSweetAlertMessage(response.error,response.message);
                            return;
                        }                  
                        if(response.data.length > 250){
                           $("#select_button").show(); 
                        }else {                       
                           $("#select_button").hide();                         
                        }
                        var rows = "";
                        var counter = 1;
                        $.each(response.data, function (key, value) {
                            rows += `
                                <tr>
                                    <td>${counter++}</td>
                                    <td>${value.employee_id}</td>
                                    <td>${value.employee_name}</td>
                                    <td>${value.akama_no} </td>
                                    <td> ${value.hourly_employee == 1 ? 'Hourly':'Basic'} </td>
                                    <td>${value.catg_name}</td>
                                    <td> ${value.employee_id}</td>
                                    <td style="color:#fff">${value.emp_auto_id}</td>
                                    <td id="">   
                                        <input type="hidden" id="emp_auto_id${value.emp_auto_id}" name="emp_auto_id[]" value="${value.emp_auto_id}">                                 
                                        <input type="checkbox" name="entry_in_checkbox-${value.emp_auto_id}" id="entry_in_checkbox-${value.emp_auto_id}" value="0">
                                    </td>
                                </tr>
                                `
                        });
                        
                         $('#employee_entry_in_table_list').html(rows);                                                 
                         document.getElementById("attn_night_shift").checked = isNightShift;
                         document.getElementById("attn_date").value = search_date ;
                         document.getElementById("attn_project_id").value = project_id;                 
 
                },
                error:function(response){
                    showSweetAlertMessage(response.error,response.message);
                    return;
                }
            });          
    }

    function showSweetAlertMessage(type,message){
        const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })            
                Toast.fire({
                    type: type,
                    title: message,
                })
    }

     // Check uncheck 
    function checkUnCheckAllEmployeeForAttendanceIn(){
        let myTable = document.getElementById('employee_entry_in_table_list');
            for (let row of myTable.rows) {
                allCell = row.cells;          
                var chkboxId = "entry_in_checkbox-" + allCell[7].innerText;         
                document.getElementById(chkboxId).checked = !(document.getElementById(chkboxId).checked) ; 
            }
    }


</script>

 

@endsection
