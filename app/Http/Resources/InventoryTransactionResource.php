<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "qunatity"=> $this->quantity,
            "vendor_id"=> $this->vendor_id,
            "product_id"=> $this->product_id,
            "status" => $this->status
        ];
    }
}
