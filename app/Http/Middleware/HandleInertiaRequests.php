<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (Auth::user()) {
            $user = new User();
            $authUser = Auth::user();
            $dummyUser = $user->forceFill($authUser->toArray())->decryptUser();

            $dummyUser->avatar = $authUser->getAttributes()['avatar'];
        }

        return array_merge(parent::share($request), [
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'authUser' => [
                'user' => $dummyUser ?? null,
                'settings' => $request->user()?->settings ?? null,
                'music' => $request->user()?->music ?? null,
            ]
        ]);
    }
}
