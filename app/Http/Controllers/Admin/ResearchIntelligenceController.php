<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NasaDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResearchIntelligenceController extends Controller
{
    public function __construct(
        protected NasaDataService $nasaService
    ) {}

    public function index(Request $request): View
    {
        $ntrsPubs = $this->nasaService->getNtrsPublications();
        $osdrStudies = $this->nasaService->getOsdrStudies();

        // Hipótesis biomecánicas y genéticas documentadas en NASA Human Research Roadmap
        $hypotheses = [
            [
                'id' => 'HYP-01',
                'title' => 'Gradiente de Presión Translaminar (IOP vs ICP)',
                'category' => 'Biomecánica de Fluidos',
                'status' => 'Fuerte Evidencia en ISS',
                'description' => 'En la Tierra, la postura erecta reduce la presión intracraneal (ICP) respecto a la presión intraocular (IOP). En microgravedad, la pérdida de este ciclo diurno mantiene una ICP crónicamente elevada en el espacio subaracnoideo retrobulbar, generando una fuerza neta posterior que protruye la papila y aplana la esclera.',
                'nasa_ref' => 'NTRS #20200002341 (Stenger et al.)',
                'countermeasure' => 'Dispositivos de Presión Negativa en Miembros Inferiores (LBNP nocturno)',
            ],
            [
                'id' => 'HYP-02',
                'title' => 'Congestión Venosa Yugular y Drenaje Coroideo',
                'category' => 'Hemodinámica',
                'status' => 'Comprobado por Ecografía en Órbita',
                'description' => 'La redistribución de 2 litros de sangre y líquido intersticial al tercio superior del cuerpo distiende las venas yugulares internas y eleva la presión venosa central. Esto retarda el drenaje coroideo a través de las venas vorticosas, engrosando la coroides y produciendo pliegues retinianos.',
                'nasa_ref' => 'NTRS #20220011504 (Macias et al.)',
                'countermeasure' => 'Suits de compresión gradiente (Charly suit) y protocolos de ejercicio resistivo',
            ],
            [
                'id' => 'HYP-03',
                'title' => 'Polimorfismos Genéticos del Metabolismo de Un Carbono',
                'category' => 'Genómica / Susceptibilidad',
                'status' => 'Investigación Activa (NASA Twins Study)',
                'description' => 'Se ha detectado una mayor concentración de metabolitos de la vía de la homocisteína y folato en astronautas que desarrollan edema de papila severo, sugiriendo una susceptibilidad vascular de base genética ante el estrés oxidativo de la microgravedad.',
                'nasa_ref' => 'OSD-370 / NASA Twins Study',
                'countermeasure' => 'Suplementación de vitaminas del complejo B y antioxidantes endoteliales',
            ],
            [
                'id' => 'HYP-04',
                'title' => 'Remodelación de la Matriz Extracelular Escleral',
                'category' => 'Biomecánica Tisular',
                'status' => 'Modelo Animal / OSD-137',
                'description' => 'Alteraciones en la síntesis de colágeno tipo I y proteoglicanos en la esclera peripapilar bajo microgravedad reducen la rigidez elástica del polo posterior del ojo, facilitando el aplanamiento axial.',
                'nasa_ref' => 'NASA OSDR OSD-137',
                'countermeasure' => 'Monitoreo de biomarcadores con OCT de alta resolución',
            ],
        ];

        return view('admin.research-intelligence.index', compact('ntrsPubs', 'osdrStudies', 'hypotheses'));
    }
}
