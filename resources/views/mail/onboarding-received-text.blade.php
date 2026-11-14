NEXUS DIGITAL DB — Información recibida

Hola, {{ $internal ? 'equipo' : $details['contact'] }}.

Recibimos y guardamos el formulario de {{ $details['project'] }}.
Empresa: {{ $details['company'] }}
Referencia: {{ $details['reference'] }}

¿Qué sigue?
1. Revisaremos las respuestas y el material compartido.
2. Identificaremos las prioridades y los puntos por aclarar.
3. Nos pondremos en contacto para alinear alcance y expectativas.

@if ($internal)
Consulta las respuestas en el panel de Proyectos y onboarding.
Puedes responder a este correo para contactar al cliente.
@else
Si te faltó contarnos algo, comunícate con tu contacto de NEXUS e incluye esta referencia. No necesitas llenar de nuevo el formulario.
@endif

Gracias por dar este primer paso con nosotros.
Confirmación automática de recepción. El alcance, los tiempos y el inicio del proyecto se acordarán contigo.
