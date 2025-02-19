@extends('layouts.admin-master')
@section('title') Employee Entry & Out @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Employee Attendence (In Time)</h4>
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
             <strong>Successfully!</strong> Added New project information.
          </div>
        @endif
        @if(Session::has('success_soft'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> delete project information.
          </div>
        @endif

        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> update project information.
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
        <form class="form-horizontal project-details-form" id="employeeEntryTime">

          <div class="card">
              <div class="card-body card_form">

                <div class="form-group row custom_form_group">
                    <label class="control-label col-md-3">Employee ID:</label>
                    <div class="col-md-7">
                      <input type="text" class="form-control typeahead" placeholder="Input Employee ID" name="emp_id" value="{{ old('emp_id') }}">
                      <span class="error d-none" id="error_massage"></span>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Select Date:</label>
                    <div class="col-sm-7">
                      <input type="text" class="form-control datepicker" id="datepicker" name="date" value="{{ Carbon\Carbon::now()->format('m/d/Y') }}" required>
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Entry Time:</label>
                    <div class="col-sm-7">
                        <input type="number" name="entry_time" value="{{ old('entry_time') }}" class="form-control" placeholder="Input Time (1 to 24 Hours)" required max="24" min="0">
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label"> Night Shift:</label>
                    <div class="col-sm-7">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="emp_io_shift" id="emp_io_shift" value="1">
                        <label class="form-check-label">If Night Shift Then Check This Box</label>
                      </div>
                    </div>
                </div>



              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" onclick="employeeEntryTime()" class="btn btn-primary waves-effect">SAVE</button>

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
                                <tbody id="employee_entry_out_time_list_view"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- script area -->
<script type="text/javascript">
/* form validation */
$(document).ready(function(){
  $('.datepicker').on('change', function(e) {
    employeeOutTimeEntryList();
    })
  employeeOutTimeEntryList();
  $("#employeeEntryTime").validate({
    /* form tag off  */
    submitHandler: function(form) {
         return false;
     },
    /* form tag off  */
    rules: {
      emp_id: {
        required : true,
      },
      date: {
        required : true,
      },
      entry_time: {
        required : true,
        number: true,
        max: 24,
        min: 0,
      },
    },

    messages: {
      emp_id: {
        required : "You Must Be Input This Field!",
      },
      date: {
        required : "You Must Be Select This Field!",
      },
      entry_time: {
        required : "Please Input This Field!",
        number : "You Must Be Input Number!",
        max : "You Must Be Input Maximum 24!",
      },
    },


  });
});

/* insert data in ajax */
function employeeEntryTime(){
  var emp_id = $('input[name="emp_id"]').val();
  var date = $('input[name="date"]').val();
  var entry_time = $('input[name="entry_time"]').val();

    if($('input[name="emp_io_shift"]').is(':checked')){
        var emp_io_shift = 1;
    } else {
        var emp_io_shift = 0;
    }


  if(emp_io_shift == 1){
    $("input[name='emp_io_shift']").attr({ "min" : 12 , "max" : 24});

    /* ajax request */
    if(emp_id != "" && entry_time != "" && date !=""){

      $.ajax({
        type:'POST',
        dataType: 'json',
        data:{ emp_id:emp_id, date:date, entry_time:entry_time, emp_io_shift:emp_io_shift },
        url:"{{ route('employee-entry-time-insert') }}",
        success:function(data){

          // error_massage
          if(data.error){
            $("span[id='error_massage']").text("Employee Not Found!");
            $("span[id='error_massage']").removeClass('d-none').addClass('d-block');
          }else{
            employeeOutTimeEntryList();
            var emp_id = $('input[name="emp_id"]').val("");
            var date = $('input[name="date"]').val("");
            var entry_time = $('input[name="entry_time"]').val("");
            var emp_io_shift = $('input[name="emp_io_shift"]').prop('checked', false);
            $("span[id='error_massage']").addClass('d-none').removeClass('d-block');
          }
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

  }else{
    $("input[name='emp_io_shift']").attr({ "max" : 12 , "min" : 0 });
    /* ajax request */
    if(emp_id != "" && entry_time != "" && date !=""){

      $.ajax({
        type:'POST',
        dataType: 'json',
        data:{ emp_id:emp_id, date:date, entry_time:entry_time, emp_io_shift:emp_io_shift },
        url:"{{ route('employee-entry-time-insert') }}",
        success:function(data){

          // error_massage
          if(data.error){
            $("span[id='error_massage']").text("Employee Not Found!");
            $("span[id='error_massage']").removeClass('d-none').addClass('d-block');
          }else{
            employeeOutTimeEntryList();
            var emp_id = $('input[name="emp_id"]').val("");
            // var date = $('input[name="date"]').val("");
            var entry_time = $('input[name="entry_time"]').val("");
            var emp_io_shift = $('input[name="emp_io_shift"]').prop('checked', false);
            $("span[id='error_massage']").addClass('d-none').removeClass('d-block');
          }
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

}

</script>



 <!-- employee out time -->

<script type="text/javascript">
    function employeeOutTimeEntryList(){
     
      // var proj_name = $('select[name="proj_name"]').val();
      var date = $('input[name="date"]').val();
      
      if(  date != ""){
        $.ajax({
          
          type:'POST',
          dataType: 'json',
          data:{  date:date },
          url:"{{ route('show-employee-entry.out.time') }}",
          success:function(response){
            
            if(response.entryList){
              var rows = "";
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
                                <input type="hidden" id="empId${value.emp_io_id}" value="${value.emp_auto_id}">
                                <input type="hidden" id="month${value.emp_io_id}" value="${value.emp_io_month}">
                                <input type="hidden" id="year${value.emp_io_id}" value="${value.emp_io_year}">
                                <input type="hidden" id="project${value.emp_io_id}" value="${value.project_id}">


                                <div class="col-md-12">
                                    <div class="custom__timeout__wrap">
                                      <input type="number" name="out_time" id="out_time${value.emp_io_id}" value="" class="form-control custom__out__time" placeholder="" required max="23" min="0">
                                      <a title="Add" id="${value.emp_io_id}" onclick="addOutEmployeeTime(this.id)"><i class="fas fa-thumbs-up fa-lg edit_icon"></i></a>
                                    </div>
                                </div>
                            </div>
                        </td>
                  </tr>
                  `
              });
              $('#employee_entry_out_time_list_view').html(rows);
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
    employeeOutTimeEntryList();

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
          employeeOutTimeEntryList();
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
