<!doctype html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="referrer" content="no-referrer">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tu proyecto | NEXUS DIGITAL DB</title>
    <link rel="icon" href="{{ asset('landing/logos/logo black.png') }}">
    @viteReactRefresh
    @vite('resources/js/onboarding/main.tsx')
</head>
<body>
    <div id="onboarding-root"></div>
    <script id="onboarding-data" type="application/json">{!! json_encode($payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <noscript>Activa JavaScript para completar el formulario de tu proyecto.</noscript>
</body>
</html>
