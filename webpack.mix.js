const mix = require('laravel-mix');
const path = require('path');

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
   .sass('resources/sass/app.scss', 'public/css', {
       sassOptions: {
           api: 'modern',
           silenceDeprecations: ['legacy-js-api']
       }
   })
   .postCss('resources/css/app.css', 'public/css', [
       require('tailwindcss'),
       require('autoprefixer'),
   ]);

// Admin panel assets - tenant bağımsız
mix.js('resources/js/admin.js', 'public/admin-assets/js')
   .sass('resources/sass/admin.scss', 'public/admin-assets/css', {
       sassOptions: {
           api: 'modern',
           silenceDeprecations: ['legacy-js-api']
       }
   });

// Frontend theme assets - tenant-aware
mix.js('resources/js/frontend.js', 'public/assets/js')
   .sass('resources/sass/frontend.scss', 'public/assets/css', {
       sassOptions: {
           api: 'modern',
           silenceDeprecations: ['legacy-js-api']
       }
   });

// AI Content System - global asset
mix.js('public/assets/js/ai-content-system.js', 'public/assets/js/ai-content-system.min.js');

// Theme-Based CSS Bundle System - Performance Optimization
// Her tema kendi bundle'ını oluşturabilir, tenant'lar birbirini etkilemez

// İXTİF Theme Bundle (tenant: ixtif.com)
if (require('fs').existsSync('public/css/ixtif-theme.css')) {
    mix.styles([
        'public/css/ixtif-theme.css',
        'public/css/custom-gradients.css',
        'public/css/core-system.css',
        'public/css/ixtif-mobile-bottom-bar.css'
    ], 'public/css/ixtif-bundle.min.css');
}

// SIMPLE Theme Bundle (gelecek için hazır)
// if (require('fs').existsSync('public/css/simple-theme.css')) {
//     mix.styles([
//         'public/css/simple-theme.css',
//         'public/css/custom-gradients.css',
//         'public/css/core-system.css'
//     ], 'public/css/simple-bundle.min.css');
// }

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

// BrowserSync disabled for production
// mix.browserSync({
//     proxy: 'laravel.test',
//     files: [
//         'app/**/*.php',
//         'resources/views/**/*.php',
//         'Modules/**/resources/views/**/*.php',
//         'public/js/**/*.js',
//         'public/css/**/*.css'
//     ],
//     ignore: [
//         'node_modules/**/*',
//         'vendor/**/*'
//     ]
// });

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
    },
    stats: {
        children: true,
        warnings: true,
        warningsFilter: []
    }
});

// Notification on completion
mix.disableNotifications();