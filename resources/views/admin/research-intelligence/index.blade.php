@extends('layouts.app')

@section('title', 'Research Intelligence · Biblioteca Científica e Hipótesis SANS')

@push('styles')
<style>
    .intelligence-hero {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        box-shadow: var(--shadow);
    }
    .hypo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .hypo-card {
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
    .hypo-card:hover {
        border-color: var(--foreground);
    }
    .bib-box {
        background: #09090b;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 14px 18px;
        font-family: ui-monospace, monospace;
        font-size: 11px;
        color: #f4f4f5;
        overflow-x: auto;
        white-space: pre-wrap;
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<div class="intelligence-hero">
    <div>
        <div class="eyebrow">
            NASA HUMAN RESEARCH ROADMAP · SCIENTIFIC EVIDENCE CORPUS
        </div>
        <h1 style="margin:4px 0 8px; font-size:24px; font-weight:800; color:var(--foreground); letter-spacing:-0.03em;">
            Research Intelligence & Matriz de Hipótesis SANS
        </h1>
        <p style="margin:0; font-size:13px; color:var(--muted-foreground); max-width:680px;">
            Síntesis estructurada de literatura biomédica espacial, vías mecanicistas de alteración ocular en microgravedad, contramedidas y generador de citas bibliográficas indexadas.
        </p>
    </div>
    <div>
        <span class="badge-subtle" style="font-weight:700;">
            <i class="fa fa-graduation-cap"></i> 4 VÍAS FISIOLÓGICAS MODELADAS
        </span>
    </div>
</div>

{{-- Matriz de Hipótesis Biomecánicas y Genéticas --}}
<div style="margin-bottom:16px;">
    <h2 style="font-size:16px; font-weight:700; color:var(--foreground); margin:0 0 4px;">Hipótesis Biomecánicas del Síndrome SANS</h2>
    <p style="font-size:12px; color:var(--muted-foreground); margin:0;">Mecanismos etiológicos identificados en el programa de investigación en humanos de la NASA (NASA HRP).</p>
</div>

<div class="hypo-grid">
    @foreach ($hypotheses as $h)
        <div class="hypo-card">
            <div>
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                    <span class="badge-subtle" style="font-family:ui-monospace, monospace; font-size:10px; font-weight:800;">
                        {{ $h['id'] }} · {{ $h['category'] }}
                    </span>
                    <span style="font-size:11px; color:var(--muted-foreground); font-weight:600;">{{ $h['status'] }}</span>
                </div>
                <h3 style="font-size:15px; font-weight:700; color:var(--foreground); margin:4px 0 8px;">{{ $h['title'] }}</h3>
                <p style="font-size:12px; color:var(--muted-foreground); line-height:1.6; margin-bottom:14px;">
                    {{ $h['description'] }}
                </p>
            </div>
            <div style="border-top:1px solid var(--border); padding-top:12px; margin-top:8px;">
                <div style="font-size:11px; color:var(--muted-foreground); margin-bottom:4px;">
                    <strong>Referencia NASA:</strong> <span style="color:var(--foreground); font-family:ui-monospace, monospace;">{{ $h['nasa_ref'] }}</span>
                </div>
                <div style="font-size:11px; color:var(--muted-foreground);">
                    <strong>Contramedida:</strong> {{ $h['countermeasure'] }}
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Generador de Citas Bibliográficas para Investigadores --}}
<div class="card" style="padding:24px; margin-bottom:28px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
            <h2 class="card-title" style="font-size:16px;">Generador de Citas Científicas de NASA</h2>
            <p class="card-description">Exporta referencias de publicaciones técnicas oficiales en formatos académicos estándar (APA / BibTeX).</p>
        </div>
        <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-outline" onclick="copyBibtex()" style="font-size:12px; padding:6px 12px;">
                <i class="fa fa-copy"></i> Copiar BibTeX
            </button>
        </div>
    </div>

    <div class="bib-box" id="bibtex-output">@article{stenger2021sans,
  title={Risk of Spaceflight-Associated Neuro-ocular Syndrome (SANS): Human Research Program Evidence Report},
  author={Stenger, Michael B. and Tarver, William J. and Brunstetter, Tyson and Gibson, C. Robert and Laurie, Steven S. and Lee, Andrew G. and Macias, Brandon R. and Mader, Thomas H.},
  journal={NASA Lyndon B. Johnson Space Center Technical Publication},
  year={2021},
  url={https://ntrs.nasa.gov/citations/20200002341},
  doi={10.1016/j.ophtha.2020.09.021}
}

@article{mader2019optic,
  title={Optic Disc Edema and Posterior Globe Flattening in Long-Duration Spaceflight Crewmembers},
  author={Mader, Thomas H. and Gibson, C. Robert and Pass, Anastas F. and Lee, Andrew G. and Stenger, Michael B.},
  journal={JAMA Ophthalmology / NASA HRP},
  volume={137},
  number={4},
  pages={380--387},
  year={2019},
  doi={10.1001/jamaophthalmol.2018.4219}
}</div>
</div>

{{-- Cuaderno de Notas de Investigación (Persistente en Navegador) --}}
<div class="card" style="padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h2 class="card-title" style="font-size:16px;">Notas del Investigador Biomédico (Espacio Seguro)</h2>
        <span id="save-status" style="font-size:11px; color:var(--muted-foreground); font-weight:600;">Guardado automáticamente</span>
    </div>
    <textarea id="researcher-notes" rows="4" class="form-control" placeholder="Escribe aquí observaciones preliminares sobre correlaciones entre shift hipermetrópico y días de misión..." style="border-radius:8px; padding:12px; font-size:13px; line-height:1.5; width:100%; height:auto;"></textarea>
</div>
@endsection

@push('scripts')
<script>
function copyBibtex() {
    const text = document.getElementById('bibtex-output').innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Citas en formato BibTeX copiadas al portapapeles.');
    });
}

const notesEl = document.getElementById('researcher-notes');
const statusEl = document.getElementById('save-status');

notesEl.value = localStorage.getItem('space_vision_researcher_notes') || 
    "Observación de simulación: CDR Alex Mercer muestra una pendiente hipermetrópica acelerada a partir de FD45 coincidente con edema papilar Frisén Grado 1. Hipótesis compatible con elevación de ICP retrobulbar sostenida en microgravedad.";

notesEl.addEventListener('input', function() {
    localStorage.setItem('space_vision_researcher_notes', this.value);
    statusEl.textContent = 'Guardado';
});
</script>
@endpush
