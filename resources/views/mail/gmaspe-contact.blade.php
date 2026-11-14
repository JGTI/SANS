<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Nueva solicitud de Grupo MASPE</title></head>
<body style="margin:0;padding:0;background:#f2f5f8;color:#132235;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:28px 12px;">
<table role="presentation" width="620" cellpadding="0" cellspacing="0" style="width:100%;max-width:620px;background:#fff;border:1px solid #dce3ea;">
    <tr><td style="padding:24px 30px;background:#091b32;color:#fff;"><strong style="font-size:18px;letter-spacing:.08em;">GRUPO MASPE</strong><div style="margin-top:6px;font-size:11px;color:#b8c8d9;">NUEVA SOLICITUD DESDE GMASPE.COM</div></td></tr>
    <tr><td style="padding:30px;">
        <h1 style="margin:0 0 24px;font-size:24px;">Solicitud de {{ $inquiry['name'] }}</h1>
        <p><strong>Correo:</strong> <a href="mailto:{{ $inquiry['email'] }}">{{ $inquiry['email'] }}</a></p>
        <p><strong>Empresa:</strong> {{ $inquiry['company'] ?? 'No indicada' }}</p>
        <p><strong>Teléfono:</strong> {{ $inquiry['phone'] ?? 'No indicado' }}</p>
        <p><strong>Servicio:</strong> {{ $inquiry['service'] ?? 'No indicado' }}</p>
        <p style="margin:24px 0 8px;"><strong>Mensaje:</strong></p>
        <div style="padding:18px;background:#f5f7f9;border-left:3px solid #315f8f;white-space:pre-line;line-height:1.6;">{{ $inquiry['message'] }}</div>
    </td></tr>
    <tr><td style="padding:18px 30px;border-top:1px solid #e5e9ed;font-size:11px;color:#6d7986;">Puedes responder directamente a este correo para contactar a la persona solicitante.</td></tr>
</table>
</td></tr></table>
</body>
</html>
