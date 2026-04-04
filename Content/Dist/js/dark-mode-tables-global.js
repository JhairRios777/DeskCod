// ============================================
// dark-mode-tables-global.js
// Content/Dist/js/dark-mode-tables-global.js
// ============================================
(function() {

    // Inyecta CSS de hover una sola vez
    var styleEl = document.createElement('style');
    styleEl.id  = 'dm-table-hover-fix';
    styleEl.textContent = `
        html.dark-mode .table-hover tbody tr:hover td,
        html.dark-mode .table-hover tbody tr:hover th {
            color: #ffffff !important;
            --bs-table-bg-state: rgba(0,230,118,0.09) !important;
            --bs-table-bg-type:  rgba(0,230,118,0.09) !important;
        }
        html.dark-mode .table-hover tbody tr:hover td *,
        html.dark-mode .table-hover tbody tr:hover th * {
            color: #ffffff !important;
        }
        html.dark-mode .table-hover tbody tr:hover td .badge-activa,
        html.dark-mode .table-hover tbody tr:hover td .badge-vencida,
        html.dark-mode .table-hover tbody tr:hover td .badge-por-vencer,
        html.dark-mode .table-hover tbody tr:hover td .badge-suspendida {
            color: inherit !important;
        }
        html.dark-mode .table-hover tbody tr:hover td .badge.bg-secondary,
        html.dark-mode .table-hover tbody tr:hover td .badge.bg-primary,
        html.dark-mode .table-hover tbody tr:hover td .badge.bg-warning,
        html.dark-mode .table-hover tbody tr:hover td .badge.bg-danger,
        html.dark-mode .table-hover tbody tr:hover td .badge.bg-info,
        html.dark-mode .table-hover tbody tr:hover td .badge.bg-success {
            color: inherit !important;
        }
        html.dark-mode .table-hover tbody tr:hover td .btn-outline-warning,
        html.dark-mode .table-hover tbody tr:hover td .btn-outline-danger,
        html.dark-mode .table-hover tbody tr:hover td .btn-outline-primary,
        html.dark-mode .table-hover tbody tr:hover td .btn-outline-secondary {
            color: inherit !important;
        }
    `;
    document.head.appendChild(styleEl);

    function applyTableFix() {
        var isDark   = document.documentElement.classList.contains('dark-mode');
        var txtColor = isDark ? '#ffffff' : '#212529';
        var hoverBg  = isDark ? 'rgba(0,230,118,0.09)' : 'rgba(0,92,62,0.06)';
        var borderC  = isDark ? '#1e3329' : '#dee2e6';

        document.querySelectorAll('.table').forEach(function(t) {
            t.style.setProperty('--bs-table-color',        txtColor);
            t.style.setProperty('--bs-table-bg',           'transparent');
            t.style.setProperty('--bs-table-border-color', borderC);
            t.style.setProperty('--bs-table-hover-color',  txtColor);
            t.style.setProperty('--bs-table-hover-bg',     hoverBg);

            t.querySelectorAll('td, th').forEach(function(cell) {
                cell.style.color = txtColor;
            });

            t.querySelectorAll('td *').forEach(function(el) {
                var c = typeof el.className === 'string' ? el.className : '';
                var esBadge = c.includes('badge-activa')     ||
                              c.includes('badge-vencida')    ||
                              c.includes('badge-por-vencer') ||
                              c.includes('badge-suspendida') ||
                              c.includes('status-active-pulse') ||
                              (c.includes('badge') && c.includes('bg-')) ||
                              c.includes('btn-outline');
                el.style.color = esBadge ? '' : txtColor;
            });
        });
    }

    applyTableFix();

    new MutationObserver(applyTableFix).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
})();