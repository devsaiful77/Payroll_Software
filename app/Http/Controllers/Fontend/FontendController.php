<?php

namespace App\Http\Controllers\Fontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BannerInfoController;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\CompanyDataService;

class FontendController extends Controller
{
  public function index()
  {


    $banner = new BannerInfoController();
    $getBanner = (new CompanyDataService())->getBannersInformation();
    $proj = (new EmployeeRelatedDataService())->getAllProjectInformation();

    return view('website.index', compact('getBanner', 'proj'));
  }

  public function projectDetails($proj_id)
  {

    $proj = (new EmployeeRelatedDataService())->findAProjectInformation($proj_id);
    $muliple = (new EmployeeRelatedDataService())->getProjectMultipleImage($proj_id);
    return view('website.project-details', compact('proj', 'muliple'));
  }
}
