// tailwind.config.js - iXtif Premium Dark/Light Mode Config
// ⚠️ ÖZEL KURAL: Siyah (black) YOK - Navy (lacivert) kullan!

module.exports = {
  // ⚠️ KRİTİK: class-based dark mode
  darkMode: 'class',

  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './Modules/**/resources/**/*.blade.php',
    './Modules/**/resources/**/*.js',
    './Modules/**/resources/**/*.vue',
  ],

  theme: {
    extend: {
      colors: {
        // Navy - En koyu lacivert (siyah yerine!)
        navy: {
          950: '#0a0e27', // En koyu (body background) - BLACK YERİNE!
          900: '#0f1629', // Section background
          800: '#1a1f3a', // Card background
          700: '#252b4a', // Hover state
          600: '#303654', // Light state
        },
        // Gold gradient için custom renk paleti
        gold: {
          50: '#fefce8',
          100: '#fef9c3',
          200: '#fef08a',
          300: '#fde047',
          400: '#facc15',
          500: '#f4e5a1', // Light gold (gradient)
          600: '#d4af37', // Main gold (gradient)
          700: '#b8941f',
          800: '#92740f',
          900: '#78600a',
          950: '#5c4808',
        },
      },

      backgroundImage: {
        // Premium gold gradient
        'gold-gradient': 'linear-gradient(90deg, #d4af37, #f4e5a1, #d4af37, #f4e5a1)',

        // Alternatif gradient'ler
        'gold-gradient-r': 'linear-gradient(to right, #ca8a04, #eab308)',
        'gold-radial': 'radial-gradient(circle, #f4e5a1, #d4af37)',

        // Section gradient'leri (Navy ile!)
        'gradient-dark': 'linear-gradient(to bottom, #0a0e27, #0f1629, #1a1f3a)',
        'gradient-light': 'linear-gradient(to bottom, #ffffff, #f9fafb, #f3f4f6)',
      },

      animation: {
        'gold-shimmer': 'gold-shimmer 3s ease infinite',
        'glow': 'glow 2s ease-in-out infinite',
      },

      keyframes: {
        'gold-shimmer': {
          '0%': { backgroundPosition: '0% 50%' },
          '50%': { backgroundPosition: '100% 50%' },
          '100%': { backgroundPosition: '0% 50%' },
        },
        'glow': {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.7' },
        },
      },

      boxShadow: {
        // Gold glow shadows
        'gold-sm': '0 0 20px rgba(212, 175, 55, 0.3)',
        'gold': '0 0 20px rgba(212, 175, 55, 0.5)',
        'gold-lg': '0 0 40px rgba(212, 175, 55, 0.5)',
        'gold-xl': '0 0 60px rgba(212, 175, 55, 0.6)',

        // Yellow glow shadows
        'yellow-sm': '0 0 20px rgba(234, 179, 8, 0.3)',
        'yellow': '0 0 20px rgba(234, 179, 8, 0.5)',
        'yellow-lg': '0 0 40px rgba(234, 179, 8, 0.5)',
      },

      backdropBlur: {
        xs: '2px',
      },

      backgroundSize: {
        '200': '200% auto',
      },
    },
  },

  plugins: [],
}

/*
 * KULLANIM ÖRNEKLERİ:
 *
 * 1. Gold Gradient Text:
 * <h1 class="bg-gold-gradient bg-clip-text text-transparent">PREMIUM</h1>
 *
 * 2. Gold Gradient Button:
 * <button class="bg-gold-gradient text-gray-950">SATIN AL</button>
 *
 * 3. Gold Shimmer Animation:
 * <div class="bg-gold-gradient bg-200 animate-gold-shimmer bg-clip-text text-transparent">
 *
 * 4. Gold Glow Shadow:
 * <button class="hover:shadow-gold-lg">BUTTON</button>
 *
 * 5. Custom Gold Color:
 * <div class="bg-gold-600 text-gold-50">
 *
 * 6. Dark Mode (Navy ile!):
 * <div class="bg-white dark:bg-navy-950">
 * <p class="text-gray-900 dark:text-white">
 *
 * 7. Section Gradient (Navy ile!):
 * <section class="bg-gradient-to-b from-navy-950 via-navy-900 to-navy-800">
 *
 * ⚠️ ÖZEL KURAL:
 * - bg-black kullanma! → bg-navy-950 kullan!
 * - Siyah yerine daima navy (lacivert) kullan!
 */
