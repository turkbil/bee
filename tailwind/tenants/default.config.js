/**
 * Default / Central Config
 * Fallback ve central sistem icin
 */

const baseConfig = require('../base.config.js');

module.exports = {
    ...baseConfig,

    content: [
        // Tum tema dosyalari
        './resources/views/themes/**/*.blade.php',

        // Tum modul tema dosyalari
        './Modules/**/resources/views/themes/**/*.blade.php',

        // Ortak dosyalar
        './resources/views/layouts/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './resources/views/profile/**/*.blade.php',
        './resources/views/errors/**/*.blade.php',

        // Modul dosyalari
        './Modules/**/resources/views/front/**/*.blade.php',
        './Modules/**/resources/views/livewire/front/**/*.blade.php',

        // Pagination
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',

        // JS dosyalari
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            ...baseConfig.theme.extend,
        }
    },

    safelist: [
        ...baseConfig.safelist,
    ],
};
