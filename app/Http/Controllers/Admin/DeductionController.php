<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deduction;
/* ======== externel controller ========*/
use App\Http\Controllers\Admin\EmployeeInfoController;
use App\Http\Controllers\Admin\EmpCategoryController;
use App\Http\Controllers\DataServices\EmployeeDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Models\Enum\JobStatusEnum;


class DeductionController extends Controller
{
  /*
      ================================
      =======DATABASE OPERATION=======
      ================================
  */
  public function getAllDeduction()
  {
    $all = Deduction::get();
  }

  /*
      ================================
      =======VIEW OPERATION===========
      ================================
  */
  public function index()
  {
    return 'This Page Comming Soon......';
    return Redirect()->back();
  }

  public function add()
  {
    /* employee info */
    $empObj = new EmployeeInfoController();
    // $empInfo = $empObj->getAllEmployees();
    $empInfo = (new EmployeeDataService())->getAllEmployeesInformation(-1, JobStatusEnum::Active);
    /* employee category */
     $allCatg =  (new EmployeeRelatedDataService())->getEmpAllCategoryInfoForDropdown();


    return view('admin.employee-deduction.add', compact('empInfo', 'allCatg'));
  }
}
