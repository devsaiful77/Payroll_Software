@extends('layouts.admin-master')
@section('title') Driver @endsection
@section('content')
@section('internal-css')
<style>
    .edit_modla_button {
        padding: 2px 5px;
        border-radius: 3px;
    }
</style>

@endsection

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Driver Informations</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Driver Information Add</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong> {{ Session::get('success')}} </strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong> {{ Session::get('error') }} </strong>
        </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" id="driverForm-validation" action="{{ route('insert-driver-info') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <div class="row">

                    </div>
                </div>
                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="form-group row custom_form_group" id="searchEmployeeId">
                                <label class="control-label col-md-3">Employee ID:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control typeahead"
                                        placeholder="Input Driver Employee ID" name="dri_empId" id="emp_id_search"
                                        onkeyup="empSearch()" onfocus="showResult()" onblur="hideResult()">

                                    <div id="showEmpId"></div>
                                    <span id="error_show" class="d-none" style="color: red"></span>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            {{-- Search Employee IQama No --}}
                            <div id="searchIqamaNo">
                                <div class="form-group row custom_form_group ">
                                    <label class="control-label col-md-3">IQama No:<span
                                        class="req_star">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" id="iqamaNoSearch" class="form-control typeahead"
                                            placeholder="Input Driver IQama No" name="dri_iqamaNo">
                                        <span id="error_show" class="d-none" style="color: red"></span>
                                    </div>
                                    <div class="col-md-3"></div>
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Name:<span class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="dri_name"
                                        value="{{ old('dri_name') }}" placeholder="Please Enter Driver Name">
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Present Address:</label>
                                <div class="col-md-6">
                                    <textarea name="present_address" class="form-control" value="{{old('dri_address')}}"
                                        placeholder="Please Enter Driver Address Here">{{old('present_address')}}</textarea>
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                            <div class="form-group row custom_form_group{{ $errors->has('dri_license_type_id') ? ' has-error' : '' }}">
                                <label class="control-label col-md-3" for="account_id">License Type: </label>
                                <div class="col-sm-6">
                                    <select id="dri_license_type_id" class="form-select" name="dri_license_type_id">
                                        <option value="">Select Driver License Type</option>
                                        <option value="1">Heavy Vehicle</option>
                                        <option value="2">Light Vehicle</option>
                                    </select>
                                    @if ($errors->has('dri_license_type_id'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('dri_license_type_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                            <div class="form-group mt-3 row custom_form_group">
                                <label class="col-sm-3 control-label">Iqama Certificate:</label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="dri_iqama_certificate" id="imgInp4">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <img id='img-upload4' class="upload_image" />
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Driving License:<span
                                        class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="dri_license_certificate" id="imgInp2">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <img id='img-upload2' class="upload_image" />
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Insurance Certificate:<span
                                    class="req_star">*</span></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="dri_ins_certificate" id="imgInp3">
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
                                <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">

                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="alltableinfo" class="table table-bordered custom_table mb-0">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Name</th>
                                        <th>Employee ID</th>
                                        <th>License Type</th>
                                        <th>Iqama No</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td> {{ $item->dri_name }} </td>
                                        <td> {{ $item->dri_emp_id }} </td>
                                        <td> {{ $item->dri_license_type_id == 1 ? "Heavy Vehicle" : "Light Vehicle" }} </td>
                                        <td> {{ $item->dri_iqama_no }} </td>
                                        <td> {{ $item->dri_address }} </td>
                                        <td> {{ $item->status == 0 ? "Inactive" : "Active"  }} </td>
                                        <td>

                                            <button type="button" data-toggle="modal" data-target="#driverInformationUpdateModal{{ $item->dri_auto_id }}" class="btn btn-primary edit_modla_button"><i class="fas fa-pencil-alt"></i></button>

                                            <a href="{{ route('driver-info-deactive',$item->dri_auto_id) }}" title="delete"
                                                id="delete" title="delete data"><i
                                                    class="fa fa-trash fa-lg delete_icon"></i></a>
                                        </td>



                                        <!-- Edit Form Modal Example Start -->
                                        <div class="modal fade" id="driverInformationUpdateModal{{ $item->dri_auto_id }}" value="{{ $item->dri_auto_id }}" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update <span class="text-info">{{ $item->dri_name }}</span> Driver Information Details</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <!-- Driver Basic Information Update Start -->
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <form class="form-horizontal" id="company_driver_details_information_update_form" method="post" action="#">
                                                                @csrf
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <div class="row">
                                                                                <div class="col-md-8">
                                                                                    <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Driver Information</h3>
                                                                                </div>
                                                                                <div class="clearfix"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body card_form" style="padding-top: 0;">
                                                                            <input type="hidden" name="dri_auto_id" value="{{ $item->dri_auto_id }}">

                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group row custom_form_group" id="searchEmployeeId">
                                                                                        <label class="control-label col-md-4">Employee ID:<span class="req_star">*</span></label>
                                                                                        <div class="col-md-6">
                                                                                            <input type="text" class="form-control typeahead"
                                                                                                placeholder="Input Driver Employee ID" name="dri_empId"
                                                                                                value="{{ $item->dri_emp_id }}" id="emp_id_search" onkeyup="empSearch()"
                                                                                                onfocus="showResult()" onblur="hideResult()">

                                                                                            <div id="showEmpId"></div>
                                                                                            <span id="error_show" class="d-none" style="color: red"></span>
                                                                                        </div>
                                                                                        <div class="col-md-2"></div>
                                                                                    </div>
                                                                                    {{-- Search Employee IQama No --}}
                                                                                    <div id="searchIqamaNo">
                                                                                        <div class="form-group row custom_form_group ">
                                                                                            <label class="control-label col-md-4">IQama No:</label>
                                                                                            <div class="col-md-6">
                                                                                                <input type="text" id="iqamaNoSearch" class="form-control typeahead"
                                                                                                    placeholder="Input Driver IQama No" name="dri_iqamaNo"
                                                                                                    value="{{ $item->dri_iqama_no }}">
                                                                                                <span id="error_show" class="d-none" style="color: red"></span>
                                                                                            </div>
                                                                                            <div class="col-md-2"></div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row custom_form_group">
                                                                                        <label class="control-label col-md-4">Name:<span
                                                                                                class="req_star">*</span></label>
                                                                                        <div class="col-md-6">
                                                                                            <input type="text" class="form-control" name="dri_name"
                                                                                                value="{{ $item->dri_name }}" placeholder="Please Enter Driver Name">
                                                                                        </div>
                                                                                        <div class="col-md-2"></div>
                                                                                    </div>
                                                                                    <div class="form-group row custom_form_group">
                                                                                        <label class="control-label col-md-4">Present Address:</label>
                                                                                        <div class="col-md-6">
                                                                                            <textarea name="present_address" class="form-control" value="{{old('dri_address')}}"
                                                                                                placeholder="Please Enter Driver Address Here">{{ $item->dri_address }}</textarea>
                                                                                        </div>
                                                                                        <div class="col-md-2"></div>
                                                                                    </div>
                                                                                    <div class="form-group row custom_form_group">
                                                                                        <label class="control-label col-md-4" for="account_id">License Type: </label>
                                                                                        <div class="col-sm-6">
                                                                                            <select id="dri_license_type_id" class="form-select" name="dri_license_type_id">
                                                                                                <option value="">Select Driver License Type</option>
                                                                                                <option value="1" {{ $item->dri_license_type_id == 1 ? 'selected' : "" }}>Heavy Vehicle</option>
                                                                                                <option value="2"  {{ $item->dri_license_type_id == 2 ? 'selected' : "" }}>Light Vehicle</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-md-2"></div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="card-footer card_footer_button text-center">
                                                                                    <button id="company_driver_information_update_button" class="btn btn-primary waves-effect">UPDATE</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <!-- Driver Basic Information Update End -->



                                                        <!-- Driver uploaded documents Update Start -->
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <form class="form-horizontal" id="company_driver_photo_update_form" method="POST" enctype="multipart/form-data" action="#">
                                                                    @csrf
                                                                    <div class="card">
                                                                        <div class="card-header">
                                                                            <div class="row">
                                                                                <div class="col-md-8">
                                                                                    <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Update Driver Related File </h3>
                                                                                </div>
                                                                                <div class="clearfix"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body row card_form">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <input type="hidden" name="dri_auto_id" value="{{ $item->dri_auto_id }}">

                                                                                    <input type="hidden" name="old_dri_iqama_certificate" value="{{ $item->dri_iqama_certificate }}">
                                                                                    <input type="hidden" name="old_dri_license_certificate" value="{{ $item->dri_license_certificate }}">
                                                                                    <input type="hidden" name="old_dri_ins_certificate" value="{{ $item->dri_ins_certificate }}">

                                                                                    <!-- Iqama Certificate -->
                                                                                    <div class="form-group row custom_form_group">
                                                                                        <label class="col-sm-4 control-label">Iqama Certificate:<span
                                                                                                class="req_star">*</span></label>
                                                                                        <div class="col-sm-5">
                                                                                            <div class="input-group">
                                                                                                <span class="input-group-btn">
                                                                                                    <span class="btn btn-default btn-file btnu_browse">
                                                                                                        Browse… <input type="file" name="dri_iqama_certificate" id="imgInp5">
                                                                                                    </span>
                                                                                                </span>
                                                                                                <input type="text" class="form-control" readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <img id='img-upload5' class="upload_image" />
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-- Driving License -->
                                                                                    <div class="form-group row custom_form_group">
                                                                                        <label class="col-sm-4 control-label">Driving License:<span
                                                                                                class="req_star">*</span></label>
                                                                                        <div class="col-sm-5">
                                                                                            <div class="input-group">
                                                                                                <span class="input-group-btn">
                                                                                                    <span class="btn btn-default btn-file btnu_browse">
                                                                                                        Browse… <input type="file" name="dri_license_certificate" id="imgInp7">
                                                                                                    </span>
                                                                                                </span>
                                                                                                <input type="text" class="form-control" readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <img id='img-upload7' class="upload_image" />
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-- Insurance Certificate -->
                                                                                    <div class="form-group row custom_form_group">
                                                                                        <label class="col-sm-4 control-label">Insurance Certificate:</label>
                                                                                        <div class="col-sm-5">
                                                                                            <div class="input-group">
                                                                                                <span class="input-group-btn">
                                                                                                    <span class="btn btn-default btn-file btnu_browse">
                                                                                                        Browse… <input type="file" name="dri_ins_certificate" id="imgInp8">
                                                                                                    </span>
                                                                                                </span>
                                                                                                <input type="text" class="form-control" readonly>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <img id='img-upload8' class="upload_image" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="card-footer card_footer_button text-center">
                                                                                        <button id="company_driver_photo_update_button" class="btn btn-primary waves-effect">UPDATE</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <!-- Driver uploaded documents Update End -->

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
@endsection
