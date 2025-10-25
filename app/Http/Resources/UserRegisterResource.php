<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRegisterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['user']->id,
            'name' => $this->resource['user']->name,
            'email' => $this->resource['user']->email,
            'balance' => $this->resource['user']->wallet->balance,
            'token' => $this->resource['tokenData']->json(),
        ];
    }
}
