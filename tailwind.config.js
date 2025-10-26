const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');
const typography = require('@tailwindcss/typography');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './Modules/**/resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
    ],
    safelist: [
        // Gradient base
        'bg-gradient-to-r',
        'bg-gradient-to-br',

        // Light mode gradients (existing)
        'from-violet-600',
        'to-purple-600',
        'from-violet-700',
        'to-purple-700',
        'from-violet-800',
        'to-purple-800',
        'hover:from-violet-700',
        'hover:to-purple-700',
        'hover:from-violet-800',
        'hover:to-purple-800',
        'dark:from-violet-700',
        'dark:to-purple-700',
        'dark:hover:from-violet-800',
        'dark:hover:to-purple-800',
        'border-violet-300',
        'dark:border-violet-700',
        'text-violet-700',
        'dark:text-violet-300',
        'bg-violet-50',
        'hover:bg-violet-50',
        'dark:hover:bg-violet-900/20',
        'dark:hover:bg-violet-900/30',

        // Mega Menu Colors - Blue
        'from-blue-50', 'to-blue-50', 'from-blue-100', 'to-blue-100',
        'from-blue-500', 'to-blue-600', 'from-blue-600', 'to-blue-700',
        'bg-blue-50', 'bg-blue-100', 'bg-blue-500', 'bg-blue-600',
        'text-blue-400', 'text-blue-500', 'text-blue-600', 'text-blue-700',
        'dark:from-blue-900/30', 'dark:to-blue-900/30', 'dark:from-blue-900/20', 'dark:to-blue-900/20',
        'dark:from-blue-600', 'dark:to-blue-700',
        'dark:text-blue-300', 'dark:text-blue-400', 'dark:text-blue-500',
        'dark:bg-blue-900/50',
        'hover:text-blue-600', 'hover:text-blue-700',
        'dark:hover:text-blue-300', 'dark:hover:text-blue-400',
        'group-hover:text-blue-600', 'dark:group-hover:text-blue-400',

        // Mega Menu Colors - Green
        'from-green-50', 'to-green-50', 'from-green-100', 'to-green-100',
        'from-green-500', 'to-green-600', 'from-green-600', 'to-green-700',
        'bg-green-50', 'bg-green-100', 'bg-green-500', 'bg-green-600',
        'text-green-400', 'text-green-500', 'text-green-600', 'text-green-700',
        'dark:from-green-900/30', 'dark:to-green-900/30', 'dark:from-green-900/20', 'dark:to-green-900/20',
        'dark:from-green-600', 'dark:to-green-700',
        'dark:text-green-300', 'dark:text-green-400', 'dark:text-green-500',
        'dark:bg-green-900/50',
        'hover:text-green-600', 'hover:text-green-700',
        'dark:hover:text-green-300', 'dark:hover:text-green-400',
        'group-hover:text-green-600', 'dark:group-hover:text-green-400',
        'from-emerald-50', 'to-emerald-50',
        'dark:from-emerald-900/30', 'dark:to-emerald-900/30',

        // Mega Menu Colors - Purple
        'from-purple-50', 'to-purple-50', 'from-purple-100', 'to-purple-100',
        'from-purple-500', 'to-purple-600', 'from-purple-600', 'to-purple-700',
        'bg-purple-50', 'bg-purple-100', 'bg-purple-500', 'bg-purple-600',
        'text-purple-400', 'text-purple-500', 'text-purple-600', 'text-purple-700',
        'dark:from-purple-900/30', 'dark:to-purple-900/30', 'dark:from-purple-900/20', 'dark:to-purple-900/20',
        'dark:from-purple-600', 'dark:to-purple-700',
        'dark:text-purple-300', 'dark:text-purple-400', 'dark:text-purple-500',
        'dark:bg-purple-900/50',
        'hover:text-purple-600', 'hover:text-purple-700',
        'dark:hover:text-purple-300', 'dark:hover:text-purple-400',
        'group-hover:text-purple-600', 'dark:group-hover:text-purple-400',

        // Mega Menu Colors - Orange
        'from-orange-50', 'to-orange-50', 'from-orange-100', 'to-orange-100',
        'from-orange-500', 'to-orange-600', 'from-orange-600', 'to-orange-700',
        'bg-orange-50', 'bg-orange-100', 'bg-orange-500', 'bg-orange-600',
        'text-orange-400', 'text-orange-500', 'text-orange-600', 'text-orange-700',
        'dark:from-orange-900/30', 'dark:to-orange-900/30', 'dark:from-orange-900/20', 'dark:to-orange-900/20',
        'dark:from-orange-600', 'dark:to-orange-700',
        'dark:text-orange-300', 'dark:text-orange-400', 'dark:text-orange-500',
        'dark:bg-orange-900/50',
        'hover:text-orange-600', 'hover:text-orange-700',
        'dark:hover:text-orange-300', 'dark:hover:text-orange-400',
        'group-hover:text-orange-600', 'dark:group-hover:text-orange-400',

        // Mega Menu Colors - Red
        'from-red-50', 'to-red-50', 'from-red-100', 'to-red-100',
        'from-red-500', 'to-red-600', 'from-red-600', 'to-red-700',
        'bg-red-50', 'bg-red-100', 'bg-red-500', 'bg-red-600',
        'text-red-400', 'text-red-500', 'text-red-600', 'text-red-700',
        'dark:from-red-900/30', 'dark:to-red-900/30', 'dark:from-red-900/20', 'dark:to-red-900/20',
        'dark:from-red-600', 'dark:to-red-700',
        'dark:text-red-300', 'dark:text-red-400', 'dark:text-red-500',
        'dark:bg-red-900/50',
        'hover:text-red-600', 'hover:text-red-700',
        'dark:hover:text-red-300', 'dark:hover:text-red-400',
        'group-hover:text-red-600', 'dark:group-hover:text-red-400',

        // Mega Menu Colors - Pink
        'from-pink-50', 'to-pink-50', 'from-pink-100', 'to-pink-100',
        'from-pink-500', 'to-pink-600', 'from-pink-600', 'to-pink-700',
        'bg-pink-50', 'bg-pink-100', 'bg-pink-500', 'bg-pink-600',
        'text-pink-400', 'text-pink-500', 'text-pink-600', 'text-pink-700',
        'dark:from-pink-900/30', 'dark:to-pink-900/30', 'dark:from-pink-900/20', 'dark:to-pink-900/20',
        'dark:from-pink-600', 'dark:to-pink-700',
        'dark:text-pink-300', 'dark:text-pink-400', 'dark:text-pink-500',
        'dark:bg-pink-900/50',
        'hover:text-pink-600', 'hover:text-pink-700',
        'dark:hover:text-pink-300', 'dark:hover:text-pink-400',
        'group-hover:text-pink-600', 'dark:group-hover:text-pink-400',

        // Service Cards - Yellow/Orange
        'from-yellow-50', 'to-yellow-50', 'from-yellow-500', 'to-yellow-600',
        'from-yellow-500', 'to-orange-500', 'via-yellow-500', 'via-yellow-400',
        'hover:from-yellow-50/50', 'hover:to-orange-50/50',
        'dark:hover:from-yellow-900/20', 'dark:hover:to-orange-900/20',
        'hover:text-yellow-600', 'dark:hover:text-yellow-400',

        // Service Cards - Teal/Cyan
        'from-teal-50', 'to-teal-50', 'from-teal-500', 'to-teal-600',
        'from-teal-500', 'to-cyan-500', 'via-teal-500', 'via-cyan-500',
        'from-cyan-50', 'to-cyan-50', 'from-cyan-500', 'to-cyan-600',
        'hover:from-teal-50/50', 'hover:to-cyan-50/50',
        'dark:hover:from-teal-900/20', 'dark:hover:to-cyan-900/20',

        // Badge Gradient Animation
        'bg-[length:200%_100%]', 'animate-gradient',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                }
            },
            // iXtif Theme Container (Standard Widths)
            spacing: {
                'ixtif-container-padding': 'clamp(1rem, 2vw, 0px)', // Responsive padding: mobile 1rem, tablet+ 0
            },
            typography: {
                DEFAULT: {
                    css: {
                        maxWidth: 'none',
                        // Section içindeki elementlere de stil uygula
                        // Section element spacing fix
                        'section': {
                            marginBottom: '3rem',
                        },
                        'section h1': {
                            fontSize: '2.25rem',
                            fontWeight: '700',
                            marginBottom: '1.5rem',
                            lineHeight: '1.25',
                            color: '#111827',
                        },
                        'section h2': {
                            fontSize: '1.875rem',
                            fontWeight: '700',
                            marginBottom: '1.25rem',
                            lineHeight: '1.25',
                            color: '#111827',
                        },
                        'section h3': {
                            fontSize: '1.5rem',
                            fontWeight: '700',
                            marginBottom: '1rem',
                            lineHeight: '1.375',
                            color: '#111827',
                        },
                        'section h4': {
                            fontSize: '1.25rem',
                            fontWeight: '700',
                            marginBottom: '1rem',
                            lineHeight: '1.375',
                            color: '#111827',
                        },
                        'section h5': {
                            fontSize: '1.125rem',
                            fontWeight: '600',
                            marginBottom: '0.75rem',
                            lineHeight: '1.5',
                            color: '#111827',
                        },
                        'section h6': {
                            fontSize: '1rem',
                            fontWeight: '600',
                            marginBottom: '0.75rem',
                            lineHeight: '1.5',
                            color: '#111827',
                        },
                        'section p': {
                            fontSize: '1rem',
                            lineHeight: '1.75',
                            marginBottom: '1rem',
                            color: '#374151',
                        },
                        'section ul': {
                            listStyleType: 'disc',
                            paddingLeft: '1.5rem',
                            marginBottom: '1rem',
                        },
                        'section ol': {
                            listStyleType: 'decimal',
                            paddingLeft: '1.5rem',
                            marginBottom: '1rem',
                        },
                        'section li': {
                            marginBottom: '0.5rem',
                        },
                        'section strong': {
                            fontWeight: '700',
                            color: '#111827',
                        },
                        'section em': {
                            fontStyle: 'italic',
                        },
                        'section a': {
                            color: '#2563EB',
                            textDecoration: 'underline',
                            '&:hover': {
                                color: '#1E40AF',
                            },
                        },
                    }
                }
            }
        },
    },

    plugins: [forms, typography],
};
