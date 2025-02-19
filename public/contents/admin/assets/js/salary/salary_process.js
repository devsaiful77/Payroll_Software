

function openSalarySlipUploadSection(){

    $('#emp_mobile_bill_store_form_modal').modal('hide');
    $('#emp_mobile_bill_searching_modal').modal('hide');
    $('#searching_result_table_section').removeClass('d-block').addClass('d-none');
    $('#showEmployeeSalarySheetManageSection').removeClass('d-none').addClass("d-block");


}

function openEmployeeMobileBillUploadSection(){
    $('#showEmployeeSalarySheetManageSection').removeClass('d-block').addClass("d-none");
    $('#searching_result_table_section').removeClass('d-block').addClass('d-none');
    $('#emp_mobile_bill_store_form_modal').modal('show');
}

function openEmployeeMobileBillSearchSection(){
    $('#showEmployeeSalarySheetManageSection').removeClass('d-block').addClass("d-none");
    $('#emp_mobile_bill_searching_modal').modal('show');
    $('#searching_result_table_section').removeClass('d-block').addClass('d-none');

}



$("form#employeeMobileBillStoreForm").validate({
    rules: {
        bill_month: {
            required: true,
        },
        bill_year: {
            required: true,
        },
        bill_project_id: {
            required: true,
        },
        bill_paper: {
            required: true,
        },
    },
    messages: {
        bill_month: {
            required: "Please Select Any Mobile Bill Month.",
        },
        bill_year: {
            required: "Please Select Any Mobile Bill Year.",
        },
        bill_project_id: {
            required: "Please Select Any Project Name.",
        },
        bill_paper: {
            required: "Please Add Mobile Bill Slip",
        },
    }
})

$(document).ready(function() {
     // Employee Mobile Bill Information Store Form
    $('#employeeMobileBillStoreForm').submit(function(event) {
        event.preventDefault();
        employeeMobileBillInfoStore();
    });

    function employeeMobileBillInfoStore() {
        const saveBtn = document.getElementById('employee-mobile-bill-submit-button');

        // Clear previous errors
        $('.form-error').remove();
        // Validate the form
        if (!$('#employeeMobileBillStoreForm').valid()) {
            return;
        }
        var formData = new FormData($('#employeeMobileBillStoreForm')[0]);
        saveBtn.disabled = true;
        saveBtn.innerText = 'Uploading...';

        // Call ajax
        $.ajax({
            type: 'POST',
            url: '/admin/employee/mobile-bill/information/manage',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {

                saveBtn.disabled = false;
                saveBtn.innerText = 'SAVE';
                if (res.status != 200) {
                    showSweetAlertMessage('error', res.message);
                    return;
                } else {
                    $('#employeeMobileBillStoreForm').trigger("reset");
                    $('#emp_mobile_bill_store_form_modal').modal('hide');
                    showSweetAlertMessage('success', res.message);
                }
            },
            error: function(xhr) {
                saveBtn.disabled = false;
                saveBtn.innerText = 'SAVE';
                showSweetAlertMessage('error', 'Operation Failed, Please try Again');
            }
        });
    }

    var mobile_bill_search_bttn = document.getElementById('mobile_bill_search_bttn');
    // mobile_bill_search_bttn.addEventListener('click', function(event) {
    //       //  event.preventDefault();
    //         searchUploadedMobileBillPaper();
    // }, false);

    $('#searching_mobile_bill_paper_form').submit(function(event) {
        event.preventDefault();
        searchUploadedMobileBillPaper();
    });


    function searchUploadedMobileBillPaper() {

        mobile_bill_search_bttn.disabled = true;
        mobile_bill_search_bttn.innerText = 'Searching...';

        // var project_id = $('#project_id').val();
        // var month = $('#month').val();
        // var year = $('#year').val();
        // var operation_type =  $('#operation_type').val();


        var formData = new FormData($('#searching_mobile_bill_paper_form')[0]);
         // Call ajax
        $.ajax({
            type: 'POST',
            url: '/admin/employee/mobile-bill/information/manage',
            // data: {
            //     operation_type:operation_type,
            //     month: month,
            //     year:year,
            //     project_id:project_id
            // },
          data:formData,
          processData: false,
          contentType: false,
            success: function(res) {
                mobile_bill_search_bttn.disabled = false;
                mobile_bill_search_bttn.innerText = 'Search';
                records = res.data;
                if (res.status != 200) {
                    showSweetAlertMessage('error', res.message);
                    return ;
                }
                else if(records.length == 0)
                {
                    showSweetAlertMessage('error', "Records Not Found ");
                    return;
                }

                var counter = 1;
                var rows = '';
                $.each(records, function(key,value){

                    var deleteurl =  '';
                    rows += `<tr>
                            <td>${counter++}</td>
                            <td>${value.proj_name}</td>
                            <td>${ getMonthNameByNumber(value.month)}</td>
                            <td> ${value.year}</td>
                            <td> ${value.created_by}</td>
                            <td> ${ new Date(value.created_at).toLocaleDateString("en-US")}</td>
                             <td>
                                <a href="${deleteurl}" title="delete" id="delete"><i class="fa fa-trash fa-lg delete_icon"></i> || </a>
                                <a target="_blank" href="{{ url('${value.bill_payment_paper}') }}" <i class="fas fa-eye fa-lg view_icon"></i> </a>
                            </td>
                        </tr>
                        `
                    counter++;
                });
                $('#emp_mobile_bill_searching_modal').modal('hide');
                $('#searching_result_table_section').removeClass('d-none').addClass('d-block');
                $('#mobile_bill_searching_result_table_body').html(rows);
            },
            error: function(xhr) {
                mobile_bill_search_bttn.disabled = false;
                mobile_bill_search_bttn.innerText = 'Search';
                showSweetAlertMessage('error', 'Operation Failed, Please try Again');
            }
        });
    }


    function getMonthNameByNumber(mn){
        const months = [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
                ];
        return months[mn];
  }


});
