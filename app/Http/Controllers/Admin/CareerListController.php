<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use App\Models\CoreValue;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CareerListController extends Controller
{
    public function index()
    {
        // 1. Ambil Page Setting (Hero)
        $pageSetting = PageSetting::firstOrCreate(
            ['page_slug' => 'career'],
            ['hero_title' => 'Karir & Budaya', 'hero_bg_path' => null]
        );

        // 2. Ambil Data Core Values & Jobs
        $coreValues = CoreValue::orderBy('order', 'asc')->get();
        $jobs = JobVacancy::orderBy('created_at', 'desc')->get();

        // RENDER KE FOLDER BARU: CareerList
        return Inertia::render('Admin/CareerList/Index', [
            'pageSetting' => $pageSetting,
            'coreValues' => $coreValues,
            'jobs' => $jobs
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
            $setting->hero_bg_path = $this->manualUpload($file, 'career', $fileName);
        }

        $setting->hero_title = $request->hero_title;
        $setting->save();

        return redirect()->back()->with('success', 'Hero Page diperbarui.');
    }

    // =====================================================================
    // 2. CORE VALUES (CRUD)
    // =====================================================================
    public function storeValue(Request $request)
    {
        if (CoreValue::count() >= 4) return redirect()->back()->withErrors(['msg' => 'Maksimal 4 Core Values.']);

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $this->generateSafeFileName($request->title, $file);
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
        $request->validate(['title' => 'required', 'description' => 'required', 'image' => 'nullable|image|max:5120']);

        if ($request->hasFile('image')) {
            $this->deleteOldFile($value->image);
            $file = $request->file('image');
            $fileName = $this->generateSafeFileName($request->title, $file);
            $value->image = $this->manualUpload($file, 'careers/values', $fileName);
        }

        $value->update(['title' => $request->title, 'description' => $request->description]);
        return redirect()->back()->with('success', 'Core Value diperbarui.');
    }

    public function deleteValue($id)
    {
        $value = CoreValue::findOrFail($id);
        $this->deleteOldFile($value->image);
        $value->delete();
        return redirect()->back();
    }

    public function reorderValue(Request $request)
    {
        $request->validate(['items' => 'required|array']);
        foreach ($request->items as $index => $item) {
            CoreValue::where('id', $item['id'])->update(['order' => $index + 1]);
        }
        return redirect()->back();
    }

    // =====================================================================
    // 3. JOB VACANCIES (CRUD)
    // =====================================================================
    public function storeJob(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string', 'department' => 'required|string',
            'type' => 'required|string', 'location' => 'required|string',
            'experience' => 'required|string', 'description' => 'required|string',
            'is_active' => 'boolean'
        ]);
        JobVacancy::create($data);
        return redirect()->back()->with('success', 'Lowongan ditambahkan.');
    }

    public function updateJob(Request $request, $id)
    {
        $job = JobVacancy::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string', 'department' => 'required|string',
            'type' => 'required|string', 'location' => 'required|string',
            'experience' => 'required|string', 'description' => 'required|string',
            'is_active' => 'boolean'
        ]);
        $job->update($data);
        return redirect()->back()->with('success', 'Lowongan diperbarui.');
    }

    public function deleteJob($id)
    {
        JobVacancy::findOrFail($id)->delete();
        return redirect()->back();
    }

    // =====================================================================
    // PRIVATE HELPERS (Native Move)
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
        if (!file_exists($destinationPath)) mkdir($destinationPath, 0777, true);
        $file->move($destinationPath, $fileName);
        return '/storage/' . $folderName . '/' . $fileName;
    }

    private function deleteOldFile($dbPath) {
        if ($dbPath) {
            $relativePath = str_replace('/storage/', '', $dbPath);
            $relativePath = str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $absolutePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relativePath);
            if (file_exists($absolutePath) && !str_contains($dbPath, 'defaults/')) @unlink($absolutePath);
        }
    }
}