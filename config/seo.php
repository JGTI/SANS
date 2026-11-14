<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SEO Starter
    |--------------------------------------------------------------------------
    |
    | Punto de partida. Conforme el proyecto tenga páginas públicas,
    | agrega sus rutas a sitemap.urls o reemplaza esta implementación
    | por una estrategia dinámica basada en modelos.
    |
    */

    'site_name' => env('SEO_SITE_NAME', env('APP_NAME')),

    'default_title' => env('SEO_DEFAULT_TITLE', env('APP_NAME')),

    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Sitio oficial de '.env('APP_NAME')
    ),

    'sitemap' => [
        'enabled' => filter_var(
            env('SEO_SITEMAP_ENABLED', true),
            FILTER_VALIDATE_BOOL
        ),

        'urls' => [
            [
                'path' => '/',
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
        ],
    ],

    /*
    | These paths should not be indexed by search engines.
    | The RobotsHeaders middleware also emits X-Robots-Tag.
    */
    'noindex' => [
        'onboarding',
        'onboarding/*',
        'admin',
        'admin/*',
        'api',
        'api/*',
        'login',
        'register',
        'logout',
        'password/*',
        'email/*',
        'sanctum/*',
        '_nexus/*',
    ],
];
