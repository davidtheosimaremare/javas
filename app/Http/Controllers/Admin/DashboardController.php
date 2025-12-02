<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\News;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Chart Proyek (Per Tahun)
        $projectChart = Project::select('year', DB::raw('count(*) as total'))
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->pluck('total', 'year');

        return Inertia::render('Admin/Dashboard/Index', [
            // A. Statistik Cards
            'stats' => [
                'total_projects' => Project::count(),
                'total_news' => News::count(),
                'completed_projects' => Project::where('status', 'Completed')->count(),
                'total_visitors' => 12540, // Dummy data (nanti bisa diganti real)
            ],
            
            // B. Data Chart Proyek (Untuk Bar Chart)
            'chartData' => [
                'labels' => $projectChart->keys()->map(fn($y) => (string)$y)->values(), // Pastikan jadi string array
                'values' => $projectChart->values(),
            ],

            // C. Data Grafik Pengunjung (Untuk Line Chart) - WAJIB ADA
            'visitorChart' => [
                'labels' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                'data' => [120, 190, 300, 250, 400, 320, 450]
            ],

            // D. Data Sumber Trafik - WAJIB ADA
            'trafficSources' => [
                ['name' => 'Google Search', 'count' => 5400, 'percent' => 45],
                ['name' => 'Direct (Langsung)', 'count' => 3200, 'percent' => 28],
                ['name' => 'WhatsApp Share', 'count' => 1800, 'percent' => 15],
                ['name' => 'Instagram/FB', 'count' => 1200, 'percent' => 12],
            ],

            // E. Tabel Proyek
            'latest_projects' => Project::latest()->take(5)->get()
        ]);
    }
}