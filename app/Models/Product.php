<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ["title", "description","sku"] ;

    public function productInventories(){
        return $this->hasMany(ProductInventory::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
