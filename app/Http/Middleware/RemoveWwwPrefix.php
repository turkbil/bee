<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * WWW Prefix Remover
 * 
 * www.muzibu.com.tr → muzibu.com.tr
 * www.ixtif.com → ixtif.com (opsiyonel)
 */
class RemoveWwwPrefix
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // www ile başlıyorsa redirect et (SADECE GET request'ler için!)
        // POST request'lerde redirect yapma (form data kaybolur!)
        if (str_starts_with($host, 'www.') && $request->isMethod('GET')) {
            $newHost = substr($host, 4); // "www." kısmını çıkar
            $newUrl = $request->getScheme() . '://' . $newHost . $request->getRequestUri();

            return redirect($newUrl, 301); // Permanent redirect
        }

        return $next($request);
    }
}
