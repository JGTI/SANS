<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Información recibida · NEXUS DIGITAL DB</title></head>
<body style="margin:0;padding:0;background-color:#f1f2f4;color:#202124;font-family:Arial,Helvetica,sans-serif;">
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">Recibimos la información de {{ $details['project'] }}. Este es el siguiente paso.</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f2f4;"><tr><td align="center" style="padding:32px 12px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;background-color:#ffffff;border:1px solid #e0e2e5;border-radius:16px;">
    <tr><td style="padding:24px 32px;border-bottom:1px solid #eceef0;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
            <td width="84"><img src="{{ $message->embed(resource_path('images/mail/logo-black.png')) }}" width="72" height="72" alt="NEXUS" style="display:block;border:0;background-color:#ffffff;"></td>
            <td style="font-size:14px;font-weight:bold;letter-spacing:2px;color:#151515;">NEXUS DIGITAL DB<br><span style="font-size:10px;font-weight:normal;letter-spacing:1px;color:#70757a;">DE TU IDEA AL SIGUIENTE PASO</span></td>
        </tr></table>
    </td></tr>
    <tr><td style="padding:32px 32px 12px;">
        <span style="display:inline-block;padding:7px 12px;background-color:#eaf4ee;color:#276443;border-radius:20px;font-size:11px;font-weight:bold;letter-spacing:1px;">INFORMACIÓN RECIBIDA</span>
        <h1 style="margin:20px 0 16px;font-size:30px;line-height:1.2;letter-spacing:-1px;color:#161616;">{{ $internal ? 'Un proyecto listo para revisar.' : 'Tu idea ya tiene un punto de partida.' }}</h1>
        @if ($internal)
            <p style="font-size:15px;line-height:1.75;color:#555;">{{ $details['contact'] }} completó el formulario de {{ $details['company'] }}. Las respuestas ya están disponibles en el panel de Proyectos y onboarding.</p>
        @else
            <p style="font-size:15px;line-height:1.75;color:#555;">Hola, {{ $details['contact'] }}.</p>
            <p style="font-size:15px;line-height:1.75;color:#555;">Gracias por compartir lo que quieres construir. <strong style="color:#202124;">La información de tu proyecto quedó recibida y guardada.</strong> Nos ayudará a entender tus prioridades y preparar una conversación enfocada en lo que necesitas.</p>
        @endif
    </td></tr>
    <tr><td style="padding:12px 32px 24px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f6f7f8;border-left:3px solid #202124;"><tr><td style="padding:20px;">
            <p style="margin:0 0 8px;font-size:10px;letter-spacing:2px;color:#777;">TU PROYECTO · {{ $details['reference'] }}</p>
            <p style="margin:0 0 6px;font-size:19px;line-height:1.4;font-weight:bold;overflow-wrap:anywhere;">{{ $details['project'] }}</p>
            <p style="margin:0;font-size:13px;line-height:1.5;color:#666;overflow-wrap:anywhere;">{{ $details['company'] }}</p>
        </td></tr></table>
    </td></tr>
    <tr><td style="padding:0 32px 28px;">
        <h2 style="font-size:18px;margin:0 0 20px;">¿Qué sigue ahora?</h2>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        @foreach ([['01', 'Entender tus prioridades', 'Revisaremos tus respuestas y el material que compartiste.'], ['02', 'Conectar los puntos', 'Identificaremos qué conviene aclarar y cuáles son las necesidades principales.'], ['03', 'Conversar sobre el siguiente paso', 'Nos pondremos en contacto contigo para alinear alcance y expectativas.']] as [$number, $title, $description])
            <tr><td width="40" valign="top" style="padding:0 0 20px;font-size:12px;font-weight:bold;color:#888;">{{ $number }}</td><td style="padding:0 0 20px;"><strong style="font-size:14px;">{{ $title }}</strong><p style="margin:5px 0 0;font-size:13px;line-height:1.6;color:#666;">{{ $description }}</p></td></tr>
        @endforeach
        </table>
        <p style="margin:0;padding-top:16px;border-top:1px solid #eceef0;font-size:13px;line-height:1.7;color:#666;">{{ $internal ? 'Ingresa al panel administrativo para consultar el formulario. Puedes responder a este correo para contactar al cliente.' : '¿Te faltó contarnos algo? Comunícate con tu contacto de NEXUS e incluye la referencia de este correo. No necesitas llenar de nuevo el formulario.' }}</p>
    </td></tr>
    <tr><td style="padding:22px 32px;background-color:#181818;color:#ffffff;"><strong style="font-size:13px;">Gracias por dar este primer paso con nosotros.</strong><p style="margin:7px 0 0;font-size:11px;line-height:1.6;color:#bdbdbd;">NEXUS DIGITAL DB · Ingeniería de software alrededor de tu empresa.</p></td></tr>
</table>
<p style="max-width:520px;margin:20px 16px 0;color:#858585;font-size:11px;line-height:1.6;">Confirmación automática de recepción. El alcance, los tiempos y el inicio del proyecto se acordarán contigo.</p>
</td></tr></table>
</body></html>
