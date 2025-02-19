@extends('layouts.admin-master')
@section('title') Management User @endsection
@section('content')
<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title"> Manage User</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="active">User List</li>
        </ol>
    </div>
</div>
<!-- add division -->
<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        @if(Session::has('success'))
          <div class="alert alert-success alertsuccess" role="alert">
             <strong> {{ Session::get('success') }} </strong>
          </div>
        @endif
        @if(Session::has('error'))
          <div class="alert alert-warning alerterror" role="alert">
             <strong> {{ Session::get('error') }} </strong>
          </div>
        @endif
    </div>
    <div class="col-md-1"></div>
</div>

<!-- User list -->
<div class="row">
    <div class="col-lg-12">
      <div class="card">
          <div class="card-header">
              <div class="row">
                  <div class="col-md-8">
                   </div>
                  <div class="col-md-4 text-right">
                      <a href="{{ route('users.create') }}" class="btn btn-md btn-primary waves-effect card_top_button"><i class="fa fa-plus-circle mr-2"></i>New User</a>
                  </div>
                  <div class="clearfix"></div>
              </div>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">


                          <table id="alltableinfo" class="responsive table table-bordered custom_table mb-0">
                              <thead>
                                  <tr>
                                      <th>No</th>
                                      <th>Name</th>
                                      <th>Email</th>
                                      <th>Status</th>
                                      <th>Role</th>
                                      <th>Manage</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($data as $key => $user)
                                  <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->status == 1 ? "Active":"Inactive" }}</td>
                                    <td>
                                      @if(!empty($user->getRoleNames()))
                                        @foreach($user->getRoleNames() as $v)
                                           <label class="badge badge-success">{{ $v }}</label>
                                        @endforeach
                                      @endif
                                    </td>
                                    <td>
                                       <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">Edit</a>
                                        {!! Form::open(['method' => 'DELETE','route' => ['users.destroy', $user->id],'style'=>'display:inline']) !!}
                                        {{-- {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!} --}}
                                    </td>
                                  </tr>
                                @endforeach
                              </tbody>
                          </table>
                          {!! $data->render() !!}
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
</div>
@endsection
