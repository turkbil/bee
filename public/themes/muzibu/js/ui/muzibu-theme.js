/**
 * Muzibu Theme Manager
 * Dark/Light mode toggle
 *
 * Note: safeStorage is defined globally in muzibu-player.js
 */

function muzibuTheme() {
    return {
        isDarkMode: safeStorage.getItem('theme') === 'light' ? false : true,

        init() {
            // Apply theme on load
            if (this.isDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            
            if (this.isDarkMode) {
                document.documentElement.classList.add('dark');
                safeStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                safeStorage.setItem('theme', 'light');
            }

            // Show toast notification
            const toast = Alpine.store('toast');
            if (toast && toast.show) {
                toast.show(
                    this.isDarkMode ? '🌙 Karanlık mod aktif' : '☀️ Aydınlık mod aktif',
                    'info'
                );
            }
        }
    }
}
