<div id="sidebar-menu">
    <ul>
        <li><a href="{{ route('admin.dashboard') }}" class="waves-effect"><i class="md md-home"></i><span>Dashboard </span></a></li>

        <!-- Administor Settings -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fas fa-user-cog"></i><span>Admin Settings</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                {{-- User Role Permision --}}
                <li class="mm-active">
                    <a href="#" class="waves-effect"><i class="fas fa-project-diagram"></i><span>Permission</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        @can ('role-list')
                            <li><a href="{{ route('users.index') }}"><span><i class="fas fa-arrow-right"></i></span> User</a></li>
                            <li><a href="{{ route('roles.index') }}"><span><i class="fas fa-arrow-right"></i></span> Role</a></li>
                            <li><a href="{{ route('salary-process-permission-ui') }}"><span><i class="fas fa-arrow-right"></i></span> Salary Process</a></li>
                        @endcan
                    </ul>
                </li>
                <li class="mm-active">
                    <a href="#" class="waves-effect"><i class="fas fa-project-diagram"></i><span>Company Profile</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        @can('company-profile')
                        <li><a href="{{ route('company-profiles') }}"><span><i class="fas fa-arrow-right"></i></span> Company Profile</a></li>
                        @endcan
                        @can('subcompany-list')
                        <li><a href="{{ route('sub-comp-info') }}"><span><i class="fas fa-arrow-right"></i></span> Sub Company</a></li>
                        @endcan
                        @can('sponser-list')
                        <li><a href="{{ route('add-sponser') }}"><span><i class="fas fa-arrow-right"></i></span> Add Sponser</a></li>
                        <li><a href="{{ route('agency.add-agencry.form') }}"><span><i class="fas fa-arrow-right"></i></span>Add Agency</a></li>
                        @endcan

                        @can('agency.add-agencry.form')
                        <li><a href="{{ route('agency.add-agencry.form') }}"><span><i class="fas fa-arrow-right"></i></span>Add Agency</a></li>
                        @endcan


                        @can('banner-list')
                        <li><a href="{{ route('banner-info') }}"><span><i class="fas fa-arrow-right"></i></span> Upload Banner</a></li>
                        @endcan
                        @can('designation-list')
                        <li><a href="{{ route('emp-category') }}"><span><i class="fas fa-arrow-right"></i></span> Emp. Trade</a></li>
                        @endcan

                        @can('company_bank_info_list')
                        <li><a href="{{ route('bank-infos') }}"><span><i class="fas fa-arrow-right"></i></span>Bank Info  </a></li>
                        @endcan

                        @can('main_contractor_list')
                        <li><a href="{{ route('main-contrator-info') }}"><span><i class="fas fa-arrow-right"></i></span> Main Contractor </a></li>
                        @endcan
                    </ul>
                </li>

                <li class="mm-active">
                    <a href="#" class="waves-effect"><i class="fas fa-project-diagram"></i><span>Address Section</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        @can('country-list')
                        <li><a href="{{ route('add-country') }}"><span><i class="fas fa-arrow-right"></i></span> Country</a></li>
                        @endcan
                        @can('division-list')
                        <li><a href="{{ route('add-division') }}"><span><i class="fas fa-arrow-right"></i></span> Division</a></li>
                        @endcan

                        @can('district-list')
                        <li><a href="{{ route('add-district') }}"><span><i class="fas fa-arrow-right"></i></span> District</a></li>
                        @endcan
                    </ul>
                </li>
                <li class="mm-active">
                    <a href="#" class="waves-effect"><i class="fas fa-project-diagram"></i><span>Project</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        @can('project-list')
                        <li><a href="{{ route('project-info') }}"><span ><i class="fas fa-arrow-right"></i></span> Add New </a></li>
                        @endcan

                        @can('projectincharge-list')
                        <li><a href="{{ route('add-project.incharge') }}"><span><i class="fas fa-arrow-right"></i></span>In-charge</a></li>
                        @endcan

                        @can('projectimage-list')
                        <li><a href="{{ route('project-image-upload') }}"><span><i class="fas fa-arrow-right"></i></span> Upload Photos </a></li>
                        @endcan

                        @can('user_project_access_permission')
                        <li><a href="{{ route('user-project-access-permission') }}"><span><i class="fas fa-arrow-right"></i></span>Project Permission</a></li>
                        @endcan
                    </ul>
                </li>

                <li class="mm-active">
                    <a href="#" class="waves-effect"><i class="fas fa-project-diagram"></i><span>Cost Controll</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    <ul class="list-unstyled">
                        @can('cost_controll_activity_element_insert')
                        <li><a href="{{ route('costcontrol.activity.insert.ui') }}"><span ><i class="fas fa-arrow-right"></i></span> Activity </a></li>
                        @endcan
                    </ul>
                </li>

            </ul>
        </li>

        <!-- Approval -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fas fa-users"></i><span>Approval</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                @can('job-approve')
                <li><a href="{{ route('employee.new.employee.job.approval.ui') }}"><span><i class="fas fa-arrow-right"></i></span> New Employee Approval </a></li>
                @endcan

                @can('iqama-renewal-expense-approval')
                <li><a href="{{ route('show-pending-iqamarenewal-fee') }}"><span><i class="fas fa-arrow-right"></i></span> Iqama Renewal Approval </a></li>
                @endcan

                @can('attendance-records-approval')
                    <li><a href="{{ route('monthly.attendance.approval.ui') }}"><span><i class="fas fa-arrow-right"></i></span> Attendance Approval </a></li>
                @endcan
                @can('income-approve')
                <li><a href="{{ route('income-list') }}"><span><i class="fas fa-arrow-right"></i></span> Income Approval </a></li>
                @endcan

                @can('expenditure-approve')
                <li><a href="{{ route('company.daily.expesne.approval-pending.list') }}"><span><i class="fas fa-arrow-right"></i></span> Expenses Approval </a></li>
                @endcan

                @can('leave_application_update')
                <li><a href="{{ route('leave.application.pending.list') }}"><span><i class="fas fa-arrow-right"></i></span>Leave Application </a></li>
                @endcan


            </ul>
        </li>


        <!-- Employee -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fas fa-users"></i><span>Employee</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                @can('employee-add')
                <li><a href="{{ route('add-employee') }}"><span><i class="fas fa-arrow-right"></i></span> Add Employee</a></li>
                @endcan

                @can('salarydetails-list')
                <li><a href="{{ route('salary-details') }}"><span><i class="fas fa-arrow-right"></i></span> Salary Details</a></li>
                @endcan

                @can('employee-list')
                <li><a href="{{ route('employee-list')}}"><span><i class="fas fa-arrow-right"></i></span> Employee List</a></li>
                @endcan

                @can('employee-search')
                <li><a href="{{ route('search-employee')}}"><span><i class="fas fa-arrow-right"></i></span> Emp. Search</a></li>
                 @endcan

                @can('multiple-employee-transfer')
                 <li><a href="{{ route('employee.transfer-to-new-project')}}"><span><i class="fas fa-arrow-right"></i></span> Emp. Transfer</a></li>
                @endcan

                @can('employee-status')
                 <li><a href="{{ route('search-employee.status')}}"><span><i class="fas fa-arrow-right"></i></span> Emp. Status</a></li>
                @endcan

                @can('employee_all_information_update')
                <li><a href="{{ route('search-employee.update.info')}}"><span><i class="fas fa-arrow-right"></i></span> Emp. Info Update </a></li>
                 @endcan

                @can('emp_bank_info_add')
                 <li><a href="{{ route('employee.bank.details.ui.load')}}"><span><i class="fas fa-arrow-right"></i></span>Bank Info </a></li>
                @endcan


                @can('leave_application_submit')
                <li><a href="{{ route('leave.application.form') }}"><span><i class="fas fa-arrow-right"></i></span>Leave Application</a></li>
                @endcan

                @can('employee-promotion')
                <li><a href="{{ route('employee-promosion') }}"><span><i class="fas fa-arrow-right"></i></span> Emp. Promotion</a></li>
                @endcan

                @can('employee_job_status_change_activity')
                <li><a href="{{ route('employee.new.activity.insert.form') }}"><span><i class="fas fa-arrow-right"></i></span> Emp. Activity</a></li>
                @endcan

                @can('emp_tuv_information_insert')
                <li><a href="{{ route('employee.tuv.information.insert.form') }}"><span><i class="fas fa-arrow-right"></i></span>TUV Info</a></li>
                @endcan


                @can('employee-contact-persion')
                <li><a href="{{ route('emp-contact-person') }}"><span><i class="fas fa-arrow-right"></i></span> Emp. Contact Person</a></li>
                @endcan

                @can('employee-job-experience')
                <li><a href="{{ route('emp-job-experience') }}"><span><i class="fas fa-arrow-right"></i></span> Emp. Job Experience</a></li>
                @endcan

                @can('employee-iqama-expire')
                <li><a href="{{ route('iqama-expired') }}"><span><i class="fas fa-arrow-right"></i></span> Emp. Iqama Expire </a></li>
                @endcan

                @can('employee-passport-expire')
                <li><a href="{{ route('passport-expired') }}"><span><i class="fas fa-arrow-right"></i></span> Emp. Passport Expire </a></li>
                @endcan


            </ul>
        </li>

        <!-- Work Menu -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fas fa-users"></i><span>Work History</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">

                @can('month-work-history')
                <li><a href="{{ route('add-daily-work') }}"><span><i class="fas fa-arrow-right"></i></span> Add Monthly Work</a></li>
                <li><a href="{{ route('multiple.emp.monthly.records.form') }}"><span><i class="fas fa-arrow-right"></i></span> Multiemployee Work Add</a></li>
                <li><a href="{{ route('employee.month.work.record.searchui') }}"><span><i class="fas fa-arrow-right"></i></span>Work Record Search</a></li>

                @endcan

                @can('employee_working_shift_update')
                <li><a href="{{ route('employee.shift-status-update-ui')}}"><span><i class="fas fa-arrow-right"></i></span>Employee Working Shift </a></li>
                @endcan

                @can('attendence-in')
                <li><a href="{{ route('employee-in.time')}}"><span><i class="fas fa-arrow-right"></i></span> Attendance (IN)</a></li>
                @endcan

                @can('attendence-out')
                <li><a href="{{ route('employee-out.time')}}"><span><i class="fas fa-arrow-right"></i></span> Attendance (OUT)</a></li>
                @endcan

                @can('attendence-edit')
                <li><a href="{{ route('employee-attendance-in-out-edit-form')}}"><span><i class="fas fa-arrow-right"></i></span>Attendance Edit</a></li>
                @endcan

                @can('attendance-processing')
                <li><a href="{{ route('employee-attendance-process-form')}}"><span><i class="fas fa-arrow-right"></i></span>Attendance Process</a></li>
                @endcan


                @can('month-work-report')
                <li><a href="{{ route('monthwork-reportprocess')}}"><span><i class="fas fa-arrow-right"></i></span> Monthly Work Report</a></li>
                @endcan

            </ul>

        </li>

        <!-- Iqama & Advance -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fas fa-money-check"></i><span>Iqama & Advance </span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">

                @can('employee-advance-processing')
                <li><a href="{{ route('addvance.processing.ui') }}"><span><i class="fas fa-arrow-right"></i></span> Advance Processing</a></li>
                @endcan

                @can('set-advance')
                <li><a href="{{ route('addvance.payment') }}"><span><i class="fas fa-arrow-right"></i></span>Emp. Advance</a></li>
                @endcan

                @can('bd office payment setup')
                <li><a href="{{ route('employee.payment.create.from-bdoffice-payment') }}"><span><i class="fas fa-arrow-right"></i></span>BD Payment Setup</a></li>
                @endcan

                @can('bd office payment employee list')
                <li><a href="{{ route('employee.payment.bdoffice-payment-pending') }}"><span><i class="fas fa-arrow-right"></i></span>BD Payment List</a></li>
                @endcan

                @can('summary-adjuget')
                <li><a href="{{ route('employee.search.with.cash-payment') }}"><span><i class="fas fa-arrow-right"></i></span>Emp. Cash Payment</a></li>
                @endcan
                @can('advance-adjuget')
                <li><a href="{{ route('advance-pay.adjust') }}"><span><i class="fas fa-arrow-right"></i></span>Iqama & Advance Setting </a></li>
                @endcan

                @can('employee-anualsfee')
                <li><a href="{{ route('anual-fee.details') }}"><span><i class="fas fa-arrow-right"></i></span>Iqama Renewal Expense</a></li>
                @endcan

                @can('iqama-renewal-expense-edit')
                <li><a href="{{ route('iqama.renewal.expense.record.search.ui') }}"><span><i class="fas fa-arrow-right"></i></span>Iqama Expense Update</a></li>
                @endcan

                @can('employee-contribution')
                <li><a href="{{ route('cpf-Contribution.set') }}"><span><i class="fas fa-arrow-right"></i></span> Emp. Contribution</a></li>
                @endcan


            </ul>
        </li>


         <!-- Catering Service -->
         <li class="has_sub">
            @can('catering_monthly_record_insert')
            <a href="#" class="waves-effect"><i class="fas fa-record-vinyl"></i><span>Catering</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                <li><a href="{{ route('catering.monthly.information') }}"><span><i class="fas fa-arrow-right"></i></span> Monthly Record </a></li>
            </ul>
            @endcan
        </li>

        <!-- Salary System -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fas fa-money-check"></i><span>Salary System</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                @can('salary-processing')
                <li><a href="{{ route('salary-processing-for-month') }}"><span><i class="fas fa-arrow-right"></i></span> Salary Processing</a></li>
                @endcan
                @can('salary-pending')
                <li><a href="{{ route('salary-pending') }}"><span><i class="fas fa-arrow-right"></i></span> Salary Pending</a></li>
                <li><a href="{{ route('salary-paid') }}"><span><i class="fas fa-arrow-right"></i></span> Salary Paid</a></li>
                <li><a href="{{ route('salary-sheet') }}"><span><i class="fas fa-arrow-right"></i></span> Salary Sheet Upload</a></li>
                @endcan


                @can('salary_bonus_add')
                    <li><a href="{{ route('employee.salary.bonus') }}"><span><i class="fas fa-arrow-right"></i></span>Emp. Bonus</a></li>
                @endcan

                @can('employee_fiscal_year_add')
                    <li><a href="{{ route('employee.fiscal.year.ui') }}"><span><i class="fas fa-arrow-right"></i></span>Salary Closing</a></li>
                @endcan
            </ul>
        </li>

        <!-- Accounting -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fas fa-user-cog"></i><span>Accounting</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                {{-- Accounting Setting --}}
                <li class="mm-active">
                    @can('chart_of_account_account_related_setting')
                    <a href="#" class="waves-effect"><i class="fas fa-project-diagram"></i><span>Account Setting</span><span class="pull-right"><i class="md md-add"></i></span></a>
                    @endcan
                    <ul class="list-unstyled">
                        @can('chartofaccount_mother_account_information')
                        <li><a href="{{ route('company.chart.of.account') }}"><span><i class="fas fa-arrow-right"></i></span>Main Accounts </a></li>
                        @endcan
                        @can('chartofaccount_journal_information')
                        <li><a href="{{ route('account.journal.info') }}"><span><i class="fas fa-arrow-right"></i></span> Journal </a></li>
                        @endcan
                    </ul>
                </li>
                {{-- sales invoice --}}
                <li><a href="{{ route('admin.accounting.product.list') }}" class="waves-effect"> <i class="fas fa-arrow-right"></i> <span>Products </span></a></li>
                <li><a href="{{ route('admin.accounting.sale.create') }}" class="waves-effect"> <i class="fas fa-arrow-right"></i> <span> Add Sale </span></a></li>
                <li><a href="{{ route('admin.accounting.sale.list') }}" class="waves-effect"> <i class="fas fa-arrow-right"></i> <span> All Sale </span></a></li>


            </ul>
        </li>


        <!-- Report Generate -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fas fa-record-vinyl"></i><span>Report</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                @can('report-attendence')
                <li><a href="{{ route('employee-entry-out-report') }}"><span><i class="fas fa-arrow-right"></i></span> Employee Attendance </a></li>
                @endcan


                @can('report-employee-iqama-project')
                <li><a href="{{ route('employee.renewal.expense.report.process.form') }}"><span><i class="fas fa-arrow-right"></i></span>Iqama Renewal</a></li>
                @endcan

                @can('report-employee-iqama-single')

                {{-- <li><a href="{{ route('employee.payment.from-bd-office.report.ui') }}"><span><i class="fas fa-arrow-right"></i></span>BD Office Payment Report</a></li> --}}
                @endcan

                @can('report-employee-salary-summary')
                <li><a href="{{ route('employee-salary.summary') }}"><span><i class="fas fa-arrow-right"></i></span> Employee Salary Summary </a></li>
                @endcan

                @can('salary_report_generation_main_form')
                <li><a href="{{ route('salary.report.generation.form') }}"><span><i class="fas fa-arrow-right"></i></span>Salary Report</a></li>
                @endcan



                @can('hr_report_employee_information')
                <li><a href="{{ route('hr.employee.report.process.form') }}"><span><i class="fas fa-arrow-right"></i></span>HR Reports</a></li>
                @endcan


                @can('emp_advance_paid_report')
                <li><a href="{{ route('addvance.report.process.form') }}"><span><i class="fas fa-arrow-right"></i></span>Advance Report</a></li>
                <!-- <li><a href="{{ route('CPF-contribution-report') }}"><span><i class="fas fa-arrow-right"></i></span> CPF Contribution</a></li> -->
                @endcan


                {{-- @can('report-expenditure')
                <li><a href="{{ route('report-expenditure') }}"><span><i class="fas fa-arrow-right"></i></span> Expenditure</a></li>
                @endcan --}}

                {{-- @can('report-income')
                <li><a href="{{ route('report-income') }}"><span><i class="fas fa-arrow-right"></i></span> Founding Source</a></li>
                <li><a href="{{ route('income-expense-report-ui') }}"><span><i class="fas fa-arrow-right"></i></span>Income Expense</a></li>
                @endcan --}}

                {{-- @can('report-item-stock')
                <li><a href="{{ route('items-report') }}"><span><i class="fas fa-arrow-right"></i></span> Item Stock </a></li>
                @endcan --}}

                @can('report-log-book')
                <li><a href="{{ route('log-book.report') }}"><span><i class="fas fa-arrow-right"></i></span> Log Book</a></li>
                @endcan

                @can('vehicle_details_info_report')
                <li><a href="{{ route('vehicles.record.report') }}"><span><i class="fas fa-arrow-right"></i></span> Vehicles Report</a></li>
                @endcan

                @can('cost_controll_activity_report')
                <li><a href="{{ route('costcontrol.activity.details.report.ui') }}"><span><i class="fas fa-arrow-right"></i></span>Cost Controlling</a></li>
                @endcan
            </ul>
        </li>

        <!-- Cost Controll Section -->
        {{-- <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fab fa-acquisitions-incorporated"></i><span>Cost Controlling</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                    @can('cost_controll_activity_details_record_insert')
                        <li><a href="{{ route('costcontrol.activity.details.insert.ui') }}"><span ><i class="fas fa-arrow-right"></i></span>Activity Details</a></li>
                    @endcan
            </ul>
        </li> --}}

        <!-- Income Cost -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fab fa-acquisitions-incorporated"></i><span>Income & Expense</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">


                @can('evoucher-list')
                <li><a href="{{ route('create.qrcode.only') }}"><span><i class="fas fa-arrow-right"></i></span> Create QRCode</a></li>
                @endcan

                @can('invoice_summary_add')
                <li><a href="{{ route('invoice.summary.create.ui') }}"><span><i class="fas fa-arrow-right"></i></span>Invoice Summary</a></li>
                @endcan


                @can('invoice_with_qr_code_create')
                <li><a href="{{ route('bill-voucher.create') }}"><span><i class="fas fa-arrow-right"></i></span>Create Invoice</a></li>
                @endcan


                @can('invoice_with_qr_code_update')
                <li><a href="{{ route('qrcode.bill.voucher.update.ui') }}"><span><i class="fas fa-arrow-right"></i></span>Invoice Update</a></li>
                <li><a href="{{ route('submited-bill-voucher-ui') }}"><span><i class="fas fa-arrow-right"></i></span>Invoice Records</a></li>
                @endcan

                @can('invoice_and_salary_summary_statement_create')
                <li><a href="{{ route('invoice.and.salary.statement.summary') }}"><span><i class="fas fa-arrow-right"></i></span>Invoice Statement</a></li>
                @endcan



                {{-- @can('income-add')
                <li><a href="{{ route('income-source') }}"><span><i class="fas fa-arrow-right"></i></span> Income Details </a></li>
                @endcan --}}

                @can('expenditure-type')
                <li><a href="{{ route('cost-type') }}"><span><i class="fas fa-arrow-right"></i></span>Expense Head </a></li>
                @endcan

                @can('expenditure-list')
                    <li><a href="{{ route('company.daily.new.expesne.form') }}"><span><i class="fas fa-arrow-right"></i></span> Daily Expenses</a></li>
                    <li><a href="{{ route('company.daily.transaction.form') }}"><span><i class="fas fa-arrow-right"></i></span> Cash Transaction</a></li>
                @endcan

            </ul>
        </li>

        <!-- Inventory Section -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fab fa-asymmetrik"></i><span>Inventory</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">

                @can('inventory_sub_store_add')
                <li><a href="{{ route('inventory-sub-store-infos') }}"><span><i class="fas fa-arrow-right"></i></span> Sub Store Info</a></li>
                @endcan

                @can('inventory_item_category_add')
                <li><a href="{{ route('inventory-category') }}"><span><i class="fas fa-arrow-right"></i></span> Category</a></li>
                @endcan

                @can('inventory_item_subcategory_add')
                <li><a href="{{ route('inventory-sub-category') }}"><span><i class="fas fa-arrow-right"></i></span> Sub Category</a></li>
                @endcan

                @can('inventory_item_name_add')
                <li><a href="{{ route('inventory-item-name') }}"><span><i class="fas fa-arrow-right"></i></span> Inventory Item</a></li>
                @endcan

                @can('inventory_item_details_add')
                <li><a href="{{ route('inventory-item-details-name') }}"><span><i class="fas fa-arrow-right"></i></span> Item Stock</a></li>
                @endcan

                @can('inventory_item_distribution')
                <li><a href="{{ route('emp.received.item.info.insert.form') }}"><span><i class="fas fa-arrow-right"></i></span>Item Distribution</a></li>
                @endcan

                {{-- @can('inventory_report') --}}
                <li><a href="{{ route('invenotry.report.process.ui') }}"><span><i class="fas fa-arrow-right"></i></span> Report</a></li>
                {{-- @endcan --}}


                {{-- @can('tools-purchase')
                <li><a href="{{ route('order-metarial-tools') }}"><span><i class="fas fa-arrow-right"></i></span> Purchase</a></li>
                @endcan

                @can('tools-condem')
                <li><a href="#"><span><i class="fas fa-arrow-right"></i></span> Tools Condem</a></li>
                @endcan   --}}



            </ul>
        </li>

        <!-- Vehicle and Bulding -->
        <li class="has_sub">
            <a href="#" class="waves-effect"><i class="fab fa-asymmetrik"></i><span>Vehicle & Building</span><span class="pull-right"><i class="md md-add"></i></span></a>
            <ul class="list-unstyled">
                @can('vichle-add')
                <li><a href="{{ route('add-new.vehicle') }}"><span><i class="fas fa-arrow-right"></i></span> New Vehicle</a></li>
                @endcan

                @can('vichle-add')
                <li><a href="{{ route('add.vehicle.fine') }}"><span><i class="fas fa-arrow-right"></i></span>Vehicle Fine</a></li>
                <li><a href="{{ route('driver-info-add-ui') }}"><span><i class="fas fa-arrow-right"></i></span> Add Driver </a></li>
                <li><a href="{{ route('driver-vehicle-info-add-ui') }}"><span><i class="fas fa-arrow-right"></i></span> Assign Driver-Vehicle </a></li>

                @endcan

                @can('vichle-maintanece')
                <li><a href="#"><span><i class="fas fa-arrow-right"></i></span> Vehicle Maintenance</a></li>
                @endcan

                @can('log-book')
                <li><a href="{{ route('add-new.LogBook') }}"><span><i class="fas fa-arrow-right"></i></span> Log Book</a></li>
                @endcan

                @can('rent-new-building')
                <li><a href="{{ route('rent.new-building') }}"><span><i class="fas fa-arrow-right"></i></span> New Building </a></li>
                @endcan
            </ul>
        </li>

    </ul>
    <div class="clearfix"></div>
</div>
