<?php

namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\VariationTypeOption;
use Illuminate\Support\Facades\Log;
use App\Models\CartItem;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\VariationType;
use function Symfony\Component\Translation\t;


class CartService
{

    private ?array $cachedCartItems = null;
    protected const COOKIE_NAME = 'cartItems'; // Name of the cookie
    protected const COOKIE_LIFETIME = 60 * 24 * 365; // 1 year

    // This function ensures that products added to the cart are associated with specific variation options and their corresponding prices.
    public function addItemToCart(Product $product, int $quantity = 1, $optionIds = null)// parameters came from the Cart Controller > Update
    {
        // \Log::info('Parameters received by addItemToCart:', [
        //     'product_id' => $product->id,
        //     'quantity' => $quantity,
        //     'option_ids' => $optionIds,
        // ]);
        // If no options are provided, select the default options for the product's variations
        if (!$optionIds) {
            $optionIds = $product->getFirstOptionsMap();
        }
        // Calculate the price based on the selected options
        $price = $product->getPriceForOptions($optionIds);
        // dd($quantity,$price,$optionIds); // Debugging
        // Save the item to the cart (database for logged-in users or cookies for guests)
         Auth::check()
            ? $this->saveItemToDatabase($product->id, $quantity, $price, $optionIds)
            : $this->saveItemToCookies($product->id, $quantity, $price, $optionIds);
    }
    //This function updates the quantity of a specific product in the cart, ensuring the correct variation options (if provided) are updated as well.
    public function updateItemQuantity(int $productId, int $quantity, $optionIds = null)
    {
    // If logged in, update the quantity in the database
    // If not logged in, update the quantity in the cookies
        Auth::check() 
        ? $this->updateQuantityInDatabase($productId, $quantity, $optionIds) 
        : $this->updateItemQuantityInCookies($productId, $quantity, $optionIds);
    }
    // This function removes a specific product, including its associated variation options (if provided), from the cart.
    public function removeItemFromCart(int $productId, $optionIds = null)
    {
        // If logged in, remove the product from the database
        // If not logged in, remove the product from the cookies
        Auth::check() 
        ? $this->removeItemFromDatabase($productId, $optionIds) 
        : $this->removeItemFromCookies($productId, $optionIds);
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
                // dd($cartItems);

                //You Can also use this.
                // if(Auth::check()){
                //     $cartItems = $this->getCartItemsFromDatabase();
                // }else{
                //     $cartItems = $this->getCartItemsFromCookies();
                // }

                // Collect all product IDs from the cart items
                $productIds= collect($cartItems)->map(fn($item) => $item['product_id']);

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
            // throw $e; //
            // Log the error message and stack trace if an exception occurs
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        // Return an empty array if an error occurs
        return [];
    }
    // Calculates the total number of items in the cart by summing up the quantity of each item.
    public function getTotalQuantity()
    {
        $totalQuantity = 0;
        // Loop through each cart item and add its quantity to the total
        foreach ($this->getCartItems() as $item) {
            $totalQuantity += $item['quantity'];
        }
        return $totalQuantity; // Return the total quantity of all cart items
    }

    // Calculates the total cost of all items in the cart by multiplying each item's quantity by 
    // its price and summing them up. 
    public function getTotalPrice()
    {
        $total = 0;
        // Loop through each cart item and calculate the total cost
        foreach ($this->getCartItems() as $item) {
            $total += $item['quantity'] * $item['price'];
        }
        return $total; // Return the total price of all cart items
    }

    // Updates the quantity of a specific cart item in the database. 
    // It searches for a matching cart item using the product ID and option IDs.
    protected function updateQuantityInDatabase(int $productId, int $quantity, array $optionIds = null)// parameters came from the Cart Controller > Update
    {
        $userId = Auth::id();
        // Find the cart item by product ID and option IDs.
        $cartItems = CartItem::where('product_id', $productId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', json_encode($optionIds))
            ->first();
        // If the cart item exists, update its quantity.
        if ($cartItems) {
            $cartItems->update([
                'quantity' => $quantity
            ]);
        }
    }

    // This function updates the quantity of a specific product in the cart stored in cookies for guest users.
    protected function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds = null)
    {
        $cartItems = $this->getCartItemsFromCookies();  // Retrieve the current cart items from cookies
        ksort($optionIds); // Sort the option IDs to ensure a consistent key structure
        $itemKey = $productId . '_' . json_encode($optionIds); // Generate a unique key for the cart item using the product ID and option IDs

        // Check if the item exists in the cart and update its quantity if found
        if (isset($cartItems[$itemKey])) {
            $cartItems[$itemKey]['quantity'] = $quantity;
        }

        // Save the updated cart items back to the cookies with the defined lifetime
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems),self::COOKIE_LIFETIME);
    }

    // This function manages saving cart items for authenticated users directly into the database.
    protected function saveItemToDatabase(int $productId, int $quantity, $price, array $optionIds)
    {
        $userId = Auth::id(); // Retrieve the ID of the currently authenticated user
        ksort($optionIds); // Sort the option IDs to ensure consistency in the unique key generation
        // Check if the cart item with the same product and options already exists for the user
        $encodedOptionIds = json_encode($optionIds);

        // for debugging
        // Log::info('Option IDs: ' . print_r($optionIds, true));

        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', $encodedOptionIds) // To pile up the products instead of rerendering
            ->first();

        // If the item exists, update its quantity by incrementing it
        if ($cartItem) {
            $cartItem->update([
                'quantity' => DB::raw('quantity + ' . $quantity),
            ]);
        // If the item does not exist, create a new cart item entry
        } else {
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'variation_type_option_ids' => $optionIds
            ]);
        }
    }

    // This function manages adding or updating cart items in cookies for users who are not logged in (guest users).
    protected function saveItemToCookies(int $productId, int $quantity, $price, array $optionIds)
    {
        $cartItems = $this->getCartItemsFromCookies(); // Retrieve existing cart items from cookies
        // Debugging: Dump the current cart items, product details, quantity, price, and option IDs
        // dd($cartItems, $productId,$quantity,$price,$optionIds);

        ksort($optionIds); // Sort the option IDs for consistent key generation
        // Generate a unique key for the cart item based on product ID and sorted option IDs
        $itemKey = $productId . '_' . json_encode($optionIds);
        // If the product already exists in the cart, update its quantity and price
        if (isset($cartItems[$itemKey])) { 
            $cartItems[$itemKey]['quantity'] += $quantity;
            $cartItems[$itemKey]['price'] = $price;
        } else {  // If the product is new, add it to the cart with its details
            $cartItems[$itemKey] = [
                'id'=> Str::uuid(),
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'option_ids' => $optionIds
            ];
        }
        // Update the cart data in the cookies with the new state and set its lifetime
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems),self::COOKIE_LIFETIME);
    }
    // Removes a specific product (and its variations, if applicable) from the authenticated user's 
    // cart stored in the database.
    protected function removeItemFromDatabase(int $productId, array $optionIds = null)
    {
        $userId = Auth::id(); // Get the currently authenticated user's ID
        // Find and delete the cart item matching the user, product, and variation options
        CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', json_encode($optionIds))
            ->delete();
    }
    // Removes a specific product (and its associated options, if any) from the user's cart stored in cookies.
    // For guest users. 
    protected function removeItemFromCookies(int $productId, array $optionIds = null)
    {
        $cartItems = $this->getCartItemsFromCookies(); // Retrieve the current cart items from cookies
        ksort($optionIds); // Sort the option IDs to maintain consistent order for key generation
         // Generate a unique key for the product and its options in the cart
        $cartKey = $productId . '_' . json_encode($optionIds); 
        unset($cartItems[$cartKey]); // Remove the item from the cart items array
         // Update the cart in cookies by saving the updated cart items array
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems),self::COOKIE_LIFETIME);
    }
    // Retrieves all cart items for the currently authenticated user from the database
    protected function getCartItemsFromDatabase()
    {
        $userId = Auth::id(); // Get the authenticated user's ID
        // Query the CartItem table for all items belonging to the user
        $cartItems = CartItem::where('user_id', $userId)
            ->get()
            // Map the raw cart items into a structured array with the necessary details
            ->map(function ($cartItem) {
                return [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'option_ids' => $cartItem->variation_type_option_ids, // variation_type_option_ids please check this line.
                ];
            })
            ->toArray(); // Convert the mapped collection to a plain array
            return $cartItems; // Return the array of cart items
    }

    // Retrieves cart items stored in the browser cookies for users who are not logged in.
    protected function getCartItemsFromCookies()
    {
        // Decode the JSON string stored in the specified cookie; default to an empty array if the cookie is not found
        $cartItems = json_decode(Cookie::get(self::COOKIE_NAME, '[]'), true);
        return $cartItems; // Return the array of cart items
    }
    // Organizes cart items by user, calculates the total quantity and total price.
    public function getCartItemsGrouped(): array
    {
        $cartItems = $this->getCartItems(); // Retrieve all cart items from cookies or database
        // Group and process the cart items using Laravel's collection helper
        return collect($cartItems)
            ->groupBy(fn($item) => $item['user']['id']) // Group items by user ID
            // Map each user group to a summary structure
            ->map(fn($items,$userId) => [
                'user'=>$items->first()['user'],
                'items'=>$items->toArray(),
                'totalQuantity'=>$items->sum('quantity'),
                'totalPrice'=>$items->sum(fn($item) => $item['price'] * $item['quantity'])
            ])
            ->toArray();
    }

    // Moves cart items from cookies to the database when a user logs in. 
    // If the item already exists in the database for the user, it updates the quantity and price. 
    // If it doesn’t exist, it creates a new entry.
    public function moveCartItemsToDatabase($userId) {
        $cartItems = $this->getCartItemsFromCookies(); // Retrieve cart items stored in cookies

        foreach ($cartItems as $itemkey => $cartItem) {
            // Check if the item already exists in the database for this user
            $existingItem = CartItem::where('user_id', $userId)
                ->where('product_id', $cartItem['product_id'])
                ->where('variation_type_option_ids', json_encode($cartItem['option_ids']))
                ->first();

            if ($existingItem) {
                // If item exists, update the quantity and price
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $cartItem['quantity'],
                    'price' => $cartItem['price'],
                ]);
            } else {
                // If item does not exist, create a new record in the database
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price'],
                    'variation_type_option_ids' => $cartItem['option_ids']
                ]);
            }
        }
        // Clear the cart stored in cookies after transferring data to the database
        Cookie::queue(self::COOKIE_NAME, '', -1); 
    }

}
