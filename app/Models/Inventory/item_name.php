<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class item_name extends Model
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

    public function itemBrand(){
      return $this->belongsTo(ItemBrand::class,'item_id','item_id');
    }
}
