@extends('layouts.admin-master')
@section('title') Employee Iqama Expired @endsection
@section('internal-css')
  <style media="screen">
      tr td span.days{
        display: inline-block;
        color: #fff;
        padding: 5px 10px;
      }
  </style>
@endsection

@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Iqama Expired Report</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active"> Employees Iqama Expired Report</li>
        </ol>
    </div>
</div>
<!-- division list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">                   
              </div>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table id="alltableinfo" class="responsive table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>S.N</th>
                                      <th>Emp. ID</th>
                                      <th>Name</th>                                      
                                      <th>Designation</th>
                                      <th>Basic</th>
                                      <th>Hourly Rate</th> 
                                      <th>Iqama No</th>                                    
                                      <th>Iqama Expire Date</th>
                                      <th>Remaining Days</th>
                                  </tr>
                              </thead>
                              <tbody>

                                @php
                                  $second = new DateTime(Carbon\Carbon::now()->format('Y-m-d'));
                                @endphp

                                @foreach($emp as $item)
                                
                                    @php
                                        $expire = $item->akama_expire_date;                                
                                        $first = new DateTime($expire);                                 
                                        $diffDate = date_diff($second,$first);
                                        $diff =  $diffDate->format("%R%a days");
                                        $days =  $diffDate->format("%R%a");
                                    @endphp

                                <tr>

                                  <td>{{ $loop->iteration }}</td>
                                  <td>{{ $item->employee_id }}</td>
                                  <td>{{ $item->employee_name }}</td>                                
                                  <td>{{ $item->catg_name }}</td>
                                  <td>{{ $item->basic_amount }}</td>        
                                  <td>{{ $item->hourly_rent }}</td>                          
                                  <td>{{ $item->akama_no }}</td>
                                  <td>{{ $item->akama_expire_date }}</td>


                                  @if($days >= 1 && $days <= 30)
                                  <td> <span class="days" style="background: red"> {{ $diff }}</span> </td>
                                  @elseif($days <= 0)
                                  <td> <span class="days" style="background: red">Expired</span> </td>
                                  @elseif($days <= 60)
                                  <td> <span class="days" style="background: yellow; color: #222">{{ $diff }}</span> </td>
                                  @elseif($days <= 90)
                                  <td> <span class="days" style="background: green">{{ $diff }}</span> </td>
                                  @else
                                  <td> <span>{{ $diff }}</span> </td>
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
@endsection
