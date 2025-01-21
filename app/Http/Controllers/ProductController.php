<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductListResource;
use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Inertia;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    // This function place at home so whenever you go to home page products will be rendered as well.
    public function home()
    {
        $products = Product::query() //Render products data
            ->forWebsite() // Fetches products that are marked as 'Published'.
            ->paginate(12);

        return Inertia::render('Home',[
              'products' => ProductListResource::collection($products),
            ]);
    }
    // Handles the request to show a specific product's details.
    public function show(Product $product, $slug)
    {
        // Fetch the product by its slug from the database. 
        // If the product is not found, throw a 404 error.
        $product = Product::where('slug', $slug)->firstOrFail();
        // Return the product as a resource and pass variation options
        return Inertia::render('Product/Show', [ // Product>Show.tsx
            'product'=> new ProductResource($product), // Render product resource app\Http\Resources\ProductResource
            'variationOptions' => request('options', []), // Get selected variation options from the request or default to an empty array.
            // 'options' retrieves the options parameter from the incoming HTTP request.
        ]);
    }
}
