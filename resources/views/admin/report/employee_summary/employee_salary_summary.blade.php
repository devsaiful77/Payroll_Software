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
                size: A4 landscape;
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

        .td__gross_salary {
            font-size: 11px;
            color: navy;
            font-weight: 200;
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
                        <td class="td__bold"> {{ $employee->employee_id}}, {{ $employee->email}} </td>

                        <td> Emp. Status:</td>
                        <td class="td__bold"><b>
                            @if($employee->job_status == 1)
                                Active
                            @elseif($employee->job_status == 2)
                                Inactive
                            @elseif($employee->job_status == 3)
                                Final Exit
                            @elseif($employee->job_status == 4)
                                Release
                            @elseif($employee->job_status == 5)
                                 Vacation
                            @elseif($employee->job_status == 6)
                                 Runway
                            @endif                           
                            , {{$empl_last_activity}}                             
                          , Salary Status: {{$employee->salary_status->name}}
                         </b>
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
                        <td> Passport No:</td>
                        <td> {{$employee->passfort_no}}, {{$employee->passfort_expire_date}}</td>
                        <td> Mobile No</td>
                        <td> {{$employee->mobile_no }}, {{$employee->phone_no }}</td>
                    </tr>
                    
                    <tr>
                        <td> Agency Name:</td>
                        <td> {{$employee->agc_title}}</td>
                        <td> Joining:</td>
                        <td> {{$employee->joining_date }}</td>
                    </tr>
                    <tr>
                        <td> Address:</td>
                        <td>  
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
            <p style=" text-align:center; font-size:16px; "> Salary Details</p>
        </section>

        <!-- Salary Details part -->
        <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr>
                        <th>SL No </th>
                        <th> Month </th>
                        <th> BS/Rate </th>
                        <th>Hr/WrD </th>
                        <th> OT/Amt</th>
                        <th> (Food+Other) </th>
                        <th> Total Salary </th>
                        <th> (Adv1+Adv2) </th>
                        <th>ID Renew</th>
                        <th> Gross Salary </th>
                        <th> Project </th>
                        <th> Status </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totalHours = 0;
                    $totalOverTimeHours = 0;
                    $totalOverTimeAmount = 0;
                    $totalFoodAllowance = 0;
                    $totalSaudiTax = 0;
                    $totalContribution = 0;
                    $totalOtherAdvance = 0;
                    $totalIqamaRenewal = 0;
                    $totalSalaryAmount = 0;

                    @endphp

                    @foreach($unpaid_salary_records as $salary)

                    @php
                    $totalHours += $salary->slh_total_hours;
                    $totalOverTimeHours += $salary->slh_total_overtime;
                    $totalOverTimeAmount += $salary->slh_overtime_amount;
                    $totalFoodAllowance += $salary->food_allowance;
                    $totalIqamaRenewal += $salary->slh_iqama_advance;
                    $totalSaudiTax += $salary->slh_saudi_tax;
                    $totalContribution += $salary->slh_cpf_contribution;
                    $totalOtherAdvance += $salary->slh_other_advance;
                    $totalSalaryAmount += $salary->slh_total_salary;

                    @endphp

                    <tr>
                        <td class="td__center">{{ $loop->iteration }}</td>

                        <td class="td__gross_salary"> {{ date('F', mktime(0, 0, 0, $salary->slh_month, 10)); }}, {{
                            $salary->slh_year }} </td>
                        <td class="td__center"> {{ $salary->basic_amount }}/{{ $salary->hourly_rent }}</td>
                        <td class="td__sponser"> {{ $salary->slh_total_hours }}/{{ $salary->slh_total_working_days }}
                        </td>
                        <td class="td__center"> {{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}
                        </td>

                        <td class="td__center"> {{round($salary->food_allowance) }} + {{ $salary->otherAmount }} </td>
                        <td class="td__gross_salary"> {{round( $salary->slh_total_salary + $salary->slh_cpf_contribution
                            + $salary->slh_iqama_advance + $salary->slh_saudi_tax + $salary->slh_other_advance) }} </td>
                        <td class="td__center"> {{ round($salary->slh_saudi_tax) }} +
                            {{round($salary->slh_other_advance) }}</td>

                        <td class="td__center">{{ round($salary->slh_cpf_contribution)}}/{{
                            round($salary->slh_iqama_advance) }} </td>
                        <td class="td__gross_salary"><u> {{ round($salary->slh_total_salary) }}</u> </td>
                        <td class="td__project"> {{ Str::limit($salary->project->proj_name,40) }}<br></td>
                        <td class="td__gross_salary">
                            <div class="box__signature"> {{ $salary->Status == 0 ? 'Unpaid':'Paid'}} </div>
                        </td>

                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </section>



        <section>
            <br>
            <table id="employeeinfo">
            <tbody>
                    <tr>
                        <td class="td__gross_salary"> TOTAL CASH RECEIVED FROM EMPLOYEE:</td>
                        <td class="td__gross_salary"> {{$cashReceiveTotalPaidAmount }}</td>
                        <td class="td__gross_salary">UNPAID SALARY TOTAL AMOUNT:</td>
                        <td class="td__gross_salary">{{ $totalUnPaidSalaryAmount }} </td>
                    </tr>
                    <tr>

                        <td class="td__gross_salary">IQAMA EXPENCE PAYMENT RECEIVED FROM SALARY: </td>
                        <td class="td__gross_salary">{{ $toal_iqama_expense_deduction_from_salary }} </td>
                        <td class="td__gross_salary"  > OTHER ADVANCE TOTAL AMOUNT </td>
                        <td class="td__gross_salary"  > {{ $otherAdvanceTotalAmount}} </td>
                       
                    </tr>
                    <tr>
                        <td class="td__gross_salary">IQAMA RENEWAL PAID TOTAL AMOUNT:</td>
                        <td class="td__gross_salary">{{ $toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount }} </td>
                        <td class="td__gross_salary"> OTHER ADVANCE PAID TOTAL AMOUNT:</td>
                        <td class="td__gross_salary"> {{ $total_other_advace_deduction_from_salary}} </td>
                    </tr>

                    <tr>
                        <td class="td__gross_salary">BD OFFICE PAID TOTAL AMOUNT</td>
                        <td class="td__gross_salary"> {{$bd_office_paid_total_amount}} </td>
                        <td class="td__gross_salary"> </td>
                        <td class="td__gross_salary">  </td>
                        
                    </tr>
                   
                    <tr>
                        <td class="td__gross_salary">TOTAL AMOUNT EXPENSE OF IQAMA:</td>
                        <td class="td__gross_salary">{{ $iqamaRenewalTotalExpence }} </td>
                        <td class="td__gross_salary">IQAMA EXPENSE UNPAID:</td>
                        <td class="td__gross_salary"> {{ $iqamaRenewalTotalExpence - ($toal_iqama_expense_deduction_from_salary+$cashReceiveTotalPaidAmount) }} </td>
                        
                    </tr>

                    

                    

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
