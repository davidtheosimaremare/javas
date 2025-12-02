<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Footer extends Model
{
    use HasFactory;
    
    // --- TAMBAHKAN BARIS INI ---
    protected $table = 'footer_links';
    // ---------------------------

    // Kolom yang aman untuk mass assignment
    protected $fillable = [
        'type',
        'name',
        'url',
        'order'
    ];
    
    // ... (sisa kode Model)
}