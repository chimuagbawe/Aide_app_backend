<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], // Allow all HTTP methods

    'allowed_origins' => ['*'], // Allow requests from any origin (remove restrictions)

    'allowed_origins_patterns' => [], // No specific patterns needed

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [], // No headers need to be exposed

    'max_age' => 0, // No caching of the preflight request

    'supports_credentials' => true, // Allow credentials (cookies, authorization headers)
];
