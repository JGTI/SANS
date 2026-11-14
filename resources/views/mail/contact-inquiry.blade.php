<!doctype html>
<html lang="es">
<body style="font-family:Arial,sans-serif;color:#171717;line-height:1.5">
    <h1 style="font-size:20px">Nuevo contacto desde NEXUS DIGITAL DB</h1>
    <p><strong>Nombre:</strong> {{ $inquiry['name'] }}</p>
    <p><strong>Correo:</strong> {{ $inquiry['email'] }}</p>
    <p><strong>Empresa:</strong> {{ $inquiry['company'] ?? 'No indicado' }}</p>
    <p><strong>Teléfono:</strong> {{ $inquiry['phone'] ?? 'No indicado' }}</p>
    <p><strong>Servicio:</strong> {{ $inquiry['service'] ?? 'No indicado' }}</p>
    <p><strong>Mensaje:</strong></p>
    <p style="white-space:pre-line">{{ $inquiry['message'] }}</p>
</body>
</html>
