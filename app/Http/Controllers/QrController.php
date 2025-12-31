<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Universal QR Controller
 * /qr/{path} URL pattern için otomatik QR üretici
 */
class QrController extends Controller
{
    /**
     * Generate QR code for any path
     *
     * @param Request $request
     * @param string $path
     * @return Response
     */
    public function generate(Request $request, string $path = ''): Response
    {
        // Build full URL (current domain + path)
        $url = $request->getSchemeAndHttpHost() . '/' . $path;

        // Generate QR as raw PNG
        $qrPng = qr_png($url, 300);

        return response($qrPng, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'public, max-age=86400', // 1 day cache
        ]);
    }
}
