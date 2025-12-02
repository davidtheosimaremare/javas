<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Models\PageSetting;
use App\Models\VmItem;      // Model Visi Misi
use App\Models\Team;        // Model Leadership
use App\Models\GalleryItem; // Model Gallery
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AboutEditorController extends Controller
{
    public function index()
    {
        // 1. Pastikan data PageSetting ada (untuk Hero)
        $pageSetting = PageSetting::firstOrCreate(
            ['page_slug' => 'about'],
            ['hero_title' => 'Tentang Kami']
        );

        // 2. Pastikan data AboutContent ada (untuk Body)
        $aboutContent = AboutContent::firstOrCreate([], [
            'page_title' => 'Sekilas Tentang JBB',
            'content_html' => '<p>Konten default...</p>',
            'quote' => 'Excellence in Execution',
        ]);

        return Inertia::render('Admin/About/Index', [
            'pageSetting' => $pageSetting,
            'aboutContent' => $aboutContent,
            'misiList' => VmItem::orderBy('order', 'asc')->get(),
            'leadershipTeam' => Team::orderBy('order', 'asc')->get(),
            'galleryImages' => GalleryItem::orderBy('order', 'asc')->get(),
        ]);
    }

    // =====================================================================
    // HELPER PRIVATE (Sama seperti HomeEditor agar Upload Aman)
    // =====================================================================
    private function generateSafeFileName($prefix, $file) {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
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

    // =====================================================================
    // 1. UPDATE HERO & PAGE SETTINGS
    // =====================================================================
    public function updatePageSetting(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string',
            'section_subtitle' => 'nullable|string',
            'quote_explanation_text' => 'nullable|string',
            'hero_bg_path' => 'nullable|image|max:5120',
        ]);

        $setting = PageSetting::where('page_slug', 'about')->first();

        if ($request->hasFile('hero_bg_path')) {
            $this->deleteOldFile($setting->hero_bg_path);
            $file = $request->file('hero_bg_path');
            $fileName = $this->generateSafeFileName('hero-about', $file);
            $setting->hero_bg_path = $this->manualUpload($file, 'about', $fileName);
        }

        $setting->hero_title = $request->hero_title;
        $setting->section_subtitle = $request->section_subtitle; // Pastikan kolom ini ada di migration page_settings
        $setting->quote_explanation_text = $request->quote_explanation_text; // Pastikan kolom ini ada
        
        $setting->save();

        return redirect()->back()->with('success', 'Hero & Setting diperbarui.');
    }

    // =====================================================================
    // 2. UPDATE BODY CONTENT (AboutContent)
    // =====================================================================
    public function updateContent(Request $request)
    {
        $content = AboutContent::first();

        $request->validate([
            'page_title' => 'required|string',
            'content_html' => 'required|string',
            'quote' => 'required|string',
        ]);

        $content->update([
            'page_title' => $request->page_title,
            'content_html' => $request->content_html,
            'quote' => $request->quote,
        ]);

        return redirect()->back()->with('success', 'Konten utama diperbarui.');
    }

    // =====================================================================
    // 3. MANAJEMEN VISI & MISI (VmItem)
    // =====================================================================
    public function storeMisi(Request $request)
    {
        $request->validate([
            'type' => 'required|in:visi,misi',
            'content' => 'required|string'
        ]);

        VmItem::create([
            'type' => $request->type,
            'content' => $request->content,
            'order' => VmItem::max('order') + 1
        ]);

        return redirect()->back()->with('success', 'Item ditambahkan.');
    }

    public function updateMisi(Request $request, $id)
    {
        $item = VmItem::findOrFail($id);
        $item->update($request->all());
        return redirect()->back()->with('success', 'Item diperbarui.');
    }

    public function deleteMisi($id)
    {
        VmItem::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Item dihapus.');
    }

    // =====================================================================
    // 4. MANAJEMEN TEAM (Leadership)
    // =====================================================================
    public function storeTeam(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'title' => 'required|string', // Jabatan
            'image_url' => 'required|image|max:5120'
        ]);

        $path = null;
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $fileName = $this->generateSafeFileName('team', $file);
            $path = $this->manualUpload($file, 'teams', $fileName);
        }

        Team::create([
            'name' => $request->name,
            'title' => $request->title,
            'image_url' => $path,
            'order' => Team::max('order') + 1
        ]);

        return redirect()->back()->with('success', 'Anggota tim ditambahkan.');
    }

    public function updateTeam(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        if ($request->hasFile('image_url')) {
            $this->deleteOldFile($team->image_url);
            $file = $request->file('image_url');
            $fileName = $this->generateSafeFileName('team', $file);
            $team->image_url = $this->manualUpload($file, 'teams', $fileName);
        }

        $team->name = $request->name;
        $team->title = $request->title;
        $team->save();

        return redirect()->back()->with('success', 'Data tim diperbarui.');
    }

    public function deleteTeam($id)
    {
        $team = Team::findOrFail($id);
        $this->deleteOldFile($team->image_url);
        $team->delete();
        return redirect()->back();
    }

    // =====================================================================
    // 5. MANAJEMEN GALLERY (GalleryItem)
    // =====================================================================
    public function storeGallery(Request $request)
    {
        $request->validate(['image_url' => 'required|image|max:5120']);

        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $fileName = $this->generateSafeFileName('gallery', $file);
            $path = $this->manualUpload($file, 'about-gallery', $fileName);

            GalleryItem::create([
                'image_url' => $path,
                'order' => GalleryItem::max('order') + 1
            ]);
        }
        return redirect()->back()->with('success', 'Foto galeri ditambahkan.');
    }

    public function deleteGallery($id)
    {
        $gallery = GalleryItem::findOrFail($id);
        $this->deleteOldFile($gallery->image_url);
        $gallery->delete();
        return redirect()->back();
    }
}