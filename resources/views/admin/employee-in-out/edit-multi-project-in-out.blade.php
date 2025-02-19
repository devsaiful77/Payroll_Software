@extends('layouts.admin-master')
@section('title') Attn. Update @endsection
@section('content')

<style>
  body {
    font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
  }
</style>
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Multi Project Work Record Update</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Employee Attendence</li>
    </ol>
  </div>
</div>

<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>Successfully!</strong> Update information.
    </div>
    @endif
    @if(Session::has('delete_multi_proj'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>Successfully!</strong> {{ Session::get('delete_multi_proj')}}
    </div>
    @endif
    @if(Session::has('success_soft'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>Successfully!</strong> delete information.
    </div>
    @endif

    @if(Session::has('success_update'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong>Successfully!</strong> update information.
    </div>
    @endif

    @if(Session::has('data_not_found'))
    <div class="alert alert-warning alerterror" role="alert">
      <strong>Opps!</strong> invalid Employee Id.
    </div>
    @endif
  </div>
  <div class="col-md-2"></div>
</div>


<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <form class="form-horizontal project-details-form" method="POST" action="">
      @csrf
      <div class="card">

        <div class="card-body card_form">

          <div class="form-group row custom_form_group">

            <label class="control-label col-md-7">Employee ID: {{$multyProjInfoAnEmp->employee->employee_id}}, Emp. Name : {{$multyProjInfoAnEmp->employee->employee_name}}</label>
            <div class="col-md-2">
              <input type="text" class="form-control typeahead" name="emp_id" readonly value="{{ $multyProjInfoAnEmp->emp_id }}">
              <input type="text" name="empwh_auto_id" readonly value="{{ $multyProjInfoAnEmp->empwh_auto_id}}">
              <span class="error d-none" id="error_massage"></span>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Project Name:</label>
            <div class="col-md-7">
              <select class="form-control" name="proj_name">
                <option value="">Select Project</option>
                @foreach($project as $proj)
                <option value="{{ $proj->proj_id }}" {{$proj->proj_id==$multyProjInfoAnEmp->project_id ? 'selected' : ''}}>{{ $proj->proj_name }}</option>
                @endforeach
              </select>
              <span class="error d-none" id="error_massage"></span>
            </div>
          </div>

          <!-- <div class="form-group row custom_form_group">
            <label class="control-label col-md-3">Month:</label>
            <div class="col-md-7">
              <select class="form-control" name="month">
                @foreach($month as $data)
                <option value="{{ $data->month_id }}" {{ $data->month_id == $multyProjInfoAnEmp->month ? 'selected':'' }}>{{ $data->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div> -->

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Total Work Hour:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control " name="totalHourTime" value="{{ $multyProjInfoAnEmp->total_hour}}" required>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Total Overtime:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control " name="totalOverTime" value="{{ $multyProjInfoAnEmp->total_overtime}}" required>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Total Work Day:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control " name="total_day" value="{{ $multyProjInfoAnEmp->total_day}}" min="1" required>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Work From:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control datepicker" id="datepicker" name="startDate" value="{{ $multyProjInfoAnEmp->start_date==null? Carbon\Carbon::now()->format('m/d/Y') : date('m/d/Y', strtotime($multyProjInfoAnEmp->start_date))}}" required>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label">Work End:</label>
            <div class="col-sm-7">
              <input type="text" class="form-control datepicker" id="workDatepicker" name="endDate" value="{{ $multyProjInfoAnEmp->end_date==null? Carbon\Carbon::now()->format('m/d/Y') : date('m/d/Y', strtotime($multyProjInfoAnEmp->end_date))}}" required>
            </div>
          </div>

        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="button" class="btn btn-primary waves-effect" onclick="employeeMultipleWorkRecrdTimeUpdate()">UPDATE</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-1"></div>
</div>


<!-- script area -->
<script type="text/javascript">
  /* form validation */
  $(document).ready(function() {

    $("#employeeMultipleEntryTime").validate({
      /* form tag off  */
      submitHandler: function(form) {
        return false;
      },
      /* form tag off  */
      rules: {
        emp_id: {
          required: true,
        },
        proj_name: {
          required: true,
        },
        startDate: {
          required: true,
        },
        endDate: {
          required: true,
        },
        totalHourTime: {
          required: true,
          number: true,
        },
        totalOverTime: {
          number: true,
        },

      },

      messages: {
        emp_id: {
          required: "You Must Be Input This Field!",
        },
        proj_name: {
          required: "You Must Be Input This Field!",
        },
        startDate: {
          required: "You Must Be Select This Field!",
        },
        endDate: {
          required: "You Must Be Select This Field!",
        },
        totalHourTime: {
          required: "Please Input This Field!",
          number: "You Must Be Input Number!",
        },
        totalOverTime: {
          number: "You Must Be Input Number!",
        },
      },


    });
  });




  function changeStatus(id) {
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this imaginary file!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })

      .then((willDelete) => {
        if (willDelete) {
          $.ajax({
            type: 'GET',
            url: "{{  url('admin/delete/employee/multiple/in/out') }}/" + id,
            dataType: 'json',
            success: function(response) {
              window.location.reload();
            }
          });

        }
      });

  }



  function employeeMultipleWorkRecrdTimeUpdate() {
      // alert('update');
      var emp_id = $('input[name="emp_id"]').val();
      var proj_name = $('select[name="proj_name"]').val();
      var startDate = $('input[name="startDate"]').val();
      var endDate = $('input[name="endDate"]').val();
      var totalHourTime = $('input[name="totalHourTime"]').val();
      var totalOverTime = $('input[name="totalOverTime"]').val();
      var empwh_auto_id = $('input[name="empwh_auto_id"]').val();
      var total_day = $('input[name="total_day"]').val();
      
     
      if (emp_id != "" && proj_name != "" && startDate != "" && endDate != "" && totalHourTime != "") {
        // alert(proj_name)
        $.ajax({
          type: 'POST',
          dataType: 'json',
          data: {
            emp_id: emp_id,
            proj_name: proj_name,
            startDate: startDate,
            endDate: endDate,
            totalHourTime: totalHourTime,
            totalOverTime: totalOverTime,
            empwh_auto_id: empwh_auto_id,
            total_day: total_day
          },
          url: "{{ route('employee-multiple-time-update') }}",
          success: function(data) {

            // error_massage
            if (data.error) {
              $("span[id='error_massage']").text("Employee Not Found!");
              $("span[id='error_massage']").removeClass('d-none').addClass('d-block');
            } else {
              // alert('ok');
              var emp_id = $('input[name="emp_id"]').val("");
              $("span[id='error_massage']").addClass('d-none').removeClass('d-block');
            }

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
        });
      }

    }



    
</script>

@endsection