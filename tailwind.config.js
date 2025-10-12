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
        // Light mode gradients
        'from-violet-600',
        'to-purple-600',
        'from-violet-700',
        'to-purple-700',
        'from-violet-800',
        'to-purple-800',
        // Hover variants
        'hover:from-violet-700',
        'hover:to-purple-700',
        'hover:from-violet-800',
        'hover:to-purple-800',
        // Dark mode gradients - explicit full class names
        'dark:from-violet-700',
        'dark:to-purple-700',
        'dark:hover:from-violet-800',
        'dark:hover:to-purple-800',
        // Border colors
        'border-violet-300',
        'dark:border-violet-700',
        // Text colors
        'text-violet-700',
        'dark:text-violet-300',
        // Background colors
        'bg-violet-50',
        'hover:bg-violet-50',
        'dark:hover:bg-violet-900/20',
        'dark:hover:bg-violet-900/30',
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
