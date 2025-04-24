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
            //
        });

        $this->renderable(function (TokenMismatchException $e, $request) {
            return Redirect::to('/');
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