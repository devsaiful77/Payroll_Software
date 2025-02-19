<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Closing Sheet
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
            margin: 5px;

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
                max-width: 98%;
                margin: 0 auto 5px;

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
            
            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }
        }


        .main__wrap {
            width: 90%;
            margin: 10px auto;
            margin-top: 5px;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: right;
            margin-bottom: 5px;
        }

        .title__part {
            align-items: center;
        }

        .print__part {}

        /* table part */
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

        .td__center {
            text-align: center
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
            font-size: 11px;
            font-weight: 100;

        }

        .country {
            color: red;
            text-align: center;
            font-size: 11px;
        }

        .employe__trade {
            color: red;
            text-align: center;
            font-size: 11px;
        }

        .td__project {
            font-size: 8px;
            padding-bottom: 5px;
            color: red;
            font-weight: 100;
            text-align: center;

        }

        .td__gross_salary {
            font-size: 14px;
            color: navy;
            font-weight: 900;
            text-align: center
        }

        .box__signature {
            width: 150px;
            height: 30px;
            border: 1px solid gray;
            margin: 0px;
            color: lightgray;
        }

        .final_table {
            width: 70%;
            margin-left: auto;
            margin-right: auto;

        }

        .final_table tr {
            border: 0px;
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!-- Preloader -->
    <div id="preloader">
        <div id="status">&nbsp;</div>
    </div>



    <div class="main__wrap">
        <!-- Report Header part-->
        <section class="header__part">

            <div class="date__part">

            </div>
            <!-- title -->
            <div class="title__part">

                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address" style="text-align:center;">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
               <p> <strong>Print:</strong> {{ Carbon\Carbon::now() }} </p>
               <button type="" onclick="showEmployeActivityRecord()" class="print__button">Activities Details</button>
                &nbsp; &nbsp; &nbsp; &nbsp;
               <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>

        </section>

        <section>
            <br>
            <h3 style=" text-align:center; font-size:20px; "><b> Employee Salary Summary Details</b></h3><br>
        </section>
        <!-- Employee Info part -->
        <section class="table__part">
            <table id="employeeinfo">
                <tbody>
                    <tr>
                        <td> Employee ID:</td>
                              <td class="td__bold"> <span id ="emp_id">{{$employee->employee_id}}</span> </td>
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
                                 Runaway
                            @endif                           
                            , {{$empl_last_activity}}                             
                           ,Salary Status:  
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
                        <td>  {{$employee->details}},{{$employee->address}},{{$employee->country->country_name}}
                        </td>
                        <td> Project:</td>
                        <td> {{$employee->project->proj_name}} </td>
                    </tr>


                </tbody>
            </table>
        </section>
        <section>
            <br>
        </section>
        <!-- Salary Details part -->
        <section class="table__part">
            <table id="employeeinfo">
                <thead>
                    <tr> <td colspan="5" style="text-align:right; font-size:14px; "> 
                        Salary Details of {{ $employee->employee_id}}, {{$employee->employee_name}} </td>                    
                        <td colspan="5" style="text-align:center; font-size:14px; " > <b> Previous Balance: </b>  </td> 
                        <td style="text-align:center; font-size:14px; ">   {{ $closed_fiscal_record->balance_amount }} </td>   
                        <td   class="td__center">  </td>                                          
                    </tr>
                    <tr>
                        <th>SL No </th>
                        <th> Month </th>
                        <th> BS/Rate </th>
                        <th>Hr/WrD </th>
                        <th> OT/Amt</th>
                        <th> (Food+Bonus+Other) </th>
                        <th> Total Salary </th>
                        <th>  S.T+Adv+C.D</th>
                        <th> Contri<br>ID Renew</th>
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

                    @foreach($salary_records as $salary)

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
                        <td class="td__gross_salary"> {{ date('F', mktime(0, 0, 0, $salary->slh_month, 10)); }}, {{ $salary->slh_year }} </td>
                        <td class="td__center"> {{ $salary->basic_amount }}/{{ $salary->hourly_rent }}</td>
                        <td class="td__sponser"> {{ $salary->slh_total_hours }}/{{ $salary->slh_total_working_days }} </td>
                        <td class="td__center"> {{ $salary->slh_total_overtime }}/{{ $salary->slh_overtime_amount }}</td>
                        <td class="td__center"> {{round($salary->food_allowance) }}   + {{$salary->slh_bonus_amount}} + {{ $salary->otherAmount }} </td>
                        <td class="td__gross_salary">  {{
                          $salary->slh_all_include_amount > 0 ?  round( $salary->slh_all_include_amount) : round( $salary->slh_total_salary + $salary->slh_cpf_contribution + $salary->slh_iqama_advance + $salary->slh_saudi_tax + $salary->slh_other_advance)
                            }} 
                        </td>
                        <td class="td__center"> {{ round($salary->slh_saudi_tax) }} + {{round($salary->slh_other_advance) }} {{ $salary->slh_food_deduction > 0 ? "+".$salary->slh_food_deduction:'' }}</td>
                        <td class="td__center">{{ round($salary->slh_cpf_contribution)}}/{{ round($salary->slh_iqama_advance) }} </td>
                        <td class="td__gross_salary"><u> {{ round($salary->slh_total_salary) }}</u> </td>
                        <td class="td__project"> {{ Str::limit($salary->project->proj_name,40) }}<br></td>
                        <td class="td__gross_salary">
                            <div class="box__signature"> {{ $salary->Status == 0 ? 'Unpaid': ( $salary->slh_paid_method == null ? "Paid by Cash" : "Paid by Bank") }} </div>
                        </td>

                    </tr>
                    @endforeach
                    <tr>
                        <td class="td__center"></td>
                        <td class="td__gross_salary">Total </td>
                        <td class="td__center"> </td>
                        <td class="td__gross_salary">{{ $totalHours }} </td>
                        <td class="td__center"></td>
                        <td class="td__center"> </td>
                        <td class="td__center"> </td>
                        <td class="td__gross_salary">{{$totalSaudiTax}}</td>
                        <td class="td__gross_salary">{{ $totalContribution }}/{{ $totalIqamaRenewal }}</td>
                        <td class="td__gross_salary"> {{ $totalSalaryAmount }} </td>
                        <td class="td__center"> </td>
                        <td class="td__center"> </td>
                    </tr>

                </tbody>
            </table>
        </section>
        <!-- Bonus Salary Records -->
        <section class="table__part">

           @if($bonus_records->count() >0)
            <table id="employeeinfo">
                <thead>
                    <tr> <td colspan="12" style="text-align:center; font-size:14px; "> Bonus  Details of {{ $employee->employee_id}}, {{$employee->employee_name}} </td></tr>
                    <tr>
                        <th>SL No </th>
                        <th>Month</th>
                        <th> Year </th>
                        <th> Bonus Type </th>
                        <th> Date </th>
                        <th> Amount </th>
                        <th> Remarks </th>
                    </tr>
                </thead>
                <tbody>
                   

                    @foreach($bonus_records as $arecord) 

                    <tr>
                        <td class="td__center">{{ $loop->iteration }}</td>
                        <td class="td__gross_salary"> {{ date('F', mktime(0, 0, 0, $arecord->month, 10)); }}  </td>
                        <td class="td__gross_salary"> {{ $arecord->year }} </td>
                        <td class="td__center"> {{ $arecord->bonus_type->name}}</td>
                        <td class="td__center"> {{ $arecord->updated_at }} </td>
                        <td class="td__center"> {{ $arecord->amount }} </td>
                        <td class="td__center"> {{ $arecord->remarks }} </td>
                    </tr>
                    @endforeach
                  

                </tbody>
            </table>
            @endif
        </section>

        <!-- Iqama Renewal Details part -->
        <section>
            <br>
        </section>
        <section>
            <table id="employeeinfo">
                <thead>
                    <tr> <td colspan="10" style="text-align:center; font-size:14px; "> Iqama Renewal Expense Details of {{ $employee->employee_id}},{{$employee->employee_name}} </td></tr>

                    <tr>
                        <th class="td__center">S.N</th>
                        <th class="td__center">Date</th>
                        <th class="td__center">Iqama Duration</th>
                        <th class="td__center">Jawat Fee</th>
                        <th class="td__center">Amal Fee</th>
                        <th class="td__center">BD Amount</th>
                        <th class="td__center">Medical Insurance</th>
                        <th class="td__center">Jawat Fee Penalty</th>
                        <th class="td__center">Others1</th>
                        <th class="td__center">Total</th>
                        <th class="td__center">Expense Paid By</th>
                        <th class="td__center">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $counter = 1;
                    $totalIqamaExpence = 0;
                    @endphp

                    @foreach($iqamaExpenseAllRecords as $item)
                    @php
                        $totalIqamaExpence += $item->jawazat_fee + $item->maktab_alamal_fee +$item->bd_amount +$item->medical_insurance + $item->others_fee + $item->Cost6 + $item->jawazat_penalty;
                    @endphp
                    <tr>
                        <td class="td__center">{{$counter++}}</td>
                        <td class="td__center"> {{ $item->renewal_date }} </td>
                        <td class="td__center"> {{ $item->duration }} Month </td>
                        <td class="td__center"> {{ $item->jawazat_fee }} </td>
                        <td class="td__center"> {{ $item->maktab_alamal_fee }} </td>
                        <td class="td__center"> {{ $item->bd_amount }} </td>
                        <td class="td__center"> {{ $item->medical_insurance }} </td>
                        <td class="td__center"> {{ $item->jawazat_penalty }} </td>
                        <td class="td__center"> {{ $item->others_fee }} </td>
                        <td class="td__center"> {{ $item->total_amount }} </td>
                        <td class="td__center"> {{ $item->expense_paid_by == 2 ? 'Company' : 'Self' }} </td>
                        <td class="td__center"> {{ $item->remarks }} </td>

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
                        <td class="td__gross_salary" >
                            <!--{{  $closed_fiscal_record->end_year != null ? $closed_fiscal_record->end_year : 'PREVIOUS' }} YEAR-->
                             CLOSING BALANCE:</td>
                        <td class="td__gross_salary">
                        {{ $closed_fiscal_record->balance_amount  }} 
                     </td>   
                     <td class="td__gross_salary" colspan="3">
                        
                     </td>                      
                         
                    </tr>
                    <tr>
                        <td class="td__gross_salary"> CASH RECEIVED</td>
                        <td class="td__gross_salary"> {{$cashReceiveTotalPaidAmount }}</td>
                        <td class="td__gross_salary">UNPAID SALARY</td>
                        <td class="td__gross_salary">{{ $totalUnPaidSalaryAmount }} </td>
                    </tr>
                    <tr>

                        <td class="td__gross_salary">IQAMA DEDUCTION FROM SALARY </td>
                        <td class="td__gross_salary">{{ $toal_iqama_expense_deduction_from_salary }} </td>
                        <td class="td__gross_salary"  >TOTAL ADVANCE </td>
                        <td class="td__gross_salary"  > {{ $otherAdvanceTotalAmount}} </td>
                       
                    </tr>
                    <tr>
                        <td class="td__gross_salary">TOTAL RECEIVED</td>
                        <td class="td__gross_salary">{{ $toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount }} </td>
                        <td class="td__gross_salary"> ADVANCE RECEIVED</td>
                        <td class="td__gross_salary"> {{ $total_other_advace_deduction_from_salary}} </td>
                    </tr>
                   
                    <tr>
                        <td class="td__gross_salary">TOTAL EXPENSE OF IQAMA</td>
                        <td class="td__gross_salary">{{ $iqamaRenewalTotalExpence + $closed_fiscal_record->balance_amount }} </td>
                        <td class="td__gross_salary">IQAMA EXPENSE BALANCE</td>
                        <td class="td__gross_salary"> {{ ($iqamaRenewalTotalExpence+$closed_fiscal_record->balance_amount) -   $toal_iqama_expense_deduction_from_salary + $cashReceiveTotalPaidAmount}} </td>
                        
                    </tr>

                </tbody>
            </table>
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

    $(window).on('load', function() { // makes sure the whole site is loaded 
        $('#status').fadeOut(); // will first fade out the loading animation 
        $('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website. 
        $('body').delay(350).css({
            'overflow': 'visible'
        });
    })
    
       function showEmployeActivityRecord(){
        var emp_id = document.getElementById('emp_id').innerHTML;       
        
        var url = "{{ route('anemployee.activities.details.report', ':parameter') }}";
        url = url.replace(':parameter', emp_id);
        window.open(url, '_blank');

   }
</script>

</html>