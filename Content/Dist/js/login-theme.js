(function() {
    'use strict';

    var isDark = localStorage.getItem('darkMode') === 'true';
    var themeIcon = document.getElementById('themeIconLogin');

    if (isDark) {
        document.documentElement.classList.add('dark-mode');
    }

    function updateIcon() {
        if (themeIcon) {
            themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    updateIcon();

    var themeToggle = document.getElementById('themeToggleLogin');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            isDark = !isDark;
            document.documentElement.classList.toggle('dark-mode', isDark);
            localStorage.setItem('darkMode', isDark);
            updateIcon();
        });
    }
})();
