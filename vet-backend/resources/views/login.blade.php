<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - VetCare</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F8FAFC;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            padding: 32px 28px;
        }
        .btn-teal {
            background-color: #0D9488;
            color: #ffffff;
            border-radius: 10px;
            font-weight: 600;
            padding: 12px;
        }
        .btn-teal:hover {
            background-color: #0F766E;
            color: #ffffff;
        }
        .form-control:focus {
            border-color: #0D9488;
            box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.25);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark">🐾 VetCare</h3>
            <p class="text-muted">Ingresa a tu cuenta para continuar</p>
        </div>

        <div id="alert-error" class="alert alert-danger d-none" role="alert"></div>

        <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary">Correo Electrónico</label>
                <input type="email" id="email" class="form-control" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary">Contraseña</label>
                <input type="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" id="btnLogin" class="btn btn-teal w-100">
                Iniciar Sesión
            </button>
        </form>
    </div>

    <script>
        async function handleLogin(e) {
            e.preventDefault();
            const alertError = document.getElementById('alert-error');
            const btnLogin = document.getElementById('btnLogin');
            alertError.classList.add('d-none');

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            btnLogin.disabled = true;
            btnLogin.innerText = 'Cargando...';

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    // Guardar Token en localStorage
                    localStorage.setItem('vet_token', data.token);
                    localStorage.setItem('vet_user', JSON.stringify(data.user));

                    // Redirección según el Rol devuelto por el Backend
                    const rol = data.user.rol;

                    if (rol === 'admin') {
                        window.location.href = '/admin';
                    } else if (rol === 'veterinario') {
                        window.location.href = '/admin';
                    } else {
                        alert('Bienvenido cliente. Tu cuenta se gestiona desde la App Móvil.');
                        window.location.href = '/login';
                    }
                } else {
                    alertError.innerText = data.message || 'Credenciales incorrectas';
                    alertError.classList.remove('d-none');
                }
            } catch (err) {
                alertError.innerText = 'Error de conexión con el servidor';
                alertError.classList.remove('d-none');
            } finally {
                btnLogin.disabled = false;
                btnLogin.innerText = 'Iniciar Sesión';
            }
        }
    </script>
</body>
</html>