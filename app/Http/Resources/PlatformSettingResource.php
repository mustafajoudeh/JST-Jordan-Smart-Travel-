<?php

namespace App\Http\Resources;

use App\Support\Admin\PlatformSettingCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlatformSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $sensitive = PlatformSettingCatalog::isSensitive($this->key);
        $definition = PlatformSettingCatalog::definition($this->key);

        return [
            'key' => $this->key,
            'name' => $definition['name'],
            'group' => $definition['group'],
            'group_label' => $definition['group_label'],
            'value' => $sensitive ? null : $this->value,
            'value_type' => $this->value_type,
            'description' => $this->description,
            'is_sensitive' => $sensitive,
            'has_value' => $this->value !== null && $this->value !== '',
            'is_protected' => PlatformSettingCatalog::isProtected($this->key),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
