<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGmaspeContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'company' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service' => ['nullable', 'string', Rule::in([
                'Maquinados de alta precisión',
                'Dispositivos y fixtures',
                'Herramentales',
                'Calibradores',
                'Producción en serie',
                'Metrología',
                'Otro requerimiento',
            ])],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'privacy' => ['accepted'],
            'website' => ['nullable', 'max:0'],
            'captcha_token' => ['required', 'string', 'max:4000'],
            'captcha_answer' => ['required', 'integer', 'between:0,30'],
        ];
    }

    public function messages(): array
    {
        return [
            'privacy.accepted' => 'Debes aceptar el uso de tus datos para atender la solicitud.',
            'website.max' => 'No fue posible verificar la solicitud.',
            'captcha_answer.required' => 'Resuelve la verificación “No soy un robot”.',
        ];
    }
}
