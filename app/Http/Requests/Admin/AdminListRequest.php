<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,inactive'],
            'kind' => ['nullable', 'in:all,tourism,service'],
            'group' => ['nullable', 'in:all,booking_finance,booking_policy,other'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', 'in:name,key,kind,updated_at'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }
}
