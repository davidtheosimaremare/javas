<?php

namespace App\Http\Controllers;

use App\Models\CoreValue;
use App\Models\JobVacancy;
use App\Models\PageSetting; // Ambil settingan hero juga
use Inertia\Inertia;

class CareerController extends Controller
{
    public function index()
    {
        // Ambil Data Halaman (Hero Image/Title)
        $pageSetting = PageSetting::where('page_slug', 'career')->first();

        // Ambil Data Core Values
        $values = CoreValue::orderBy('id', 'asc')->get();

        // Ambil Data Lowongan Kerja (Hanya yang aktif)
        $jobs = JobVacancy::where('is_active', true)
            ->latest()
            ->get();

        return Inertia::render('Career/Index', [
            'pageSetting' => $pageSetting,
            'values' => $values,
            'jobs' => $jobs
        ]);
    }
}