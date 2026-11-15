@php
    $crew = \App\Models\AstronautProfile::crewMetaForCase($activeCase->case_id);
@endphp

<section class="crew-app" data-crew-app data-locale="{{ $isEn ? 'en' : 'es' }}" data-case="{{ $activeCase->case_id }}">
    {{-- Orbital Flight Logbook Telemetry Strip (High-Contrast Monochrome) --}}
    <div style="background:#09090b;color:#ffffff;border:1px solid #27272a;border-radius:10px;padding:0.65rem 1rem;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;gap:0.75rem;flex-wrap:wrap;font-family:ui-monospace,monospace;font-size:0.74rem;">
        <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
            <span style="background:#ffffff;color:#09090b;font-weight:900;padding:0.15rem 0.5rem;border-radius:4px;letter-spacing:0.06em;">
                <i class="fa fa-space-shuttle"></i> {{ $isEn ? 'ORBITAL FLIGHT LOG' : 'BITÁCORA ESPACIAL DE VUELO' }}
            </span>
            <span style="color:#a1a1aa;">ISS ALT: <strong style="color:#ffffff;">408 km</strong></span>
            <span style="color:#a1a1aa;">GRAVITY: <strong style="color:#ffffff;">0.00 G (Microgravity)</strong></span>
            <span style="color:#a1a1aa;">RECORD: <strong style="color:#ffffff;">{{ $activeCase->case_id }}</strong></span>
        </div>
        <div style="display:flex;align-items:center;gap:0.35rem;flex-wrap:wrap;">
            @foreach($cases as $cItem)
                @php $cMeta = \App\Models\AstronautProfile::crewMetaForCase($cItem->case_id); @endphp
                <a href="{{ route('public.astronaut', ['case_id' => $cItem->case_id, 'lang' => app()->getLocale()]) }}"
                   style="padding:0.2rem 0.55rem;border-radius:5px;text-decoration:none;font-size:0.68rem;font-weight:800;border:1px solid {{ $cItem->case_id === $activeCase->case_id ? '#ffffff' : '#3f3f46' }};background:{{ $cItem->case_id === $activeCase->case_id ? '#ffffff' : '#18181b' }};color:{{ $cItem->case_id === $activeCase->case_id ? '#09090b' : '#d4d4d8' }};">
                    {{ $cMeta['name'] }}
                </a>
            @endforeach
        </div>
    </div>

    <header class="crew-hero">
        <div class="crew-identity">
            <img src="{{ $crew['avatar'] }}" alt="{{ $crew['name'] }}" class="crew-avatar">
            <div>
                <p class="crew-kicker">{{ $isEn ? 'Mission day 30 · Personal eye log · ' . $activeCase->case_id : 'Día 30 de misión · Bitácora visual personal · ' . $activeCase->case_id }}</p>
                <h1>{{ $isEn ? 'Good morning,' : 'Buenos días,' }} {{ $crew['name'] }}</h1>
                <p>{{ $isEn ? 'Your next check is ready. It takes about 8 minutes.' : 'Tu siguiente revisión está lista. Toma aproximadamente 8 minutos.' }}</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <label for="crewAstronautSelect" style="display:flex;align-items:center;gap:6px;background:#ffffff;border:1px solid #d4d4d8;border-radius:8px;padding:4px 10px;font-size:0.78rem;font-weight:700;color:#09090b;">
                <i class="fa fa-user-circle"></i>
                <span>{{ $isEn ? 'Astronaut:' : 'Astronauta:' }}</span>
                <select id="crewAstronautSelect" aria-label="{{ $isEn ? 'Switch astronaut' : 'Cambiar de astronauta' }}" onchange="if(this.value) window.location.href=this.value;" style="border:none;background:transparent;font-weight:800;color:#09090b;cursor:pointer;outline:none;font-size:0.78rem;">
                    @foreach($cases as $cItem)
                        @php $cMeta = \App\Models\AstronautProfile::crewMetaForCase($cItem->case_id); @endphp
                        <option value="{{ route('public.astronaut', ['case_id' => $cItem->case_id, 'lang' => app()->getLocale()]) }}" {{ $cItem->case_id === $activeCase->case_id ? 'selected' : '' }}>
                            {{ $cMeta['name'] }} ({{ $cItem->case_id }})
                        </option>
                    @endforeach
                </select>
            </label>
            <div class="crew-sync" role="status">
                <span class="crew-sync-dot" aria-hidden="true"></span>
                {{ $isEn ? 'Synced with ground team' : 'Sincronizado con el equipo en Tierra' }}
            </div>
        </div>
    </header>

    <nav class="crew-tabs" aria-label="{{ $isEn ? 'Astronaut workspace' : 'Espacio del astronauta' }}">
        <button type="button" class="crew-tab is-active" data-crew-tab="today"><i class="fa fa-sun-o"></i><span>01 · {{ $isEn ? 'Today & 3D Eye' : 'Hoy y Ojo 3D' }}</span></button>
        <button type="button" class="crew-tab" data-crew-tab="log"><i class="fa fa-book"></i><span>02 · {{ $isEn ? 'Logbook' : 'Bitácora' }}</span></button>
        <button type="button" class="crew-tab" data-crew-tab="trends"><i class="fa fa-line-chart"></i><span>03 · {{ $isEn ? 'Trends' : 'Tendencias' }}</span></button>
        <button type="button" class="crew-tab" data-crew-tab="profile"><i class="fa fa-user-o"></i><span>04 · {{ $isEn ? 'Profile' : 'Perfil' }}</span></button>
    </nav>

    <div class="crew-view is-active" data-crew-view="today">
        {{-- Quick-Locate Action Strip --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:10px;margin-bottom:16px;">
            <button type="button" data-start-check style="text-align:left;background:#09090b;color:#ffffff;border:1px solid #09090b;border-radius:10px;padding:12px 14px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <span><small style="display:block;font-family:ui-monospace,monospace;font-size:10px;color:#a1a1aa;letter-spacing:0.06em;">WIZARD 1-BY-1</small><strong style="font-size:13px;">{{ $isEn ? 'Start Guided Check' : 'Iniciar Revisión' }}</strong></span>
                <i class="fa fa-arrow-right"></i>
            </button>
            <button type="button" data-open-tab="log" style="text-align:left;background:#ffffff;color:#09090b;border:1px solid #e4e4e7;border-radius:10px;padding:12px 14px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <span><small style="display:block;font-family:ui-monospace,monospace;font-size:10px;color:#71717a;letter-spacing:0.06em;">TIMELINE</small><strong style="font-size:13px;">{{ $isEn ? 'Mission Logbook' : 'Bitácora de Misión' }}</strong></span>
                <i class="fa fa-book"></i>
            </button>
            <button type="button" data-open-tab="trends" style="text-align:left;background:#ffffff;color:#09090b;border:1px solid #e4e4e7;border-radius:10px;padding:12px 14px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <span><small style="display:block;font-family:ui-monospace,monospace;font-size:10px;color:#71717a;letter-spacing:0.06em;">BIOMARKERS</small><strong style="font-size:13px;">{{ $isEn ? 'IOP & OCT Trends' : 'Tendencias PIO/OCT' }}</strong></span>
                <i class="fa fa-line-chart"></i>
            </button>
            <a href="{{ route('public.digital-twin', ['mode' => 'dual', 'case_id' => $activeCase->case_id, 'lang' => app()->getLocale()]) }}" style="text-decoration:none;text-align:left;background:#ffffff;color:#09090b;border:1px solid #e4e4e7;border-radius:10px;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <span><small style="display:block;font-family:ui-monospace,monospace;font-size:10px;color:#71717a;letter-spacing:0.06em;">DIGITAL TWIN</small><strong style="font-size:13px;">{{ $isEn ? 'Full 3D + OCT' : 'Visor 3D + OCT' }}</strong></span>
                <i class="fa fa-cube"></i>
            </a>
        </div>

        {{-- Above-the-fold: 3D Orbital Eye Monitor + Guided Check --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;align-items:stretch;margin-bottom:16px;">
            <article class="crew-primary-panel" style="margin:0;">
                <div class="crew-panel-icon" aria-hidden="true"><i class="fa fa-eye"></i></div>
                <p class="crew-eyebrow">{{ $isEn ? 'TODAY’S CHECK' : 'REVISIÓN DE HOY' }}</p>
                <h2>{{ $isEn ? 'How are your eyes feeling?' : '¿Cómo se sienten tus ojos?' }}</h2>
                <p>{{ $isEn ? 'We will record symptoms, six visual checks (1-by-1 wizard), eye pressure and context.' : 'Registraremos síntomas, seis pruebas visuales (asistente 1 a 1), presión ocular y contexto.' }}</p>
                <ul class="crew-check-list" aria-label="{{ $isEn ? 'Included activities' : 'Actividades incluidas' }}">
                    <li><i class="fa fa-check"></i> {{ $isEn ? 'Symptoms and wellbeing' : 'Síntomas y bienestar' }}</li>
                    <li><i class="fa fa-check"></i> {{ $isEn ? '6-step visual wizard (Acuity, Amsler, Contrast)' : 'Asistente visual de 6 pasos (Agudeza, Amsler, Contraste)' }}</li>
                    <li><i class="fa fa-check"></i> {{ $isEn ? 'Eye pressure and physiological context' : 'Presión ocular y contexto fisiológico' }}</li>
                </ul>
                <button type="button" class="crew-primary-action" style="background:#09090b;border-color:#09090b;" data-start-check>
                    {{ $isEn ? 'Start today’s check' : 'Comenzar revisión de hoy' }} <i class="fa fa-arrow-right"></i>
                </button>
                <p class="crew-action-note"><i class="fa fa-lock"></i> {{ $isEn ? 'Saved under your pseudonymous mission record.' : 'Se guarda en tu expediente de misión seudonimizado.' }}</p>
            </article>

            {{-- Live 3D Orbital Microgravity Eye & Flight Log Telemetry Card (Monochrome) --}}
            <div style="background:#09090b;border:1px solid #27272a;border-radius:12px;overflow:hidden;color:#ffffff;display:flex;flex-direction:column;justify-content:space-between;">
                <div style="padding:10px 16px;background:#18181b;border-bottom:1px solid #27272a;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;font-size:12px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="width:8px;height:8px;border-radius:50%;background:#ffffff;display:inline-block;"></span>
                        <strong>{{ $isEn ? '3D Orbital Eye Telemetry & Fluid-Shift Monitor (ISS Flight Log)' : 'Monitor 3D Orbital de Fluido Cefálico y Globo Ocular (Bitácora ISS)' }}</strong>
                    </div>
                    <a href="{{ route('public.digital-twin', ['mode' => 'dual', 'case_id' => $activeCase->case_id, 'lang' => app()->getLocale()]) }}"
                       style="color:#ffffff;font-weight:800;font-size:11.5px;text-decoration:underline;">
                        {{ $isEn ? 'Open Full 3D + OCT →' : 'Abrir 3D + OCT →' }}
                    </a>
                </div>
                <div id="crewThreeContainer" style="width:100%;height:230px;position:relative;cursor:grab;">
                    <div style="position:absolute;bottom:10px;left:12px;font-family:ui-monospace,monospace;font-size:10.5px;background:rgba(9,9,11,0.85);padding:5px 9px;border-radius:6px;border:1px solid #3f3f46;color:#d4d4d8;pointer-events:none;">
                        {{ $isEn ? 'Drag to inspect 3D eye · 0G Cephalic Shift' : 'Arrastra para rotar ojo 3D · Empuje cefálico 0G' }}
                    </div>
                </div>
                <div style="padding:12px 16px;border-top:1px solid #27272a;display:grid;grid-template-columns:repeat(3,1fr);gap:10px;font-family:ui-monospace,monospace;font-size:11.5px;background:#111113;">
                    <div>
                        <span style="display:block;color:#a1a1aa;font-size:10px;">{{ $isEn ? 'Fluid Shift' : 'Fluidos 0G' }}</span>
                        <strong style="color:#ffffff;">~2,000 mL</strong>
                    </div>
                    <div>
                        <span style="display:block;color:#a1a1aa;font-size:10px;">{{ $isEn ? 'Baseline OCT' : 'OCT Basal' }}</span>
                        <strong>{{ $caseEvaluation['eyes']['OD']['baseline_trt'] !== null ? $caseEvaluation['eyes']['OD']['baseline_trt'].' µm' : ($isEn ? 'Unscanned' : 'Sin escaneo') }}</strong>
                    </div>
                    <div>
                        <span style="display:block;color:#a1a1aa;font-size:10px;">{{ $isEn ? 'Max ΔTRT' : 'Máx ΔTRT' }}</span>
                        <strong>{{ $caseEvaluation['eyes']['OD']['max_delta_trt'] !== null ? sprintf('%+d µm', $caseEvaluation['eyes']['OD']['max_delta_trt']) : ($isEn ? 'Unmeasured' : 'Sin medición') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="crew-today-grid">
            <aside class="crew-side-panel" style="grid-column:1 / -1;">
                <div class="crew-side-heading">
                    <div>
                        <p class="crew-eyebrow">{{ $isEn ? 'LAST CHECK SUMMARY' : 'RESUMEN DE ÚLTIMA REVISIÓN' }}</p>
                        <h3>{{ $isEn ? 'No relevant change · ' . $activeCase->case_id : 'Sin cambios relevantes · ' . $activeCase->case_id }}</h3>
                    </div>
                    <span class="crew-state-ok" style="background:#09090b;color:#ffffff;"><i class="fa fa-check"></i></span>
                </div>
                <p>{{ $isEn ? 'Completed yesterday at 08:40. All captures passed the quality check.' : 'Completada ayer a las 08:40. Todas las capturas aprobaron el control de calidad.' }}</p>
                <dl class="crew-last-values">
                    <div><dt>{{ $isEn ? 'Right eye pressure' : 'Presión ojo derecho' }}</dt><dd>16 mmHg</dd></div>
                    <div><dt>{{ $isEn ? 'Left eye pressure' : 'Presión ojo izquierdo' }}</dt><dd>15 mmHg</dd></div>
                    <div><dt>{{ $isEn ? 'Reported symptoms' : 'Síntomas reportados' }}</dt><dd>{{ $isEn ? 'None' : 'Ninguno' }}</dd></div>
                </dl>
                <button type="button" class="crew-text-action" data-open-tab="log">{{ $isEn ? 'See complete mission logbook' : 'Ver bitácora completa de misión' }} <i class="fa fa-angle-right"></i></button>
            </aside>
        </div>
    </div>

    <div class="crew-view" data-crew-view="log" hidden>
        <div class="crew-page-heading" style="border-bottom:1px solid #e4e4e7;padding-bottom:14px;margin-bottom:18px;">
            <div>
                <p class="crew-eyebrow" style="color:#52525b;font-family:ui-monospace,monospace;letter-spacing:0.08em;">{{ $isEn ? 'PERSONAL FLIGHT RECORD · ' . $activeCase->case_id : 'REGISTRO PERSONAL DE VUELO · ' . $activeCase->case_id }}</p>
                <h2 style="color:#09090b;">{{ $isEn ? 'Mission logbook' : 'Bitácora de misión' }}</h2>
            </div>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <a href="{{ route('public.digital-twin', ['mode' => 'dual', 'case_id' => $activeCase->case_id, 'lang' => app()->getLocale()]) }}" class="crew-secondary-action" style="border-color:#d4d4d8;color:#09090b;background:#ffffff;text-decoration:none;">
                    <i class="fa fa-cube"></i> {{ $isEn ? 'Open 3D + OCT' : 'Abrir 3D + OCT' }}
                </a>
                <button type="button" class="crew-primary-action" style="background:#09090b;color:#ffffff;border-color:#09090b;" data-start-check>
                    <i class="fa fa-plus"></i> {{ $isEn ? 'New entry' : 'Nueva entrada' }}
                </button>
            </div>
        </div>

        <style>
            .sv-logbook-list { display:flex; flex-direction:column; gap:12px; max-width:920px; }
            .sv-log-card { display:grid; grid-template-columns:120px 38px minmax(0,1fr); gap:16px; align-items:start; background:#ffffff; border:1px solid #e4e4e7; border-radius:10px; padding:16px 18px; transition:border-color .15s ease; }
            .sv-log-card:hover { border-color:#09090b; }
            .sv-log-time { font-family:ui-monospace,monospace; text-align:right; padding-top:2px; }
            .sv-log-time strong { display:block; color:#09090b; font-size:0.82rem; font-weight:800; }
            .sv-log-time span { display:block; color:#71717a; font-size:0.72rem; margin-top:2px; }
            .sv-log-badge { width:38px; height:38px; border-radius:8px; display:grid; place-items:center; background:#09090b; color:#ffffff; font-size:0.9rem; border:1px solid #09090b; }
            .sv-log-badge.is-muted { background:#f4f4f5; color:#09090b; border-color:#d4d4d8; }
            .sv-log-body h3 { margin:0 0 4px; font-size:0.98rem; font-weight:800; color:#09090b; }
            .sv-log-body p { margin:0 0 10px; font-size:0.84rem; color:#52525b; line-height:1.45; }
            .sv-log-chips { display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
            .sv-log-chip { font-family:ui-monospace,monospace; font-size:0.71rem; font-weight:700; padding:3px 8px; border-radius:5px; background:#f4f4f5; color:#18181b; border:1px solid #e4e4e7; }
            .sv-log-chip.is-dark { background:#09090b; color:#ffffff; border-color:#09090b; }
            @media (max-width:640px) { .sv-log-card { grid-template-columns:1fr; gap:10px; } .sv-log-time { text-align:left; } }
        </style>

        <div class="sv-logbook-list" data-log-list>
            @php
                $logCheckpoints = isset($missionObservations)
                    ? collect($missionObservations)->groupBy(fn($o) => ($o->visit_stage ?? 'DURANTE') . '|' . ($o->mission_day ?? 'X') . '|' . ($o->days_post_end ?? 'X'))
                    : collect();
            @endphp
            @if($logCheckpoints->count() > 0)
                @foreach($logCheckpoints as $groupKey => $rows)
                    @php
                        $first = $rows->first();
                        $stage = $first->visit_stage ?? 'DURANTE';
                        $fdLabel = $first->days_post_end !== null
                            ? ('+' . $first->days_post_end . 'd')
                            : ($first->mission_day !== null ? ('FD' . $first->mission_day) : $stage);
                        $obsDate = $first->observed_at ? \Carbon\Carbon::parse($first->observed_at)->format('Y-m-d') : '—';
                        $trtOdRow = $rows->first(fn($r) => $r->eye === 'OD' && $r->metric_code === 'PERIPAPILLARY_TRT' && $r->value !== null);
                        $trtOsRow = $rows->first(fn($r) => $r->eye === 'OS' && $r->metric_code === 'PERIPAPILLARY_TRT' && $r->value !== null);
                        $iopOdRow = $rows->first(fn($r) => $r->eye === 'OD' && $r->metric_code === 'IOP' && $r->value !== null);
                        $iopOsRow = $rows->first(fn($r) => $r->eye === 'OS' && $r->metric_code === 'IOP' && $r->value !== null);

                        $trtOd = $trtOdRow ? (round((float)$trtOdRow->value) . ' µm') : ($isEn ? 'No OCT' : 'Sin OCT');
                        $trtOs = $trtOsRow ? (round((float)$trtOsRow->value) . ' µm') : ($isEn ? 'No OCT' : 'Sin OCT');
                        $iopTxt = ($iopOdRow || $iopOsRow)
                            ? (($iopOdRow ? round((float)$iopOdRow->value, 1) : '—') . ' / ' . ($iopOsRow ? round((float)$iopOsRow->value, 1) : '—') . ' mmHg')
                            : ($isEn ? 'IOP pending' : 'PIO pendiente');
                        $hasOct = $trtOdRow !== null;
                        $stageTitle = match($stage) {
                            'ANTES' => $isEn ? 'Pre-flight baseline check' : 'Revisión basal pre-vuelo',
                            'SEGUIMIENTO_POS' => $isEn ? 'Post-flight recovery control' : 'Control post-vuelo de recuperación',
                            default => $isEn ? 'Scheduled orbital eye check' : 'Revisión ocular orbital programada',
                        };
                    @endphp
                    <article class="sv-log-card">
                        <div class="sv-log-time">
                            <strong>{{ $fdLabel }}</strong>
                            <span>{{ $obsDate }}</span>
                        </div>
                        <div class="sv-log-badge {{ $hasOct ? '' : 'is-muted' }}">
                            <i class="fa {{ $hasOct ? 'fa-check' : 'fa-clock-o' }}"></i>
                        </div>
                        <div class="sv-log-body">
                            <h3>{{ $stageTitle }}</h3>
                            <p>{{ $isEn ? 'Logged in mission telemetry dossier · Calibrated seated protocol' : 'Registrado en expediente de telemetría orbital · Protocolo sentado calibrado' }}</p>
                            <div class="sv-log-chips">
                                <span class="sv-log-chip">OCT OD: {{ $trtOd }}</span>
                                <span class="sv-log-chip">OCT OS: {{ $trtOs }}</span>
                                <span class="sv-log-chip">{{ $isEn ? 'IOP' : 'PIO' }}: {{ $iopTxt }}</span>
                                <span class="sv-log-chip is-dark">{{ $hasOct ? ($isEn ? 'Verified capture' : 'Captura verificada') : ($isEn ? 'Pending OCT' : 'OCT pendiente') }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            @else
                <article class="sv-log-card">
                    <div class="sv-log-time"><strong>{{ $isEn ? 'Yesterday' : 'Ayer' }}</strong><span>08:40 UTC</span></div>
                    <div class="sv-log-badge"><i class="fa fa-check"></i></div>
                    <div class="sv-log-body">
                        <h3>{{ $isEn ? 'Routine eye check' : 'Revisión visual rutinaria' }}</h3>
                        <p>{{ $isEn ? 'No symptoms · Visual tests completed · IOP 16 / 15 mmHg' : 'Sin síntomas · Pruebas visuales completas · PIO 16 / 15 mmHg' }}</p>
                        <div class="sv-log-chips">
                            <span class="sv-log-chip is-dark">{{ $isEn ? 'Quality verified' : 'Calidad verificada' }}</span>
                        </div>
                    </div>
                </article>
            @endif
        </div>
    </div>

    <div class="crew-view" data-crew-view="trends" hidden>
        <div class="crew-page-heading"><div><p class="crew-eyebrow">{{ $isEn ? 'FROM YOUR BASELINE' : 'DESDE TU LÍNEA BASAL' }}</p><h2>{{ $isEn ? 'Changes over time' : 'Cambios a través del tiempo' }}</h2></div><span class="crew-neutral-status">{{ $isEn ? 'No persistent change' : 'Sin cambio persistente' }}</span></div>
        <div class="crew-trend-layout">
            <div class="crew-trend-chart">
                <div class="crew-chart-heading"><div><strong>{{ $isEn ? 'Intraocular pressure' : 'Presión intraocular' }}</strong><span>{{ $isEn ? 'Same device · seated protocol' : 'Mismo dispositivo · protocolo sentado' }}</span></div><div class="crew-eye-legend"><span><i class="od"></i>OD</span><span><i class="os"></i>OS</span></div></div>
                <svg viewBox="0 0 720 260" role="img" aria-label="{{ $isEn ? 'Intraocular pressure trend by eye' : 'Tendencia de presión intraocular por ojo' }}">
                    <g class="crew-grid-lines"><line x1="58" y1="38" x2="690" y2="38"/><line x1="58" y1="102" x2="690" y2="102"/><line x1="58" y1="166" x2="690" y2="166"/><line x1="58" y1="230" x2="690" y2="230"/></g>
                    <path class="crew-line od" d="M62 176 C150 170 188 154 270 158 S390 146 474 151 S586 132 686 140"/>
                    <path class="crew-line os" d="M62 182 C150 181 188 167 270 171 S390 159 474 165 S586 151 686 155"/>
                    <g class="crew-chart-labels"><text x="58" y="252">{{ $isEn ? 'Baseline' : 'Basal' }}</text><text x="255" y="252">{{ $isEn ? 'Day 10' : 'Día 10' }}</text><text x="462" y="252">{{ $isEn ? 'Day 20' : 'Día 20' }}</text><text x="650" y="252">{{ $isEn ? 'Today' : 'Hoy' }}</text></g>
                </svg>
            </div>
            <aside class="crew-trend-summary"><p class="crew-eyebrow">{{ $isEn ? 'PLAIN-LANGUAGE SUMMARY' : 'RESUMEN CLARO' }}</p><h3>{{ $isEn ? 'Your measurements remain close to baseline.' : 'Tus mediciones permanecen cerca de la línea basal.' }}</h3><p>{{ $isEn ? 'Small day-to-day differences are expected. No persistent pattern has been identified in this demonstration record.' : 'Se esperan pequeñas diferencias entre días. No se ha identificado un patrón persistente en este expediente demostrativo.' }}</p><div class="crew-honesty"><i class="fa fa-shield"></i><span>{{ $isEn ? 'This view supports monitoring and ground review. It does not diagnose SANS or estimate intracranial pressure.' : 'Esta vista apoya el seguimiento y la revisión en Tierra. No diagnostica SANS ni estima la presión intracraneal.' }}</span></div></aside>
        </div>
    </div>

    <div class="crew-view" data-crew-view="profile" hidden>
        <div class="crew-page-heading"><div><p class="crew-eyebrow">{{ $isEn ? 'BASELINE PROFILE' : 'PERFIL BASAL' }}</p><h2>{{ $isEn ? 'Verified before flight' : 'Verificado antes del vuelo' }}</h2></div><span class="crew-neutral-status"><i class="fa fa-lock"></i> {{ $isEn ? 'Read only in flight' : 'Solo lectura en vuelo' }}</span></div>
        <div class="crew-profile-form">
            <p class="crew-profile-note"><i class="fa fa-shield"></i> {{ $isEn ? 'A specialist established this profile before launch. The astronaut cannot change baseline information during the mission.' : 'Un especialista estableció este perfil antes del lanzamiento. El astronauta no puede modificar la información basal durante la misión.' }}</p>
            <dl class="crew-profile-values"><div><dt>{{ $isEn ? 'Participant code' : 'Código de participante' }}</dt><dd>{{ $activeCase->case_id }}</dd></div><div><dt>{{ $isEn ? 'Age at launch' : 'Edad al lanzamiento' }}</dt><dd>42</dd></div><div><dt>{{ $isEn ? 'Sex assigned at birth' : 'Sexo asignado al nacer' }}</dt><dd>{{ $isEn ? 'Female' : 'Femenino' }}</dd></div><div><dt>{{ $isEn ? 'Height' : 'Estatura' }}</dt><dd>168 cm</dd></div><div><dt>{{ $isEn ? 'Pre-flight weight' : 'Peso pre-vuelo' }}</dt><dd>64 kg</dd></div><div><dt>{{ $isEn ? 'Baseline completed' : 'Línea basal completada' }}</dt><dd>{{ $isEn ? 'Yes · verified' : 'Sí · verificada' }}</dd></div></dl>
            <details><summary><span><i class="fa fa-eye"></i>{{ $isEn ? 'Verified eye history' : 'Antecedentes oculares verificados' }}</span><i class="fa fa-angle-down"></i></summary><div class="crew-detail-body"><p>{{ $isEn ? 'Uses corrective lenses. No prior ocular surgery or documented retinal disease in this demonstration profile.' : 'Usa lentes correctivos. Sin cirugía ocular previa ni enfermedad retiniana documentada en este perfil demostrativo.' }}</p></div></details>
            <details><summary><span><i class="fa fa-heartbeat"></i>{{ $isEn ? 'What updates during flight' : 'Qué se actualiza durante el vuelo' }}</span><i class="fa fa-angle-down"></i></summary><div class="crew-detail-body"><p>{{ $isEn ? 'Only measurements, symptoms, medication changes and environmental context are appended through the guided check.' : 'Solo se agregan mediciones, síntomas, cambios de medicamentos y contexto ambiental mediante la revisión guiada.' }}</p></div></details>
        </div>
    </div>

    <div class="crew-check-flow" data-check-flow hidden aria-live="polite">
        <div class="crew-flow-topbar"><button type="button" class="crew-close-flow" data-close-flow><i class="fa fa-times"></i><span>{{ $isEn ? 'Exit check' : 'Salir de la revisión' }}</span></button><div class="crew-progress-track"><span data-progress-bar></span></div><span data-progress-label>1 / 4</span></div>
        <form data-check-form>
            <section class="crew-step is-active" data-step="0">
                <p class="crew-eyebrow">{{ $isEn ? 'SYMPTOMS' : 'SÍNTOMAS' }}</p><h2>{{ $isEn ? 'How do you feel right now?' : '¿Cómo te sientes en este momento?' }}</h2><p>{{ $isEn ? 'Choose every symptom you have noticed since your last check.' : 'Selecciona todos los síntomas que hayas notado desde tu última revisión.' }}</p>
                <div class="crew-choice-list"><label><input type="checkbox" name="symptoms" value="blur"><span><i class="fa fa-eye"></i>{{ $isEn ? 'Blurred near vision' : 'Visión cercana borrosa' }}</span></label><label><input type="checkbox" name="symptoms" value="headache"><span><i class="fa fa-bolt"></i>{{ $isEn ? 'Headache or pressure sensation' : 'Cefalea o sensación de presión' }}</span></label><label><input type="checkbox" name="symptoms" value="double"><span><i class="fa fa-clone"></i>{{ $isEn ? 'Double vision' : 'Visión doble' }}</span></label><label><input type="checkbox" name="symptoms" value="none"><span><i class="fa fa-check-circle"></i>{{ $isEn ? 'No symptoms' : 'Sin síntomas' }}</span></label></div>
                <p class="crew-step-feedback" data-step-feedback="0" hidden></p>
            </section>
            <section class="crew-step" data-step="1" hidden>
                <p class="crew-eyebrow">{{ $isEn ? 'GUIDED VISUAL WIZARD' : 'ASISTENTE VISUAL GUIADO' }}</p><h2>{{ $isEn ? 'Step through the six screen checks' : 'Completa paso a paso las seis pruebas' }}</h2><p>{{ $isEn ? 'One check at a time. Follow the distance shown and cover one eye only when instructed.' : 'Una prueba a la vez. Sigue la distancia indicada y cubre un ojo solo cuando se indique.' }}</p>
                <div class="crew-tests-toolbar" aria-label="{{ $isEn ? 'Test progress' : 'Progreso de pruebas' }}">
                    <div class="crew-wiz-stepper" role="tablist" aria-label="{{ $isEn ? 'Visual check steps' : 'Pasos de prueba visual' }}">
                        <button type="button" class="crew-wiz-pill is-active" data-wiz-jump="0">01</button><i></i>
                        <button type="button" class="crew-wiz-pill" data-wiz-jump="1">02</button><i></i>
                        <button type="button" class="crew-wiz-pill" data-wiz-jump="2">03</button><i></i>
                        <button type="button" class="crew-wiz-pill" data-wiz-jump="3">04</button><i></i>
                        <button type="button" class="crew-wiz-pill" data-wiz-jump="4">05</button><i></i>
                        <button type="button" class="crew-wiz-pill" data-wiz-jump="5">06</button>
                    </div>
                    <output data-tests-progress>{{ $isEn ? '0 of 6 recorded' : '0 de 6 registradas' }}</output>
                </div>
                <div class="crew-guided-tests">
                    <article class="crew-guided-test" data-visual-test data-test-number="1">
                        <header><span class="crew-test-number">01</span><span class="crew-test-icon"><i class="fa fa-binoculars"></i></span><span class="crew-test-title"><strong>{{ $isEn ? 'Distance vision' : 'Visión lejana' }}</strong><small>{{ $isEn ? 'Calibrated distance acuity' : 'Agudeza lejana calibrada' }}</small></span><em data-test-state><i class="fa fa-circle-o"></i>{{ $isEn ? 'Pending' : 'Pendiente' }}</em></header>
                        <div class="crew-test-body">
                            <div class="crew-test-stage crew-optotype" aria-hidden="true"><b>E</b><span>F P</span><small>T O Z</small></div>
                            <div class="crew-test-controls">
                                <p class="crew-test-instruction"><i class="fa fa-arrows-h"></i><span><strong>{{ $isEn ? 'Distance: 3 m (10 ft)' : 'Distancia: 3 m (10 pies)' }}</strong>{{ $isEn ? 'Use the calibrated display, cover one eye and read the smallest clear line.' : 'Usa la pantalla calibrada, cubre un ojo y lee la línea más pequeña que distingas.' }}</span></p>
                                <div class="crew-eye-responses"><label><span>OD<small>{{ $isEn ? 'Right' : 'Derecho' }}</small></span><select name="distance_acuity_od" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option>20/20</option><option>20/25</option><option>20/40</option><option value="unreadable">{{ $isEn ? 'Unable to read' : 'No puede leer' }}</option></select></label><label><span>OS<small>{{ $isEn ? 'Left' : 'Izquierdo' }}</small></span><select name="distance_acuity_os" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option>20/20</option><option>20/25</option><option>20/40</option><option value="unreadable">{{ $isEn ? 'Unable to read' : 'No puede leer' }}</option></select></label></div>
                            </div>
                        </div>
                    </article>
                    <article class="crew-guided-test" data-visual-test data-test-number="2">
                        <header><span class="crew-test-number">02</span><span class="crew-test-icon"><i class="fa fa-book"></i></span><span class="crew-test-title"><strong>{{ $isEn ? 'Near vision' : 'Visión cercana' }}</strong><small>{{ $isEn ? 'Reading acuity at 40 cm' : 'Agudeza de lectura a 40 cm' }}</small></span><em data-test-state><i class="fa fa-circle-o"></i>{{ $isEn ? 'Pending' : 'Pendiente' }}</em></header>
                        <div class="crew-test-body">
                            <div class="crew-test-stage"><div class="crew-near-card" aria-hidden="true"><b>SPACE VISION</b><span>ORBITAL HEALTH</span><small>VISUAL MONITOR</small></div></div>
                            <div class="crew-test-controls">
                                <p class="crew-test-instruction"><i class="fa fa-arrows-h"></i><span><strong>{{ $isEn ? 'Distance: 40 cm (16 in)' : 'Distancia: 40 cm (16 pulgadas)' }}</strong>{{ $isEn ? 'Cover one eye and record the smallest line you can read comfortably.' : 'Cubre un ojo y registra la línea más pequeña que puedes leer con comodidad.' }}</span></p>
                                <div class="crew-eye-responses"><label><span>OD<small>{{ $isEn ? 'Right' : 'Derecho' }}</small></span><select name="near_acuity_od" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option>N5</option><option>N6</option><option>N8</option><option value="unreadable">{{ $isEn ? 'Unable to read' : 'No puede leer' }}</option></select></label><label><span>OS<small>{{ $isEn ? 'Left' : 'Izquierdo' }}</small></span><select name="near_acuity_os" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option>N5</option><option>N6</option><option>N8</option><option value="unreadable">{{ $isEn ? 'Unable to read' : 'No puede leer' }}</option></select></label></div>
                            </div>
                        </div>
                    </article>
                    <article class="crew-guided-test" data-visual-test data-test-number="3">
                        <header><span class="crew-test-number">03</span><span class="crew-test-icon"><i class="fa fa-paint-brush"></i></span><span class="crew-test-title"><strong>{{ $isEn ? 'Color vision' : 'Visión del color' }}</strong><small>{{ $isEn ? 'Color discrimination screening' : 'Tamizaje de discriminación cromática' }}</small></span><em data-test-state><i class="fa fa-circle-o"></i>{{ $isEn ? 'Pending' : 'Pendiente' }}</em></header>
                        <div class="crew-test-body">
                            <div class="crew-test-stage"><div class="crew-color-plate" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><b>12</b></div></div>
                            <div class="crew-test-controls">
                                <p class="crew-test-instruction"><i class="fa fa-tint"></i><span><strong>{{ $isEn ? 'Read the number in the color field' : 'Lee el número dentro del campo de color' }}</strong>{{ $isEn ? 'Test one eye at a time under neutral lighting. This is a demonstration screen, not a diagnostic plate.' : 'Evalúa un ojo a la vez con luz neutra. Esta es una pantalla demostrativa, no una lámina diagnóstica.' }}</span></p>
                                <div class="crew-eye-responses"><label><span>OD<small>{{ $isEn ? 'Right' : 'Derecho' }}</small></span><select name="color_od" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option value="12">12</option><option value="unclear">{{ $isEn ? 'Unclear' : 'No es claro' }}</option><option value="none">{{ $isEn ? 'No number identified' : 'No identifica número' }}</option></select></label><label><span>OS<small>{{ $isEn ? 'Left' : 'Izquierdo' }}</small></span><select name="color_os" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option value="12">12</option><option value="unclear">{{ $isEn ? 'Unclear' : 'No es claro' }}</option><option value="none">{{ $isEn ? 'No number identified' : 'No identifica número' }}</option></select></label></div>
                            </div>
                        </div>
                    </article>
                    <article class="crew-guided-test" data-visual-test data-test-number="4">
                        <header><span class="crew-test-number">04</span><span class="crew-test-icon"><i class="fa fa-cubes"></i></span><span class="crew-test-title"><strong>{{ $isEn ? 'Stereopsis' : 'Estereopsis' }}</strong><small>{{ $isEn ? 'Binocular depth perception' : 'Percepción binocular de profundidad' }}</small></span><em data-test-state><i class="fa fa-circle-o"></i>{{ $isEn ? 'Pending' : 'Pendiente' }}</em></header>
                        <div class="crew-test-body">
                            <div class="crew-test-stage"><div class="crew-stereo-sample" aria-hidden="true"><span class="circle"></span><span class="square"></span><span class="triangle"></span></div></div>
                            <div class="crew-test-controls">
                                <p class="crew-test-instruction"><i class="fa fa-cube"></i><span><strong>{{ $isEn ? 'Use the paired red/cyan viewer' : 'Usa el visor rojo/cian vinculado' }}</strong>{{ $isEn ? 'With both eyes open, select the shape that appears closest. A calibrated viewer is required.' : 'Con ambos ojos abiertos, selecciona la figura que parece más cercana. Se requiere un visor calibrado.' }}</span></p>
                                <div class="crew-eye-responses crew-single-response"><label><span>{{ $isEn ? 'Binocular result' : 'Resultado binocular' }}<small>OU</small></span><select name="stereopsis" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option value="circle">{{ $isEn ? 'Circle' : 'Círculo' }}</option><option value="square">{{ $isEn ? 'Square' : 'Cuadrado' }}</option><option value="triangle">{{ $isEn ? 'Triangle' : 'Triángulo' }}</option><option value="none">{{ $isEn ? 'No depth perceived' : 'No percibe profundidad' }}</option></select></label></div>
                            </div>
                        </div>
                    </article>
                    <article class="crew-guided-test" data-visual-test data-test-number="5">
                        <header><span class="crew-test-number">05</span><span class="crew-test-icon"><i class="fa fa-th"></i></span><span class="crew-test-title"><strong>{{ $isEn ? 'Amsler grid' : 'Rejilla de Amsler' }}</strong><small>{{ $isEn ? 'Central field check' : 'Revisión del campo central' }}</small></span><em data-test-state><i class="fa fa-circle-o"></i>{{ $isEn ? 'Pending' : 'Pendiente' }}</em></header>
                        <div class="crew-test-body">
                            <div class="crew-test-stage"><div class="crew-amsler" aria-hidden="true"><span></span></div></div>
                            <div class="crew-test-controls">
                                <p class="crew-test-instruction"><i class="fa fa-dot-circle-o"></i><span><strong>{{ $isEn ? 'Keep looking at the center point' : 'Mantén la mirada en el punto central' }}</strong>{{ $isEn ? 'Indicate whether the lines look straight, distorted or incomplete.' : 'Indica si las líneas se ven rectas, distorsionadas o incompletas.' }}</span></p>
                                <div class="crew-eye-responses"><label><span>OD<small>{{ $isEn ? 'Right' : 'Derecho' }}</small></span><select name="amsler_od" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option value="straight">{{ $isEn ? 'All lines straight' : 'Todas rectas' }}</option><option value="distorted">{{ $isEn ? 'Distorted lines' : 'Líneas distorsionadas' }}</option><option value="missing">{{ $isEn ? 'Missing area' : 'Área faltante' }}</option></select></label><label><span>OS<small>{{ $isEn ? 'Left' : 'Izquierdo' }}</small></span><select name="amsler_os" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option value="straight">{{ $isEn ? 'All lines straight' : 'Todas rectas' }}</option><option value="distorted">{{ $isEn ? 'Distorted lines' : 'Líneas distorsionadas' }}</option><option value="missing">{{ $isEn ? 'Missing area' : 'Área faltante' }}</option></select></label></div>
                            </div>
                        </div>
                    </article>
                    <article class="crew-guided-test" data-visual-test data-test-number="6">
                        <header><span class="crew-test-number">06</span><span class="crew-test-icon"><i class="fa fa-adjust"></i></span><span class="crew-test-title"><strong>{{ $isEn ? 'Contrast sensitivity' : 'Sensibilidad al contraste' }}</strong><small>{{ $isEn ? 'Low-contrast recognition' : 'Reconocimiento de bajo contraste' }}</small></span><em data-test-state><i class="fa fa-circle-o"></i>{{ $isEn ? 'Pending' : 'Pendiente' }}</em></header>
                        <div class="crew-test-body">
                            <div class="crew-test-stage crew-contrast-sample" aria-hidden="true"><span>C</span><span>C</span><span>C</span></div>
                            <div class="crew-test-controls">
                                <p class="crew-test-instruction"><i class="fa fa-eye"></i><span><strong>{{ $isEn ? 'Identify the faintest visible group' : 'Identifica el grupo más tenue visible' }}</strong>{{ $isEn ? 'Record the last group you can recognize with each eye.' : 'Registra el último grupo que reconoces con cada ojo.' }}</span></p>
                                <div class="crew-eye-responses"><label><span>OD<small>{{ $isEn ? 'Right' : 'Derecho' }}</small></span><select name="contrast_od" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option value="all">{{ $isEn ? 'All three groups' : 'Los tres grupos' }}</option><option value="medium">{{ $isEn ? 'Up to medium' : 'Hasta el intermedio' }}</option><option value="high">{{ $isEn ? 'High contrast only' : 'Solo alto contraste' }}</option></select></label><label><span>OS<small>{{ $isEn ? 'Left' : 'Izquierdo' }}</small></span><select name="contrast_os" data-test-answer><option value="">{{ $isEn ? 'Select result' : 'Seleccionar resultado' }}</option><option value="all">{{ $isEn ? 'All three groups' : 'Los tres grupos' }}</option><option value="medium">{{ $isEn ? 'Up to medium' : 'Hasta el intermedio' }}</option><option value="high">{{ $isEn ? 'High contrast only' : 'Solo alto contraste' }}</option></select></label></div>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="crew-wiz-nav" data-wiz-nav>
                    <button type="button" class="crew-secondary-action" data-wiz-prev disabled><i class="fa fa-chevron-left"></i> {{ $isEn ? 'Previous check' : 'Prueba anterior' }}</button>
                    <span class="crew-wiz-counter" data-wiz-counter>{{ $isEn ? 'Check 1 of 6' : 'Prueba 1 de 6' }}</span>
                    <button type="button" class="crew-secondary-action" data-wiz-next>{{ $isEn ? 'Next check' : 'Siguiente prueba' }} <i class="fa fa-chevron-right"></i></button>
                </div>
                <style>
                    .crew-wiz-stepper { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
                    .crew-wiz-pill { border:1px solid #d4d4d8; background:#ffffff; color:#09090b; font-family:ui-monospace,monospace; font-weight:700; font-size:12px; padding:5px 10px; border-radius:6px; cursor:pointer; transition:all .15s ease; }
                    .crew-wiz-pill:hover { border-color:#09090b; color:#09090b; background:#f4f4f5; }
                    .crew-wiz-pill.is-active { background:#09090b; border-color:#09090b; color:#ffffff; box-shadow:0 0 0 3px rgba(9,9,11,0.12); }
                    .crew-wiz-pill.is-done { background:#f4f4f5; border-color:#18181b; color:#09090b; }
                    .crew-wiz-pill.is-active.is-done { background:#09090b; border-color:#09090b; color:#ffffff; }
                    .crew-wiz-nav { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:14px; padding:12px 16px; background:#fafafa; border:1px solid #e4e4e7; border-radius:10px; }
                    .crew-wiz-counter { font-family:ui-monospace,monospace; font-size:12.5px; font-weight:700; color:#18181b; }
                </style>
                <div class="crew-wiz-nav" data-wiz-nav>
                    <button type="button" class="crew-secondary-action" data-wiz-prev disabled><i class="fa fa-chevron-left"></i> {{ $isEn ? 'Previous check' : 'Prueba anterior' }}</button>
                    <span class="crew-wiz-counter" data-wiz-counter>{{ $isEn ? 'Check 1 of 6' : 'Prueba 1 de 6' }}</span>
                    <button type="button" class="crew-secondary-action" data-wiz-next>{{ $isEn ? 'Next check' : 'Siguiente prueba' }} <i class="fa fa-chevron-right"></i></button>
                </div>
                <style>
                    .crew-wiz-stepper { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
                    .crew-wiz-pill { border:1px solid #d4d4d8; background:#ffffff; color:#09090b; font-family:ui-monospace,monospace; font-weight:700; font-size:12px; padding:5px 10px; border-radius:6px; cursor:pointer; transition:all .15s ease; }
                    .crew-wiz-pill:hover { border-color:#09090b; color:#09090b; background:#f4f4f5; }
                    .crew-wiz-pill.is-active { background:#09090b; border-color:#09090b; color:#ffffff; box-shadow:0 0 0 3px rgba(9,9,11,0.12); }
                    .crew-wiz-pill.is-done { background:#f4f4f5; border-color:#18181b; color:#09090b; }
                    .crew-wiz-pill.is-active.is-done { background:#09090b; border-color:#09090b; color:#ffffff; }
                    .crew-wiz-nav { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:14px; padding:12px 16px; background:#fafafa; border:1px solid #e4e4e7; border-radius:10px; }
                    .crew-wiz-counter { font-family:ui-monospace,monospace; font-size:12.5px; font-weight:700; color:#18181b; }
                </style>
                <p class="crew-step-feedback" data-step-feedback="1" hidden></p>
            </section>
            <section class="crew-step" data-step="2" hidden>
                <p class="crew-eyebrow">{{ $isEn ? 'PHYSICAL MEASUREMENTS' : 'MEDICIONES FÍSICAS' }}</p><h2>{{ $isEn ? 'Receive readings from digital devices' : 'Recibe lecturas de dispositivos digitales' }}</h2><p>{{ $isEn ? 'In operation, paired devices would send these measurements directly to the application.' : 'En operación, los dispositivos vinculados enviarían estas mediciones directamente a la aplicación.' }}</p>
                <div class="crew-demo-notice" role="note"><i class="fa fa-info-circle"></i><span><strong>{{ $isEn ? 'Demonstration mode' : 'Modo demostración' }}</strong>{{ $isEn ? 'The buttons simulate the data transfer. No physical hardware is connected to this prototype.' : 'Los botones simulan la transferencia de datos. Este prototipo no está conectado a hardware físico.' }}</span></div>
                <figure class="crew-equipment-guide">
                    <div class="crew-equipment-visual"><img src="{{ asset('images/sans/mission-medical-devices.png') }}" alt="{{ $isEn ? 'Rebound tonometer and wearable mission health sensor' : 'Tonómetro de rebote y sensor portátil de salud de misión' }}"></div>
                    <figcaption>
                        <span class="crew-equipment-label"><i class="fa fa-check-circle"></i>{{ $isEn ? 'DIGITAL EQUIPMENT REFERENCE' : 'REFERENCIA DE EQUIPO DIGITAL' }}</span>
                        <strong>{{ $isEn ? 'Identify the devices before capturing' : 'Identifica los dispositivos antes de capturar' }}</strong>
                        <p>{{ $isEn ? 'Use the rebound tonometer for eye pressure and the wearable sensor for vital signs.' : 'Usa el tonómetro de rebote para la presión ocular y el sensor portátil para los signos vitales.' }}</p>
                        <div><span><i class="fa fa-eye"></i>{{ $isEn ? 'Eye pressure' : 'Presión ocular' }}</span><span><i class="fa fa-heartbeat"></i>{{ $isEn ? 'Vital signs' : 'Signos vitales' }}</span></div>
                    </figcaption>
                </figure>
                <div class="crew-sync-console" data-sync-console role="status" aria-live="polite">
                    <span class="crew-sync-orbit" aria-hidden="true"><i class="fa fa-rss"></i></span>
                    <span><strong data-sync-title>{{ $isEn ? 'Secure device link ready' : 'Enlace seguro de dispositivos listo' }}</strong><small data-sync-detail>{{ $isEn ? 'Start each simulated reading to view the transfer sequence.' : 'Inicia cada lectura simulada para ver la secuencia de transferencia.' }}</small></span>
                    <output data-sync-count>0 / 2</output>
                </div>
                <div class="crew-device-list">
                    <article data-device="tonometer"><div><i class="fa fa-eye"></i><span><strong>{{ $isEn ? 'Digital rebound tonometer' : 'Tonómetro digital de rebote' }}</strong><small>{{ $isEn ? 'Measures OD, then OS under the same baseline protocol.' : 'Mide OD y después OS con el mismo protocolo basal.' }}</small></span></div><dl><div><dt>OD</dt><dd><output data-reading="iop_od">—</output> mmHg<input type="hidden" name="iop_od"></dd></div><div><dt>OS</dt><dd><output data-reading="iop_os">—</output> mmHg<input type="hidden" name="iop_os"></dd></div></dl><button type="button" class="crew-secondary-action" data-capture-device="tonometer"><i class="fa fa-play"></i>{{ $isEn ? 'Simulate reading' : 'Simular lectura' }}</button></article>
                    <article data-device="vitals"><div><i class="fa fa-heartbeat"></i><span><strong>{{ $isEn ? 'Digital mission health sensor' : 'Sensor digital de salud de misión' }}</strong><small>{{ $isEn ? 'Measures blood pressure and heart rate with a timestamp.' : 'Mide presión arterial y frecuencia cardiaca con hora.' }}</small></span></div><dl><div><dt>{{ $isEn ? 'Blood pressure' : 'Presión arterial' }}</dt><dd><output data-reading="bp">— / —</output> mmHg<input type="hidden" name="bp_sys"><input type="hidden" name="bp_dia"></dd></div><div><dt>{{ $isEn ? 'Heart rate' : 'Frecuencia cardiaca' }}</dt><dd><output data-reading="hr">—</output> bpm<input type="hidden" name="hr"></dd></div></dl><button type="button" class="crew-secondary-action" data-capture-device="vitals"><i class="fa fa-play"></i>{{ $isEn ? 'Simulate reading' : 'Simular lectura' }}</button></article>
                </div>
                <p class="crew-step-feedback" data-step-feedback="2" hidden></p>
            </section>
            <section class="crew-step" data-step="3" hidden>
                <p class="crew-eyebrow">{{ $isEn ? 'MEASUREMENT CONTEXT' : 'CONTEXTO DE MEDICIÓN' }}</p><h2>{{ $isEn ? 'A little context makes the data useful' : 'Un poco de contexto hace útiles los datos' }}</h2><p>{{ $isEn ? 'These factors are recorded alongside the eye results; they do not diagnose a cause.' : 'Estos factores se registran junto con los resultados oculares; no diagnostican una causa.' }}</p>
                <div class="crew-measure-grid"><label>{{ $isEn ? 'Sleep last night' : 'Sueño anoche' }}<select name="sleep"><option value="under5">{{ $isEn ? 'Less than 5 hours' : 'Menos de 5 horas' }}</option><option value="5to7">5–7 h</option><option value="7to9" selected>7–9 h</option><option value="over9">{{ $isEn ? 'More than 9 hours' : 'Más de 9 horas' }}</option></select></label><label>{{ $isEn ? 'Hydration today' : 'Hidratación hoy' }}<select name="hydration"><option value="usual">{{ $isEn ? 'Usual' : 'Habitual' }}</option><option value="low">{{ $isEn ? 'Less than usual' : 'Menor a la habitual' }}</option><option value="high">{{ $isEn ? 'More than usual' : 'Mayor a la habitual' }}</option></select></label><label>{{ $isEn ? 'Exercise in last 2 hours' : 'Ejercicio en últimas 2 horas' }}<select name="exercise"><option value="no">{{ $isEn ? 'No' : 'No' }}</option><option value="yes">{{ $isEn ? 'Yes' : 'Sí' }}</option></select></label><label>{{ $isEn ? 'Measurement posture' : 'Postura de medición' }}<select name="posture"><option value="seated">{{ $isEn ? 'Seated / secured' : 'Sentado / asegurado' }}</option><option value="supine">{{ $isEn ? 'Supine' : 'Decúbito supino' }}</option></select></label><label class="crew-wide-label">{{ $isEn ? 'Relevant change since last check' : 'Cambio relevante desde la última revisión' }}<select name="context_change"><option value="none">{{ $isEn ? 'No relevant change' : 'Sin cambios relevantes' }}</option><option value="medication">{{ $isEn ? 'Medication changed' : 'Cambió un medicamento' }}</option><option value="illness">{{ $isEn ? 'New illness or discomfort' : 'Nueva enfermedad o malestar' }}</option><option value="other">{{ $isEn ? 'Other — request ground follow-up' : 'Otro — solicitar seguimiento en Tierra' }}</option></select></label></div>
            </section>
            <section class="crew-step crew-complete-step" data-step="4" hidden>
                <span class="crew-complete-mark"><i class="fa fa-check"></i></span><p class="crew-eyebrow">{{ $isEn ? 'CHECK COMPLETE' : 'REVISIÓN COMPLETA' }}</p><h2>{{ $isEn ? 'Your entry is ready for ground review' : 'Tu registro está listo para revisión en Tierra' }}</h2><p>{{ $isEn ? 'The measurements were saved with their context. This result supports monitoring and is not an automated diagnosis.' : 'Las mediciones se guardaron con su contexto. Este resultado apoya el seguimiento y no es un diagnóstico automatizado.' }}</p><button type="button" class="crew-primary-action" data-finish-flow>{{ $isEn ? 'Return to today' : 'Volver a Hoy' }}</button>
            </section>
            <div class="crew-flow-actions" data-flow-actions><button type="button" class="crew-secondary-action" data-prev-step disabled><i class="fa fa-arrow-left"></i> {{ $isEn ? 'Back' : 'Atrás' }}</button><button type="button" class="crew-primary-action" data-next-step>{{ $isEn ? 'Continue' : 'Continuar' }} <i class="fa fa-arrow-right"></i></button></div>
        </form>
    </div>
</section>

<script>
(() => {
    const app = document.querySelector('[data-crew-app]');
    if (!app || app.dataset.bound === 'true') return;
    app.dataset.bound = 'true';
    const isEn = app.dataset.locale === 'en';
    const tabs = [...app.querySelectorAll('[data-crew-tab]')];
    const views = [...app.querySelectorAll('[data-crew-view]')];
    const flow = app.querySelector('[data-check-flow]');
    const steps = [...app.querySelectorAll('[data-step]')];
    let currentStep = 0;

    function openTab(name) {
        flow.hidden = true;
        tabs.forEach(tab => tab.classList.toggle('is-active', tab.dataset.crewTab === name));
        views.forEach(view => { const active = view.dataset.crewView === name; view.hidden = !active; view.classList.toggle('is-active', active); });
    }
    function renderStep() {
        steps.forEach((step, index) => { step.hidden = index !== currentStep; step.classList.toggle('is-active', index === currentStep); });
        const actions = app.querySelector('[data-flow-actions]');
        actions.hidden = currentStep === 4;
        app.querySelector('[data-prev-step]').disabled = currentStep === 0;
        app.querySelector('[data-next-step]').innerHTML = currentStep === 3 ? `${isEn ? 'Save check' : 'Guardar revisión'} <i class="fa fa-check"></i>` : `${isEn ? 'Continue' : 'Continuar'} <i class="fa fa-arrow-right"></i>`;
        app.querySelector('[data-progress-bar]').style.width = `${Math.min((currentStep + 1) / 4 * 100, 100)}%`;
        app.querySelector('[data-progress-label]').textContent = currentStep < 4 ? `${currentStep + 1} / 4` : (isEn ? 'Complete' : 'Completa');
    }
    function startFlow() { views.forEach(view => view.hidden = true); flow.hidden = false; currentStep = 0; renderStep(); flow.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    function saveEntry() {
        const form = app.querySelector('[data-check-form]');
        const data = Object.fromEntries(new FormData(form).entries());
        data.completed_at = new Date().toISOString();
        try { localStorage.setItem(`space-vision-check-${app.dataset.case}`, JSON.stringify(data)); } catch (_) {}
        currentStep = 4;
        renderStep();
    }

    function showFeedback(step, message) {
        const feedback = app.querySelector(`[data-step-feedback="${step}"]`);
        if (!feedback) return;
        feedback.textContent = message;
        feedback.hidden = false;
    }

    function clearFeedback(step) {
        const feedback = app.querySelector(`[data-step-feedback="${step}"]`);
        if (feedback) feedback.hidden = true;
    }

    const visualTests = [...app.querySelectorAll('[data-visual-test]')];
    const wizPills = [...app.querySelectorAll('[data-wiz-jump]')];
    let currentVisualTest = 0;

    function renderVisualWizard() {
        visualTests.forEach((test, idx) => {
            test.hidden = idx !== currentVisualTest;
        });
        wizPills.forEach((pill, idx) => {
            pill.classList.toggle('is-active', idx === currentVisualTest);
            pill.classList.toggle('is-done', visualTests[idx]?.classList.contains('is-complete'));
        });
        const counter = app.querySelector('[data-wiz-counter]');
        if (counter) counter.textContent = isEn ? `Check ${currentVisualTest + 1} of ${visualTests.length}` : `Prueba ${currentVisualTest + 1} de ${visualTests.length}`;
        const prevBtn = app.querySelector('[data-wiz-prev]');
        const nextBtn = app.querySelector('[data-wiz-next]');
        if (prevBtn) prevBtn.disabled = currentVisualTest === 0;
        if (nextBtn) nextBtn.disabled = currentVisualTest === visualTests.length - 1;
    }

    function updateVisualProgress() {
        const completed = app.querySelectorAll('[data-visual-test].is-complete').length;
        const total = visualTests.length;
        const progress = app.querySelector('[data-tests-progress]');
        if (progress) progress.textContent = isEn ? `${completed} of ${total} recorded` : `${completed} de ${total} registradas`;
        renderVisualWizard();
    }

    function canAdvance() {
        clearFeedback(currentStep);
        if (currentStep === 0 && !app.querySelector('input[name="symptoms"]:checked')) {
            showFeedback(0, isEn ? 'Select a symptom or “No symptoms” to continue.' : 'Selecciona un síntoma o “Sin síntomas” para continuar.');
            return false;
        }
        if (currentStep === 1) {
            const firstIncomplete = visualTests.findIndex(test => [...test.querySelectorAll('[data-test-answer]')].some(a => !a.value));
            if (firstIncomplete !== -1) {
                currentVisualTest = firstIncomplete;
                renderVisualWizard();
                showFeedback(1, isEn ? `Complete check ${firstIncomplete + 1} of ${visualTests.length} before continuing.` : `Completa la prueba ${firstIncomplete + 1} de ${visualTests.length} antes de continuar.`);
                return false;
            }
        }
        if (currentStep === 2 && app.querySelectorAll('[data-device].is-complete').length < 2) {
            showFeedback(2, isEn ? 'Simulate both device readings before continuing.' : 'Simula las lecturas de ambos dispositivos antes de continuar.');
            return false;
        }
        return true;
    }

    wizPills.forEach(pill => pill.addEventListener('click', () => {
        currentVisualTest = Number(pill.dataset.wizJump || 0);
        renderVisualWizard();
    }));
    app.querySelector('[data-wiz-prev]')?.addEventListener('click', () => {
        if (currentVisualTest > 0) { currentVisualTest--; renderVisualWizard(); }
    });
    app.querySelector('[data-wiz-next]')?.addEventListener('click', () => {
        if (currentVisualTest < visualTests.length - 1) { currentVisualTest++; renderVisualWizard(); }
    });
    renderVisualWizard();

    tabs.forEach(tab => tab.addEventListener('click', () => openTab(tab.dataset.crewTab)));
    app.querySelectorAll('[data-open-tab]').forEach(button => button.addEventListener('click', () => openTab(button.dataset.openTab)));
    app.querySelectorAll('[data-start-check]').forEach(button => button.addEventListener('click', startFlow));
    app.querySelector('[data-close-flow]').addEventListener('click', () => openTab('today'));
    app.querySelector('[data-finish-flow]').addEventListener('click', () => openTab('today'));
    app.querySelector('[data-prev-step]').addEventListener('click', () => { if (currentStep > 0) { currentStep--; renderStep(); } });
    app.querySelector('[data-next-step]').addEventListener('click', () => {
        if (!canAdvance()) return;
        if (currentStep === 3) saveEntry(); else { currentStep++; renderStep(); }
    });

    app.querySelectorAll('input[name="symptoms"]').forEach(input => input.addEventListener('change', () => {
        const symptomInputs = [...app.querySelectorAll('input[name="symptoms"]')];
        if (input.value === 'none' && input.checked) symptomInputs.filter(item => item !== input).forEach(item => item.checked = false);
        if (input.value !== 'none' && input.checked) symptomInputs.find(item => item.value === 'none').checked = false;
        clearFeedback(0);
    }));

    app.querySelectorAll('[data-test-answer]').forEach(select => select.addEventListener('change', () => {
        const test = select.closest('[data-visual-test]');
        const complete = [...test.querySelectorAll('[data-test-answer]')].every(answer => answer.value);
        test.classList.toggle('is-complete', complete);
        test.querySelector('[data-test-state]').innerHTML = complete
            ? `<i class="fa fa-check"></i>${isEn ? 'Recorded' : 'Registrada'}`
            : `<i class="fa fa-circle-o"></i>${isEn ? 'Pending' : 'Pendiente'}`;
        updateVisualProgress();
        clearFeedback(1);
    }));

    app.querySelectorAll('[data-capture-device]').forEach(button => button.addEventListener('click', () => {
        if (button.disabled) return;
        const type = button.dataset.captureDevice;
        const device = button.closest('[data-device]');
        button.disabled = true;
        device.setAttribute('aria-busy', 'true');
        device.classList.add('is-syncing');
        button.innerHTML = `<i class="fa fa-refresh fa-spin"></i>${isEn ? 'Synchronizing…' : 'Sincronizando…'}`;

        const consolePanel = app.querySelector('[data-sync-console]');
        const consoleTitle = app.querySelector('[data-sync-title]');
        const consoleDetail = app.querySelector('[data-sync-detail]');
        consolePanel.classList.add('is-active');
        consoleTitle.textContent = isEn ? 'Establishing encrypted link' : 'Estableciendo enlace cifrado';
        consoleDetail.textContent = isEn ? 'Verifying device identity and simulated telemetry channel…' : 'Verificando identidad del dispositivo y canal de telemetría simulado…';

        window.setTimeout(() => {
            device.classList.remove('is-syncing');
            device.classList.add('is-processing');
            button.innerHTML = `<i class="fa fa-cog fa-spin"></i>${isEn ? 'Processing…' : 'Procesando…'}`;
            consoleTitle.textContent = isEn ? 'Processing telemetry' : 'Procesando telemetría';
            consoleDetail.textContent = isEn ? 'Validating units, timestamp and signal integrity…' : 'Validando unidades, marca de tiempo e integridad de señal…';
        }, 700);

        window.setTimeout(() => {
            if (type === 'tonometer') {
                device.querySelector('[data-reading="iop_od"]').textContent = '16';
                device.querySelector('[data-reading="iop_os"]').textContent = '15';
                device.querySelector('input[name="iop_od"]').value = '16';
                device.querySelector('input[name="iop_os"]').value = '15';
            } else {
                device.querySelector('[data-reading="bp"]').textContent = '118 / 76';
                device.querySelector('[data-reading="hr"]').textContent = '68';
                device.querySelector('input[name="bp_sys"]').value = '118';
                device.querySelector('input[name="bp_dia"]').value = '76';
                device.querySelector('input[name="hr"]').value = '68';
            }
            device.classList.remove('is-processing');
            device.classList.add('is-complete');
            device.setAttribute('aria-busy', 'false');
            button.innerHTML = `<i class="fa fa-check"></i>${isEn ? 'Simulation complete' : 'Simulación completada'}`;

            const completed = app.querySelectorAll('[data-device].is-complete').length;
            app.querySelector('[data-sync-count]').textContent = `${completed} / 2`;
            consolePanel.classList.remove('is-active');
            consoleTitle.textContent = completed === 2
                ? (isEn ? 'All simulated readings synchronized' : 'Todas las lecturas simuladas están sincronizadas')
                : (isEn ? 'Simulated reading synchronized' : 'Lectura simulada sincronizada');
            consoleDetail.textContent = completed === 2
                ? (isEn ? 'The demonstration dataset is ready to continue.' : 'El conjunto de datos demostrativo está listo para continuar.')
                : (isEn ? 'Start the remaining device when ready.' : 'Inicia el dispositivo restante cuando estés listo.');
            clearFeedback(2);
        }, 1400);
    }));
})();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script>
(() => {
    const mount = document.getElementById('crewThreeContainer');
    if (!mount || typeof THREE === 'undefined') return;

    const w = mount.clientWidth || 420;
    const h = mount.clientHeight || 240;
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x05070d);

    const camera = new THREE.PerspectiveCamera(38, w / h, 0.1, 200);
    camera.position.set(16, 5, 16);

    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    mount.appendChild(renderer.domElement);

    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.autoRotate = true;
    controls.autoRotateSpeed = 1.4;
    controls.enableZoom = false;

    scene.add(new THREE.AmbientLight(0xffffff, 0.9));
    const keyLight = new THREE.DirectionalLight(0x38bdf8, 1.2);
    keyLight.position.set(15, 20, 15);
    scene.add(keyLight);

    const eyeGroup = new THREE.Group();
    const sclera = new THREE.Mesh(
        new THREE.SphereGeometry(5.2, 36, 36),
        new THREE.MeshPhysicalMaterial({ color: 0xf1f5f9, roughness: 0.3, transmission: 0.15, transparent: true, opacity: 0.92 })
    );
    eyeGroup.add(sclera);

    const iris = new THREE.Mesh(
        new THREE.RingGeometry(0.9, 2.3, 32),
        new THREE.MeshStandardMaterial({ color: 0x0042A6, side: THREE.DoubleSide })
    );
    iris.rotation.y = Math.PI / 2;
    iris.position.x = 4.6;
    eyeGroup.add(iris);

    const nerve = new THREE.Mesh(
        new THREE.CylinderGeometry(1.1, 1.3, 5.5, 24),
        new THREE.MeshStandardMaterial({ color: 0x334155 })
    );
    nerve.rotation.z = Math.PI / 2;
    nerve.position.x = -7.2;
    eyeGroup.add(nerve);

    const ring = new THREE.Mesh(
        new THREE.TorusGeometry(6.8, 0.06, 12, 64),
        new THREE.MeshBasicMaterial({ color: 0x2E96F5 })
    );
    ring.rotation.x = Math.PI / 2.4;
    eyeGroup.add(ring);

    const arrow1 = new THREE.ArrowHelper(new THREE.Vector3(1, 0, 0), new THREE.Vector3(-10.5, 1.6, 0), 2.8, 0xEAFE07, 0.6, 0.3);
    const arrow2 = new THREE.ArrowHelper(new THREE.Vector3(1, 0, 0), new THREE.Vector3(-10.5, -1.6, 0), 2.8, 0xEAFE07, 0.6, 0.3);
    eyeGroup.add(arrow1, arrow2);
    scene.add(eyeGroup);

    const resize = () => {
        const nw = mount.clientWidth;
        const nh = mount.clientHeight;
        if (nw > 0 && nh > 0) {
            camera.aspect = nw / nh;
            camera.updateProjectionMatrix();
            renderer.setSize(nw, nh);
        }
    };
    window.addEventListener('resize', resize);

    function loop() {
        requestAnimationFrame(loop);
        controls.update();
        ring.rotation.z += 0.008;
        renderer.render(scene, camera);
    }
    loop();
})();
</script>
