<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    // Ensures these attributes can be mass-assigned when creating or updating an order
    protected $fillable = [
        'stripe_session_id',
        'user_id',
        'total_price',
        'status',
        'online_payment_commission',
        'website_commission',
        'vendor_subtotal',
        'payment_intent',
    ];

    // This means one order can have multiple order items
    // Useful for retrieving all items associated with an order.
    public function orderItems():HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Establishes a relationship between the order and the user who placed it.
    // Each order belongs to a single user.
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // Links the order to the vendor user (the seller).
    // This helps identify which user is the vendor of the order.
    public function vendorUser():BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_user_id');
    }

    // Connects the order to a Vendor model.
    // Ensures the order is linked to the correct vendor entity.
    public function vendor():BelongsTo
    {
        return $this->belongsTo(Vendor::class,'vendor_user_id', 'user_id');
    }

}
