<?php
// ============================================
// Helper — verifica si el empleado tiene permiso
// Uso: puedeVer('clientes'), puedeCriar('tickets')
// ============================================
function puedeVer(string $modulo): bool {
    return !empty($_SESSION['system']['Permisos'][$modulo]['ver']);
}

function puedeCriar(string $modulo): bool {
    return !empty($_SESSION['system']['Permisos'][$modulo]['crear']);
}

function esAdmin(): bool {
    return !empty($_SESSION['system']['EsAdmin']);
}
?>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/Home" class="sidebar-brand d-flex align-items-center">
            <img src="/Content/Demo/logos/LOGO_PRINCIPAL_perfil.png" class="me-2" style="height:32px;">
            <span>DeskCod</span>
        </a>
        <button type="button" class="btn-close-sidebar" id="closeSidebar" aria-label="Cerrar menú">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">

            <?php if (puedeVer('dashboard')): ?>
            <li class="nav-item">
                <a class="nav-link" href="/Home">
                    <i class="fas fa-chart-pie"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVer('clientes')): ?>
            <li class="nav-item">
                <a class="nav-link" href="/Clientes">
                    <i class="fas fa-users"></i>
                    <span class="nav-link-text">Clientes</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVer('suscripciones')): ?>
            <li class="nav-item">
                <a class="nav-link" href="#submenuSuscripciones"
                   data-bs-toggle="collapse"
                   aria-expanded="false"
                   aria-controls="submenuSuscripciones">
                    <i class="fas fa-sync-alt"></i>
                    <span class="nav-link-text">Suscripciones</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="submenuSuscripciones">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/Suscripciones">
                                <i class="fas fa-list"></i>
                                <span class="nav-link-text">Ver todas</span>
                            </a>
                        </li>
                        <?php if (puedeCriar('suscripciones')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/Suscripciones/Registry">
                                <i class="fas fa-plus-circle"></i>
                                <span class="nav-link-text">Nueva suscripción</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <?php if (puedeVer('tickets')): ?>
            <li class="nav-item">
                <a class="nav-link" href="#submenuTickets"
                   data-bs-toggle="collapse"
                   aria-expanded="false"
                   aria-controls="submenuTickets">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="nav-link-text">Tickets</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="submenuTickets">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/Tickets">
                                <i class="fas fa-list"></i>
                                <span class="nav-link-text">Ver todos</span>
                            </a>
                        </li>
                        <?php if (puedeCriar('tickets')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/Tickets/Registry">
                                <i class="fas fa-plus-circle"></i>
                                <span class="nav-link-text">Nuevo ticket</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <?php if (puedeVer('empleados')): ?>
            <li class="nav-item">
                <a class="nav-link" href="/Empleados">
                    <i class="fas fa-user-tie"></i>
                    <span class="nav-link-text">Empleados</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVer('planes')): ?>
            <li class="nav-item">
                <a class="nav-link" href="#submenuPlanes"
                   data-bs-toggle="collapse"
                   aria-expanded="false"
                   aria-controls="submenuPlanes">
                    <i class="fas fa-layer-group"></i>
                    <span class="nav-link-text">Planes</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="submenuPlanes">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="/Planes">
                                <i class="fas fa-list"></i>
                                <span class="nav-link-text">Ver planes</span>
                            </a>
                        </li>
                        <?php if (puedeCriar('planes')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/Planes/Registry">
                                <i class="fas fa-plus-circle"></i>
                                <span class="nav-link-text">Nuevo plan</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <?php if (puedeVer('pagos')): ?>
            <li class="nav-item">
                <a class="nav-link" href="/Pagos">
                    <i class="fas fa-credit-card"></i>
                    <span class="nav-link-text">Pagos y Facturación</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if (puedeVer('reportes')): ?>
            <li class="nav-item">
                <a class="nav-link" href="/Reportes">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-link-text">Reportes</span>
                </a>
            </li>
            <?php endif; ?>

        </ul>
    </nav>
</aside>

<header class="top-header d-flex align-items-center justify-content-between" id="topHeader">
    <button type="button" class="btn-menu-toggle" id="menuToggle" aria-label="Abrir menú">
        <i class="fas fa-bars"></i>
    </button>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn-theme-toggle" id="themeToggle" aria-label="Cambiar tema">
            <i class="fas fa-moon" id="themeIcon"></i>
        </button>
        <div class="dropdown">
            <button class="btn btn-profile dropdown-toggle" type="button"
                    id="profileDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                <i class="fas fa-user me-2"></i>
                <?= htmlspecialchars($_SESSION['system']['UserName'] ?? 'Usuario') ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li>
                    <a class="dropdown-item" href="/Auth/logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>