<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\VariationTypeOption;
use Illuminate\Support\Facades\Log;


class CartService
{

    private ?array $cachedCartItems = null;
    protected const COOKIE_NAME = 'cartItems'; // Name of the cookie
    protected const COOKIE_LIFETIME = 60 * 24 * 365; // 1 year

    public function addItemToCart(Product $product, int $quantity = 1, array $optionIds = null)// parameters came from the Cart Controller
    {
        //
    }
    public function updateItemQuantity(int $productId, int $quantity, $optionIds = null)// parameters came from the Cart Controller
    {

    }
    public function removeItemFromCart(int $productId, $optionIds = null)// parameters came from the Cart Controller
    {

    }
    // This function retrieves detailed cart item data for the current user or session,
    public function getCartItems()
    {
        try {
            // Check if cart items are already cached to avoid redundant processing
            if($this->cachedCartItems === null){
                // If the user is logged in, get cart items from the database, otherwise from cookies
                $cartItems = Auth::check() ? 
                $this->getCartItemsFromDatabase() : 
                $this->getCartItemsFromCookies();

                //You Can also use this.
                // if(Auth::check()){
                //     $cartItems = $this->getCartItemsFromDatabase();
                // }else{
                //     $cartItems = $this->getCartItemsFromCookies();
                // }

                // Collect all product IDs from the cart items
                $productIds= collect($cartItems)->maps(fn($item) => $item['product_id']);

                // Fetch product details for the product IDs, including vendor information
                $products = Product::whereIn('id', $productIds)
                    ->with('user.vendor') // Load vendor information
                    ->forWebsite() // Scope to filter products specific to the website. Declared in Product model.
                    ->get()
                    ->keyBy('id'); // Group products by their ID for easy lookup
                $cartItemData = [];
                    // Loop through the cart items to prepare detailed cart data
                    foreach ($cartItems as $key => $cartItem) {
                        // Fetch the corresponding product for the current cart item
                        $product = data_get($products, $cartItem['product_id']);
                        if (!$product) continue; // Skip if the product doesn't exist

                        $optionInfo = []; // To store option details for the current cart item
                        // Fetch variation options for the product
                        $options = VariationTypeOption::with('variationType') // Load the option's type details
                            ->whereIn('id', $cartItem['option_ids']) // Match the options with the IDs in the cart
                            ->get()
                            ->keyBy('id');  // Group options by their ID for easy lookup
                        $imageUrl=null; // To store the option's image URL
                        // Loop through the options to collect details
                        foreach ($cartItem['option_ids'] as $option_id) {
                            $option= data_get($options, $option_id);
                            if (!$imageUrl) {
                                // Set the image URL for the first option that has an image
                                $imageUrl = $option->getFirstMediaUrl('images','small');
                            }
                            // Add option details to the array
                            $optionInfo[] = [
                                'id' => $option_id,
                                'name' => $option->name,
                                'type' => [
                                    'id' => $option->variationType->id,
                                    'name' => $option->variationType->name,
                                ],

                            ];
                        }
                        // Prepare the complete cart item data
                        $cartItemData[] = [
                            'id' => $cartItem['id'],
                            'product_id' => $product->id,
                            'title' => $product->title,
                            'slug' => $product->slug,
                            'price' => $cartItem['price'],
                            'quantity' => $cartItem['quantity'],
                            'option_ids' => $cartItem['option_ids'],
                            'options' => $optionInfo,
                            'image' => $imageUrl ?: $product->getFirstMediaUrl('images','small'),
                            'user' => [ // Vendor details
                                'id' => $product->created_by, // Vendor ID
                                'name' => $product->user->vendor->store_name, // Vendor's store name
                            ]
                            
                        ];
                    }
                // Cache the processed cart items for future use
                $this->cachedCartItems = $cartItemData;
            }
            // Return the cached cart items
            return $this->cachedCartItems;

        } catch (\Exception $e) {
            // Log the error message and stack trace if an exception occurs
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        // Return an empty array if an error occurs
        return [];
    }
    public function getTotalQuantity()
    {

    }
    public function getTotalPrice()
    {

    }
    protected function updateQuantityInDatabase(int $productId, int $quantity, array $optionIds = null)// parameters came from the Cart Controller
    {

    }
    protected function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds = null)// parameters came from the Cart Controller
    {

    }
    protected function saveItemToDatabase(int $productId, int $quantity, array $optionIds = null)// parameters came from the Cart Controller
    {

    }
    protected function removeItemFromCookies(int $productId, array $optionIds = null)// parameters came from the Cart Controller
    {
        
    }
    protected function getCartItemsFromDatabase()
    {
        
    }
    protected function getCartItemsFromCookies()
    {
        
    }

}
