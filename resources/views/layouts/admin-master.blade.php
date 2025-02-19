<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta content="" name="description" />
  <meta content="" name="author" />

  <title>Payroll @yield('title') </title>
   <link href="{{asset('contents/admin')}}/assets/css/salary-bootstrap.min.css" rel="stylesheet" type="text/css" />

  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> -->
  <link href="{{asset('contents/admin')}}/assets/css/datatables.min.css" rel="stylesheet" type="text/css" />
  <link href="{{asset('contents/common')}}/css/magnific-popup.css" rel="stylesheet" type="text/css" />
  <link href="{{asset('contents/admin')}}/assets/css/icons.css" rel="stylesheet" type="text/css" />
  <link href="{{asset('contents/admin')}}/assets/css/all.min.css" rel="stylesheet" type="text/css" />
  <link href="{{asset('contents/admin')}}/plugins/summernote/summernote-bs4.css" rel="stylesheet" type="text/css" />
  <link href="{{asset('contents/admin')}}/assets/css/moltran.css" rel="stylesheet" type="text/css" />
  <link href="{{asset('contents/admin')}}/assets/css/chosen.css" rel="stylesheet" type="text/css" />
  <link href="{{asset('contents/admin')}}/assets/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
  <link href="{{asset('contents/admin')}}/assets/css/style.css" rel="stylesheet" type="text/css" />
  <script src="{{asset('contents/admin')}}/assets/js/modernizr.min.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/jquery.min.js"></script>

  {{-- <script src="{{asset('contents/admin')}}/assets/tags/bootstrap-tagsinput.css"></script>
  <script src="{{asset('contents/admin')}}/assets/tags/bootstrap-tagsinput.js"></script> --}}

  <script src="{{asset('contents/admin')}}/assets/js/font_end_validation/jquery.validate.min.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/font_end_validation/salary-details.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/font_end_validation/employee-info.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/font_end_validation/division.js"></script>

  <!-- vite bundler -->
  @vite('resources/js/app.js')
  <!-- vite bundler -->
  
  @yield('internal-css')

</head>



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
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<!-- Preloader -->
<div id="preloader">
  <div id="status">&nbsp;</div>
</div>

<script>
  $(window).on('load', function() {
    $('#status').fadeOut();
    $('#preloader').delay(350).fadeOut('slow');
    $('body').delay(350).css({
      'overflow': 'visible'
    });
  })
</script>


<body class="fixed-left">

 {{-- developer information in footer --}}
<div id="wrapper">
    @include('layouts.include.topbar')
    <div class="left side-menu">
      <div class="sidebar-inner slimscrollleft">

        @include('layouts.include.admin-sidebar');
        <div class="clearfix"></div>
      </div>
    </div>
    <div class="content-page">
      <div class="content">
        <div class="container-fluid">
          @yield('content')
        </div>
      </div>
      <footer class="footer">
        Developed by <a target="_blank" href="#">ABC </a>
      </footer>
    </div>
</div>

  <script>
    // need for imported js file
    var resizefunc = [];
  </script>

  <script src="{{asset('contents/admin')}}/assets/js/bootstrap.bundle.min.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/datatables.min.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/detect.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/fastclick.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/jquery.slimscroll.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/jquery.blockUI.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/jquery-validator.min.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/form-validation.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/waves.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/wow.min.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/jquery.nicescroll.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/jquery.scrollTo.min.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/bootstrap-datepicker.js"></script>
  <!-- Sweet Alert  -->
  <script src="{{asset('contents/common')}}/js/sweetalert2.min.js"></script>

  <!-- Sweet Alert 2 -->
  <script src="{{asset('contents/admin')}}/assets/js/sweetalert/sweetalert.min.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/sweetalert/code.js"></script>
  <!-- end Sweet Alert -->
  <script src="{{asset('contents/admin')}}/plugins/moment/moment.min.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/waypoints/lib/jquery.waypoints.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/counterup/jquery.counterup.min.js"></script>
  <script src="{{asset('contents/common')}}/js/magnific.min.js"></script>

  {{-- <script src="{{asset('contents/admin')}}/plugins/flot-chart/jquery.flot.min.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/flot-chart/jquery.flot.time.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/flot-chart/jquery.flot.tooltip.min.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/flot-chart/jquery.flot.resize.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/flot-chart/jquery.flot.pie.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/flot-chart/jquery.flot.selection.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/flot-chart/jquery.flot.stack.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/flot-chart/jquery.flot.crosshair.js"></script> --}}

  {{-- <script src="{{asset('contents/admin')}}/assets/pages/jquery.todo.js"></script> --}}
  {{-- <script src="{{asset('contents/admin')}}/assets/pages/jquery.dashboard.js"></script> --}}
  <script src="{{asset('contents/admin')}}/assets/js/jquery.app.js"></script>
  <script src="{{asset('contents/admin')}}/plugins/summernote/summernote-bs4.js"></script>
  {{-- <script src="{{asset('contents/admin')}}/assets/js/chosen.jquery.js"></script> --}}
  <script src="{{asset('contents/admin')}}/assets/js/jquery.printPage.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/axios.min.js"></script>


  @yield('script')
  <script src="{{asset('contents/admin')}}/assets/js/custom.js"></script>
  <script type="text/javascript" src="{{ asset('contents/admin') }}/assets/js/ajax/typehead.min.js"></script>


  <script type="text/javascript">
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': document.getElementsByName("_token")[0].value

      }
    })

    function empSearch() {

        let empId = $("#emp_id_search").val();
        if (empId.length <= 3) {
          return;
        }
          // employee id minimum length 3 but searching validation = 4
          $.ajax({
            type: 'POST',
            url: "{{ url('/admin/find/employee-id') }}",
            data: {
              empId: empId
            },
            beforeSend:()=>{
              $("body").addClass("loading");
            },
            complete:()=>{
              $("body").removeClass("loading");
            },
            success: function(result) {
              $("#showEmpId").html(result);
            },
            error:function(response){
                alert(response.message);
            }
          });

        if (empId.length < 1) $("#showEmpId").html("");
    }

    function showResult() {
      $("#showEmpId").slideDown();
    }

    function hideResult() {
      $("#showEmpId").slideUp();
    }

    /* ================= Metarial & Tools Add to Cart ================= */

    function addToCart() {

      var itype_id = $('#itype_id option:selected').val();
      var itype_name = $('#itype_id option:selected').text();
      var icatg_id = $('#icatg_id option:selected').val();
      var icatg_name = $('#icatg_id option:selected').text();
      var iscatg_id = $('#iscatg_id option:selected').val();
      var iscatg_name = $('#iscatg_id option:selected').text();
      var quantity = $('#quantity').val();
      var stock_amount = $('#stock_amount').val();
      /* ajax request execute */
      $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
          itype_id: itype_id,
          icatg_id: icatg_id,
          iscatg_id: iscatg_id,
          quantity: quantity,
          stock_amount: stock_amount,
          itype_name: itype_name,
          icatg_name: icatg_name,
          iscatg_name: iscatg_name,
        },
        url: "/admin/metarial-tools/cart/store",
        success: function(data) {
          getMetarialList();
          //  start message
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          })
          if ($.isEmptyObject(data.error)) {
            Toast.fire({
              type: 'success',
              title: data.success
            })
          } else {
            Toast.fire({
              type: 'error',
              title: data.error
            })
          }
          //  end message
        }
      });
    }

    /* ================= Metarial & Tools View to List ================= */
    function getMetarialList() {

      $.ajax({
        type: 'GET',
        url: '/admin/metarial-tools/list/view',
        dataType: 'json',
        success: function(response) {
          $('span[id="cartSubTotal"]').text(response.cartTotal);
          $('input[id="net_amount"]').val(response.cartTotal);
          $('input[id="net_amount_hidden"]').val(response.cartTotal);
          var rows = "";
          $.each(response.carts, function(key, value) {
            rows += `
                <tr>
                  <td>${value.options.itype_id}, ${value.options.itype_name}</td>
                  <td>${value.options.icatg_id}, ${value.options.icatg_name}</td>
                  <td>${value.name}, ${value.options.iscatg_name}</td>
                  <td>( ${value.qty} X ${value.price} )</td>
                  <td><a style="cursor:pointer"  type="submit" title="delete" id="${value.rowId}" onclick="removeToCart(this.id)"><i class="fa fa-trash fa-lg delete_icon"></i></a></td>
                  <td>${value.subtotal}</td>
                </tr>

                `
          });
          $('#metarial_tools_list_view').html(rows);
        }
      });
    }
    getMetarialList();

    /* ================= remove type ================= */
    function removeToCart(rowId) {
      $.ajax({
        type: 'GET',
        url: '/admin/metarial-tools/single-list/remove/' + rowId,
        dataType: 'json',
        success: function(data) {
          getMetarialList();
          //  start message
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          })
          if ($.isEmptyObject(data.error)) {
            Toast.fire({
              type: 'success',
              title: data.success
            })
          } else {
            Toast.fire({
              type: 'error',
              title: data.error
            })
          }
          //  end message
        }
      });
    }

  </script>

  {{-- Address script now use  --}}
  <script type="text/javascript">
    $(document).ready(function() {

      $('select[name="country_id"]').on('change', function() {
        var country_id = $(this).val();

        if (country_id) {
          $.ajax({
            url: "{{  url('/admin/division/ajax') }}/" + country_id,
            type: "GET",
            dataType: "json",
            success: function(data) {
              if (data == "") {
                // Division
                $('select[name="division_id"]').empty();
                $('select[name="division_id"]').append('<option value="">Data Not Found! </option>');
                // District
                $('select[name="district_id"]').empty();
                $('select[name="district_id"]').append('<option value=""> Data Not Found! </option>');

              } else {
                // Division
                $('select[name="division_id"]').empty();
                $('select[name="division_id"]').append('<option value="">Select Division</option>');
                // District
                $('select[name="district_id"]').empty();
                $('select[name="district_id"]').append('<option value=""> Select District </option>');
                // Division List
                $.each(data, function(key, value) {
                  $('select[name="division_id"]').append('<option value="' + value.division_id + '">' + value.division_name + '</option>');
                });
              }

            },

          });
        } else {

        }
      });
      /* call district */
      $('select[name="division_id"]').on('change', function() {
        var division_id = $(this).val();
        if (division_id) {
          $.ajax({
            url: "{{  url('/admin/district/ajax') }}/" + division_id,
            type: "GET",
            dataType: "json",
            success: function(data) {
              $('select[name="district_id"]').empty();
              if (data == "") {
                // District
                $('select[name="district_id"]').empty();
                $('select[name="district_id"]').append('<option value=""> Data Not Found! </option>');
              } else {
                // District
                $('select[name="district_id"]').empty();
                $('select[name="district_id"]').append('<option value=""> Select District </option>');
                // District List
                $.each(data, function(key, value) {
                  $('select[name="district_id"]').append('<option value="' + value.district_id + '">' + value.district_name + '</option>');
                });
              }





            },

          });
        } else {

        }
      });
      /* call employee category */
      $('select[name="emp_type_id"]').on('change', function() {
        var emp_type_id = $(this).val();

        if (emp_type_id == 1) {
          $("#hourlyEmployee").removeClass(' d-none').addClass(' d-block');
        } else {
          $("#hourlyEmployee").addClass('d-none').removeClass('d-block');
        }

        if (emp_type_id) {
          $.ajax({
            url: "{{  url('/admin/employee/category/ajax') }}/" + emp_type_id,
            type: "GET",
            dataType: "json",
            success: function(data) {
              $('select[name="designation_id"]').empty();
              $.each(data, function(key, value) {
                $('select[name="designation_id"]').append('<option value="' + value.catg_id + '">' + value.catg_name + '</option>');
              });

            },

          });
        } else {
          alert('danger');
        }
      });

    });





  </script>



  <!-- All Required js file -->
  <script src="{{asset('contents/admin')}}/assets/js/accounts/chart_of_accounts.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/accounts/journal_info.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/transportation/driver_ajax.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/employee_information/status.js"></script>
  <script src="{{asset('contents/admin')}}/assets/js/salary/salary_process.js"></script>

  {{-- Manage User --}}
  {{-- @yield('manageUser') --}}
  {{-- Manage User --}}
</body>

</html>
