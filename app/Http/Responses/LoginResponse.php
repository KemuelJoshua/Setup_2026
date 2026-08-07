<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
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
