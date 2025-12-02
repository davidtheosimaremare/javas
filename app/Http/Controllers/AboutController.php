<?php

namespace App\Http\Controllers;

use App\Models\AboutContent;
use App\Models\GalleryItem;
use App\Models\VmItem;
use App\Models\Team;
use App\Models\PageSetting;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function index()
    {
        // 1. Ambil Pengaturan Halaman (Hero Title/BG)
        $pageSettings = PageSetting::where('page_slug', 'about')->first();
        
        // 2. Ambil Konten Utama Halaman (Asumsi ID 1)
        $aboutContent = AboutContent::find(1); 

        // 3. Ambil List Item Dinamis (Diurutkan)
        $vmItems = VmItem::orderBy('order', 'asc')->get();
        $leadershipTeam = Team::orderBy('order', 'asc')->get();
        $galleryImages = GalleryItem::orderBy('order', 'asc')->get();

        // Data Project Locations (Diambil, meski tidak dipasang di tampilan saat ini)
       

        // 4. Kirim SEMUA PROPS ke Vue
        return Inertia::render('About/Index', [
            
            // HERO SECTION
            'heroTitle' => $pageSettings->hero_title ?? 'Profil Perusahaan',
            'heroBg' => $pageSettings->hero_bg_path,
            'section_subtitle' => $pageSettings->section_subtile,
            'quote_explanation_text' => $pageSettings->quote_explanation_text,


            // BODY CONTENT
            'aboutContent' => $aboutContent,
            'galleryImages' => $galleryImages,
            'misiList' => $vmItems, // Visi & Misi
            'leadershipTeam' => $leadershipTeam 
        ]);
    }
}