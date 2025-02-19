<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Http\Services\{SalesCustomerService,SalesProductInfoService,SalesService};

class SalesController extends Controller
{
    
    public function index()
    {
        return view('account::pages.sales.index');
    }

    public function create(SalesCustomerService $salesCustomerService, SalesProductInfoService $salesProductInfoService){
        $data['customer_list'] = $salesCustomerService->getCustomersForSales();
        $data['product_list'] = $salesProductInfoService->getProductsForSales();
        return view('account::pages.sales.create',compact('data'));
    }
    
}
