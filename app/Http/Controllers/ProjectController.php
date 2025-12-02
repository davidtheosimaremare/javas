<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\PageSetting;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        // 1. Ambil Pengaturan Halaman (Hero Image, Title, Subtitle)
        // Kita cari yang slug-nya 'projects' (jamak) atau 'project' sesuai database Anda
        $pageSetting = PageSetting::where('page_slug', 'projects')->first();

        // 2. Data Pagination Project
        $projects = Project::where('is_active', true)
            ->orderBy('order', 'asc')
            ->paginate(6);

        // 3. Data Peta
        $mapProjects = Project::whereNotNull('coord_top')
            ->whereNotNull('coord_left')
            ->select('id', 'client', 'title', 'map_area as area', 'coord_top as top', 'coord_left as left', 'hero_image as image', 'slug')
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'mapProjects' => $mapProjects,
            'pageSetting' => $pageSetting // <--- Kirim ke Vue
        ]);
    }

    public function show($slug)
{
    // Cari project berdasarkan slug, load relasi galleries dan testimonials
    $project = Project::with(['galleries', 'testimonials'])
        ->where('slug', $slug)
        ->firstOrFail(); // 404 jika tidak ketemu

    return Inertia::render('Projects/Show', [
        'project' => $project
    ]);
}
    
}