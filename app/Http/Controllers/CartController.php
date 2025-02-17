<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Inertia;
use Stripe\Stripe;
use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartService $cartService)
    {
        return Inertia::render('Cart/Index', [
            'cartItems' => $cartService->getCartItemsGrouped(),
        ]);
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
    $request->mergeIfMissing(['quantity' => 1]);

    // Normalize "options_ids" to "option_ids" if it exists
    // JS or laravel renames the array to plural form as common name convention is options_ids it assumes that you just typo.
    if ($request->has('options_ids')) {
        $request->merge(['option_ids' => $request->input('options_ids')]);
    }

    // Log the updated request data to verify, for debugging
    // \Log::info('Normalized request data:', $request->all());

    // Validate the incoming request
    $data = $request->validate([
        'option_ids' => ['nullable', 'array'],
        'quantity' => ['nullable', 'integer', 'min:1'],
    ]);

    // Log the validated data
    // \Log::info('Validated data:', $data);

    // Check if 'option_ids' exists in the validated data and handle accordingly
    // Add the product to the cart using the CartService, passing the product, quantity, and options
    $cartService->addItemToCart(
        $product,
        $data['quantity'],
        $data['option_ids'] ?? [],
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
        $optionIds = $request->input('option_ids') ?: [];
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

    // Handles the checkout process using stripe for the user's cart.
    public function checkout(Request $request, CartService $cartService){
        Stripe::setApiKey(config('app.stripe_secret_key'));  // Set Stripe API key
        $vendorId = $request->input('vendor_id'); // Get vendor ID from request
        $allCartItems = $cartService->getCartItemsGrouped(); // Get cart items grouped by vendor
        DB::beginTransaction(); // Begin database transaction
        try {
            $checkoutCartItems = $allCartItems;
            if($vendorId){
                $checkoutCartItems = [$allCartItems[$vendorId]]; // Filter cart items for specific vendor
            }
            $orders = [];
            $lineItems = [];
            foreach($checkoutCartItems as $item){
                $user = $item['user']; // Vendor details
                $cartItems = $item['items']; // Cart items for this vendor

                // Create an order in the database
                $order = Order::create([
                    'stripe_session_id' => null,
                    'user_id' => $request->user()->id, // Buyer ID
                    'vendor_user_id' => $user['id'], // Vendor ID
                    'total_price'=>$item['totalPrice'],
                    'status' => OrderStatusEnum::Draft->value, // Initial status set to 'draft'
                ]);
                $orders[] = $order;

                foreach($cartItems as $cartItem){
                    // Store each cart item as an order item in the database
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem['product_id'],
                        'quantity' => $cartItem['quantity'],
                        'price' => $cartItem['price'],
                        'variation_type_option_ids' => $cartItem['option_ids'],
                    ]);

                    // Generate product description from selected options
                    $description = collect($cartItem['options'])->map(function($item){
                        return "{$item['type']['name']}: {$item['name']}";
                    })->implode(', ');

                    // Prepare item details for Stripe Checkout
                    $lineItem= [
                        'price_data' => [
                            'currency' => config('app.currency'),
                            'product_data' => [
                                'name' => $cartItem['title'],
                                'images'=> [$cartItem['image']]
                            ],
                            'unit_amount' => $cartItem['price'] * 100, // Convert price to cents
                        ],
                        'quantity' => $cartItem['quantity'],
                    ];
                    if($description){
                        $lineItem['price_data']['product_data']['description'] = $description;
                    }
                    $lineItems[] = $lineItem;
                }
            }
            // Create a Stripe checkout session
            $session = \Stripe\Checkout\Session::create([
                'customer_email' => $request->user()->email,
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success',[]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel',[]),
            ]);
            // Update each order with the Stripe session ID
            foreach($orders as $order){
                $order->stripe_session_id = $session->id;
                $order->save();
            }
            DB::commit(); // Commit the transaction
            return redirect($session->url); // Redirect user to Stripe checkout page
        } catch (\Exception $e) {
            Log::error($e); // Log error
            Db::rollback(); // Rollback database changes
            return back()->with('error', $e->getMessage() ?: 'Something went wrong'); // Return error message
        }
    }
}
