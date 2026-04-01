<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auth Rate Limits
    |--------------------------------------------------------------------------
    |
    | Maximum number of attempts per minute for authentication endpoints,
    | keyed by email + IP. Applies to both login and two-factor challenges.
    |
    */

    'auth' => (int) env('RATE_LIMIT_AUTH', 5),

    /*
    |--------------------------------------------------------------------------
    | Token Rate Limit
    |--------------------------------------------------------------------------
    |
    | Maximum token create/delete operations per minute per user.
    |
    */

    'tokens' => (int) env('RATE_LIMIT_TOKENS', 20),

    /*
    |--------------------------------------------------------------------------
    | API Rate Limits (tiered by plan)
    |--------------------------------------------------------------------------
    |
    | Requests per minute for each plan tier. Applied to all authenticated
    | API endpoints — including MCP access, which runs under the same user.
    |
    */

    'api' => [
        'free' => (int) env('RATE_LIMIT_API_FREE', 60),
        'pro'  => (int) env('RATE_LIMIT_API_PRO', 300),
    ],

];
