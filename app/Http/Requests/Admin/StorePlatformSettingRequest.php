<?php

namespace App\Http\Requests\Admin;

use App\Models\PlatformSetting;
use App\Support\Admin\PlatformSettingCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class StorePlatformSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'value_type' => ['required', 'in:string,decimal,integer,boolean'],
            'value' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $key = Str::slug($this->string('name')->toString(), '_');
            if ($key === '' || strlen($key) > 60) {
                $validator->errors()->add('name', 'Use a name that can be saved as a setting.');

                return;
            }

            if (PlatformSetting::query()->whereKey($key)->exists()) {
                $validator->errors()->add('name', 'A setting with this name already exists.');

                return;
            }

            $message = PlatformSettingCatalog::valueError($key, $this->string('value_type')->toString(), $this->string('value')->toString());
            if ($message) {
                $validator->errors()->add('value', $message);
            }
        });
    }
}
