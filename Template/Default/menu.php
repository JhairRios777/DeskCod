<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/index.php" class="sidebar-brand d-flex align-items-center">
            <img src="/Content/Demo/logos/LOGO_PRINCIPAL_perfil.png" class="me-2" style="height:32px;">
            <span>DeskCod</span>
        </a>
        <button type="button" class="btn-close-sidebar" id="closeSidebar" aria-label="Cerrar menú">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column">

            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="/Home">
                    <i class="fas fa-chart-pie"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!-- Clientes -->
            <li class="nav-item">
                <a class="nav-link" href="/Clientes">
                    <i class="fas fa-users"></i>
                    <span class="nav-link-text">Clientes</span>
                </a>
            </li>

            <!-- Suscripciones (con submenú) -->
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
                        <li class="nav-item">
                            <a class="nav-link" href="/Suscripciones/nueva">
                                <i class="fas fa-plus-circle"></i>
                                <span class="nav-link-text">Nueva suscripción</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Suscripciones/por-vencer">
                                <i class="fas fa-clock"></i>
                                <span class="nav-link-text">Por vencer</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Tickets (con submenú) -->
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
                        <li class="nav-item">
                            <a class="nav-link" href="/Tickets/abiertos">
                                <i class="fas fa-folder-open"></i>
                                <span class="nav-link-text">Abiertos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Tickets/en-proceso">
                                <i class="fas fa-spinner"></i>
                                <span class="nav-link-text">En proceso</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Tickets/resueltos">
                                <i class="fas fa-check-circle"></i>
                                <span class="nav-link-text">Resueltos</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Empleados -->
            <li class="nav-item">
                <a class="nav-link" href="/Empleados">
                    <i class="fas fa-user-tie"></i>
                    <span class="nav-link-text">Empleados</span>
                </a>
            </li>

            <!-- Planes (con submenú) -->
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
                        <li class="nav-item">
                            <a class="nav-link" href="/Planes/nuevo">
                                <i class="fas fa-plus-circle"></i>
                                <span class="nav-link-text">Nuevo plan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Pagos y Facturación -->
            <li class="nav-item">
                <a class="nav-link" href="/Pagos">
                    <i class="fas fa-credit-card"></i>
                    <span class="nav-link-text">Pagos y Facturación</span>
                </a>
            </li>

            <!-- Reportes -->
            <li class="nav-item">
                <a class="nav-link" href="/Reportes">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-link-text">Reportes</span>
                </a>
            </li>

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
                    <a class="dropdown-item" href="/Empleados/perfil">
                        <i class="fas fa-user me-2"></i>Mi perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="/Empleados/configuracion">
                        <i class="fas fa-cog me-2"></i>Configuración
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item dropdown-item-logout" href="/Auth/logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>