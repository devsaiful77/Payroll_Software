<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary Report
    </title>
    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid blue;
            border-right: 16px solid green;
            border-bottom: 16px solid red;
            border-left: 16px solid pink;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        body {
            overflow: hidden;
            border: 1px solid black;
            margin: 15px;

        }

        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #fff;
            /* change if the mask should have another color then white */
            z-index: 99;
            /* makes sure it stays on top */
        }

        #status {
            width: 200px;
            height: 200px;
            position: absolute;
            left: 50%;
            /* centers the loading animation horizontally one the screen */
            top: 50%;
            /* centers the loading animation vertically one the screen */
            background-image: url(https://raw.githubusercontent.com/niklausgerber/PreLoadMe/master/img/status.gif);
            /* path to your loading animation */
            background-repeat: no-repeat;
            background-position: center;
            margin: -100px 0 0 -100px;
            /* is width and height divided by two */

            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid blue;
            border-right: 16px solid green;
            border-bottom: 16px solid red;
            border-left: 16px solid pink;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 95%;
                margin: 5px;

            }


            @page {
                size: A4 portrait;
                margin: 5mm 0mm 5mm 0mm;
                /* top, right,bottom last value was= 10, left */

            }

            a[href]:after {
                content: none !important;
            }

            .print__button {
                visibility: hidden;
            }
        }


        .main__wrap {
            width: 95%;
            margin: 5px auto;

        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .title__part {
            text-align: center;
            font-size: 14px;
        }

        .title_left__part {
            text-align: left;
            font-size: 10px;
        }

        .address {
            font-size: 10px;
        }

        .print__part {
            text-align: right;
            font-size: 10px;
        }

        /* table part */
        .td__center {
            text-align: center
        }

        .td__bold {
            font-weight: 400;
        }

        .td__project {
            font-size: 8px;
            color: red;
            font-weight: 100;
            text-align: center;

        }

        .td__amount {
            font-size: 12px;
            color: black;
            font-weight: 100;
            text-align: center
        }
        .td__gross_salary {
            font-size: 11px;
            color: black;
            font-weight: bold;
            text-align: center
        }

        .box__signature {
            width: 150px;
            height: 20px;
            border: 1px solid gray;
            margin: 0px;
            padding: 0px;
            color: lightgray;
        }



        /* Employee Information Table */

        #employeeinfo {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .table__part {
            display: flex;
        }


        #employeeinfo td,
        #employeeinfo th {
            border: 1px solid #ddd;
            font-size: 12px;
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
            text-align: center;
            background-color: #EAEDED;
            color: black;
            font-weight: bold;
        }

       
    </style>
</head>

<body>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> --}}
    <!-- Preloader -->
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>

    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">

            <div class="title_left__part">
            </div>
            <!-- title -->
            <div class="title__part">
                <h6>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h6>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>

        <section>
            <h5 style=" text-align:center; font-size:12px; padding-bottom:5px; "><b> Employee Salary Summary</b></h5>
        </section>

        <!-- Employee Info part -->
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td> Employee ID:</td>
                        <td class="td__bold"> {{ $employee->employee_id}}</td>

                        <td> Emp. Status:</td>
                        <td class="td__bold">
                            @if($employee->job_status == 1)
                                Active
                            @elseif($employee->job_status == 2)
                                Inactive
                            @elseif($employee->job_status == 3)
                                Prerelease
                            @elseif($employee->job_status == 4)
                                Release
                            @elseif($employee->job_status == 5)
                                 Vacation
                            @endif                           
                            
                        </td>
                    </tr>
                    <tr>
                        <td> Employee Name:</td>
                        <td class="td__bold">{{$employee->employee_name}}</td>
                        <td> Sponser:</td>
                        <td> {{$employee->sponser->spons_name}} </td>
                    </tr>
                    <tr>
                        <td> Iqama No:</td>
                        <td> {{$employee->akama_no}}, {{$employee->akama_expire_date}}</td>
                        <td> Trade:</td>
                        <td> {{$employee->employeeType->name}}, {{$employee->category->catg_name}} </td>
                    </tr>
                    <tr>
                        <td> Agency Name:</td>
                        <td> {{$employee->agc_title}}</td>
                        <td> Joining:</td>
                        <td> {{$employee->joining_date }}</td>
                    </tr>
                    <tr>
                        <td> Address:</td>
                        <td> {{$employee->mobile_no}},
                            {{$employee->details}},{{$employee->address}},{{$employee->country->country_name}}
                        </td>
                        <td> Project:</td>
                        <td> {{$employee->project->proj_name}} </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section>
            <br>
            <p style=" text-align:center; font-size:16px; "> Advance Details</p>
        </section>

        <!-- 'salary_records', 'advace_deduction_from_salary_total_amount',  'cash_receive_total_Amount',
             'iqama_renewal_records', 'iqama_renewal_total_amount', 'advance_records', 'advance_total_amount -->

        <!-- Salary Details part -->
        <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th>SL No </th>
                        <th>Advance Date</th>
                        <th> Year </th>
                        <th> Month </th>
                        <th>Advance Amount </th>
                        <th>Paid Amount</th>
                        <th> Unpaid Balance </th>
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                    $counter = 0; 
                    $adv_total_amnt = 0;    
                    $adv_paid_total_amnt = 0;                

                    @endphp

                    @foreach($advance_records as $adv)
                   
                        @php
                            $adv_paid = $counter < count($salary_records) ?  $salary_records[$counter]->slh_other_advance : 0 ;  
                            $counter += 1;
                                                    
                            $adv_total_amnt += $adv->adv_amount ;  
                            $adv_paid_total_amnt += $adv_paid ;   
                        @endphp

                    

                    <tr>
                        <td class="td__center">{{ $loop->iteration }}</td>
                        <td class="td__center">{{ $adv->date }}</td>
                        <td class="td__amount"> {{ $adv->year }} </td>
                        <td class="td__amount"> {{ date('F', mktime(0, 0, 0, $adv->adv_month, 10)); }}  </td>
                        <td class="td__amount"> {{ $adv->adv_amount  }}   </td>
                        <td class="td__amount"> {{ $adv_paid}}  </td>
                        <td class="td__amount"> {{ $adv_total_amnt - $adv_paid_total_amnt  }} </td>
                    </tr> 
                    @endforeach
                    <tr>
                        <td class="td__gross_salary" colspan="4">Total </td>
                        <td class="td__gross_salary">{{ $advance_total_amount }} </td>
                        <td class="td__gross_salary">{{ $advace_deduction_from_salary_total_amount }} </td>
                        <td class="td__gross_salary">{{ $advance_total_amount - $advace_deduction_from_salary_total_amount }} </td>                   
                    </tr>
                    <!-- <tr>
                        <td class="td__center"> </td>
                        <td class="td__gross_salary" colspan="4">Total Cash Received </td>
                        <td class="td__gross_salary">{{ $cash_receive_total_Amount }} </td>     
                        <td class="td__gross_salary"> $adv_total_amnt - ($adv_paid_total_amnt ) </td>                
                    </tr> -->
                </tbody>
            </table>
        </section>

         
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>  <b>  ------------------- <br> {{$login_user}}  </b> <br> Prepared By  </p>
                    <p>  <b>  -------------------- <br>   </b> <br> Verified By  </p>
                    <p>  <b>  ----------------------- <br>   </b> <br> Managing Director  </p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>

    </div>
</body>

<script>
    $(window).on('load', function () { // makes sure the whole site is loaded
        $('#status').fadeOut(); // will first fade out the loading animation
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
        $('body').delay(350).css({
            'overflow': 'visible'
        });
    })
</script>

</html>
