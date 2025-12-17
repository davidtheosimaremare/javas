<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectEditorController extends Controller
{
    public function index()
    {
        // Ambil setting untuk halaman 'projects'
        // Kita tambahkan default value untuk kolom baru agar tidak error saat pertama kali load
        $pageSetting = PageSetting::firstOrCreate(
            ['page_slug' => 'projects'],
            [
                'hero_title' => 'Proyek & Referensi',
                'hero_bg_path' => null,
                // Default value untuk field baru
                'project_title' => 'Daftar Proyek Terkini',
                'project_description' => 'Menampilkan hasil karya profesional JBB Group',
                'map_title' => 'Peta Sebaran Proyek',
                'map_description' => 'Jangkauan layanan kami di seluruh Indonesia',
            ]
        );

        return Inertia::render('Admin/Project/Index', [
            'pageSetting' => $pageSetting
        ]);
    }

    // --- UPDATE PAGE SETTING (HERO + TEXT SECTIONS) ---
    public function updatePageSetting(Request $request)
    {
        $request->validate([
            // Validasi Hero
            'hero_title' => 'required|string|max:255',
            'hero_bg_path' => 'nullable|image|max:5120', // Max 5MB

            // Validasi Text Section Proyek
            'project_title' => 'nullable|string|max:255',
            'project_description' => 'nullable|string|max:1000', // Agak panjang buat deskripsi

            // Validasi Text Section Peta
            'map_title' => 'nullable|string|max:255',
            'map_description' => 'nullable|string|max:1000',
        ]);

        $setting = PageSetting::where('page_slug', 'projects')->firstOrFail();

        // 1. Logic Upload Gambar (Metode Native Move - Anti Gagal)
        if ($request->hasFile('hero_bg_path')) {
            // Hapus file lama
            $this->deleteOldFile($setting->hero_bg_path);

            // Upload baru
            $file = $request->file('hero_bg_path');
            $fileName = $this->generateSafeFileName('hero-projects', $file);
            
            // Simpan ke folder 'projects'
            $setting->hero_bg_path = $this->manualUpload($file, 'projects', $fileName);
        }

        // 2. Simpan Data Text Hero
        $setting->hero_title = $request->hero_title;

        // 3. Simpan Data Text Baru (Project & Map)
        $setting->project_title = $request->project_title;
        $setting->project_description = $request->project_description;
        $setting->map_title = $request->map_title;
        $setting->map_description = $request->map_description;

        $setting->save();

        return redirect()->back()->with('success', 'Pengaturan halaman proyek berhasil diperbarui.');
    }

    // =====================================================================
    // PRIVATE HELPERS
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