<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - VetCare</title>
    <!-- Script de autenticación inmediata para evitar parpadeos -->
    <script>
        if (!localStorage.getItem('vet_token')) {
            window.location.href = '/login';
        }
    </script>
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
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            background: #FFFFFF;
        }
        .btn-teal {
            background-color: #0D9488;
            color: #ffffff;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-teal:hover {
            background-color: #0F766E;
            color: #ffffff;
        }
        .badge-pendiente { background-color: #EAB308; color: #000; }
        .badge-completada { background-color: #16A34A; }
        .badge-cancelada { background-color: #DC2626; }
    </style>
</head>
<body>

    <!-- Header / Navbar con Navegación Completa -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 shadow-sm mb-4">
        <a class="navbar-brand fw-bold" href="/admin">🐾 VetCare Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-2">
                    <a class="nav-link active fw-semibold" href="/admin">
                        <i class="fas fa-calendar-alt me-1"></i> Citas y Diagnósticos
                    </a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link text-white fw-semibold" href="/admin/usuarios">
                        <i class="fas fa-users-cog me-1"></i> Gestión de Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <button class="btn btn-outline-light btn-sm px-3" onclick="logout()">
                        <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                    </button>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <!-- Mensajes de Alerta -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Columna Izquierda: Citas Solicitadas -->
            <div class="col-lg-7">
                <div class="card card-custom p-4">
                    <h4 class="fw-bold text-dark mb-4">📅 Citas Solicitadas desde App Móvil</h4>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Mascota</th>
                                    <th>Fecha / Hora</th>
                                    <th>Motivo</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($citas as $cita)
                                    <tr>
                                        <td class="fw-semibold">{{ $cita->mascota->dueno->name ?? 'Usuario' }}</td>
                                        <td>🐾 {{ $cita->mascota->nombre ?? 'Mascota' }}</td>
                                        <td><small>{{ $cita->fecha_hora }}</small></td>
                                        <td>{{ $cita->motivo }}</td>
                                        <td>
                                            @php
                                                $estado = strtolower($cita->estado);
                                                $badgeClass = $estado === 'completada' ? 'badge-completada' : ($estado === 'cancelada' ? 'badge-cancelada' : 'badge-pendiente');
                                            @endphp
                                            <span class="badge {{ $badgeClass }} px-2 py-1 text-capitalize">{{ $cita->estado }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.citas.status', $cita->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="Pendiente" {{ $cita->estado == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                    <option value="Completada" {{ $cita->estado == 'Completada' ? 'selected' : '' }}>Completada</option>
                                                    <option value="Cancelada" {{ $cita->estado == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No hay citas registradas en el sistema.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Registrar Diagnóstico -->
            <div class="col-lg-5">
                <div class="card card-custom p-4">
                    <h4 class="fw-bold text-dark mb-4">🩺 Registrar Diagnóstico</h4>
                    <form action="{{ route('admin.historiales.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Seleccionar Mascota</label>
                            <select name="mascota_id" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                @foreach($mascotas as $m)
                                    <option value="{{ $m->id }}">{{ $m->nombre }} ({{ $m->especie }}) - Dueño: {{ $m->dueno->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Diagnóstico</label>
                            <textarea name="diagnostico" class="form-control" rows="3" required placeholder="Ej. Chequeo general, alergia..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tratamiento Prescrito</label>
                            <textarea name="tratamiento" class="form-control" rows="3" placeholder="Ej. Antiestamínico cada 12 horas"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Vacuna Aplicada (Opcional)</label>
                            <input type="text" name="vacuna" class="form-control" placeholder="Ej. Rabia, Múltiple">
                        </div>

                        <button type="submit" class="btn btn-teal w-100 py-2 fw-bold">Guardar Historial</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function logout() {
            localStorage.removeItem('vet_token');
            localStorage.removeItem('vet_user');
            window.location.href = '/login';
        }
    </script>
    
</body>
</html>