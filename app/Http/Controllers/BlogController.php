<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::where('is_active', true);

        // 1. Fitur Search
        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        // 2. Pagination & Formatting
        $news = $query->latest('published_at')
            ->paginate(8) // 8 item per halaman
            ->withQueryString() // Agar parameter search tidak hilang saat pindah halaman
            ->through(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'category' => $item->category,
                    'date' => Carbon::parse($item->published_at)->format('d M Y'), // Format Tanggal
                    'image' => $item->image, 
                    // Jika pakai storage nanti: 'image' => Storage::url($item->image)
                ];
            });

        return Inertia::render('News/Index', [
            'news' => $news,
            'filters' => $request->only(['q']) // Kirim state search ke frontend
        ]);
    }

    public function show($slug)
    {
        // Untuk halaman detail nanti
        $blog = Blog::where('slug', $slug)->firstOrFail();
        return Inertia::render('News/Show', ['blog' => $blog]);
    }
}