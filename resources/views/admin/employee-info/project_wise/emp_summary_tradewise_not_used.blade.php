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
                max-width: 90%;
                margin: 0 auto 10px;
               

            }
            
            @page {
                size: A4 portrait;
                margin: 15mm 0mm 10mm 20mm;
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
            font-size:12px;
        }
        .title_left__part{
            text-align: left;
            font-size:12px;
        }
        .address {
            font-size:12px;
        }
        .print__part{
            text-align: right;
            font-size:10px;
            padding-right:20px;
        }

        /* table part */
        .table__part {
            display: flex;
        }

        table {
            width: 95%;
            padding: 0px;
            align:center
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
            text-transform:capitalize;
            font-family: "Times New Roman", Times, serif;
        }

        table td {
            text-align: center;
            font-size: 11px;
            border: 1px ridge gray; 
            padding: 5px;
            margin: 0px; 
            text-transform:capitalize;
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
        .td__total_emp {
            font-size: 11px;
            color: black;
            font-weight: 400;
            text-align: center;
            text-transform:capitalize
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

        <p> Project: {!! nl2br($project_name) !!}  </p><br>      

        <section class="table__part">
            <table>
                <thead>
                <tr>
                  <th > <span>S.N</span> </th>
                  <th> <span>Trade Name</span> </th>
                  <th> <span>Number of Total Employees</span> </th>     
                </tr>
                </thead>
                <tbody>
                  @php  $total_emp = 0; @endphp
                @foreach ($list as $emp)
                    
                   @php $total_emp += $emp->total_emp @endphp
                 
                <tr >
                  <td class="td__s_n"> {{ $loop->iteration }}</td>
                  <td class="td__title_name"> {{ Str::upper(Str::limit($emp->catg_name,110)) }}  </td>
                  <td class="td__total_emp"> {{ $emp->total_emp }} </td>
                 </tr>
                @endforeach
                <tr >
                  <td colspan ="2" class="td__total_emp"> <b> Total </b></td>                  
                  <td class="td__total_emp"><b> {{ $total_emp }}</b></td>
                 </tr>
              </tbody>

                <p style="page-break-after: always;"></p>
                </tbody>
            </table>
        </section>
        <!-- ---------- -->
        <section>
            {{-- Officer Signature --}}
            <div class="row" style="padding-top: 50px;">
                <div class="officer-signature" style="display: flex; justify-content:space-between">
                    <p>Accountant</p>
                    <p>Verified</p>
                    <p>General Manager</p>
                </div>
            </div>
            {{-- Officer Signature --}}
        </section>

    
    </div>
</body>

</html>