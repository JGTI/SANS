$(function () {
    'use strict';
    var base = $('#contact-inbox').data('url');
    var currentId = null;
    var saving = false;
    var labels = { new: 'Nuevo', in_progress: 'En seguimiento', closed: 'Cerrado' };
    function error(xhr) {
        if (xhr.status === 401 || xhr.status === 419) return 'Tu sesión expiró. Recarga la página e inicia sesión.';
        if (xhr.status === 403) return 'No tienes permiso para consultar estos mensajes.';
        return 'No fue posible completar la operación. Intenta nuevamente.';
    }
    var table = $('#contact-table').DataTable({
        processing: true, serverSide: true, pageLength: 25, searchDelay: 350, order: [[0, 'desc']],
        ajax: { url: base, headers: { Accept: 'application/json' }, data: function (data) { data.status = $('#contact-filter').val(); },
            error: function (xhr) { $('#contact-error').text(error(xhr)).prop('hidden', false); } },
        columns: [{ data: 'id' }, { data: 'name' }, { data: 'email' }, { data: 'company', defaultContent: '—' },
            { data: 'service', defaultContent: '—' }, { data: 'status', render: function (value) { return labels[value] || '—'; } },
            { data: 'created_at', searchable: false },
            { data: null, searchable: false, orderable: false, render: function () { return '<button type="button" class="btn btn-outline btn-sm view-contact" style="padding:4px 10px; font-size:12px;">Ver mensaje</button>'; } }],
        language: { search: 'Buscar:', lengthMenu: 'Mostrar _MENU_ registros', info: '_START_–_END_ de _TOTAL_ mensajes', infoEmpty: 'Sin mensajes',
            infoFiltered: '(de _MAX_ en total)', zeroRecords: 'Sin resultados', emptyTable: 'Todavía no hay mensajes de contacto.', processing: 'Cargando…',
            paginate: { next: 'Siguiente', previous: 'Anterior' } }
    });
    table.on('xhr', function (event, settings, json) { if (json) $('#contact-error').prop('hidden', true); });
    $('#contact-filter').on('change', function () { table.ajax.reload(); });
    $('#contact-table').on('click', '.view-contact', function () {
        var button = $(this);
        var id = table.row(button.closest('tr')).data().id;
        button.prop('disabled', true);
        $.ajax({ url: base + '/' + id, dataType: 'json', headers: { Accept: 'application/json' } }).done(function (record) {
            currentId = record.id;
            $('#contact-modal [data-field]').each(function () {
                var field = $(this).data('field');
                $(this).text(field === 'created_at' ? new Date(record[field]).toLocaleString('es-MX') : (record[field] || '—'));
            });
            $('#contact-notification').text(record.email_sent_at ? 'Enviada' : 'No enviada; el mensaje está guardado en esta bandeja.');
            $('#contact-status').val(record.status);
            $('#contact-save-error').prop('hidden', true);
            $('#contact-modal').modal('show');
        }).fail(function (xhr) { $('#contact-error').text(error(xhr)).prop('hidden', false); })
            .always(function () { button.prop('disabled', false); });
    });
    $('#contact-modal').on('hide.bs.modal', function (event) { if (saving) event.preventDefault(); });
    $('#contact-form').on('submit', function (event) {
        event.preventDefault();
        if (saving || !currentId) return;
        saving = true;
        $('#contact-save').prop('disabled', true).text('Guardando…');
        $('#contact-save-error').prop('hidden', true);
        $.ajax({ url: base + '/' + currentId, method: 'PATCH', contentType: 'application/json', dataType: 'json',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: JSON.stringify({ status: $('#contact-status').val() })
        }).done(function () {
            saving = false;
            $('#contact-modal').modal('hide');
            table.ajax.reload(null, false);
        }).fail(function (xhr) { $('#contact-save-error').text(error(xhr)).prop('hidden', false); })
            .always(function () { saving = false; $('#contact-save').prop('disabled', false).text('Guardar estado'); });
    });
});
