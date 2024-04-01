<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'created_at' => $this->created_at,
            'userable' => $this->userable(),
            'wallet' => WalletResource::make($this->whenLoaded('wallet')),
            'sentTransactions' => TransactionResource::collection($this->whenLoaded('sentTransactions')),
            'receivedTransactions' => TransactionResource::collection($this->whenLoaded('receivedTransactions')),
        ];
    }

    /**
     * Display userable resource based on type.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function userable(): JsonResource
    {
        $type = strtolower(Str::afterLast($this->userable_type, '\\'));

        if ($type === User::CUSTOMER_TYPE) {
            return CustomerResource::make($this->whenLoaded('userable'));
        } elseif ($type === User::SHOPKEEEPER_TYPE) {
            return ShopkeeperResource::make($this->whenLoaded('userable'));
        }
    }
}
