<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = false; // Disable wrapping
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     // To ensure that the frontend receives a well-structured and consistent API response 
     // that can be easily used to display product information.
     
     // Responsible for formatting product data into a JSON structure for frontend rendering.
     // Fetches data from the database then pass it on to the frontend.
    public function toArray(Request $request): array
    {
        $options = $request->input('options') ?: [];
        if ($options) {
            $images = $this->getImageForOptions($options);
        } else {
            $images = $this->getImages();
        }
        return [
            "id"=> $this->id,
            "title"=> $this->title,
            "slug"=> $this->slug,
            "description"=> $this->description,
            "price"=> $this->price,
            "quantity"=> $this->quantity,
            "image"=> $this->getFirstMediaUrl('images'), // Include the URL of the first image associated with the product, stored in the 'images' collection
            "images" => $this->getMedia('images')->map(function($image){ // Include an array of all images in the 'images' collection.
                return [
                    "id"=> $image->id,
                    'thumb' => $image->getUrl('thumb'),
                    'small' => $image->getUrl('small'),
                    'large' => $image->getUrl('large'),
                ];
            }),
            "user"=>[
                "id"=> $this->user->id,
                "name"=> $this->user->name,
                "store_name" => $this->user->vendor->store_name
            ],
            "department" => [
                "id" => $this->department->id,
                "name" => $this->department->name,
                "slug" => $this->department->slug
            ],
            'variationTypes' => $this->variationTypes->map(function ($variationType) {
                return [
                    'id' => $variationType->id,
                    'name' => $variationType->name,
                    'type' => $variationType->type,
                    'options' => $variationType->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'images' => $option->getMedia('images')->map(function ($image) {
                                return [
                                    'id' => $image->id,
                                    'thumb' => $image->getUrl('thumb'),
                                    'small' => $image->getUrl('small'),
                                    'large' => $image->getUrl('large'),
                                ];
                            }),
                        ];
                    }),
                ];
            }),
            'variations' => $this->variations->map(function ($variation) { // List of product variations
                return [
                    'id' => $variation->id,
                    'variation_type_option_ids' => $variation->variation_type_option_ids,
                    'quantity' => $variation->quantity,
                    'price' => $variation->price,
                ];
            }),
        ];
    }
}
