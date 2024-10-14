<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    use HasFactory;


    protected $fillable = [
        "total_quantity",
        "vendor_id",
        "product_id",
        "status"
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }


    public function users(){
        return $this->belongsTo(User::class);
    }

}
