<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use App\Enums\VendorStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\RegisterCustomMediaConversions;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;


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
        return $query->where('products.status',ProductStatusEnum::Published);
    }
    // filters data specifically meant to be shown on the website.
    public function scopeForWebsite(Builder $query): Builder
    {
        return $query->published()->vendorApproved();
    }

    // Scope a query to filter records where the vendor's status is 'Approved'.
    public function scopeVendorApproved(Builder $query)
    {
        return $query->join('vendors', 'vendors.user_id', '=', 'products.created_by')
            ->where('vendors.status', VendorStatusEnum::Approved->value);
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

     // Define a "has many through" relationship with VariationTypeOption through VariationType
    public function options()
    {
        return $this->hasManyThrough(
            VariationTypeOption::class,
            VariationType::class,
            'product_id',
            'variation_type_id',
            'id',
            'id'
        );
    }

    //Defines a one-to-many relationship where a product can have multiple variations, 
    // linked by the product_id column in the ProductVariation model.
    public function variations(): HasMany // when this data is extracted it will generate product_variations in the database.
    {
        return $this->hasMany(ProductVariation::class,'product_id');
    }

    // Retrieves the correct price for a product based on the selected variations (options).
    public function getPriceForOptions($optionIds = [])
    {
        $optionIds = array_values($optionIds); // Reindex the array to ensure numeric keys
        sort($optionIds); // Sort the option IDs to ensure consistent comparison
        // Loop through all variations and compare option IDs
        foreach ($this->variations as $variation) {
            $a = $variation->variation_type_option_ids; // Get the variation's option IDs
            sort($a); // Sort the variation's option IDs for comparison
            // If option IDs match, return the variation price or the default price
            if ($optionIds === $a) {
                return $variation->price !== null ? $variation->price : $this->price;
            }
        }
        // If no match is found, return the default product price
        return $this->price;
    }

    // Get the price of the first option for each variation type
    public function getPriceForFirstOptions(): float
    {
        // Get the first options map
        $firstOptions = $this->getFirstOptionsMap();
        // If first options exist, return the price for those options
        if($firstOptions){
            return $this->getPriceForOptions($firstOptions);
        }
        // If no first options, return the default price
        return $this->price;
    }

    // This function retrieves the first image URL from a media collection.
    public function getFirstImageUrl($collectionName = 'images', $conversion = 'small'):string
    {
        // Check if the object has options
        if($this->options->count() > 0) {
            // Loop through each option
            foreach ($this->options as $option) {
                // Get the first media URL from the option
              $imageUrl = $option->getFirstMediaUrl($collectionName,$conversion);
               // Return the image URL if found
              if ($imageUrl){
                return $imageUrl;
              }  
            }
        }
        // Return the main object's first media URL if no option has an image
        return $this->getFirstMediaUrl($collectionName,$conversion);
    }

    // Retrieves the correct image for a product based on the selected variations (options).
    public function getImageForOptions(array $optionIds = null)
    {
        if($optionIds){
            $optionIds = array_values($optionIds); // Reindex the array to ensure numeric keys
            sort($optionIds); // Sort the option IDs to ensure consistent comparison
            $options = VariationTypeOption::whereIn('id', $optionIds)->get();

            foreach ($options as $option) {
                $image = $option->getFirstMediaUrl('images','small');
                if ($image) {
                    return $image;
                }
            }
        }
        return $this->getFirstMediaUrl('images','small');
    }

    // Purpose: Retrieves the first available option for each variation type
    public function getFirstOptionsMap(): array
    {
        // Returns an associative array mapping variation type IDs to their first option ID
        return $this->variationTypes
            ->mapWithKeys(fn($type) => [$type->id => $type->options[0]->id]) // Get the first option's ID for each variation type
            ->toArray(); // Convert collection to an array
    }

    // retrieve images associated with a model, either directly or from related options. 
    //It returns a MediaCollection, likely using Spatie Media Library.
    public function getImages(): MediaCollection
    {
        // Check if the model has options
        if($this->options->count() > 0) {
            foreach ($this->options as $option) {
                // Get media (images) from the related option
                $images = $option->getMedia('images');
                if ($images) {
                    return $images; // Return the first found image collection
                }
            }
        }
        // If no images are found in options, get images directly from this model
        return $this->getMedia('images');
    }
}


