<?php

namespace App\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait RegisterCustomMediaConversions
{
    use InteractsWithMedia;

    public function registerCustomMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(100); // Creates a 'thumb' image conversion with a width of 100 pixels.
        $this->addMediaConversion('small')
             ->width(480); // Creates a 'small' image conversion with a width of 480 pixels.
        $this->addMediaConversion('large')
             ->width(1200); // Creates a 'large' image conversion with a width of 1200 pixels.
    }
}

