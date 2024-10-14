<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'path', 'is_default'];

    public function imageable(){
        return $this->morphTo();
    }

    public function defaultImages(){
        return $this->hasMany(DefaultImage::class);
    }

}



