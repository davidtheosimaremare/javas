<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $profile = CompanyProfile::first();
        
        // Decode JSON Social Media agar bisa dibaca di Vue
        if($profile && $profile->social_media) {
            $profile->social_media = json_decode($profile->social_media, true);
        }

        return Inertia::render('Admin/CompanyProfile/Index', [
            'profile' => $profile
        ]);
    }

    public function update(Request $request)
    {
        $profile = CompanyProfile::firstOrNew();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_description' => 'nullable|string',
            'company_email' => 'required|email',
            'company_address' => 'required|string',
            'phone_number' => 'required|string',
            'logo_primary' => 'nullable|image|max:2048',
            'logo_secondary' => 'nullable|image|max:2048',
            'social_media' => 'nullable|array'
        ]);

        // Handle File Upload Logo Primary
        if ($request->hasFile('logo_primary')) {
            if ($profile->logo_primary) Storage::disk('public')->delete($profile->logo_primary);
            $profile->logo_primary = $request->file('logo_primary')->store('company', 'public');
        }

        // Handle File Upload Logo Secondary
        if ($request->hasFile('logo_secondary')) {
            if ($profile->logo_secondary) Storage::disk('public')->delete($profile->logo_secondary);
            $profile->logo_secondary = $request->file('logo_secondary')->store('company', 'public');
        }

        $profile->company_name = $validated['company_name'];
        $profile->company_description = $validated['company_description'];
        $profile->company_email = $validated['company_email'];
        $profile->company_address = $validated['company_address'];
        $profile->phone_number = $validated['phone_number'];
        
        // Simpan Social Media sebagai JSON
        $profile->social_media = json_encode($request->social_media);

        $profile->save();

        return redirect()->back()->with('success', 'Profil Perusahaan berhasil diperbarui.');
    }
}