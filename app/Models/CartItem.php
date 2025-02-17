<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * This ensures that the `variation_type_option_ids` attribute is automatically 
 * converted to an array when retrieved from the database and stored as JSON when saved.
 */
class CartItem extends Model
{
    protected $casts = [
        'variation_type_option_ids' => 'array',
    ];
}
