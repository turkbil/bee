<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsrfController extends Controller
{
    /**
     * CSRF token'ı yeniler ve geri döndürür
     * 
     * @param Request $request
     * @return string
     */
    public function refresh(Request $request)
    {
        $request->session()->regenerateToken();
        return csrf_token();
    }
}