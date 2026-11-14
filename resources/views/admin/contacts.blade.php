@extends('layouts.app')

@section('title', 'Mensajes de Contacto · SPACE VISION')

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">Administración</div>
        <h1>Mensajes de Contacto</h1>
        <p>Bandeja de solicitudes y comunicaciones recibidas a través del portal.</p>
    </div>
</div>

<div id="contact-inbox" data-url="{{ route('admin.contacts.index') }}" class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Bandeja de Entrada</h2>
            <p class="card-description">Solicitudes recibidas desde el formulario web.</p>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <label for="contact-filter" style="font-size:12px; font-weight:600; color:var(--muted-foreground); margin:0;">Estado:</label>
            <select id="contact-filter" class="form-control" style="width:160px; height:34px; font-size:12px;">
                <option value="">Todos</option>
                <option value="new">Nuevo</option>
                <option value="in_progress">En seguimiento</option>
                <option value="closed">Cerrado</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div id="contact-error" class="alert alert-danger" role="alert" hidden></div>
        <div class="table-responsive">
            <table id="contact-table" class="table" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Empresa</th>
                        <th>Servicio</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="contact-modal" tabindex="-1" role="dialog" aria-labelledby="contact-title">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="contact-title">Mensaje de Contacto</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
            </div>
            <form id="contact-form">
                <div class="modal-body">
                    <div id="contact-save-error" class="alert alert-danger" role="alert" hidden></div>
                    <dl style="display:grid; grid-template-columns:140px 1fr; gap:8px 16px; margin:0 0 16px;">
                        <dt style="font-weight:600; color:var(--muted-foreground);">Nombre</dt>
                        <dd data-field="name" style="margin:0; font-weight:600;"></dd>
                        <dt style="font-weight:600; color:var(--muted-foreground);">Correo</dt>
                        <dd data-field="email" style="margin:0;"></dd>
                        <dt style="font-weight:600; color:var(--muted-foreground);">Teléfono</dt>
                        <dd data-field="phone" style="margin:0;"></dd>
                        <dt style="font-weight:600; color:var(--muted-foreground);">Empresa</dt>
                        <dd data-field="company" style="margin:0;"></dd>
                        <dt style="font-weight:600; color:var(--muted-foreground);">Servicio</dt>
                        <dd data-field="service" style="margin:0;"></dd>
                        <dt style="font-weight:600; color:var(--muted-foreground);">Recibido</dt>
                        <dd data-field="created_at" style="margin:0;"></dd>
                        <dt style="font-weight:600; color:var(--muted-foreground);">Notificación</dt>
                        <dd id="contact-notification" style="margin:0;"></dd>
                        <dt style="font-weight:600; color:var(--muted-foreground);">Mensaje</dt>
                        <dd data-field="message" style="margin:0; white-space:pre-wrap; overflow-wrap:anywhere; background:var(--muted); padding:10px; border-radius:6px;"></dd>
                    </dl>
                    <div class="form-group" style="margin-top:16px;">
                        <label for="contact-status" class="form-label" style="font-weight:600; font-size:13px; display:block; margin-bottom:6px;">Estado de atención</label>
                        <select id="contact-status" class="form-control">
                            <option value="new">Nuevo</option>
                            <option value="in_progress">En seguimiento</option>
                            <option value="closed">Cerrado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-dismiss="modal">Cerrar</button>
                    <button id="contact-save" type="submit" class="btn btn-primary">Guardar estado</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-contacts.js') }}"></script>
@endpush
