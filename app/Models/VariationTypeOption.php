<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Traits\RegisterCustomMediaConversions;

//
class VariationTypeOption extends Model implements HasMedia
{
    use InteractsWithMedia, RegisterCustomMediaConversions;

    public $timestamps = false; //Disables updated_at and created_at timestamps.
    
    //Refactored version
    public function registerMediaConversions(?Media $media = null): void 
        { 
          // App > Traits > RegisterCustomMediaConversions
            $this->registerCustomMediaConversions($media); 
        }

    // public function registerMediaConversions(?Media $media = null): void
    // {
    //     $this->addMediaConversion('thumb')
    //          ->width(100); // Creates a 'thumb' image conversion with a width of 100 pixels.
    //     $this->addMediaConversion('small')
    //          ->width(480); // Creates a 'small' image conversion with a width of 480 pixels.
    //     $this->addMediaConversion('large')
    //          ->width(1200); // Creates a 'large' image conversion with a width of 1200 pixels.
    // }

    // Indicates that this model belongs to a VariationType model.
    public function variationType()
    {
        return $this->belongsTo(VariationType::class);
    }
}
