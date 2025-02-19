<!DOCTYPE html>
<html lang="en">

<head>
    <title>Emp Report(Desig Head)</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- style -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
        }


        @media print {
            .container {
                max-width: 98%;
                margin: 5 auto ;


            }

            @page {
                size: A4 landscape;
                margin: 15mm 5mm 10mm 15mm;
                /* top, right,bottom, left */

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
            width: 99%;
            margin: 10px auto;
        }

        .header__part {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title__part {
            text-align: center;
            font-size: 12px;
        }

        .title_left__part {
            text-align: left;
            font-size: 12px;
        }

        .address {
            font-size: 12px;
        }

        .print__part {
            text-align: right;
            font-size: 10px;
            padding-right: 20px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 98%;
            padding: 0px;
            align: center
        }

        table,
        tr {
            border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 12px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 11px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform: capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 11px;
            color: black;
            text-align: center;

        }

        .td__title_name {
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }
        .td__emplyoee_info{
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;
            padding-left: 10px;
        }
        .td__project_title{
            font-size: 11px;
            color: black;
            text-align: left;
            font-weight: 100;


        }
         .td_common{
            font-size: 11px;
            color: black;
            text-align: center;
            font-weight: 100;
         }

        .td__total_emp {
            font-size: 11px;
            color: black;
            font-weight: 400;
            text-align: center;
            text-transform: capitalize
        }
    </style>
    <!-- style -->
</head>

<body>
    <div class="main__wrap">
        <!-- header part-->
        <section class="header__part">
            <!-- date -->
            <div class="title_left__part">
                <p> <strong> Employees Increments Details Reports  </strong></p>

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
                <p> <strong>Print Date:</strong> {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->



        <section class="table__part">
            <table>
                <thead>
                    <tr>
                        <th > <span>S.N</span> </th>
                        <th> <span>Emp. ID</span> </th>
                        <th> <span>Employee Name</span> </th>
                        <th> <span>Iqama</span> </th>
                        <th> <span>Trade</span> </th>
                        <th> <span>Natio.</span> </th>
                        <th> <span>Project Name</span> </th>
                        <th> <span>Joining</span> </th>
                        <th> <span>J.Salary</span> </th>
                        <th> <span>C.Salary</span> </th>
                        <th> <span>Increment Details</span> </th>

                     </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $emp)
                    <tr >
                        <td class="td__s_n"> {{ $loop->iteration }}</td>
                        <td class="td_common">  {{ $emp->employee_id }}</td>
                        <td class="td__emplyoee_info"> {{ Str::lower($emp->employee_name) }}</td>
                        <td class="td_common"> {{ $emp->akama_no }}</td>
                        <td class="td_common"> {{ Str::limit($emp->catg_name,20) }} </td>
                        <td class="td_common"> {{ Str::limit($emp->country_name,3) }}</td>
                        <td class="td__project_title"> {{ Str::limit($emp->proj_name,40) }}</td>
                        <td class="td_common"> {{ $emp->joining_date }}</td>
                        <td class="td_common"> {{ $emp->joining_salary }}</td>

                        <td class="td_common"> {{ $emp->hourly_employee == 1 ?  $emp->hourly_rent :
                        ($emp->basic_amount+ $emp->house_rent +$emp->food_allowance+ $emp->mobile_allowance
                        + $emp->medical_allowance+ $emp->local_travel_allowance+ $emp->conveyance_allowance+ $emp->others1 )}}</td>
                        <td class="td_common">
                            @php
                                $pr_info = '';
                            @endphp
                            @foreach($emp->promotion_records as $pr )

                            {{ $pr->total_amount}}/{{$pr->hourly_rent}}-{{ $pr->month}},{{ $pr->year}} <br>
                            @endforeach


                        </td>
                       </tr>
                    @endforeach
                </tbody>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
        <br><br><br>
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 20px; padding-right:30px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:11px">
                     <p>Prepared By<br/><br/><br/> {{$login_user_name}} </p>
                    <p>Checked By</p>
                    <p>Verified By</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>
    </div>
</body>

</html>
