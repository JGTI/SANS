@extends('layouts.app')

@section('title', 'Usuarios · SPACE VISION')

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">Administración del Sistema</div>
        <h1>Usuarios del Sistema</h1>
        <p>Gestiona el acceso de tripulantes, investigadores, médicos y operadores en SPACE VISION.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" id="create-record">
            <i class="fa fa-plus" aria-hidden="true"></i> Nuevo Usuario
        </button>
    </div>
</div>

<div id="admin-crud" data-kind="users" data-url="{{ route('admin.users.index') }}" data-current-id="{{ auth()->id() }}">
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Cuentas y Credenciales</h2>
                <p class="card-description">Gestiona los permisos y roles de cada tripulante o especialista clínico.</p>
            </div>
        </div>
        <div class="card-body">
            <div id="list-error" class="alert alert-danger" role="alert" hidden></div>
            <div class="table-responsive">
                <table id="records-table" class="table" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Roles</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal para crear/editar usuario --}}
    <div class="modal fade" id="record-modal" tabindex="-1" role="dialog" aria-labelledby="record-title">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="record-form">
                    <div class="modal-header">
                        <h3 class="modal-title" id="record-title">Nuevo Usuario</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="form-errors" class="alert alert-danger" role="alert" tabindex="-1" hidden></div>

                        <div class="form-group" style="margin-bottom:16px;">
                            <label for="record-name" class="form-label" style="font-weight:600; font-size:13px; display:block; margin-bottom:6px;">Nombre Completo</label>
                            <input id="record-name" name="name" class="form-control" required maxlength="255" autocomplete="name" placeholder="Ej. Dr. Scott Kelly">
                        </div>

                        <div class="form-group" style="margin-bottom:16px;">
                            <label for="record-email" class="form-label" style="font-weight:600; font-size:13px; display:block; margin-bottom:6px;">Correo Electrónico</label>
                            <input id="record-email" name="email" type="email" class="form-control" required maxlength="255" autocomplete="off" placeholder="usuario@spacevision.nasa">
                        </div>

                        <div class="form-group" style="margin-bottom:16px;">
                            <label for="record-roles" class="form-label" style="font-weight:600; font-size:13px; display:block; margin-bottom:6px;">Roles Asignados</label>
                            <select id="record-roles" name="roles[]" multiple class="form-control" required aria-describedby="roles-help" style="width:100%;">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" data-privileged="{{ app(\App\Services\UserAdministration::class)->privilegedRole($role) ? '1' : '0' }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <p id="roles-help" class="help-block">Selecciona uno o varios roles. No puedes modificar tus propios roles.</p>
                        </div>

                        <div class="form-group" style="margin-bottom:16px;">
                            <label for="record-password" class="form-label" style="font-weight:600; font-size:13px; display:block; margin-bottom:6px;">Contraseña</label>
                            <input id="record-password" name="password" type="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" aria-describedby="password-help" placeholder="••••••••">
                            <p id="password-help" class="help-block">Mínimo 8 caracteres. Al editar, déjala vacía para conservar la actual.</p>
                        </div>

                        <div class="form-group" style="margin-bottom:16px;">
                            <label for="record-password-confirmation" class="form-label" style="font-weight:600; font-size:13px; display:block; margin-bottom:6px;">Confirmar Contraseña</label>
                            <input id="record-password-confirmation" name="password_confirmation" type="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" placeholder="••••••••">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="save-record">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>window.nexusSuperAdmin = @json(auth()->user()->hasRole('super-admin'));</script>
<script src="{{ asset('js/admin-crud.js') }}"></script>
@endpush
