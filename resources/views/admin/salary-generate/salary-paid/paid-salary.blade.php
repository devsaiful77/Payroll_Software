@extends('layouts.admin-master')
@section('title')Salary Paid @endsection
@section('content')



<style>
    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid blue;
        border-right: 16px solid green;
        border-bottom: 16px solid red;
        border-left: 16px solid pink;
        width: 100px;
        height: 158px;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
    }

    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
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
        <h4 class="pull-left page-title bread_title">Salary Paid Employees</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Salary Paid</li>
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
                        <div class="form-group row custom_form_group">
                            <label class="col-md-1 control-label">Sponsor:</label>
                            <div class="col-md-3">
                                <select class="form-control" name="SponsId" id="SponsId" required>
                                    <option value="0">ALL</option>
                                    @foreach($sponserList as $spons)
                                    <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-md-1 control-label">Project:</label>
                            <div class="col-md-3">
                                <select class="form-control" name="proj_id" id="proj_id" required>
                                    <option value="0">ALL</option>
                                    @foreach($projectlist as $proj)
                                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group row custom_form_group">
                            <label class="col-sm-1 control-label">From:<span class="req_star">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control fromDate" id="datepickerFrom" autocomplete="off" name="fromDate" value="{{date('m/Y')}}" required>
                            </div>
                            <label class="col-sm-1 control-label">To:<span class="req_star">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control toDate" id="datepickerTo" autocomplete="off" name="toDate" value="{{date('m/Y')}}" required>
                            </div>
                            <div class="col-sm-3">
                                <input type="number" class="form-control" placeholder="Search By Employee ID" autofocus name="employee_id" id="employee_id">
                            </div>
                            <div class="col-md-1">
                                <button type="button" onclick="searchPaidSalaryRecords()" style="margin-top: 2px" class="btn btn-primary waves-effect">SEARCH</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- Searching Result Table -->
        <div class="row" id="salary_paid_records_section">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Emp.Id</th>
                                <th>Name</th>
                                <th>Iqama</th>
                                <th>Sponser</th>
                                <th>Trade</th>
                                <th>Type</th>
                                <th>Month</th>
                                <th>Salary</th>
                                <th colspan="2" class="text-center">Manage</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="employeePendingList"></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>





<script>
    $(document).ready(function() {
       // searchPaidSalaryRecords();
    });

    $('.loader')
        .hide() // Hide it initially
        .ajaxStart(function() {
            $(this).show();
        })
        .ajaxStop(function() {
            $(this).hide();

        });

    var $loading = $('.loader').hide();
    $(document)
        .ajaxStart(function() {
            $loading.show();
          //  alert('loading start');
        })
        .ajaxStop(function() {
            $loading.hide();
           // alert('loading done');
        });

    $('#employee_id').keydown(function (e) {
        if (e.keyCode == 13) {
            searchPaidSalaryRecords();
        }

    })

    function searchPaidSalaryRecords() {

        var fromDate = $('.fromDate').val();
        var toDate = $('.toDate').val();
        var proj_id =   $('#proj_id').val();
        var SponsId =   $('#SponsId').val();
        var employee_id = $('#employee_id').val();

        if (proj_id == 0 && SponsId == 0 && employee_id == null) {
            showSweetAlert("Please Input Data Correctly ",'error');
            return;
        }

        $('#employeePendingList').html('');
        $.ajax({
                type: "POST",
                url: "{{ route('salary-paid.list') }}",
                data: {
                    fromDate: fromDate,
                    toDate: toDate,
                    SponsId: SponsId,
                    proj_id: proj_id,
                    employee_id:employee_id,
                },
                dataType: "json",
                success: function(response) {

                        if(response.status != 200){
                            showSweetAlert('Records Not Found','error');
                            return;
                        }else if(response.pendingSalary.length == 0){
                            showSweetAlert('Salary Records Not Found','error');
                            return;
                        }
                        var rows = "";
                        var counter = 0;
                        $.each(response.pendingSalary, function(key, value) {
                            counter++;
                            rows += `
                                    <tr>
                                        <td>${counter}</td>
                                        <td>${value.employee.employee_id}</td>
                                        <td>${value.employee.employee_name}</td>
                                        <td>${value.employee.akama_no}</td>
                                        <td>${value.employee.sponsor.spons_name}</td>
                                        <td>${value.employee.category.catg_name}</td>
                                        <td>${value.employee.type.name}</td>
                                        <td>${value.month.month_name},${value.slh_year}</td>
                                        <td>${value.slh_total_salary}</td>
                                        <td id="">
                                        <a title="Salary Unpaid" onclick="updateAnEmpSalaryStatusAsPaidToUnpaid(${value.slh_auto_id})"><i class="fas fa-thumbs-up fa-lg edit_icon"></i>Click to Unpaid</a>
                                        </td>
                                    </tr>
                                `
                        });
                        $('#employeePendingList').html(rows);

                },
                error:function(response){
                    showSweetAlert('Operation Failed, Please try Again','error');
                }

        });

    }

    // pay salay function

    function updateAnEmpSalaryStatusAsPaidToUnpaid(id) {
        if (id) {
            $.ajax({
                type: "POST",
                url: "{{route('payment.salary.undo')}}",
                dataType: "json",
                data: {
                    id: id
                },
                success: function(response) {

                    searchPaidSalaryRecords();
                    //  start message
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    })
                    if ($.isEmptyObject(data.error)) {
                        Toast.fire({
                            type: 'success',
                            title: data.success
                        })
                    } else {
                        Toast.fire({
                            type: 'error',
                            title: data.error
                        })
                    }
                    //  end message
                }
            })
        } else {
             showSweetAlert('Employee Not Found ','error');
        }

    }


    // show sweet alert message
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

    // Datepicker
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
            // viewMode: "months",
            minViewMode: "months",
            // format: "mm/yyyy",
        });
    });
</script>
@endsection
