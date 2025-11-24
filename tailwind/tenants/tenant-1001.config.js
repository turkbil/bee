/**
 * Tenant 1001 - muzibu.com
 * Muzik platformu
 */

const baseConfig = require('../base.config.js');

module.exports = {
    ...baseConfig,

    content: [
        // muzibu tema dosyalari
        './resources/views/themes/muzibu/**/*.blade.php',

        // Modullerin muzibu tema dosyalari
        './Modules/**/resources/views/themes/muzibu/**/*.blade.php',

        // Ortak layout ve componentler
        './resources/views/layouts/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './resources/views/profile/**/*.blade.php',
        './resources/views/errors/**/*.blade.php',

        // Modul ortak dosyalari
        './Modules/**/resources/views/front/**/*.blade.php',
        './Modules/**/resources/views/livewire/front/**/*.blade.php',

        // Muzibu modulu ozel
        './Modules/Muzibu/resources/views/**/*.blade.php',

        // JS dosyalari
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            ...baseConfig.theme.extend,
            colors: {
                ...baseConfig.theme.extend.colors,
                // muzibu ozgu renkler (sonra degistirilecek)
                // Simdilik varsayilan
            }
        }
    },

    // muzibu icin ek safelist
    safelist: [
        ...baseConfig.safelist,
        // muzibu'ya ozel siniflar
        'bg-gradient-to-b',
        'from-gray-900', 'to-black',
        'from-purple-900', 'to-gray-900',
    ],
};
