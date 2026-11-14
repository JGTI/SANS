<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ScientificEngineService;
use Illuminate\Http\Request;

class ModelRegistryController extends Controller
{
    protected ScientificEngineService $engine;

    public function __construct(ScientificEngineService $engine)
    {
        $this->engine = $engine;
    }

    public function index()
    {
        $registryData = $this->engine->getModelRegistry();
        $models = $registryData['models'] ?? [];
        $isOnline = $this->engine->isOnline();

        return view('admin.model-registry.index', compact('models', 'isOnline'));
    }
}
