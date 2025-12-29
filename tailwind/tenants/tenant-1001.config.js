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

        // Subscription modulu (onemli!)
        './Modules/Subscription/resources/views/**/*.blade.php',

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
                // Subscription page colors
                'sub-coral': '#ff6b6b',
                'sub-coral-hover': '#ff5252',
                'sub-dark': '#141414',
                'sub-darker': '#0a0a0a',
            },
            // Ring color için muzibu-coral tanımı
            ringColor: {
                'muzibu-coral': '#ff7f50',
            },
            // Hover shimmer/gloss animasyonu
            animation: {
                'shimmer': 'shimmer 2s linear infinite',
                'shimmer-sweep': 'shimmer-sweep 0.8s ease-out forwards',
            },
            keyframes: {
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                'shimmer-sweep': {
                    '0%': { transform: 'translateX(-100%)', opacity: '0' },
                    '20%': { opacity: '1' },
                    '100%': { transform: 'translateX(100%)', opacity: '0' },
                },
            },
        }
    },

    // muzibu icin ek safelist
    safelist: [
        ...baseConfig.safelist,
        // Subscription Plans Page - onemli!
        'bg-sub-coral', 'hover:bg-sub-coral-hover', 'text-sub-coral',
        'bg-sub-dark', 'bg-sub-darker',
        'border-sub-coral', 'border-sub-coral/30', 'border-sub-coral/50',
        'bg-emerald-500', 'hover:bg-emerald-600', 'text-emerald-400', 'text-emerald-500',
        'bg-emerald-500/20', 'border-emerald-500/30',
        'bg-white/5', 'bg-white/10', 'bg-white/15', 'bg-white/20',
        'border-white/8', 'border-white/10', 'border-white/15',
        'shadow-lg', 'shadow-xl', 'rounded-3xl', 'rounded-2xl',
        'bg-gray-700', 'text-yellow-300',
        // Subscription card alignment
        'min-h-[60px]', 'min-h-[50px]', 'min-h-[100px]', 'min-h-[120px]',
        'items-stretch', 'line-clamp-2', 'pt-4',
        // muzibu'ya ozel siniflar
        'bg-gradient-to-b', 'bg-gradient-to-br',
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
        'from-muzibu-coral', 'to-pink-500', 'to-pink-600', 'to-orange-600', 'to-purple-600',
        'to-[#ff9966]', 'from-[#ff9966]', 'hover:from-[#ff9966]', 'hover:to-muzibu-coral',
        'border-muzibu-coral/60', 'hover:border-muzibu-coral',
        'shadow-lg', 'shadow-muzibu-coral/30', 'shadow-pink-500/30',
        'hover:opacity-90', 'ring-muzibu-coral',
        // Muzibu gray variants
        'bg-muzibu-gray', 'bg-muzibu-gray/95', 'hover:bg-gray-700',
        'bg-muzibu-gray-light', 'bg-muzibu-gray-light/30',
        'border-muzibu-gray', 'border-2',
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
        'bg-slate-700', 'bg-slate-800', 'bg-slate-800/50', 'bg-slate-800/95', 'bg-slate-900',
        // Corporate subscriptions page - badges
        'text-red-400', 'border-red-500/30', 'text-yellow-400', 'text-yellow-500',
        'bg-yellow-500/20', 'border-yellow-500/30', 'bg-amber-500/20', 'text-amber-400', 'border-amber-500/30',
        'from-purple-500', 'via-pink-500', 'to-rose-500', 'from-purple-600', 'to-pink-600',
        'shadow-purple-500/25', 'shadow-purple-500/40', 'ring-purple-500',
        'bg-purple-500/10', 'bg-purple-500/20', 'text-purple-300', 'text-purple-400', 'border-purple-500/30',
        'hover:bg-purple-500/10', 'hover:bg-purple-500/20',
        // Orange variants
        'from-orange-500', 'from-orange-600', 'to-orange-500', 'ring-orange-500',
        'border-orange-500/50', 'text-orange-400', 'text-orange-500',
        // Gradient from/via/to variants (right sidebar dynamic gradients)
        'from-orange-500/40', 'via-orange-500/20', 'to-transparent',
        'from-green-500/40', 'via-green-500/20',
        'from-blue-500/40', 'via-blue-500/20',
        'from-transparent', 'via-black/30', 'to-[#121212]',
        // Right sidebar grid columns (dynamic PHP variable - must be safelisted)
        // MD (768px+): Right sidebar visible, left sidebar hidden
        'md:grid-cols-[1fr_280px]',
        // LG (1024px+): Both sidebars visible
        'lg:grid-cols-[220px_1fr]',
        'lg:grid-cols-[220px_1fr_280px]',
        // XL (1280px+): Wider right sidebar
        'xl:grid-cols-[220px_1fr_320px]',
        // 2XL (1536px+): Even wider right sidebar
        '2xl:grid-cols-[220px_1fr_360px]',
        // Gap and padding responsive
        'md:gap-3', 'md:px-3', 'md:pt-3',
        // Right sidebar visibility
        'md:block',
        // Card hover shimmer/parlama efekti (çapraz)
        'animate-shimmer', 'group-hover:animate-shimmer',
        'animate-shimmer-sweep', 'group-hover:animate-shimmer-sweep',
        'bg-gradient-to-r', 'from-transparent', 'via-white/10', 'via-white/5', 'via-white/20',
        'group-hover:opacity-100', 'opacity-0',
        'overflow-hidden', 'pointer-events-none',
        '-translate-x-full', 'group-hover:translate-x-full',
        '-inset-full', 'skew-x-12',
        'transition-transform', 'duration-1000', 'ease-in-out',
        'transition-opacity', 'duration-500',
        // Hover koyu renk (açık değil!)
        'hover:bg-spotify-black', 'hover:bg-[#121212]',
        // Currently playing - subtle border
        'border-muzibu-coral/50', 'border-muzibu-coral/60', 'border-2', 'border-transparent',
        'hover:bg-gray-700/80',
        // Dynamic grid columns (Alpine x-bind)
        'md:grid-cols-[1fr_280px]',
        'lg:grid-cols-[220px_1fr_280px]',
        'xl:grid-cols-[220px_1fr_320px]',
        '2xl:grid-cols-[220px_1fr_360px]',
        'lg:grid-cols-[220px_1fr]',
        'xl:grid-cols-[220px_1fr]',
        '2xl:grid-cols-[220px_1fr]',
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
