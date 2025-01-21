<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartService $cartService)
    {
        dd($cartService);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

     // Handles adding a product to the cart.
    public function store(Request $request, Product $product, CartService $cartService)
    {
        // Ensure the 'quantity' field has a default value of 1 if not provided in the request
        $request->mergeIfMissing (['quantity' => 1]);
        // Validate the incoming request data
        $data= $request->validate([
            'option_ids' => ['nullable','array'],
            'quantity' => ['nullable', 'integer', 'min:1']
        ]);
        // Add the product to the cart using the CartService, passing the product, quantity, and options
        $cartService->addItemToCart(
            $product, 
            $data['quantity'], 
            $data['option_ids']
        );
        // Redirect back to the previous page with a success message
        return back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Handles updating the quantity of a specific product in the cart.
    public function update(Request $request, Product $product, CartService $cartService)
    {
        // Validate the 'quantity' field to ensure it is an integer and at least 1
        $request->validate([
            'quantity' => ['integer', 'min:1']
        ]);

        // Retrieve optional product option IDs and the updated quantity from the request
        $optionIds = $request->input('option_ids');
        $quantity = $request->input('quantity');
         // Use the CartService to update the quantity of the specified product in the cart
        $cartService->updateItemQuantity(
            $product->id, 
            $quantity, 
            $optionIds);
        // Redirect back to the previous page with a success message
        return back()->with('success', 'Quantity was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    //This method handles removing a specific product 
    // (and its associated options, if any) from the user's cart.
    public function destroy(Request $request, Product $product, CartService $cartService)
    {
        // Retrieve optional product option IDs from the request
        $optionIds = $request->input('option_ids');
        // Use the CartService to remove the specified product (and options, if any) from the cart
        $cartService->removeItemFromCart($product->id, $optionIds);
        // Redirect back to the previous page with a success message
        return back()->with('success', 'Product removed from cart successfully!');
    }
}
