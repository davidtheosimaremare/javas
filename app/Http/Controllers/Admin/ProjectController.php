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
        return redirect()->back()->with('success', 'Proyek dihapus.');
    }

    // ... (Sisa private function dan helper tetap sama) ...
    private function validateProject($request) {
        return $request->validate([
            'title' => 'required|string',
            'overview' => 'required|string',
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
            foreach ($request->file('new_gallery_images') as $file) {
                $path = $this->uploadFile($file, 'projects/gallery');
                $project->galleries()->create(['image_path' => $path]);
            }
        }
    }

    private function uploadFile($file, $folder) {
        $fileName = Str::random(10) . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = storage_path('app/public/' . $folder);
        if (!file_exists($path)) mkdir($path, 0777, true);
        $file->move($path, $fileName);
        return '/storage/' . $folder . '/' . $fileName;
    }

    private function deleteFile($path) {
        if (!$path) return;
        $cleanPath = str_replace('/storage/', '', $path);
        $absPath = storage_path('app/public/' . $cleanPath);
        if (file_exists($absPath)) @unlink($absPath);
    }
    
    public function destroyGallery($id) { /* ... code lama ... */ }
    public function destroyTestimonial($id) { /* ... code lama ... */ }
    public function storeTestimonial(Request $request, $projectId) { /* ... code lama ... */ }
}