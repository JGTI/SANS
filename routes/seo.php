<?php

use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
    ];

    /*
     * Never allow non-production environments to be indexed.
     */
    if (! app()->environment('production')) {
        $lines[] = 'Disallow: /';

        return response(
            implode(PHP_EOL, $lines).PHP_EOL,
            200,
            ['Content-Type' => 'text/plain; charset=UTF-8']
        );
    }

    $lines[] = 'Allow: /';
    $lines[] = 'Disallow: /admin';
    $lines[] = 'Disallow: /api/';
    $lines[] = 'Disallow: /login';
    $lines[] = 'Disallow: /register';
    $lines[] = 'Disallow: /password/';
    $lines[] = 'Disallow: /sanctum/';
    $lines[] = '';
    $lines[] = 'Sitemap: '.url('/sitemap.xml');

    return response(
        implode(PHP_EOL, $lines).PHP_EOL,
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
})->name('seo.robots');

Route::get('/sitemap.xml', function () {
    abort_unless((bool) config('seo.sitemap.enabled', true), 404);

    $urls = collect(config('seo.sitemap.urls', []))
        ->filter(fn (array $item): bool => isset($item['path']))
        ->map(function (array $item): array {
            return [
                'loc' => url($item['path']),
                'lastmod' => $item['lastmod'] ?? now()->toDateString(),
                'changefreq' => $item['changefreq'] ?? 'weekly',
                'priority' => $item['priority'] ?? '0.5',
            ];
        });

    return response()
        ->view('seo.sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('seo.sitemap');
