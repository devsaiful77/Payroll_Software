@extends('layouts.admin-master')
@section('title') Daily Cost List @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Daily Expenditure List For Approval</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Expenditure Approval</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @if(Session::has('approve'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong>Successfully!</strong> Approve Cost Information.
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong>Opps!</strong> please try again.
          </div>
        @endif
    </div>
</div>

<!-- division list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                      <h3 class="card-title card_top_title"><i class="fab fa-gg-circle"></i> Expenditure Details</h3>
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
                                      <th>Date</th>
                                      <th>Project Name</th>

                                      <th>Cost By</th>
                                      <th>Amount</th>

                                      <th>Vouchar</th>
                                      <th>Approve</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach($all as $item)
                                  <tr>
                                    <td>{{ Carbon\Carbon::parse($item->expire_date)->format('D, d F Y') }}</td>
                                    <td>{{ $item->project->proj_name }}</td>

                                    <td>{{ $item->employee->employee_name }}</td>
                                    <td>{{ $item->amount }}</td>

                                    <td>



                                      <a href="{{ asset('uploads/vouchar/'.$item->vouchar) }}" class="project-popup">
                                          <img src="{{ asset('uploads/vouchar/'.$item->vouchar) }}" alt="Vouchar" style="width: 80px">
                                      </a>
                                    </td>
                                    <td>
                                      @if($item->status == 'pending')
                                      <a href="{{ route('approve-cost',$item->cost_id) }}" title="Approve"><i class="fas fa-thumbs-up fa-lg edit_icon"></i></a>
                                      @else
                                        ---
                                      @endif
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
@endsection
