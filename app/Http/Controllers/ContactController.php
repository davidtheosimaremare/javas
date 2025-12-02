<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\PageSetting;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        // 1. Ambil Profil Perusahaan (Nama, Alamat, Email, No HP)
        $profile = CompanyProfile::first(); // Asumsi cuma ada 1 row

        // Decode JSON socmed jika ada (biar aman di Vue)
        if ($profile && $profile->social_media) {
            $profile->social_media = json_decode($profile->social_media, true);
        }

        // 2. Ambil Setting Halaman (Hero Image)
        $pageSetting = PageSetting::where('page_slug', 'contact')->first();

        return Inertia::render('Contact/Index', [
            'profile' => $profile,
            'pageSetting' => $pageSetting
        ]);
    }

    // Nanti kita buat method store() untuk handle submit form
   public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'service_type' => 'required|string', // Pastikan nama field di Vue snake_case
            'location' => 'required|string|max:255',
            'message' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg|max:5120', // Max 5MB
        ]);

        // 2. Handle File Upload (Jika ada)
        $filePath = null;
        if ($request->hasFile('file')) {
            // Simpan di folder 'inquiries' dalam 'public' disk
            $filePath = $request->file('file')->store('inquiries', 'public');
        }

        // 3. Simpan ke Database
        ContactMessage::create([
            'name' => $validated['name'],
            'company' => $validated['company'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'service_type' => $validated['service_type'],
            'location' => $validated['location'],
            'message' => $validated['message'],
            'file_path' => $filePath,
        ]);

        // 4. Redirect kembali dengan pesan sukses
        // Pesan ini akan otomatis dibaca oleh Inertia form.recentlySuccessful
        return redirect()->back()->with('success', 'Permintaan Anda berhasil dikirim!');
    }
}