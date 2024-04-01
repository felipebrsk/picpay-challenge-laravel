<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionTokenResource extends JsonResource
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
            'token' => $this->token,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'to' => UserResource::make($this->whenLoaded('to')),
            'from' => UserResource::make($this->whenLoaded('from')),
        ];
    }
}
