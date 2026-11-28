<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kind' => $this->kind,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            'related_counts' => [
                'destinations' => (int) ($this->destinations_count ?? 0),
                'activities' => (int) ($this->activities_count ?? 0),
                'providers' => (int) ($this->providers_count ?? 0),
                'services' => (int) ($this->services_count ?? 0),
                'travelers' => (int) ($this->interested_users_count ?? 0),
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
