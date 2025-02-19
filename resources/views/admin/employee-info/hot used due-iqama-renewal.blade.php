@extends('layouts.admin-master')
@section('title') Salary History @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Iqama History</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Iqama History</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
        <div class="alert alert-warning alerterror" role="alert">
            <strong>Opps!</strong> Employee Id Dosen,t Not Match!.
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <form class="form-horizontal" id="registration" target="_blank"
            action="{{ route('project-wise-iqwama-renewal-process') }}" method="post">
            @csrf
            <div class="card">
                <div class="card-body card_form">

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Project Name:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="proj_id" required>
                                <option value="0">ALL</option>
                                @foreach($projects as $proj)
                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="control-label col-md-3">Sponsor Name:</label>
                        <div class="col-md-6">
                            <select class="form-control" name="spons_id" required>
                                <option value="0">ALL</option>
                                @foreach($sponsor as $spons)
                                <option value="{{ $spons->spons_id }}">{{ $spons->spons_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Month Dropdown Menu -->
                    <div class="form-group row custom_form_group">
                        <label class="control-label col-3">Salary Month</label>
                        <div class="col-sm-7">
                            <select class="form-control" name="month" id="month">
                                @foreach($months as $month)
                                <option value="{{$month->month_id}}" {{$month->month_id == $currentMonth ? 'selected' :
                                    "" }}>{{$month->month_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row custom_form_group">
                        <label class="col-sm-3 control-label">Salary Year:<span class="req_star">*</span></label>
                        <div class="col-sm-6">
                            <select class="form-control" name="year">
                                @foreach(range(date('Y'), date('Y')-5) as $y)
                                <option value="{{$y}}" {{$y}}>{{$y}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="card-footer card_footer_button text-center">
                        <button type="submit" class="btn btn-primary waves-effect">PROCESS</button>
                    </div>

                </div>
        </form>
    </div>
    <div class="col-md-2"></div>
</div>
@endsection
