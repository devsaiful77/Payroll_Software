<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>  
        Expenses Report
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
                margin: 0 auto 5px;

            }


            @page {
                size: A4 portrait;
                margin: 10mm 5mm 10mm 5mm;
                /* top, right,bottom, left */

            }

            a[href]:after {
                content: none !important;
            }
            .print__button {
                visibility: hidden;
            }
        }


        .main__wrap {
            width: 98%;
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
        }

        .report_type__part {
            font-size: 11px;
            padding-left: 10px;
        }

        .print__part {
            font-size: 11px;
            padding-right: 10px;
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
            font-size: 11px;
            font-weight: bold;
            padding: 5px 2px;
            border: 1px solid gray;
        }

       
 
        td {
            padding: 5px 2px;
            /* Top,Right,Bottom,left */
            border: 1px solid gray;
        }
 
        .td__serial_no {
            font-size: 11px;
            color: black;
            font-weight: 100;
            text-align: center;

        }
 
        .td__project {
            font-size: 11px;
            padding-left: 5px;
            color: black;
            font-weight: 100;
            text-align: left;

        }
 
        .td__gross_amount {
            font-size: 11px;
            color: navy;
            font-weight: 900;
            text-align: right;
            padding-right:5px;
        }
    </style>
    <!-- style -->
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
            <!-- date -->
            <div class="report_type__part">
                <p> Expenses From  {{ $from_date }} To {{$to_date}} </p>
            </div>
            <!-- Middle title -->
            <div class="title__part">
                <h4>{{$company->comp_name_en}} <small>{{$company->comp_name_arb}} </small> </h4>
                <address class="address">
                    {{$company->comp_address}}
                </address>
            </div>
            <!-- print button -->
            <div class="print__part">
                <p> Print Date: {{ Carbon\Carbon::now()->format('d/m/Y') }} </p>
                <button type="" onclick="window.print()" class="print__button">Print</button>
            </div>
        </section>
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                    <tr>
                                <th>  S.NO </th>
                                <th>  Sub Company  </th>
                                <th> Project </th>
                                <th> Expense Head  </th>
                                <th>  Expense By  </th>
                                <th> Expensed Date  </th>
                                <th>  (Gross+VAT)  </th>
                                <th> Total Amount    </th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($expend_report as $report)
                        <tr>
                                 <td class="td__serial_no"> {{ $loop->iteration }} </td>
                                <td class="td__project">{{ $report->subCompany->sb_comp_name }} </td>
                                <td class="td__project"> {{ $report->project->proj_name }} </td>
                                <td class="td__project"> {{ $report->expenseHead->cost_type_name }}</td>
                                <td class="td__project"> {{ $report->employee->employee_name }} </td>
                                <td  class="td__serial_no"> {{ $report->voucher_date }} </td>
                                <td class="td__gross_amount"> {{ $report->gross_amount }}+{{ $report->vat }}  </td>
                                <td class="td__gross_amount"> {{ $report->total_amount }} </td>
                        </tr>
                        <p style="page-break-after: always;"></p>
                         
                  
                    @endforeach
 
                </tbody>
            </table>
        </section>
        <!-- ---------- -->


        </section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 50px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between">
                <p>Accountant</p>
                <p>Verified</p>
                <p>General Manager</p>
            </div>
        </div>
        {{-- Officer Signature --}}
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