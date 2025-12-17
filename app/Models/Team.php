<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    // Pastikan nama tabel benar (biasanya jamak/plural)
    protected $table = 'teams'; 

    // --- TAMBAHKAN INI AGAR TIDAK ERROR MASS ASSIGNMENT ---
    protected $fillable = [
        'name',
        'title',
        'image_url',
    ];
}