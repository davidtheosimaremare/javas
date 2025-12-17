<?php

use App\Http\Controllers\AboutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CompanyProfileController as CompanyProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HomeEditorController as HomeEditorController;
use App\Http\Controllers\Admin\CareerListController as CareerListController;
use App\Http\Controllers\Admin\CareerEditorController as CareerEditorController;
use App\Http\Controllers\Admin\NewsEditorController as NewsEditorController;
use App\Http\Controllers\Admin\ContactEditorController as ContactEditorController;
use App\Http\Controllers\Admin\ProjectEditorController as ProjectEditorController;
use App\Http\Controllers\Admin\NewsListController as NewsListController;
use App\Http\Controllers\Admin\AboutEditorController as AboutEditorController;
use Inertia\Inertia;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/career', [CareerController::class, 'index'])->name('career');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');




// --- GROUP ROUTE ADMIN ---
Route::prefix('admin')->group(function () {
    
    // Guest (Belum Login)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    });

    // Authenticated (Sudah Login)
    Route::middleware('auth')->group(function () {
        // Dashboard (Nanti kita buat)
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard/Index');
        })->name('admin.dashboard');

        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        // route auth end

        //admin dashboard route
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/company-profile', [CompanyProfileController::class, 'index'])->name('admin.company.index');
    Route::post('/company-profile', [CompanyProfileController::class, 'update'])->name('admin.company.update');

        // Home Editor Routes

    Route::post('/home-editor/slider/reorder', [HomeEditorController::class, 'reorderSlider']);
    Route::get('/home-editor', [HomeEditorController::class, 'index'])->name('admin.home.index');
    // Actions Update
    Route::post('/home-editor/slider/{id}', [HomeEditorController::class, 'updateSlider']);
    Route::post('/home-editor/service/{id}', [HomeEditorController::class, 'updateService']);
    Route::post('/home-editor/statistic/{id}', [HomeEditorController::class, 'updateStatistic']);
    
    Route::post('/home-editor/service-header', [HomeEditorController::class, 'updateServiceHeader']);
    Route::post('/home-editor/stats-header', [HomeEditorController::class, 'updateStatsHeader']);
    Route::post('/home-editor/project-header', [HomeEditorController::class, 'updateProjectHeader']);
    Route::post('/projects/{id}/toggle-featured', [HomeEditorController::class, 'toggleFeaturedProject']);

    //about editor routes

    Route::get('/about-editor', [App\Http\Controllers\Admin\AboutEditorController::class, 'index'])->name('admin.about.index');
    
    // Update Sections
    Route::post('/about-editor/page-setting', [App\Http\Controllers\Admin\AboutEditorController::class, 'updatePageSetting']);
    Route::post('/about-editor/content', [App\Http\Controllers\Admin\AboutEditorController::class, 'updateContent']);
    Route::post('/about-editor/quote', [App\Http\Controllers\Admin\AboutEditorController::class, 'updateQuoteSection']);
    
    // Visi Misi (VmItem)
    Route::post('/about-editor/misi', [App\Http\Controllers\Admin\AboutEditorController::class, 'storeMisi']);
    Route::post('/about-editor/misi/{id}', [App\Http\Controllers\Admin\AboutEditorController::class, 'updateMisi']);
    Route::delete('/about-editor/misi/{id}', [App\Http\Controllers\Admin\AboutEditorController::class, 'deleteMisi']);
    
    // Team
    // 1. Untuk Tambah Baru (POST)
    Route::post('/about-editor/leader', [AboutEditorController::class, 'storeLeader'])->name('about.leader.store');
    
    // 2. Untuk Edit/Update (PUT)
    Route::put('/about-editor/leader/{id}', [AboutEditorController::class, 'updateLeader'])->name('about.leader.update');
    
    // 3. Untuk Hapus (DELETE)
    Route::delete('/about-editor/leader/{id}', [AboutEditorController::class, 'destroyLeader'])->name('about.leader.destroy');




    // Gallery
    Route::post('/about-editor/gallery', [App\Http\Controllers\Admin\AboutEditorController::class, 'storeGallery']);
    Route::delete('/about-editor/gallery/{id}', [App\Http\Controllers\Admin\AboutEditorController::class, 'deleteGallery']);

    



    Route::get('/project-editor', [App\Http\Controllers\Admin\ProjectEditorController::class, 'index'])->name('admin.project.index');
    Route::post('/project-editor/page-setting', [App\Http\Controllers\Admin\ProjectEditorController::class, 'updatePageSetting']);




    Route::get('/career-editor', [App\Http\Controllers\Admin\CareerEditorController::class, 'index'])->name('admin.career.index');
    
    // Page Setting
    Route::post('/career-editor/page-setting', [App\Http\Controllers\Admin\CareerEditorController::class, 'updatePageSetting']);
    
    // Core Values (CRUD)
    Route::post('/career-editor/value', [App\Http\Controllers\Admin\CareerEditorController::class, 'storeValue']);
    Route::post('/career-editor/value/{id}', [App\Http\Controllers\Admin\CareerEditorController::class, 'updateValue']);
    Route::delete('/career-editor/value/{id}', [App\Http\Controllers\Admin\CareerEditorController::class, 'deleteValue']);


    Route::get('/news-editor', [App\Http\Controllers\Admin\NewsEditorController::class, 'index'])->name('admin.news.index');
    Route::post('/news-editor/page-setting', [App\Http\Controllers\Admin\NewsEditorController::class, 'updatePageSetting']);


    Route::get('/contact-editor', [App\Http\Controllers\Admin\ContactEditorController::class, 'index'])->name('admin.contact.index');
    Route::post('/contact-editor/page-setting', [App\Http\Controllers\Admin\ContactEditorController::class, 'updatePageSetting']);
    






    Route::get('/projects', [App\Http\Controllers\Admin\ProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/projects/create', [App\Http\Controllers\Admin\ProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/projects', [App\Http\Controllers\Admin\ProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/projects/{id}/edit', [App\Http\Controllers\Admin\ProjectController::class, 'edit'])->name('admin.projects.edit');
    // Gunakan POST untuk update karena ada file (method spoofing _method: PUT)
    Route::post('/projects/{id}', [App\Http\Controllers\Admin\ProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/projects/{id}', [App\Http\Controllers\Admin\ProjectController::class, 'destroy'])->name('admin.projects.destroy');
    
    


    Route::get('/news-list', [App\Http\Controllers\Admin\NewsListController::class, 'index'])->name('admin.news-list.index');
    Route::post('/news-list/header', [App\Http\Controllers\Admin\NewsListController::class, 'updateHeader']);
    
    // 2. CRUD Berita
    Route::get('/news-list/create', [App\Http\Controllers\Admin\NewsListController::class, 'create'])->name('admin.news-list.create');
    Route::post('/news-list', [App\Http\Controllers\Admin\NewsListController::class, 'store'])->name('admin.news-list.store');
    Route::get('/news-list/{id}/edit', [App\Http\Controllers\Admin\NewsListController::class, 'edit'])->name('admin.news-list.edit');
    Route::post('/news-list/{id}', [App\Http\Controllers\Admin\NewsListController::class, 'update'])->name('admin.news-list.update'); // Pakai POST untuk update file
    Route::delete('/news-list/{id}', [App\Http\Controllers\Admin\NewsListController::class, 'destroy'])->name('admin.news-list.destroy');




    Route::get('/career-list', [CareerListController::class, 'index'])->name('admin.career-list.index');
    
    // Page Setting
    Route::post('/career-list/page-setting', [CareerListController::class, 'updatePageSetting']);
    
    // Core Values
    Route::post('/career-list/value', [CareerListController::class, 'storeValue']);
    Route::post('/career-list/value/reorder', [CareerListController::class, 'reorderValue']); // Reorder
    Route::post('/career-list/value/{id}', [CareerListController::class, 'updateValue']);
    Route::delete('/career-list/value/{id}', [CareerListController::class, 'deleteValue']);

    // Jobs
    Route::post('/career-list/job', [CareerListController::class, 'storeJob']);
    Route::post('/career-list/job/{id}', [CareerListController::class, 'updateJob']);
    Route::delete('/career-list/job/{id}', [CareerListController::class, 'deleteJob']);
    //end dashboard



    


    });
});


