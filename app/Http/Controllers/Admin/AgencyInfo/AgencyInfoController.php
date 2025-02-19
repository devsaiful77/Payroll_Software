<?php

namespace App\Http\Controllers\Admin\AgencyInfo;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DataServices\CompanyDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// last update
class AgencyInfoController extends Controller
{

     // Permision
    function __construct()
    {
        $this->middleware('permission:agency.add-agencry.form', ['only' => ['newAgencyAddForm','AgencyInformationInsertionUISubmit']]);

    }

    public function newAgencyAddForm()
    {

        $all = (new CompanyDataService())->getAllAgencies();
        return view('admin.agency.agency-add', compact('all'));
    }
    function AgencyInformationInsertionUISubmit(Request $request)
    {
        // dd($request->all());
        $insert = (new CompanyDataService())->saveNewAgencyInformation($request->agency_name, $request->address, $request->contact_no);
        if ($insert) {
            Session::flash('success', 'Successfully Added New Agency.');
            return redirect()->back();
        } else {
            Session::flash('error', 'Please Try Again.');
            return redirect()->back();
        }
    }
}
