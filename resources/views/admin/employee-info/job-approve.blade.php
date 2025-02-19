@extends('layouts.admin-master')
@section('title') Unapproval Employee list @endsection
@section('internal-css')
<style media="screen">
    .approve_button {
        background: #2B4049;
        color: #fff;
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 5px;
    }

    .approve_button:hover {
        color: #fff;
    }
</style>
@endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Unapproval Employee List</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Unapproval Employee</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{ Session::get('success') }}</strong>
        </div>
        @endif
        @if(Session::has('approve'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> Approve in Employee Job.
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

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>ID</th>
                                        <th>Emp. Name</th>
                                        <th>Sponsor</th>
                                        <th>Addr.</th>
                                        <th>Type</th>
                                        <th>Basic</th>
                                        <th>B.Hours</th>
                                        <th>Hourly</th>                                        
                                        <th>Food</th>
                                        <th>Sau.T</th>
                                        <th>Photo</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $item->employee_id }}</td>
                                        <td>{{Str::words($item->employee_name,3)}}</td>
                                        <td>{{$item->sponser->spons_name}}</td>
                                        @if($item->country_id == NULL)
                                        <td>Not-Set</td>
                                        @else
                                        <td>{{ Str::limit($item->country->country_name,5) }}</td>
                                        <!--country-->
                                        @endif
                                        <td>{{ $item->emp_type_id == NULL ? 'Not Set' :
                                            Str::limit($item->employeeType->name,4) }}</td>
                                        <!--employeeType-->
                                        <td>{{ $item->basic_amount }}</td>
                                        <td>{{ $item->basic_hours }}</td>
                                        <td>{{ $item->hourly_rent }}</td>
                                        <td>{{ $item->food_allowance }}</td>
                                        <td>{{ $item->saudi_tax }}</td>                                        
                                        <td>
                                            <img src="{{ asset($item->profile_photo) }}" alt="Not Found" width="80">
                                        </td>
                                        <td>
                                            @can('job-approve')
                                            <a href="{{ route('employee-job-approve.success',$item->emp_auto_id) }}"
                                                title="Approve" id="approved"><i class="fas fa-thumbs-up fa-lg edit_icon"></i></a>
                                            @endcan
                                            <a href="#" title="Edit" data-toggle="modal"
                                                data-target="#demoModalEdit{{ $item->emp_auto_id }}"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>

                                            <a href="#" title="View" class="approve_button" data-toggle="modal"
                                                data-target="#demoModalView-{{ $item->emp_auto_id }}">View</a>
                                            
                                        </td>


                                        <!-- Employee Info View With Modal Start -->
                                        <div class="modal fade" id="demoModalView-{{ $item->emp_auto_id }}"
                                            value="{{ $item->emp_auto_id }}" tabindex="-1" role="dialog" aria-
                                            labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="card-title text-danger">{{$item->employee_name}}
                                                            Details Informations</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close" id="closeModal">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            
                                                                            <div class="col-sm-12">
                                                                            Please Download 
                                                                                <a target="_blank" class="download_button" href="{{URL::to($item->pasfort_photo) }}">Passport</a> &nbsp; &nbsp; 
                                                                                <a target="_blank" class="download_button" href="{{URL::to($item->akama_photo) }}">Iqama</a>  &nbsp; &nbsp; 
                                                                                <a target="_blank" class="download_button" href="{{URL::to($item->employee_appoint_latter) }}">Offer Letter</a> &nbsp; &nbsp; 
                                                                            </div>
                                                                             
                                                                        </div>

                                                                        <ul class="list-group">
                                                                            <li  class="list-group-item list-group-item-primary">
                                                                            

                                                                            </li>

                                                                            <li
                                                                                class="list-group-item list-group-item-primary">
                                                                                Employee Id No : <span
                                                                                    class="badge badge-primary badge-pill">{{$item->employee_id}}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item list-group-item-success">
                                                                                Name : <span
                                                                                    class="badge badge-primary badge-pill">{{$item->employee_name}}</span>
                                                                            </li>
                                                                            <li class="list-group-item">Akama No :
                                                                                {{$item->akama_no}}</li>
                                                                            <li class="list-group-item">Sponsor Name :
                                                                                <b> {{$item->sponser->spons_name}} </b>
                                                                            </li>
                                                                            <li class="list-group-item">Agency Name :
                                                                                <b>{{$item->agc_title}} </b>
                                                                            </li>
                                                                            <li class="list-group-item">Job Status :
                                                                                <b>Approval Pending</b>
                                                                            </li>
                                                                            <!-- <li class="list-group-item">Project Name : <b>{{$item->project->proj_name}} </b></li> -->
                                                                            <li class="list-group-item">Employee
                                                                                Designation :
                                                                                <b>{{$item->category->catg_name}} </b>
                                                                            </li>
                                                                            <li class="list-group-item">Akama Expire
                                                                                Date : {{
                                                                                Carbon\Carbon::parse($item->akama_expire_date)->format('D,
                                                                                d F Y') }}</li>
                                                                            <li class="list-group-item">Passport No :
                                                                                <b> {{$item->passfort_no}} </b>
                                                                            </li>
                                                                            <li class="list-group-item">Passport Expire
                                                                                Date : {{
                                                                                Carbon\Carbon::parse($item->passfort_expire_date)->format('D,
                                                                                d F Y') }}</li>
                                                                            <li class="list-group-item">Employee Type :
                                                                                <b>{{ $item->employeeType->name }} </b>
                                                                            </li>

                                                                            <li class="list-group-item">Date of Birth :
                                                                                {{
                                                                                Carbon\Carbon::parse($item->date_of_birth)->format('D,
                                                                                d F Y') }}</li>
                                                                            <li class="list-group-item">Moblie Number :
                                                                                @if($item->mobile_no == NULL) No Number
                                                                                ... @else {{ $item->mobile_no }} @endif
                                                                            </li>
                                                                            <li class="list-group-item">Phone Number :
                                                                                {{ $item->phone_no }}</li>

                                                                            <li class="list-group-item">Email Address :
                                                                                {{ $item->email }}</li>
                                                                            <li class="list-group-item">Gender :
                                                                                @if($item->gender == 'm') Male @else
                                                                                Female @endif</li>
                                                                            <li class="list-group-item">Maritus Status :
                                                                                @if($item->maritus_status == 1)
                                                                                Unmarried
                                                                                @else
                                                                                Married
                                                                                @endif
                                                                            </li>
                                                                            <li class="list-group-item">Religion :
                                                                                @if($item->religion == 1)
                                                                                Muslim
                                                                                @elseif($item->religion == 2)
                                                                                Christianity
                                                                                @else
                                                                                Hinduism
                                                                                @endif
                                                                            </li>
                                                                            <li class="list-group-item">Joining Date :
                                                                                {{
                                                                                Carbon\Carbon::parse($item->joining_date)->format('D,
                                                                                d F Y') }}</li>
                                                                            <li class="list-group-item">Confirmation
                                                                                Date : {{
                                                                                Carbon\Carbon::parse($item->confirmation_date)->format('D,
                                                                                d F Y') }}</li>
                                                                            <li class="list-group-item">Appointment Date
                                                                                : {{
                                                                                Carbon\Carbon::parse($item->appointment_date)->format('D,
                                                                                d F Y') }}</li>
                                                                            <li class="list-group-item">Job Location :
                                                                                {{ $item->job_location }}</li>
                                                                            <li class="list-group-item">Employee Insert
                                                                                Date : {{
                                                                                Carbon\Carbon::parse($item->emp_insert_date)->format('D,
                                                                                d F Y') }}</li>
                                                                            <li class="list-group-item">Present Address
                                                                                : {{$item->present_address}}</li>
                                                                            <li class="list-group-item">Parmanent
                                                                                Address :
                                                                                {{$item->country->country_name}}, {{
                                                                                $item->division->division_name}}, {{
                                                                                $item->district->district_name}},
                                                                                <span>post code : {{ $item->post_code }}
                                                                            </li>
                                                                            <li class="list-group-item">Parmanent
                                                                                Address Details : {{ $item->details }}
                                                                            </li>
                                                                            {{-- <li class="list-group-item">Phone
                                                                                Number : {{ $item->phone_no }}</li> --}}
                                                                        </ul>

                                                                        <div class="card-header bg-primary text-white">
                                                                            {{$item->employee_name}} Salary Details
                                                                            Informations
                                                                        </div>
                                                                        <ul class="list-group list-group-flush">
                                                                            <li class="list-group-item">Basic Amount :
                                                                                <b> {{ $item->basic_amount}} </b>
                                                                            </li>
                                                                            <li class="list-group-item">Basic Hour : {{
                                                                                $item->basic_hours}}</li>
                                                                            <li class="list-group-item">Hourly Rent :
                                                                                <b> {{ $item->hourly_rent}} </b>
                                                                            </li>
                                                                            <li class="list-group-item">Mobile Allowance
                                                                                : {{ $item->mobile_allowance}}</li>
                                                                            <li class="list-group-item">Food Allowance :
                                                                                <b> {{ $item->food_allowance}} </b>
                                                                            </li>
                                                                            <li class="list-group-item">Saudi Tax : {{
                                                                                $item->saudi_tax}}</li>
                                                                        </ul>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Employee Info View With Modal End -->


                                        <!-- Edit Form Modal Example Start -->
                                        <div class="modal fade" id="demoModalEdit{{ $item->emp_auto_id }}"
                                            value="{{ $item->emp_auto_id }}" tabindex="-1" role="dialog" aria-
                                            labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="card-title">Employee Salary Details Update</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close" id="closeModal">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <form class="form-horizontal" id="employee-info-form"
                                                                method="post"
                                                                action="{{ route('employee.salary.details.update.at-approval.time') }}">
                                                                @csrf
                                                                <div class="card">
                                                                    <div class="EmpIDName text-center">
                                                                        <h5 class="card-title">Employee Id : <span
                                                                                class="req_star">{{ $item->employee_id
                                                                                }}</span></h5> <br>
                                                                        <h5 class="card-title">Employee Name : <span
                                                                                class="req_star">{{ $item->employee_name
                                                                                }}</span></h5>
                                                                    </div>
                                                                    <div class="card-body card_form">
                                                                        <input type="hidden" name="empId"
                                                                            value="{{$item->employee_id }}">


                                                                        <div class="row custom_form_group">
                                                                            <label
                                                                                class="col-sm-4 control-label">Employee
                                                                                Type:<span
                                                                                    class="req_star">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <div
                                                                                    class="form{{ $errors->has('emp_type_id') ? ' has-error' : '' }}">
                                                                                    <select class="form-control"
                                                                                        name="emp_type_id">

                                                                                        @foreach($empTypes as $emp)
                                                                                        <option value="{{ $emp->id }}"
                                                                                            {{ $item->emp_type_id ==
                                                                                            $emp->id ? 'selected' : ''
                                                                                            }}> {{ $emp->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div id="hourlyEmployeeFeild" class="d-block">
                                                                            <div class="row custom_form_group">
                                                                                <div class="col-sm-3"></div>
                                                                                <div class="col-sm-6"
                                                                                    style="margin-left: 10px;">
                                                                                    <div class="form-check">
                                                                                        @if($item->hourly_employee == 1)
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox" value="1"
                                                                                            checked
                                                                                            name="hourly_employee"
                                                                                            id="flexCheckDefault">
                                                                                        @else
                                                                                        <input class="form-check-input"
                                                                                            type="checkbox" value="1"
                                                                                            name="hourly_employee"
                                                                                            id="flexCheckDefault">
                                                                                        @endif
                                                                                        <label class="form-check-label"
                                                                                            for="flexCheckDefault"
                                                                                            style="font-size:13px; font-weight:400">
                                                                                        </label>
                                                                                        <label
                                                                                            class="control-label">Hourly
                                                                                            Employee:<span
                                                                                                class="req_star">*</span></label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-3"></div>
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('basic_amount') ? ' has-error' : '' }}">
                                                                            <label class="col-sm-4 control-label"
                                                                                for="basic_amount">Basic Salary:<span
                                                                                    class="req_star">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text"
                                                                                    placeholder="Input Amount"
                                                                                    class="form-control"
                                                                                    id="basic_amount"
                                                                                    name="basic_amount"
                                                                                    value="{{ $item->basic_amount }}"
                                                                                    min="0"">
                                                                                        @if ($errors->has('basic_amount'))
                                                                                            <span class="
                                                                                    invalid-feedback" role="alert">
                                                                                <strong>{{
                                                                                    $errors->first('basic_amount')
                                                                                    }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('basic_hours') ? ' has-error' : '' }}">
                                                                            <label class="col-sm-4 control-label"
                                                                                for="basic_hours">Basic Hours:<span
                                                                                    class="req_star">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text"
                                                                                    placeholder="Input Basic Hours"
                                                                                    class="form-control"
                                                                                    id="basic_hours" name="basic_hours"
                                                                                    value="{{$item->basic_hours}}"
                                                                                    min="1">
                                                                                @if ($errors->has('basic_hours'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('basic_hours')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('mobile_allowance') ? ' has-error' : '' }}">
                                                                            <label class="col-sm-4 control-label">Moblie
                                                                                Allowance:</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text"
                                                                                    placeholder="Input Amount"
                                                                                    class="form-control"
                                                                                    id="mobile_allowance"
                                                                                    name="mobile_allowance"
                                                                                    value="{{$item->mobile_allowance}}">
                                                                                @if ($errors->has('mobile_allowance'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('mobile_allowance')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('medical_allowance') ? ' has-error' : '' }}">
                                                                            <label
                                                                                class="col-sm-4 control-label">Medical
                                                                                Allowance:</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text"
                                                                                    placeholder="Input Amount"
                                                                                    class="form-control"
                                                                                    id="medical_allowance"
                                                                                    name="medical_allowance"
                                                                                    value="{{$item->medical_allowance}}">
                                                                                @if ($errors->has('medical_allowance'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('medical_allowance')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('saudi_tax') ? ' has-error' : '' }}">
                                                                            <label class="col-sm-4 control-label">Saudi
                                                                                Tax:</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text"
                                                                                    placeholder="Input Amount"
                                                                                    class="form-control" id="saudi_tax"
                                                                                    name="saudi_tax"
                                                                                    value="{{$item->saudi_tax}}">
                                                                                @if ($errors->has('saudi_tax'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('saudi_tax')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('food_allowance') ? ' has-error' : '' }}">
                                                                            <label class="col-sm-4 control-label">Food
                                                                                Allowance:<span
                                                                                    class="req_star">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control"
                                                                                    name="food_allowance"
                                                                                    value="{{$item->food_allowance}}"
                                                                                    min="0">
                                                                                @if ($errors->has('food_allowance'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('food_allowance')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>

                                                                        <div
                                                                            class="form-group row custom_form_group{{ $errors->has('hourly_rate') ? ' has-error' : '' }}">
                                                                            <label class="col-sm-4 control-label"
                                                                                for="basic_hours">Hourly Rate:<span
                                                                                    class="req_star">*</span></label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text"
                                                                                    placeholder="Input Hours Rate"
                                                                                    class="form-control"
                                                                                    id="hourly_rate" name="hourly_rate"
                                                                                    value="{{$item->hourly_rent}}"
                                                                                    min="0">
                                                                                @if ($errors->has('hourly_rate'))
                                                                                <span class="invalid-feedback"
                                                                                    role="alert">
                                                                                    <strong>{{
                                                                                        $errors->first('hourly_rate')
                                                                                        }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>


                                                                        <div class="text-center">
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Update</button>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- Edit Form Modal Example End-->

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

      // Clear Modal View Data
    // $('#activity_details_ui_modal').on('hidden.bs.modal', function (e) {
    //     $(this)
    //     .find("input,textarea,select").val('').end()
    //     .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
    // })
    
</script>

@endsection

@section('script')
<script>
    /*Employee Designation feild change */
    // $('select[name="emp_type_id"]').on('change', function () {
    //     var designation = $(this).val();
    //     alert(designation)

    //     if (designation == 1) {
    //         $("#hourlyEmployeeFeild").removeClass('d-none').addClass('d-block');
    //     } else {
    //         $("#hourlyEmployeeFeild").addClass('d-none').removeClass('d-block');
    //     }
    // });

   

</script>

@endsection
