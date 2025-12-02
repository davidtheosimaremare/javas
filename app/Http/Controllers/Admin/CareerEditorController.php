<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use App\Models\CoreValue; // Pastikan Model CoreValue sudah ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CareerEditorController extends Controller
{
    public function index()
    {
        // 1. Ambil Page Setting (Hero)
        $pageSetting = PageSetting::firstOrCreate(
            ['page_slug' => 'career'],
            [
                'hero_title' => 'Karir & Budaya',
                'hero_bg_path' => null
            ]
        );

        // 2. Ambil Data Core Values
        $coreValues = CoreValue::orderBy('order', 'asc')->get();

        return Inertia::render('Admin/Career/Index', [
            'pageSetting' => $pageSetting,
            'coreValues' => $coreValues
        ]);
    }

    // =====================================================================
    // 1. UPDATE HERO PAGE SETTINGS
    // =====================================================================
    public function updatePageSetting(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_bg_path' => 'nullable|image|max:5120',
        ]);

        $setting = PageSetting::where('page_slug', 'career')->first();

        if ($request->hasFile('hero_bg_path')) {
            $this->deleteOldFile($setting->hero_bg_path);
            
            $file = $request->file('hero_bg_path');
            $fileName = $this->generateSafeFileName('hero-career', $file);
            
            // Simpan ke folder 'career'
            $setting->hero_bg_path = $this->manualUpload($file, 'career', $fileName);
        }

        $setting->hero_title = $request->hero_title;
        // Reset subtitle/quote jika ingin dibersihkan, atau biarkan
        $setting->save();

        return redirect()->back()->with('success', 'Hero Page diperbarui.');
    }

    // =====================================================================
    // 2. CORE VALUES (CRUD)
    // =====================================================================
    
    public function storeValue(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|max:5120', // Wajib ada saat create
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $this->generateSafeFileName($request->title, $file);
            // Simpan ke folder 'careers/values'
            $path = $this->manualUpload($file, 'careers/values', $fileName);
        }

        CoreValue::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $path,
            'order' => CoreValue::max('order') + 1
        ]);

        return redirect()->back()->with('success', 'Core Value ditambahkan.');
    }

    public function updateValue(Request $request, $id)
    {
        $value = CoreValue::findOrFail($id);

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteOldFile($value->image);

            $file = $request->file('image');
            $fileName = $this->generateSafeFileName($request->title, $file);
            $value->image = $this->manualUpload($file, 'careers/values', $fileName);
        }

        $value->update([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $request->order ?? $value->order
        ]);

        return redirect()->back()->with('success', 'Core Value diperbarui.');
    }

    public function deleteValue($id)
    {
        $value = CoreValue::findOrFail($id);
        $this->deleteOldFile($value->image);
        $value->delete();

        return redirect()->back()->with('success', 'Core Value dihapus.');
    }

    // =====================================================================
    // PRIVATE HELPERS (SAMA SEPERTI CONTROLLER SEBELUMNYA)
    // =====================================================================
    private function generateSafeFileName($title, $file) {
        $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($title)));
        $cleanTitle = substr($cleanTitle, 0, 30);
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        return $cleanTitle . '-' . time() . '.' . $extension;
    }

    private function manualUpload($file, $folderName, $fileName) {
        // Gunakan path fisik lengkap dengan DIRECTORY_SEPARATOR
        // folderName bisa berupa 'career' atau 'careers/values' (subfolder otomatis dibuat oleh mkdir recursive)
        
        // Ubah slash / menjadi separator OS (Windows pakai \)
        $folderNameOS = str_replace('/', DIRECTORY_SEPARATOR, $folderName);
        $destinationPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderNameOS);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true); // recursive true
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