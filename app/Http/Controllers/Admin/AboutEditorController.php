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
        
        $setting->save();

        return redirect()->back()->with('success', 'Hero & Setting diperbarui.');
    }

    // =====================================================================
    // 2. UPDATE BODY CONTENT (AboutContent)
    // =====================================================================
   public function updateContent(Request $request)
    {
        $content = AboutContent::firstOrFail();

        $request->validate([
            'page_title' => 'required|string',
            'content_html' => 'required|string',
            // 'quote' dihapus dari sini
        ]);

        $content->update([
            'page_title' => $request->page_title,
            'content_html' => $request->content_html,
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
   public function storeLeader(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('image')->store('team-images', 'public');

        Team::create([
            'name' => $request->name,
            'title' => $request->title,
            'image_url' => $path, // Pastikan kolom di database anda 'image_url'
        ]);

        return redirect()->back()->with('success', 'Anggota tim berhasil ditambahkan');
    }

    // --- UPDATE (EDIT DATA) ---
    public function updateLeader(Request $request, $id)
    {
        // Cari data berdasarkan ID, jika tidak ada akan return 404 (bukan 500)
        $leader = Team::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Nullable: user tidak wajib ganti foto
        ]);

        $leader->name = $request->name;
        $leader->title = $request->title;

        // Logic Ganti Foto
        if ($request->hasFile('image')) {
            // Hapus foto lama
            if ($leader->image_url && Storage::disk('public')->exists($leader->image_url)) {
                Storage::disk('public')->delete($leader->image_url);
            }
            // Upload foto baru
            $path = $request->file('image')->store('team-images', 'public');
            $leader->image_url = $path;
        }

        $leader->save();

        return redirect()->back()->with('success', 'Data tim berhasil diperbarui');
    }

    // --- DELETE (HAPUS) ---
    public function destroyLeader($id)
    {
        $leader = Team::findOrFail($id);
        
        if ($leader->image_url && Storage::disk('public')->exists($leader->image_url)) {
            Storage::disk('public')->delete($leader->image_url);
        }
        
        $leader->delete();
        return redirect()->back()->with('success', 'Anggota tim dihapus');
    }

    // =====================================================================
    // 5. MANAJEMEN GALLERY (GalleryItem)
    // =====================================================================
    public function storeGallery(Request $request)
{
    // 1. Validasi Input (Sesuaikan dengan nama field di Vue: 'image' dan 'caption')
    $request->validate([
        'image' => 'required|image|max:5120', // Ubah 'image_url' jadi 'image'
        'caption' => 'nullable|string|max:255', // Tambahkan validasi caption
    ]);

    // 2. Cek File pada input 'image'
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $fileName = $this->generateSafeFileName('gallery', $file);
        
        // Upload file
        $path = $this->manualUpload($file, 'about-gallery', $fileName);

        // 3. Simpan ke Database
        // Mapping: Input 'caption' -> Kolom 'caption'
        // Mapping: Path hasil upload -> Kolom 'image_url'
        GalleryItem::create([
            'image_url' => $path, 
            'caption' => $request->caption, // Masukkan caption ke database
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


    public function updateQuoteSection(Request $request)
    {
        $content = AboutContent::firstOrFail();

        $request->validate([
            'section_subtitle' => 'nullable|string',
            'quote' => 'required|string', // Quote divalidasi di sini
            'quote_explanation_text' => 'nullable|string',
        ]);

        $content->update([
            'section_subtitle' => $request->section_subtitle,
            'quote' => $request->quote, // Update quote di sini
            'quote_explanation_text' => $request->quote_explanation_text,
        ]);

        return redirect()->back()->with('success', 'Quote & Subtitle diperbarui.');
    }
}