<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Http\Resources\AuthUserResource;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

   
    //
    public function share(Request $request): array
    {
        $cartService = app(CartService::class);
        $totalQuantity = $cartService->getTotalQuantity();
        $totalPrice = $cartService->getTotalPrice();
        $cartItems = $cartService->getCartItems();

        $departments = Department::published()
            ->with('categories')
            ->get();

        // dd($totalPrice,$totalQuantity,$cartItems);

        return [
            ...parent::share($request),
            'appName' => config('app.name'),
            'csrf_token' => csrf_token(),
            'auth' => [
                'user' => $request->user() ? new AuthUserResource($request->user()): null,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            //
            'success' => [
                'message' => session('success'),
                'time' => microtime(true),
            ],
            'error' => session('error'),
            'totalPrice'=>$totalPrice,
            'totalQuantity'=>$totalQuantity,
            // 'cartItem' => $cartItems,
            'miniCartItems' =>$cartItems,
            'departments' => DepartmentResource::collection($departments)->collection->toArray(),
            'keyword' => $request->query('keyword')
        ];
    }
}
