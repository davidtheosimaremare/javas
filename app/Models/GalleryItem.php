<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
   // App/Models/GalleryItem.php
protected $fillable = [
    'image_url', 
    'caption', // <--- Pastikan ini ada
    'order'
];


}
