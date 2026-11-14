<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemApiKey extends Model
{
    protected $table = 'system_api_keys';

    protected $fillable = [
        'key_name',
        'service_label',
        'key_value',
        'category',
        'endpoint_url',
        'description',
        'is_secret',
        'status',
        'last_tested_at',
    ];

    protected $casts = [
        'is_secret' => 'boolean',
        'last_tested_at' => 'datetime',
    ];

    /**
     * Devuelve el valor enmascarado si es secreto.
     */
    public function getMaskedValueAttribute(): string
    {
        if (empty($this->key_value)) {
            return '— (No configurada)';
        }
        if (!$this->is_secret) {
            return $this->key_value;
        }
        $len = strlen($this->key_value);
        if ($len <= 8) {
            return str_repeat('•', 8);
        }
        return substr($this->key_value, 0, 4) . str_repeat('•', max(4, $len - 8)) . substr($this->key_value, -4);
    }
}
