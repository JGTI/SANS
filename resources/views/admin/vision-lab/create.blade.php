@extends('layouts.app')

@section('title', 'Nueva Evaluación Oftalmológica · Vision Lab')

@push('styles')
<style>
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 20px; }
    .calc-preview {
        background: var(--muted);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px;
        margin-bottom: 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }
    .calc-item { display: flex; flex-direction: column; gap: 4px; }
    .calc-item label { font-size: 11px; text-transform: uppercase; color: var(--muted-foreground); font-weight: 700; letter-spacing: .05em; }
    .calc-value { font-size: 20px; font-weight: 800; font-family: ui-monospace, monospace; color: var(--foreground); }
    .section-title { font-size: 15px; font-weight: 700; margin: 20px 0 12px; color: var(--foreground); display: flex; align-items: center; gap: 8px; }
    .checkbox-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; background: var(--muted); cursor: pointer; font-size: 13px; }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow"><a href="{{ route('admin.vision-lab.index') }}" style="color:var(--muted-foreground);">&larr; Volver a Vision Lab</a></div>
        <h1>Registrar Evaluación Oftalmológica</h1>
        <p>Instrumentación clínica para Directores Médicos de Vuelo (Flight Surgeons). Cálculo asistido en tiempo real.</p>
    </div>
</div>

@if ($errors->any())
    <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <ul style="margin:0; padding-left:16px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.vision-lab.store') }}" method="POST">
    @csrf

    {{-- Cuadro de Telemetría Clínica Calculada en Tiempo Real --}}
    <div class="calc-preview">
        <div class="calc-item">
            <label>Equivalente Esférico (SE)</label>
            <div class="calc-value" id="preview-se">0.00 D</div>
            <span style="font-size:10px; color:var(--muted-foreground);">Fórmula: SPH + (CYL / 2)</span>
        </div>
        <div class="calc-item">
            <label>Agudeza LogMAR</label>
            <div class="calc-value" id="preview-logmar">0.000</div>
            <span style="font-size:10px; color:var(--muted-foreground);">log10(Denominador / 20)</span>
        </div>
        <div class="calc-item">
            <label>Vectores Thibos (M, J0, J45)</label>
            <div class="calc-value" id="preview-thibos" style="font-size:14px; font-weight:700;">M: 0.00 | J0: 0.00 | J45: 0.00</div>
            <span style="font-size:10px; color:var(--muted-foreground);">B (Norma): <strong id="preview-thibos-b" style="color:var(--foreground);">0.00 D</strong></span>
        </div>
        <div class="calc-item">
            <label>Alerta de Riesgo SANS</label>
            <div class="calc-value" id="preview-risk" style="color:var(--foreground);">BAJO</div>
            <span style="font-size:10px; color:var(--muted-foreground);" id="preview-risk-desc">Parámetros dentro de norma</span>
        </div>
    </div>

    {{-- 1. Datos Generales de la Misión --}}
    <div class="card" style="margin-bottom: 24px; padding: 20px;">
        <h3 class="section-title">
            <i class="fa fa-user" style="margin-right:8px;"></i>
            1. Parámetros de Misión y Sujeto
        </h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="astronaut_id">Astronauta *</label>
                <select name="astronaut_id" id="astronaut_id" class="form-control" required>
                    <option value="">-- Seleccionar tripulante --</option>
                    @foreach ($astronauts as $astro)
                        <option value="{{ $astro->id }}" data-baseline-od="{{ $astro->baseline_se_od }}" data-baseline-os="{{ $astro->baseline_se_os }}">
                            {{ $astro->code }} - {{ $astro->name }} ({{ $astro->mission_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="eye">Ojo a Evaluar *</label>
                <select name="eye" id="eye" class="form-control" required>
                    <option value="OD">Ojo Derecho (OD - Oculus Dexter)</option>
                    <option value="OS">Ojo Izquierdo (OS - Oculus Sinister)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="evaluated_at">Fecha de Examen *</label>
                <input type="datetime-local" name="evaluated_at" id="evaluated_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="mission_day">Día de Misión (Flight Day) *</label>
                <input type="number" name="mission_day" id="mission_day" class="form-control" placeholder="Ej: 45" min="0" max="2000" value="45" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="flight_phase">Fase de Vuelo *</label>
                <select name="flight_phase" id="flight_phase" class="form-control" required>
                    <option value="in_flight" selected>En órbita (In-Flight / Microgravedad)</option>
                    <option value="pre_flight">Línea base pre-vuelo (Pre-Flight)</option>
                    <option value="post_flight">Recuperación post-vuelo (Post-Flight)</option>
                </select>
            </div>
        </div>
    </div>

    {{-- 2. Examen de Refracción Clínica --}}
    <div class="card" style="margin-bottom: 24px; padding: 20px;">
        <h3 class="section-title">
            <i class="fa fa-bullseye" style="margin-right:8px;"></i>
            2. Refracción Clínica por Ojo
        </h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="sphere">Esfera (SPH en Dioptrías) *</label>
                <input type="number" step="0.25" name="sphere" id="sphere" class="form-control calc-trigger" placeholder="Ej: +1.25 ó -0.50" value="0.00" required>
                <span style="font-size:11px; color:var(--muted-foreground);">Valores positivos indican hipermetropía.</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="cylinder">Cilindro (CYL en Dioptrías) *</label>
                <input type="number" step="0.25" name="cylinder" id="cylinder" class="form-control calc-trigger" placeholder="Ej: -0.50" value="0.00" required>
                <span style="font-size:11px; color:var(--muted-foreground);">Corrección de astigmatismo.</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="axis">Eje (Grados 0° - 180°) *</label>
                <input type="number" name="axis" id="axis" class="form-control calc-trigger" placeholder="Ej: 180" min="0" max="180" value="0" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="snellen_denominator">Agudeza Visual Snellen (20 / X) *</label>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-weight:700;">20 /</span>
                    <input type="number" name="snellen_denominator" id="snellen_denominator" class="form-control calc-trigger" placeholder="20, 25, 40, 100" min="10" max="400" value="20" required>
                </div>
                <input type="hidden" name="snellen_numerator" value="20">
            </div>

            <div class="form-group">
                <label class="form-label" for="intraocular_pressure">Presión Intraocular (IOP en mmHg)</label>
                <input type="number" step="0.1" name="intraocular_pressure" id="intraocular_pressure" class="form-control" placeholder="Ej: 16.5" min="5" max="50">
            </div>
        </div>

        <div style="margin-top:10px;">
            <label class="checkbox-pill">
                <input type="checkbox" name="with_correction" value="1">
                Evaluación realizada con lentes correctivos puestos
            </label>
        </div>
    </div>

    {{-- 3. Biomarcadores y Signos Estructurales SANS --}}
    <div class="card" style="margin-bottom: 24px; padding: 20px;">
        <h3 class="section-title">
            <i class="fa fa-eye" style="margin-right:8px;"></i>
            3. Signos Estructurales SANS (Fondo de Ojo / OCT)
        </h3>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="frisen_scale_grade">Edema de Disco Óptico (Escala Frisen)</label>
                <select name="frisen_scale_grade" id="frisen_scale_grade" class="form-control calc-trigger">
                    <option value="0" selected>Grado 0: Disco normal sin edema</option>
                    <option value="1">Grado 1: Halo en forma de C en polo nasal</option>
                    <option value="2">Grado 2: Halo circunferencial completo</option>
                    <option value="3">Grado 3: Obscuración de vasos mayores en el margen</option>
                    <option value="4">Grado 4: Obscuración total de vasos centrales</option>
                    <option value="5">Grado 5: Edema severo en cúpula</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="rnfl_thickness_microns">Grosor Capa Fibras Nerviosas (RNFL OCT en &micro;m)</label>
                <input type="number" name="rnfl_thickness_microns" id="rnfl_thickness_microns" class="form-control calc-trigger" placeholder="Ej: 110" min="50" max="300">
                <span style="font-size:11px; color:var(--muted-foreground);">Promedio normal basal: 90 - 105 &micro;m.</span>
            </div>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:10px;">
            <label class="checkbox-pill">
                <input type="checkbox" name="posterior_globe_flattening" id="posterior_globe_flattening" value="1" class="calc-trigger">
                Aplanamiento posterior del globo ocular (Ecografía)
            </label>
            <label class="checkbox-pill">
                <input type="checkbox" name="choroidal_folds" id="choroidal_folds" value="1" class="calc-trigger">
                Pliegues coroideos / estrías retinianas
            </label>
            <label class="checkbox-pill">
                <input type="checkbox" name="cotton_wool_spots" id="cotton_wool_spots" value="1" class="calc-trigger">
                Manchas algodonosas (isquemia axónica)
            </label>
        </div>
    </div>

    {{-- 4. Síntomas y Notas Médicas --}}
    <div class="card" style="margin-bottom: 24px; padding: 20px;">
        <h3 class="section-title">
            <i class="fa fa-file-text-o" style="margin-right:8px;"></i>
            4. Síntomas Reportados por el Astronauta
        </h3>
        <div style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
            <label class="checkbox-pill">
                <input type="checkbox" name="reported_symptoms[]" value="blurred_near_vision"> Dificultad para lectura cercana
            </label>
            <label class="checkbox-pill">
                <input type="checkbox" name="reported_symptoms[]" value="eyestrain"> Astenopia / fatiga ocular
            </label>
            <label class="checkbox-pill">
                <input type="checkbox" name="reported_symptoms[]" value="headache"> Cefalea frontal / retroocular
            </label>
            <label class="checkbox-pill">
                <input type="checkbox" name="reported_symptoms[]" value="scotoma"> Escotomas / manchas visuales
            </label>
        </div>

        <div class="form-group">
            <label class="form-label" for="clinical_notes">Notas e Interpretación del Flight Surgeon</label>
            <textarea name="clinical_notes" id="clinical_notes" class="form-control" rows="3" placeholder="Observaciones clínicas, recomendaciones de descanso y ajustes de filtros o iluminación en cabina..."></textarea>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:40px;">
        <a href="{{ route('admin.vision-lab.index') }}" class="btn btn-outline">Cancelar</a>
        <button type="submit" class="btn btn-primary" style="padding:12px 28px; font-weight:700;">
            <i class="fa fa-floppy-o" style="margin-right:8px;"></i>
            Guardar Evaluación Oftalmológica
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateCalculations() {
        const sph = parseFloat(document.getElementById('sphere').value) || 0;
        const cyl = parseFloat(document.getElementById('cylinder').value) || 0;
        const denom = parseInt(document.getElementById('snellen_denominator').value) || 20;

        // 1. SE = SPH + CYL/2
        const se = sph + (cyl / 2);
        document.getElementById('preview-se').textContent = (se >= 0 ? '+' : '') + se.toFixed(2) + ' D';

        // 2. LogMAR = log10(denom / 20)
        const logmar = Math.log10(denom / 20);
        document.getElementById('preview-logmar').textContent = logmar.toFixed(3);

        // 3. Thibos Power Vectors
        const axis = parseFloat(document.getElementById('axis').value) || 0;
        const axisRad = (axis * Math.PI) / 180;
        const mVal = se;
        const j0Val = -(cyl / 2) * Math.cos(2 * axisRad);
        const j45Val = -(cyl / 2) * Math.sin(2 * axisRad);
        const bNorm = Math.sqrt(mVal * mVal + j0Val * j0Val + j45Val * j45Val);

        document.getElementById('preview-thibos').textContent = `M: ${(mVal>=0?'+':'')+mVal.toFixed(2)} | J0: ${(j0Val>=0?'+':'')+j0Val.toFixed(2)} | J45: ${(j45Val>=0?'+':'')+j45Val.toFixed(2)}`;
        document.getElementById('preview-thibos-b').textContent = bNorm.toFixed(2) + ' D';

        // 4. Simulación de Riesgo SANS
        const frisen = parseInt(document.getElementById('frisen_scale_grade').value) || 0;
        const flattening = document.getElementById('posterior_globe_flattening').checked;
        const folds = document.getElementById('choroidal_folds').checked;
        const rnfl = parseInt(document.getElementById('rnfl_thickness_microns').value) || 95;

        let score = 0;
        if (frisen >= 3) score += 45;
        else if (frisen >= 1) score += 25;
        if (flattening) score += 20;
        if (folds) score += 20;
        if (rnfl >= 120) score += 15;
        if (se >= 1.0) score += 20;

        const riskEl = document.getElementById('preview-risk');
        const descEl = document.getElementById('preview-risk-desc');

        if (score >= 50) {
            riskEl.textContent = 'ALTO / SEVERO';
            riskEl.style.color = 'var(--foreground)';
            riskEl.style.textDecoration = 'underline';
            descEl.textContent = 'Signos cardinales de SANS detectados';
        } else if (score >= 25) {
            riskEl.textContent = 'MODERADO';
            riskEl.style.color = 'var(--foreground)';
            riskEl.style.textDecoration = 'none';
            descEl.textContent = 'Vigilancia estrecha recomendada';
        } else {
            riskEl.textContent = 'BAJO';
            riskEl.style.color = 'var(--muted-foreground)';
            riskEl.style.textDecoration = 'none';
            descEl.textContent = 'Parámetros dentro de tolerancia';
        }
    }

    document.querySelectorAll('.calc-trigger').forEach(el => {
        el.addEventListener('input', updateCalculations);
        el.addEventListener('change', updateCalculations);
    });

    updateCalculations();
});
</script>
@endpush
@endsection
