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
                // Spotify-style Muzibu colors
                'spotify-black': '#121212',
                'spotify-dark': '#181818',
                'spotify-green': '#1DB954',
                'spotify-green-light': '#1ed760',
                'spotify-gray': '#282828',
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
        // ALL Spotify colors (extracted from views)
        'bg-spotify-black', 'bg-spotify-dark', 'bg-spotify-gray', 'bg-spotify-green',
        'text-spotify-green', 'text-spotify-green-light',
        'border-spotify-green',
        'focus:ring-spotify-green',
        'from-spotify-black', 'from-spotify-dark', 'from-spotify-green', 'from-spotify-green-light',
        'to-spotify-black',
        'hover:bg-spotify-gray', 'hover:bg-spotify-green-light',
        'hover:text-spotify-green', 'hover:text-spotify-green-light',
    ],
};
