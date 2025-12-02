<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class NewsEditorController extends Controller
{
    public function index()
    {
        // Ambil atau buat setting untuk halaman 'news'
        $pageSetting = PageSetting::firstOrCreate(
            ['page_slug' => 'news'],
            [
                'hero_title' => 'Berita & Artikel',
                'hero_bg_path' => null
            ]
        );

        return Inertia::render('Admin/News/Index', [
            'pageSetting' => $pageSetting
        ]);
    }

    // --- UPDATE PAGE SETTING (HERO) ---
    public function updatePageSetting(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_bg_path' => 'nullable|image|max:5120', // Max 5MB
        ]);

        $setting = PageSetting::where('page_slug', 'news')->first();

        // Logic Upload Gambar (Metode Native Move)
        if ($request->hasFile('hero_bg_path')) {
            $this->deleteOldFile($setting->hero_bg_path);

            $file = $request->file('hero_bg_path');
            $fileName = $this->generateSafeFileName('hero-news', $file);
            
            // Simpan ke folder 'news'
            $setting->hero_bg_path = $this->manualUpload($file, 'news', $fileName);
        }

        $setting->hero_title = $request->hero_title;
        $setting->save();

        return redirect()->back()->with('success', 'Hero Page Berita berhasil diperbarui.');
    }

    // =====================================================================
    // PRIVATE HELPERS (Copy Paste dari Controller sebelumnya agar mandiri)
    // =====================================================================
    
    private function generateSafeFileName($prefix, $file) {
        $ext = $file->getClientOriginalExtension();
        if (empty($ext)) $ext = $file->guessExtension() ?? 'jpg';
        return $prefix . '-' . time() . '.' . $ext;
    }

    private function manualUpload($file, $folderName, $fileName) {
        $destinationPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderName);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $file->move($destinationPath, $fileName);
        return '/storage/' . $folderName . '/' . $fileName;
    }

    private function deleteOldFile($dbPath) {
        if ($dbPath) {
            $relativePath = str_replace('/storage/', '', $dbPath);
            $relativePath = str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $absolutePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relativePath);
            
            if (file_exists($absolutePath) && !str_contains($dbPath, 'defaults/')) {
                @unlink($absolutePath);
            }
        }
    }
}