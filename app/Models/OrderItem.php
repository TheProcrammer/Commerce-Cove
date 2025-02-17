<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false; // Disables automatic timestamping (created_at and updated_at).

    // Ensures only these attributes can be bulk inserted or updated using create() or update().
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'variation_type_option_ids',
    ];

    // Converts variation_type_option_ids into an array when retrieving it from the database.
    // This is useful if the field stores JSON data.
    protected $casts = [
        'variation_type_option_ids' => 'array'
    ];

    // This means each record in this model belongs to one order.
    // Useful when retrieving the order details for a specific item.
    public function order():BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // This means each record in this model belongs to one product.
    // Useful for fetching product details for a specific order item.
    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
