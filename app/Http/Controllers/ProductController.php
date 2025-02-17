<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductListResource;
use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Inertia;
use App\Http\Resources\ProductResource;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;

class ProductController extends Controller
{
    // This function place at home so whenever you go to home page products will be rendered as well.
    public function home(Request $request)
    {
        $keyword = $request->query('keyword');
        $products = Product::query() //Render products data
            ->forWebsite() // Fetches products that are marked as 'Published'.
            ->when($keyword, function ($query, $keyword){
                $query->where(function($query) use ($keyword){
                    $query->where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%");
                });
            })
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

    // Retrieves and displays products for a specific department.
    public function byDepartment(Request $request, Department $department)
    {
        // Abort with 404 if the department is not active
        abort_unless($department->active, 404);
        // Get the 'keyword' query parameter from the request
        $keyword = $request->query('keyword');

        // Query products belonging to the given department
        $products = Product::query()
            ->forWebsite() // Apply a predefined scope for website-specific products
            ->where('department_id', $department->id) // Filter products by department
            ->when($keyword, function ($query, $keyword){ // If a keyword is provided, filter products
                $query->where(function($query) use ($keyword){
                    $query->where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%"); // Or by description
                });
            })
            ->paginate(); // Paginate the results

            // Render the Department/Index page with department and product data
            return Inertia::render('Department/Index', [
                'department' => new DepartmentResource($department),
                'products' => ProductListResource::collection($products),
            ]);
    }
}
