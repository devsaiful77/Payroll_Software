@extends('layouts.admin-master')
@section('title') Employee Salary Processing System @endsection
@section('content')
<div class="row bread_part">
  <div class="col-sm-12 bread_col">
    <h4 class="pull-left page-title bread_title">Employee Monthly Work Records</h4>
    <ol class="breadcrumb pull-right">
      <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="active">Work Records</li>
    </ol>
  </div>
</div>
<!-- alert message -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    @if(Session::has('success'))
    <div class="alert alert-success alertsuccess" role="alert">
    <strong> {{Session::get('success')}}</strong>
    </div>
    @endif
    @if(Session::has('error'))
    <div class="alert alert-success alertsuccess" role="alert">
      <strong> {{Session::get('error')}}</strong>
    </div>
    @endif

  </div>
</div>

<!-- Procet, Sponser base All Employee Monthly work Record -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="registration" target="_blank" action="{{ route('project-wise-employe.month-work.record.report') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="card-title card_top_title salary-generat-heading"> Employee Work Records </h3>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form" style="padding-top: 0;">
          <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">Working Project:</label>
                <div class="col-sm-5">
                <select class="form-control" name="proj_id" required>
                  {{-- <option value="0">All</option> --}}
                  @foreach($projects as $proj)
                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                  @endforeach
                </select>
              </div>
          </div>

          {{-- Sponser List --}}
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label" >Select Sponser:</label>
            <div class="col-sm-5">
              <select class="form-control" name="SponsId" required>
                <option value="0">All</option>
                @foreach($sponser as $spons)
                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Select Month:</label>
            <div class="col-sm-5">
               <select class="form-control" name="month" required>
                @foreach($month as $item)
                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Year:</label>
            <div class="col-sm-5">
                        <select class="form-control" name="year" required>
                                        @foreach(range(date('Y'), date('Y')-1) as $y)
                                          <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                        </select>
            </div>
          </div>
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label" >Data Source:</label>
            <div class="col-sm-5">
                <select class="form-control" name="data_source" required>
                  <option value="1">Monthly Work Records</option>
                  <option value="2">Multi-Project Records</option>
                  <option value="3">Attendance IN/OUT</option>
                </select>
            </div>
          </div>
        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>

<!-- Attendance In Out Record Summary -->
{{-- <div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="registration" target="_blank" action="{{ route('project-wise-employe.month-work.record.report') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="card-title card_top_title salary-generat-heading"> Employee Attendance IN/OUT Monthly Summary </h3>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form" style="padding-top: 0;">
          <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">Employees of Project:</label>
                <div class="col-sm-5">
                <select class="form-control" name="proj_id" required>
                  <option value="0">All</option>
                  @foreach($projects as $proj)
                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                  @endforeach
                </select>
              </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Select Month:</label>
            <div class="col-sm-5">
               <select class="form-control" name="month" required>
                @foreach($month as $item)
                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Year:</label>
            <div class="col-sm-5">
                        <select class="form-control" name="year" required>
                                        @foreach(range(date('Y'), date('Y')-1) as $y)
                                          <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                        </select>
            </div>
          </div>
        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div> --}}


<!-- Active Employee But Not In Work History by Procet, Sponser  -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="registration" target="_blank" action="{{ route('work-history-employee-notin-work-record') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="card-title card_top_title salary-generat-heading"> Employee Those are not in Work Records </h3>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form" style="padding-top: 0;">


          <!-- <div class="form-group custom_form_group">
            <label class="control-label d-block" style="text-align: left;">Select Project:</label>
            <div>
              <select class="form-control" name="proj_id" required>
                <option value="0">All</option>
                @foreach($projects as $proj)
                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                @endforeach
              </select>
            </div>
          </div>



          <div class="form-group custom_form_group">
            <label class="control-label d-block" style="text-align: left;">Select Sponser:</label>
            <div>
              <select class="form-control" name="SponsId" required>
                <option value="0">All</option>
                @foreach($sponser as $spons)
                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                @endforeach
              </select>
            </div>
          </div>


          <div class="form-group custom_form_group">
            <label class="control-label d-block" style="text-align: left;"> Employee Status:</label>
            <div>
              <select class="form-control" name="emp_status_id">
                <option value="0">All</option>
                @foreach($emplyoyeeStatus as $status)
                <option value="{{ $status->id }}">{{ $status->title }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group custom_form_group">
            <label class="control-label d-block" style="text-align: left;">Select Month:</label>
            <div>
              <select class="form-control" name="month" required>
                @foreach($month as $item)
                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div> -->


          <div class="form-group row custom_form_group">
                <label class="col-sm-3 control-label">Working Project:</label>
                <div class="col-sm-5">
                <select class="form-control" name="proj_id" required>
                  {{-- <option value="0">All</option> --}}
                  @foreach($projects as $proj)
                  <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                  @endforeach
                </select>
              </div>
          </div>

          {{-- Sponser List --}}
          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label" >Select Sponser:</label>
            <div class="col-sm-5">
              <select class="form-control" name="SponsId" required>
                <option value="0">All</option>
                @foreach($sponser as $spons)
                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Select Month:</label>
            <div class="col-sm-5">
               <select class="form-control" name="month" required>
                @foreach($month as $item)
                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="col-sm-3 control-label"  >Year:</label>
            <div class="col-sm-5">
                        <select class="form-control" name="year" required>
                                        @foreach(range(date('Y'), date('Y')-1) as $y)
                                          <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                        @endforeach
                        </select>
            </div>
          </div>

          <div class="form-group row custom_form_group">
            <label class="control-label col-sm-3"> Employee Status:</label>
            <div class="col-sm-5">
              <select class="form-control" name="emp_status_id">
                @foreach($emplyoyeeStatus as $status)
                <option value="{{ $status->id }}">{{ $status->title }}</option>
                @endforeach
              </select>
            </div>
          </div>

        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>




<!-- All Employee Work Status Summary Report -->
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-8">
    <form class="form-horizontal" id="registration" target="_blank" action="{{ route('all-employee-work-status-summary') }}" method="post">
      @csrf
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-12">
              <h3 class="card-title card_top_title salary-generat-heading">Total Employees Summary Report </h3>
            </div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="card-body card_form" style="padding-top: 0;">

          <div class="form-group row custom_form_group">
            <label class="control-label col-sm-3"  >Select Month:</label>
            <div class="col-sm-5">
              <select class="form-control" name="month" required>
                @foreach($month as $item)
                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' :'' }}>{{ $item->month_name }}</option>
                @endforeach
              </select>
            </div>
          </div>


          {{-- Sponser List --}}
          <!-- <div class="form-group custom_form_group">
            <label class="control-label d-block" style="text-align: left;">Select Sponser:</label>
            <div>
              <select class="form-control" name="SponsId" required>
                <option value="0">All</option>
                @foreach($sponser as $spons)
                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                @endforeach
              </select>
            </div>
          </div> -->

          <!-- <div class="form-group custom_form_group">
            <label class="control-label d-block" style="text-align: left;">Select Project:</label>
            <div>
              <select class="form-control" name="proj_id" required>
                <option value="0">All</option>
                @foreach($projects as $proj)
                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                @endforeach
              </select>
            </div>
          </div> -->

        </div>
        <div class="card-footer card_footer_button text-center">
          <button type="submit" class="btn btn-primary waves-effect">Show</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-md-2"></div>
</div>


@endsection
