<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nexus Maintenance Panel
    |--------------------------------------------------------------------------
    |
    | Panel stateless pensado para cPanel compartido, donde puede no existir
    | SSH. Nunca permite comandos arbitrarios: únicamente una whitelist.
    |
    | En producción debe habilitarse explícitamente desde el .env.
    |
    */

    'maintenance' => [
        'enabled' => filter_var(
            env('NEXUS_MAINTENANCE_ENABLED', false),
            FILTER_VALIDATE_BOOL
        ),

        'key' => env('NEXUS_MAINTENANCE_KEY'),
    ],
];
