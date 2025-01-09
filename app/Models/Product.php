<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\RegisterCustomMediaConversions;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model implements HasMedia
{
    // Enables media uploads and interactions using Spatie Media Library.
    use InteractsWithMedia, RegisterCustomMediaConversions;

    //Refactored version of registerMediaConversions.
    public function registerMediaConversions(?Media $media = null): void 
        { 
        // App > Traits > RegisterCustomMediaConversions
            $this->registerCustomMediaConversions($media); 
        }
    //Can get this on Spatie Media Library Documentation on Preparing your model section.
    // public function registerMediaConversions(?Media $media = null): void
    // {
    //     $this->addMediaConversion('thumb')
    //          ->width(100); // Creates a 'thumb' image conversion with a width of 100 pixels.
    //     $this->addMediaConversion('small')
    //          ->width(480); // Creates a 'small' image conversion with a width of 480 pixels.
    //     $this->addMediaConversion('large')
    //          ->width(1200); // Creates a 'large' image conversion with a width of 1200 pixels.
    // }

    public function department()  // A product belongs to one department.
    {
        return $this->belongsTo(Department::class);
    }
    public function category()  // A product belongs to one category.
    {
        return $this->belongsTo(Category::class);
    }

    public function variationTypes(): HasMany  
    {
        // Defines a one-to-many relationship between the current model and VariationType.
        // This means one product can have multiple variation types associated with it.
        return $this->hasMany(VariationType::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class,'product_id');
    }
}


