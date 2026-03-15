<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Content/Dist/css/login.css">
</head>
<body class="login-body">
    <button type="button" class="btn-theme-toggle-login" id="themeToggleLogin" aria-label="Cambiar tema">
        <i class="fas fa-moon" id="themeIconLogin"></i>
    </button>

    <div class="login-container">
        <div class="login-wrapper">
            <div class="login-card">
                <div class="login-header text-center">
                    <div class="login-logo">
                        <div class="logo-glow"></div>
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h1 class="login-title">Bienvenido</h1>
                    <p class="login-subtitle">Ingresa tus credenciales para continuar</p>
                </div>

                <form id="loginForm" class="mt-4">
                    <div class="form-floating-modern mb-3">
                        <div class="input-wrapper">
                            <input type="text" class="form-control form-control-modern" id="username" name="username" placeholder=" " autocomplete="username" required>
                            <i class="input-icon fas fa-user"></i>
                            <label class="floating-label" for="username">Usuario</label>
                            <span class="input-line"></span>
                        </div>
                    </div>

                    <div class="form-floating-modern mb-3">
                        <div class="input-wrapper">
                            <input type="password" class="form-control form-control-modern" id="password" name="password" placeholder=" " autocomplete="current-password" required>
                            <i class="input-icon fas fa-lock"></i>
                            <label class="floating-label" for="password">Contraseña</label>
                            <span class="input-line"></span>
                            <button type="button" class="btn-toggle-password" id="togglePassword" aria-label="Mostrar contraseña">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                            <label class="form-check-label" for="rememberMe">Recordarme</label>
                        </div>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-login-modern w-100">
                        <span class="btn-text">Iniciar sesión</span>
                        <span class="btn-loader"></span>
                    </button>
                </form>

                <div class="divider mt-4">
                    <span>o continúa con</span>
                </div>

                <div class="d-flex gap-2 justify-content-center mt-3">
                    <button type="button" class="btn btn-social btn-google flex-grow-1">
                        <i class="fab fa-google"></i> Google
                    </button>
                    <button type="button" class="btn btn-social btn-microsoft flex-grow-1">
                        <i class="fab fa-microsoft"></i> Microsoft
                    </button>
                </div>

                <div class="login-footer text-center mt-4">
                    <p class="mb-0">¿No tienes cuenta? <a href="#">Regístrate</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Content/Dist/js/login-theme.js"></script>
    <script src="Content/Dist/js/login.js"></script>
</body>
</html>
