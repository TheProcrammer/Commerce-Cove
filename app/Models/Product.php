<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\RegisterCustomMediaConversions;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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


    // This function is for you to be able to call product.image when you uploaded an image
    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('images'); // 'images' is the collection name
    }
    
    //Scope a query to filter records created by the currently authenticated user.
    public function scopeForVendor(Builder $query): Builder
    {
        return $query->where('created_by',auth()->user()->id);
    }

    // Scope a query to filter records with a status of 'Published'.
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status',ProductStatusEnum::Published);
    }
    // filters data specifically meant to be shown on the website.
    public function scopeForWebsite(Builder $query): Builder
    {
        return $query->published();
    }
    // Define a `user` method to establish the relationship between Product model and the `User` model.
    public function user():BelongsTo
    {
        // 'created_by' Is the foreign key in the current model (e.g., Product) that stores the ID of the User who created it.
        // Define a 'belongsTo' relationship, where Product model belongs to a User.
        return $this->belongsTo(User::class, 'created_by');
    }
    public function department()  // A product belongs to one department.
    {
        // Define a 'belongsTo' relationship, where Product model belongs to a Department.
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

    //Defines a one-to-many relationship where a product can have multiple variations, 
    // linked by the product_id column in the ProductVariation model.
    public function variations(): HasMany // when this data is extracted it will generate product_variations in the database.
    {
        return $this->hasMany(ProductVariation::class,'product_id');
    }
}


