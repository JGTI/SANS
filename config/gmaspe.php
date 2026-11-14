<?php

return [
    'contact_recipients' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('GMASPE_CONTACT_RECIPIENTS', 'grupomaspe@gmaspe.com')),
    ))),
    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('GMASPE_ALLOWED_ORIGINS', 'https://gmaspe.com,https://www.gmaspe.com')),
    ))),
    'captcha_ttl_seconds' => (int) env('GMASPE_CAPTCHA_TTL_SECONDS', 600),
    'captcha_minimum_seconds' => (int) env('GMASPE_CAPTCHA_MINIMUM_SECONDS', 2),
];
