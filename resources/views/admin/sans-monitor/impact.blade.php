@extends('layouts.app')

@php
    $isEn = app()->getLocale() === 'en';
@endphp

@section('title', $isEn ? 'Through the Astronaut\'s Eyes & Mission Impact' : 'En los Ojos del Astronauta e Impacto')

@push('styles')
<style>
    .impact-hero {
        background: linear-gradient(135deg, #07173F 0%, #0042A6 100%);
        color: #ffffff;
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.25rem;
        border: 1.5px solid #2E96F5;
        box-shadow: 0 10px 28px rgba(7, 23, 63, 0.22);
    }
    .cockpit-viewport {
        position: relative;
        background: #040814;
        border: 2px solid #1e3a8a;
        border-radius: 12px;
        overflow: hidden;
        padding: 1.25rem;
        color: #f8fafc;
        min-height: 330px;
    }
    .cockpit-distortion-layer {
        transition: filter 0.25s ease, opacity 0.25s ease;
    }
    .cockpit-vignette {
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(circle at center, transparent 48%, rgba(2, 6, 23, 0.88) 100%);
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .cockpit-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 0.9rem;
    }
    @media (max-width: 720px) {
        .cockpit-grid { grid-template-columns: 1fr; }
    }
    .cockpit-instrument {
        background: #0b1224;
        border: 1px solid #1e3a8a;
        border-radius: 8px;
        padding: 0.75rem 0.9rem;
        font-family: ui-monospace, monospace;
    }
    .override-btn {
        background: #1e293b;
        color: #f8fafc;
        border: 1.5px solid #38bdf8;
        border-radius: 8px;
        padding: 0.55rem 0.9rem;
        font-family: ui-monospace, monospace;
        font-weight: 800;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .override-btn:hover {
        background: #0042A6;
        border-color: #EAFE07;
        color: #EAFE07;
    }
    .roi-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 900px) {
        .roi-grid { grid-template-columns: 1fr; }
    }
    .roi-card {
        background: var(--card);
        border: 1.5px solid var(--border);
        border-top: 4px solid var(--space-blue);
        border-radius: 10px;
        padding: 1.15rem 1.25rem;
    }
    .roi-big-number {
        font-size: 2.15rem;
        font-weight: 900;
        letter-spacing: -0.04em;
        color: var(--space-blue);
        line-height: 1.05;
        margin: 0.35rem 0;
    }
    .mode-pill-btn {
        padding: 0.45rem 0.9rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        border: 1.5px solid #2E96F5;
        cursor: pointer;
        background: rgba(255,255,255,0.1);
        color: #ffffff;
        transition: all 0.15s ease;
    }
    .mode-pill-btn.is-active {
        background: #EAFE07;
        color: #07173F;
        border-color: #EAFE07;
    }
</style>
@endpush

@section('content')
{{-- SVG Wavy Choroidal Folds Filter --}}
<svg width="0" height="0" style="position:absolute;">
    <defs>
        <filter id="sans-choroidal-warp">
            <feTurbulence type="turbulence" baseFrequency="0.025" numOctaves="2" result="noise" />
            <feDisplacementMap in="SourceGraphic" in2="noise" scale="0" id="choroidal-displace" xChannelSelector="R" yChannelSelector="G" />
        </filter>
    </defs>
</svg>

{{-- 1. HERO: ENTIENDE EL PROBLEMA EN 10 SEGUNDOS --}}
<div class="impact-hero">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
        <div style="max-width:760px;">
            <div style="display:inline-flex;align-items:center;gap:0.45rem;background:rgba(234,254,7,0.16);border:1px solid #EAFE07;color:#EAFE07;padding:0.22rem 0.65rem;border-radius:999px;font-size:0.7rem;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:0.65rem;">
                <i class="fa fa-bolt"></i> {{ $isEn ? '15-SECOND EXECUTIVE & MISSION DEMO' : 'DEMO EJECUTIVA Y DE MISIÓN EN 15 SEGUNDOS' }}
            </div>
            <h1 style="font-size:clamp(1.45rem,2.6vw,2.15rem);font-weight:900;margin:0 0 0.45rem;line-height:1.12;">
                {{ $isEn
                    ? 'In space, fluid shifts flatten the back of the eye. Can you land a spacecraft if you can\'t read the cockpit?'
                    : 'En el espacio, los fluidos aplastan el ojo por detrás. ¿Podrías aterrizar una nave si no logras leer el panel?' }}
            </h1>
            <p style="margin:0;color:#dbeafe;font-size:0.95rem;line-height:1.5;">
                {{ $isEn
                    ? 'Experience Spaceflight-Associated Neuro-ocular Syndrome (SANS) in first person, see the financial & mission risk, and test how early monitoring protects crews in orbit and patients on Earth.'
                    : 'Siente en primera persona qué ve un astronauta con SANS, conoce el costo operativo en misión y descubre cómo el mismo flujo salva visión en comunidades rurales de la Tierra.' }}
            </p>
        </div>

        {{-- Traductor Científico <-> Humano --}}
        <div style="display:flex;flex-direction:column;gap:0.45rem;align-items:flex-end;">
            <span style="font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;color:#93c5fd;font-weight:700;">
                {{ $isEn ? 'Language Mode:' : 'Traductor de Lenguaje:' }}
            </span>
            <div style="display:flex;gap:0.4rem;">
                <button type="button" class="mode-pill-btn is-active" id="btn-lang-human" onclick="setJargonMode('human')">
                    <i class="fa fa-user"></i> {{ $isEn ? 'Judge / Business Mode' : 'Modo Juez / Negocio' }}
                </button>
                <button type="button" class="mode-pill-btn" id="btn-lang-science" onclick="setJargonMode('science')">
                    <i class="fa fa-flask"></i> {{ $isEn ? 'NASA Science Mode' : 'Modo Científico NASA' }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 2. SIMULADOR EN PRIMERA PERSONA: "¿PUEDES ATERRIZAR LA NAVE?" --}}
<div class="card" style="padding:1.25rem 1.4rem;margin-bottom:1.25rem;border:1.5px solid var(--space-blue);">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
        <div>
            <h2 style="font-size:1.12rem;font-weight:800;margin:0;color:var(--space-blue);">
                <i class="fa fa-eye"></i>
                {{ $isEn ? '1. First-Person Simulator: Through the Astronaut\'s Eyes (40 cm Cockpit Panel)' : '1. Simulador en Primera Persona: A Través de los Ojos del Astronauta (Panel a 40 cm)' }}
            </h2>
            <p style="margin:0.2rem 0 0;font-size:0.84rem;color:var(--muted-foreground);" id="sim-plain-explainer">
                {{ $isEn
                    ? 'Move the mission slider or pick an astronaut to feel how fluid pressure blurs near instruments and warps straight lines.'
                    : 'Mueve el deslizador de misión o elige un astronauta para sentir cómo la presión cefálica desenfoca los botones cercanos y ondula las líneas rectas.' }}
            </p>
        </div>

        <div style="display:flex;gap:0.45rem;flex-wrap:wrap;">
            <button type="button" class="btn btn-outline" style="padding:0.4rem 0.75rem;font-size:0.78rem;" onclick="applyPreset(0)">
                <i class="fa fa-check-circle" style="color:#16a34a;"></i> {{ $isEn ? 'Day 0 · Healthy Earth' : 'Día 0 · Tierra (Sano)' }}
            </button>
            <button type="button" class="btn btn-outline" style="padding:0.4rem 0.75rem;font-size:0.78rem;" onclick="applyPreset(30)">
                <i class="fa fa-exclamation-triangle" style="color:#d97706;"></i> {{ $isEn ? 'Day 30 · Alex (Alert +26 µm)' : 'Día 30 · Alex (Alerta +26 µm)' }}
            </button>
            <button type="button" class="btn btn-outline" style="padding:0.4rem 0.75rem;font-size:0.78rem;" onclick="applyPreset(180)">
                <i class="fa fa-fire" style="color:#dc2626;"></i> {{ $isEn ? 'Day 180 · Mars (Untreated)' : 'Día 180 · Marte (Sin control)' }}
            </button>
            <button type="button" class="btn btn-primary" style="padding:0.4rem 0.85rem;font-size:0.78rem;" onclick="activateEarlyProtection()">
                <i class="fa fa-shield"></i> {{ $isEn ? 'Apply SPACE VISION Early Action' : 'Activar Detección SPACE VISION' }}
            </button>
        </div>
    </div>

    {{-- Deslizador de Días de Misión --}}
    <div style="margin-bottom:1rem;padding:0.8rem 1rem;background:var(--muted);border-radius:8px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;flex-wrap:wrap;gap:0.5rem;">
            <strong style="font-size:0.85rem;" id="sim-stage-label">Día 30 en Órbita — CDR Alex Mercer (CASE-PROG-02)</strong>
            <span class="badge" id="sim-metric-badge" style="font-family:monospace;font-size:0.76rem;">
                Desenfoque cercano: +0.75 D · Espesor retina: +26 µm
            </span>
        </div>
        <input type="range" min="0" max="180" value="30" step="15" id="impact-day-slider" style="width:100%;accent-color:#0042A6;cursor:pointer;">
        <div style="display:flex;justify-content:space-between;font-size:0.72rem;color:var(--muted-foreground);margin-top:0.25rem;font-family:monospace;">
            <span>Día 0 (Tierra 1G)</span>
            <span>Día 30 (ISS · Alerta temprana)</span>
            <span>Día 90 (Edema + Pliegues)</span>
            <span>Día 180 (Aproximación a Marte)</span>
        </div>
    </div>

    {{-- Panel de Cabina Visto por el Astronauta --}}
    <div class="cockpit-viewport">
        <div class="cockpit-distortion-layer" id="cockpit-view-layer">
            <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #1e3a8a;padding-bottom:0.65rem;flex-wrap:wrap;gap:0.5rem;">
                <div>
                    <div style="font-size:0.68rem;color:#38bdf8;letter-spacing:0.08em;font-family:monospace;">ORION / ARTEMIS MANUAL LANDING CONSOLE · 40 CM READING DISTANCE</div>
                    <div style="font-size:1.05rem;font-weight:800;color:#ffffff;">MANUAL DE-ORBIT BURN &amp; EMERGENCY CHECKLIST</div>
                </div>
                <div style="background:#0f172a;border:1px solid #38bdf8;padding:0.35rem 0.7rem;border-radius:6px;font-family:monospace;font-size:0.82rem;">
                    OVERRIDE CODE: <strong style="color:#EAFE07;font-size:1.05rem;letter-spacing:0.15em;">7 - 4 - 9 - 2</strong>
                </div>
            </div>

            <div class="cockpit-grid">
                <div class="cockpit-instrument">
                    <div style="font-size:0.68rem;color:#94a3b8;">CABIN PRESSURE / ALTITUDE</div>
                    <div style="font-size:1.35rem;font-weight:800;color:#22c55e;">101.3 kPa · 14.2 km</div>
                    <div style="font-size:0.72rem;color:#cbd5e1;margin-top:0.25rem;">Verify valve B-14 closed before burn</div>
                </div>

                <div class="cockpit-instrument">
                    <div style="font-size:0.68rem;color:#94a3b8;">RE-ENTRY ALIGNMENT GRID (STRAIGHT LINES)</div>
                    <div style="margin:0.35rem 0;height:44px;border:1px solid #38bdf8;background-image:linear-gradient(#38bdf8 1px,transparent 1px),linear-gradient(90deg,#38bdf8 1px,transparent 1px);background-size:11px 11px;opacity:0.85;"></div>
                    <div style="font-size:0.7rem;color:#cbd5e1;">Lines warp when choroidal folds appear</div>
                </div>

                <div class="cockpit-instrument">
                    <div style="font-size:0.68rem;color:#94a3b8;">5-SECOND JUDGE CHALLENGE</div>
                    <div style="font-size:0.75rem;color:#fde68a;margin-bottom:0.45rem;">
                        {{ $isEn ? 'Tap the correct Override Code (7-4-9-2):' : 'Presiona el Código de Emergencia correcto (7-4-9-2):' }}
                    </div>
                    <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                        <button type="button" class="override-btn" onclick="checkChallenge(false)">7-4-8-2</button>
                        <button type="button" class="override-btn" onclick="checkChallenge(true)">7-4-9-2</button>
                        <button type="button" class="override-btn" onclick="checkChallenge(false)">7-1-9-2</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="cockpit-vignette" id="cockpit-vignette"></div>
    </div>

    <div id="challenge-feedback" style="display:none;margin-top:0.75rem;padding:0.7rem 0.95rem;border-radius:8px;font-size:0.84rem;font-weight:700;"></div>
</div>

{{-- 3. LA TARJETA DEL CONTADOR: IMPACTO FINANCIERO, OPERATIVO Y EN LA TIERRA --}}
<div class="roi-grid">
    <div class="roi-card">
        <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;color:var(--muted-foreground);letter-spacing:0.06em;">
            {{ $isEn ? 'THE MISSION RISK' : 'EL RIESGO OPERATIVO' }}
        </div>
        <div class="roi-big-number">~70%</div>
        <div style="font-size:0.9rem;font-weight:800;margin-bottom:0.35rem;">
            {{ $isEn ? 'of long-duration astronauts develop SANS signs' : 'de astronautas en misiones largas presentan signos SANS' }}
        </div>
        <p style="margin:0;font-size:0.82rem;color:var(--muted-foreground);line-height:1.45;" class="translatable-copy"
           data-human="{{ $isEn ? 'It is NASA\'s #1 physiological barrier to Mars. You cannot send a crew for 3 years if they lose near-vision on month 2.' : 'Es la barrera fisiológica #1 de la NASA para llegar a Marte. No puedes mandar una tripulación 3 años si al mes 2 dejan de ver de cerca.' }}"
           data-science="{{ $isEn ? 'Peripapillary TRT thickening (ΔTRT >= 20 µm), posterior globe flattening, and hyperopic shift >= +0.50 D documented in HRP Evidence Reports.' : 'Engrosamiento peripapilar TRT (ΔTRT >= 20 µm), aplanamiento escleral posterior y shift hipermetrópico >= +0.50 D documentado en NASA HRP.' }}">
            {{ $isEn ? 'It is NASA\'s #1 physiological barrier to Mars. You cannot send a crew for 3 years if they lose near-vision on month 2.' : 'Es la barrera fisiológica #1 de la NASA para llegar a Marte. No puedes mandar una tripulación 3 años si al mes 2 dejan de ver de cerca.' }}
        </p>
    </div>

    <div class="roi-card" style="border-top-color:#07173F;">
        <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;color:var(--muted-foreground);letter-spacing:0.06em;">
            {{ $isEn ? 'THE ACCOUNTANT\'S VIEW (ROI)' : 'LA VISIÓN DEL CONTADOR (ROI)' }}
        </div>
        <div class="roi-big-number" style="color:#07173F;">$2.5B+ USD</div>
        <div style="font-size:0.9rem;font-weight:800;margin-bottom:0.35rem;">
            {{ $isEn ? 'mission hardware & crew safety protected' : 'en misión y seguridad de tripulación protegidos' }}
        </div>
        <p style="margin:0;font-size:0.82rem;color:var(--muted-foreground);line-height:1.45;" class="translatable-copy"
           data-human="{{ $isEn ? 'A single aborted EVA or damaged spacecraft docking costs hundreds of millions. Detecting +26 µm early costs almost zero using existing on-board hardware.' : 'Una sola caminata espacial cancelada o un error de acoplamiento cuesta cientos de millones. Detectar +26 µm a tiempo cuesta casi cero usando el equipo ya a bordo.' }}"
           data-science="{{ $isEn ? 'Zero-hallucination triage flags Prioritize Review only when ΔTRT >= +20 µm or progression holds, avoiding false alarms and unnecessary mission aborts.' : 'Triaje determinista sin alucinaciones: activa Priorizar Revisión solo ante ΔTRT >= +20 µm o progresión sostenida, evitando falsas alarmas.' }}">
            {{ $isEn ? 'A single aborted EVA or damaged spacecraft docking costs hundreds of millions. Detecting +26 µm early costs almost zero using existing on-board hardware.' : 'Una sola caminata espacial cancelada o un error de acoplamiento cuesta cientos de millones. Detectar +26 µm a tiempo cuesta casi cero usando el equipo ya a bordo.' }}
        </p>
    </div>

    <div class="roi-card" style="border-top-color:#16a34a;">
        <div style="font-size:0.7rem;font-weight:800;text-transform:uppercase;color:var(--muted-foreground);letter-spacing:0.06em;">
            {{ $isEn ? 'REAL IMPACT ON EARTH' : 'IMPACTO REAL EN LA TIERRA' }}
        </div>
        <div class="roi-big-number" style="color:#16a34a;">80%</div>
        <div style="font-size:0.9rem;font-weight:800;margin-bottom:0.35rem;">
            {{ $isEn ? 'of rural clinics also lack an ophthalmologist' : 'de clínicas rurales tampoco tienen oftalmólogo' }}
        </div>
        <p style="margin:0;font-size:0.82rem;color:var(--muted-foreground);line-height:1.45;" class="translatable-copy"
           data-human="{{ $isEn ? 'No eye doctor on the ISS is the exact same problem as rural towns, ships, and oil rigs on Earth. SPACE VISION prioritizes who needs urgent specialist rescue—without replacing the doctor.' : 'Sin oftalmólogo en la estación espacial es el mismo problema de los pueblos rurales, barcos y plataformas en la Tierra. SPACE VISION prioriza quién requiere atención urgente sin reemplazar al médico.' }}"
           data-science="{{ $isEn ? 'Strict RF-10/RF-11 boundary: separates data completeness (Missing Data) from clinical escalation (Prioritize Review) while reserving Fitness Verdicts for licensed physicians.' : 'Frontera ética RF-10/RF-11: separa datos faltantes de escalamiento clínico y reserva el dictamen médico exclusivamente al especialista humano.' }}">
            {{ $isEn ? 'No eye doctor on the ISS is the exact same problem as rural towns, ships, and oil rigs on Earth. SPACE VISION prioritizes who needs urgent specialist rescue—without replacing the doctor.' : 'Sin oftalmólogo en la estación espacial es el mismo problema de los pueblos rurales, barcos y plataformas en la Tierra. SPACE VISION prioriza quién requiere atención urgente sin reemplazar al médico.' }}
        </p>
    </div>
</div>

{{-- 4. PUENTE DIRECTO AL SISTEMA CIENTÍFICO COMPLETO --}}
<div class="card" style="padding:1.1rem 1.35rem;display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
    <div>
        <strong style="font-size:0.98rem;">{{ $isEn ? 'Ready to inspect the real scientific engine behind this?' : '¿Listo para inspeccionar el motor científico real detrás de esto?' }}</strong>
        <div style="font-size:0.82rem;color:var(--muted-foreground);">
            {{ $isEn ? 'Jump to the Ground Scientists Workstation, the In-Flight Astronaut Check, or the 3D + OCT Dual Viewer.' : 'Entra al Módulo Científico en Tierra, a la Vista del Astronauta en Vuelo o al Visor 3D + Consola OCT.' }}
        </div>
    </div>
    <div style="display:flex;gap:0.55rem;flex-wrap:wrap;">
        <a href="{{ route('home') }}" class="btn btn-primary" style="font-size:0.8rem;">
            <i class="fa fa-flask"></i> {{ $isEn ? 'Scientific Module (SANS)' : 'Módulo Científico (SANS)' }}
        </a>
        <a href="{{ route('public.astronaut') }}" class="btn btn-outline" style="font-size:0.8rem;">
            <i class="fa fa-user-md"></i> {{ $isEn ? 'Astronaut Check' : 'Vista Astronauta' }}
        </a>
        <a href="{{ route('public.digital-twin', ['mode' => 'dual']) }}" class="btn btn-outline" style="font-size:0.8rem;">
            <i class="fa fa-cube"></i> {{ $isEn ? '3D Viewer + OCT' : 'Visor 3D + OCT' }}
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
let jargonMode = 'human';
let currentSimDay = 30;
let protectedMode = false;

function setJargonMode(mode) {
    jargonMode = mode;
    document.getElementById('btn-lang-human').classList.toggle('is-active', mode === 'human');
    document.getElementById('btn-lang-science').classList.toggle('is-active', mode === 'science');

    document.querySelectorAll('.translatable-copy').forEach(el => {
        el.textContent = mode === 'science' ? el.dataset.science : el.dataset.human;
    });
    updateVisionSimulation(currentSimDay, protectedMode);
}

function applyPreset(day) {
    protectedMode = false;
    document.getElementById('impact-day-slider').value = day;
    updateVisionSimulation(day, false);
}

function activateEarlyProtection() {
    protectedMode = true;
    updateVisionSimulation(currentSimDay, true);
}

function updateVisionSimulation(day, isProtected) {
    currentSimDay = Number(day);
    const layer = document.getElementById('cockpit-view-layer');
    const vignette = document.getElementById('cockpit-vignette');
    const displace = document.getElementById('choroidal-displace');
    const stageLabel = document.getElementById('sim-stage-label');
    const badge = document.getElementById('sim-metric-badge');

    if (isProtected) {
        layer.style.filter = 'none';
        vignette.style.opacity = '0';
        displace.setAttribute('scale', '0');
        stageLabel.textContent = jargonMode === 'science'
            ? `SPACE VISION Active (Day ${day}) — Early ΔTRT Alert + Countermeasure / Lens Compensation`
            : `Con SPACE VISION Activo (Día ${day}) — Alerta Temprana + Compensación Óptica`;
        badge.textContent = 'Visión nítida 20/20 · Riesgo mitigado a tiempo';
        badge.style.background = '#dcfce7';
        badge.style.color = '#166534';
        return;
    }

    const blurPx = (day / 180) * 3.4;
    const warpScale = day >= 60 ? ((day - 45) / 135) * 10 : 0;
    const vigOpacity = Math.min(0.85, (day / 180) * 0.75);

    layer.style.filter = warpScale > 0
        ? `blur(${blurPx.toFixed(2)}px) url(#sans-choroidal-warp)`
        : `blur(${blurPx.toFixed(2)}px)`;
    displace.setAttribute('scale', warpScale.toFixed(1));
    vignette.style.opacity = vigOpacity.toFixed(2);

    const deltaTrt = Math.round((day / 30) * 26);
    const hyperopia = ((day / 180) * 1.5).toFixed(2);

    if (jargonMode === 'science') {
        stageLabel.textContent = `FD${day} · Simulated Progression (ΔTRT +${deltaTrt} µm · ΔSE +${hyperopia} D)`;
        badge.textContent = `Blur: ${blurPx.toFixed(1)}px · Folds warp: ${warpScale.toFixed(1)}`;
    } else {
        stageLabel.textContent = day === 0
            ? 'Día 0 en Tierra (1G) — Visión completamente nítida'
            : (day <= 30
                ? `Día ${day} en Órbita — Empieza el desenfoque de cerca (Alerta temprana +${deltaTrt} µm)`
                : `Día ${day} rumbo a Marte — El ojo aplastado ya distorsiona los botones y líneas`);
        badge.textContent = `Desenfoque cercano: +${hyperopia} D · Hinchazón retina: +${deltaTrt} µm`;
    }
    badge.style.background = day >= 60 ? '#fee2e2' : (day >= 30 ? '#fef3c7' : '#dcfce7');
    badge.style.color = day >= 60 ? '#991b1b' : (day >= 30 ? '#92400e' : '#166534');
}

function checkChallenge(correct) {
    const fb = document.getElementById('challenge-feedback');
    fb.style.display = 'block';
    if (correct && (currentSimDay === 0 || protectedMode)) {
        fb.style.background = '#dcfce7';
        fb.style.color = '#166534';
        fb.textContent = '✅ ¡Código 7-4-9-2 confirmado sin dudar! Así es como la detección temprana protege la lectura crítica en cabina.';
    } else if (correct) {
        fb.style.background = '#fef3c7';
        fb.style.color = '#92400e';
        fb.textContent = `⚠️ Acertaste en el Día ${currentSimDay}, pero nota cuánto esfuerzo visual tomó leerlo con desenfoque. Imagina hacerlo en turbulencia de reentrada sin detección previa.`;
    } else {
        fb.style.background = '#fee2e2';
        fb.style.color = '#991b1b';
        fb.textContent = '❌ Código incorrecto (el 9 y el 8/1 se confunden con hipermetropía y pliegues). Por eso SPACE VISION detecta el cambio antes de llegar a este punto.';
    }
}

document.getElementById('impact-day-slider').addEventListener('input', function() {
    protectedMode = false;
    updateVisionSimulation(this.value, false);
});

document.addEventListener('DOMContentLoaded', () => updateVisionSimulation(30, false));
</script>
@endpush
