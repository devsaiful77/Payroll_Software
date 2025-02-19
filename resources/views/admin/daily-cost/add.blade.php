@extends('layouts.admin-master')
@section('title') Create Daily Cost @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Create Daily Cost</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Create Daily Cost</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Added Daily Cost Information.
          </div>
        @endif
        @if(Session::has('success_update'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Update Daily Cost Information.
          </div>
        @endif
        @if(Session::has('delete'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Delete This Information.
          </div>
        @endif
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
      <form class="form-horizontal" id="registration" action="{{ route('insert-daily-cost') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Create Daily Cost</h3>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body card_form" style="padding-top: 0;">
              <div class="form-group custom_form_group{{ $errors->has('cost_type_id') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Cost Type:<span class="req_star">*</span></label>
                  <div>
                    <select class="form-control" name="cost_type_id" required>
                        <option value="">Select Here</option>
                        @foreach($costType as $cost)
                        <option value="{{ $cost->cost_type_id }}">{{ $cost->cost_type_name }}</option>
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
                        <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
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
                        <option value="{{ $emp->emp_auto_id }}">{{ $emp->employee_name }}</option>
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
                    <input type="text" class="form-control" id="vouchar_no" name="vouchar_no" value="{{old('vouchar_no')}}" placeholder="Vouchar No" required>
                    @if ($errors->has('vouchar_no'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('vouchar_no') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>


              <div class="form-group custom_form_group{{ $errors->has('expire_date') ? ' has-error' : '' }}">
                  <label class="control-label d-block" style="text-align: left;">Date:<span class="req_star">*</span></label>
                  <div>
                    <input type="date" class="form-control" id="expire_date" name="expire_date" value="{{old('expire_date')}}" required>
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
                    <input type="text" class="form-control" placeholder="Amount" id="amount" name="amount" value="{{old('amount')}}" required>
                    @if ($errors->has('amount'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('amount') }}</strong>
                        </span>
                    @endif
                  </div>
              </div>




              <div class="form-group {{ $errors->has('vouchar') ? ' has-error' : '' }}">
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
                <button type="submit" class="btn btn-primary waves-effect">CREATE</button>
            </div>
        </div>
      </form>
    </div>
    <div class="col-md-2"></div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Cost List</h3>
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
                                      <th>Cost Type</th>
                                      <th>Project Name</th>
                                      <th>Employee Name</th>
                                      <th>Amount</th>
                                      <th>Date</th>
                                      <th>Vouchar</th>
                                      <th>Status</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ $item->costType->cost_type_name }}</td>
                                    <td>{{ $item->project->proj_name }}</td>
                                    <td>{{ $item->employee->employee_name }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{ Carbon\Carbon::parse($item->expire_date)->format('D, d F Y') }}</td>
                                    <td>
                                      <img src="{{ asset('uploads/vouchar/'.$item->vouchar) }}" alt="Vouchar" style="width: 80px">
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item->status }}</span>
                                    </td>
                                    @if($item->status == 'pending')
                                    <td>
                                      <a href="{{ route('edit-cost',$item->cost_id) }}" title="edit"><i class="fa fa-pencil-square fa-lg edit_icon"></i></a>
                                      <a href="{{ route('delete-cost',$item->cost_id) }}" title="delete"  id="delete" title="delete data"><i class="fa fa-trash fa-lg delete_icon"></i></a>
                                    </td>
                                    @else
                                      <td> --- </td>
                                    @endif
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
$(document).ready(function () {
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [label]);
    });

    $('.btn-file :file').on('fileselect', function (event, label) {

        var input = $(this).parents('.input-group').find(':text'),
                log = label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }

    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img-vouchar').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imgvouchar").change(function () {
        readURL(this);
    });

});
</script>

@endsection
