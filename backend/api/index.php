<?php
require_once __DIR__ . '/config.php';
json_response([
    'success' => true,
    'message' => 'ONOXFRI PHP API is running',
    'endpoints' => [
        'POST /auth/register.php',
        'POST /auth/login.php'
    ]
]);
