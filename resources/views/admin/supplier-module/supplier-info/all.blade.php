@extends('layouts.admin-master')
@section('title') Supplier @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Supplier Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Supplier Information</li>
        </ol>
    </div>
</div>
<!-- Alert Start -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
        <div class="alert alert-success alertsuccess" role="alert">
            <strong>{{Session::get('success')}} </strong>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>{{Session::get('error')}} </strong>
        </div>
        @endif
    </div>
</div>


<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card"><br>
            <div class="card-body card_form" style="padding-top: 0;">

                <div class="row form-group custom_form_group{{ $errors->has('searchBy') ? ' has-error' : '' }}">
                    <label class="col-md-4 control-label d-block" style="text-align: left;">Select Type:<span
                            class="req_star">*</span></label>
                    <div class="col-md-8">
                        <select class="form-control" name="searchBy" id="searchBy" required>
                            <option value="1">Supplier Search By Name</option>
                            <option value="2">Supplier Search By Email</option>
                            <option value="3">Supplier Seach By Phone</option>

                        </select>
                        @if ($errors->has('searchBy'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('searchBy') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="form-group custom_form_group row{{ $errors->has('supp_info') ? ' has-error' : '' }}">
                    <label class="control-label col-md-4" style="text-align: left;">Supplier Info :<span
                            class="req_star">*</span></label>
                    <div class="col-md-8">
                        <input type="text" placeholder="Please Enter Supplier Name" class="form-control" id="supp_info"
                            name="supp_info" value="{{ old('supp_info') }}">
                        @if ($errors->has('supp_info'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('supp_info') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" onclick="suppliersInfo()" style="margin-top: 2px"
                    class="btn btn-primary waves-effect">SEARCH</button>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>

    <br>
    <!-- division list -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Supplier List</h3>
                        </div>
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
                                            <th>SN</th>
                                            <th>Name</th>
                                            <th>Company</th>
                                            <th>Address</th>
                                            <th>Number</th>
                                            <th>Email</th>
                                            <th>Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="suppliersInfo">

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
