<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Throwable;

class TenantOfflineHandler
{
    /**
     * Tenant'ın aktif olmadığı durumları işler
     *
     * @param Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        return response()->view('errors.offline', [], 503);
    }
}