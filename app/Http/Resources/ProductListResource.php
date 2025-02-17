<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    
    // This method converts the model instance into an array suitable for JSON or API responses.
    // This is important for frontend side rendering to convert it suitable for JSON first.
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "title"=> $this->title,
            "slug"=> $this->slug,
            "description"=> $this->description,
            "meta_title" => $this->meta_title,
            "meta_description" => $this->meta_description,
            "price"=> $this->getPriceForFirstOptions(),
            "quantity"=> $this->quantity,
            "image"=> $this->getFirstImageUrl(),
            "user"=> [
                "id"=> $this->user->id,
                "name"=> $this->user->name,
                'store_name' => $this->user->vendor->store_name
            ],
            "department" => [
                "id" => $this->department->id,
                "name" => $this->department->name,
                "slug" => $this->department->slug
            ]
        ];
    }
}
