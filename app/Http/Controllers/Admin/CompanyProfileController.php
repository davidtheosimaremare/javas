<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Footer;
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

        $footerLinks = Footer::orderBy('type')->orderBy('order', 'asc')->get();

        return Inertia::render('Admin/CompanyProfile/Index', [
            'profile' => $profile,
            'footerLinks' => $footerLinks
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
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
            'social_media' => 'nullable|array',
            'footer_text' => 'nullable|string'
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

        // Handle File Upload Favicon
        if ($request->hasFile('favicon')) {
            if ($profile->favicon) Storage::disk('public')->delete($profile->favicon);
            $profile->favicon = $request->file('favicon')->store('company', 'public');
        }

        $profile->company_name = $validated['company_name'];
        $profile->company_description = $validated['company_description'];
        $profile->company_email = $validated['company_email'];
        $profile->company_address = $validated['company_address'];
        $profile->phone_number = $validated['phone_number'];
        $profile->footer_text = $validated['footer_text'] ?? null;
        
        // Simpan Social Media sebagai JSON
        $profile->social_media = json_encode($request->social_media);

        $profile->save();

        return redirect()->back()->with('success', 'Profil Perusahaan berhasil diperbarui.');
    }

    public function storeFooterLink(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'url' => 'required|string',
        ]);

        $maxOrder = Footer::where('type', $request->type)->max('order') ?? 0;

        Footer::create([
            'type' => $request->type,
            'name' => $request->name,
            'url' => $request->url,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->back()->with('success', 'Tautan footer berhasil ditambahkan.');
    }

    public function updateFooterLink(Request $request, $id)
    {
        $link = Footer::findOrFail($id);

        $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'url' => 'required|string',
            'order' => 'integer',
        ]);

        $link->update($request->only('type', 'name', 'url', 'order'));

        return redirect()->back()->with('success', 'Tautan footer berhasil diperbarui.');
    }

    public function destroyFooterLink($id)
    {
        Footer::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Tautan footer berhasil dihapus.');
    }
}