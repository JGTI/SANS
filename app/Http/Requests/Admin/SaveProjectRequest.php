<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super-admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email:rfc', 'max:255'],
            'status' => ['required', Rule::in(array_keys(config('onboarding.statuses')))],
            'notes' => ['nullable', 'string', 'max:10000'],
            'link_enabled' => ['required', 'boolean'],
            'expires_at' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
