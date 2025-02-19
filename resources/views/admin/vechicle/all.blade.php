@extends('layouts.admin-master')
@section('title')New Vechicle @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">New Vechicle Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Vechicle</li>
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
        <form class="form-horizontal" id="vechicleForm-validation" action="{{ route('insert-new.vechicle') }}"
            method="post" enctype="multipart/form-data">
            @csrf
            <div class="card">

                <div class="card-body card_form" style="padding-top: 0;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Company Name:<span
                                        class="req_star">*</span></label>
                                <div  class="col-sm-6">
                                    <select class="form-control" name="company_id" required>
                                        <option value="1">Asloob Internation Contracting Comany</option>
                                        <option value="2">Asloob Bedda Contracting Comany </option>
                                        <option value="3">Bedaa General Contracting Comany</option>
                                        <option value="4">Other Employee</option>
                                    </select>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Vehicle Name:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_name" value="{{ old('veh_name') }}"
                                        placeholder="Input Vechicle Name" required>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-3 control-label">Vechicle Type:<span
                                        class="req_star">*</span></label>
                                <div  class="col-sm-6">
                                    <select class="form-control" name="veh_type_id" required>
                                        <option value="">Select Vechicle Type</option>
                                        <option value="1">Jeep</option>
                                        <option value="2">Pickup</option>
                                        <option value="3">Bus</option>
                                        <option value="4">Truck</option>
                                    </select>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Plate Number:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_plate_number"
                                        value="{{ old('veh_plate_number') }}" placeholder="Input Vechicle Plate Number" required>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Model Number:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_model_number"
                                        value="{{ old('veh_model_number') }}" placeholder="Input Vechicle Model Number">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Brand Name:</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_brand_name"
                                        value="{{ old('veh_brand_name') }}" placeholder="Input Vechicle Brand Name">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Licence Number:<span
                                    class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_licence_no"
                                        value="{{ old('veh_licence_no') }}" placeholder="Input Vechicle Licence Number">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Insurrance Date:</label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_insurrance_date"
                                        value="{{ date('Y-m-d') }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3"> Price </label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="veh_price" value="0"
                                        placeholder="Input Vechicle Price">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Meter Reading  </label>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="veh_present_metar"
                                        value="0" placeholder="Input Vechicle Present Meter" required>
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Color </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="veh_color" value="{{ old('veh_color') }}"
                                        placeholder="Input Vechicle Color">
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-3">Owner Type:<span class="req_star">*</span></label>
                                <div  class="col-sm-6">
                                    <select class="form-control" name="veh_owner_type" required>
                                        <option value="">Select Vehicle Owner Type</option>
                                        <option value="1">Asloob Internation Contracting Comany</option>
                                        <option value="21">Rental Car</option>
                                    </select>
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Project Name:</label>
                                <div  class="col-sm-6">
                                    <select class="form-control" name="veh_proj_id">
                                        <option value="">Select Project Name</option>
                                        @foreach ($projects as $item)
                                        <option value="{{ $item->proj_id }}">{{ $item->proj_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3"></div>
                            </div>

                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Purchase </label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_purchase_date"
                                        value="{{ date('Y-m-d') }}" placeholder="Input Purchase Date">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div
                                class="form-group row custom_form_group{{ $errors->has('veh_ins_expire_date') ? ' has-error' : '' }}">
                                <label class="control-label col-md-2">Insurance Exp.  </label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_ins_expire_date"
                                        value="{{ date('Y-m-d') }}" value="{{old('veh_ins_expire_date')}}">
                                    @if ($errors->has('veh_ins_expire_date'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('veh_ins_expire_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Insurance Renewal  </label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_ins_renew_date"
                                        value="{{ date('Y-m-d') }}" >
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Reg. Exp:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_reg_expire_date"
                                        value="{{ date('Y-m-d') }}"  >
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Reg. Renewal
                                    Date:<span class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="veh_reg_renew_date"
                                        value="{{ date('Y-m-d') }}"  >
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="control-label col-md-2">Remarks:<span
                                        class="req_star">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="remarks" value="{{ old('remarks') }}"
                                        placeholder="Remarks Here">
                                </div>
                                <div class="col-md-3"></div>
                            </div>
                            <br>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-2 control-label">Insurance Cert.</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="veh_ins_certificate" id="imgInp4">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <img id='img-upload4' class="upload_image" />
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-2 control-label">Reg. Cert.</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="veh_reg_certificate" id="imgInp2">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <img id='img-upload2' class="upload_image" />
                                </div>
                            </div>
                            <div class="form-group row custom_form_group">
                                <label class="col-sm-2 control-label">Veh. Photo:</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file btnu_browse">
                                                Browse… <input type="file" name="veh_photo" id="imgInp3">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <img id='img-upload3' class="upload_image" />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer card_footer_button text-center">
                            <button type="submit" class="btn btn-primary waves-effect">SAVE</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>

<!-- Vehicle list -->
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
                                        <th>Company Name</th>
                                        <th>Project Name</th>
                                        <th>Name,Brand</th>
                                        <th>Plat No</th>
                                        <th>Reg. No</th>
                                        <th>Insu. Date</th>
                                        <th>Driver</th>
                                        <th>Reg. File</th>
                                        <th>Status</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($all as $item)
                                    <tr>
                                        <td>
                                            @if ($item->company_id == 1)
                                                Asloob Int. Co.
                                            @elseif ($item->company_id == 2)
                                                Asloob Bedda Co.
                                            @elseif ($item->company_id == 3)
                                                Bedaa General Co
                                            @elseif ($item->company_id == 4)
                                                Other Employee
                                            @endif
                                        </td>
                                        <td> {{ $item->proj_name == null ? "Not Assigned" : $item->proj_name }} </td>
                                        <td> {{ $item->veh_name }}, {{ $item->veh_brand_name }}  </td>
                                        <td> {{ $item->veh_plate_number }} </td>
                                        <td> {{ $item->veh_licence_no }} </td>
                                        <td> {{ $item->veh_ins_expire_date }} </td>
                                        <td>
                                            @if($item->driver_id == NULL)
                                            No Driver Assigned
                                            @else
                                            {{ $item->employee->employee_id ?? '' }} , {{ $item->employee->employee_name
                                            ?? '' }}
                                            @endif
                                        </td>
                                        <td>{{$item->veh_reg_certificate != null? 'Found' : 'Not Found' }}</td>
                                        <td>
                                            @if ($item->status == 1)
                                                <span class="badge badge-pill badge-success">Active</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">In Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('edit-vechicle',$item->veh_id) }}" title="edit"><i
                                                    class="fa fa-pencil-square fa-lg edit_icon"></i></a>

                                            <a href="{{ route('delete-vechicle',$item->veh_id) }}" title="delete"
                                                id="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
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
<!-- script area -->
<script type="text/javascript">
    /* form validation */
    $(document).ready(function () {
        $("#vechicleForm-validation").validate({

            rules: {
                veh_name: {
                    required: true,
                },
                veh_plate_number: {
                    required: true,
                },
                veh_type: {
                    required: true,
                },
                veh_brand_name:{
                    required:true,
                }
                // veh_licence_no: {
                //     required: true,
                // },
                /*
                veh_purchase_date: {
                    required: true,
                },
                veh_ins_expire_date: {
                    required: true,
                },
                veh_reg_expire_date: {
                    required: true,
                },
                veh_ins_certificate: {
                    required: true,
                },
                veh_reg_certificate: {
                    required: true,
                },
                veh_present_metar: {
                    required: true,
                    number: true,
                    maxlength: 9,
                },
                veh_price: {
                    required: true,
                    number: true,
                    maxlength: 15,
                },  */
            },

            messages: {
                veh_name: {
                    required: "You Must Be Input This Field!",
                },
                veh_plate_number:{
                    required: "You Must Be Input This Field!",
                },
                veh_type:{
                    required: "You Must Be Input This Field!",
                },
                veh_brand_name:{
                    required: "You Must Be Input This Field!",
                }
                // veh_licence_no:{
                //     required: "You Must Be Input This Field!",
                // }

                /*
                veh_color: {
                    required: "You Must Be Input This Field!",
                },
                veh_purchase_date: {
                    required: "You Must Be Select This Field!",
                },
                veh_ins_expire_date: {
                    required: "You Must Be Select This Field!",
                },
                veh_reg_expire_date: {
                    required: "You Must Be Select This Field!",
                },
                veh_ins_certificate: {
                    required: "You Must Be Provide Your Insurance Certificate Here!",
                },
                veh_reg_certificate: {
                    required: "You Must Be Provide Your Registration Certificate Here!",
                },
                veh_price: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 15!",
                },
                veh_present_metar: {
                    required: "Please Input This Field!",
                    number: "You Must Be Input Number!",
                    max: "You Must Be Input Maximum Length 9!",
                },
                */
            },
        });
    });

</script>


@endsection
