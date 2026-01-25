<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

/**
 * Tenant oluşturulunca otomatik tema dosyaları oluştur
 * resources/views/themes/t-{id}/ klasörü altında
 * BLANK/MINIMAL şablonlar - içerik sonradan eklenir
 */
class CreateThemeFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    protected Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(): void
    {
        $tenantId = $this->tenant->id;
        $themeId = $this->tenant->theme_id ?? $tenantId;
        $themeName = "t-{$themeId}";
        $themePath = resource_path("views/themes/{$themeName}");

        // Tema klasörü zaten varsa ve dosyaları mevcutsa atla
        if ($this->themeFilesExist($themePath)) {
            Log::channel('system')->info("🎨 Tema dosyaları zaten mevcut: {$themeName}");
            return;
        }

        Log::channel('system')->info("🎨 Tema dosyaları oluşturuluyor: {$themeName}", [
            'tenant_id' => $tenantId,
        ]);

        try {
            // Klasörleri oluştur
            foreach (['', '/layouts', '/components', '/partials'] as $dir) {
                $dirPath = "{$themePath}{$dir}";
                if (!File::isDirectory($dirPath)) {
                    File::makeDirectory($dirPath, 0755, true);
                }
            }

            // Dosyaları oluştur
            File::put("{$themePath}/layouts/app.blade.php", $this->getAppTemplate($themeName));
            File::put("{$themePath}/layouts/header.blade.php", $this->getHeaderTemplate());
            File::put("{$themePath}/layouts/footer.blade.php", $this->getFooterTemplate());
            File::put("{$themePath}/homepage.blade.php", $this->getHomepageTemplate($themeName));
            File::put("{$themePath}/config.json", $this->getConfigJson($themeName, $themeId));

            // İzinleri ayarla
            exec("sudo chown -R tuufi.com_:psaserv {$themePath}");
            exec("sudo find {$themePath} -type f -exec chmod 644 {} \\;");
            exec("sudo find {$themePath} -type d -exec chmod 755 {} \\;");

            // Tema kaydı
            $this->ensureThemeRecord($themeName, $themeId);

            Log::channel('system')->info("✅ Tema dosyaları oluşturuldu: {$themeName}");

        } catch (\Exception $e) {
            Log::channel('system')->error("❌ Tema oluşturulamadı: {$e->getMessage()}");
        }
    }

    protected function themeFilesExist(string $themePath): bool
    {
        return File::exists("{$themePath}/layouts/app.blade.php")
            && File::exists("{$themePath}/layouts/header.blade.php")
            && File::exists("{$themePath}/layouts/footer.blade.php")
            && File::exists("{$themePath}/homepage.blade.php");
    }

    protected function ensureThemeRecord(string $themeName, int $themeId): void
    {
        $exists = DB::connection('mysql')->table('themes')->where('theme_id', $themeId)->exists();
        if (!$exists) {
            DB::connection('mysql')->table('themes')->insert([
                'theme_id' => $themeId,
                'name' => $themeName,
                'slug' => $themeName,
                'display_name' => "Tema {$themeId}",
                'is_active' => true,
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * BLANK App Layout - Sadece temel yapı
     */
    protected function getAppTemplate(string $themeName): string
    {
        return <<<BLADE
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta />
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.min.css') }}">

    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="bg-white text-gray-900">
    @include('themes.{$themeName}.layouts.header')

    <main>
        @yield('content')
        @yield('module_content')
    </main>

    @include('themes.{$themeName}.layouts.footer')

    @stack('scripts')
    <x-pwa-registration />
</body>
</html>
BLADE;
    }

    /**
     * BLANK Header - Minimal yapı
     */
    protected function getHeaderTemplate(): string
    {
        return <<<'BLADE'
{{-- Header - Düzenlenecek --}}
<header class="bg-white border-b">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-xl font-bold">
                {{ setting('site_title') ?: 'Site Adı' }}
            </a>
            <nav class="flex gap-4">
                <a href="{{ url('/') }}" class="hover:text-blue-600">Ana Sayfa</a>
            </nav>
        </div>
    </div>
</header>
BLADE;
    }

    /**
     * BLANK Footer - Minimal yapı
     */
    protected function getFooterTemplate(): string
    {
        return <<<'BLADE'
{{-- Footer - Düzenlenecek --}}
<footer class="bg-gray-100 border-t mt-auto">
    <div class="container mx-auto px-4 py-8 text-center text-gray-600">
        <p>&copy; {{ date('Y') }} {{ setting('site_title') ?: 'Site Adı' }}</p>
    </div>
</footer>
BLADE;
    }

    /**
     * BLANK Homepage - Boş sayfa
     */
    protected function getHomepageTemplate(string $themeName): string
    {
        return <<<BLADE
@extends('themes.{$themeName}.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="text-center">
        <h1 class="text-4xl font-bold mb-4">{{ setting('site_title') ?: 'Hoş Geldiniz' }}</h1>
        <p class="text-gray-600">{{ setting('site_description') }}</p>
    </div>
</div>
@endsection
BLADE;
    }

    protected function getConfigJson(string $themeName, int $themeId): string
    {
        return json_encode([
            'name' => $themeName,
            'display_name' => "Tema {$themeId}",
            'version' => '1.0.0',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
