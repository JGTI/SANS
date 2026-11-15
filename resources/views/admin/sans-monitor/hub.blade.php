@php
    $hubCrew = \App\Models\AstronautProfile::crewMetaForCase($activeCase->case_id);
    $statusCode = $caseEvaluation['case_operational_code'] ?? 'FALTAN_DATOS';
    $status = match ($statusCode) {
        'LISTO_PARA_REVISION' => ['label' => $isEn ? 'Ready for review' : 'Listo para revisión', 'class' => 'sv-hub-status-ready'],
        'PRIORIZAR_REVISION' => ['label' => $isEn ? 'Prioritize review' : 'Priorizar revisión', 'class' => 'sv-hub-status-priority'],
        default => ['label' => $isEn ? 'Missing data' : 'Faltan datos', 'class' => 'sv-hub-status-missing'],
    };
    $archetypeLabels = [
        'ESTABLE' => $isEn ? 'Stable pattern' : 'Patrón estable',
        'EMPEORA' => $isEn ? 'Needs attention' : 'Requiere atención',
        'SIN_ESCANEAR' => $isEn ? 'Awaiting scans' : 'Esperando escaneos',
        'SIN_CONTROL_RECIENTE' => $isEn ? 'Follow-up due' : 'Seguimiento pendiente',
    ];
@endphp

@push('styles')
<style>
    .sv-hub { --hub-ink:#111827; --hub-muted:#64748b; --hub-line:#dbe4ee; --hub-blue:#155eef; --hub-lime:#dfff3f; max-width:1280px; margin:0 auto; }
    .sv-hub-hero { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(280px,.65fr); gap:22px; padding:34px; border-radius:18px; background:linear-gradient(120deg,#0b1f3a 0%,#123b68 58%,#e7f2ff 58%,#e7f2ff 100%); color:#fff; overflow:hidden; }
    .sv-hub-kicker,.sv-hub-eyebrow { margin:0 0 9px; font:700 .7rem/1.2 ui-monospace,monospace; letter-spacing:.12em; text-transform:uppercase; opacity:.72; }
    .sv-hub-hero h1 { max-width:680px; margin:0; font-size:clamp(2rem,4vw,4.2rem); line-height:.98; letter-spacing:0; }
    .sv-hub-hero p { max-width:590px; margin:16px 0 0; color:#d7e8fb; font-size:1rem; line-height:1.55; }
    .sv-hub-hero-panel { align-self:stretch; padding:20px; border:1px solid rgba(21,94,239,.18); border-radius:14px; background:#fff; color:var(--hub-ink); box-shadow:0 12px 30px rgba(8,38,77,.12); }
    .sv-hub-hero-panel img { width:54px; height:54px; object-fit:cover; border-radius:10px; border:2px solid #dbeafe; }
    .sv-hub-hero-panel strong { display:block; font-size:1.05rem; }
    .sv-hub-hero-panel small { color:var(--hub-muted); }
    .sv-hub-status { display:inline-flex; margin-top:17px; padding:6px 9px; border-radius:5px; font:800 .7rem/1 ui-monospace,monospace; text-transform:uppercase; }
    .sv-hub-status-ready { color:#166534; background:#dcfce7; }.sv-hub-status-priority { color:#9a3412; background:#ffedd5; }.sv-hub-status-missing { color:#475569; background:#e2e8f0; }
    .sv-hub-section { margin-top:25px; }.sv-hub-section h2 { margin:0; font-size:1.15rem; color:var(--hub-ink); }
    .sv-hub-section-heading { display:flex; justify-content:space-between; align-items:end; gap:12px; margin-bottom:11px; }.sv-hub-section-heading p { margin:3px 0 0; color:var(--hub-muted); font-size:.84rem; }
    .sv-hub-actions { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .sv-hub-action { min-height:132px; display:flex; flex-direction:column; justify-content:space-between; padding:17px; border:1px solid var(--hub-line); border-radius:12px; background:#fff; color:var(--hub-ink); text-decoration:none; transition:transform .16s ease,border-color .16s ease,box-shadow .16s ease; }
    .sv-hub-action:hover { transform:translateY(-2px); border-color:var(--hub-blue); box-shadow:0 10px 24px rgba(21,94,239,.11); }.sv-hub-action-primary { background:var(--hub-blue); color:#fff; border-color:var(--hub-blue); }.sv-hub-action i { font-size:1.2rem; }.sv-hub-action strong { display:block; margin-top:13px; font-size:1rem; }.sv-hub-action span { display:block; margin-top:4px; color:var(--hub-muted); font-size:.78rem; line-height:1.4; }.sv-hub-action-primary span { color:#dbeafe; }
    .sv-hub-crew-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(225px,1fr)); gap:10px; }.sv-hub-crew-card { display:flex; align-items:center; gap:11px; padding:12px; border:1px solid var(--hub-line); border-radius:10px; background:#fff; color:var(--hub-ink); text-decoration:none; }.sv-hub-crew-card:hover,.sv-hub-crew-card.is-active { border-color:var(--hub-blue); box-shadow:0 4px 12px rgba(21,94,239,.08); }.sv-hub-crew-card img { width:42px; height:42px; border-radius:8px; object-fit:cover; }.sv-hub-crew-card strong { display:block; font-size:.84rem; }.sv-hub-crew-card small { color:var(--hub-muted); font: .68rem ui-monospace,monospace; }
    .sv-hub-register { display:grid; grid-template-columns:1.1fr 1fr 1fr auto; gap:9px; align-items:end; padding:16px; border:1px solid var(--hub-line); border-radius:12px; background:#f8fafc; }.sv-hub-register label { display:block; color:var(--hub-muted); font-size:.72rem; font-weight:700; }.sv-hub-register input,.sv-hub-register select { width:100%; margin-top:5px; }.sv-hub-register button { height:38px; white-space:nowrap; }
    .sv-hub-tools { display:flex; flex-wrap:wrap; gap:8px; }.sv-hub-tool { display:inline-flex; align-items:center; gap:7px; padding:9px 11px; border:1px solid var(--hub-line); border-radius:7px; color:var(--hub-ink); background:#fff; text-decoration:none; font-size:.78rem; font-weight:700; }.sv-hub-tool:hover { border-color:var(--hub-blue); color:var(--hub-blue); }
    @media (max-width:800px) { .sv-hub-hero { grid-template-columns:1fr; background:linear-gradient(150deg,#0b1f3a 0%,#123b68 68%,#e7f2ff 68%); padding:24px; }.sv-hub-actions { grid-template-columns:1fr; }.sv-hub-register { grid-template-columns:1fr 1fr; }.sv-hub-register button { width:100%; } }
    @media (max-width:520px) { .sv-hub-register { grid-template-columns:1fr; } }
</style>
@endpush

<section class="sv-hub" aria-labelledby="hub-title">
    <div class="sv-hub-hero">
        <div>
            <p class="sv-hub-kicker">SPACE VISION / SANS MONITOR</p>
            <h1 id="hub-title">{{ $isEn ? 'Prevent SANS with a clearer medical record.' : 'Prevenir el SANS con un expediente más claro.' }}</h1>
            <p>{{ $isEn ? 'Start with one astronaut, one new record, or the population signal. The clinical tools stay one click away.' : 'Empieza con un astronauta, un nuevo registro o la señal poblacional. Las herramientas clínicas siguen a un clic.' }}</p>
        </div>
        <aside class="sv-hub-hero-panel" aria-label="{{ $isEn ? 'Active astronaut' : 'Astronauta activo' }}">
            <div style="display:flex;align-items:center;gap:11px;">
                <img src="{{ $hubCrew['avatar'] }}" alt="{{ $hubCrew['name'] }}">
                <div><strong>{{ $hubCrew['name'] }}</strong><small>{{ $activeCase->case_id }} · {{ $hubCrew['role'] }}</small></div>
            </div>
            <span class="sv-hub-status {{ $status['class'] }}">{{ $status['label'] }}</span>
            <div style="margin-top:12px;color:var(--hub-muted);font-size:.77rem;">{{ $archetypeLabels[$activeCase->scenario_archetype] ?? $activeCase->scenario_archetype }}</div>
        </aside>
    </div>

    <div class="sv-hub-section">
        <div class="sv-hub-section-heading"><div><h2>{{ $isEn ? 'What do you need to do?' : '¿Qué necesitas hacer?' }}</h2><p>{{ $isEn ? 'Three clear starting points for the next clinical action.' : 'Tres puntos de entrada para la siguiente acción clínica.' }}</p></div></div>
        <div class="sv-hub-actions">
            <a class="sv-hub-action sv-hub-action-primary" href="{{ route('public.astronaut', ['case_id' => $activeCase->case_id]) }}"><i class="fa fa-user-md"></i><div><strong>{{ $isEn ? 'Run astronaut check' : 'Hacer revisión del astronauta' }}</strong><span>{{ $isEn ? 'Guided symptoms, visual checks, IOP and context.' : 'Síntomas, pruebas visuales, PIO y contexto guiados.' }}</span></div></a>
            <a class="sv-hub-action" href="#new-register"><i class="fa fa-plus-circle" style="color:var(--hub-blue);"></i><div><strong>{{ $isEn ? 'Create new register' : 'Crear nuevo registro' }}</strong><span>{{ $isEn ? 'Open a pseudonymous dossier for a new subject.' : 'Abre un expediente seudonimizado para una persona nueva.' }}</span></div></a>
            <a class="sv-hub-action" href="{{ route('home', ['tab' => 'poblacion']) }}"><i class="fa fa-line-chart" style="color:#0f766e;"></i><div><strong>{{ $isEn ? 'Review population signal' : 'Revisar señal poblacional' }}</strong><span>{{ $isEn ? 'Compare cohorts, trends and data coverage with filters.' : 'Compara cohortes, tendencias y cobertura con filtros.' }}</span></div></a>
        </div>
    </div>

    <div class="sv-hub-section">
        <div class="sv-hub-section-heading"><div><h2>{{ $isEn ? 'Select an astronaut' : 'Selecciona un astronauta' }}</h2><p>{{ $isEn ? 'Continue the longitudinal record without losing your place.' : 'Continúa el expediente longitudinal sin perder el contexto.' }}</p></div><a class="btn btn-outline" href="{{ route('public.astronaut') }}">{{ $isEn ? 'Open astronaut workspace' : 'Abrir espacio del astronauta' }}</a></div>
        <div class="sv-hub-crew-grid">
            @foreach($cases as $c)
                @php $meta = \App\Models\AstronautProfile::crewMetaForCase($c->case_id); @endphp
                <a class="sv-hub-crew-card {{ $c->case_id === $activeCase->case_id ? 'is-active' : '' }}" href="{{ route('public.astronaut', ['case_id' => $c->case_id]) }}"><img src="{{ $meta['avatar'] }}" alt="{{ $meta['name'] }}"><span><strong>{{ $meta['name'] }}</strong><small>{{ $c->case_id }} · {{ $archetypeLabels[$c->scenario_archetype] ?? $c->scenario_archetype }}</small></span></a>
            @endforeach
        </div>
    </div>

    <div class="sv-hub-section" id="new-register">
        <div class="sv-hub-section-heading"><div><h2>{{ $isEn ? 'New medical register' : 'Nuevo registro médico' }}</h2><p>{{ $isEn ? 'Start with the minimum identity and mission context. Add measurements later.' : 'Comienza con la identidad mínima y el contexto de misión. Añade mediciones después.' }}</p></div></div>
        <form class="sv-hub-register" method="POST" action="{{ route('admin.sans-monitor.cases.store') }}">
            @csrf
            <label>{{ $isEn ? 'Subject ID / pseudonym' : 'ID / pseudónimo' }}<input class="form-control" type="text" name="case_id" placeholder="CASE-ASTRONAUT-09" required></label>
            <label>{{ $isEn ? 'Mission phase' : 'Fase de misión' }}<select class="form-control" name="mission_phase"><option value="PRE_VUELO">{{ $isEn ? 'Before flight' : 'Antes del vuelo' }}</option><option value="EN_VUELO">{{ $isEn ? 'In flight' : 'En vuelo' }}</option><option value="POSVUELO">{{ $isEn ? 'Post flight' : 'Posvuelo' }}</option></select></label>
            <label>{{ $isEn ? 'Planned duration' : 'Duración prevista' }}<input class="form-control" type="number" name="planned_duration_days" value="90" min="1"></label>
            <input type="hidden" name="planned_duration_state" value="VALOR"><input type="hidden" name="previous_flights_state" value="DESCONOCIDO"><input type="hidden" name="previous_flights" value="0"><input type="hidden" name="source_type" value="FICTICIO_DEMO"><input type="hidden" name="applied_protocol_state" value="DESCONOCIDO"><input type="hidden" name="reason_for_review" value="Initial SANS prevention register">
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{ $isEn ? 'Create register' : 'Crear registro' }}</button>
        </form>
    </div>

    <div class="sv-hub-section">
        <div class="sv-hub-section-heading"><div><h2>{{ $isEn ? 'Advanced workspace' : 'Espacio avanzado' }}</h2><p>{{ $isEn ? 'The complete dashboard is here when you need deeper analysis.' : 'El dashboard completo está aquí cuando necesites análisis más profundo.' }}</p></div><a class="btn btn-primary" href="{{ route('home', ['tab' => 'biomarcadores', 'case_id' => $activeCase->case_id]) }}"><i class="fa fa-flask"></i> {{ $isEn ? 'Open clinical workspace' : 'Abrir espacio clínico' }}</a></div>
        <div class="sv-hub-tools"><a class="sv-hub-tool" href="{{ route('public.digital-twin', ['mode' => 'dual', 'case_id' => $activeCase->case_id]) }}"><i class="fa fa-cube"></i> 3D + OCT</a><a class="sv-hub-tool" href="{{ route('public.vision-lab') }}"><i class="fa fa-camera"></i> {{ $isEn ? 'Fundus scanner' : 'Escáner de fondo' }}</a><a class="sv-hub-tool" href="{{ route('public.model-lab') }}"><i class="fa fa-cogs"></i> {{ $isEn ? 'Model lab' : 'Laboratorio de modelos' }}</a><a class="sv-hub-tool" href="{{ route('public.explainability') }}"><i class="fa fa-lightbulb-o"></i> {{ $isEn ? 'Explainability' : 'Explicabilidad' }}</a><a class="sv-hub-tool" href="{{ route('public.nasa-data') }}"><i class="fa fa-database"></i> NASA data</a></div>
    </div>
</section>