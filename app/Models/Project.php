<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // --- TAMBAHKAN KODE DI BAWAH INI ---

    // 1. Relasi ke Gallery
    public function galleries()
    {
        return $this->hasMany(ProjectGallery::class);
    }

    // 2. Relasi ke Testimonials (Tambahkan juga biar tidak error berikutnya)
    public function testimonials()
    {
        return $this->hasMany(ProjectTestimonial::class);
    }

    // ------------------------------------

    // Logic Slug Otomatis (Jika sudah ada, biarkan saja)
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }
}