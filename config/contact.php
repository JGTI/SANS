<?php

return [
    'cc' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CONTACT_CC', '')),
    ))),
    'send_confirmation' => env('CONTACT_SEND_CONFIRMATION', true),
    'recipients' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CONTACT_RECIPIENTS', '')),
    ))),
];
