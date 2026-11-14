@extends('layouts.app')

@section('title', 'Proyectos · SPACE VISION')

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">Administración</div>
        <h1>Proyectos</h1>
        <p>Gestión de proyectos y seguimiento de registros.</p>
    </div>
    <div>
        <button class="btn btn-primary" id="new-project">
            <i class="fa fa-plus" aria-hidden="true"></i> Nuevo proyecto
        </button>
    </div>
</div>

<div id="projects-admin" data-url="{{ route('admin.projects.index') }}" class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Proyectos de clientes</h2>
            <p class="card-description">Crea el proyecto, copia su enlace privado y compártelo.</p>
        </div>
        <div>
            <label for="project-filter" style="font-size:12px; font-weight:600; color:var(--muted-foreground); margin-right:6px;">Estado:</label>
            <select id="project-filter" class="form-control" style="width:180px; display:inline-block; height:34px; font-size:12px;">
                <option value="">Todos los estados</option>
                @foreach(config('onboarding.statuses') as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body">
        <div id="project-list-error" class="alert alert-danger" role="alert" hidden></div>
        <div class="table-responsive">
            <table id="projects-table" class="table" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Proyecto</th>
                        <th>Empresa</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Onboarding</th>
                        <th>Enlace</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div id="project-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="project-modal-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="project-modal-title" class="modal-title">Proyecto</h3>
                <button class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
            </div>
            <div class="modal-body">
                <ul class="tabs" style="margin-bottom:18px;">
                    <li class="tab active"><a href="#project-settings" data-toggle="tab">Proyecto y enlace</a></li>
                    <li id="answers-tab" class="tab"><a href="#project-answers" data-toggle="tab">Respuestas y archivos</a></li>
                </ul>
                <div class="tab-content" style="padding-top:10px;">
                    <div id="project-settings" class="tab-pane active">
                        <div id="project-errors" class="alert alert-danger" role="alert" tabindex="-1" hidden></div>
                        <form id="project-form">
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                                @foreach(['name' => 'Nombre del proyecto', 'company' => 'Empresa / cliente', 'contact_name' => 'Persona de contacto', 'contact_email' => 'Correo del contacto'] as $key => $label)
                                    <div class="form-group">
                                        <label for="p-{{ $key }}" class="form-label">{{ $label }}</label>
                                        <input class="form-control" id="p-{{ $key }}" name="{{ $key }}" type="{{ $key === 'contact_email' ? 'email' : 'text' }}" maxlength="255" required>
                                    </div>
                                @endforeach
                                <div class="form-group">
                                    <label for="p-status" class="form-label">Estado</label>
                                    <select id="p-status" name="status" class="form-control">
                                        @foreach(config('onboarding.statuses') as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="p-expires_at" class="form-label">Vencimiento del enlace (opcional)</label>
                                    <input type="date" id="p-expires_at" name="expires_at" class="form-control">
                                    <span class="help-block">Disponible hasta el final del día seleccionado.</span>
                                </div>
                            </div>
                            <div class="checkbox" style="margin:12px 0;">
                                <label style="display:flex; align-items:center; gap:8px;">
                                    <input name="link_enabled" id="p-link_enabled" type="checkbox" checked style="accent-color:var(--foreground);"> Enlace activo para el cliente
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="p-notes" class="form-label">Notas internas</label>
                                <textarea name="notes" id="p-notes" class="form-control" rows="3" maxlength="10000"></textarea>
                                <span class="help-block">Solo visibles para el superadministrador.</span>
                            </div>
                            <button id="save-project" class="btn btn-primary" type="submit">Guardar proyecto</button>
                        </form>
                        <div id="invitation-controls" style="margin-top:24px" hidden>
                            <hr style="border:none; border-top:1px solid var(--border); margin:18px 0;">
                            <label for="invitation-url" class="form-label">Enlace privado de onboarding</label>
                            <div style="display:flex; gap:8px; margin-bottom:8px;">
                                <input id="invitation-url" readonly class="form-control" style="flex:1;">
                                <button id="copy-invitation" class="btn btn-outline" type="button">Copiar</button>
                            </div>
                            <p class="help-block">Quien tenga este enlace puede completar el formulario. Compártelo solo con el cliente.</p>
                            <p id="invitation-state" class="text-muted"></p>
                            <div style="display:flex; gap:8px;">
                                <button id="rotate-invitation" class="btn btn-outline" type="button">Renovar enlace</button>
                                <button id="reopen-onboarding" class="btn btn-outline" type="button" hidden>Reabrir para correcciones</button>
                            </div>
                        </div>
                    </div>
                    <div id="project-answers" class="tab-pane" style="display:none;"><p id="submission-info"></p><div id="answer-sections"></div></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>window.onboardingSections = @json(config('onboarding.sections'));</script>
<script src="{{ asset('js/admin-projects.js') }}?v={{ filemtime(public_path('js/admin-projects.js')) }}"></script>
@endpush
