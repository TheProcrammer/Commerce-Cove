<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//
class VariationType extends Model
{
    public $timestamps = false; //Disables updated_at and created_at columns

    // Links a VariationType model to multiple VariationTypeOption models using a one-to-many relationship.
    public function options () {
        return $this->hasMany(VariationTypeOption::class, 'variation_type_id');
    }
}
