<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

class ComputerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray (Request $request): array
    {
        return [
            'computer_name' => $this->resource->computer_name,
            'full_name' => $this->resource->full_name,
            'ip_address' => $this->resource->ip_address,
            'mac_address' => $this->resource->mac_address,
            'status' => $this->resource->status,
        ];
    }
}
