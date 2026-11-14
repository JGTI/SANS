<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('super-admin');
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->name)) {
            $this->merge(['name' => mb_strtolower(trim($this->name))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9][a-z0-9_-]*$/',
                Rule::notIn(['super-admin', 'admin', 'user']),
                Rule::unique('roles')->where('guard_name', 'web')->ignore($this->route('role'))],
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::in(['users.manage'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Escribe el nombre del rol.',
            'name.regex' => 'Usa letras minúsculas, números, guiones o guiones bajos.',
            'name.unique' => 'Ya existe un rol con ese nombre.',
            'name.not_in' => 'Este nombre está reservado para un rol del sistema.',
        ];
    }
}
