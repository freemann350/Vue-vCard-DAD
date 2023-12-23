<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VcardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public static $format = "default";

    public function toArray(Request $request): array
    {
        switch (VcardResource::$format) {
            case 'minimal':
                return [
                    'phone_number' => $this->phone_number,
                    'name' => $this->name,
                    'photo_url' => $this->photo_url,
                ];
            default:
                return [
                    'phone_number' => $this->phone_number,
                    'name' => $this->name,
                    'email' => $this->email,
                    'balance' => $this->balance,
                    'max_debit' => $this->max_debit,
                    'photo_url' => $this->photo_url,
                    'blocked' => $this->blocked
                ];
        }
    }
}
