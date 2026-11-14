<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NasaDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NasaObservatoryController extends Controller
{
    public function __construct(
        protected NasaDataService $nasaService
    ) {}

    public function index(Request $request): View
    {
        $activeTab = $request->query('tab', 'osdr');
        $query = $request->query('q');

        $osdrStudies = $this->nasaService->getOsdrStudies($activeTab === 'osdr' ? $query : null);
        $ntrsPubs = $this->nasaService->getNtrsPublications($activeTab === 'ntrs' ? $query : null);
        $donkiAlerts = $this->nasaService->getDonkiAlerts();
        $apod = $this->nasaService->getApod();
        $healthStatus = $this->nasaService->getApiHealthStatus();

        return view('admin.nasa-data.index', compact(
            'activeTab',
            'query',
            'osdrStudies',
            'ntrsPubs',
            'donkiAlerts',
            'apod',
            'healthStatus'
        ));
    }
}
