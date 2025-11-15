<?php

return [
    'to' => env('ERROR_REPORT_TO'),
    'bcc' => env('ERROR_REPORT_BCC'),
    'from' => env('ERROR_REPORT_FROM'),


    // Poziomy Monolog/Laravel, które mają wyzwalać maila
    'levels' => collect(explode(',', (string)env('ERROR_REPORT_LEVELS', 'error,critical,alert,emergency')))
        ->map(fn($l) => strtolower(trim($l)))
        ->filter()
        ->values()
        ->all(),


    // Anty‑spam: ile sekund dla identycznego zdarzenia (hash treści) nie wysyłać kolejnych maili
    'throttle_seconds' => (int)env('ERROR_REPORT_THROTTLE_SECONDS', 60),


    // Czy używać kolejek (Mail::queue) — wymaga skonfigurowanego queue worker
    'queue' => filter_var(env('ERROR_REPORT_QUEUE', false), FILTER_VALIDATE_BOOL),


    // Czy dołączać dane żądania
    'include_request' => true,
];