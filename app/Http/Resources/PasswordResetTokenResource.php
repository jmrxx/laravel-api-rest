<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PasswordResetTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'email' => $this->email,
            'token' => $this->token,
            "expired_at" => $this->created_at->addMinutes(60),
            'expires_in_minutes' => now()->diffInMinutes($this->created_at->addMinutes(60), false),
        ];
    }
}
