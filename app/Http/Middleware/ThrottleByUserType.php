<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleByUserType
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limitType = 'api'): Response
    {
        $user = $request->user();

        // Determine limits based on user type and endpoint
        [$maxAttempts, $decayMinutes] = $this->getLimits($user, $limitType);

        // Create unique key for rate limiting
        $key = $this->resolveRequestSignature($request, $user);

        // Check if limit exceeded
        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildRateLimitResponse($key, $maxAttempts);
        }

        // Increment attempts
        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Get rate limits based on user type and endpoint
     */
    protected function getLimits($user, string $limitType): array
    {
        // Guest (not logged in)
        if (!$user) {
            return match($limitType) {
                'stream' => [60, 1],   // 60 requests per minute (increased from 30)
                'auth' => [20, 1],     // 20 requests per minute (increased from 10)
                'api' => [120, 1],     // 120 requests per minute (increased from 60)
                default => [120, 1],
            };
        }

        // Premium Member
        if ($user->isPremium()) {
            return match($limitType) {
                'stream' => [300, 1],  // 300 requests per minute
                'auth' => [30, 1],     // 30 requests per minute
                'api' => [500, 1],     // 500 requests per minute
                default => [500, 1],
            };
        }

        // Normal Member (logged in but not premium)
        return match($limitType) {
            'stream' => [200, 1],      // 200 requests per minute (increased from 120)
            'auth' => [40, 1],         // 40 requests per minute (increased from 20)
            'api' => [300, 1],         // 300 requests per minute (increased from 180)
            default => [300, 1],
        };
    }

    /**
     * Resolve request signature for rate limiting key
     */
    protected function resolveRequestSignature(Request $request, $user): string
    {
        if ($user) {
            // For logged-in users: user ID + route name
            return sha1(
                $user->id . '|' . $request->route()->getName()
            );
        }

        // For guests: IP address + route name
        return sha1(
            $request->ip() . '|' . $request->route()->getName()
        );
    }

    /**
     * Calculate remaining attempts
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return $this->limiter->remaining($key, $maxAttempts);
    }

    /**
     * Build rate limit exceeded response (429 Too Many Requests)
     */
    protected function buildRateLimitResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->limiter->availableIn($key);

        return response()->json([
            'error' => 'rate_limit_exceeded',
            'message' => 'Çok fazla istek gönderdiniz. Lütfen biraz bekleyin.',
            'retry_after' => $retryAfter,
            'retry_after_formatted' => $this->formatRetryAfter($retryAfter)
        ], 429)
        ->header('X-RateLimit-Limit', $maxAttempts)
        ->header('X-RateLimit-Remaining', 0)
        ->header('Retry-After', $retryAfter);
    }

    /**
     * Add rate limit headers to response
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        // BinaryFileResponse doesn't have ->header() method, use ->headers->set() instead
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $remainingAttempts));

        return $response;
    }

    /**
     * Format retry after seconds to human-readable
     */
    protected function formatRetryAfter(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' saniye';
        }

        $minutes = ceil($seconds / 60);
        return $minutes . ' dakika';
    }
}
