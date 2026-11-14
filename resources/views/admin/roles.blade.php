@extends('layouts.app')

@section('title', 'Roles y Permisos · SPACE VISION')

@section('content')
<div class="page-heading">
    <div>
        <div class="eyebrow">Administración del Sistema</div>
        <h1>Roles y Permisos</h1>
        <p>Gestiona los niveles de acceso y los privilegios de usuarios en SPACE VISION.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" id="create-record">
            <i class="fa fa-plus" aria-hidden="true"></i> Nuevo Rol
        </button>
    </div>
</div>

<div id="admin-crud" data-kind="roles" data-url="{{ route('admin.roles.index') }}">
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Roles de Acceso</h2>
                <p class="card-description">Super-admin administra usuarios y roles; admin administra cuentas normales; user accede al panel. Los tres roles base están protegidos.</p>
            </div>
        </div>
        <div class="card-body">
            <div id="list-error" class="alert alert-danger" role="alert" hidden></div>
            <div class="table-responsive">
                <table id="records-table" class="table" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rol</th>
                            <th>Permisos</th>
                            <th>Usuarios</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal para crear/editar rol --}}
    <div class="modal fade" id="record-modal" tabindex="-1" role="dialog" aria-labelledby="record-title">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="record-form">
                    <div class="modal-header">
                        <h3 class="modal-title" id="record-title">Nuevo Rol</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="form-errors" class="alert alert-danger" role="alert" tabindex="-1" hidden></div>
                        <div class="form-group" style="margin-bottom:16px;">
                            <label for="record-name" class="form-label" style="font-weight:600; font-size:13px; display:block; margin-bottom:6px;">Nombre del rol</label>
                            <input id="record-name" name="name" class="form-control" required maxlength="80" pattern="[a-z0-9][a-z0-9_-]*" placeholder="Ejemplo: soporte" aria-describedby="name-help">
                            <p id="name-help" class="help-block">Letras minúsculas, números y guiones, sin espacios.</p>
                        </div>
                        <fieldset style="border:1px solid var(--border); border-radius:8px; padding:14px; margin-top:16px;">
                            <legend style="font-size:13px; font-weight:700; padding:0 8px;">Permisos</legend>
                            <p class="text-muted" style="font-size:12px; margin-bottom:10px;">Todos los roles permiten iniciar sesión y acceder al panel.</p>
                            <div class="checkbox" style="display:flex; align-items:center; gap:8px;">
                                <input type="checkbox" id="perm-manage" name="permissions[]" value="users.manage" style="accent-color:var(--foreground);">
                                <label for="perm-manage" style="font-weight:500; font-size:13px; margin:0; cursor:pointer;">Administrar usuarios</label>
                            </div>
                            <p class="help-block" style="margin-top:6px;">Permite crear, editar y eliminar cuentas normales. La administración de roles está reservada al superadministrador.</p>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="save-record">Guardar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-crud.js') }}"></script>
@endpush
