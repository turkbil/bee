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
                'session_token' => $request->session()->token(),
                'request_token_header' => $request->header('X-CSRF-TOKEN'),
                'request_token_input' => $request->input('_token')
            ]);

            // Login sayfasından geldiyse tekrar login'e yönlendir
            if ($request->is('login') || $request->url() == route('login')) {
                return redirect()->route('login')
                    ->withInput($request->except('password', '_token'))
                    ->with('error', 'Oturum süreniz dolmuş. Lütfen tekrar deneyin.');
            }

            // Diğer sayfalar için homepage'e yönlendir
            return redirect('/')
                ->with('error', 'Oturum süreniz dolmuş. Lütfen giriş yapın.');
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