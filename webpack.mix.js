const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | TENANT-SAFE ASSET PIPELINE - Laravel Mix Configuration
 |--------------------------------------------------------------------------
 |
 | Bu yapılandırma tenant sistemde sorun çıkarmayacak şekilde tasarlandı.
 | Vite'ın aksine, Mix tenant routing ile uyumlu çalışır.
 |
 */

// Public path ayarı - tenant sistemde sorun çıkarmasın
mix.setPublicPath('public');

// Ana CSS/JS dosyaları - tenant-safe paths
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .postCss('resources/css/app.css', 'public/css', [
       require('tailwindcss'),
       require('autoprefixer'),
   ]);

// Admin panel assets - tenant bağımsız
mix.js('resources/js/admin.js', 'public/admin-assets/js')
   .sass('resources/sass/admin.scss', 'public/admin-assets/css');

// Frontend theme assets - tenant-aware
mix.js('resources/js/frontend.js', 'public/assets/js')
   .sass('resources/sass/frontend.scss', 'public/assets/css');

// AI Content System - global asset
mix.js('public/assets/js/ai-content-system.js', 'public/assets/js/ai-content-system.min.js');

// Production optimizations
if (mix.inProduction()) {
    mix.version(); // Asset versioning for cache busting
    mix.options({
        terser: {
            terserOptions: {
                compress: {
                    drop_console: true, // Remove console.logs in production
                }
            }
        }
    });
}

// Development options
mix.options({
    processCssUrls: false, // Tenant URL'lerde sorun çıkarmasın
    postCss: [
        require('tailwindcss'),
        require('autoprefixer'),
    ]
});

// Source maps for development
if (!mix.inProduction()) {
    mix.sourceMaps();
}

// BrowserSync - tenant domain aware (opsiyonel)
mix.browserSync({
    proxy: 'laravel.test', // Ana domain
    files: [
        'app/**/*.php',
        'resources/views/**/*.php',
        'Modules/**/resources/views/**/*.php',
        'public/js/**/*.js',
        'public/css/**/*.css'
    ],
    ignore: [
        'node_modules/**/*',
        'vendor/**/*'
    ]
});

// Webpack configuration - tenant routing compatible
mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            '@admin': path.resolve('resources/js/admin'),
            '@components': path.resolve('resources/js/components')
        }
    },
    output: {
        // Tenant-safe public path
        publicPath: '/',
        chunkFilename: 'js/[name].[chunkhash].js'
    }
});

// Notification on completion
mix.disableNotifications();