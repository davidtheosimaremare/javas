<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use App\Models\CoreValue; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CareerEditorController extends Controller
{
    public function index()
    {
        // 1. Ambil Page Setting (Hero + Text Sections)
        $pageSetting = PageSetting::firstOrCreate(
            ['page_slug' => 'career'],
            [
                // Default Hero
                'hero_title' => 'Karir & Budaya',
                'hero_bg_path' => null,
                
                // Default Intro Section
                'career_intro_title' => 'Bergabung Bersama Keluarga Besar JBB',
                'career_intro_desc' => 'Kami percaya bahwa SDM adalah aset terbesar perusahaan. Di JBB, kami tidak hanya membangun infrastruktur listrik, tetapi juga membangun masa depan talenta-talenta terbaik bangsa.',
                
                // Default Values Section
                'career_values_title' => 'Nilai Kerja Kami',
                'career_values_subtitle' => 'Pondasi yang membuat kami terus bertumbuh dan dipercaya',
                
                // Default Jobs Section
                'career_jobs_title' => 'Posisi Tersedia',
                'career_jobs_subtitle' => 'Temukan peran yang sesuai dengan keahlian dan minat Anda',
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
    // 1. UPDATE PAGE SETTINGS (HERO + TEXT SECTIONS)
    // =====================================================================
    public function updatePageSetting(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_bg_path' => 'nullable|image|max:5120',
            
            // Validasi Text Tambahan
            'career_intro_title' => 'nullable|string|max:255',
            'career_intro_desc' => 'nullable|string',
            'career_values_title' => 'nullable|string|max:255',
            'career_values_subtitle' => 'nullable|string|max:255',
            'career_jobs_title' => 'nullable|string|max:255',
            'career_jobs_subtitle' => 'nullable|string|max:255',
        ]);

        $setting = PageSetting::where('page_slug', 'career')->firstOrFail();

        // Logic Upload Gambar Hero
        if ($request->hasFile('hero_bg_path')) {
            $this->deleteOldFile($setting->hero_bg_path);
            $file = $request->file('hero_bg_path');
            $fileName = $this->generateSafeFileName('hero-career', $file);
            $setting->hero_bg_path = $this->manualUpload($file, 'career', $fileName);
        }

        // Simpan Semua Text
        $setting->hero_title = $request->hero_title;
        $setting->career_intro_title = $request->career_intro_title;
        $setting->career_intro_desc = $request->career_intro_desc;
        $setting->career_values_title = $request->career_values_title;
        $setting->career_values_subtitle = $request->career_values_subtitle;
        $setting->career_jobs_title = $request->career_jobs_title;
        $setting->career_jobs_subtitle = $request->career_jobs_subtitle;

        $setting->save();

        return redirect()->back()->with('success', 'Halaman karir berhasil diperbarui.');
    }

    // =====================================================================
    // 2. CORE VALUES (CRUD)
    // =====================================================================
    
    public function storeValue(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|max:5120', // Wajib ada saat create
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $this->generateSafeFileName('value-' . $request->title, $file);
            $path = $this->manualUpload($file, 'career/values', $fileName);
        }

        CoreValue::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_url' => $path, // Pastikan nama kolom di DB 'image_url' atau sesuaikan
            'order' => CoreValue::max('order') + 1
        ]);

        return redirect()->back()->with('success', 'Core Value ditambahkan.');
    }

    public function updateValue(Request $request, $id)
    {
        $value = CoreValue::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteOldFile($value->image_url); // Sesuaikan nama kolom

            $file = $request->file('image');
            $fileName = $this->generateSafeFileName('value-' . $request->title, $file);
            $value->image_url = $this->manualUpload($file, 'career/values', $fileName);
        }

        $value->title = $request->title;
        $value->description = $request->description;
        $value->save();

        return redirect()->back()->with('success', 'Core Value diperbarui.');
    }

    public function deleteValue($id)
    {
        $value = CoreValue::findOrFail($id);
        $this->deleteOldFile($value->image_url); // Sesuaikan nama kolom
        $value->delete();

        return redirect()->back()->with('success', 'Core Value dihapus.');
    }

    // =====================================================================
    // PRIVATE HELPERS
    // =====================================================================
    private function generateSafeFileName($title, $file) {
        $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($title)));
        $cleanTitle = substr($cleanTitle, 0, 30);
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        return $cleanTitle . '-' . time() . '.' . $extension;
    }

    private function manualUpload($file, $folderName, $fileName) {
        $folderNameOS = str_replace('/', DIRECTORY_SEPARATOR, $folderName);
        $destinationPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderNameOS);

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



    // edit untuk core values 



    
}