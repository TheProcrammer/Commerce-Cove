<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// format order data before sending it as a JSON response. For frontend rendering.
//It ensures that only the required fields are included when returning order details.
class VendorUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'store_name' => $this->vendor->store_name,
            'store_address' => $this->vendor->store_address,
        ];
    }
}
