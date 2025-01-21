<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    // This property ensures that the variation_type_option_ids attribute is automatically cast 
    //to and from a JSON array when accessed or saved in the database.
    protected $casts = [
        'variation_type_option_ids' => 'json'
    ];
}
