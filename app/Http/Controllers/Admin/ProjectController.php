<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectGallery;
use App\Models\ProjectTestimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    // --- 1. LIST PROYEK (Update Path View) ---
    public function index()
    {
        $projects = Project::orderBy('created_at', 'desc')->paginate(10);
        
        // Ganti path view ke 'Admin/ProjectListing/Index'
        return Inertia::render('Admin/ProjectListing/Index', [
            'projects' => $projects
        ]);
    }

    // --- 2. HALAMAN CREATE (Update Path View) ---
    public function create()
    {
        // Ganti path view ke 'Admin/ProjectListing/Form'
        return Inertia::render('Admin/ProjectListing/Form');
    }

    // --- 3. HALAMAN EDIT (Update Path View) ---
    public function edit($id)
    {
        $project = Project::with(['galleries', 'testimonials'])->findOrFail($id);
        
        // Ganti path view ke 'Admin/ProjectListing/Form'
        return Inertia::render('Admin/ProjectListing/Form', [
            'project' => $project
        ]);
    }

    // ... (Method store, update, destroy, dll TETAP SAMA, tidak perlu diubah) ...
    // ... Karena logic database dan route tidak berubah, hanya tampilan view saja ...
    
    public function store(Request $request)
    {
        $request->validate([
            'hero_image' => 'nullable|image|max:10240'
        ], [
            'hero_image.max' => 'Kapasitas gambar tidak boleh melebihi 10MB.',
            'hero_image.uploaded' => 'Gagal mengunggah gambar. Pastikan kapasitas gambar tidak melebihi 10MB.',
            'hero_image.image' => 'File yang diunggah harus berupa gambar.'
        ]);
        
        $data = $this->validateProject($request);
        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $this->uploadFile($request->file('hero_image'), 'projects/hero');
        }
        $data['slug'] = Str::slug($data['title']);
        $project = Project::create($data);
        $this->handleRelations($request, $project);
        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $request->validate([
            'hero_image' => 'nullable|image|max:10240'
        ], [
            'hero_image.max' => 'Kapasitas gambar tidak boleh melebihi 10MB.',
            'hero_image.uploaded' => 'Gagal mengunggah gambar. Pastikan kapasitas gambar tidak melebihi 10MB.',
            'hero_image.image' => 'File yang diunggah harus berupa gambar.'
        ]);

        $data = $this->validateProject($request);
        if ($request->hasFile('hero_image')) {
            $this->deleteFile($project->hero_image);
            $data['hero_image'] = $this->uploadFile($request->file('hero_image'), 'projects/hero');
        }
        $data['slug'] = Str::slug($data['title']);
        $project->update($data);
        $this->handleRelations($request, $project);
        return redirect()->route('admin.projects.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $project = Project::with(['galleries', 'testimonials'])->findOrFail($id);
        $this->deleteFile($project->hero_image);
        foreach($project->galleries as $gal) $this->deleteFile($gal->image_path);
        foreach($project->testimonials as $testi) $this->deleteFile($testi->avatar);
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Proyek dihapus.');
    }

    // ... (Sisa private function dan helper tetap sama) ...
    private function validateProject($request) {
        return $request->validate([
            'title' => 'required|string',
            'overview' => 'nullable|string',
            'client' => 'nullable|string',
            'location' => 'nullable|string',
            'category' => 'nullable|string',
            'year' => 'nullable|string',
            'status' => 'required|string',
            'scope' => 'nullable|string',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'map_area' => 'nullable|string',
            'coord_top' => 'nullable|string',
            'coord_left' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
    }

    private function handleRelations($request, $project) {
        if ($request->hasFile('new_gallery_images')) {
            $files = $request->file('new_gallery_images');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $path = $this->uploadFile($file, 'projects/gallery');
                        $project->galleries()->create(['image_path' => $path]);
                    }
                }
            }
        }
    }

    private function uploadFile($file, $folder) {
        $fileName = Str::random(10) . '-' . time() . '.' . $file->getClientOriginalExtension();
        
        if (config('filesystems.disks.public.driver') === 's3') {
            $path = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs($folder, $file, $fileName);
            return '/storage/' . $path;
        }

        $path = storage_path('app/public/' . $folder);
        if (!file_exists($path)) mkdir($path, 0777, true);
        $file->move($path, $fileName);
        return '/storage/' . $folder . '/' . $fileName;
    }

    private function deleteFile($path) {
        if (!$path) return;
        $cleanPath = str_replace('/storage/', '', $path);
        
        if (config('filesystems.disks.public.driver') === 's3') {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanPath);
            }
            return;
        }

        $absPath = storage_path('app/public/' . $cleanPath);
        if (file_exists($absPath)) @unlink($absPath);
    }
    
    public function destroyGallery($id) {
        $gallery = ProjectGallery::findOrFail($id);
        $this->deleteFile($gallery->image_path);
        $gallery->delete();
        return redirect()->back()->with('success', 'Foto galeri dihapus.');
    }

    public function destroyTestimonial($id) {
        $testi = ProjectTestimonial::findOrFail($id);
        $this->deleteFile($testi->avatar);
        $testi->delete();
        return redirect()->back()->with('success', 'Testimoni dihapus.');
    }

    public function storeTestimonial(Request $request, $projectId) {
        $request->validate([
            'name' => 'required|string',
            'position' => 'required|string',
            'quote' => 'required|string',
            'avatar' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name', 'position', 'quote']);
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->uploadFile($request->file('avatar'), 'projects/testimonials');
        }

        ProjectTestimonial::create(array_merge($data, ['project_id' => $projectId]));
        return redirect()->back()->with('success', 'Testimoni ditambahkan.');
    }
}