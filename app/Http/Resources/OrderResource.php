<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'purchase_date' => $this->purchase_date->toDateString(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'medications' => MedicationResource::collection($this->whenLoaded('items', fn () => $this->items->pluck('medication'))),
        ];
    }
}
