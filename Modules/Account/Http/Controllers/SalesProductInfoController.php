<?php

namespace Modules\Account\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Http\Services\SalesProductInfoService;

class SalesProductInfoController extends Controller
{

    public function index(SalesProductInfoService $salesProductInfoService)
    {
        // return $salesProductInfoService->index();
        return view('account::pages.products.index');
    }






    /* ======================== API RESPONSE ======================== */
    public function getProductsForSales(SalesProductInfoService $salesProductInfoService) {
        $products = $salesProductInfoService->getProductsForSales();
        
    }
}
