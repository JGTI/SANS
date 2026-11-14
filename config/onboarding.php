<?php

// Shared questionnaire: validation, client form and administrative review.
$yes = ['Sí', 'No', 'No estoy seguro'];
$field = static fn (string $key, string $label, string $type = 'text', array $extra = []) =>
    ['key' => $key, 'label' => $label, 'type' => $type] + $extra +
    ['maxLength' => $type === 'textarea' ? 5000 : ($type === 'links' ? 2000 : 500), 'maxLinks' => 5];
$choice = static fn (string $key, string $label, array $options, array $extra = []) =>
    $field($key, $label, 'choice', ['options' => $options] + $extra);
$multi = static fn (string $key, string $label, array $options, array $extra = []) =>
    $field($key, $label, 'multi', ['options' => $options] + $extra);
$when = static fn (string $key, string $value = 'Sí') => ['when' => [$key, $value]];

return [
    'statuses' => ['new' => 'Nuevo', 'review' => 'En revisión', 'contacted' => 'Contactado',
        'proposal' => 'Propuesta enviada', 'approved' => 'Aprobado', 'closed' => 'No continuó'],
    'max_files' => 20,
    'max_file_kb' => 10240,
    'sections' => [
        ['title' => 'Sobre ti y tu empresa', 'intro' => 'Empecemos por conocernos.', 'fields' => [
            $field('company', 'Nombre de la empresa o proyecto', 'text', ['required' => true]),
            $field('contact_name', 'Nombre de la persona de contacto', 'text', ['required' => true]),
            $field('contact_email', 'Correo electrónico', 'email', ['required' => true]),
            $field('phone', 'Teléfono o WhatsApp', 'text', ['help' => 'Opcional. Puedes incluir +52, espacios, guiones o extensión. Si no tienes o no lo utilizas, puedes escribirlo o dejarlo vacío.']),
            $field('business_description', '¿A qué se dedica tu empresa?', 'textarea', ['help' => 'Cuéntanos qué hacen, qué venden o qué servicio ofrecen.']),
            $field('business_locations', '¿En dónde trabajan o venden?', 'text', ['help' => 'Por ejemplo: Querétaro, todo México o Estados Unidos.']),
        ]],
        ['title' => 'Cuéntanos qué necesitas', 'intro' => 'No necesitas saber cómo construirlo. Cuéntanos qué te gustaría mejorar.', 'fields' => [
            $multi('project_types', '¿Qué tipo de proyecto tienes en mente?', ['Página o sitio para mi empresa', 'Sistema para trabajar mejor dentro de mi empresa', 'Portal para clientes o proveedores', 'Tienda en línea', 'Aplicación móvil', 'Dashboard o panel de información', 'Automatizar un proceso', 'Conectar sistemas que ya utilizamos', 'Tengo una idea nueva y necesito ayuda para definirla', 'Otro'], ['required' => true]),
            $field('project_type_other', 'Cuéntanos qué tienes en mente', 'text', $when('project_types', 'Otro')),
            $field('main_problem', '¿Qué problema quieres resolver?', 'textarea', ['required' => true, 'help' => 'Explícanos qué sucede hoy y qué te gustaría mejorar.']),
            $field('current_process', '¿Cómo hacen actualmente ese proceso?', 'textarea', ['help' => 'Usamos Excel, WhatsApp, llenamos formatos o lo hacemos manualmente…']),
            $field('pain_points', '¿Qué les genera más tiempo, trabajo o problemas?', 'textarea'),
            $field('goal_one', 'Si resolviéramos tres cosas: ¿cuál sería la más importante?', 'text', ['required' => true]),
            $field('goal_two', 'Segundo punto'), $field('goal_three', 'Tercer punto'),
            $field('success_criteria', '¿Cómo sabrías que el proyecto fue un éxito?', 'textarea', ['help' => 'Ahorrar tiempo, vender más, reducir errores o consultar información más rápido.']),
        ]],
        ['title' => 'Las personas que lo usarán', 'intro' => 'Pensemos en su día a día.', 'fields' => [
            $multi('user_types', '¿Quiénes van a utilizar la solución?', ['Clientes', 'Empleados', 'Administradores', 'Vendedores', 'Proveedores', 'Directivos', 'Público en general', 'Otro']),
            $choice('different_permissions', '¿Habrá diferentes tipos de usuarios con diferentes permisos?', $yes),
            $field('user_permissions', '¿Qué debería poder hacer cada persona?', 'textarea', $when('different_permissions') + ['help' => 'Un vendedor registra clientes, pero solamente el gerente aprueba descuentos.']),
            $choice('user_count', '¿Cuántas personas la utilizarán aproximadamente?', ['1 a 10', '11 a 50', '51 a 200', 'Más de 200', 'No lo sabemos todavía']),
            $choice('devices', '¿Desde dónde la utilizarán principalmente?', ['Computadora', 'Celular', 'Tablet', 'Todos', 'No estoy seguro']),
        ]],
        ['title' => 'Lo que debe poder hacer', 'intro' => 'Primero lo esencial. Después podemos crecer.', 'fields' => [
            $multi('features', '¿Qué necesitas que pueda hacer?', ['Iniciar sesión', 'Manejar diferentes tipos de usuarios', 'Registrar clientes', 'Registrar productos o servicios', 'Capturar información mediante formularios', 'Subir documentos, fotografías o archivos', 'Consultar información', 'Buscar y filtrar información', 'Mostrar gráficas o indicadores', 'Generar reportes', 'Descargar Excel', 'Generar PDF o documentos', 'Enviar correos', 'Enviar mensajes o notificaciones', 'Usar WhatsApp', 'Agendar citas o servicios', 'Recibir pagos', 'Manejar inventario', 'Aprobar solicitudes', 'Llevar historial de movimientos', 'Usar ubicación o mapas', 'Firma o aceptación de documentos', 'Otra']),
            $field('feature_other', '¿Qué otra función necesitas?', 'textarea', $when('features', 'Otra')),
            $multi('required_features', '¿Cuáles son indispensables para la primera versión?', [], ['options_from' => 'features']),
            $multi('optional_features', '¿Cuáles podrían esperar a una segunda etapa?', [], ['options_from' => 'features']),
            $field('payments', '¿Qué cobrarían y cómo prefieren recibir los pagos?', 'text', $when('features', 'Recibir pagos')),
        ]],
        ['title' => 'Información y herramientas', 'intro' => 'Aprovechemos lo que ya funciona. No compartas contraseñas ni accesos.', 'fields' => [
            $multi('current_tools', '¿Qué herramientas utilizan actualmente?', ['Excel', 'Google Sheets', 'WhatsApp', 'Correo', 'Otro sistema', 'Sistema de seguimiento de clientes', 'Sistema de administración de la empresa', 'Software contable', 'Formularios en papel', 'No utilizamos ninguna herramienta', 'Otro']),
            $choice('migration_required', '¿Hay información que necesitemos pasar al nuevo proyecto?', $yes),
            $multi('migration_sources', '¿En dónde está esa información?', ['Excel', 'Otro sistema', 'Archivos de datos', 'Documentos', 'PDFs', 'Formularios físicos', 'Google Drive', 'Otro'], $when('migration_required')),
            $field('sample', 'Puedes subir un archivo de ejemplo', 'files', $when('migration_required')),
            $choice('integration_required', '¿Necesitamos conectar alguna herramienta que ya utilizan?', $yes),
            $field('integrations', '¿Con cuál?', 'textarea', $when('integration_required') + ['help' => 'Sistema contable, ventas, Google, Microsoft, WhatsApp, pagos o facturación. Si necesitamos acceso, lo pediremos por un medio seguro.']),
        ]],
        ['title' => 'Diseño e identidad', 'intro' => 'Hagamos que se sienta como tu marca.', 'fields' => [
            $choice('has_interface', '¿Habrá pantallas o una parte visual que las personas utilicen?', $yes),
            $choice('has_logo', '¿Ya tienen logotipo?', ['Sí', 'No'], ['interface' => true]),
            $field('logo', 'Sube tu logotipo', 'files', $when('has_logo') + ['interface' => true, 'help' => 'Imagen, SVG o PDF.']),
            $choice('has_brand_guide', '¿Tienen una guía de marca?', ['Sí', 'No'], ['interface' => true]),
            $field('brand_guide', 'Sube tu guía de marca', 'files', $when('has_brand_guide') + ['interface' => true]),
            $field('brand_colors', '¿Qué colores quieres que utilicemos?', 'text', ['interface' => true, 'help' => 'Escribe varios nombres o códigos de color, separados por comas.']),
            $field('forbidden_colors', '¿Qué colores NO quieres?', 'text', ['interface' => true]),
            $choice('has_typography', '¿Tu empresa utiliza algún tipo de letra?', $yes, ['interface' => true]),
            $field('typography', '¿Cuál?', 'text', $when('has_typography') + ['interface' => true]),
            $choice('typography_style', '¿Qué estilo de letra prefieres?', ['Moderna y limpia', 'Elegante', 'Seria y corporativa', 'Tecnológica', 'Clásica', 'Creativa', 'No tengo preferencia, quiero una propuesta'], ['interface' => true]),
            $field('forbidden_typography', '¿Qué letras o estilos no te gustan?', 'text', ['interface' => true]),
            $multi('preferred_styles', '¿Qué estilo visual te gustaría? Elige hasta 3.', ['Corporativo', 'Premium', 'Minimalista', 'Moderno', 'Tecnológico', 'Elegante', 'Industrial', 'Creativo', 'Sobrio', 'Cercano', 'Juvenil', 'Otro'], ['interface' => true, 'max' => 3]),
            $choice('appearance', '¿Qué apariencia prefieres?', ['Clara', 'Oscura', 'Combinación de ambas', 'No tengo preferencia'], ['interface' => true]),
            $field('references', 'Comparte hasta 5 enlaces que te gusten', 'links', ['interface' => true, 'help' => 'Opcional: si no tienes ejemplos, déjalo vacío. Una dirección por renglón. Para explicar algo o decir «no ocupo», usa el espacio de comentarios de abajo.']),
            $field('reference_notes', 'Comentarios sobre los enlaces o ejemplos', 'textarea', ['interface' => true, 'help' => 'Puedes escribir «no tengo», «no ocupo» o describir una referencia aunque no conozcas su enlace.']),
            $field('reference_likes', '¿Qué te gusta de esos ejemplos?', 'textarea', ['interface' => true]),
            $field('forbidden_styles', '¿Hay algún estilo que definitivamente no quieras?', 'textarea', ['interface' => true]),
            $field('must_avoid', '¿Qué NO quieres ver en el resultado final?', 'textarea', ['interface' => true, 'highlight' => true, 'help' => 'Por ejemplo: degradados, demasiadas animaciones o una apariencia demasiado seria.']),
        ]],
        ['title' => 'Material que ya tienen', 'intro' => 'Comparte lo que tengas a la mano. Podemos completar lo demás juntos.', 'fields' => [
            $multi('materials', '¿Qué material puedes proporcionarnos?', ['Fotografías', 'Videos', 'Logotipo', 'Presentaciones', 'Catálogos', 'Brochures', 'PDFs', 'Certificaciones', 'Casos de éxito', 'Testimonios', 'Textos', 'Información de productos o servicios', 'Otro']),
            $field('materials_files', 'Sube tus archivos', 'files'),
            $choice('content_status', '¿Ya tienen los textos o información que aparecerá?', ['Sí, ya tenemos todo', 'Tenemos una parte', 'Necesitamos ayuda para mejorarlos', 'Necesitamos que nos ayuden a crearlos']),
            $choice('communication_tone', '¿Qué tono quieres para la comunicación?', ['Profesional', 'Cercano', 'Comercial', 'Técnico', 'Institucional', 'Premium', 'Casual', 'No estoy seguro']),
            $field('slogan', '¿Hay una frase, mensaje o slogan que debamos usar?'),
            $field('forbidden_words', '¿Qué palabras, temas o expresiones debemos evitar?'),
            $choice('material_permission', '¿Podemos utilizar públicamente este material?', ['Sí', 'No', 'Solo algunos archivos']),
            $field('material_permission_details', 'Indica cuáles podemos utilizar', 'textarea', $when('material_permission', 'Solo algunos archivos')),
        ]],
        ['title' => 'Presencia en Internet', 'intro' => 'Solo lo que tenga sentido para tu proyecto.', 'fields' => [
            $choice('public_site', '¿Tendrá información visible públicamente en Internet?', $yes),
            $multi('visitor_actions', '¿Qué quieres que haga una persona después de encontrarte?', ['Llamar', 'Mandar WhatsApp', 'Llenar un formulario', 'Comprar', 'Solicitar una cotización', 'Agendar', 'Conocer nuestros servicios', 'Descargar información', 'Otro'], $when('public_site')),
            $choice('seo_required', '¿Quieres aparecer en Google cuando busquen tus servicios?', $yes, $when('public_site')),
            $field('seo_services', '¿Qué productos o servicios te interesa que encuentren?', 'text', $when('seo_required') + ['public' => true]),
            $field('seo_locations', '¿En qué ciudades, estados o países?', 'text', $when('seo_required') + ['public' => true]),
            $multi('public_features', '¿Necesitas alguno de estos elementos?', ['Formulario de contacto', 'Botón de WhatsApp', 'Redes sociales', 'Mapa o ubicación', 'Blog o noticias', 'Testimonios', 'Casos de éxito', 'Preguntas frecuentes', 'Varios idiomas', 'Otro'], $when('public_site')),
        ]],
        ['title' => 'Situación actual', 'intro' => 'Lo que debemos tener en cuenta antes de empezar.', 'fields' => [
            $choice('has_domain', '¿Ya tienen una dirección de Internet?', $yes),
            $field('domain', 'Escríbela aquí', 'text', $when('has_domain')),
            $choice('has_hosting', '¿Ya pagan un servicio donde funciona su sitio o sistema?', $yes),
            $choice('has_existing_system', '¿Existe alguna página, sistema o aplicación que debamos conservar o tomar como referencia?', ['Sí', 'No']),
            $field('existing_system', 'Comparte el enlace o explícanos cuál es', 'textarea', $when('has_existing_system')),
            $field('restrictions', '¿Hay reglas o restricciones que debamos conocer?', 'textarea', ['help' => 'Proveedores autorizados, cuentas corporativas o políticas internas, por ejemplo.']),
        ]],
        ['title' => 'Fechas, responsables y alcance', 'intro' => 'Alineemos expectativas desde el principio.', 'fields' => [
            $choice('has_deadline', '¿Hay una fecha en la que necesites tenerlo listo?', $yes),
            $field('deadline', 'Fecha objetivo', 'date', $when('has_deadline') + ['help' => 'Opcional. Selecciona una fecha en el calendario. Si todavía no sabes el día exacto, déjalo vacío y explícalo abajo.']),
            $field('deadline_reason', '¿Por qué es importante esa fecha?', 'textarea', $when('has_deadline') + ['help' => 'Un lanzamiento, evento, auditoría o inicio de operaciones.']),
            $field('project_contact', '¿Quién será nuestra principal persona de contacto?'),
            $field('decision_maker', '¿Quién dará la aprobación final?'),
            $choice('more_reviewers', '¿Hay más personas que deban revisar o aprobar avances?', ['Sí', 'No']),
            $field('reviewers', 'Cuéntanos quiénes', 'textarea', $when('more_reviewers')),
            $choice('budget_range', '¿Tienen un rango de inversión pensado?', ['Aún no lo hemos definido', 'Menos de $20,000 MXN', '$20,000 a $50,000 MXN', '$50,000 a $100,000 MXN', '$100,000 a $250,000 MXN', 'Más de $250,000 MXN', 'Prefiero hablarlo directamente']),
            $field('out_of_scope', '¿Qué NO forma parte de este proyecto?', 'textarea'),
        ]],
        ['title' => 'Después de la entrega', 'intro' => 'Queremos que puedas aprovecharlo desde el primer día.', 'fields' => [
            $choice('managed_by', '¿Quién administrará o utilizará el proyecto?', ['Nuestro equipo', 'Una persona específica', 'Queremos que ustedes lo administren', 'No lo sabemos todavía']),
            $choice('training', '¿Necesitarán capacitación?', $yes),
            $choice('maintenance_interest', '¿Les interesa soporte o mantenimiento?', ['Sí', 'No', 'Quiero conocer las opciones']),
        ]],
        ['title' => 'Algo más y revisión', 'intro' => 'Un último vistazo antes de compartirlo con nuestro equipo.', 'fields' => [
            $field('additional_notes', '¿Hay algo importante que no te hayamos preguntado?', 'textarea'),
            $field('additional_files', 'Adjunta cualquier otro material importante', 'files'),
        ]],
    ],
];
