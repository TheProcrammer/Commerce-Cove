<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;



class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Allowing Admin and Vendor to go to admin panel but users access must go to dashboard.     
        $route = "/";
        if ($user->hasAnyRole([RolesEnum::Admin, RolesEnum::Vendor])) {
            return Inertia::location('/admin');
        } else if ($user->hasRole([RolesEnum::User])) {
            $route = route("dashboard", absolute: false);
        }

        // Makes the API Dynamic not just /admin only.
        // if ($user->hasAnyRole([RolesEnum::Admin, RolesEnum::Vendor])) {
        //     $route = $user->hasRole(RolesEnum::Vendor) ? '/vendor' : '/admin';
        //     return Inertia::location($route);
        // } else if ($user->hasRole(RolesEnum::User)) {
        //     $route = route("dashboard", absolute: false);
        // }        

        return redirect()->intended($route);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
