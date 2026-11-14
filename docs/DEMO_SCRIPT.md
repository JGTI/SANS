# 🎬 SPACE VISION — DEMO SCRIPT PARA JUECES (3 MINUTOS)

## Minuto 0:00 - 0:45 · El Problema (Hook & Contexto)
- **Mensaje:** "Durante misiones prolongadas a la Luna o Marte, los astronautas pierden agudeza visual y experimentan hipermetropía progresiva por el síndrome SANS. Diseñamos SPACE VISION para monitorear y modelar este fenómeno antes de que comprometa la misión."
- **Acción:** Ingresar a `/login` con credenciales de Director de Vuelo (`admin@spacevision.nasa`).
- **Vista:** Mission Control Dashboard con indicadores de salud visual de la tripulación en tiempo real.

## Minuto 0:45 - 1:30 · Vision Lab & Telemetría Oftalmológica
- **Mensaje:** "En Vision Lab, registramos evaluaciones refractivas por ojo (esfera, cilindro, eje) y convertimos automáticamente la agudeza visual a notación científica LogMAR."
- **Acción:** Abrir una evaluación en curso, mostrar el cálculo matemático de Equivalente Esférico ($SE = SPH + CYL/2$).
- **Demostración de alerta:** Visualizar el desplazamiento hipermetrópico ($\Delta SE > +0.75\text{ D}$) detectado con explicación clínica inmediata.

## Minuto 1:30 - 2:15 · Gemelo Digital & Datos NASA
- **Mensaje:** "El Gemelo Digital del astronauta correlaciona la duración de la misión y telemetría ambiental espacial proveniente de APIs de NASA (como DONKI y reportes NTRS)."
- **Acción:** Navegar por la línea de tiempo del astronauta y mostrar la visualización de incertidumbre estadística.

## Minuto 2:15 - 3:00 · Gestión, Consola y Cierre
- **Mensaje:** "La plataforma cuenta con control de acceso por roles (Flight Surgeons, Investigadores, Tripulación) y una consola de telemetría de sistema que garantiza operabilidad continua tanto en órbita como en centros de control."
- **Acción:** Mostrar la pantalla de Gestión de Usuarios (`/admin/users`) y la Consola de Comandos (`/admin/maintenance`).
- **Frase final:** "SPACE VISION: Protegiendo la visión humana más allá de la Tierra."
