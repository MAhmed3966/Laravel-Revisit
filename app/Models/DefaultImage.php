<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class DefaultImage extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'path', 'image_id', 'image_size'];
    public function image(){
        return $this->belongsTo(Image::class);
    }
}
