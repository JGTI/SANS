<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProjectOnboarding
{
    public function find(string $token, bool $lock = false): Project
    {
        $query = Project::where('token_hash', hash('sha256', $token))
            ->where('link_enabled', true)->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));

        return ($lock ? $query->lockForUpdate() : $query)->firstOrFail();
    }

    public function visible(array $field, array $answers): bool
    {
        if (($field['interface'] ?? false) && ($answers['has_interface'] ?? null) === 'No') {
            return false;
        }
        if (($field['public'] ?? false) && ($answers['public_site'] ?? null) !== 'Sí') {
            return false;
        }
        if (isset($field['when'])) {
            [$key, $value] = $field['when'];

            return in_array($value, (array) ($answers[$key] ?? []), true);
        }

        return true;
    }

    public function answers(array $input, bool $submit): array
    {
        // Preserve free-form answers; remove only accidental surrounding whitespace.
        $input = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $input);
        $rules = $labels = [];
        foreach (config('onboarding.sections') as $section) {
            foreach ($section['fields'] as $field) {
                $key = $field['key'];
                if ($field['type'] === 'files' || ! $this->visible($field, $input)) {
                    unset($input[$key]);
                    continue;
                }
                $rules[$key] = ['bail', $submit && ($field['required'] ?? false) ? 'required' : 'nullable'];
                $labels[$key] = $field['label'];
                if ($field['type'] === 'multi') {
                    $rules[$key] = array_merge($rules[$key], ['array', 'max:'.($field['max'] ?? 30)]);
                    $rules[$key.'.*'] = ['string', 'distinct', Rule::in($field['options_from'] ?? false
                        ? (array) ($input[$field['options_from']] ?? []) : $field['options'])];
                } elseif ($field['type'] === 'links') {
                    if (is_array($input[$key] ?? null)) {
                        $input[$key] = array_map(function ($value) {
                            if (! is_string($value)) {
                                return $value;
                            }
                            $value = trim($value);
                            if ($value === '') {
                                return '';
                            }
                            if (str_starts_with($value, '//')) {
                                return 'https:'.$value;
                            }

                            return preg_match('/^[a-z][a-z0-9+.-]*:/i', $value) ? $value : 'https://'.$value;
                        }, $input[$key]);
                        $input[$key] = array_values(array_filter($input[$key], fn ($value) => $value !== null && $value !== ''));
                    }
                    $rules[$key] = array_merge($rules[$key], ['array', 'max:'.$field['maxLinks']]);
                    $rules[$key.'.*'] = ['required', 'string', 'url:http,https', 'max:'.($field['maxLength'] + 8)];
                } else {
                    $rules[$key][] = 'string';
                    $rules[$key][] = 'max:'.$field['maxLength'];
                    if ($field['type'] === 'choice') {
                        $rules[$key][] = Rule::in($field['options']);
                    } elseif ($field['type'] === 'email') {
                        $rules[$key][] = 'email:rfc';
                    } elseif ($field['type'] === 'date') {
                        $rules[$key][] = 'date_format:Y-m-d';
                    }
                }
            }
        }
        $answers = Validator::make($input, $rules, [
            'required' => 'Completa este campo para enviar tu información.',
            'email' => 'Escribe un correo electrónico válido.',
            'in' => 'Selecciona una de las opciones disponibles.',
            'max.string' => 'Puedes escribir hasta :max caracteres en este campo. Si necesitas más espacio, adjunta un documento.',
            'max.array' => 'Puedes agregar hasta :max opciones o enlaces en este campo.',
            'url' => 'Revisa el enlace. Por ejemplo: facebook.com/tuempresa. No necesitas escribir https://.',
            'date_format' => 'Selecciona una fecha válida en el calendario.',
            'string' => 'Escribe tu respuesta como texto.',
            'array' => 'Revisa las opciones o enlaces de este campo.',
        ], $labels)->validate();
        if (array_intersect($answers['required_features'] ?? [], $answers['optional_features'] ?? [])) {
            throw ValidationException::withMessages(['optional_features' => 'Una función no puede estar en ambas etapas.']);
        }

        return $answers;
    }
}
