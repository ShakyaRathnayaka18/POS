<?php

namespace App\Http\Responses;

use App\Enums\RolesEnum;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();

        // Role-based redirects
        if ($user->hasRole(RolesEnum::CASHIER->value)) {
            return redirect()->intended('/cashier');
        }

        if ($user->hasRole(RolesEnum::STOCK_CLERK->value)) {
            return redirect()->intended('/products');
        }

        if ($user->hasRole(RolesEnum::ACCOUNTANT->value)) {
            return redirect()->intended('/reports');
        }

        // Default redirect for Admin, Manager, and Super Admin
        return redirect()->intended('/cashier');
    }
}
