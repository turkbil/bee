<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class AssetService
{
    /**
     * CSS dosyalarını render et
     *
     * @return string
     */
    public function renderCss(): string
    {
        $useCdn = config('studio.editor.cdn.enabled', false);
        $output = '';
        
        if ($useCdn) {
            $version = config('studio.editor.cdn.version', '0.21.8');
            $output .= '<link rel="stylesheet" href="https://unpkg.com/grapesjs@' . $version . '/dist/css/grapes.min.css">';
        } else {
            // Önce admin/libs/studio içinde ara
            if (file_exists(public_path('admin/libs/studio/css/grapes.min.css'))) {
                $output .= '<link rel="stylesheet" href="' . asset('admin/libs/studio/css/grapes.min.css') . '">';
            } else {
                $output .= '<link rel="stylesheet" href="' . asset('modules/studio/css/grapes.min.css') . '">';
            }
        }
        
        // Özel CSS dosyaları
        if (file_exists(public_path('admin/libs/studio/css/studio-editor.css'))) {
            $output .= '<link rel="stylesheet" href="' . asset('admin/libs/studio/css/studio-editor.css') . '">';
        } else if (file_exists(public_path('modules/studio/css/studio-editor.css'))) {
            $output .= '<link rel="stylesheet" href="' . asset('modules/studio/css/studio-editor.css') . '">';
        }
        
        if (file_exists(public_path('admin/libs/studio/css/studio-editor-ui.css'))) {
            $output .= '<link rel="stylesheet" href="' . asset('admin/libs/studio/css/studio-editor-ui.css') . '">';
        }
        
        if (file_exists(public_path('admin/libs/studio/css/studio-grapes-overrides.css'))) {
            $output .= '<link rel="stylesheet" href="' . asset('admin/libs/studio/css/studio-grapes-overrides.css') . '">';
        }
        
        return $output;
    }
    
    /**
     * JS dosyalarını render et
     *
     * @return string
     */
    public function renderJs(): string
    {
        $useCdn = config('studio.editor.cdn.enabled', false);
        $output = '';
        
        // JQuery
        $output .= '<script src="' . asset('admin/libs/jquery@3.7.1/jquery.min.js') . '"></script>';
        
        // GrapesJS
        if ($useCdn) {
            $version = config('studio.editor.cdn.version', '0.21.8');
            $output .= '<script src="https://unpkg.com/grapesjs@' . $version . '/dist/grapes.min.js"></script>';
        } else {
            // Önce admin/libs/studio içinde ara
            if (file_exists(public_path('admin/libs/studio/grapes.min.js'))) {
                $output .= '<script src="' . asset('admin/libs/studio/grapes.min.js') . '"></script>';
            } else {
                $output .= '<script src="' . asset('modules/studio/js/grapes.min.js') . '"></script>';
            }
        }
        
        // Parçalı JS dosyaları
        $partialFiles = [
            'studio-fix.js',
            'studio-utils.js',
            'studio-plugins.js',
            'studio-core.js',
            'studio-blocks.js',
            'studio-ui.js',
            'studio-actions.js',
            'studio-html-parser.js',
        ];
        
        foreach ($partialFiles as $file) {
            if (file_exists(public_path('admin/libs/studio/partials/' . $file))) {
                $output .= '<script src="' . asset('admin/libs/studio/partials/' . $file) . '"></script>';
            }
        }
        
        // Ana Studio JS
        if (file_exists(public_path('admin/libs/studio/studio.js'))) {
            $output .= '<script src="' . asset('admin/libs/studio/studio.js') . '"></script>';
        }
        
        if (file_exists(public_path('admin/libs/studio/app.js'))) {
            $output .= '<script src="' . asset('admin/libs/studio/app.js') . '"></script>';
        }
        
        return $output;
    }
    
    /**
     * Varlık yükle (GÜVENLİK İYİLEŞTİRMELİ)
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     * @throws \Exception
     */
    public function uploadAsset($file): array
    {
        // GÜVENLİK KONTROL 1: File validation
        $this->validateUploadedFile($file);
        
        // GÜVENLİK KONTROL 2: Virus scanning (if available)
        $this->scanFileForViruses($file);
        
        // GÜVENLİK KONTROL 3: Content type validation  
        $this->validateFileContent($file);
        
        // Safe file name oluştur
        $safeFileName = $this->generateSafeFileName($file);
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        // Storage yolunu belirle (tenant aware)
        $storagePath = config('studio.editor.storage.path', 'studio/assets');
        
        // GÜVENLİK KONTROL 4: Path validation
        $this->validateStoragePath($storagePath);
        
        // Dosyayı güvenli şekilde yükle
        $path = $file->storeAs($storagePath, $safeFileName, 'public');
        
        // GÜVENLİK LOG: Upload activity log
        $this->logAssetUpload($safeFileName, $mimeType, $size, $path);
        
        return [
            'name' => $safeFileName,
            'original_name' => $file->getClientOriginalName(),
            'type' => $mimeType,
            'size' => $size,
            'url' => Storage::disk('public')->url($path),
            'path' => $path
        ];
    }
    
    /**
     * Uploaded file'ı validate et
     */
    private function validateUploadedFile($file): void
    {
        if (!$file || !$file->isValid()) {
            throw new \InvalidArgumentException('Invalid file upload');
        }
        
        // File size kontrolü (10MB limit)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file->getSize() > $maxSize) {
            throw new \InvalidArgumentException('File too large. Maximum size is 10MB');
        }
        
        // Minimum file size (anti-malware)
        if ($file->getSize() < 10) {
            throw new \InvalidArgumentException('File too small. Suspicious file detected');
        }
        
        // Allowed extensions
        $allowedExtensions = [
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', // Images
            'css', 'js', // Code files  
            'woff', 'woff2', 'ttf', 'eot', // Fonts
            'mp4', 'webm', 'ogg', // Videos (small)
            'mp3', 'wav', 'ogg' // Audio (small)
        ];
        
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            throw new \InvalidArgumentException('File type not allowed: ' . $extension);
        }
        
        // Dangerous file names
        $fileName = strtolower($file->getClientOriginalName());
        $dangerousPatterns = [
            'index.php', 'config.php', '.htaccess', 'web.config',
            '.env', 'composer.json', 'package.json'
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (str_contains($fileName, $pattern)) {
                throw new \InvalidArgumentException('Dangerous file name detected: ' . $fileName);
            }
        }
        
        Log::debug('File upload validation passed', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);
    }
    
    /**
     * File content'ini validate et
     */
    private function validateFileContent($file): void
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        
        // MIME type ve extension uyumluluğu
        $mimeMap = [
            'jpg' => ['image/jpeg', 'image/jpg'],
            'jpeg' => ['image/jpeg', 'image/jpg'], 
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'svg' => ['image/svg+xml', 'text/plain'],
            'css' => ['text/css', 'text/plain'],
            'js' => ['application/javascript', 'text/javascript', 'text/plain'],
            'woff' => ['font/woff', 'application/font-woff'],
            'woff2' => ['font/woff2', 'application/font-woff2'],
            'ttf' => ['font/ttf', 'application/x-font-ttf'],
            'mp4' => ['video/mp4'],
            'webm' => ['video/webm'],
            'mp3' => ['audio/mpeg', 'audio/mp3'],
            'wav' => ['audio/wav', 'audio/wave']
        ];
        
        if (isset($mimeMap[$extension])) {
            if (!in_array($mimeType, $mimeMap[$extension])) {
                throw new \InvalidArgumentException('MIME type mismatch. Extension: ' . $extension . ', MIME: ' . $mimeType);
            }
        }
        
        // File header validation (magic number)
        $this->validateFileHeader($file, $extension);
        
        // SVG özel kontrol (XSS prevention)
        if ($extension === 'svg') {
            $this->validateSvgContent($file);
        }
        
        // CSS/JS özel kontrol
        if (in_array($extension, ['css', 'js'])) {
            $this->validateCodeContent($file, $extension);
        }
    }
    
    /**
     * File header'ı validate et (magic number)
     */
    private function validateFileHeader($file, string $extension): void
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if (!$handle) {
            throw new \InvalidArgumentException('Cannot read file header');
        }
        
        $header = fread($handle, 20);
        fclose($handle);
        
        $magicNumbers = [
            'jpg' => ["\xFF\xD8\xFF"],
            'jpeg' => ["\xFF\xD8\xFF"],  
            'png' => ["\x89\x50\x4E\x47"],
            'gif' => ["\x47\x49\x46\x38"],
            'pdf' => ["\x25\x50\x44\x46"],
        ];
        
        if (isset($magicNumbers[$extension])) {
            $valid = false;
            foreach ($magicNumbers[$extension] as $magic) {
                if (str_starts_with($header, $magic)) {
                    $valid = true;
                    break;
                }
            }
            
            if (!$valid) {
                throw new \InvalidArgumentException('File header validation failed for: ' . $extension);
            }
        }
    }
    
    /**
     * SVG content'ini validate et
     */
    private function validateSvgContent($file): void
    {
        $content = file_get_contents($file->getRealPath());
        
        // Dangerous SVG patterns
        $dangerousPatterns = [
            '/<script/i',
            '/javascript:/i', 
            '/onload=/i',
            '/onerror=/i',
            '/<iframe/i',
            '/<embed/i',
            '/<object/i'
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new \InvalidArgumentException('Dangerous SVG content detected');
            }
        }
    }
    
    /**
     * CSS/JS content'ini validate et
     */
    private function validateCodeContent($file, string $type): void
    {
        $content = file_get_contents($file->getRealPath());
        
        // File size limit for code files (1MB)
        if (strlen($content) > 1024 * 1024) {
            throw new \InvalidArgumentException('Code file too large');
        }
        
        if ($type === 'js') {
            // Dangerous JS patterns
            $dangerousPatterns = [
                '/eval\s*\(/i',
                '/Function\s*\(/i',
                '/document\.write/i',
                '/location\.href\s*=/i',
                '/window\.location/i'
            ];
            
            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    throw new \InvalidArgumentException('Dangerous JavaScript content detected');
                }
            }
        }
        
        if ($type === 'css') {
            // Dangerous CSS patterns
            $dangerousPatterns = [
                '/javascript:/i',
                '/expression\s*\(/i',
                '/@import/i',
                '/behavior\s*:/i'
            ];
            
            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    throw new \InvalidArgumentException('Dangerous CSS content detected');
                }
            }
        }
    }
    
    /**
     * Virus scanning (placeholder)
     */
    private function scanFileForViruses($file): void
    {
        // ClamAV veya benzeri antivirus entegrasyonu buraya eklenebilir
        // Şimdilik basic file size ve extension kontrolü yapılıyor
        
        Log::debug('File virus scan completed (placeholder)', [
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize()
        ]);
    }
    
    /**
     * Safe file name oluştur
     */
    private function generateSafeFileName($file): string
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        
        // File name'i temizle
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $safeName = trim($safeName, '_-');
        
        // Minimum name length
        if (strlen($safeName) < 3) {
            $safeName = 'asset_' . $safeName;
        }
        
        // Maximum name length
        if (strlen($safeName) > 50) {
            $safeName = substr($safeName, 0, 50);
        }
        
        // Timestamp + random ekle (unique name)
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        
        return $safeName . '_' . $timestamp . '_' . $random . '.' . strtolower($extension);
    }
    
    /**
     * Storage path'ini validate et
     */
    private function validateStoragePath(string $path): void
    {
        // Path traversal kontrolü
        if (str_contains($path, '..') || str_contains($path, '//')) {
            throw new \InvalidArgumentException('Invalid storage path');
        }
        
        // Allowed base paths
        $allowedPaths = ['studio/assets', 'studio/uploads', 'assets', 'uploads'];
        $isAllowed = false;
        
        foreach ($allowedPaths as $allowed) {
            if (str_starts_with($path, $allowed)) {
                $isAllowed = true;
                break;
            }
        }
        
        if (!$isAllowed) {
            throw new \InvalidArgumentException('Storage path not allowed: ' . $path);
        }
    }
    
    /**
     * Asset upload'unu log'la
     */
    private function logAssetUpload(string $fileName, string $mimeType, int $size, string $path): void
    {
        Log::info('Asset uploaded successfully', [
            'file_name' => $fileName,
            'mime_type' => $mimeType,
            'file_size' => $size,
            'storage_path' => $path,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'uploaded_at' => now()->toISOString()
        ]);
    }
    
    /**
     * Varlık URL'sini al
     *
     * @param string $path
     * @return string
     */
    public function getAssetUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
    
    /**
     * Resim optimize et (küçük boyutlandır)
     *
     * @param string $path
     * @param array $options
     * @return string
     */
    public function optimizeImage(string $path, array $options = []): string
    {
        // Şimdilik basit bir yönlendirme
        return $this->getAssetUrl($path);
    }
}