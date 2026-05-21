<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return Inertia::render('Admin/Page/Index', [
            'pages' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Page/Form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'hero_image' => 'nullable|image|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $path = null;
        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');
            $fileName = $this->generateSafeFileName($request->title, $file);
            $path = $this->manualUpload($file, 'pages', $fileName);
        }

        Page::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'hero_image' => $path,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil dibuat.');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return Inertia::render('Admin/Page/Form', ['page' => $page]);
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'hero_image' => 'nullable|image|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('hero_image')) {
            $this->deleteOldFile($page->hero_image);
            $file = $request->file('hero_image');
            $fileName = $this->generateSafeFileName($request->title, $file);
            $page->hero_image = $this->manualUpload($file, 'pages', $fileName);
        }

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $this->deleteOldFile($page->hero_image);
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Halaman dihapus.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Str::random(10) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $this->manualUpload($file, 'pages/editor', $fileName);
            return response()->json(['url' => $path]);
        }
        return response()->json(['error' => 'No image provided'], 400);
    }

    private function generateSafeFileName($title, $file) {
        $cleanTitle = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($title)));
        $cleanTitle = substr($cleanTitle, 0, 30);
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        return $cleanTitle . '-' . time() . '.' . $extension;
    }

    private function manualUpload($file, $folderName, $fileName) {
        if (config('filesystems.disks.public.driver') === 's3') {
            $path = Storage::disk('public')->putFileAs($folderName, $file, $fileName);
            return '/storage/' . $path;
        }

        $destinationPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderName);
        if (!file_exists($destinationPath)) mkdir($destinationPath, 0777, true);
        $file->move($destinationPath, $fileName);
        return '/storage/' . $folderName . '/' . $fileName;
    }

    private function deleteOldFile($dbPath) {
        if ($dbPath) {
            $relativePath = str_replace('/storage/', '', $dbPath);
            
            if (config('filesystems.disks.public.driver') === 's3') {
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
                return;
            }

            $relativePath = str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $absolutePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relativePath);
            if (file_exists($absolutePath) && !str_contains($dbPath, 'defaults/')) @unlink($absolutePath);
        }
    }
}
