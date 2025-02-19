<?php

namespace App\Models\Inventory;

use App\Models\Inventory\SubStoreInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDetails extends Model
{
    use HasFactory;
    public function itemType(){
      return $this->belongsTo(ItemType::class,'itype_id','itype_id');
    }

    public function itemCatg(){
      return $this->belongsTo(ItemCategory::class,'icatg_id','icatg_id');
    }

    public function itemSubCatg(){
      return $this->belongsTo(ItemSubCategory::class,'iscatg_id','iscatg_id');
    }

    public function itemNameCode(){
      return $this->belongsTo(item_name::class,'item_deta_code','item_deta_code');
    }

    public function itemCompanyName(){
      return $this->belongsTo(ItemCompany::class,'item_comp_id','item_comp_id');
    }

    public function itemBrandName(){
      return $this->belongsTo(ItemBrand::class,'ibrand_id','ibrand_id');
    }

    public function subStore(){
      return $this->belongsTo(SubStoreInfo::class,'store_id','sub_store_id' );
    }

}
