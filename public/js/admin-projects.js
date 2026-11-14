$(function () {
    'use strict';
    var base = $('#projects-admin').data('url'), id = null, busy = false;
    var modal = $('#project-modal'), form = $('#project-form');
    var notice = Swal.mixin({ background: '#262626', color: '#ededed', confirmButtonColor: '#111', cancelButtonColor: '#555' });
    function message(xhr) {
        if (xhr.status === 419 || xhr.status === 401) return 'Tu sesión expiró. Recarga la página e inicia sesión.';
        return Object.values(xhr.responseJSON?.errors || {}).flat().join('\n') || xhr.responseJSON?.message || 'No se pudo completar la operación. Intenta nuevamente.';
    }
    function request(path, method, data) {
        return $.ajax({ url: base + path, method: method || 'GET', contentType: 'application/json', dataType: 'json',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, data: data ? JSON.stringify(data) : undefined });
    }
    var columns = ['id', 'name', 'company', 'contact_name', 'status_label', 'onboarding_state', 'link_state', 'created_at'].map(function (key) {
        return { data: key, name: key === 'status_label' ? 'status' : key,
            orderable: !['onboarding_state', 'link_state'].includes(key), searchable: !['status_label', 'onboarding_state', 'link_state', 'created_at'].includes(key),
            render: function (value, type) {
                if (type !== 'display') return value;
                var text = new DOMParser().parseFromString(String(value ?? ''), 'text/html').documentElement.textContent;
                return $('<span>').text(text).html();
            } };
    });
    columns.push({ data: null, orderable: false, searchable: false, render: function () { return '<button class="btn btn-outline btn-sm open-project" style="padding:4px 10px; font-size:12px;">Gestionar</button>'; } });
    var table = $('#projects-table').DataTable({ processing: true, serverSide: true, searchDelay: 350, pageLength: 25, order: [[0, 'desc']], columns: columns,
        ajax: { url: base, headers: { Accept: 'application/json' }, data: function (data) { data.status = $('#project-filter').val(); },
            error: function (xhr) { $('#project-list-error').text(message(xhr)).prop('hidden', false); } },
        language: { search: 'Buscar:', lengthMenu: 'Mostrar _MENU_', info: '_START_–_END_ de _TOTAL_ proyectos', infoEmpty: 'Sin proyectos',
            infoFiltered: '(de _MAX_ en total)', emptyTable: 'Crea tu primer proyecto para compartir un onboarding.', zeroRecords: 'Sin resultados', processing: 'Cargando…',
            paginate: { next: 'Siguiente', previous: 'Anterior', first: 'Primero', last: 'Último' } } });
    table.on('xhr', function (e, settings, json) { if (json) $('#project-list-error').prop('hidden', true); });
    $('#project-filter').on('change', function () { table.ajax.reload(); });
    function renderAnswers(record) {
        var answers = record.answers || {}, container = $('#answer-sections').empty();
        $('#submission-info').text(record.submitted_at ? 'Información enviada: ' + new Date(record.submitted_at).toLocaleString('es-MX') : (record.draft_saved_at ? 'Borrador en progreso; el cliente aún no lo envía.' : 'El cliente todavía no ha guardado respuestas.'));
        window.onboardingSections.forEach(function (section) {
            var block = $('<section class="box box-default">'), body = $('<div class="box-body">');
            block.append($('<div class="box-header with-border">').append($('<h4 class="box-title">').text(section.title)));
            section.fields.forEach(function (field) {
                if (field.type === 'files') {
                    (record.attachments || []).filter(function (file) { return file.category === field.key; }).forEach(function (file) {
                        body.append($('<p>').append($('<a>').attr('href', file.url).text(file.original_name + ' · ' + Math.ceil(file.size / 1024) + ' KB')));
                    });
                    return;
                }
                var value = answers[field.key];
                if (value == null || value === '' || (Array.isArray(value) && !value.length)) return;
                body.append($('<strong>').text(field.label), $('<p>').css('white-space', 'pre-wrap').text(Array.isArray(value) ? value.join('\n') : value));
            });
            if (!body.children().length) body.append($('<p class="text-muted">').text('Sin respuestas en esta sección.'));
            container.append(block.append(body));
        });
    }
    function open(record) {
        form[0].reset(); id = record?.id || null;
        $('#project-errors').prop('hidden', true);
        $('#project-modal-title').text(id ? record.name : 'Nuevo proyecto');
        ['name', 'company', 'contact_name', 'contact_email', 'status', 'notes'].forEach(function (key) { $('#p-' + key).val(record?.[key] || (key === 'status' ? 'new' : '')); });
        $('#p-expires_at').val(record?.expires_at?.slice(0, 10) || '');
        $('#p-link_enabled').prop('checked', record ? record.link_enabled : true);
        $('#answers-tab, #invitation-controls').prop('hidden', !id);
        $('#invitation-url').val(record?.url || '');
        $('#reopen-onboarding').prop('hidden', !record?.submitted_at);
        $('#invitation-state').text(record ? (!record.link_enabled ? 'Enlace desactivado.' : record.expires_at && new Date(record.expires_at) < new Date() ? 'Enlace vencido.' : 'Enlace activo.') : '');
        $('.nav-tabs a[href="#project-settings"]').tab('show');
        if (record) renderAnswers(record);
        modal.modal('show');
    }
    function fail(xhr) { notice.fire({ icon: 'error', title: 'No se pudo completar', text: message(xhr) }); }
    $('#new-project').on('click', function () { open(null); });
    $('#projects-table').on('click', '.open-project', function () {
        var button = $(this).prop('disabled', true), record = table.row(button.closest('tr')).data();
        request('/' + record.id).done(open).fail(fail).always(function () { button.prop('disabled', false); });
    });
    modal.on('hide.bs.modal', function (event) { if (busy) event.preventDefault(); });
    form.on('submit', function (event) {
        event.preventDefault(); if (busy) return;
        var data = {}; form.serializeArray().forEach(function (item) { data[item.name] = item.value; });
        data.link_enabled = $('#p-link_enabled').prop('checked'); data.expires_at = data.expires_at || null;
        busy = true; $('#save-project').prop('disabled', true).text('Guardando…');
        request(id ? '/' + id : '', id ? 'PUT' : 'POST', data).done(function (result) {
            table.ajax.reload(null, false);
            request('/' + (id || result.id)).done(open).fail(fail);
            notice.fire({ icon: 'success', title: result.message, timer: 1800, showConfirmButton: false });
        }).fail(function (xhr) { $('#project-errors').text(message(xhr)).prop('hidden', false).trigger('focus'); })
            .always(function () { busy = false; $('#save-project').prop('disabled', false).text('Guardar proyecto'); });
    });
    $('#copy-invitation').on('click', async function () {
        var input = $('#invitation-url')[0];
        try { await navigator.clipboard.writeText(input.value); $(this).text('Copiado'); setTimeout(() => $(this).text('Copiar'), 1800); }
        catch (_) { input.focus(); input.select(); notice.fire({ title: 'Copia el enlace seleccionado', text: 'Presiona Ctrl+C o mantén presionado para copiar.' }); }
    });
    function action(path, title, text) {
        if (busy || !id) return;
        notice.fire({ icon: 'question', title: title, text: text, showCancelButton: true, confirmButtonText: 'Continuar', cancelButtonText: 'Cancelar' }).then(function (result) {
            if (!result.isConfirmed) return;
            busy = true;
            request('/' + id + '/' + path, 'POST').done(function () { request('/' + id).done(open).fail(fail); table.ajax.reload(null, false); }).fail(fail).always(function () { busy = false; });
        });
    }
    $('#rotate-invitation').on('click', function () { action('rotate', '¿Renovar enlace?', 'El enlace anterior dejará de funcionar. Las respuestas y archivos se conservan.'); });
    $('#reopen-onboarding').on('click', function () { action('reopen', '¿Permitir correcciones?', 'El cliente podrá modificar sus respuestas y volver a enviarlas.'); });
});
