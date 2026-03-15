(function() {
    'use strict';

    var isDark = localStorage.getItem('darkMode') === 'true';

    if (isDark) {
        document.documentElement.classList.add('dark-mode');
    }

    function updateIcon() {
        var themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    updateIcon();

    var themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            isDark = !isDark;
            document.documentElement.classList.toggle('dark-mode', isDark);
            localStorage.setItem('darkMode', isDark);
            updateIcon();
        });
    }
})();
