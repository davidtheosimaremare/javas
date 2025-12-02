<?php

namespace App\Http\Middleware;
use App\Models\CompanyProfile; // <-- Import Model
use App\Models\Footer;     // <-- Import Model
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $profile = CompanyProfile::find(1);
        if ($profile) {
            // Pastikan social_media di-decode dari JSON
            $profile->social_media = json_decode($profile->social_media, true);
        }
        $footerLinks = Footer::orderBy('order', 'asc')->get()->groupBy('type');
        return [

            'globalData' => [
                'profile' => $profile,
                'footerLinks' => $footerLinks,
            ],

            ...parent::share($request),
            //
        ];
    }
}
