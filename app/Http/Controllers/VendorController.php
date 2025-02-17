<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Enums\VendorStatusEnum;
use App\Enums\RolesEnum;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Http\Resources\ProductListResource;
use Inertia\Inertia;

class VendorController extends Controller
{
    // This method is responsible for displaying a vendor's profile along with their products.
    public function profile(Request $request,Vendor $vendor)
    {
        $keyword = $request->query('keyword'); // Get search keyword from URL
        $products = Product::query()
            ->forWebsite() // Custom scope to filter website-specific products
            ->where('created_by', $vendor->user_id) // Get products created by the vendor
            ->when($keyword, function ($query, $keyword){
                $query->where(function($query) use ($keyword){
                    $query->where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%");
                });
            })
            ->paginate(12); // Paginate results (12 per page)

        return Inertia::render('Vendor/Profile', [
            'vendor' => $vendor,
            'products' => ProductListResource::collection($products),
        ]);
    }

    // This method handles vendor registration or updating vendor details.
    public function store(Request $request)
    {
        $user=$request->user(); // Get logged-in user
        $request->validate([
            // Ensure only lowercase alphanumeric and hyphens
            'store_name'=>['required','regex:/^[a-z0-9-]+$/', Rule::unique('vendors','store_name')
                ->ignore($user->id, 'user_id'),
            ],
            'store_address'=>'nullable', // Optional address field
        ], [
            'store_name.regex'=> 'Store name must only contains lowercase alphanumeric characters.',
        ]);
        $vendor=$user->vendor ?: new Vendor(); // Get vendor if exists, otherwise create new
        $vendor->user_id = $user->id;
        $vendor->status = VendorStatusEnum::Approved->value; // Set status as approved
        $vendor->store_name = $request->store_name;
        $vendor->store_address = $request->store_address;
        $vendor->save(); // Save to database

        $user->assignRole(RolesEnum::Vendor); // Assign vendor role to user
    }
}
