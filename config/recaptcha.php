<?php

return [
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    'minimum_score' => (float) env('RECAPTCHA_MINIMUM_SCORE', 0.5),
    'allowed_hostnames' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('RECAPTCHA_ALLOWED_HOSTNAMES', '')),
    ))),
];
