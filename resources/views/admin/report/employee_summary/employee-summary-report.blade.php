<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Generate</title>
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('contents/admin') }}/assets/css/salary-style.css">
    <style media="screen">
        a.print-button{
          text-decoration: none;
          background: teal;
          color: #fff;
          padding: 5px 10px;
        }
        /* p.toEndDate{} */
        p.toEndDate strong{ font-size: 14px }
        p.toEndDate span {
          font-size: 14px;
          font-weight: 600;
          margin-left: 2px;
        }
        div.officer-signature{
          display: flex;
          justify-content: space-between;
        }
        /* Employee Summary Table */
        table.employee-summary{
          border: 1px solid #ddd;
        }
        table.employee-summary thead{
          border: 1px solid #2B4049;
          background: #2B4049;
        }
        table.employee-summary thead th{
          color: #fff;
          padding-left: 20px;
        }
        table.employee-summary thead th:first-child{
          width: 56px;
          text-align: right;
        }
        table.employee-summary tbody td{
          padding-left: 20px;
        }
        span{
          font-size: 10px !important;
          font-weight: 400 !important;
        }
        span.total{
          display: block;
          text-align: right;
        }
        .p-title{
          text-align: right !important;
          font-weight: 600;
        }
        .p-amount{
          text-align: left !important;
        }
    </style>
</head>
<body>
  <section class="salary">
      <div class="container">
          <!-- salary header -->
          <div class="row align-center">
              <div class="col-md-6">
                  <div class="salary__header">

                  </div>
              </div>
              <div class="col-md-6"></div>
          </div>
          <!-- salary bottom header -->
          <div class="salary__header-bottom">
              <div class="row">
                  <div class="col-md-3">
                    <div class="project_info" style="margin-left:0">
                        <p class="project_name">  <strong>Salary Summary Report:</strong> {{ Carbon\Carbon::now()->format('Y') }} </p>
                    </div>
                  </div>


                  <div class="col-md-6">
                    <div class="company_information" style="text-align:center">
                      <h4>{{$company->comp_name_en}}  <small>{{$company->comp_name_arb}} </small> </h4>
                      <address class="address">
                           {{$company->comp_address}}
                      </address>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="salary__download" style="text-align:right; display:flex-; justify-content: right; align-items:center">
                      <p> <strong>Print Date</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                      <a href="#" class="print-button" onclick="window.print()">PDF Or Pirnt</a>
                    </div>
                  </div>
              </div>
          </div>
 

          <div class="salary__table-wrap">
              <div class="row">
                  <div class="col-md-12">
                      <table class="table table-responsive salary__table">
                          <thead>
                              <!-- first-head-row -->
                              <tr class="first-head-row">
                                  <th> <span>Empl ID</span> </th>
                                  <th> <span>Employee Name</span> </th>
                                  <th> <span>Iqama No</span> </th>
                                  <th> <span>Sponser</span> </th>

                                  <th> <span>Nation</span> </th>
                                  <th> <span>Type</span> </th>
                                  <th> <span>Trade</span> </th>


                                  <th> <span></span> </th>
                                  <th> <span></span> </th>

                                  <th> <span></span> </th>
                                  <th> <span></span> </th>

                                  <th> <span></span> </th>
                                  <th></th>
                                  <th> <span>Signature</span> </th>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                              </tr>
                              <tr class="second-head-row">
                                  <th></th>
                                  <th colspan="2"> <span class="currency"> Salary Month </span> </th> </th>
                                  <th> <span class="currency"> BS/(Rate) </span> </th>
                                  <th> <span class="currency"> Ovh(Amt) </span> </th>
                                  <th> <span class="currency">Hr/WrD</span> </th>
                                  <th> <span class="currency"> Total(FA + Allow) </span> </th>
                                  <th> <span class="currency"> (Adv1 + Adv2) </span> </th>
                                  <th> <span class="currency"> (Visa Amt) </span> </th>
                                  <th> <span class="currency"> (Contr)/(Insur) </span> </th>
                                  <th> <span class="currency"> (Iqama Renewal) </span> </th>
                                  <th> <span class="currency"> Grow Salary </span> </th>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                              </tr>
                          </thead>
                          <tbody>
                            <tr class="salary-row-parent">
                              <!-- first tr -->
                              <tr class="first-row">
                                  <td> <span>{{ $employee->employee_id }}</span> </td>
                                  <td> <span> {{ $employee->employee_name }} </span> </td>
                                  <td colspan="1"> <span> {{ $employee->akama_no }} </span> </td>
                                  <td colspan="1"> <span>{{ $employee->sponser->spons_name }}</span> </td>
                                  <td colspan="1"> <span>{{ $employee->country->country_name }}</span> </td>
                                  <td colspan="1"> <span>{{ $employee->employeeType->name }}</span> </td>
                                  <td colspan="1"> <span>{{ $employee->category->catg_name }}</span> </td>

                                  <td> <span>Salary Status</span> </td>
                                  <td> <span></span> </td>
                                  <td> <span></span> </td>
                                  <td></td>
                                  <td></td>
                                  <td colspan="5" rowspan="2" class="signature_field"> <span class="signature">  </span> </td>
                              </tr>
                               <!-- second tr -->
                              @foreach($salary as $data)
                                {{-- Calculation --}}
                                @php
                                  $others = $data->house_rent + $data->conveyance_allowance + $data->medical_allowance + $data->others;
                                @endphp
                                {{-- Calculation --}}
                                
                                <tr class="second-row">
                                    <td></td>
                                    <td colspan="2"> <span class="currency_data">{{ date('F', mktime(0, 0, 0, $data->slh_month, 10)); }} / {{ $data->slh_year }}</span> </td>

                                    <td> <span class="currency_data">{{ $data->basic_amount }}/({{ $data->hourly_rent }})</span> </td>
                                    <td> <span class="currency_data">{{ $data->slh_total_overtime }}({{ $data->slh_overtime_amount }})</span> </td>

                                    <td> <span class="currency_data">{{ $data->slh_total_hours }}/{{ $data->slh_total_working_days }}</span> </td>
                                    <td> <span class="currency_data">{{ $data->food_allowance + $others}}({{ $data->food_allowance }} + {{ $others }})</span> </td>
                                    <td> <span class="currency_data">({{ $data->slh_saudi_tax }} + {{ $data->slh_other_advance }})</span> </td>

                                    <td> <span class="currency_data">--</span> </td> <!-- iqama -->
                                    <td> <span class="currency_data">({{ $data->slh_cpf_contribution }})/(-)</span> </td>
                                    <td> <span class="currency_data"> {{ $data->slh_iqama_advance }} </span> </td>
                                    <td> <span class="currency_data"> {{ $data->slh_total_salary }} </span> </td>
                                 </tr>
                              @endforeach
                            </tr>

                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>



                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>



                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>



                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>



                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>



                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>



                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr>


                            <tr>
                              <td colspan="3">
                               <p style="margin-bottom:0"> <strong>Year:</strong> {{ $year }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Work Hours:</strong> {{ $totalHours }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Total Contribution:</strong> {{ $totalContribution }} </p>
                              </td>
                              <td colspan="3">
                                <p style="margin-bottom:0"> <strong>Iqama Renewal:</strong> {{ $totalIqama }} </p>
                              </td>
                              <td colspan="2"> <p> <strong>Total Salary Amount:</strong> </p> </td>
                              <td> <p style="margin-bottom:0"> {{ $totalSalary }} </p> </td>
                            </tr> 

                          </tbody>
                      </table>




                      
                      {{-- Employee Summary Details --}}
                      <div class="row">
                        <div class="col-md-8">
                          <table class="table employee-summary">
                            <thead>
                              <tr>
                                <th scope="col">{{ $employee->employee_id }}</th>
                                <th scope="col">Description</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Remarks</th>
                              </tr>
                            </thead>
                            <tbody>

                              {{-- @foreach($anualFee as $item)
                                <tr>
                                  <td></td>
                                  <td><span>{{ $item->fee_title }}</span></td>
                                </tr>
                              @endforeach --}}

                              {{-- Jawazat Fee
                              Maktab Al Amal Fee
                              BD Amount
                              Medical Inssurance
                              REMAINNIG IQAMA AMOUNT --}}

                              @if($iqamaDetails == NULL)
                              <tr>
                                  <td></td>
                                  <td> <span>Jawazat Fee</span> </td>
                                  <td> <span>Not Assigned</span> </td>
                                  <td> <span>--</span> </td>
                                </tr>
                                <tr>
                                  <td></td>
                                  <td> <span>Maktab Al Amal Fee</span> </td>
                                  <td> <span>Not Assigned</span> </td>
                                  <td> <span>--</span> </td>
                                </tr>
                                <tr>
                                  <td></td>
                                  <td> <span>BD Amount</span> </td>
                                  <td> <span>Not Assigned</span> </td>
                                  <td> <span>--</span> </td>
                                </tr>
                                <tr>
                                  <td></td>
                                  <td> <span>Medical Inssurance</span> </td>
                                  <td> <span>Not Assigned</span> </td>
                                  <td> <span>--</span> </td>
                                </tr>
                                <tr>
                                  <td></td>
                                  <td> <span>Others</span> </td>
                                  <td> <span>Not Assigned</span> </td>
                                  <td> <span>--</span> </td>
                                </tr>
                              @else
                                <tr>
                                  <td></td>
                                  <td>Jawazat Fee</td>
                                  <td> {{ $iqamaDetails->Cost1 }} </td>
                                  <td> <span></span> </td>
                                </tr>
                                <tr>
                                  <td></td>
                                  <td> Maktab Al Amal Fees </td>
                                  <td> {{ $iqamaDetails->Cost2 }} </td>
                                  <td> <span>--</span> </td>
                                </tr>
                                <tr>
                                  <td></td>
                                  <td> BD Amount </td>
                                  <td> {{ $iqamaDetails->Cost3 }} </td>
                                  <td> <span>--</span> </td>
                                </tr>
                                <tr>
                                  <td></td>
                                  <td> Medical Inssurance </td>
                                  <td> {{ $iqamaDetails->Cost4 }} </td>
                                  <td> <span>--</span> </td>
                                </tr>
                                <tr>
                                  <td></td>
                                  <td> Others</td>
                                  <td> {{ $iqamaDetails->Cost5 }} </td>
                                  <td> <span>--</span> </td>
                                </tr>
                              
                              @endif



                              <tr>
                                <td colspan="2"> <span class="total"> TOTAL AMOUNT :</span> </td>
                                <td>  {{ $totalAmount }} </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="col-md-4">
                          <div class="row">
                            <div class="col-md-7">
                              <p class="p-title">SUB TOTAL IQAMA RENEWAL</p>
                            </div>
                            <div class="col-md-1">
                              <p>:</p>
                            </div>
                            <div class="col-md-4">
                              <p class="p-amount">{{ $totalIqama }}</p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-7">
                              <p class="p-title">TO BE DEDUCTED AMOUNT</p>
                            </div>
                            <div class="col-md-1">
                              <p>:</p>
                            </div>
                            <div class="col-md-4">
                              <p class="p-amount">{{ $totalAmount }}</p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-7">
                              <p class="p-title">REMAINING AMOUNT </p>
                            </div>
                            <div class="col-md-1">
                              <p>:</p>
                            </div>
                            <div class="col-md-4">
                              <p class="p-amount">{{ $totalAmount - $totalAdvance }}</p>
                            </div>
                          </div>
                        </div>
                      </div>
                      {{-- Officer Signature --}}
                      <div class="row" style="padding-top: 40px;">
                            <div class="officer-signature" style="display: flex; justify-content:space-between">
                                <p>  <b>  ------------------- <br> {{$login_user}}  </b> <br> Prepared By  </p>
                                <p>  <b>  -------------------- <br>   </b> <br> Verified By  </p>
                                <p>  <b>  ----------------------- <br>   </b> <br> Managing Director  </p>
                            </div>
                      </div>
                   </div>
              </div>
          </div>
      </div>
  </section>


</body>
</html>
