<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeAttendanceDataService;
use App\Http\Controllers\DataServices\AuthenticationDataService;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\DataServices\ProjectDataService;
use App\Http\Controllers\Admin\Helper\HelperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function index()
    {
        $noOfProjects = (new ProjectDataService())->countTotalNumberOfRunningPrjectsOfABranchOffice(Auth::user()->branch_office_id);
        $noOfUsers = (new AuthenticationDataService())->countTotalUsersInABranchOffice(Auth::user()->branch_office_id);
        $noOfEmpl = (new EmployeeDataService())->countTotalNumberOfEmployeesInABranchOffice(1,Auth::user()->branch_office_id); // active employee
        $noOfEmpl_vacation = (new EmployeeDataService())->countTotalNumberOfEmployeesInABranchOffice(5,Auth::user()->branch_office_id); // active employee
        $day =  (int) date('d');
        $month =  (int) date('m');
        $year = (int)date('Y');
        $attendance_summary = (new EmployeeAttendanceDataService())->getTodayAttendanceSummaryReportInABranchOffice(-1,$day,$month,$year,Auth::user()->branch_office_id);    // -1 both day & night
        $day_month_year = (new HelperController())->getDayMonthAndYearFromDateValue(date('d-m-Y',strtotime("-1 days")));
        $yesterday_nightshift_attend_summary = (new EmployeeAttendanceDataService())->getYesterdayNightshiftAttendanceSummaryReportOfABranchOffice(1,$day_month_year[0],$day_month_year[1],$day_month_year[2],Auth::user()->branch_office_id);
        $yesterday_dayshift_attend_summary = (new EmployeeAttendanceDataService())->getYesterdayDayshiftAttendanceSummaryReportOfABranchOffice(1,$day_month_year[0],$day_month_year[1],$day_month_year[2],Auth::user()->branch_office_id);
        $yesterday_present = (new EmployeeAttendanceDataService())->countTotalNumberOfWorkersPresentInADayOfABranchOffice($day_month_year[0],$day_month_year[1],$day_month_year[2],Auth::user()->branch_office_id);

        return view('admin.dashboard.index', compact('noOfProjects', 'noOfEmpl','noOfEmpl_vacation', 'noOfUsers','attendance_summary','yesterday_nightshift_attend_summary','yesterday_present'));

    }



}
