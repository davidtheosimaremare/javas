<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    // TAMBAHKAN BARIS INI
    // Artinya: "Izinkan semua kolom diisi, KECUALI kolom id"
    protected $guarded = ['id']; 
}