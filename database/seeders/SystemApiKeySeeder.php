<?php

namespace Database\Seeders;

use App\Models\SystemApiKey;
use Illuminate\Database\Seeder;

class SystemApiKeySeeder extends Seeder
{
    public function run(): void
    {
        $keys = [
            [
                'key_name' => 'NASA_API_KEY',
                'service_label' => 'NASA Open Data & Telemetry API',
                'key_value' => env('NASA_API_KEY', 'DEMO_KEY'),
                'category' => 'Aeroespacial & Telemetría',
                'endpoint_url' => 'https://api.nasa.gov/',
                'description' => 'Llave de acceso a los servicios de NASA: APOD (imágenes astronómicas), DONKI (clima espacial orbital) y OSDR (Open Science Data Repository).',
                'is_secret' => true,
                'status' => 'ACTIVE',
            ],
            [
                'key_name' => 'SCIENTIFIC_ENGINE_TOKEN',
                'service_label' => 'Python Scientific Engine Secret Bearer Token',
                'key_value' => env('SCIENTIFIC_ENGINE_TOKEN', 'spacevision-scientific-secret-token-2026'),
                'category' => 'Motor Científico Python',
                'endpoint_url' => 'http://127.0.0.1:8001/api/v1',
                'description' => 'Token Bearer interno para la comunicación autenticada entre Laravel y el microservicio FastAPI (NumPy, SciPy, Scikit-learn).',
                'is_secret' => true,
                'status' => 'ACTIVE',
            ],
            [
                'key_name' => 'SCIENTIFIC_ENGINE_URL',
                'service_label' => 'Python Scientific Engine Base URL',
                'key_value' => env('SCIENTIFIC_ENGINE_URL', 'http://127.0.0.1:8001'),
                'category' => 'Motor Científico Python',
                'endpoint_url' => 'http://127.0.0.1:8001',
                'description' => 'Dirección de red y puerto del servidor FastAPI de computación biomecánica y machine learning.',
                'is_secret' => false,
                'status' => 'ACTIVE',
            ],
            [
                'key_name' => 'GOOGLE_CLIENT_ID',
                'service_label' => 'Google Cloud OAuth 2.0 Client ID',
                'key_value' => env('GOOGLE_CLIENT_ID', ''),
                'category' => 'Autenticación & Seguridad',
                'endpoint_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'description' => 'Identificador público del proyecto en Google Cloud para autenticación de astronautas e investigadores biomédicos.',
                'is_secret' => false,
                'status' => 'ACTIVE',
            ],
            [
                'key_name' => 'GOOGLE_CLIENT_SECRET',
                'service_label' => 'Google Cloud OAuth 2.0 Client Secret',
                'key_value' => env('GOOGLE_CLIENT_SECRET', ''),
                'category' => 'Autenticación & Seguridad',
                'endpoint_url' => 'https://oauth2.googleapis.com/token',
                'description' => 'Clave secreta del proveedor OAuth 2.0 para intercambio de tokens de sesión autenticada.',
                'is_secret' => true,
                'status' => 'ACTIVE',
            ],
            [
                'key_name' => 'RECAPTCHA_SITE_KEY',
                'service_label' => 'Google reCAPTCHA v3 Site Key',
                'key_value' => env('RECAPTCHA_SITE_KEY', '6Lc9DxcuAAAAAF3ClTdOZsJ3gcgFMx8p0ysOZqxs'),
                'category' => 'Autenticación & Seguridad',
                'endpoint_url' => 'https://www.google.com/recaptcha/api/siteverify',
                'description' => 'Clave pública para protección antirobot y verificación biométrica de formularios de contacto y registro.',
                'is_secret' => false,
                'status' => 'ACTIVE',
            ],
            [
                'key_name' => 'RECAPTCHA_SECRET_KEY',
                'service_label' => 'Google reCAPTCHA v3 Secret Key',
                'key_value' => env('RECAPTCHA_SECRET_KEY', ''),
                'category' => 'Autenticación & Seguridad',
                'endpoint_url' => 'https://www.google.com/recaptcha/api/siteverify',
                'description' => 'Clave privada de servidor para cálculo del score de validación humana.',
                'is_secret' => true,
                'status' => 'ACTIVE',
            ],
            [
                'key_name' => 'GEMINI_API_KEY',
                'service_label' => 'Google Gemini AI / Multimodal Vision Key',
                'key_value' => env('GEMINI_API_KEY', ''),
                'category' => 'Inteligencia Artificial',
                'endpoint_url' => 'https://generativelanguage.googleapis.com/v1beta',
                'description' => 'Clave opcional para modelos fundacionales multimodales y análisis semántico extendido de imágenes de retina.',
                'is_secret' => true,
                'status' => 'OPTIONAL',
            ],
        ];

        foreach ($keys as $data) {
            SystemApiKey::updateOrCreate(
                ['key_name' => $data['key_name']],
                $data
            );
        }
    }
}
