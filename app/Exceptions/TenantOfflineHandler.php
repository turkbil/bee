<?php

namespace App\Exceptions;

use Exception;

class TenantOfflineException extends Exception
{
    /**
     * Tenant'ın offline olduğu durumlarda fırlatılacak özel istisna
     */
    public function __construct($message = "Bu tenant şu anda erişime kapalıdır.", $code = 503, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Raporlama için bir render metodu
     */
    public function render($request)
    {
        return response()->view('errors.offline', [], 503);
    }
}