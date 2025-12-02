<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageSetting extends Model
{
    use HasFactory;

    // --- TAMBAHKAN BARIS INI ---
    // Artinya: "Izinkan semua kolom diisi secara massal, KECUALI kolom id"
    protected $guarded = ['id'];
}
