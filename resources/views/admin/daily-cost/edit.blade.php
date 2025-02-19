@extends('layouts.admin-master')
@section('title') Edit Daily Cost @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Edit Daily Cost</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Edit Daily Cost</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <form class="form-horizontal" id="registration" action="{{ route('update-daily-cost') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Edit Daily Cost</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <input type="hidden" name="id" value="{{ $edit->cost_id }}">
              <input type="hidden" name="old_image" value="{{ $edit->vouchar }}">
              <div class="form-group custom_form_group{{ $errors->has('cost_type_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Cost Type:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="cost_type_id" required>
                        <option value="">Select Here</option>
                        @foreach($costType as $cost)
                        <option value="{{ $cost->cost_type_id }}" {{ $cost->cost_type_id == $edit->costType->cost_type_id ? 'selected' : '' }}>{{ $cost->cost_type_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('cost_type_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('cost_type_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="form-group custom_form_group{{ $errors->has('project_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Project Name:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="project_id" required>
                        <option value="">Select Here</option>
                        @foreach($project as $proj)
                        <option value="{{ $proj->proj_id }}" {{ $proj->proj_id == $edit->project->proj_id ? 'selected' : '' }}>{{ $proj->proj_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('project_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('project_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="form-group custom_form_group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Expenditure By:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="employee_id" required>
                        <option value="">Select Here</option>
                        @foreach($employee as $emp)
                        <option value="{{ $emp->emp_auto_id }}" {{ $emp->emp_auto_id == $edit->employee->emp_auto_id ? 'selected' : '' }} >{{ $emp->employee_name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('employee_id'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('employee_id') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('vouchar_no') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Vouchar No:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" id="vouchar_no" name="vouchar_no" value="{{ $edit->vouchar_no }}" placeholder="Vouchar No" required>
                    @if ($errors->has('vouchar_no'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('vouchar_no') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group custom_form_group{{ $errors->has('expire_date') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Expire Date:<span class="req_star">*</span></label>
                  <div>
                    <input type="date" class="form-control" id="expire_date" name="expire_date" value="{{ $edit->expire_date }}" required>
                    @if ($errors->has('expire_date'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('expire_date') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>
              <div class="form-group custom_form_group{{ $errors->has('amount') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Amount:<span class="req_star">*</span></label>
                  <div>
                    <input type="text" class="form-control" placeholder="Amount" id="amount" name="amount" value="{{ $edit->amount }}" required>
                    @if ($errors->has('amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('amount') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>

              <div class="form-group {{ $errors->has('vouchar') ? ' has-error' : '' }}">
                <div class="vouchar_image">
                    <img src="{{asset('uploads/vouchar/'.$edit->vouchar)}}" alt="">
                </div>
                <label class="control-label">Vouchar:</label>
                <div class="input-group">
                    <span class="input-group-btn">
                        <span class="btn btn-default btn-file btnu_browse">
                            Browse… <input type="file" id="vouchar" name="vouchar" id="imgvouchar">
                        </span>
                    </span>
                    <input type="text" class="form-control" readonly>
                </div>
                @if ($errors->has('vouchar'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('vouchar') }}</strong>
                    </span>
                @endif
                <img id='img-vouchar'/>

              </div>

            </div>
            <div class="card-footer card_footer_button text-center">
                <button type="submit" class="btn btn-primary waves-effect">UPDATE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>


@endsection
