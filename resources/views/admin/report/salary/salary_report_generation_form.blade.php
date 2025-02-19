@extends('layouts.admin-master')
@section('title') Salary Report @endsection
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
        background: rgba(255, 255, 255, 0.8) url('{{ asset("Loading.gif")}}') center no-repeat;
    }

    /* Turn off scrollbar when body element has the loading class */
    body.loading {
        overflow: hidden;
    }

    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay {
        display: block;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
</style>

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title"> Salary Reprot </h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active"> Salary Report</li>
    </ol>
  </div>
</div>

<div class="row">
  @can('prevacation_salary_summary_statement')
      <div class="col-md-4 col-xl-3">
        <a href="" id="leave_application_edit_button" data-toggle="modal" data-target="#prevacation_salary_report_modal">
          <div class="mini-stat clearfix bx-shadow bg-white">
              <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
              <div class="mini-stat-info text-center text-dark mini_stat_info">
                  Prevacation Salary Report
              </div>
          </div>
        </a>
      </div>
  @endcan

  @can('salary_closing_employee_report')
    <div class="col-md-4 col-xl-3">
      <a href="" id="leave_application_edit_button" data-toggle="modal" data-target="#salary_closing_report_modal">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-center text-dark mini_stat_info">
              Salary Closing Report
            </div>
        </div>
      </a>
    </div>
  @endcan

  @can('office_staff_employee_salary_sheet_print')
    <div class="col-md-4 col-xl-3">
      <a href="" id="leave_application_edit_button" data-toggle="modal" data-target="#office_staff_salary_report_modal">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-center text-dark mini_stat_info">
              Staff Salary
            </div>
        </div>
      </a>
    </div>
  @endcan

  @can('debit_invoice_daily_report')
    <div class="col-md-4 col-xl-3">
      <a href="" id="expense_report_button" data-toggle="modal" data-target="#expense_by_empid_report_modal">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-center text-dark mini_stat_info">
             Cash Received Report
            </div>
        </div>
      </a>
    </div>
  @endcan


@can('salary_bonus_report')
  <div class="col-md-4 col-xl-3">
    <a href="" id="bonus_report_button" data-toggle="modal" data-target="#emp_bonus_report_modal">
      <div class="mini-stat clearfix bx-shadow bg-white">
          <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
          <div class="mini-stat-info text-center text-dark mini_stat_info">
           Bonus Report
          </div>
      </div>
    </a>
  </div>
@endcan



</div>


<!-- Prevacation Salary Report Modal -->
<div class="modal fade" id="prevacation_salary_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Prevacation Salary Report </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="employee_salary_report_form" target="_blank" action="{{ route('anemployee.salary.report') }}" method="post">
        @csrf
          <div class="modal-body">
            <input type="text" id="report_type" hidden name="report_type" value="4">
            <input type="text" class="form-control col-sm-12 typeahead" placeholder="Enter Multiple Employee ID" name="multiple_employee_Id" id="multiple_employee_Id" autofocus>
          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                <button type="submit" class="btn btn-primary">Report</button>
                </div>
                <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
          </div>
        </form>
    </div>
  </div>
</div>

<!-- Salary Closing Report Modal -->
<div class="modal fade" id="salary_closing_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Salary Closing Report </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="salary_closing_report_form" target="_blank" action="{{ route('salary.closing.report') }}" method="post">
        @csrf
          <div class="modal-body">
               <div class="form-group row custom_form_group">
                <label class="control-label col-md-2">From</label>
                <div class="col-md-4">
                  <input type="date" class="form-control" name="from_date" value="{{date("Y-m-d")}}">
                    {{-- <select class="form-select" name="month" id="month" required>
                        <option value="1" {{ 1 == date('m') ? 'selected' :'' }}> January</option>
                        <option value="2" {{ 2 == date('m') ? 'selected' :'' }}> February</option>
                        <option value="3" {{ 3 == date('m') ? 'selected' :'' }}> March</option>
                        <option value="4" {{ 4 == date('m') ? 'selected' :'' }}> April</option>
                        <option value="5" {{ 5 == date('m') ? 'selected' :'' }}> May</option>
                        <option value="6" {{ 6 == date('m') ? 'selected' :'' }}> June</option>
                        <option value="7" {{ 7 == date('m') ? 'selected' :'' }}> July</option>
                        <option value="8" {{ 8 == date('m') ? 'selected' :'' }}> Auguest</option>
                        <option value="9" {{ 9 == date('m') ? 'selected' :'' }}> September</option>
                        <option value="10" {{ 10 == date('m') ? 'selected' :'' }}> October</option>
                        <option value="11" {{ 11 == date('m') ? 'selected' :'' }}> November</option>
                        <option value="12" {{ 12 == date('m') ? 'selected' :'' }}> December</option>
                    </select> --}}
                </div>
                <label class="col-sm-2 control-label"> To </label>
                <div class="col-sm-3">
                    <input type="date" class="form-control" name="to_date" value="{{date("Y-m-d")}}">
                    {{-- <select class="form-select" name="year"  id="year">
                        @foreach(range(date('Y'), date('Y')-1) as $y)
                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                        @endforeach
                    </select> --}}
                </div>
              </div>
              <div class="form-group row custom_form_group">
                <label class="control-label col-md-2">Report</label>
                  <select name="report_type" class="form-control col-md-10" id="report_type">
                    <option value="1">Employee List</option>
                    <option value="2">Summary Month by Month</option>
                  </select>
              </div>

          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                <button type="submit" class="btn btn-primary">Report</button>
                </div>
                <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
          </div>
        </form>
    </div>
  </div>
</div>


<!-- Salary Hold Employee Salary Report Modal -->
<div class="modal fade" id="salary_hold_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center red" id="exampleModalLabel">Salary hold Employees Salary Report </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="salary_hold_report_form" target="_blank" action="{{ route('employee.salary.sheet.print_preview.bysalary_type') }}" method="post">
        @csrf
          <div class="modal-body">
              <input type="hidden" name="salary_report_type" name="salary_report_type"  value="1">
              <div class="form-group row custom_form_group">
                  <label class="control-label col-md-2">Project</label>
                  <div class="col-md-10">
                    <select name="project_ids[]" class="selectpicker" id="project_ids[]" multiple>
                      @foreach($projects as $p)
                          <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                      @endforeach
                    </select>
                  </div>
              </div>

               <div class="form-group row custom_form_group">
                <label class="control-label col-md-2">Month</label>
                <div class="col-md-4">
                    <select class="form-select" name="month" id="month" required>
                        <option value="1" {{ 1 == date('m') ? 'selected' :'' }}> January</option>
                        <option value="2" {{ 2 == date('m') ? 'selected' :'' }}> February</option>
                        <option value="3" {{ 3 == date('m') ? 'selected' :'' }}> March</option>
                        <option value="4" {{ 4 == date('m') ? 'selected' :'' }}> April</option>
                        <option value="5" {{ 5 == date('m') ? 'selected' :'' }}> May</option>
                        <option value="6" {{ 6 == date('m') ? 'selected' :'' }}> June</option>
                        <option value="7" {{ 7 == date('m') ? 'selected' :'' }}> July</option>
                        <option value="8" {{ 8 == date('m') ? 'selected' :'' }}> Auguest</option>
                        <option value="9" {{ 9 == date('m') ? 'selected' :'' }}> September</option>
                        <option value="10" {{ 10 == date('m') ? 'selected' :'' }}> October</option>
                        <option value="11" {{ 11 == date('m') ? 'selected' :'' }}> November</option>
                        <option value="12" {{ 12 == date('m') ? 'selected' :'' }}> December</option>
                    </select>
                </div>
                <label class="col-sm-2 control-label"> Year </label>
                <div class="col-sm-4">
                    <select class="form-select" name="year"  id="year">
                        @foreach(range(date('Y'), date('Y')-2) as $y)
                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                        @endforeach
                    </select>
                </div>
              </div>


          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                <button type="submit" class="btn btn-primary">Process</button>
                </div>
                <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
          </div>
        </form>
    </div>
  </div>
</div>

<!-- Office Staff Salary Report Modal -->
<div class="modal fade" id="office_staff_salary_report_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center red" id="exampleModalLabel"> Office Staff Employees Salary Report </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" id="office_staff_salary_report_form" target="_blank" action="{{ route('employee.salary.sheet.print_preview.bysalary_type') }}" method="post">
        @csrf
          <div class="modal-body">

               <input type="hidden" name="salary_report_type" name="salary_report_type"  value="2">
              <div class="form-group row custom_form_group">
                  <label class="control-label col-md-2">Project</label>
                  <div class="col-md-10">
                    <select name="project_ids[]" class="selectpicker" id="project_ids[]" multiple>
                      @foreach($projects as $p)
                          <option value="{{$p->proj_id}}">{{$p->proj_name}}</option>
                      @endforeach
                    </select>
                  </div>
              </div>

               <div class="form-group row custom_form_group">
                <label class="control-label col-md-2">Month</label>
                <div class="col-md-4">
                    <select class="form-select" name="month" id="month" required>
                        <option value="1" {{ 1 == date('m') ? 'selected' :'' }}> January</option>
                        <option value="2" {{ 2 == date('m') ? 'selected' :'' }}> February</option>
                        <option value="3" {{ 3 == date('m') ? 'selected' :'' }}> March</option>
                        <option value="4" {{ 4 == date('m') ? 'selected' :'' }}> April</option>
                        <option value="5" {{ 5 == date('m') ? 'selected' :'' }}> May</option>
                        <option value="6" {{ 6 == date('m') ? 'selected' :'' }}> June</option>
                        <option value="7" {{ 7 == date('m') ? 'selected' :'' }}> July</option>
                        <option value="8" {{ 8 == date('m') ? 'selected' :'' }}> Auguest</option>
                        <option value="9" {{ 9 == date('m') ? 'selected' :'' }}> September</option>
                        <option value="10" {{ 10 == date('m') ? 'selected' :'' }}> October</option>
                        <option value="11" {{ 11 == date('m') ? 'selected' :'' }}> November</option>
                        <option value="12" {{ 12 == date('m') ? 'selected' :'' }}> December</option>
                    </select>
                </div>
                <label class="col-sm-2 control-label"> Year </label>
                <div class="col-sm-4">
                    <select class="form-select" name="year"  id="year">
                        @foreach(range(date('Y'), date('Y')-2) as $y)
                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                        @endforeach
                    </select>
                </div>
              </div>


          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-sm-6 text-left">
                <button type="submit" class="btn btn-primary">Process</button>
                </div>
                <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
          </div>
        </form>
    </div>
  </div>
</div>



<!-- Cash Receive From Rashed Vai as Expense by Emp ID Report !-->
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




<!-- Employee Bonus Report By Emp Id Or Date To Date !-->
<div class="modal fade" id="emp_bonus_report_modal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Bonus Report </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <div class="modal-body">


                   <form class="form-horizontal" id="employee_bonus_report_process_form" target="_blank" action="{{ route('employee.bonus.details.report') }}" method="post">
                    @csrf

                    <select name="bonus_report_type" id="bonus_report_type" hidden>
                        <option value="1">Date to Date Report</option>
                    </select>

                    <select name="bonus_type" id="bonus_type" hidden>
                        <option value="1">Bonus Type</option>
                    </select>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Employee ID </label>
                        <div class="col-sm-8">
                            <input type="number" id="employee_id" placeholder="Enter Employee ID Number" name="employee_id" class="form-control">
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">From Date<span class="req_star">*</span> </label>
                        <div class="col-sm-8">
                            <input type="date" id="from_date" name="from_date" value="<?= date("Y-m-d") ?>" class="form-control">
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">To Date<span class="req_star">*</span> </label>
                        <div class="col-sm-8">
                            <input type="date" id="to_date" name="to_date" value="<?= date("Y-m-d") ?>" class="form-control">
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="text-center mt-3">
                        <button id="emp_bonus_process_submit_button" class="btn btn-primary waves-effect"  style="border-radius: 15px; width: 150px; height: 40px; letter-spacing: 1px;">Report</button>
                    </div>
                 </form>
              </div>
          </div>
    </div>
</div>



<div class="overlay"></div>
<!-- added thes for Multiple Selection dropdownlist  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css" integrity="sha512-mR/b5Y7FRsKqrYZou7uysnOdCIJib/7r5QeJMFvLNHNhtye3xJp1TdJVPLtetkukFn227nKpXD9OjUc09lx97Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js" integrity="sha512-FHZVRMUW9FsXobt+ONiix6Z0tIkxvQfxtCSirkKc5Sb4TKHmqq1dZa8DphF0XqKb3ldLu/wgMa8mT6uXiLlRlw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



@endsection
