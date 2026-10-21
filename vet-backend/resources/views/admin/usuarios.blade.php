<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios - VetCare</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #F8FAFC;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background-color: #0D9488;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .btn-teal {
            background-color: #0D9488;
            color: #ffffff;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-teal:hover {
            background-color: #0F766E;
            color: #ffffff;
        }
        .badge-admin { background-color: #8B5CF6; }
        .badge-veterinario { background-color: #0D9488; }
        .badge-cliente { background-color: #3B82F6; }
    </style>
</head>
<body>

    <!-- Navbar Header -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="/admin">🐾 VetCare Admin</a>
        <div class="ms-auto d-flex align-items-center text-white">
            <span class="me-3" id="user-display-name"><i class="fas fa-user-circle"></i> Admin</span>
            <a href="/admin" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Gestión de Usuarios y Roles</h2>
                <p class="text-muted">Administra los accesos y tipos de cuenta del sistema.</p>
            </div>
            <button class="btn btn-teal px-4 py-2" onclick="openCreateModal()">
                <i class="fas fa-plus me-2"></i> Nuevo Usuario
            </button>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Rol</th>
                            <th>Fecha de Registro</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="spinner-border text-teal" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para Crear / Editar Usuario -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content style-modal" style="border-radius: 12px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="userId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre Completo</label>
                            <input type="text" id="userName" class="form-control" required placeholder="Ej. Dr. Carlos Mendoza">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Correo Electrónico</label>
                            <input type="email" id="userEmail" class="form-control" required placeholder="ejemplo@veterinaria.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rol del Usuario</label>
                            <select id="userRole" class="form-select" required>
                                <option value="cliente">Cliente</option>
                                <option value="veterinario">Veterinario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3" id="passwordGroup">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <input type="password" id="userPassword" class="form-control" placeholder="Mínimo 6 caracteres">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-teal" onclick="saveUser()">Guardar Usuario</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const API_URL = '/api';
        const token = localStorage.getItem('vet_token');
        let userModal;

        document.addEventListener('DOMContentLoaded', () => {
            userModal = new bootstrap.Modal(document.getElementById('userModal'));
            loadUsers();
        });

        async function loadUsers() {
            try {
                const res = await fetch(`${API_URL}/users`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (res.status === 401 || res.status === 403) {
                    const fallbackRes = await fetch(`${API_URL}/users`);
                    if (!fallbackRes.ok) throw new Error('Error al obtener usuarios');
                    const users = await fallbackRes.json();
                    renderTable(users);
                    return;
                }

                const users = await res.json();
                renderTable(users);
            } catch (error) {
                console.error('Error al cargar usuarios:', error);
            }
        }

        function renderTable(users) {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = '';

            users.forEach(user => {
                const badgeClass = `badge-${user.rol}`;
                const isAdmin = user.rol === 'admin';

                const actionButtons = isAdmin ? `
                    <span class="badge bg-secondary px-2 py-1 text-white" title="El Administrador principal no se puede modificar">
                        <i class="fas fa-lock me-1"></i> Protegido
                    </span>
                ` : `
                    <button class="btn btn-outline-primary btn-sm me-1" onclick="openEditModal(${user.id}, '${user.name}', '${user.email}', '${user.rol}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteUser(${user.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                `;

                tbody.innerHTML += `
                    <tr>
                        <td>#${user.id}</td>
                        <td class="fw-semibold">${user.name}</td>
                        <td>${user.email}</td>
                        <td><span class="badge ${badgeClass} text-capitalize px-3 py-2">${user.rol}</span></td>
                        <td>${new Date(user.created_at).toLocaleDateString()}</td>
                        <td class="text-end">${actionButtons}</td>
                    </tr>
                `;
            });
        }

        function openCreateModal() {
            document.getElementById('userId').value = '';
            document.getElementById('userName').value = '';
            document.getElementById('userEmail').value = '';
            document.getElementById('userRole').value = 'cliente';
            document.getElementById('userPassword').value = '';
            document.getElementById('modalTitle').innerText = 'Nuevo Usuario';
            document.getElementById('userPassword').required = true;
            userModal.show();
        }

        function openEditModal(id, name, email, role) {
            document.getElementById('userId').value = id;
            document.getElementById('userName').value = name;
            document.getElementById('userEmail').value = email;
            document.getElementById('userRole').value = role;
            document.getElementById('userPassword').value = '';
            document.getElementById('modalTitle').innerText = 'Editar Usuario';
            document.getElementById('userPassword').required = false;
            userModal.show();
        }

        async function saveUser() {
            const id = document.getElementById('userId').value;
            const name = document.getElementById('userName').value;
            const email = document.getElementById('userEmail').value;
            const rol = document.getElementById('userRole').value;
            const password = document.getElementById('userPassword').value;

            if (!name || !email) {
                alert('Por favor completa todos los campos requeridos');
                return;
            }

            const isEdit = !!id;
            const url = isEdit ? `${API_URL}/users/${id}` : `${API_URL}/users`;
            const method = isEdit ? 'PUT' : 'POST';

            const payload = { name, email, rol };
            if (password) payload.password = password;

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (res.ok) {
                    userModal.hide();
                    loadUsers();
                } else {
                    const err = await res.json();
                    alert(err.message || 'Error al guardar el usuario');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function deleteUser(id) {
            if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) return;

            try {
                const res = await fetch(`${API_URL}/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    loadUsers();
                } else {
                    const err = await res.json();
                    alert(err.message || 'No se pudo eliminar el usuario');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
</body>
</html>