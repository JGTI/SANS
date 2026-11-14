/* AdminLTE 2 / Bootstrap 3: server-paginated lists and JSON mutations. */
$(function () {
    'use strict';
    var root = $('#admin-crud');
    if (!root.length) return;
    var users = root.data('kind') === 'users';
    var base = root.data('url');
    var editingId = null;
    var saving = false;
    var form = $('#record-form');
    var modal = $('#record-modal');
    var saveButton = $('#save-record');
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var saveLabel = saveButton.text();
    var notice = Swal.mixin({ background: '#09090b', color: '#fafafa', confirmButtonColor: '#ffffff', cancelButtonColor: '#27272a' });

    function errorMessage(xhr) {
        if (xhr.status === 401 || xhr.status === 419) return 'Tu sesión expiró. Recarga la página e inicia sesión nuevamente.';
        if (xhr.status === 403) return 'No tienes permiso para realizar esta acción.';
        if (xhr.status === 404) return 'El registro ya no existe. Actualiza el listado.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
            return Object.values(xhr.responseJSON.errors).flat().join('\n');
        }
        return 'No se pudo completar la operación. Revisa la conexión e inténtalo de nuevo.';
    }

    function request(url, method, data) {
        return $.ajax({ url: url, method: method, contentType: 'application/json', dataType: 'json',
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            data: data ? JSON.stringify(data) : undefined });
    }

    function actions(value, type, row) {
        if (type !== 'display') return '';
        if (!users && row.protected) return '<span class="text-muted" style="font-size:12px;"><i class="fa fa-lock" aria-hidden="true"></i> Protegido</span>';
        var edit = !users || row.can_edit;
        var remove = users ? row.can_delete : Number(row.users_count) === 0;
        return '<div style="display:inline-flex; gap:6px;">' +
            (edit ? '<button type="button" class="btn btn-outline btn-sm edit-record" style="padding:4px 10px; font-size:12px;">Editar</button>' : '') +
            (remove ? '<button type="button" class="btn btn-outline btn-sm delete-record" style="padding:4px 10px; font-size:12px; border-color:var(--border);">Eliminar</button>' : '') +
            (!edit ? '<span class="text-muted" style="font-size:12px;">Solo superadministrador</span>' : '') +
            '</div>';
    }

    var columns = users ? [
        { data: 'id', name: 'id' }, { data: 'name', name: 'name' }, { data: 'email', name: 'email' },
        { data: 'role_names', name: 'roles.name', orderable: false },
        { data: 'created_at', name: 'created_at', searchable: false },
        { data: null, orderable: false, searchable: false, render: actions }
    ] : [
        { data: 'id', name: 'id' }, { data: 'name', name: 'name' },
        { data: 'permission_names', searchable: false, orderable: false },
        { data: 'users_count', searchable: false },
        { data: null, orderable: false, searchable: false, render: actions }
    ];
    // Yajra escapes data on the server; decode then render as text to avoid double escaping.
    columns.forEach(function (column) {
        if (!column.render) column.render = function (value, type) {
            if (type !== 'display') return value;
            var decoded = new DOMParser().parseFromString(String(value == null ? '' : value), 'text/html').documentElement.textContent;
            return $('<span>').text(decoded).html();
        };
    });

    var table = $('#records-table').DataTable({
        processing: true, serverSide: true, pageLength: 25, searchDelay: 350, order: [[0, 'desc']],
        ajax: { url: base, headers: { Accept: 'application/json' },
            error: function (xhr) { $('#list-error').text(errorMessage(xhr)).prop('hidden', false); } },
        columns: columns,
        language: { search: 'Buscar:', lengthMenu: 'Mostrar _MENU_ registros', info: '_START_–_END_ de _TOTAL_ registros',
            infoEmpty: 'Sin registros', infoFiltered: '(de _MAX_ en total)', zeroRecords: 'No se encontraron resultados',
            emptyTable: 'No hay registros', processing: 'Cargando…', loadingRecords: 'Cargando…',
            paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' } }
    });
    table.on('xhr', function (event, settings, json) { if (json) $('#list-error').prop('hidden', true); });

    if (users) $('#record-roles').select2({ width: '100%', dropdownParent: modal, placeholder: 'Selecciona los roles' });

    function openForm(record) {
        form[0].reset();
        editingId = record ? record.id : null;
        $('#form-errors').text('').prop('hidden', true);
        $('#record-title').text((record ? 'Editar ' : 'Nuevo ') + (users ? 'usuario' : 'rol'));
        $('#record-name').val(record ? record.name : '');
        if (users) {
            $('#record-email').val(record ? record.email : '');
            $('#record-password, #record-password-confirmation').prop('required', !record);
            $('#record-roles option').each(function () {
                $(this).prop('disabled', !window.nexusSuperAdmin && $(this).data('privileged') === 1 && !(record && record.is_self));
            });
            $('#record-roles').prop('disabled', !!(record && record.is_self)).val(record ? record.roles.map(String) : []).trigger('change');
        } else {
            form.find('[name="permissions[]"]').each(function () { this.checked = !!record && record.permissions.includes(this.value); });
        }
        modal.modal('show');
    }

    modal.on('shown.bs.modal', function () { $('#record-name').trigger('focus'); });
    modal.on('hide.bs.modal', function (event) { if (saving) event.preventDefault(); });
    $('#create-record').on('click', function () { openForm(null); });

    $('#records-table').on('click', '.edit-record', function () {
        var button = $(this);
        var record = table.row(button.closest('tr')).data();
        button.prop('disabled', true);
        request(base + '/' + record.id, 'GET').done(openForm)
            .fail(function (xhr) { notice.fire({ icon: 'error', title: 'No se pudo abrir', text: errorMessage(xhr) }); })
            .always(function () { button.prop('disabled', false); });
    });

    form.on('submit', function (event) {
        event.preventDefault();
        if (saving) return;
        var data = { name: $('#record-name').val() };
        if (users) {
            data.email = $('#record-email').val();
            data.roles = ($('#record-roles').val() || []).map(Number);
            data.password = $('#record-password').val();
            data.password_confirmation = $('#record-password-confirmation').val();
        } else {
            data.permissions = form.find('[name="permissions[]"]:checked').map(function () { return this.value; }).get();
        }
        saving = true;
        saveButton.prop('disabled', true).text('Guardando…');
        $('#form-errors').prop('hidden', true);
        request(base + (editingId ? '/' + editingId : ''), editingId ? 'PUT' : 'POST', data)
            .done(function (result) {
                saving = false;
                modal.modal('hide');
                table.ajax.reload(null, false);
                notice.fire({ icon: 'success', title: result.message, timer: 1800, showConfirmButton: false });
            })
            .fail(function (xhr) { $('#form-errors').text(errorMessage(xhr)).prop('hidden', false).trigger('focus'); })
            .always(function () { saving = false; saveButton.prop('disabled', false).text(saveLabel); });
    });

    $('#records-table').on('click', '.delete-record', function () {
        var button = $(this);
        var record = table.row(button.closest('tr')).data();
        var label = new DOMParser().parseFromString(record.name, 'text/html').documentElement.textContent;
        notice.fire({ icon: 'warning', title: '¿Eliminar ' + (users ? 'usuario' : 'rol') + '?',
            text: 'Se eliminará “' + label + '”. Esta acción no se puede deshacer.',
            showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar', focusCancel: true
        }).then(function (result) {
            if (!result.isConfirmed) return;
            button.prop('disabled', true);
            request(base + '/' + record.id, 'DELETE').done(function (response) {
                table.ajax.reload(null, false);
                notice.fire({ icon: 'success', title: response.message, timer: 1800, showConfirmButton: false });
            }).fail(function (xhr) {
                button.prop('disabled', false);
                notice.fire({ icon: 'error', title: 'No se pudo eliminar', text: errorMessage(xhr) });
            });
        });
    });
});
