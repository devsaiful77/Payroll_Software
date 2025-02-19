function showSweetAlertMessage(type, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    })
    Toast.fire({
        type: type,
        title: message,
    })
}

$("form#company_chart_of_account").validate({
    rules: {
        acct_type_id: {
            required: true,
        },
        chart_of_acct_name: {
            required: true,
        },
        chart_of_acct_number: {
            required: true,
        },
        opening_date: {
            required: true,
        },
    },
    messages: {
        acct_type_id: {
            required: "Select Account Type Name.",
        },
        chart_of_acct_name: {
            required: "Enter Account Holder Name.",
        },
        chart_of_acct_number: {
            required: "Enter Account Number.",
        },
        opening_date    : {
            required: "Please Select Account Opening Date.",
        },

    }
});

$(document).ready(function() {
    loadChartAccountInfo();
    // Attach the submit event handler to the form
    $('#company_chart_of_account').submit(function(event) {
        event.preventDefault();
        chartOfAccountInfoStore();
    });
    function chartOfAccountInfoStore() {
        const chartOfAccBtn = document.getElementById('account-info-submit-button');

        // Validate the form
        if (!$('#company_chart_of_account').valid()) {
            return;
        }

        var formData = new FormData($('#company_chart_of_account')[0]);

        // Disable button to prevent multiple submissions
        chartOfAccBtn.disabled = true;
        chartOfAccBtn.innerText = 'Submitting...';

        // Call ajax
        $.ajax({
            type: 'POST',
            url: '/admin/company/chart-of-account/records-store',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                console.log(res)
                if (res.success) {
                    $('#company_chart_of_account').trigger("reset");
                    showSweetAlertMessage('success', res.message);
                    loadChartAccountInfo();
                } else {
                    showSweetAlertMessage('error', res.message);
                }
                chartOfAccBtn.disabled = false;
                chartOfAccBtn.innerText = 'SAVE INFO';
            },
            error: function(status, error) {
                showSweetAlertMessage('error', 'An error occurred');
                chartOfAccBtn.disabled = false;
                chartOfAccBtn.innerText = 'SAVE INFO';
            }
        });
    }

    $('#chartOfAccountEditForm').submit(function(event) {
        event.preventDefault();
        chartOfAccountInfoUpdate();
    });

    function chartOfAccountInfoUpdate() {
        const chartOfAccUpdateBtn = document.getElementById('account-info-update-button');

        // Validate the form
        if (!$('#chartOfAccountEditForm').valid()) {
            return;
        }

        var formData = new FormData($('#chartOfAccountEditForm')[0]);

         chartOfAccUpdateBtn.disabled = true;
        chartOfAccUpdateBtn.innerText = 'Submitting...';

         $.ajax({
            type: 'POST',
            url: '/admin/company/chart-of-account/records-update',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                debugger;
                console.log(res)
                if (res.success) {
                    showSweetAlertMessage('success', res.message);
                    loadChartAccountInfo();
                } else {
                    showSweetAlertMessage('error', res.message);
                }
                $('#chartOfAccountEditModal').modal('hide');

                chartOfAccUpdateBtn.disabled = false;
                chartOfAccUpdateBtn.innerText = 'UPDATE INFO';
            },
            error: function(status, error) {
                debugger;
                showSweetAlertMessage('error', 'An error occurred');
                chartOfAccUpdateBtn.disabled = false;
                chartOfAccUpdateBtn.innerText = 'UPDATE';
            }
        });
    }
});


function loadChartAccountInfo(){
    $.ajax({
        type: 'GET',
        url: '/admin/company/chart-of-account/records-load',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                var rows = ""
                $.each(res.chart_of_accounts, function(key, value) {
                    rows +=
                        `
                            <tr>
                                    <td>
                                        ${value.acct_type_name}
                                    </td>
                                    <td>
                                        ${value.chart_of_acct_name}
                                    </td>
                                    <td>
                                        ${value.chart_of_acct_number}
                                    </td>
                                    <td>
                                        ${value.acct_balance}
                                    </td>
                                    <td>
                                        ${value.opening_date}
                                    </td>

                                    <td>
                                        ${value.is_predefined == 1 ? 'Predefined':'Created'}
                                    </td>
                                    <td>
                                        ${value.is_transaction == 1 ? 'Occurred':'No Yet'}
                                    </td>
                                    <td>
                                        ${value.is_closed == 1 ? 'Closed':'Running'}
                                    </td>
                                    <td>
                                        ${value.created_by_id}
                                    </td>
                                    <td style="text-align: center;">
                                        <button style="border:none;padding: 1px 3px 0px 3px;margin-left: 3px;background: #3232a3;color: white;text-transform: uppercase;" id="${value.chart_of_acct_id}" onclick="editChartOfAccountInfo(this.id)">
                                            <i class="fa fa-pencil-square fa-lg"></i>
                                        </button>

                                        <button style="border:none;padding: 1px 3px 1px 3px;margin-left: 3px;background: #b92b31;color: white;text-transform: uppercase;" class="close-button" data-id="1">
                                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                        `
                });
                $('#chart_of_account_table_content_view').html(rows);
            }
        }
    });
}



function editChartOfAccountInfo(chartOfAcctID) {

    $.ajax({
        type: 'GET',
        url: '/admin/company/chart-of-account/record-edit/' + chartOfAcctID,
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                 $('#chartOfAccountEditModal').find('#chart_of_acct_id').val(res.chart_of_account.chart_of_acct_id );
                $('#chartOfAccountEditModal').find('#acct_type_id').val(res.chart_of_account.acct_type_id);
                $('#chartOfAccountEditModal').find('#chart_of_acct_name').val(res.chart_of_account.chart_of_acct_name);
                $('#chartOfAccountEditModal').find('#chart_of_acct_number').val(res.chart_of_account.chart_of_acct_number);
                $('#chartOfAccountEditModal').find('#acct_balance').val(res.chart_of_account.acct_balance);
                $('#chartOfAccountEditModal').find('#opening_date').val(res.chart_of_account.opening_date);

                // Show the modal
                $('#chartOfAccountEditModal').modal('show');
            } else {
                showSweetAlertMessage('error', res.message);
            }
        }
    });
}




$(document).on("click", ".close-button", function(e){
    e.preventDefault();
    var button = $(this); // Store the button that was clicked
    var accountId = button.data("id");

    swal({
        title: "Are you sure To Confirm?",
        text: "Once Confirm, you will not go Back Step Again!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            // Run the onclick event handler after confirmation
            chartOfAccountClosed(accountId);
        } else {
            // Optional: Handle the case where the user cancels
            // swal("Not Confirm!");
        }
    });
});


function chartOfAccountClosed(accountId) {
    $.ajax({
        type: 'GET',
        url: '/admin/company/chart-of-account/close/' + accountId, // Replace with your actual URL
        success: function(res) {
            if (res.success) {
                showSweetAlertMessage('success', res.message);
                loadChartAccountInfo();
            } else {
                showSweetAlertMessage('error', res.message);
            }
            // window.location.reload();
        },
        error: function(status, error) {
            showSweetAlertMessage('error', 'An error occurred');
        }
    });
}




$('#acct_type_id').on('change', function() {
    var acc_type_id = $(this).val();

        $.ajax({
            url: "/admin/company/chart-of-account/search-by-account-type/" + acc_type_id,
             type:"GET",
            data:{
                account_type_id:acc_type_id
            },
            dataType:"json",
            success:function(reponse) {

                 $('select[name="account_id"]').empty();
                 if (reponse.data.length > 0) {
                    $.each(reponse.data, function(key, value){
                        $('select[name="account_id"]').append('<option value="'+ value.chart_of_acct_id +'">' + value.chart_of_acct_number + '-'+ value.chart_of_acct_name + '</option>');
                    });
                } else {
                    $('select[name="account_id"]').append('<option value="">' + 'Not Found Any' + '</option>');
                }
            },
        });

});
