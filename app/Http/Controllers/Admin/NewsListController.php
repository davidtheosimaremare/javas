<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\PageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NewsListController extends Controller
{
    // --- 1. LISTING & PAGE SETTING ---
    public function index(Request $request)
    {
        // Ambil Setting Halaman 'news'
        $pageSetting = PageSetting::firstOrCreate(
            ['page_slug' => 'news'],
            ['hero_title' => 'Berita & Artikel', 'hero_bg_path' => null]
        );

        // Ambil Data Berita (Pagination + Search)
        $newsQuery = News::orderBy('published_at', 'desc');

        if ($request->search) {
            $newsQuery->where('title', 'like', '%' . $request->search . '%');
        }

        return Inertia::render('Admin/NewsList/Index', [
            'pageSetting' => $pageSetting,
            'news' => $newsQuery->paginate(10)->withQueryString(),
            'filters' => $request->only(['search'])
        ]);
    }

    // --- 2. UPDATE HEADER (HERO PAGE) ---
    public function updateHeader(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_bg_path' => 'nullable|image|max:5120',
        ]);

        $setting = PageSetting::where('page_slug', 'news')->first();

        if ($request->hasFile('hero_bg_path')) {
            $this->deleteOldFile($setting->hero_bg_path);
            $file = $request->file('hero_bg_path');
            $fileName = $this->generateSafeFileName('hero-news', $file);
            $setting->hero_bg_path = $this->manualUpload($file, 'news-header', $fileName);
        }

        $setting->hero_title = $request->hero_title;
        $setting->save();

        return redirect()->back()->with('success', 'Header halaman berita diperbarui.');
    }

    // --- 3. FORM CREATE ---
    public function create()
    {
        return Inertia::render('Admin/NewsList/Form');
    }

    // --- 4. STORE DATA ---
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'published_at' => 'required|date',
            'summary' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'required|image|max:5120', // Wajib ada gambar utama
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $this->generateSafeFileName($request->title, $file);
            $path = $this->manualUpload($file, 'news', $fileName);
        }

        News::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'category' => $request->category,
            'published_at' => $request->published_at,
            'summary' => $request->summary,
            'content' => $request->content, // HTML Content
            'image' => $path,
            'author' => auth()->user()->name ?? 'Admin'
        ]);

        return redirect()->route('admin.news-list.index')->with('success', 'Berita berhasil diterbitkan.');
    }

    // --- 5. FORM EDIT ---
    public function edit($id)
    {
        $news = News::findOrFail($id);
        return Inertia::render('Admin/NewsList/Form', ['news' => $news]);
    }

    // --- 6. UPDATE DATA ---
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'published_at' => 'required|date',
            'summary' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteOldFile($news->image);
            $file = $request->file('image');
            $fileName = $this->generateSafeFileName($request->title, $file);
            $news->image = $this->manualUpload($file, 'news', $fileName);
        }

        $news->update([
            'title' => $request->title,
            // Update slug jika judul berubah (opsional, hati-hati SEO)
            'slug' => Str::slug($request->title) . '-' . time(), 
            'category' => $request->category,
            'published_at' => $request->published_at,
            'summary' => $request->summary,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.news-list.index')->with('success', 'Berita berhasil diperbarui.');
    }

    // --- 7. DELETE ---
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $this->deleteOldFile($news->image);
        $news->delete();
        return redirect()->back()->with('success', 'Berita dihapus.');
    }

    // ==========================================
    // PRIVATE HELPERS (ANTI GAGAL WINDOWS)
    // ==========================================
    private function generateSafeFileName($title, $file) {
        $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($title)));
        $cleanTitle = substr($cleanTitle, 0, 30);
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        return $cleanTitle . '-' . time() . '.' . $extension;
    }

    private function manualUpload($file, $folderName, $fileName) {
        $destinationPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderName);
        if (!file_exists($destinationPath)) mkdir($destinationPath, 0777, true);
        $file->move($destinationPath, $fileName);
        return '/storage/' . $folderName . '/' . $fileName;
    }

    private function deleteOldFile($dbPath) {
        if ($dbPath) {
            $relativePath = str_replace('/storage/', '', $dbPath);
            $relativePath = str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $absolutePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relativePath);
            if (file_exists($absolutePath) && !str_contains($dbPath, 'defaults/')) @unlink($absolutePath);
        }
    }
}