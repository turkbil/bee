<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Livewire JSON Response Sanitizer Middleware
 *
 * This middleware sanitizes JSON responses from Livewire to prevent
 * "Malformed UTF-8 characters, possibly incorrectly encoded" errors
 * during JSON serialization.
 */
class LivewireJsonSanitizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Intercept BEFORE response is created for Livewire
        if ($request->is('livewire/*') || $request->header('X-Livewire')) {
            try {
                // Sanitize request payload
                if ($request->isJson() && $request->has('components')) {
                    $data = $request->all();
                    $sanitized = $this->sanitizeData($data);
                    $request->merge($sanitized);
                }
            } catch (\Exception $e) {
                \Log::warning('LivewireJsonSanitizer: Failed to sanitize request', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $response = $next($request);

        // Process response
        if (($request->is('livewire/*') || $request->header('X-Livewire'))
            && $response instanceof \Illuminate\Http\JsonResponse) {
            try {
                // Get the original data
                $data = $response->getData(true);

                // Sanitize recursively
                $sanitized = $this->sanitizeData($data);

                // Set the sanitized data back
                $response->setData($sanitized);
            } catch (\Exception $e) {
                // Log detailed error
                \Log::error('LivewireJsonSanitizer: Failed to sanitize response', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'url' => $request->fullUrl(),
                ]);

                // Return error response instead of crashing
                return response()->json([
                    'error' => 'Invalid UTF-8 encoding detected',
                    'message' => 'Some data contains invalid characters. Please refresh the page.',
                ], 500);
            }
        }

        return $response;
    }

    /**
     * Recursively sanitize data to ensure valid UTF-8 encoding
     *
     * @param mixed $data
     * @return mixed
     */
    private function sanitizeData($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeData'], $data);
        }

        if (is_string($data)) {
            // Check if string is valid UTF-8
            if (!mb_check_encoding($data, 'UTF-8')) {
                // Convert invalid UTF-8 characters to valid UTF-8
                return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }
        }

        return $data;
    }
}
