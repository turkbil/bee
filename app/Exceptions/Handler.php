<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Redirect;
use Throwable;
use App\Exceptions\TenantOfflineException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Array to string conversion hatalarını özel loglama
            if (str_contains($e->getMessage(), 'Array to string conversion')) {
                \Log::error('🔥 ARRAY TO STRING CONVERSION DETECTED!', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                ]);
            }
        });

        $this->renderable(function (TokenMismatchException $e, $request) {
            // CSRF token mismatch - logout sonrası veya session expire durumu
            \Log::warning('CSRF Token Mismatch', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_token' => $request->session()->token() ?? 'NO_SESSION',
                'request_token_header' => $request->header('X-CSRF-TOKEN'),
                'request_token_input' => $request->input('_token'),
                'session_id' => $request->session()->getId() ?? 'NO_SESSION_ID',
            ]);

            // API request'leri için JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh the page.',
                    'error' => 'token_mismatch'
                ], 419);
            }

            // Login sayfasından geldiyse tekrar login'e yönlendir
            if ($request->is('login') || $request->routeIs('login')) {
                return redirect()->route('login')
                    ->withInput($request->except('password', '_token'))
                    ->with('error', 'Oturum süreniz dolmuş. Lütfen tekrar giriş yapın.');
            }

            // Register sayfası için
            if ($request->is('register') || $request->routeIs('register')) {
                return redirect()->route('register')
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Oturum süreniz dolmuş. Lütfen tekrar deneyin.');
            }

            // Diğer sayfalar için önceki sayfaya dön
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Oturum süreniz dolmuş. Lütfen tekrar deneyin.');
        });
        
        // Tenant offline durumu için özel istisna işleyici
        $this->renderable(function (TenantOfflineException $e, $request) {
            return response()->view('errors.offline', [], 503);
        });
        
        // Genel HTTP 503 hataları için offline sayfasını göster
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException $e, $request) {
            return response()->view('errors.offline', [], 503);
        });

        // Tüm HTTP 403 hataları için özel sayfa
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [], 403);
            }
        });
    }
}