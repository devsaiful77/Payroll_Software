<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Salary Month : {{ $monthName }},{{$salaryYear}}
        {{$company->comp_name_en}} {{$company->comp_name_arb}}
    </title>



    <!-- style -->
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
        }


        /* Preloader */

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
                max-width: 98%;
                margin: 0 auto 10px;

            }


            @page {
                size: A4 landscape;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom last value was= 10, left */

            }

            a[href]:after {
                content: none !important;
            }
             .print__button {
                visibility: hidden;
            }
          table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }
            
        }
        /*   End of Page Print Setting */


        .main__wrap {
            width: 90%;
            margin: 20px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
        }

        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 5px;
        }

        table,
        tr {
            border: 1px solid gray;
            border-collapse: collapse;
        }

        table th {
            font-size: 13px;
        }

        table td {
            text-align: left;
            font-size: 13px;

        }

        th,
        td {
            padding: 5px 2px;
            /* Top,Right,Bottom,left */
        }

        .td__left {

            text-align: left
        }

        .td__center {
            text-align: center
        }

        .td__right {
            text-align: right
        }

        .td__name {
            padding-bottom: 25px;
        }

        .td__bold {
            font-weight: 700;
        }

        .td__emplyoeeId {
            font-size: 14px;
            padding-bottom: 25px;
            color: blue;
            font-weight: 900;
            text-align: center
        }

        .td__sponser {
            color: green;
            font-weight: 300;
            text-align: center;

        }

        .sponser__name {
            font-size: 10px;
            font-weight: 100;

        }

        .country {
            color: red;
            text-align: center;
            font-size: 10px;
        }

        .employe__trade {
            color: red;
            text-align: center;
            font-size: 10px;
        }

        .td__project {
            font-size: 8px;
            padding-bottom: 5px;
            color: red;
            font-weight: 100;
            text-align: center;

        }


        .td__multi__project {
            font-size: 10px;
            color: red;
            text-align: center
        }


        .td__red__color {
            color: red;
            text-align: center;
        }

        .td__total {
            font-weight: bold;
            text-align: center;
            color: black;
            font-size: 14px;
        }

        .box__signature {
            width: 200px;
            height: 30px;
            border: 1px solid gray;
            margin-top: 5px;
            margin-left: 0px;
            color: gray;
        }

        .td__gross_salary {
            font-size: 14px;
            color: navy;
            font-weight: 900;
            text-align: center
        }
    </style>
    <!-- style -->
</head>

<body>




    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!-- Preloader -->
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>


    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="date__part">
                <p> Salary Month : <strong class="td__red__color"> {{ $monthName }}, {{$salaryYear}} </strong> </p>
            </div>
            <!-- title -->
            <div class="title__part">

                <!-- preloder -->
                <!-- <div class="loader"></div> -->
                <!-- preeloder -->


                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
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
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th>SL<br>No </th>
                        <th> Employee <br> ID </th>
                        <th> Employee Name </th>
                        <th> Iqama No<br> BS/Rate </th>
                        <th>Sponsor <br>Hr/WrD </th>
                        <th>Nation <br> OT/Amt</th>
                        <th> Trade <br> (Food+Other) </th>
                        <th> <br> Total Salary </th>
                        <th> <br> (S.T+Adv+C.D) </th>
                        <th> <br>Contribution<br>ID Renew</th>
                        <th> <br> Gross Salary </th>
                        <th> Project <br> Signature </th>
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
                    $grandTotalSalary = 0;
                    $total_slh_all_include_amount = 0 ;
                    @endphp

                    @foreach($salaryReport as $salary)

                    {{-- find multiple project --}}
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
                    
                    $total_slh_all_include_amount += $salary->slh_all_include_amount;

                    $multi_pro_total_hour = "";
                    $multi_project = "";
                    $findMultipleProjectEmployee = App\Models\EmployeeMultiProjectWorkHistory::where('emp_id',$salary->emp_auto_id)->where('month',$salary->slh_month)->where('year',$salary->slh_year)->get();
                    @endphp

                    <tr style="border-bottom:0;">
                        <td class="td__center td__name">{{ $loop->iteration }}</td>
                        <td class="td__center td__emplyoeeId"> {{ $salary->employee_id }} </td>
                        <td class="td__left td__name"> <span>{{ $salary->employee_name }}</span> </td>

                        <td class="td__center"> {{ $salary->akama_no }} <br><br> {{ $salary->basic_amount }}/{{ $salary->hourly_rent }}

                        </td>
                        <td class="td__sponser"> <span class="sponser__name">{{Str::limit( $salary->sponser->spons_name,15)}}</span> <br><br> {{ $salary->slh_total_hours }}/{{ $salary->slh_total_working_days }} </td>
                        <td class="td__center"> <span class="country"> {{ Str::limit($salary->country->country_name,3) }}</span> <br><br>{{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}</td>

                        <td class="td__center"> <span class="employe__trade"> {{ Str::limit($salary->category->catg_name,10) }}</span> <br><br> {{round($salary->food_allowance) }}
                             + {{ $salary->house_rent + $salary->mobile_allowance  + $salary->medical_allowance + $salary->local_travel_allowance 
                            + $salary->conveyance_allowance + $salary->others + $salary->slh_bonus_amount  }}
                        </td>
                         <td class="td__gross_salary"><br><br> {{round( $salary->slh_all_include_amount) }}
                         </td>
                        <td class="td__center"><span class="td__red__color"> {{ "" }} </span> <br><br> {{ round($salary->slh_saudi_tax) }} +
                            {{round($salary->slh_other_advance) }} + {{ $salary->slh_food_deduction }}
                        </td>

                        <td class="td__center"> <br><br> {{ round($salary->slh_cpf_contribution)}}/{{round( $salary->slh_iqama_advance) }} </td>
                        <td class="td__gross_salary"> <br><br><u> {{ $salary->slh_total_salary }} </u> </td>
                        <td class="td__project"> {{ Str::limit($salary->project->proj_name,40) }}<br>
                            <div class="box__signature">  
                                 @if($salary->salary_status >=2)
                                   <b> Salary Hold </b>  
                                 @else 
                                    {{  $salary->Status == 0 ? 'Unpaid':'Paid'}} 
                                 @endif 
                            </div> 
                        </td>
                    </tr>



                    @if($findMultipleProjectEmployee->count()>1)
                    @foreach($findMultipleProjectEmployee as $multiData)
                    @php
                    $multi_pro_total_hour = $multi_pro_total_hour.($multiData->total_hour."/".$multiData->total_day)."<br>";
                    $multi_project = $multi_project.$multiData->projectName->proj_name."<br>";
                    @endphp
                    @endforeach
                    <tr style="border:0; padding:0">
                        <td></td>
                        <td></td>
                        <td> </td>
                        <td></td>
                        <td class="td__center">{!! $multi_pro_total_hour !!}</td>
                        <td class="td__center"> </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td></td>
                        <td> </td>
                        <td class="td__multi__project">{!! $multi_project !!}</td>
                    </tr>

                    @endif

                 
                    @endforeach


                    <tr style="border-bottom:0;">
                        <td class="td__center td__name"></td>
                        <td class="td__center td__emplyoeeId"> {{ "" }} </td>
                        <td class="td__left td__name"> <span>{{ "" }}</span> </td>

                        <td class="td__total">Total <br><br> {{ "" }}</td>
                        <td class="td__total">Hours <br><br> {{ $totalHours }} </td>
                        <td class="td__total"> O.T/Amnt <br><br>{{$totalOverTimeHours}}/{{ $totalOverTimeAmount }}</td>

                        <td class="td__total">Food Allo. <br><br> {{$totalFoodAllowance }}</td>
                        <td class="td__total">Total Salary <br><br> {{ $total_slh_all_include_amount }}
                         </td>
                        <td class="td__total">S.T/Adv <br>{{ $totalSaudiTax }} <br>{{$totalOtherAdvance}}</td>
                        <td class="td__total">Iqama/CPF <br> {{ $totalIqamaRenewal}}<br>{{$totalContribution}} </td>
                        <td class="td__total"> Salary <br><br> {{round( $totalSalaryAmount) }} </td>
                        <td class="td__project"> {{""}} <br> {{""}} </td>
                    </tr>


                </tbody>
            </table>
        </section>
        <!-- ---------- -->


        </section>
        {{-- Officer Signature --}}
            <div class="row" style="padding-top: 40px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>  <b>  ------------------- <br> {{$login_name}}  </b> <br> Prepared By  </p>
                    <p>  <b>  -------------------- <br>   </b> <br> Verified By  </p>
                    <p>  <b>  ----------------------- <br>   </b> <br> Managing Director  </p>
                </div>
            </div>
        <section>
    </div>
</body>

<script>
    $(window).on('load', function() { // makes sure the whole site is loaded 
        $('#status').fadeOut(); // will first fade out the loading animation 
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website. 
        $('body').delay(350).css({
            'overflow': 'visible'
        });
    })
</script>

</html>