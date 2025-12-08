<?php

use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Get stateful domains from database (domains table)
 * Falls back to env if database is not available
 */
$getStatefulDomains = function () {
    $defaultDomains = 'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1';

    try {
        // Check if we can connect to database and domains table exists
        if (Schema::hasTable('domains')) {
            $dbDomains = DB::table('domains')
                ->whereNotNull('domain')
                ->pluck('domain')
                ->toArray();

            if (!empty($dbDomains)) {
                // Add www variants for each domain (if not already present)
                $allDomains = [];
                foreach ($dbDomains as $domain) {
                    $allDomains[] = $domain;
                    if (!str_starts_with($domain, 'www.')) {
                        $allDomains[] = 'www.' . $domain;
                    }
                }

                return $defaultDomains . ',' . implode(',', array_unique($allDomains));
            }
        }
    } catch (\Exception $e) {
        // Database not available yet (during config cache, migrations, etc.)
        // Fall back to env
    }

    // Fallback to env variable
    return env('SANCTUM_STATEFUL_DOMAINS', $defaultDomains);
};

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production domains which access your API via a frontend SPA.
    |
    | 🔥 DYNAMIC: Domains are loaded from the `domains` table automatically!
    | New tenants will be recognized without code changes.
    |
    */

    'stateful' => explode(',', $getStatefulDomains()),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that will be checked when
    | Sanctum is trying to authenticate a request. If none of these guards
    | are able to authenticate the request, Sanctum will use the bearer
    | token that's present on an incoming request for authentication.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. This will override any values set in the token's
    | "expires_at" attribute, but first-party sessions are not affected.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Sanctum can prefix new tokens in order to take advantage of numerous
    | security scanning initiatives maintained by open source platforms
    | that notify developers if they commit tokens into repositories.
    |
    | See: https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum you may need to
    | customize some of the middleware Sanctum uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
