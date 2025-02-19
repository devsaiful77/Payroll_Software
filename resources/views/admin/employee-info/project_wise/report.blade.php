<!DOCTYPE html>
<html lang="en">

<head>

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
                margin: 0 auto 10px;


            }

            @page {
                size: A4 landscape;
                margin: 15mm 0mm 10mm 0mm;
                /* top, right,bottom, left */

            }
            .print__button {
                  visibility: hidden;
               }

        }


        .main__wrap {
            width: 95%;
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
            font-size:12px;
        }
        .title_left__part{
            text-align: left;
            font-size:10px;
        }
        .address {
            font-size:10px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 100%;
            padding: 0px;
        }

        table,
        tr {
          border: 1px ridge gray;
            border-collapse: collapse;
            margin: 0px;
            padding: 0px;
        }

        table th {
            font-size: 10px;
            border: 1px ridge gray;
            font-weight: bold;
            padding: 5px;
            color: #000;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 10px;
            border: 1px ridge gray;
            padding: 5px;
            margin: 0px;
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
            /* Top,Right,Bottom,left */
        }


        .td__s_n {
            font-size: 10px;
            color: black;
            text-align: center;

        }

        .td__employee_id {
            font-size: 10px;
            color: red;
            text-align: center;
            font-weight: 300;
            padding: 0px;
        }
        .td__emplyoee_info {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: left;
            text-transform:capitalize
        }

        .td__iqama {
            font-size:10px;
            color: black;
            font-weight: 100;
            text-align: center;
        }
        .td__country{
            font-size:10px;
            color: black;
            font-weight: 100;
            text-align: center;
        }

        .td__emp_trade {
            font-size: 10px;
            color: black;
            font-weight: 100;
            text-align: center;
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
                <p> <strong> Employee Details of {{$project}}</strong></p>
                <p> <strong> Employee Type: {{$report_title}}</strong></p>
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
        <!-- table part -->
        <section class="table__part">
            <table>
                <thead>
                <tr>
                  <th > <span>S.N</span> </th>
                  <th> <span>Empl ID</span> </th>
                  <th> <span>Employee Name</span> </th>
                  <th> <span>Iqama No</span> </th>
                  <th> <span>Nation</span> </th>
                  <th> <span>Project Nmae</span> </th>
                  <th> <span>Trade</span> </th>
                  <th> <span>Sponser</span> </th> 
                  <th> <span>Basic/Rate</span> </th>

                </tr>
                </thead>
                <tbody>
                @foreach ($employee as $emp)
                <tr>
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__employee_id">  {{ $emp->employee_id }}</td>
                  <td class="td__emplyoee_info"> {{ $emp->employee_name }} </td>
                  <td class="td__iqama"> {{ $emp->akama_no }} </td>
                    <!-- {{ $emp->akama_expire_date }} -->
                  <td class="td__country"> {{ Str::limit($emp->country->country_name,10) }}</td>
                  <td class="td__emplyoee_info"> {{ $emp->project->proj_name }} </td>
                  <td class="td__emp_trade"> {{ $emp->category->catg_name,10 }} </td>
                  <td class="td__emplyoee_info"> {{ $emp->sponser->spons_name }}</td>
                  <td> <span>{{  $emp->hourly_rent == 1 ? "Hourly": "Basic" }}</span> </td> 

                 </tr>
                @endforeach
              </tbody>

                <p style="page-break-after: always;"></p>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->

    <section>
        {{-- Officer Signature --}}
        <div class="row" style="padding-top: 20px;">
            <div class="officer-signature" style="display: flex; justify-content:space-between; font-size:9px">
                <p>Prepared By<br/><br/><br></p>
                <p>Checked By</p>
                <p>Verified By</p>
            </div>
        </div>
        {{-- Officer Signature --}}
    </section>
    </div>
</body>

</html>
