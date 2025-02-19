<?php

namespace App\Http\Controllers\Admin\Inventory_Module\ItemSetup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\ItemType;

class ItemTypeController extends Controller{
    public function getAll(){
      return $all = ItemType::get();
    }
}
