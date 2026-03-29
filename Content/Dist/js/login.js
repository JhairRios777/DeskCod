// ============================================
// login.js — DeskCod
// Conecta el formulario con AuthController
// via fetch() — reemplaza el setTimeout falso
// ============================================

(function () {
    'use strict';

    // ── Toggle mostrar/ocultar contraseña ──
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput  = document.getElementById('password');
    const eyeIcon        = document.getElementById('eyeIcon');

    if (togglePassword && passwordInput && eyeIcon) {
        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.className  = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    }

    // ── Envío del formulario ───────────────
    const loginForm = document.getElementById('loginForm');
    const btnLogin  = loginForm ? loginForm.querySelector('.btn-login-modern') : null;

    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const password = passwordInput.value;

            if (!username || !password) {
                showAlert('Por favor completa todos los campos.', 'error');
                return;
            }

            // Activa el loader en el botón
            setLoading(true);

            try {
                // POST a /Auth/login → AuthController::login()
                const response = await fetch('/Auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ username, password }),
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('¡Acceso correcto! Redirigiendo...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || '/Home';
                    }, 800);
                } else {
                    showAlert(data.message || 'Usuario o contraseña incorrectos.', 'error');
                    setLoading(false);
                }

            } catch (err) {
                console.error('Error:', err);
                showAlert('Error de conexión. Intenta de nuevo.', 'error');
                setLoading(false);
            }
        });
    }

    // ── Efectos focus en inputs ────────────
    document.querySelectorAll('.form-control-modern').forEach(input => {
        input.addEventListener('focus', function () {
            this.closest('.form-floating-modern')?.classList.add('focused');
        });
        input.addEventListener('blur', function () {
            if (!this.value) {
                this.closest('.form-floating-modern')?.classList.remove('focused');
            }
        });
    });

    // ── Funciones auxiliares ───────────────
    function setLoading(loading) {
        if (!btnLogin) return;
        const btnText   = btnLogin.querySelector('.btn-text');
        const btnLoader = btnLogin.querySelector('.btn-loader');

        if (loading) {
            if (btnText)   btnText.style.opacity   = '0';
            if (btnLoader) btnLoader.style.display = 'block';
            btnLogin.disabled = true;
        } else {
            if (btnText)   btnText.style.opacity   = '1';
            if (btnLoader) btnLoader.style.display = 'none';
            btnLogin.disabled = false;
        }
    }

    function showAlert(message, type) {
        // Elimina alertas anteriores
        document.querySelectorAll('.alert-login').forEach(el => el.remove());

        const div = document.createElement('div');
        div.className = `alert-login alert-login-${type}`;

        const icon = type === 'success'
            ? 'fa-check-circle'
            : 'fa-times-circle';

        div.innerHTML = `<i class="fas ${icon} me-2"></i>${message}`;

        // Inserta antes del formulario
        loginForm.insertAdjacentElement('beforebegin', div);

        // Auto-elimina en 4 segundos solo si es error
        if (type === 'error') {
            setTimeout(() => div.remove(), 4000);
        }
    }

})();