@extends('layouts.admin-master')
@section('title') Upload @endsection
@section('content')



<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Salary Sheet And Mobile Bill Upload</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Salary Sheet</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> {{Session::get('success')}}
        </div>
        @endif
        @if(Session::has('delete'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>Successfully!</strong> {{Session::get('delete')}}
        </div>
        @endif


        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> please try again.
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

 {{-- Menu Section --}}
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="row"  style="padding: 0;">
                <div class="card-body card_form" >
                    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

                        <div class="btn-group" role="group" aria-label="Third group">
                            <button type="submit" onclick="openSalarySlipUploadSection()" class="btn btn-primary waves-effect">Salary Sheet</button>
                        </div>

                        <div class="btn-group mr-2" role="group" aria-label="Second group">
                            <button type="button"  onclick="openEmployeeMobileBillUploadSection()" class="btn btn-primary waves-effect">Mobile Bill</button>
                        </div>


                        <div class="btn-group mr-2" role="group" aria-label="Second group">
                             <button type="button"  onclick="openEmployeeMobileBillSearchSection()" class="btn btn-primary waves-effect">Search Mobile Paper</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Salary Sheet Upload Section -->
<div class="row d-none" id="showEmployeeSalarySheetManageSection">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                     </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">

                <form method="post" action="{{ route('salary-sheet.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                    <div class="col-md-2"> </div>
                        <div class="col-md-4">

                            <label class="control-label d-block">Number No Of Emp.:<span class="req_star">*</span></label>
                            <div class="">
                                <input type="text" class="form-control" name="no_of_emp" value="0" required>
                            </div>

                            <label class="control-label d-block" style="text-align: left;">Salary Month:</label>
                            <div>
                                <select class="form-control" name="month" required>
                                    @foreach($month as $item)
                                    <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="control-label d-block" style="text-align: left;">Salary Year:</label>
                            <div>
                                <select class="form-control" name="year">
                                    @foreach(range(date('Y'), date('Y')-5) as $y)
                                    <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="control-label d-block">Salary Date:<span class="req_star">*</span></label>
                            <div class="">
                                <input type="date" name="salary_date" value="<?= date("Y-m-d") ?>" class="form-control">
                            </div>

                        </div>
                        <div class="col-md-4">
                            {{-- Project List --}}

                            <label class="control-label d-block" style="text-align: left;">Project Name:</label>
                            <div>
                                <select class="form-control" name="proj_od" id="proj_id" required>
                                    <option value="0">ALL</option>
                                    @foreach($projectlist as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="control-label d-block" style="text-align: left;">Sponser Name:</label>
                            <div>
                                <select class="form-control" name="spons_id" id="SponsId" required>
                                    <option value="0">ALL</option>
                                    @foreach($sponserList as $spons)
                                    <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="control-label d-block">Total Amount:<span class="req_star">*</span></label>
                            <div class="">
                                <input type="number" class="form-control" name="total_salary" value="0" required>
                            </div>

                            <label class="control-label d-block">Remarks</label>
                            <div class="">
                                <input type="text" class="form-control" name="remarks" >
                            </div>


                            <label class="control-label d-block">Upload File:<span class="req_star">*</span></label>
                            <div class="">
                                <input type="file" class="form-control" name="file_name">
                            </div>



                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">

                                <button type="submit" class="btn btn-dark">Submit</button>
                            </div>
                        </div>
                    </div>


                </form>

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">

                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                    <th> S.N </th>
                                        <th> Number Of Emp.</th>
                                        <th> Project </th>
                                        <th> Sponsor </th>
                                        <th> Month </th>
                                        <th> Year </th>
                                        <th> Date </th>
                                        <th> Total Salary </th>
                                        <th> Remarks </th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allSheet as $item)
                                    <tr>
                                        <td>{{ $loop->iteration}}</td>
                                        <td>{{ $item->no_of_emp}}</td>
                                        <td>{{ $item->proj_od }}</td>
                                        <td>{{ $item->spons_id}}</td>
                                        <td>{{ $item->month}}</td>
                                        <td>{{ $item->year}}</td>
                                        <td>{{ $item->salary_date}}</td>
                                        <td>{{ $item->total_salary}}</td>
                                        <td>{{ $item->remarks}}</td>
                                        <!-- <td><img height="50" src="{{asset($item->file_path.$item->file_name) }}" alt=""></td> -->
                                        <td>
                                            <!-- <a href="#" title="view"><i class="fas fa-eye fa-lg view_icon"></i></a>
                                            <a href="#" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a> -->
                                            <a href="{{ route('salary-sheet.delete',$item->ss_auto_id) }}" title="delete" id="delete"><i class="fa fa-trash fa-lg delete_icon"></i></a>
                                        </td>
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

<!-- Mobile Bill Upload Section -->
<div class="modal fade" id="emp_mobile_bill_store_form_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Employee Mobile Bill Documents Upload<span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <form id="employeeMobileBillStoreForm" action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="text" id="operation_type" name="operation_type" value = "1">

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Year: <span class="req_star">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" name="bill_year"  id="bill_year" required>
                                     @foreach(range(date('Y'), date('Y')-1) as $y)
                                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('bill_year'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('bill_year') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Month:<span class="req_star">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" name="bill_month"  id="bill_month" required>
                                    <option value="">Select Month</option>
                                    @foreach($month as $item)
                                        <option value="{{ $item->month_id }}">{{ $item->month_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('bill_month'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('bill_month') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Project <span class="req_star">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" name="bill_project_id"  id="bill_project_id" required>
                                    <option value="">Select Any Project</option>
                                    @foreach($projectlist as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('bill_project_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('bill_project_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="col-sm-3 control-label">Upload File:<span class="req_star">*</span></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <span class="btn btn-default btn-file btnu_browse">
                                            Browse… <input type="file" name="bill_paper" id="imgInp3" required>
                                            @if ($errors->has('bill_paper'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('bill_paper') }}</strong>
                                                </span>
                                            @endif
                                        </span>
                                    </span>
                                    <input type="text" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <img id='img-upload3' class="upload_image" />
                            </div>
                        </div>

                        <div class="text-center">
                            <button id="employee-mobile-bill-submit-button"  name="report_btn" class="btn btn-success">SAVE</button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>


<!-- Mobile Bill Search Section -->
<div class="modal fade" id="emp_mobile_bill_searching_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Searching Mobile Bill Paper<span class="text-danger" id="errorData"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <form id="searching_mobile_bill_paper_form" action="#" method="POST" >
                        @csrf
                        <input type="hidden" class="text" id="operation_type" name="operation_type" value = "2">

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Year: <span class="req_star">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" name="year"  id="year" required>
                                    @foreach(range(date('Y'), date('Y')-2) as $y)
                                        <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Month:<span class="req_star">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" name="month"  id="month"  >
                                    @foreach($month as $item)
                                        <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="form-group row custom_form_group">
                            <label class="control-label col-md-3">Project <span class="req_star">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" name="project_id"  id="project_id">
                                    <option value="">Select Any Project</option>
                                    @foreach($projectlist as $proj)
                                    <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="text-center">
                            <button id="mobile_bill_search_bttn"    class="btn btn-success">Search</button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>


<!-- Already Submitted Inovice Record -->
{{-- <div class="row d-block" id="searching_result_table_section" >
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="dt-vertical-scroll" class="table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Project Name</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Uploaded By</th>
                                        <th>Uploaded At</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="mobile_bill_searching_result_table_body">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="row d-none" id="searching_result_table_section"  >
    <div class="col-12">
        <div class="table-responsive">
            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Project Name</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Uploaded By</th>
                        <th>Uploaded At</th>
                        {{-- <th>Remarks</th> --}}
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="mobile_bill_searching_result_table_body">
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
