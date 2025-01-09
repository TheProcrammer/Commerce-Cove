<?php

namespace App\Services;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    // Public > Storage > Conversions
    public function getPath(Media $media): string 
    //Get the path for the given media, relative to the root storage path.
    {
        return md5($media->id . config('app.key')) . '/';
    }

    public function getPathForConversions(Media $media): string
    //Get the path for conversions of the given media, relative to the root storage path.
    {
        return md5($media->id . config('app.key')) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    //Get the path for responsive images of the given media, relative to the root storage path.
    {
        return md5($media->id . config('app.key')) . '/responsive-images/';
    }
    public function __construct()
    {
        //
    }
}
