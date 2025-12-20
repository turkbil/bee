/**
 * Tenant 1001 - muzibu.com
 * Muzik platformu
 */

const baseConfig = require('../base.config.js');

module.exports = {
    mode: 'jit',
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
                // Muzibu custom colors
                'muzibu-coral': '#ff7f50',
                'muzibu-gray': '#1a1a1a',
                'muzibu-gray-light': '#282828',
                'muzibu-text-gray': '#B3B3B3',
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
        // Opacity backgrounds
        'bg-white/5', 'bg-white/10', 'bg-white/20',
        'bg-black/20', 'bg-black/40', 'bg-black/50', 'bg-black/60', 'bg-black/70', 'bg-black/90',
        'hover:bg-white/10', 'hover:bg-black/60', 'hover:bg-black/90',
        // Muzibu coral variants
        'bg-muzibu-coral', 'text-muzibu-coral', 'border-muzibu-coral',
        'bg-muzibu-coral/10', 'bg-muzibu-coral/20',
        'hover:bg-muzibu-coral', 'hover:text-muzibu-coral',
        'ring-muzibu-coral', 'ring-2',
        'from-muzibu-coral', 'to-pink-600', 'to-orange-600', 'to-purple-600',
        // Muzibu gray variants
        'bg-muzibu-gray', 'bg-muzibu-gray/95', 'hover:bg-gray-700',
        'bg-muzibu-gray-light', 'bg-muzibu-gray-light/30',
        // Muzibu text-gray variants (progress bar, text)
        'text-muzibu-text-gray', 'bg-muzibu-text-gray', 'bg-muzibu-text-gray/30', 'bg-muzibu-text-gray/50',
        'hover:text-muzibu-coral', 'hover:text-white', 'group-hover:bg-muzibu-coral',
        // Tailwind default color opacity variants (used throughout the app)
        'bg-gray-700', 'bg-gray-800', 'bg-gray-800/50', 'bg-gray-900', 'bg-gray-900/95',
        'bg-blue-500/20', 'bg-blue-600',
        'bg-green-400', 'bg-green-500', 'bg-green-500/10', 'bg-green-500/20', 'bg-green-600',
        'bg-purple-600/30', 'bg-purple-600/90',
        'bg-orange-500', 'bg-orange-500/20', 'bg-orange-600',
        'bg-red-500/20', 'bg-red-600',
        'bg-indigo-600', 'bg-teal-600',
        'bg-slate-700', 'bg-slate-800', 'bg-slate-800/50',
    ],

    plugins: [
        ...baseConfig.plugins || [],
        // Custom scrollbar-hide utility
        function({ addUtilities }) {
            addUtilities({
                '.scrollbar-hide': {
                    '-ms-overflow-style': 'none',
                    'scrollbar-width': 'none',
                    '&::-webkit-scrollbar': {
                        display: 'none'
                    }
                }
            })
        }
    ]
};
