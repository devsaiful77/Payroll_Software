@extends('layouts.admin-master')
@section('title') Employee Entry Time List @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Attendence (Out Time) </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Employee Entry Time List</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <form class="form-horizontal" id="employeeEntryTimeList" action="" method="">
          <div class="card">
              <div class="card-body card_form">

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Project Name:</label>
                    <div class="col-sm-7">
                        <select class="form-control" name="proj_name">
                          <option value="">Select Project</option>
                          @foreach($project as $proj)
                          <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                          @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Date:</label>
                    <div class="col-sm-7">

                        <input type="date" name="date" value="<?= date("Y-m-d") ?>" class="form-control">

                        <!-- <input type="date" name="appointment_date" id="txtDate" value="<?= date("Y-m-d") ?>" class="form-control"> -->
                    </div>
                </div>

              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" onclick="employeeEntryList()" class="btn btn-primary waves-effect">PROCESS</button>
              </div>
          </div>
        </form>
    </div>
    <div class="col-md-1"></div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle mr-2"></i>Entry Time List</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <span id="data_not_found" class="d-none">Data Not Found!</span>
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Shift</th>
                                        <th>Entry Time</th>
                                        <th>Out Time</th>
                                    </tr>
                                </thead>
                                <tbody id="employee_entry_time_list_view"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FORM VALIDATION -->
<script type="text/javascript">
  /* form validation */
  $(document).ready(function(){

    $("#employeeEntryTimeList").validate({
      /* form tag off  */
      submitHandler: function(form) {
          return false;
      },
      /* form tag off  */
      rules: {
        proj_name: {
          required : true,
        },
        date: {
          required : true,
        },
      },

      messages: {
        proj_name: {
          required : "You Must Be Select This Field!",
        },
        date: {
          required : "You Must Be Chose This Field!",
        },
      },


    });
  });
</script>

<script type="text/javascript">
    function employeeEntryList(){

      
      var proj_name = $('select[name="proj_name"]').val();
      var date = $('input[name="date"]').val();

      
      if(proj_name != "" && date != ""){
        $.ajax({
          
          type:'POST',
          dataType: 'json',
          data:{ proj_name:proj_name, date:date },
          url:"{{ route('show-employee-out.time') }}",
          success:function(response){
            
            if(response.entryList){
              // $('select[name="proj_name"]').val("");
              // $('input[name="date"]').val("");


              // $('span[id="data_not_found"]').removeClass('d-block').addClass('d-none');
              var rows = "";
             // alert(response.entryList);
              $.each(response.entryList,function(key, value){
                  rows += `
                  <tr>
                      <td>${value.employee_id}</td>
                      <td>${value.employee_name}</td>
                      <td>${value.emp_io_date}-${value.emp_io_month}-${value.emp_io_year}</td>
                      <td>${value.emp_io_shift == 1 ? 'Nigth Shift' : 'Day Shift'} </td>
                      <td>${value.emp_io_entry_time}</td>

                        <td id="">
                            <div class="row align-items-center">
                                <div class="col-md-"></div>
                                <input type="hidden" id="empId${value.emp_io_id}" value="${value.emp_auto_id}">
                                <input type="hidden" id="month${value.emp_io_id}" value="${value.emp_io_month}">
                                <input type="hidden" id="year${value.emp_io_id}" value="${value.emp_io_year}">
                                <input type="hidden" id="project${value.emp_io_id}" value="${value.project_id}">


                                <div class="col-md-4">
                                    <input type="number" name="out_time" id="out_time${value.emp_io_id}" value="" class="form-control" placeholder="" required max="23" min="0">
                                </div>
                                <div class="col-md-3">
                                    <a title="Add" id="${value.emp_io_id}" onclick="addOutEmployeeTime(this.id)"><i class="fas fa-thumbs-up fa-lg edit_icon"></i></a>
                                </div>
                            </div>
                        </td>
                  </tr>
                  `
              });
              $('#employee_entry_time_list_view').html(rows);
            }else {
             // alert('er');
              //  start message
              const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
              })
              if($.isEmptyObject(response.error)){
                  Toast.fire({
                    type: 'success',
                    title: response.success
                  })
              }else{
                Toast.fire({
                  type: 'error',
                  title: response.error
                })
              }
              //  end message
            }





          }
        });
      }
    }
    employeeEntryList();

    function addOutEmployeeTime(id){
    // Catch Value
    var id = id;
    var out_time = $('#out_time'+id).val();
    var empId = $('#empId'+id).val();
    var month = $('#month'+id).val();
    var year = $('#year'+id).val();
    var project = $('#project'+id).val();



    if(out_time != ""){
      $.ajax({
        type:'POST',
        dataType: 'json',
        data:{ id:id, out_time:out_time, empId:empId, month:month, year:year, project:project },
        url:"{{ route('employee-entry-time-update') }}",
        success:function(data){
          employeeEntryList();
          //  start message
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          })
          if($.isEmptyObject(data.error)){
              Toast.fire({
                type: 'success',
                title: data.success
              })
          }else{
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
