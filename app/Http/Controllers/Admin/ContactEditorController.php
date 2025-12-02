<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ContactEditorController extends Controller
{
    public function index()
    {
        // Ambil setting halaman 'contact'
        $pageSetting = PageSetting::firstOrCreate(
            ['page_slug' => 'contact'],
            [
                'hero_title' => 'Hubungi Kami',
                'hero_bg_path' => null
            ]
        );

        return Inertia::render('Admin/Contact/Index', [
            'pageSetting' => $pageSetting
        ]);
    }

    public function updatePageSetting(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_bg_path' => 'nullable|image|max:5120',
        ]);

        $setting = PageSetting::where('page_slug', 'contact')->first();

        if ($request->hasFile('hero_bg_path')) {
            $this->deleteOldFile($setting->hero_bg_path);

            $file = $request->file('hero_bg_path');
            $fileName = $this->generateSafeFileName('hero-contact', $file);
            
            // Simpan ke folder 'contact'
            $setting->hero_bg_path = $this->manualUpload($file, 'contact', $fileName);
        }

        $setting->hero_title = $request->hero_title;
        $setting->save();

        return redirect()->back()->with('success', 'Hero Page Kontak diperbarui.');
    }

    // --- HELPERS (Copy-Paste Standar Kita) ---
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