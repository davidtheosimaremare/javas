<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Service;
use App\Models\Statistic;
use App\Models\Project;
use App\Models\PageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HomeEditorController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Home/Index', [
            'sliders' => Slider::orderBy('order', 'asc')->get(),
            'services' => Service::orderBy('order', 'asc')->get(),
            'statistics' => Statistic::orderBy('order', 'asc')->get(),
            'pageSetting' => PageSetting::where('page_slug', 'home')->first(),
            'projects' => Project::orderBy('created_at', 'desc')->get()
        ]);
    }

    // =====================================================================
    // 1. UPDATE SLIDER
    // =====================================================================
    public function updateSlider(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $request->validate([
            'title' => 'required|string',
            'nav_title' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120', 
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // 1. SANITASI NAMA FILE (Ganti spasi dengan dash, hapus karakter aneh)
            $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($request->title)));
            $cleanTitle = substr($cleanTitle, 0, 30); // Batasi panjang

            // 2. DETEKSI EKSTENSI (LEBIH AMAN)
            // Kita coba ambil extension, kalau gagal kita default ke 'jpg'
            $extension = $file->getClientOriginalExtension();
            if (empty($extension)) {
                $extension = $file->guessExtension() ?? 'jpg';
            }

            // Gabungkan: judul-waktu.ext
            $fileName = $cleanTitle . '-' . time() . '.' . $extension;

            // 3. TENTUKAN PATH FISIK (GUNAKAN DIRECTORY_SEPARATOR)
            // Ini agar Windows pakai backslash (\), Linux pakai slash (/)
            $storagePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'sliders');

            // 4. BUAT FOLDER JIKA BELUM ADA
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0777, true);
            }

            // 5. PINDAHKAN FILE (MOVE)
            // destination: C:\xampp\...\sliders\nama.jpg
            $file->move($storagePath, $fileName);

            // 6. HAPUS GAMBAR LAMA (CLEANUP)
            if ($slider->image) {
                // Ubah URL path menjadi File Path sistem
                $oldRelativePath = str_replace('/storage/', '', $slider->image);
                // Fix slash arahnya biar sesuai OS
                $oldRelativePath = str_replace('/', DIRECTORY_SEPARATOR, $oldRelativePath);
                
                $oldAbsolutePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $oldRelativePath);

                // Hapus file jika ada dan bukan default
                if (file_exists($oldAbsolutePath) && !str_contains($slider->image, 'defaults/')) {
                    @unlink($oldAbsolutePath); // Pakai @ agar tidak error jika file sedang dikunci Windows
                }
            }

            // 7. SIMPAN KE DATABASE (GUNAKAN SLASH BIASA / UNTUK URL)
            // Browser selalu butuh forward slash (/), jangan backslash (\)
            $slider->image = '/storage/sliders/' . $fileName;
        }

        $slider->title = $request->title;
        $slider->nav_title = $request->nav_title;
        $slider->description = $request->description;
        $slider->link = $request->link ?? '#';
        $slider->is_active = $request->is_active;
        
        $slider->save();

        return redirect()->back()->with('success', 'Slider berhasil diperbarui.');
    }


  public function updateService(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|max:5120',
            'link'  => 'nullable|string', // Validasi Link
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // 1. Sanitasi Nama File
            $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($request->title)));
            $cleanTitle = substr($cleanTitle, 0, 30);
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $fileName = $cleanTitle . '-' . time() . '.' . $extension;

            // 2. Folder Fisik
            $destinationPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'services');

            // 3. Buat Folder
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // 4. Pindahkan File
            $file->move($destinationPath, $fileName);

            // 5. Hapus Lama
            if ($service->image) {
                $oldRelativePath = str_replace('/storage/', '', $service->image);
                $oldRelativePath = str_replace('/', DIRECTORY_SEPARATOR, $oldRelativePath);
                $oldAbsolutePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $oldRelativePath);

                if (file_exists($oldAbsolutePath) && !str_contains($service->image, 'defaults/')) {
                    @unlink($oldAbsolutePath);
                }
            }

            // 6. Simpan Path
            $service->image = '/storage/services/' . $fileName;
        }

        $service->title = $request->title;
        $service->description = $request->description;
        $service->color = $request->color;
        // --- SIMPAN LINK DISINI ---
        // Jika kosong, default ke '#' atau '/services/slug-judul' nanti
        $service->link = $request->link ?? '#'; 
        $service->is_active = $request->boolean('is_active');

        $service->save();

        return redirect()->back()->with('success', 'Layanan berhasil diperbarui.');
    }

    // =====================================================================
    // 3. UPDATE STATISTIC
    // =====================================================================
    public function updateStatistic(Request $request, $id)
    {
        $stat = Statistic::findOrFail($id);

        $request->validate([
            'value' => 'required',
            'label' => 'required|string',
            'image' => 'nullable|image|max:5120'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Hapus lama
            if ($stat->image && !str_contains($stat->image, 'defaults/')) {
                $oldPath = str_replace('/storage/', '', $stat->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Generate Nama Aman
            $rawLabel = str_replace(' ', '-', strtolower($request->label));
            $safeLabel = preg_replace('/[^A-Za-z0-9\-]/', '', $rawLabel);
            $fileName = substr($safeLabel, 0, 30) . '-' . time() . '.' . $file->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('stats')) {
                Storage::disk('public')->makeDirectory('stats');
            }

            $path = Storage::disk('public')->putFileAs('stats', $file, $fileName);

            if (!$path) {
                $path = $file->store('stats', 'public');
            }

            if ($path) {
                $stat->image = '/storage/' . $path;
            }
        }

        $stat->update([
            'value' => $request->value,
            'label' => $request->label,
            'is_active' => $request->boolean('is_active')
        ]);

        return redirect()->back()->with('success', 'Statistik berhasil diperbarui.');
    }

    // =====================================================================
    // 4. REORDER SLIDER (DRAG N DROP)
    // =====================================================================
    public function reorderSlider(Request $request)
    {
        $request->validate([
            'items' => 'required|array'
        ]);

        foreach ($request->items as $index => $item) {
            Slider::where('id', $item['id'])->update(['order' => $index + 1]);
        }

        return redirect()->back()->with('success', 'Urutan berhasil diperbarui!');
    }

    // ... method lainnya ...

    // --- 5. UPDATE SERVICE HEADER (Untuk Judul & Deskripsi) ---
   public function updateServiceHeader(Request $request)
    {
        $request->validate([
            'service_title' => 'required|string|max:255',
            'service_description' => 'required|string',
        ]);

        // FIX: Tambahkan array kedua untuk nilai default jika data belum ada
        $setting = \App\Models\PageSetting::firstOrCreate(
            ['page_slug' => 'home'], // Kondisi pencarian
            [
                // Data default yang wajib diisi jika baris baru dibuat
                'hero_title' => 'Selamat Datang di JBB', 
                'hero_bg_path' => null
            ]
        );

        // Update data Service
        $setting->service_title = $request->service_title;
        $setting->service_description = $request->service_description;
        
        $setting->save();

        return redirect()->back()->with('success', 'Header Service berhasil diperbarui!');
    }


    // --- 6. UPDATE STATS HEADER (JUDUL, DESKRIPSI & BACKGROUND) ---
    public function updateStatsHeader(Request $request)
    {
        $request->validate([
            'stats_title' => 'required|string|max:255',
            'stats_description' => 'required|string',
            'stats_bg_image' => 'nullable|image|max:5120', // Validasi Gambar
        ]);

        // Ambil atau buat data setting
        $setting = PageSetting::firstOrCreate(
            ['page_slug' => 'home'],
            ['hero_title' => 'Selamat Datang', 'hero_bg_path' => null]
        );

        // --- LOGIC UPLOAD BACKGROUND (METODE ANTI-GAGAL) ---
        if ($request->hasFile('stats_bg_image')) {
            $file = $request->file('stats_bg_image');

            // 1. Buat Nama File Bersih (SEO Friendly)
            // Ambil dari judul stats, bersihkan karakter aneh
            $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($request->stats_title)));
            $cleanName = substr($cleanName, 0, 30); 
            
            // Deteksi ekstensi manual agar tidak error
            $extension = $file->getClientOriginalExtension();
            if (empty($extension)) {
                $extension = $file->guessExtension() ?? 'jpg';
            }

            $fileName = $cleanName . '-bg-' . time() . '.' . $extension;

            // 2. Tentukan Lokasi Fisik (Folder 'stats')
            // Menggunakan storage_path agar path absolut terbaca Windows
            $destinationPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'stats');

            // 3. Buat Folder Manual Jika Belum Ada
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // 4. Pindahkan File (Native Move)
            $file->move($destinationPath, $fileName);

            // 5. Hapus Background Lama (Cleanup)
            if ($setting->stats_bg_image) {
                // Ubah URL path menjadi File Path fisik
                $oldRelativePath = str_replace('/storage/', '', $setting->stats_bg_image);
                $oldRelativePath = str_replace('/', DIRECTORY_SEPARATOR, $oldRelativePath);
                $oldAbsolutePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $oldRelativePath);

                // Hapus file
                if (file_exists($oldAbsolutePath)) {
                    @unlink($oldAbsolutePath);
                }
            }

            // 6. Simpan Path ke Database
            $setting->stats_bg_image = '/storage/stats/' . $fileName;
        }

        // Simpan Text
        $setting->stats_title = $request->stats_title;
        $setting->stats_description = $request->stats_description;
        
        $setting->save();

        return redirect()->back()->with('success', 'Header Statistik diperbarui.');
    }

    // --- 7. UPDATE PROJECT HEADER (Judul & Deskripsi) ---
    public function updateProjectHeader(Request $request)
    {
        $request->validate([
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
        ]);

        $setting = PageSetting::firstOrCreate(['page_slug' => 'home']);
        
        $setting->project_title = $request->project_title;
        $setting->project_description = $request->project_description;
        $setting->save();

        return redirect()->back()->with('success', 'Header Project diperbarui.');
    }

    public function toggleFeaturedProject($id)
    {
        $project = Project::findOrFail($id);
        
        // Balik nilai boolean (True jadi False, False jadi True)
        $project->is_featured = !$project->is_featured;
        $project->save();

        return redirect()->back()->with('success', 'Status Featured proyek berhasil diubah.');
    }
    
}