<?php

namespace App\Http\Requests\Admin;

use App\Enums\DashboardPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->query->has('period')) {
            $this->merge(['period' => '30d']);
        }
    }

    public function rules(): array
    {
        return [
            'period' => ['required', 'string', Rule::in(DashboardPeriod::values())],
        ];
    }
}
