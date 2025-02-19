@extends('layouts.admin-master')
@section('content')

<style>
      /* Employee Information Table */
      #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 10px;
            padding: 5px;
        }

        #employeeinfo tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #employeeinfo tr:hover {
            background-color: #ddd;
        }

        #employeeinfo th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: left;
            background-color: #EAEDED;
            color: black;
        }
        .td__value{
            text-align: center;
            font-style:bold;
        }
        .td__total{
            text-align: center;
            font-style:bold;
            color: black;
        }
    </style>

<div class="row bread_part">
    <div class="col-sm-12 bread_col">
        <h4 class="pull-left page-title bread_title">Dashboard</h4>
        <ol class="breadcrumb pull-right">
            <li><a href="#">Dashboard</a></li>
            <li class="active">Home</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                <!-- <span class="counter text-dark"></span> -->
                <a href="https://asloobb.com/" target="_blank"> Company Profile </a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                Active = {{ $noOfEmpl }} <br>
                Vacation = {{ $noOfEmpl_vacation }} <br>
                Total Employees = {{ $noOfEmpl_vacation+ $noOfEmpl  }}
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-contacts"></i></span>
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                <span class="counter text-dark">{{ $noOfProjects }}</span>
                Total Running Project
            </div>
        </div>
    </div>   
    <div class="col-md-6 col-xl-3">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-contacts"></i></span>
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                <span class="counter text-dark">{{ $noOfUsers }}</span>
                Total Users
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-explore"></i></span>
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                <span class="counter text-dark">{{ date('Y') }}</span>
                Report
            </div>
        </div>
    </div>
    
    <!-- Yesterday Nightshift Attendance Summary -->
    <div class="col-md-6 col-xl-3">
        <table id="employeeinfo">                    
            <tr> <td colspan="6" class="td__value">Yesterday Night Shift Attendance Summary</td></tr>
            <tr>
                <th>S.N</th>
                <th>Project Name</th>
                <th>Shift</th>
                <th>Total</th>
                <th>Present</th>
                <th>Absent</th>
            </tr>
            @php $ave_total_emp = 0; $ave_total_presence = 0; $total_emp = 0;  $total_present = 0; @endphp
            @foreach($yesterday_nightshift_attend_summary as $record)
            @php
              $total_emp += $record->total_emp;
              $total_present += $record->total_present;
              if($record->proj_id == 29 || $record->proj_id == 40 || $record->proj_id == 42){
                $ave_total_emp += $record->total_emp;  
                $ave_total_presence += $record->total_present;
              }
            @endphp
                <tr>                           
                    <td>{{$loop->iteration }}</td>
                    <td>{{ $record->proj_name}}</td>
                    <td>{{ $record->is_night_shift == 1 ? 'Night':'Day'}}</td>
                    <td class="td__value">{{ $record->total_emp }}</td>
                    <td class="td__value">{{ $record->total_present }}</td>
                    <td class="td__value">{{ $record->total_emp - $record->total_present }}</td>
                </tr>
            @endforeach
            <tr>
                 <td colspan="3" class="td__total">Total</td>
                 <td class="td__total">{{$total_emp}}</td>
                 <td class="td__total">{{$total_present}}</td>
                 <td class="td__total">{{$total_emp - $total_present}}</td>
            </tr> 
        </table> 
    </div>
    <!-- Today Attendance Summary -->
    <div class="col-md-6 col-xl-3">
                <table id="employeeinfo">                    
                    <tr> <td colspan="6" class="td__value">Today Attendance Summary</td></tr>
                    <tr>
                        <th>S.N</th>
                        <th>Project Name</th>
                        <th>Shift</th>
                        <th>Total</th>
                        <th>Present</th>
                        <th>Absent</th>
                    </tr>
                    @php  $total_emp = 0;  $total_present = 0; @endphp
                    @foreach($attendance_summary as $record)
                    @php
                      $total_emp += $record->total_emp;
                      $total_present +=$record->total_present;

                      if($record->proj_id == 29 || $record->proj_id == 40 || $record->proj_id == 42){
                            $ave_total_emp += $record->total_emp;  
                            $ave_total_presence += $record->total_present;
                        }                      
                    @endphp
                        <tr>                           
                            <td>{{$loop->iteration }}</td>
                            <td>{{ $record->proj_name}}</td>
                            <td>{{ $record->is_night_shift == 1 ? 'Night':'Day'}}</td>
                            <td class="td__value">{{ $record->total_emp }}</td>
                            <td class="td__value">{{ $record->total_present }}</td>
                            <td class="td__value">{{ $record->total_emp - $record->total_present }}</td>
                        </tr>
                    @endforeach
                    <tr>
                         <td colspan="3" class="td__total">Total</td>
                         <td class="td__total">{{$total_emp}}</td>
                         <td class="td__total">{{$total_present}}</td>
                         <td class="td__total">{{$total_emp - $total_present}}</td>
                    </tr>

                </table> 
                
    </div>

    <!-- Yester Total Present -->
    <div class="col-md-6 col-xl-3">
        <div class="mini-stat clearfix bx-shadow bg-white">
            <span class="mini-stat-icon bg-primary"><i class="md md-person"></i></span>
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                Yesterday Present = {{ $yesterday_present }} <br>
                Absent = {{ $noOfEmpl - $yesterday_present }} <br>                
            </div>
            
            <div class="mini-stat-info text-right text-dark mini_stat_info">
                <hr>
                Avenue Mall Project <br>
                Dayshift Employee - {{ $ave_total_emp }} <br>
                Total Presence - {{ $ave_total_presence }} <br>
                Total Absence - {{ $ave_total_emp - $ave_total_presence }}             
            </div>

             
        </div>
    </div>



    <div id="app">
        <example-component></example-component>
        <example-component></example-component>
        <example-component></example-component>
    </div>
    
</div>
@endsection
