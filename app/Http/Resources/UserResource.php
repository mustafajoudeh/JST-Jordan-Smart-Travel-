<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $role = $this->role;

        return [
            'id' => $this->id,
            'role' => $role instanceof \BackedEnum ? $role->value : $role,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'country_code' => $this->country_code,
            'photo_url' => $this->photo_url,
            'phone' => $this->phone,
            'budget_level' => $this->budget_level,
            'trip_pace' => $this->trip_pace,
            'group_type' => $this->group_type,
            'activity_level' => $this->activity_level,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
