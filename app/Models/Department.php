<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //Establish the relationship between Department and Category.
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
