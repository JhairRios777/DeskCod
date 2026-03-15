    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-brand d-flex align-items-center">
                <img src="/Content/Demo/LOGO_PRINCIPAL_perfil_dgd.png" class="me-2" style="height:32px;">
                <span>DeskCod</span>
            </a>
            <button type="button" class="btn-close-sidebar" id="closeSidebar" aria-label="Cerrar menú">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home"></i>
                        <span class="nav-link-text">Inicio</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-link-text">Dashboard</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <header class="top-header d-flex align-items-center justify-content-between">
        <button type="button" class="btn-menu-toggle" id="menuToggle" aria-label="Abrir menú">
            <i class="fas fa-bars"></i>
        </button>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-theme-toggle" id="themeToggle" aria-label="Cambiar tema">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-profile dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user me-2"></i>
                    Usuario
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item dropdown-item-logout" href="Login"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>
