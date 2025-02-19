@extends('layouts.admin-master')
@section('title') Add Banner Information @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Banner Information</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('project-info') }}">Banner Information</a></li>
            <li class="active"> Banner</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
             <strong>Successfully!</strong> Added New Banner.
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert" style="margin-left: -20px">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
    <div class="col-md-2"></div>
</div>

<div class="row">
    <div class="col-lg-12">
        <form class="form-horizontal" id="registration" method="post" action="{{ route('insert-banner-info') }}" enctype="multipart/form-data">
          @csrf
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-8">
                          <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> New Banner Information</h3>
                      </div>
                      <div class="col-md-4 text-right">
                          <a href="{{ route('banner-info') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-th"></i> Banner List</a>
                      </div>
                      <div class="clearfix"></div>
                  </div>
              </div>
              <div class="card-body card_form">

                <div class="form-group row custom_form_group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Company name:<span class="req_star">*</span></label>
                    <div class="col-sm-7">

                      <input type="text" placeholder="banner title" class="form-control" id="ban_title" name="ban_title" value="{{ $comp->comp_name_en }}" disabled>

                    </div>
                </div>

                <input type="hidden" name="company_id" value="{{ $comp->comp_id }}">


                <div class="form-group row custom_form_group{{ $errors->has('ban_title') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Title:<span class="req_star">*</span></label>
                    <div class="col-sm-7">
                      <input type="text" placeholder="Banner Title" class="form-control" id="ban_title" name="ban_title" value="{{old('ban_title')}}" required>
                      @if ($errors->has('ban_title'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('ban_title') }}</strong>
                          </span>
                      @endif
                    </div>
                </div>

                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Subtitle:</label>
                    <div class="col-sm-7">
                      <input type="text" placeholder="Banner Subtitle" class="form-control" id="ban_subtitle" name="ban_subtitle" value="{{old('ban_subtitle')}}">
                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Caption:</label>
                    <div class="col-sm-7">
                      <input type="text" placeholder="Banner Caption" class="form-control" id="ban_caption" name="ban_caption" value="{{old('ban_caption')}}">

                    </div>
                </div>
                <div class="form-group row custom_form_group">
                    <label class="col-sm-3 control-label">Description:</label>
                    <div class="col-sm-7">
                      <textarea name="ban_description" class="form-control" placeholder="Banner Description">{{ old('ban_description') }}</textarea>

                    </div>
                </div>


                <div class="form-group row custom_form_group{{ $errors->has('ban_image') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Banner Image:</label>
                    <div class="col-sm-4">
                      <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-default btn-file btnu_browse">
                                  Browse… <input type="file" name="ban_image" id="imgInp3" accept="image/x-png,image/gif,image/jpeg">
                              </span>
                          </span>
                          <input type="text" class="form-control" readonly>
                      </div>
                      @if ($errors->has('ban_image'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('ban_image') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="col-md-3">
                      <img id='img-upload3' width="200"/>
                    </div>
                </div>


              </div>
              <div class="card-footer card_footer_button text-center">
                  <button type="submit" onclick="formValidation();" class="btn btn-primary waves-effect">SAVE</button>
              </div>
          </div>
        </form>
    </div>
</div>
@endsection
