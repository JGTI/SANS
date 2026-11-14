@extends('layouts.app')

@section('title', 'NASA Data Observatory · Observatorio Científico Aeroespacial')

@push('styles')
<style>
    .observatory-hero {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: var(--shadow);
    }
    .hero-content {
        max-width: 650px;
    }
    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--foreground);
        background: var(--muted);
        border: 1px solid var(--border);
        padding: 4px 10px;
        border-radius: 9999px;
        margin-bottom: 10px;
    }
    .tabs-nav {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 24px;
        overflow-x: auto;
    }
    .tab-btn {
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 600;
        color: var(--muted-foreground);
        text-decoration: none;
        border-bottom: 2px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .tab-btn:hover {
        color: var(--foreground);
    }
    .tab-btn.active {
        color: var(--foreground);
        border-bottom-color: var(--foreground);
        font-weight: 700;
    }
    .dataset-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 20px;
    }
    .dataset-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        box-shadow: var(--shadow);
    }
    .dataset-card:hover {
        border-color: var(--foreground);
    }
    .card-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
    }
    .accession-badge {
        font-family: ui-monospace, monospace;
        font-size: 11px;
        font-weight: 700;
        background: var(--muted);
        color: var(--foreground);
        border: 1px solid var(--border);
        padding: 3px 8px;
        border-radius: 4px;
    }
    .disclaimer-banner {
        background: var(--muted);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 12px;
        color: var(--foreground);
        line-height: 1.6;
    }
    .health-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="observatory-hero">
    <div class="hero-content">
        <span class="hero-tag">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
            NASA Open Science & Research Repository
        </span>
        <h1 style="margin:0 0 8px; font-size:24px; font-weight:800; color:var(--foreground); letter-spacing:-0.03em;">NASA Data Observatory</h1>
        <p style="margin:0; font-size:13px; color:var(--muted-foreground); line-height:1.5;">
            Integración de repositorios científicos de la NASA: estudios biológicos en microgravedad (OSDR), literatura médica técnica (NTRS) y telemetría espacial (DONKI).
        </p>
    </div>
    <div style="text-align:right;">
        <span style="display:inline-block; font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace; margin-bottom:4px;">REDUNDANCIA ACTIVA</span>
        <div>
            <span class="badge-subtle" style="font-weight:700;">
                <i class="fa fa-shield"></i> MODO DEMO 100% OPERATIVO
            </span>
        </div>
    </div>
</div>

{{-- Barra de Navegación por Pestañas --}}
<div class="tabs-nav">
    <a href="{{ route('admin.nasa-data', ['tab' => 'osdr']) }}" class="tab-btn {{ $activeTab === 'osdr' ? 'active' : '' }}">
        <i class="fa fa-flask"></i> NASA OSDR (Genómica & Tejidos Oculares)
        <span style="font-size:11px; background:var(--muted); border:1px solid var(--border); padding:2px 6px; border-radius:10px;">{{ count($osdrStudies) }}</span>
    </a>
    <a href="{{ route('admin.nasa-data', ['tab' => 'ntrs']) }}" class="tab-btn {{ $activeTab === 'ntrs' ? 'active' : '' }}">
        <i class="fa fa-book"></i> NASA NTRS (Evidencia Científica SANS)
        <span style="font-size:11px; background:var(--muted); border:1px solid var(--border); padding:2px 6px; border-radius:10px;">{{ count($ntrsPubs) }}</span>
    </a>
    <a href="{{ route('admin.nasa-data', ['tab' => 'donki']) }}" class="tab-btn {{ $activeTab === 'donki' ? 'active' : '' }}">
        <i class="fa fa-sun-o"></i> NASA DONKI (Clima Espacial de Misión)
        <span style="font-size:11px; background:var(--muted); border:1px solid var(--border); padding:2px 6px; border-radius:10px;">{{ count($donkiAlerts) }}</span>
    </a>
    <a href="{{ route('admin.nasa-data', ['tab' => 'health']) }}" class="tab-btn {{ $activeTab === 'health' ? 'active' : '' }}">
        <i class="fa fa-server"></i> Diagnóstico de APIs & Redundancia
    </a>
</div>

{{-- Pestaña 1: NASA OSDR --}}
@if ($activeTab === 'osdr')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 style="font-size:16px; font-weight:700; margin:0 0 4px; color:var(--foreground);">Estudios de Tejidos Oculares en Microgravedad</h2>
            <p style="font-size:12px; color:var(--muted-foreground); margin:0;">Datos de transcriptómica, retina y nervio óptico de misiones orbitales (ISS / Transbordador Espacial).</p>
        </div>
        <form method="GET" action="{{ route('admin.nasa-data') }}" style="display:flex; gap:8px;">
            <input type="hidden" name="tab" value="osdr">
            <input type="text" name="q" value="{{ $query }}" placeholder="Filtrar por gen, tejido o estudio..." class="form-control" style="width:260px; height:34px; font-size:12px;">
            <button type="submit" class="btn btn-primary" style="padding:6px 14px; font-size:12px; min-height:34px;">Buscar</button>
        </form>
    </div>

    <div class="dataset-grid">
        @forelse ($osdrStudies as $study)
            @php
                $sAccession = is_array($study['accession'] ?? '') ? json_encode($study['accession']) : (string)($study['accession'] ?? '');
                $sDate = is_array($study['release_date'] ?? '') ? '' : (string)($study['release_date'] ?? '');
                $sTitle = is_array($study['title'] ?? '') ? json_encode($study['title']) : (string)($study['title'] ?? '');
                $sMission = is_array($study['mission'] ?? '') ? ($study['mission']['Name'] ?? json_encode($study['mission'])) : (string)($study['mission'] ?? '');
                $sTissue = is_array($study['tissue'] ?? '') ? implode(', ', $study['tissue']) : (string)($study['tissue'] ?? '');
                $sAssay = is_array($study['assay'] ?? '') ? implode(', ', $study['assay']) : (string)($study['assay'] ?? '');
                $sDesc = is_array($study['description'] ?? '') ? json_encode($study['description']) : (string)($study['description'] ?? '');
                $sRelevance = is_array($study['relevance'] ?? '') ? json_encode($study['relevance']) : (string)($study['relevance'] ?? '');
                $sUrl = is_array($study['dataset_url'] ?? '') ? '#' : (string)($study['dataset_url'] ?? '');
            @endphp
            <div class="dataset-card">
                <div>
                    <div class="card-header-row">
                        <span class="accession-badge">{{ $sAccession }}</span>
                        <span style="font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace;">{{ $sDate }}</span>
                    </div>
                    <h3 style="font-size:14px; font-weight:700; color:var(--foreground); margin:0 0 8px; line-height:1.4;">{{ $sTitle }}</h3>
                    <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:12px;">
                        <span style="font-size:10px; background:var(--muted); color:var(--muted-foreground); border:1px solid var(--border); padding:2px 8px; border-radius:4px;"><i class="fa fa-rocket"></i> {{ $sMission }}</span>
                        <span style="font-size:10px; background:var(--muted); color:var(--muted-foreground); border:1px solid var(--border); padding:2px 8px; border-radius:4px;"><i class="fa fa-eye"></i> {{ $sTissue }}</span>
                        <span style="font-size:10px; background:var(--muted); color:var(--muted-foreground); border:1px solid var(--border); padding:2px 8px; border-radius:4px;"><i class="fa fa-code-fork"></i> {{ $sAssay }}</span>
                    </div>
                    <p style="font-size:12px; color:var(--muted-foreground); line-height:1.5; margin-bottom:14px;">
                        {{ $sDesc }}
                    </p>
                </div>
                <div style="border-top:1px solid var(--border); padding-top:12px; margin-top:8px;">
                    <div style="font-size:11px; color:var(--foreground); margin-bottom:8px;">
                        <strong>Relevancia SANS:</strong> <span style="color:var(--muted-foreground);">{{ $sRelevance }}</span>
                    </div>
                    <a href="{{ $sUrl }}" target="_blank" rel="noopener noreferrer" style="font-size:12px; color:var(--foreground); text-decoration:underline; text-underline-offset:3px; font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                        Ver estudio en repositorio oficial de NASA <i class="fa fa-external-link"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align:center; padding:40px;">
                <p style="color:var(--muted-foreground); margin:0;">No se encontraron estudios coincidentes con "{{ $query }}".</p>
            </div>
        @endforelse
    </div>

{{-- Pestaña 2: NASA NTRS --}}
@elseif ($activeTab === 'ntrs')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <div>
            <h2 style="font-size:16px; font-weight:700; margin:0 0 4px; color:var(--foreground);">NASA Technical Reports Server (NTRS)</h2>
            <p style="font-size:12px; color:var(--muted-foreground); margin:0;">Publicaciones revisadas por pares y reportes de evidencia del NASA Human Research Program.</p>
        </div>
        <form method="GET" action="{{ route('admin.nasa-data') }}" style="display:flex; gap:8px;">
            <input type="hidden" name="tab" value="ntrs">
            <input type="text" name="q" value="{{ $query }}" placeholder="Filtrar por autor o tema..." class="form-control" style="width:260px; height:34px; font-size:12px;">
            <button type="submit" class="btn btn-primary" style="padding:6px 14px; font-size:12px; min-height:34px;">Buscar</button>
        </form>
    </div>

    <div style="display:flex; flex-direction:column; gap:16px;">
        @forelse ($ntrsPubs as $pub)
            <div class="card" style="padding:20px; margin:0;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:6px;">
                    <div style="display:flex; gap:8px; align-items:center;">
                        <span class="accession-badge">NTRS #{{ $pub['id'] }}</span>
                        <span class="badge-subtle">{{ $pub['document_type'] }}</span>
                    </div>
                    <span style="font-size:12px; font-weight:700; color:var(--foreground); font-family:ui-monospace, monospace;">{{ $pub['year'] }}</span>
                </div>
                <h3 style="font-size:15px; font-weight:700; color:var(--foreground); margin:6px 0;">{{ $pub['title'] }}</h3>
                <div style="font-size:12px; color:var(--foreground); margin-bottom:8px;">
                    <i class="fa fa-user"></i> {{ $pub['authors'] }} · <span style="color:var(--muted-foreground);">{{ $pub['center'] }}</span>
                </div>
                <p style="font-size:13px; color:var(--muted-foreground); line-height:1.6; margin-bottom:12px;">
                    {{ $pub['abstract'] }}
                </p>
                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:10px;">
                    <span style="font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace;">DOI: {{ $pub['doi'] }}</span>
                    <a href="{{ $pub['url'] }}" target="_blank" rel="noopener noreferrer" style="font-size:12px; color:var(--foreground); text-decoration:underline; text-underline-offset:3px; font-weight:600;">
                        Consultar en NASA NTRS <i class="fa fa-external-link"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="card" style="text-align:center; padding:40px;">
                <p style="color:var(--muted-foreground); margin:0;">No se encontraron reportes técnicos coincidentes con "{{ $query }}".</p>
            </div>
        @endforelse
    </div>

{{-- Pestaña 3: NASA DONKI --}}
@elseif ($activeTab === 'donki')
    <div style="margin-bottom:20px;">
        <h2 style="font-size:16px; font-weight:700; margin:0 0 4px; color:var(--foreground);">Telemetría de Clima Espacial (NASA DONKI)</h2>
        <p style="font-size:12px; color:var(--muted-foreground); margin:0;">Monitoreo orbital de eyecciones de masa coronal (CME), llamaradas solares y tormentas geomagnéticas.</p>
    </div>

    {{-- Aviso de Rigor Científico Obligatorio --}}
    <div class="disclaimer-banner">
        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>
        <strong>DECLARACIÓN DE RESPONSABILIDAD CIENTÍFICA:</strong>
        La telemetría de radiación solar y clima espacial de NASA DONKI se incorpora exclusivamente como parámetro contextual de la misión aeroespacial. La investigación clínica actual de NASA <em>no establece</em> una relación de causalidad directa entre eventos solares aislados y el edema de disco o cambios refractivos de SANS, cuya etiología primaria responde a la redistribución cefálica de fluidos por microgravedad.
    </div>

    <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach ($donkiAlerts as $alert)
            <div class="card" style="padding:20px; margin:0;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="badge-subtle" style="font-weight:700;">
                            {{ $alert['messageType'] }}
                        </span>
                        <span style="font-family:ui-monospace, monospace; font-size:11px; color:var(--muted-foreground);">{{ $alert['messageID'] }}</span>
                    </div>
                    <span style="font-size:12px; font-family:ui-monospace, monospace; color:var(--muted-foreground);">{{ substr($alert['messageIssueTime'], 0, 16) }} UTC</span>
                </div>
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-bottom:12px;">
                    <div style="background:var(--muted); border:1px solid var(--border); padding:8px 12px; border-radius:6px;">
                        <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Velocidad / Magnitud</div>
                        <div style="font-size:13px; font-weight:700; color:var(--foreground);">{{ $alert['speed'] }}</div>
                    </div>
                    <div style="background:var(--muted); border:1px solid var(--border); padding:8px 12px; border-radius:6px;">
                        <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Trayectoria Objetivo</div>
                        <div style="font-size:13px; font-weight:700; color:var(--foreground);">{{ $alert['target'] }}</div>
                    </div>
                    <div style="background:var(--muted); border:1px solid var(--border); padding:8px 12px; border-radius:6px;">
                        <div style="font-size:10px; text-transform:uppercase; color:var(--muted-foreground); font-weight:700;">Impacto Estimado</div>
                        <div style="font-size:13px; font-weight:700; color:var(--foreground);">{{ $alert['estimatedImpact'] }}</div>
                    </div>
                </div>
                <p style="font-size:13px; color:var(--muted-foreground); line-height:1.5; margin:0;">
                    {{ $alert['summary'] }}
                </p>
            </div>
        @endforeach
    </div>

{{-- Pestaña 4: Diagnóstico de APIs & Redundancia --}}
@elseif ($activeTab === 'health')
    <div style="margin-bottom:20px;">
        <h2 style="font-size:16px; font-weight:700; margin:0 0 4px; color:var(--foreground);">Diagnóstico de Integraciones NASA & Resiliencia Operativa</h2>
        <p style="font-size:12px; color:var(--muted-foreground); margin:0;">Arquitectura de alta disponibilidad con caché sincronizada y degradación elegante ante caídas de red externa.</p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:16px;">
        @foreach ($healthStatus as $key => $status)
            <div class="card" style="padding:20px; margin:0;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-size:13px; font-weight:700; color:var(--foreground);">{{ $status['name'] }}</span>
                    <span class="badge-subtle" style="font-weight:700;">
                        <i class="fa fa-check-circle"></i> ACTIVO
                    </span>
                </div>
                <div style="font-size:11px; color:var(--muted-foreground); font-family:ui-monospace, monospace; margin-bottom:8px; word-break:break-all;">
                    {{ $status['endpoint'] }}
                </div>
                <div style="font-size:12px; color:var(--muted-foreground); margin-bottom:12px;">
                    <strong>Tipo:</strong> {{ $status['type'] }}
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:10px; font-size:12px;">
                    <span style="color:var(--muted-foreground);">Objetos en memoria / caché:</span>
                    <strong style="color:var(--foreground); font-family:ui-monospace, monospace;">{{ $status['items_cached'] }} registros</strong>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
