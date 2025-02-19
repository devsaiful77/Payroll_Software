@extends('layouts.admin-master')
@section('title') Set All Employee Iqama Cost @endsection
@section('content')

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Set All Employee Iqama Cost </h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">Set All Employee Iqama Cost</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-7">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert" style="margin-left: -20px">
             <strong>Successfully!</strong> Set All Employee Iqama Cost.
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
        <form class="form-horizontal" id="registration" method="post" action="{{ route('set.all-employee.iqama-cost.process') }}">
          @csrf
          <div class="card">
              <div class="card-header">
                  <div class="row">
                      <div class="col-md-8">
                          <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Set All Employee Iqama Cost </h3>
                      </div>
                      <div class="clearfix"></div>
                  </div>
              </div>
              <div class="card-body card_form">
                <div class="form-group row custom_form_group{{ $errors->has('Year') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">Year:<span class="req_star">*</span></label>
                    <div class="col-sm-4">
                      <!-- <input type="text" class="form-control" placeholder="Year" name="Year" id="Year"> -->
                      <select class="form-control" name="year">
                          @foreach(range(date('Y'), date('Y')) as $y)
                          <option value="{{$y}}" {{$y}} >{{$y}}</option>
                          @endforeach
                       </select>
                      @if ($errors->has('Year'))
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $errors->first('Year') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="col-sm-4">
                      <button type="submit" id="onSubmit" class="btn btn-primary waves-effect">PROCESS</button>
                    </div>
                </div>
              </div>
          </div>
        </form>
    </div>
</div>
@endsection
