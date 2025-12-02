<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\PageSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Setting Halaman (Hero)
        $pageSetting = PageSetting::where('page_slug', 'news')->first();

        // 2. Query Berita dengan Pencarian & Pagination
        $news = News::query()
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy('published_at', 'desc')
            ->paginate(8) // 8 per halaman
            ->withQueryString(); // Agar pagination link membawa parameter search

        return Inertia::render('News/Index', [
            'news' => $news,
            'pageSetting' => $pageSetting,
            'filters' => $request->only(['search']), // Kirim balik keyword pencarian ke Vue
        ]);
    }

   public function show($slug)
{
    // 1. Cari berita berdasarkan slug
    // Gunakan first() dulu untuk debug, jangan firstOrFail() dulu
    $news = News::where('slug', $slug)->first();

    // 2. DEBUGGING (PENTING!)
    // Jika $news kosong, kita hentikan proses dan tampilkan pesan di layar
    if (!$news) {
        dd("Berita tidak ditemukan! Slug yang dicari: " . $slug);
    }

    // 3. Kirim ke Vue
    return Inertia::render('News/Show', [
        'news' => $news
    ]);
}
}