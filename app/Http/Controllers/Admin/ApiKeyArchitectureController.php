<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemApiKey;
use App\Services\ScientificEngineService;
use App\Services\NasaDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiKeyArchitectureController extends Controller
{
    protected ScientificEngineService $scientificEngine;
    protected NasaDataService $nasaService;

    public function __construct(ScientificEngineService $scientificEngine, NasaDataService $nasaService)
    {
        $this->scientificEngine = $scientificEngine;
        $this->nasaService = $nasaService;
    }

    public function index()
    {
        $apiKeys = SystemApiKey::orderBy('category')->orderBy('key_name')->get();
        $groupedKeys = $apiKeys->groupBy('category');

        // Estado en vivo de las dos integraciones críticas
        $scientificEngineStatus = $this->scientificEngine->isOnline();
        $scientificEngineHealth = $this->scientificEngine->getHealth();

        $nasaStatus = 'ONLINE';
        try {
            $nasaKey = SystemApiKey::where('key_name', 'NASA_API_KEY')->value('key_value') ?: env('NASA_API_KEY', 'DEMO_KEY');
            $res = Http::timeout(2.0)->get("https://api.nasa.gov/planetary/apod?api_key={$nasaKey}");
            if (!$res->successful() && $res->status() !== 429) {
                $nasaStatus = 'WARNING (' . $res->status() . ')';
            }
        } catch (\Throwable $e) {
            $nasaStatus = 'CACHE_BACKUP_ACTIVE';
        }

        return view('admin.api-keys.index', compact(
            'apiKeys',
            'groupedKeys',
            'scientificEngineStatus',
            'scientificEngineHealth',
            'nasaStatus'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key_name' => 'required|string|max:64|unique:system_api_keys,key_name|regex:/^[A-Z0-9_]+$/',
            'service_label' => 'required|string|max:120',
            'key_value' => 'nullable|string',
            'category' => 'required|string|max:60',
            'endpoint_url' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:500',
            'is_secret' => 'nullable|boolean',
            'status' => 'required|in:ACTIVE,OPTIONAL,DISABLED',
        ]);

        $validated['is_secret'] = $request->boolean('is_secret', true);

        SystemApiKey::create($validated);

        return redirect()->route('admin.api-keys.index')
            ->with('status', "Clave de API '{$validated['key_name']}' registrada correctamente.");
    }

    public function update(Request $request, $id)
    {
        $key = SystemApiKey::findOrFail($id);

        $validated = $request->validate([
            'service_label' => 'required|string|max:120',
            'key_value' => 'nullable|string',
            'category' => 'required|string|max:60',
            'endpoint_url' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:500',
            'is_secret' => 'nullable|boolean',
            'status' => 'required|in:ACTIVE,OPTIONAL,DISABLED',
        ]);

        $validated['is_secret'] = $request->boolean('is_secret', true);

        // Si se envió un valor no vacío o explícitamente vacío
        if ($request->has('key_value')) {
            $validated['key_value'] = $request->input('key_value');
        }

        $key->update($validated);

        return redirect()->route('admin.api-keys.index')
            ->with('status', "Configuración de '{$key->key_name}' actualizada exitosamente.");
    }

    public function destroy($id)
    {
        $key = SystemApiKey::findOrFail($id);
        $name = $key->key_name;

        // Proteger llaves de sistema fundamentales de borrado accidental
        $coreKeys = ['NASA_API_KEY', 'SCIENTIFIC_ENGINE_TOKEN', 'SCIENTIFIC_ENGINE_URL', 'GOOGLE_CLIENT_ID'];
        if (in_array($name, $coreKeys)) {
            return redirect()->route('admin.api-keys.index')
                ->with('error', "La clave '{$name}' es un componente estructural del núcleo de misión y no puede ser eliminada (puedes cambiar su estado a DISABLED).");
        }

        $key->delete();

        return redirect()->route('admin.api-keys.index')
            ->with('status', "Clave de API '{$name}' eliminada del sistema.");
    }

    public function syncToEnv()
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath) || !is_writable($envPath)) {
            return redirect()->route('admin.api-keys.index')
                ->with('error', 'El archivo .env no tiene permisos de escritura o no existe en la raíz.');
        }

        $envContent = file_get_contents($envPath);
        $keys = SystemApiKey::whereNotNull('key_value')->where('key_value', '!=', '')->get();
        $updatedCount = 0;

        foreach ($keys as $item) {
            $key = $item->key_name;
            $val = $item->key_value;
            $escapedVal = (preg_match('/\s/', $val) || str_contains($val, '#')) ? '"' . addcslashes($val, '"') . '"' : $val;

            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$escapedVal}", $envContent);
                $updatedCount++;
            } else {
                $envContent .= "\n{$key}={$escapedVal}";
                $updatedCount++;
            }
        }

        file_put_contents($envPath, $envContent);

        return redirect()->route('admin.api-keys.index')
            ->with('status', "Sincronización completada: {$updatedCount} variables actualizadas en el entorno .env de SPACE VISION.");
    }

    public function testPing($id)
    {
        $key = SystemApiKey::findOrFail($id);
        $start = microtime(true);

        try {
            if ($key->key_name === 'SCIENTIFIC_ENGINE_TOKEN' || $key->key_name === 'SCIENTIFIC_ENGINE_URL') {
                $online = $this->scientificEngine->isOnline();
                $latency = round((microtime(true) - $start) * 1000, 1);
                $key->update(['last_tested_at' => now()]);

                return response()->json([
                    'success' => $online,
                    'latency_ms' => $latency,
                    'message' => $online ? "FastAPI Scientific Engine respondiendo en {$latency} ms" : "El microservicio Python está OFFLINE (usando fallback PHP).",
                ]);
            }

            if ($key->key_name === 'NASA_API_KEY') {
                $val = $key->key_value ?: 'DEMO_KEY';
                $res = Http::timeout(4.0)->get("https://api.nasa.gov/planetary/apod?api_key={$val}");
                $latency = round((microtime(true) - $start) * 1000, 1);
                $key->update(['last_tested_at' => now()]);

                if ($res->successful() || $res->status() === 429) {
                    return response()->json([
                        'success' => true,
                        'latency_ms' => $latency,
                        'message' => "NASA API respondiendo exitosamente ({$res->status()}) en {$latency} ms.",
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'latency_ms' => $latency,
                    'message' => "NASA API respondió código {$res->status()}: " . $res->body(),
                ]);
            }

            if (!empty($key->endpoint_url)) {
                $res = Http::timeout(3.0)->get($key->endpoint_url);
                $latency = round((microtime(true) - $start) * 1000, 1);
                $key->update(['last_tested_at' => now()]);

                return response()->json([
                    'success' => $res->successful() || $res->status() < 500,
                    'latency_ms' => $latency,
                    'message' => "Endpoint respondió código HTTP {$res->status()} en {$latency} ms.",
                ]);
            }

            return response()->json([
                'success' => true,
                'latency_ms' => 0,
                'message' => "Clave registrada sintácticamente válida.",
            ]);
        } catch (\Throwable $e) {
            $latency = round((microtime(true) - $start) * 1000, 1);
            return response()->json([
                'success' => false,
                'latency_ms' => $latency,
                'message' => "Fallo de conexión: " . $e->getMessage(),
            ]);
        }
    }
}
