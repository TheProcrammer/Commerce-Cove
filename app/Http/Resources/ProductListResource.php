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
            "price"=> $this->price,
            "quantity"=> $this->quantity,
            "image"=> $this->image,
            "user"=> [
                "id"=> $this->user->id,
                "name"=> $this->user->name
            ],
            "department" => [
                "id" => $this->department->id,
                "name" => $this->department->name
            ]
        ];
    }
}
