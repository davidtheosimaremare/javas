<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\Service;
use App\Models\Statistic;
use App\Models\Project;
use App\Models\Blog;
use App\Models\CompanyProfile;
use App\Models\Footer;
use App\Models\News;
use App\Models\PageSetting;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
       
        $sliders = Slider::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($slide) {
                return [
                    'id' => $slide->id,
                    'title' => $slide->title,
                    'desc' => $slide->description,
                    'navTitle' => $slide->nav_title,
                    'image' => $slide->image,
                    'link' => $slide->link,
                ];
            });

           $recentNews = News::orderBy('published_at', 'desc')
            ->take(6) // Ambil 6 berita terbaru
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'category' => $item->category,
                    'image' => $item->image, // Pastikan ini URL lengkap atau path yang benar
                    
                    // Format Tanggal: "18 Nov 2025"
                    'date' => Carbon::parse($item->published_at)->translatedFormat('d M Y'),
                    
                    // Buat Link Slug: "/news/judul-berita"
                    'link' => '/news/' . $item->slug, 
                ];
            });


           $services = Service::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get()
            ->map(function ($service) {
               
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'description' => $service->description,
                    'image' => $service->image, 
                    'color' => $service->color,
                    'link' => $service->link,
                ];
            });


            $stats = Statistic::where('is_active', true)
    ->orderBy('order', 'asc')
    ->get();



    $projects = Project::where('is_active', true)
            ->where('is_featured', true) // <--- HANYA YANG DIPILIH ADMIN
            ->orderBy('order', 'asc')
            ->get();
    
    $profile = CompanyProfile::find(1);
    if ($profile) {
        $profile->social_media = json_decode($profile->social_media, true);
    }

    $footerlink = Footer::orderBy('order', 'asc')->get()->groupBy('type');
    $pageSetting = PageSetting::where('page_slug', 'home')->first();


   
        return Inertia::render('Home/Index', [
            'slides' => $sliders,
            'services' => $services,
            'stats' => $stats,
            'projects' => $projects->take(3),
            'recentNews' => $recentNews->take(6),
            'pageSetting' => $pageSetting,
            

        ]);
    }
}