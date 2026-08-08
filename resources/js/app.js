import '../css/app.css';

// Theme Management
(function () {
    const STORAGE_KEY = 'watchtower_theme';
    const DARK_CLASS = 'dark';

    // Get current theme from storage or system preference
    function getStoredTheme() {
        return localStorage.getItem(STORAGE_KEY);
    }

    // Set theme in storage
    function setStoredTheme(theme) {
        localStorage.setItem(STORAGE_KEY, theme);
    }

    // Get system preference
    function getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    // Get effective theme (stored > system)
    function getEffectiveTheme() {
        const stored = getStoredTheme();
        return stored || getSystemTheme();
    }

    // Apply theme to document
    function applyTheme(theme) {
        const isDark = theme === 'dark';

        // Update HTML class
        if (isDark) {
            document.documentElement.classList.add(DARK_CLASS);
        } else {
            document.documentElement.classList.remove(DARK_CLASS);
        }

        // Update toggle icons
        updateToggleIcons(isDark);

        // Update logo
        updateLogo(isDark);
    }

    // Update toggle button icons based on theme
    function updateToggleIcons(isDark) {
        const lightIcon = document.getElementById('theme-toggle-light-icon');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');

        if (lightIcon && darkIcon) {
            if (isDark) {
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            } else {
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            }
        }
    }

    // Update theme-aware logo
    function updateLogo(isDark) {
        const logos = document.querySelectorAll('[data-logo-dark]');
        logos.forEach(img => {
            if (isDark) {
                img.src = img.dataset.logoDark;
            } else {
                img.src = img.dataset.logoLight;
            }
        });
    }

    // Toggle theme
    function toggleTheme() {
        const currentTheme = getEffectiveTheme();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setStoredTheme(newTheme);
        applyTheme(newTheme);
    }

    // Initialize theme on page load (no flash)
    function initTheme() {
        const theme = getEffectiveTheme();
        applyTheme(theme);
    }

    // Listen for system preference changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!getStoredTheme()) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', initTheme);

    // Expose toggle function globally
    window.toggleTheme = toggleTheme;
})();
