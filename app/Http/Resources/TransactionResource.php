<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'status' => $this->status,
            'amount' => Number::currency($this->amount),
            'created_at' => $this->created_at,
            'to' => $this->when($this->type == Transaction::SENT_CONST_ID, UserResource::make($this->whenLoaded('to'))),
            'from' => $this->when($this->type == Transaction::RECEIVED_CONST_ID, UserResource::make($this->whenLoaded('from'))),
        ];
    }
}
