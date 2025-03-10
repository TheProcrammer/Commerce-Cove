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
use App\Services\CartService;



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
     * This function authenticates the user, regenerates the session for security, 
     * checks the user's role, and redirects them accordingly.
     * If the user has items in their cart (stored in cookies), it moves them to the database.
     */
    public function store(LoginRequest $request, CartService $cartService): \Symfony\Component\HttpFoundation\Response
    {
        $request->authenticate(); // Authenticate the user

        $request->session()->regenerate();  // Regenerate the session to prevent session fixation attacks

        $user = Auth::user(); // Get the authenticated user

        // Allowing Admin and Vendor to go to admin panel but users access must go to dashboard.     
        $route = "/"; // Default route after login
        // Check if the user is an Admin or Vendor
        if ($user->hasAnyRole([RolesEnum::Admin, RolesEnum::Vendor])) {
            $cartService->moveCartItemsToDatabase($user->id); // Move cart items from cookies to the database
            return Inertia::location('/admin'); // Redirect to the admin panel
        // If the user is a regular User, set their dashboard route
        } else if ($user->hasRole([RolesEnum::User])) {
            $route = route("dashboard", absolute: false);
        }
        // Move cart items from cookies to the database for regular users as well
        $cartService->moveCartItemsToDatabase($user->id);
        
        // Makes the API Dynamic not just /admin only.
        // if ($user->hasAnyRole([RolesEnum::Admin, RolesEnum::Vendor])) {
        //     $route = $user->hasRole(RolesEnum::Vendor) ? '/vendor' : '/admin';
        //     return Inertia::location($route);
        // } else if ($user->hasRole(RolesEnum::User)) {
        //     $route = route("dashboard", absolute: false);
        // }        
        // Redirect to the intended page or fallback to the dashboard
        return redirect()->intended($route);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        Auth::logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect('/');
    }
}
