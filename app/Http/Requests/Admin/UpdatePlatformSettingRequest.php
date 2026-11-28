<?php

namespace App\Http\Requests\Admin;

use App\Models\PlatformSetting;
use App\Support\Admin\PlatformSettingCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdatePlatformSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value_type' => ['required', 'in:string,decimal,integer,boolean'],
            'value' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $setting = $this->route('setting');
            if (! $setting instanceof PlatformSetting || $validator->errors()->isNotEmpty()) {
                return;
            }

            $type = $this->string('value_type')->toString();
            $definition = PlatformSettingCatalog::definition($setting->key);
            if ($definition['value_type'] && $definition['value_type'] !== $type) {
                $validator->errors()->add('value_type', 'The type of a core platform setting cannot be changed.');

                return;
            }

            $value = $this->input('value');
            if (($value === null || $value === '') && PlatformSettingCatalog::isSensitive($setting->key)) {
                return;
            }

            if ($value === null || $value === '') {
                $validator->errors()->add('value', 'Enter a value for this setting.');

                return;
            }

            $message = PlatformSettingCatalog::valueError($setting->key, $type, (string) $value);
            if ($message) {
                $validator->errors()->add('value', $message);
            }
        });
    }
}
