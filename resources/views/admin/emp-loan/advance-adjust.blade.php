@extends('layouts.admin-master')
@section('title')Advance Setting @endsection
@section('content')

<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Monthly Advance Deduction Setting</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Advance Setting</li>
    </ol>
  </div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
      <strong>{{Session::get('success')}}</strong>
    </div>
    @endif
    @if(Session::has('error'))
    <div class="alert alert-warning alerterror" role="alert">
      <strong>{{Session::get('error')}}</strong>
    </div>
    @endif
  </div>
  <div class="col-md-2"></div>
</div>


<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">

    <div class="card">

      <div class="card-body card_form">
        <!-- SEARCH Employee -->
        <div class="form-group row custom_form_group" style="margin-top:10px">
          <div class="col-md-2"> </div>
          <label class="col-sm-3 control-label">Employee ID:<span class="req_star">*</span></label>
          <div class="col-sm-3">
            <input type="text" class="form-control" name="employee_id" id="employee_id" autofocus required placeholder="Employee ID Type Here ">
          </div>
          <div class="col-sm-1">
            <button onclick="findEmployeeForAdvanceSetting()" class="btn btn-primary btn-sm emp-sarch">SEARCH</button>
          </div>
        </div>

        <div id="advance_setting_form_section" class="d-none card_footer_button text-center" style="padding-top: 20px;">

          <form id="projectInchargeForm" action="{{ route('update.advance-installAmount') }}" method="post">
            @csrf
            <div class="row" >
              <div class="form-group row custom_form_group" style="margin:0px;padding:0px; color:red">
                <label class="col-sm-3 control-label" id ="emp_id">   </label>
                <label class="col-sm-3 control-label" id ="emp_name">   </label>
                <label class="col-sm-3 control-label" id ="emp_salary">   </label>
              </div>

              <div class="form-group row row custom_form_group" style="margin:0px;padding:0px;color:red">
                <label class="col-sm-3 control-label" id ="iqama_no">   </label>
                <label class="col-sm-3 control-label" id ="passport_no">   </label>
              </div>


              <div class="col-md-6">
                <div class="form-wrap">
                  <input type="hidden" name="id" id="adv_pay_id" value="">
                  <input type="hidden" name="operation_type" id="operation_type" value="1">
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Total Iqama:</label>
                    <div class="col-sm-7">
                      <input type="text" id="totalIqama" class="form-control" value="" disabled>
                    </div>
                  </div>
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Installes Amount:</label>
                    <div class="col-sm-7">
                      <input type="text" id="installIqama" class="form-control" value="" disabled>
                    </div>
                  </div>
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Total Paid:</label>
                    <div class="col-sm-7">
                      <input type="text" id="payIqama" class="form-control" value="" disabled>
                    </div>
                  </div>
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Next Pay:</label>
                    <div class="col-sm-7">
                      <input type="number" id="nextPayIqama" name="nextPayIqama" class="form-control" value="">
                    </div>
                  </div>
                </div>
              </div>



              <div class="col-md-6">
                <div class="form-wrap">
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Total Others:</label>
                    <div class="col-sm-7">
                      <input type="text" id="totalOthers" class="form-control" value="" disabled>
                    </div>
                  </div>
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Installes Amount:</label>
                    <div class="col-sm-7">
                      <input type="text" id="installOthers" class="form-control" value="" disabled>
                    </div>
                  </div>
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Total Paid:</label>
                    <div class="col-sm-7">
                      <input type="text" id="payOthers" class="form-control" value="" disabled>
                    </div>
                  </div>
                  <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Next Pay:</label>
                    <div class="col-sm-7">
                      <input type="number" id="nextPayOthers" name="nextPayOthers" class="form-control" value="">
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary btn-sm waves-effect" style="display:inline-block; margin: 0 auto;">SAVE</button>
            </div>
          </form>
        </div>

      </div>
    </div>
    <!-- </form> -->
  </div>
  <div class="col-md-1"></div>
</div>


<script type="text/javascript">


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


    // Enter Key Press Event Fire
    $('#employee_id').keydown(function(e) {
        if (e.keyCode == 13) {
          findEmployeeForAdvanceSetting();
        }
    })


  function findEmployeeForAdvanceSetting() {
    var emp_id = $("#employee_id").val();
    $("#advance_setting_form_section").addClass('d-none').removeClass('d-block');

    if(emp_id == null || emp_id == ''){
        showSweetAlertMessage('error', 'Invalid Employee ID');
        return;
    }

    $.ajax({
      type: 'POST',
      dataType: 'json',
      data: {
        emp_id: emp_id
      },
      url: "{{ route('findEmployeeForLoan') }}",
      success: function(response) {
        if (response.status != 200) {

            showSweetAlertMessage('error', 'employee not found');
            $("#advance_setting_form_section").addClass('d-none').removeClass('d-block');
            return;
        }

           $("#employee_id").val("");
           $("#emp_id").html("Employee ID: "+response.employee.employee_id);
           $("#emp_name").html('Name: '+response.employee.employee_name);
           $("#emp_salary").html('Salary Type: '+ ( response.employee.hourly_employee == null ? 'Basic' : 'Hourly'));
           $("#iqama_no").html('Iqama No: '+response.employee.akama_no);
           $("#passport_no").html('Passport No: '+response.employee.passfort_no);

           $('input[id="adv_pay_id"]').val(response.empAutoId);
           $('input[id="totalIqama"]').val(response.totalIqama);
           $('input[id="totalOthers"]').val(response.totalOthers);
           $('input[id="payIqama"]').val(response.totalPaidIqama);
           $('input[id="payOthers"]').val(response.totalPaidOthers);
            $('input[id="installIqama"]').val(response.salaryDetatils.iqama_adv_inst_amount);
            $('input[id="installOthers"]').val(response.salaryDetatils.other_adv_inst_amount);
            $('input[id="nextPayIqama"]').val(response.salaryDetatils.iqama_adv_inst_amount);
            $('input[id="nextPayOthers"]').val(response.salaryDetatils.other_adv_inst_amount);
            $("#error_through").text('');
           $("#advance_setting_form_section").removeClass('d-none').addClass('d-block');

      }
    });
  }


</script>



@endsection
