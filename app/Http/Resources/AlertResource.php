<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'sent_by' => $this->whenLoaded('user', fn () => $this->user->username),
            'sent_at' => $this->sent_at->toIso8601String(),
        ];
    }
}
