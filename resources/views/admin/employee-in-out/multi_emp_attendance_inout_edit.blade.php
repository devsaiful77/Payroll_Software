@extends('layouts.admin-master')
@section('title') Employee Entry Time List @endsection
@section('content')

<style>
  input[type="number"], textarea {

        width: 80px;
        display: inline-block;
        border: 1px solid grey;
        border-radius: 4px;
        box-sizing: border-box;

}

</style>


<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Attendence (OUT) </h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Employee Entry Time List</li>
    </ol>
  </div>
</div>

<!-- Session Message Display Section !-->
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

<!-- Searching Employee !-->
<div class="row">
  <div class="col-md-12">
    <form class="form-horizontal" id="employeeEntryTimeList" action="" method="">
      <div class="card">
        <div class="card-body card_form">

          <div class="form-group row custom_form_group">
              <label class="col-sm-2 control-label">Project Name:</label>
              <div class="col-sm-3">
                <select class="form-control" name="proj_name">
                  <option value="">Select Project</option>
                  @foreach($project as $proj)
                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                  @endforeach
                </select>
              </div>
          


           
            <label class="col-sm-1 control-label">Date:</label>
            <div class="col-sm-2">
              <input type="date" name="date" value="<?= date("Y-m-d") ?>" class="form-control">
            </div>

            <div class="col-sm-1">
                <input name="night_shift" id="night_shift" type="checkbox" value="0">
                <label for="saldetlabel">Night Shift</label>
             </div>

            <div class="col-sm-3">
              <button type="submit" onclick="employeeEntryList()" class="btn btn-primary waves-effect">Search (Employee Out)</button>
            </div>
          </div>

        </div>

      </div>
    </form>
  </div>  
</div>

<!--Mutiple Employee Attendance Update !-->
<div class="row">
   

  <!-- Employee List -->
  <div class="col-lg-12">
    <div class="card">
      <form id="employee-entry-in-form" action="{{ route('employee.attendance.out.multiple.emp') }}" onsubmit="atten_out_submit_button.disabled = true;" method="post">
                @csrf
        <div class="card-header">
            <div class="row">
                  <div class="form-group row custom_form_group" >                             
                            <label class="col-sm-3 control-label">Attendance Out Time:</label>
                            <div class="col-sm-3">
                                  <input type="hidden" id="atten_in_time" value="0" required>
                                  <input type="number" class="form-control"  id="atten_out_time"  name="atten_out_time" placeholder="Time(24 Format)"
                                    onkeyup="calculateAllEmployeeWorkingTime()" step = 0.5 required>
                                    &nbsp &nbsp &nbsp &nbsp &nbsp       
                                    <label   id="lbl_all_emp_ot_hours" > 
                            </div>
                            <div class="col-sm-1">
                                 <button type="submit" id ="atten_out_submit_button" name="atten_out_submit_button" class="btn btn-primary waves-effect">Attendance Submit</button>
                            </div>
                            <div class="col-sm-2">
                             </div>
                            <div class="col-sm-3">
                              <button type="button" onclick="checkUnCheckAllEmployeeForAttendanceOut(1)" class="btn btn-primary waves-effect">Select All</button>
                              <button type="button" onclick="checkUnCheckAllEmployeeForAttendanceOut(0)" class="btn btn-primary waves-effect">UnSelect All</button>
                            </div>
                  </div> 
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
                          <th>S.N</th>
                            <th>Emp.ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Basic Salary</th>
                            <th>Basic Hours</th>
                            <th>Time(IN)</th>
                            <th>Emp.ID</th>
                            <th colspan="2">Select</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="employee_entry_time_list_view"></tbody>
                  </table>
                </div>
              </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

 
<!-- FORM VALIDATION -->



<script type="text/javascript">
  $(document).ready(function() {

    $("#employeeEntryTimeList").validate({
      /* form tag off  */
      submitHandler: function(form) {
        return false;
      },
      /* form tag off  */
      rules: {
        proj_name: {
          required: true,
        },
        date: {
          required: true,
        },
      },

      messages: {
        proj_name: {
          required: "You Must Be Select This Field!",
        },
        date: {
          required: "You Must Be Chose This Field!",
        },
      },


    });
  });
</script>

<script type="text/javascript">
  // Single Employe  Attendance TIme Calculation
  function calculateEmployeeWorkingTime(auto_id) {

    var outTime = parseFloat($('#out_time'.concat(auto_id)).val());
    var inTime = parseFloat($('#in_time'.concat(auto_id)).val());
    var timeDiff = outTime - inTime;

    if (timeDiff < 0) {
      timeDiff += 24;
    }

    if (outTime > 23 || outTime < 0) {

      $('#out_time'.concat(auto_id)).val('');
      $('#lbl_ot_hours'.concat(auto_id)).text('');

    } else
    {

      if (timeDiff >= 10) {
        ot = timeDiff - 10;
        var otText = 'Basic + OT : 10+' + '' + ot;


        $('#lbl_ot_hours'.concat(auto_id)).text(otText);
      } else {
        ot = timeDiff;
        var otText = '' + ot;
        $('#lbl_ot_hours'.concat(auto_id)).text(otText);
      }

    }

  }


  
  // any changes from keystrokes to arrow clicks and keyboard/mouse paste, not supported in IE <9.
  $('#atten_out_time').on('input', function() {
    calculateAllEmployeeWorkingTime(); 
  });

  function calculateAllEmployeeWorkingTime() {

   //style="background-color:red"
    var outTime =  parseFloat($('#atten_out_time').val());
    var inTime =   parseFloat($('#atten_in_time').val());
    var timeDiff = outTime - inTime;
    if(inTime == 0  || outTime == 0){
      alert('Please Select Employees and Input Out Time for Attendance Out');
      return;
    }



    if (timeDiff < 0) {
      timeDiff += 24;
    }
    if (outTime > 23 || outTime < 0) {
      $('#atten_out_time').val('');
      $('#lbl_all_emp_ot_hours').text('');

    } else
    {
      document.getElementById("atten_out_submit_button").disabled = false;
      if (timeDiff >= 10) {
        ot = timeDiff - 10; // outTime - (inTime + 10);
        var otText = 'Basic + OT : 10+' + '' + ot;
        $('#lbl_all_emp_ot_hours').text(otText);
      } else {

        ot = timeDiff;
        var otText = 'Total Hours :' + ot;
        $('#lbl_all_emp_ot_hours').text(otText);
      }

    }

  }

  
  function checkUnCheckAllEmployeeForAttendanceOut(operationType){

    let myTable = document.getElementById('employee_entry_time_list_view');
    for (let row of myTable.rows) {
        allCell = row.cells;          
        var chkboxId = "entry_out_checkbox-" + allCell[9].innerText;
        document.getElementById(chkboxId).checked = operationType; 
    }

  }
</script>

<script type="text/javascript">
  // get Employee List for Out

  function employeeEntryList() {

    var proj_name = $('select[name="proj_name"]').val();
    var date = $('input[name="date"]').val();

    var isNightShift = document.getElementById("night_shift").checked;
        isNightShift = isNightShift == true ? 1: 0;

    $('#employee_entry_time_list_view').html('');
    $('#atten_in_time').val(0);
    if (proj_name != "" && date != "") {
      $.ajax({

        type: 'POST',
        dataType: 'json',
        data: {
          proj_name: proj_name,
          date: date,
          isNightShift: isNightShift
        },
        url: "{{ route('show-employee-out.time') }}",
        success: function(response) {
 
          if (response.entryList) {

            var attendance_in_time = 0;
            var rows = "";
            var counter = 1;
            $.each(response.entryList, function(key, value) {
              attendance_in_time = value.emp_io_entry_time
              rows += `
                    <tr>
                        <td>${counter++}</td>
                        <td>${value.employee_id}</td>
                        <td>${value.employee_name}</td>
                        <td>${value.emp_io_date}-${value.emp_io_month}-${value.emp_io_year}</td>
                        <td>${value.emp_io_shift}, ${value.emp_io_shift == "1" ? 'Nigth Shift' : 'Day Shift'} </td>                        
                        <td>${value.basic_amount}</td>
                        <td>${value.basic_hours}</td>
                        <td>${value.emp_io_entry_time}</td>
                        <td>${value.employee_id}</td>
                        <td style="color:#fff">${value.emp_io_id}</td>
                        <td> <input type="checkbox" name="entry_out_checkbox-${value.emp_io_id}" id="entry_out_checkbox-${value.emp_io_id}" value="0"></td>

                          <td style="with:100px">
                              <div class="row  align-items-center">

                                  <input type="hidden" id="emp_io_id${value.emp_io_id}" name="emp_io_id_list[]" value="${value.emp_io_id}">
                                  <input type="hidden" id="shift${value.emp_io_id}" value="${value.emp_io_shift}">
                                  <input type="hidden" id="in_time${value.emp_io_id}" value="${value.emp_io_entry_time}">

                                  <div class="col-md-5">
                                  <input type="hidden" name="out_time${value.emp_io_id}" id="out_time${value.emp_io_id}" onkeyup="calculateEmployeeWorkingTime(${value.emp_io_id})" value="" class="form-control" placeholder="" step = 0.5 max="23" min="0">
                                  </div>
                                 <div class="col-md-5">
                                    <label   id="lbl_ot_hours${value.emp_io_id}" >
                                    </label>
                                  </div>


                              </div>
                          </td>
                        
                    </tr>
                    `
            });
            $('#employee_entry_time_list_view').html(rows);
            $('#atten_in_time').val(attendance_in_time);
           // <a title="Add" id="${value.emp_io_id}" onclick="addOutEmployeeTime(this.id)"><i class="fas fa-thumbs-up fa-lg edit_icon"></i></a>

          } else {

            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
            if ($.isEmptyObject(response.error)) {
              Toast.fire({
                type: 'success',
                title: response.success
              })
            } else {
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


 // Single Empoyee Attendance Out AJAX
  function addOutEmployeeTime(id) {

    // Catch Value
    var id = id;
    var out_time = parseFloat($('#out_time' + id).val());
    var empId = $('#empId' + id).val();


    if (out_time > 0 && out_time < 24) {
      $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
          id: id,
          out_time: out_time,
        },
        url: "{{ route('employee-entry-time-update') }}",
        success: function(data) {
          employeeEntryList();
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
 
  // Get Employee List by Project Name
</script>




@endsection
