<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NEXUS DIGITAL DB | Software para empresas</title>
    <meta name="description" content="Sistemas, automatización, integraciones y soluciones de datos para empresas.">
    <meta name="app-base" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (config('recaptcha.site_key'))
        <meta name="recaptcha-site-key" content="{{ config('recaptcha.site_key') }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('landing/logos/logo black.png') }}" type="image/png">
    @viteReactRefresh
    @vite('resources/js/landing/main.tsx')
</head>
<body class="antialiased">
    <div id="landing-root" data-page="{{ $page ?? 'home' }}"></div>
    <noscript>Enable JavaScript to view this website.</noscript>
</body>
</html>
