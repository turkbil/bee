/**
 * Tenant 2 - ixtif.com
 * Endustriyel ekipman (forklift, transpalet)
 */

const baseConfig = require('../base.config.js');

module.exports = {
    ...baseConfig,

    content: [
        // ixtif tema dosyalari
        './resources/views/themes/ixtif/**/*.blade.php',

        // Modullerin ixtif tema dosyalari
        './Modules/**/resources/views/themes/ixtif/**/*.blade.php',

        // Ortak layout ve componentler
        './resources/views/layouts/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './resources/views/profile/**/*.blade.php',
        './resources/views/errors/**/*.blade.php',

        // Modul ortak dosyalari (front, admin degil)
        './Modules/**/resources/views/front/**/*.blade.php',
        './Modules/**/resources/views/livewire/front/**/*.blade.php',

        // JS dosyalari
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            ...baseConfig.theme.extend,
            colors: {
                ...baseConfig.theme.extend.colors,
                // ixtif ozgu renkler (sonra degistirilecek)
                // Simdilik varsayilan
            }
        }
    },

    // ixtif icin ek safelist
    safelist: [
        ...baseConfig.safelist,
        // ixtif'e ozel siniflar eklenebilir
    ],
};
