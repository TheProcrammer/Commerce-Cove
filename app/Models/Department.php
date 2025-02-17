<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Department extends Model
{
    //Establish the relationship between Department and Category.
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    // Scope a query to filter records with a status of 'Published'.
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('active',true);
    }
}
