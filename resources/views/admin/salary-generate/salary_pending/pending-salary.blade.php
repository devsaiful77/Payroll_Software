@extends('layouts.admin-master')
@section('title') Employee Pending Salary list @endsection
@section('content')

<style>
    /* Employee Salary Information Table */

    #employeeinfo {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;

    }

    #employeeinfo td,
    #employeeinfo th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #employeeinfo tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #employeeinfo tr:hover {
        background-color: #ddd;
    }

    #employeeinfo th {
        padding-top: 5px;
        padding-bottom: 5px;
        text-align: left;
        background-color: #EAEDED;
        color: black;
    }
</style>
<!-- Session Flash Message -->
<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong> {{Session::get('success')}}</strong>
        </div>
        @endif

        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
        <strong> {{Session::get('error')}}</strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<!-- Top Bar   -->
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Salary Unpaid Employees</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Salary Unpaid</li>
        </ol>
    </div>
</div>


<!-- Searching UI Form -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-body">

                <form method="post" action="#">
                    @csrf
                    <div class="row">
                        <div class="col-md-1"> </div>
                        <div class="col-md-4">
                            <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">Sponsor:</label>
                                        <div class="col-sm-7">
                                            <select class="form-control" name="SponsId" id="SponsId" required>
                                                <option value="0">ALL</option>
                                                @foreach($sponserList as $spons)
                                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Project:</label>
                                <div class="col-sm-7">
                                    <select class="form-control" name="proj_id" id="proj_id" required>
                                        <option value="0">ALL</option>
                                        @foreach($projectlist as $proj)
                                            <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    {{-- Date To Date --}}
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">From:<span class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control fromDate" id="datepickerFrom"   name="fromDate" value="{{date('m/d/Y')}}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row custom_form_group">
                                        <label class="col-sm-3 control-label">To:<span class="req_star">*</span></label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control toDate" id="datepickerTo"   name="toDate" value="{{date('m/d/Y')}}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row custom_form_group">
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" placeholder="Search By Employee ID" autofocus name="employee_id" id="employee_id">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" onclick="searchEmpPendingSalaryList()" id="search_button" style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Salary Pending Emp Searching Result Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="employee-salary-payment-form" action="{{ route('payment.salary') }}" method="post">
                @csrf
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-10">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary waves-effect">Update </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="employeeinfo">
                            <thead>
                                <tr>
                                    <th>S.N</th>
                                    <th>Emp.Id</th>
                                    <th>Name</th>
                                    <th>Iqama</th>
                                    <th>Salary</th>
                                    <th>Sponser</th>
                                    <th>Trade</th>
                                    <th>Project</th>
                                    <th>Month,Year</th>
                                    <th>Salary</th>
                                    <th colspan="2" class="text-center">Manage</th>
                                </tr>
                            </thead>
                            <tbody id="employeePendingList"></tbody>
                        </table>
                    </div>


                </div>
            </form>
        </div>
    </div>
</div>



<!--  Employee Salary Update Modal -->
<div class="modal fade" id="emp_salary_edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Udpate An Employee Salary <span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

                <div class="modal-body">

                        <input type="hidden" id="modal_emp_auto_id" name="modal_emp_auto_id" value="">
                        <input type="hidden" id="modal_slh_auto_id" name="modal_slh_auto_id" value="">

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">ID, Name & Iqama:</label>
                            <div class="col-sm-8">
                                <span id ="employee_details" style="color:red"> </span>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Month</label>
                            <div class="col-sm-3">
                                <input type="text" id="modal_slh_month" class="form-control " name="modal_slh_month"   readonly>
                            </div>

                            <label class="col-sm-3 control-label">Year</label>
                            <div class="col-sm-3">
                                <input type="text" id="modal_slh_year" class="form-control " name="modal_slh_year"   readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Working Days</label>
                            <div class="col-sm-3">
                                <input type="text" id="working_days" class="form-control " name="working_days"   readonly>
                            </div>

                            <label class="col-sm-3 control-label">Total Hours</label>
                            <div class="col-sm-3">
                                <input type="text" id="total_hours" class="form-control " name="total_hours"   readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Total(Excl. Food)</label>
                            <div class="col-sm-3">
                                <input type="number" id="total_amount" class="form-control" name="total_amount"   readonly>
                            </div>
                            <label class="col-sm-3 control-label">Food</label>
                            <div class="col-sm-3">
                                <input type="number" id="food_amount" class="form-control " name="food_amount" readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Mobile </label>
                            <div class="col-sm-3">
                                <input type="number" id="mobile_allowance" class="form-control" name="mobile_allowance"   readonly>
                            </div>
                            <label class="col-sm-3 control-label">Medical</label>
                            <div class="col-sm-3">
                                <input type="number" id="medical_allowance" class="form-control " name="medical_allowance" readonly>
                            </div>
                        </div>



                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Total (Inc. All)</label>
                            <div class="col-sm-8">
                            <input type="number" id="grand_total_salary" class="form-control " name="grand_total_salary" readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Catering Service</label>
                            <div class="col-sm-8">
                            <input type="number" id="catering_service_amount" class="form-control " name="catering_service_amount" readonly>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Food New Amnt </label>
                            <div class="col-sm-4">
                                <input type="number" id="new_food_amount" class="form-control " name="new_food_amount" value="0" min="0" max="1000" autofocus required>
                            </div>
                            <div class="col-sm-4">
                                <span id ="new_grand_total_salary" style="font-weight:bold; color:red" > </span>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Saudi Tax</label>
                            <div class="col-sm-8">
                                <input type="number" id="saudi_tax" class="form-control " name="saudi_tax" value="0" min="0" max="300" required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Other Advance</label>
                            <div class="col-sm-8">
                                <input type="number" id="new_other_advance" class="form-control " name="new_other_advance" value="0" min="0" required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-4 control-label">Iqama Renewal</label>
                            <div class="col-sm-8">
                            <input type="number" id="new_iqama_advance" class="form-control " name="new_iqama_advance" value="0" min="0" required>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">

                                <label class="col-sm-4 control-label"><b style="color:red">Receivable Salary </b></label>
                                <div class="col-sm-4">
                                    <input type="number" id="new_receivable_total_salary" class="form-control " style="font-weight:bold; color:red" name="new_receivable_total_salary"  min="0" readonly required>
                                </div>
                                <div class="col-sm-3">
                                    <input name="salary_paid__status" id="salary_paid__status" type="checkbox" >&nbsp;Salary Paid
                                </div>

                        </div>

                        <br><br>
                    <!-- <div class="modal-footer"> -->
                    <button type="submit" id="updatebtn" name="updatebtn" onclick="sumbitSalaryCorrectionData()"  class="btn btn-success"  >Salary Update</button>

                </div>

            <!-- </form> -->
        </div>
    </div>
</div>

<script>

    // Datepicker Selection Event
    $('document').ready(function() {
        $('#datepickerFrom').datepicker({
            autoclose: true,
            toggleActive: true,
            // startView: "months",  // Ata 1st month select korle date asbe
            minViewMode: "months",
            // format: "mm/yyyy",
        });

        $('#datepickerTo').datepicker({
            autoclose: true,
            toggleActive: true,
            minViewMode: "months",
            // format: "mm/yyyy",
        });
    });

    $('#employee_id').keydown(function (e) {
        if (e.keyCode == 13) {
            searchEmpPendingSalaryList();
        }

    })

    function showSweetAlert(message,opeation_status){
        const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            })
                            Toast.fire({
                                type: opeation_status,// 'error',
                                title: message
                            })

    }

    function deletePendingSalary(salary_auto_id){

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
                    url: "{{  url('admin/employee/salary/delete') }}/" + salary_auto_id,
                    dataType: 'json',
                    success: function (response) {
                        if(response.status == 200){
                            showSweetAlert("Successfully Deleted",'success');
                            searchEmpPendingSalaryList();
                        }else {
                            showSweetAlert("Operation Failed",'error');
                        }

                    },
                    error:function(response){
                        showSweetAlert("Operation Failed",'error');
                    }
                });
            }
        });
        //  window.location.reload();
    }

    function searchEmpPendingSalaryList() {

        var fromDate = $('.fromDate').val();
        var toDate = $('.toDate').val();
        var proj_id = $('#proj_id').val();
        var SponsId = $('#SponsId').val();
        var employee_id = $('#employee_id').val();
        if (fromDate == '' || toDate == '') {
            showSweetAlert("Please Select Month and Year",'error');
            return;
        }

        document.getElementById("search_button").disabled = true;
        $('#employeePendingList').html('');
            $.ajax({
                type: "POST",
                url: "{{ route('salary-pending.list') }}",
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
                    SponsId: SponsId,
                    proj_id: proj_id,
                    employee_id: employee_id
                },
                dataType: "json",
                success: function(response) {

                    var rows = "";
                    var counter = 0;
                    document.getElementById("search_button").disabled = false;
                    if(response.status != 200){
                        showSweetAlert(response.message,'error');
                        return;
                    }
                    $.each(response.pendingSalary, function(key, value) {

                            counter++;
                            rows += `
                                    <tr>
                                        <td>${counter}</td>
                                        <td>${value.employee.employee_id}</td>
                                        <td>${value.employee.employee_name}</td>
                                        <td>${value.employee.akama_no}</td>
                                        <td>${value.employee.hourly_employee == 1 ? 'Hourly':'Basic'}</td>
                                        <td>${value.employee.sponsor.spons_name}</td>
                                        <td>${value.employee.category.catg_name}</td>
                                        <td>${value.proj_name}</td>
                                        <td>${value.month.month_name},${value.slh_year}</td>
                                        <td>${value.slh_total_salary}</td>
                                        <td  style="width:100px; align-items:center;">

                                            <input type="hidden" id="slh_auto_id${value.slh_auto_id}" name="slh_auto_id[]" value="${value.slh_auto_id}">
                                            <input type="checkbox" name="emp_slh_paid_checkbox-${value.slh_auto_id}" id="emp_slh_paid_checkbox-${value.slh_auto_id}" value="0">
                                            @can('employee_salary_record_edit')
                                            ||
                                            <a href="" id="salary_edit_button" data-toggle="modal" data-target="#emp_salary_edit_modal" data-id="${value.slh_auto_id}">Edit</a>
                                            @endcan
                                            </td>
                                        <td>
                                            @can('employee_salary_record_delete')
                                                <a href="#" onClick="deletePendingSalary(${value.slh_auto_id})" title="Delete"><i id="" class="fa fa-trash fa-lg delete_icon"></i></a>
                                            @endcan
                                        </td>
                                    </tr>
                                `
                    });
                    $('#employeePendingList').html(rows);
                },
                error:function(response){
                    showSweetAlert('Operation Failed ','error');
                }
            });

          //  $('#employee_id').val("");
          document.getElementById("employee_id").focus();
          $('#employee_id').select();
    }

        // Open Modal For Update Employee Salary Information
    $(document).on("click", "#salary_edit_button", function(){

        var slh_auto_id = $(this).data('id');

        $.ajax({
            type: "post",
            url: "{{ route('get.amemployee.unpaid.salary.record.byslh.autoid') }}",
            data: {slh_auto_id: slh_auto_id},
            datatype:"json",
            success: function(response){

                if(response.status == 200){

                    var arecord = response.arecord;
                    $('#modal_emp_auto_id').val(arecord.emp_auto_id);
                    $('#modal_slh_auto_id').val(arecord.slh_auto_id);
                    $('#employee_details').text(arecord.employee_id+", "+arecord.employee_name+", "+arecord.akama_no+",Basic/Hourly: "+arecord.basic_amount+"/"+arecord.hourly_rent);
                    $('#employee_iqama').val(arecord.akama_no);
                    $('#working_days').val(arecord.slh_total_working_days);
                    $('#total_hours').val(arecord.slh_total_hours);
                    $('#modal_slh_month').val(arecord.slh_month);
                    $('#modal_slh_year').val(arecord.slh_year);

                   var slh_all_include_amount = parseFloat(arecord.slh_all_include_amount);

                    if(slh_all_include_amount <= 0){
                        slh_all_include_amount = parseFloat( arecord.slh_total_salary) + parseFloat( arecord.slh_iqama_advance) + parseFloat(arecord.slh_other_advance) + parseFloat(arecord.slh_saudi_tax) + parseFloat(arecord.slh_food_deduction);
                    }
                    var total_working_amount = slh_all_include_amount - (parseFloat( arecord.food_allowance) + parseFloat( arecord.mobile_allowance) + parseFloat( arecord.medical_allowance) );

                    $('#catering_service_amount').val(arecord.slh_food_deduction);
                    $('#total_amount').val(total_working_amount);
                    $('#food_amount').val(arecord.food_allowance);
                    $('#mobile_allowance').val(arecord.mobile_allowance);
                    $('#medical_allowance').val(arecord.medical_allowance);
                    $('#grand_total_salary').val(slh_all_include_amount);

                    $('#new_food_amount').val(arecord.food_allowance);
                    $('#saudi_tax').val(arecord.slh_saudi_tax);
                    $('#new_other_advance').val(arecord.slh_other_advance);
                    $('#new_iqama_advance').val(arecord.slh_iqama_advance);
                    $('#new_receivable_total_salary').val(arecord.slh_total_salary);
                    // set default checked
                   // document.getElementById('salary_paid__status').checked = true;

                }else{
                    $('#errorData').text(response.error);
                }
            },
            error:function(response){
                showSweetAlert('Operation Failed ','error');
            }
        })

    });

    // all number field on focus value selection code
    $("input[type='number']").on("click", function () {
        $(this).select();
    });

    $("#new_food_amount").on("input", function() {
        calculateTotalReceivableSalary();
    });
    $("#saudi_tax").on("input", function() {
        calculateTotalReceivableSalary();
    });
    $("#new_other_advance").on("input", function() {
        calculateTotalReceivableSalary();
    });
    $("#new_iqama_advance").on("input", function() {
        calculateTotalReceivableSalary();
    });

    function calculateTotalReceivableSalary(){

            if ($('#new_food_amount').val() == "" || $('#new_food_amount').val() == null) {
                $('#new_food_amount').val('0');

            }
            if ($('#saudi_tax').val() == "" || $('#saudi_tax').val() == null) {
                $('#saudi_tax').val('0');

            }
            if ($('#new_other_advance').val() == "" || $('#new_other_advance').val() == null) {
                $('#new_other_advance').val('0');

            }
            if ($('#new_iqama_advance').val() == "" || $('#new_iqama_advance').val() == null) {
                $('#new_iqama_advance').val('0');

            }


        var grand_total_salary = parseFloat($('#total_amount').val());
        var new_food_amount = parseFloat($('#new_food_amount').val());

       var new_grand_total_salary = grand_total_salary + new_food_amount;
       $('#new_grand_total_salary').text("New Total: "+ new_grand_total_salary);

        var saudi_tax = parseFloat($('#saudi_tax').val());
        var catering_service_amount = parseFloat($('#catering_service_amount').val());

        var new_other_advance = parseFloat($('#new_other_advance').val());
        var new_iqama_advance = parseFloat($('#new_iqama_advance').val());
        var total = (grand_total_salary + new_food_amount) - (new_other_advance + saudi_tax + new_iqama_advance + catering_service_amount);

        $('#new_receivable_total_salary').val(total.toFixed(2));

       // alert(food_amount);
    }

    function sumbitSalaryCorrectionData(){
        // UI reset
        $("#emp_salary_edit_modal").modal('hide');
        $('#employee_details').text('');
        $('#new_grand_total_salary').text("");

        var total_amount = $('#total_amount').val();
        var new_food_amount = $('#new_food_amount').val();
        var saudi_tax = $('#saudi_tax').val();
        var new_other_advance = $('#new_other_advance').val();
        var new_iqama_advance = $('#new_iqama_advance').val();
        var new_receivable_total_salary = $('#new_receivable_total_salary').val();

        var emp_auto_id = $('#modal_emp_auto_id').val();
        var slh_auto_id = $('#modal_slh_auto_id').val();
        var salary_month = $('#modal_slh_month').val();
        var salary_year = $('#modal_slh_year').val();
        var working_days = $('#working_days').val();
        var salary_paid__status = (document.getElementById('salary_paid__status').checked) == true ? 1:0;

        $.ajax({
            type:"POST",
            url:"{{route('employee.salary.update.request')}}",
            data:{
                working_days:working_days,
                total_amount:total_amount,
                new_food_amount:new_food_amount,
                saudi_tax:saudi_tax,
                new_other_advance:new_other_advance,
                new_iqama_advance:new_iqama_advance,
                new_receivable_total_salary:new_receivable_total_salary,
                salary_month:salary_month,
                salary_year:salary_year,
                emp_auto_id:emp_auto_id,
                slh_auto_id:slh_auto_id  ,
                salary_paid__status:salary_paid__status,
            },
            datatype:"json",
            success:function(response){
               if(response.status == 200){
                showSweetAlert('Update Operation Successfully Completed','success');
                searchEmpPendingSalaryList();
               }else {
                showSweetAlert('Update Operation Failed','error');
               }

            },
            error:function(response){
                showSweetAlert('Update Operation Failed','error');
            }

        })


    }

    // Modal View Showing Event
    $('#emp_salary_edit_modal').on('shown.bs.modal', function() {
        $(this).find('[autofocus]').focus();
        $('#new_food_amount').select();
    });

    //  Modal View Hidden Event, Reset Modal Previous Data
    $('#emp_salary_edit_modal').on('hidden.bs.modal', function (e) {
      $(this)
      .find("input,textarea,select").val('').end()
      .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();

    })



</script>
@endsection
