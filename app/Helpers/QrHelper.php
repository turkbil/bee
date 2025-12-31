<?php

use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Universal QR Helper
 * Tüm tenant'larda kullanılabilir basit QR üretici
 */

if (!function_exists('qr')) {
    /**
     * Generate QR code as base64 PNG image
     *
     * @param string $link URL or text to encode
     * @param int $size Size in pixels (default: 200)
     * @return string Base64 data URI (data:image/png;base64,...)
     */
    function qr(string $link, int $size = 200): string
    {
        $qrCode = QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->generate($link);

        return 'data:image/png;base64,' . base64_encode($qrCode);
    }
}

if (!function_exists('qr_png')) {
    /**
     * Generate QR code as raw PNG binary
     *
     * @param string $link URL or text to encode
     * @param int $size Size in pixels (default: 200)
     * @return string Raw PNG binary data
     */
    function qr_png(string $link, int $size = 200): string
    {
        return QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->generate($link);
    }
}

if (!function_exists('qr_svg')) {
    /**
     * Generate QR code as SVG string
     *
     * @param string $link URL or text to encode
     * @param int $size Size in pixels (default: 200)
     * @return string SVG markup
     */
    function qr_svg(string $link, int $size = 200): string
    {
        return QrCode::format('svg')
            ->size($size)
            ->margin(1)
            ->generate($link);
    }
}
