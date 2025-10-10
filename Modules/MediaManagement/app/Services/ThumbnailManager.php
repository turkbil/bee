<?php

namespace Modules\MediaManagement\App\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class ThumbnailManager
{
    protected Filesystem $disk;

    protected array $config = [];

    public function __construct(?Filesystem $disk = null, ?array $config = null)
    {
        $this->config = $config ?? config('mediamanagement.thumbmaker', []);
        $diskName = Arr::get($this->config, 'disk', 'public');
        $this->disk = $disk ?? Storage::disk($diskName);
    }


    public function applyToUploadedFile(UploadedFile $file, ?string $profile = null, array $overrides = []): void
    {
        if (! $file->isValid()) {
            return;
        }

        $options = $this->resolveOptions($profile, $overrides);
        $extension = $file->getClientOriginalExtension();
        if ($this->shouldSkipExtension($extension, $options) || $this->isNoop($options, (string) $extension)) {
            return;
        }

        try {
            $this->manipulate($file->getRealPath(), $options);
        } catch (Throwable $e) {
            Log::warning('thumbmaker.uploaded_file_failed', [
                'file' => $file->getClientOriginalName(),
                'profile' => $profile,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function url(Media|string $source, array|string|null $profile = null, array $overrides = []): ?string
    {
        if (is_string($profile)) {
            $options = $this->resolveOptions($profile, $overrides);
        } elseif (is_array($profile)) {
            $options = $this->resolveOptions(null, $profile);
        } else {
            $options = $this->resolveOptions(null, $overrides);
        }

        $resolved = $this->resolveSource($source);
        if (! $resolved) {
            return null;
        }

        [$absolutePath, $relativePath, $tenantId, $extension] = $resolved;

        if ($this->shouldSkipExtension($extension, $options)) {
            return $this->toPublicUrl($relativePath);
        }

        if ($this->isNoop($options, $extension)) {
            return $this->toPublicUrl($relativePath);
        }

        $format = strtolower($options['format'] ?? '') ?: $extension;
        $options['format'] = $format;

        $hash = $this->buildHash($relativePath, $absolutePath, $options);
        $targetRelative = $this->buildTargetRelative($tenantId, $hash, $format);
        $diskRelative = $this->normalizeDiskRelative($targetRelative, $tenantId);

        if ($this->disk->exists($diskRelative)) {
            return $this->toPublicUrl($targetRelative);
        }

        $this->ensureDirectory(dirname($diskRelative));

        $tempPath = $this->createTempCopy($absolutePath);

        try {
            $this->manipulate($tempPath, $options, $absolutePath);
        } catch (Throwable $e) {
            Log::warning('thumbmaker.generate_failed', [
                'source' => $relativePath,
                'profile' => $profile,
                'options' => $options,
                'message' => $e->getMessage(),
            ]);

            @unlink($tempPath);

            return $this->toPublicUrl($relativePath);
        }

        $stream = fopen($tempPath, 'r');
        $this->disk->put($diskRelative, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        @unlink($tempPath);

        return $this->toPublicUrl($targetRelative);
    }

    protected function resolveOptions(?string $profile, array $overrides = []): array
    {
        $defaults = Arr::get($this->config, 'default', []);
        if ($profile) {
            $profileOptions = Arr::get($this->config, "profiles.{$profile}", []);
            $defaults = array_merge($defaults, $profileOptions);
        }

        return array_merge($defaults, $overrides);
    }

    protected function shouldSkipExtension(?string $extension, array $options): bool
    {
        if (! $extension) {
            return false;
        }

        $extension = strtolower($extension);
        $skip = $options['skip_extensions'] ?? Arr::get($this->config, 'default.skip_extensions', []);

        return in_array($extension, $skip, true);
    }

    protected function resolveSource(Media|string $source): ?array
    {
        if ($source instanceof Media) {
            $absolute = $source->getPath();
            $relative = $this->absoluteToRelative($absolute) ?? '';
            if ($relative === '') {
                $relative = trim('media/'.$source->id.'/'.$source->file_name, '/');
            }
            $tenantId = $this->extractTenantId($relative) ?? $this->resolveTenantId();
            $extension = pathinfo($absolute, PATHINFO_EXTENSION) ?: pathinfo($source->file_name, PATHINFO_EXTENSION);

            return [$absolute, $relative, $tenantId, strtolower((string) $extension)];
        }

        if (! is_string($source)) {
            return null;
        }

        if (Str::startsWith($source, ['http://', 'https://'])) {
            return null;
        }

        $clean = ltrim($source, '/');
        if (Str::startsWith($clean, 'storage/')) {
            $relative = substr($clean, strlen('storage/'));
        } else {
            $relative = $clean;
        }

        $relative = ltrim($relative, '/');
        $tenantFromPath = $this->extractTenantId($relative);
        $relativeWithoutTenant = $tenantFromPath
            ? ltrim(Str::after($relative, 'tenant'.$tenantFromPath.'/'), '/')
            : $relative;

        $relativeNormalized = ltrim(Str::replaceFirst('app/public/', '', $relativeWithoutTenant), '/');
        $tenantGuess = $tenantFromPath ?? $this->resolveTenantId();

        $pathsToCheck = [];
        $addPath = static function (?string $path) use (&$pathsToCheck): void {
            if (! $path) {
                return;
            }

            $normalized = rtrim($path, '/');
            if ($normalized === '') {
                return;
            }

            if (! in_array($normalized, $pathsToCheck, true)) {
                $pathsToCheck[] = $normalized;
            }
        };

        if ($tenantFromPath) {
            $addPath(base_path('storage/tenant'.$tenantFromPath.'/app/public/'.$relativeNormalized));
            $addPath(base_path('storage/tenant'.$tenantFromPath.'/app/public/'.$relativeWithoutTenant));
        }

        $addPath(storage_path('app/public/'.$relativeNormalized));
        $addPath(storage_path('app/public/'.$relativeWithoutTenant));
        $addPath(base_path('storage/'.$relative));
        $addPath(base_path('storage/'.$relativeNormalized));

        if (! Str::startsWith($relative, 'tenant')) {
            $addPath(base_path('storage/tenant'.$tenantGuess.'/app/public/'.$relativeNormalized));
        }

        $absolute = null;
        foreach ($pathsToCheck as $candidate) {
            if (file_exists($candidate)) {
                $absolute = $candidate;
                break;
            }
        }

        if (! $absolute && file_exists($clean)) {
            $absolute = realpath($clean) ?: $clean;
            $relative = $this->absoluteToRelative($absolute) ?? $relative;
        }

        if (! $absolute) {
            return null;
        }

        if ($tenantFromPath) {
            $relative = 'tenant'.$tenantFromPath.'/'.ltrim($relativeNormalized, '/');
        } else {
            $fromAbsolute = $this->absoluteToRelative($absolute);
            if ($fromAbsolute !== null && $fromAbsolute !== '') {
                $relative = $fromAbsolute;
            } else {
                $relative = $relativeNormalized ?: $relative;
            }
        }

        $relative = ltrim($relative, '/');
        $tenantId = $this->extractTenantId($relative) ?? $tenantGuess;
        $extension = pathinfo($absolute, PATHINFO_EXTENSION);

        return [$absolute, $relative, $tenantId, strtolower((string) $extension)];
    }

    protected function absoluteToRelative(?string $absolute): ?string
    {
        if (! $absolute) {
            return null;
        }

        $publicPath = storage_path('app/public/');
        if (Str::startsWith($absolute, $publicPath)) {
            return ltrim(Str::replaceFirst($publicPath, '', $absolute), '/');
        }

        return null;
    }

    protected function extractTenantId(?string $relative): ?int
    {
        if (! $relative) {
            return null;
        }

        if (preg_match('/^tenant(\d+)\//', $relative, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    protected function resolveTenantId(): int
    {
        if (function_exists('tenant_id') && tenant_id()) {
            return (int) tenant_id();
        }

        if (function_exists('resolve_tenant_id')) {
            $resolved = resolve_tenant_id(false);
            if ($resolved) {
                return (int) $resolved;
            }
        }

        return 1;
    }

    protected function buildHash(string $relativePath, string $absolutePath, array $options): string
    {
        $mtime = file_exists($absolutePath) ? filemtime($absolutePath) : time();

        return md5($relativePath.'|'.$mtime.'|'.json_encode($options));
    }

    protected function buildTargetRelative(int $tenantId, string $hash, string $format): string
    {
        $format = ltrim(strtolower($format), '.');
        $cachePath = trim($this->config['cache_path'] ?? 'thumbmaker', '/');

        return "tenant{$tenantId}/{$cachePath}/{$hash}.{$format}";
    }

    protected function ensureDirectory(string $dir): void
    {
        if ($dir === '' || $dir === '.') {
            return;
        }

        if (! $this->disk->exists($dir)) {
            $this->disk->makeDirectory($dir);
        }
    }

    protected function createTempCopy(string $absolute): string
    {
        $extension = pathinfo($absolute, PATHINFO_EXTENSION);
        $temp = tempnam(sys_get_temp_dir(), 'thumbmaker_');
        $tempPath = $temp . ($extension ? '.'.strtolower($extension) : '');
        @unlink($temp);
        copy($absolute, $tempPath);

        return $tempPath;
    }

    protected function manipulate(string $path, array $options, ?string $originalPath = null): void
    {
        $image = Image::make($path);

        $width = isset($options['width']) ? (int) $options['width'] : null;
        $height = isset($options['height']) ? (int) $options['height'] : null;
        $fit = strtolower((string) ($options['fit'] ?? 'max'));
        $allowUpscale = (bool) ($options['upscale'] ?? false);

        if ($width || $height) {
            $resizeWidth = $width ?: null;
            $resizeHeight = $height ?: null;

            $constrain = function ($constraint) use ($allowUpscale) {
                $constraint->aspectRatio();
                if (! $allowUpscale) {
                    $constraint->upsize();
                }
            };

            if ($fit === 'stretch') {
                $image->resize($resizeWidth ?? $image->width(), $resizeHeight ?? $image->height());
            } elseif ($fit === 'crop') {
                $targetW = $resizeWidth ?? ($resizeHeight ?: $image->width());
                $targetH = $resizeHeight ?? ($resizeWidth ?: $image->height());
                $image->fit($targetW, $targetH, function ($constraint) use ($allowUpscale) {
                    if (! $allowUpscale) {
                        $constraint->upsize();
                    }
                });
            } else {
                $image->resize($resizeWidth, $resizeHeight, $constrain);

                if ($fit === 'fill' && $resizeWidth && $resizeHeight) {
                    $background = $options['background'] ?? '#00000000';
                    $image->resizeCanvas($resizeWidth, $resizeHeight, 'center', false, $background);
                }
            }
        }

        $format = strtolower((string) ($options['format'] ?? ''));
        $quality = array_key_exists('quality', $options) && $options['quality'] !== null
            ? (int) $options['quality']
            : null;

        if ($format !== '') {
            $image->encode($format, $quality ?? 85)->save($path);
            return;
        }

        if ($quality !== null) {
            $image->save($path, $quality);
            return;
        }

        $image->save($path);
    }

    protected function isNoop(array $options, string $extension): bool
    {
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;
        $format = strtolower($options['format'] ?? '');
        $quality = $options['quality'] ?? null;
        $optimize = $options['optimize'] ?? false;

        return ! $width && ! $height && ($format === '' || $format === strtolower($extension))
            && is_null($quality) && ! $optimize;
    }

    protected function normalizeDiskRelative(string $relative, int $tenantId): string
    {
        $relative = ltrim($relative, '/');
        $root = $this->disk->path('');

        if (Str::startsWith($relative, 'tenant'.$tenantId.'/')
            && Str::contains($root, 'tenant'.$tenantId.'/app/public')) {
            return ltrim(Str::after($relative, 'tenant'.$tenantId.'/'), '/');
        }

        return $relative;
    }

    protected function toPublicUrl(string $relative): string
    {
        return cdn('storage/'.ltrim($relative, '/'));
    }
}
