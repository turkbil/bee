<?php

namespace Modules\MediaManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Universal Thumbmaker Controller
 *
 * Anında görsel boyutlandırma, format dönüştürme ve optimizasyon
 *
 * Örnek Kullanım:
 * /thumbmaker?src=https://example.com/image.jpg&w=400&h=300&q=85&a=c&f=webp
 *
 * Parametreler:
 * - src: Kaynak görsel URL (zorunlu)
 * - w: Genişlik (width)
 * - h: Yükseklik (height)
 * - q: Kalite (quality, 1-100, varsayılan: 85)
 * - a: Hizalama (alignment: c=center, t=top, b=bottom, l=left, r=right, tl, tr, bl, br)
 * - s: Ölçeklendirme (scale: 0=fit, 1=fill, 2=stretch, varsayılan: 0)
 * - f: Format (format: webp, jpg, png, varsayılan: webp)
 * - c: Cache (cache: 0=hayır, 1=evet, varsayılan: 1)
 */
class ThumbmakerController extends Controller
{
    protected array $allowedFormats = ['webp', 'jpg', 'jpeg', 'png', 'gif'];
    protected array $allowedAlignments = ['c', 't', 'b', 'l', 'r', 'tl', 'tr', 'bl', 'br'];
    protected int $cacheDuration = 2592000; // 30 gün

    public function generate(Request $request): Response
    {
        // Parametreleri al ve validate et
        $src = $request->input('src');
        $width = $request->input('w') ? (int) $request->input('w') : null;
        $height = $request->input('h') ? (int) $request->input('h') : null;
        $quality = max(1, min(100, (int) ($request->input('q', 85))));
        $alignment = in_array($request->input('a'), $this->allowedAlignments) ? $request->input('a') : 'c';
        $scale = max(0, min(2, (int) ($request->input('s', 0))));
        $format = in_array($request->input('f'), $this->allowedFormats) ? $request->input('f') : 'webp';
        $useCache = $request->input('c', 1) == 1;

        // Src kontrolü
        if (!$src) {
            return $this->errorResponse('src parametresi zorunludur', 400);
        }

        // En az bir boyut belirtilmeli
        if (!$width && !$height) {
            return $this->errorResponse('width (w) veya height (h) parametrelerinden en az biri belirtilmelidir', 400);
        }

        // Cache key oluştur
        $cacheKey = 'thumbmaker.' . md5($src . $width . $height . $quality . $alignment . $scale . $format);

        // Cache'den kontrol et
        if ($useCache && Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            return response($cachedData['content'])
                ->header('Content-Type', $cachedData['mime'])
                ->header('Cache-Control', 'public, max-age=' . $this->cacheDuration)
                ->header('X-Thumbmaker-Cache', 'HIT');
        }

        try {
            // Görseli yükle
            $imageContent = $this->loadImage($src);
            if (!$imageContent) {
                return $this->errorResponse('Görsel yüklenemedi: ' . $src, 404);
            }

            // Intervention Image ile işle
            $image = Image::read($imageContent);

            // Boyutlandırma stratejisi
            switch ($scale) {
                case 1: // Fill (kes ve doldur)
                    if ($width && $height) {
                        $image->cover($width, $height, $this->getAlignmentPosition($alignment));
                    } elseif ($width) {
                        $image->scale(width: $width);
                    } else {
                        $image->scale(height: $height);
                    }
                    break;

                case 2: // Stretch (esnet)
                    if ($width && $height) {
                        $image->resize($width, $height);
                    } elseif ($width) {
                        $image->scale(width: $width);
                    } else {
                        $image->scale(height: $height);
                    }
                    break;

                default: // Fit (orantılı sığdır)
                    if ($width && $height) {
                        $image->scaleDown($width, $height);
                    } elseif ($width) {
                        $image->scale(width: $width);
                    } else {
                        $image->scale(height: $height);
                    }
                    break;
            }

            // Format ve encode
            $encoded = match ($format) {
                'webp' => $image->toWebp($quality),
                'jpg', 'jpeg' => $image->toJpeg($quality),
                'png' => $image->toPng(),
                'gif' => $image->toGif(),
                default => $image->toWebp($quality),
            };

            $content = (string) $encoded;
            $mimeType = 'image/' . ($format === 'jpg' ? 'jpeg' : $format);

            // Cache'e kaydet
            if ($useCache) {
                Cache::put($cacheKey, [
                    'content' => $content,
                    'mime' => $mimeType,
                ], $this->cacheDuration);
            }

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Cache-Control', 'public, max-age=' . $this->cacheDuration)
                ->header('X-Thumbmaker-Cache', 'MISS')
                ->header('X-Thumbmaker-Size', strlen($content));

        } catch (\Exception $e) {
            return $this->errorResponse('Görsel işlenemedi: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Görseli yükle (local veya remote)
     */
    protected function loadImage(string $src): ?string
    {
        // URL ise
        if (filter_var($src, FILTER_VALIDATE_URL)) {
            // Sadece güvenilir domainlere izin ver
            $parsedUrl = parse_url($src);
            $allowedHosts = [
                request()->getHost(), // Mevcut domain
                'ixtif.com',
                'ixtif.com.tr',
                'tuufi.com',
                'localhost',
            ];

            if (!in_array($parsedUrl['host'] ?? '', $allowedHosts)) {
                return null;
            }

            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'user_agent' => 'Mozilla/5.0 (Thumbmaker/1.0)',
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);

                return file_get_contents($src, false, $context);
            } catch (\Exception $e) {
                return null;
            }
        }

        // Local path ise
        if (Storage::exists($src)) {
            return Storage::get($src);
        }

        // Public path
        $publicPath = public_path($src);
        if (file_exists($publicPath)) {
            return file_get_contents($publicPath);
        }

        return null;
    }

    /**
     * Hizalama pozisyonunu döndür
     */
    protected function getAlignmentPosition(string $alignment): string
    {
        return match ($alignment) {
            't' => 'top',
            'b' => 'bottom',
            'l' => 'left',
            'r' => 'right',
            'tl' => 'top-left',
            'tr' => 'top-right',
            'bl' => 'bottom-left',
            'br' => 'bottom-right',
            default => 'center',
        };
    }

    /**
     * Hata response'u
     */
    protected function errorResponse(string $message, int $code = 400): Response
    {
        $errorImage = Image::create(400, 300)
            ->fill('f8f9fa');

        // Hata metnini ekle (basit, text() metodu yok ise sadece düz renk)
        $encoded = $errorImage->toWebp(80);

        return response((string) $encoded)
            ->header('Content-Type', 'image/webp')
            ->header('X-Thumbmaker-Error', $message)
            ->setStatusCode($code);
    }
}
