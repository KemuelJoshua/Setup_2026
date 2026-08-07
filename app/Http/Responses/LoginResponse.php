<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        // dd([
        //     'tenancy_initialized' => tenancy()->initialized,
        //     'tenant' => tenant(),
        //     'tenant_id' => tenant('id'),
        //     'host' => $request->getHost(),
        //     'guard' => auth()->getDefaultDriver(),
        //     'authenticated' => auth()->check(),
        //     'user' => auth()->user(),
        //     'session_id' => $request->session()->getId(),
        //     'session' => $request->session()->all(),
        //     'intended_url' => $request->session()->get('url.intended'),
        // ]);

        $redirect = tenancy()->initialized
            ? route('admin.dashboard', absolute: false)
            : route('central.tenants.index', absolute: false);

        return $request->wantsJson()
            ? response()->json([
                'two_factor' => false,
                'redirect' => $redirect,
            ])
            : redirect()->intended($redirect);
    }
}
