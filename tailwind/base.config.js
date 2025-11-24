/**
 * Tailwind Base Config
 * Tum tenant'lar icin ortak ayarlar
 */

const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    darkMode: 'class',

    // Ortak safelist - tum tenant'larda kullanilabilir
    safelist: [
        // Grid System
        'grid', 'grid-cols-1', 'grid-cols-12', 'gap-6', 'gap-8',
        'col-span-1', 'col-span-2', 'col-span-3', 'col-span-4', 'col-span-5', 'col-span-6',
        'col-span-7', 'col-span-8', 'col-span-9', 'col-span-10', 'col-span-11', 'col-span-12',
        'lg:grid-cols-1', 'lg:grid-cols-2', 'lg:grid-cols-3', 'lg:grid-cols-4', 'lg:grid-cols-12',
        'lg:col-span-1', 'lg:col-span-2', 'lg:col-span-3', 'lg:col-span-4', 'lg:col-span-5', 'lg:col-span-6',
        'lg:col-span-7', 'lg:col-span-8', 'lg:col-span-9', 'lg:col-span-10', 'lg:col-span-11', 'lg:col-span-12',
        'md:col-span-12', 'sm:col-span-12',

        // Gradient base
        'bg-gradient-to-r', 'bg-gradient-to-br',

        // Temel renkler
        'from-violet-600', 'to-purple-600', 'from-violet-700', 'to-purple-700',
        'from-blue-500', 'to-blue-600', 'from-green-500', 'to-green-600',
        'from-purple-500', 'to-purple-600', 'from-orange-500', 'to-orange-600',
        'from-red-500', 'to-red-600', 'from-pink-500', 'to-pink-600',
        'from-teal-500', 'to-cyan-500', 'from-yellow-500', 'to-orange-500',

        // Badge Animation
        'bg-[length:200%_100%]', 'animate-gradient',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // CSS Variables ile dinamik renkler
                primary: {
                    50: 'rgb(var(--color-primary-50, 240 249 255) / <alpha-value>)',
                    100: 'rgb(var(--color-primary-100, 224 242 254) / <alpha-value>)',
                    200: 'rgb(var(--color-primary-200, 186 230 253) / <alpha-value>)',
                    300: 'rgb(var(--color-primary-300, 125 211 252) / <alpha-value>)',
                    400: 'rgb(var(--color-primary-400, 56 189 248) / <alpha-value>)',
                    500: 'rgb(var(--color-primary-500, 14 165 233) / <alpha-value>)',
                    600: 'rgb(var(--color-primary-600, 2 132 199) / <alpha-value>)',
                    700: 'rgb(var(--color-primary-700, 3 105 161) / <alpha-value>)',
                    800: 'rgb(var(--color-primary-800, 7 89 133) / <alpha-value>)',
                    900: 'rgb(var(--color-primary-900, 12 74 110) / <alpha-value>)',
                    DEFAULT: 'rgb(var(--color-primary-500, 14 165 233) / <alpha-value>)',
                }
            },
            keyframes: {
                gradient: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                }
            },
            animation: {
                gradient: 'gradient 3s ease infinite',
            },
            typography: {
                DEFAULT: {
                    css: {
                        maxWidth: 'none',
                        'section': { marginBottom: '3rem' },
                        'section h1': { fontSize: '2.25rem', fontWeight: '700', marginBottom: '1.5rem' },
                        'section h2': { fontSize: '1.875rem', fontWeight: '700', marginBottom: '1.25rem' },
                        'section h3': { fontSize: '1.5rem', fontWeight: '700', marginBottom: '1rem' },
                        'section p': { fontSize: '1rem', lineHeight: '1.75', marginBottom: '1rem' },
                    }
                }
            }
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
