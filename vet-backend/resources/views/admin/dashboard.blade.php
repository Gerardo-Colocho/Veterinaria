<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Veterinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #F8FAFC; }
        .navbar-custom { background-color: #0D9488; }
        .card-custom { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-teal { background-color: #0D9488; color: white; }
        .btn-teal:hover { background-color: #0F766E; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom px-4 mb-4">
    <a class="navbar-brand fw-bold" href="#">🐾 VetCare Admin Panel</a>
</nav>

<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Tabla de Citas -->
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <h4 class="fw-bold mb-3">📅 Citas Solicitadas desde App Móvil</h4>
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
                                <td><strong>{{ $cita->mascota->dueno->name ?? 'N/D' }}</strong></td>
                                <td>🐾 {{ $cita->mascota->nombre ?? 'N/D' }}</td>
                                <td>{{ $cita->fecha_hora }}</td>
                                <td>{{ $cita->motivo }}</td>
                                <td>
                                    <span class="badge {{ $cita->estado == 'completada' ? 'bg-success' : ($cita->estado == 'cancelada' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ ucfirst($cita->estado ?? 'pendiente') }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('admin.citas.status', $cita->id) }}" method="POST" class="d-flex gap-1">
                                        @csrf
                                        <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="pendiente" {{ $cita->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="completada" {{ $cita->estado == 'completada' ? 'selected' : '' }}>Completada</option>
                                            <option value="cancelada" {{ $cita->estado == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay citas registradas en el sistema.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Formulario para Registrar Historial Médico -->
        <div class="col-lg-4">
            <div class="card card-custom p-4">
                <h4 class="fw-bold mb-3">🩺 Registrar Diagnóstico</h4>
                <form action="{{ route('admin.historiales.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Seleccionar Mascota</label>
                        <select name="mascota_id" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($mascotas as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }} ({{ $m->especie }}) - Dueno: {{ $m->dueno->name ?? 'N/D' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Diagnóstico</label>
                        <textarea name="diagnostico" class="form-control" rows="2" placeholder="Ej. Chequeo general, alergia..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tratamiento Prescrito</label>
                        <textarea name="tratamiento" class="form-control" rows="2" placeholder="Ej. Antiestamínico cada 12 horas"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Vacuna Aplicada (Opcional)</label>
                        <input type="text" name="vacuna" class="form-control" placeholder="Ej. Rabia, Múltiple">
                    </div>

                    <button type="submit" class="btn btn-teal w-100 fw-bold">Guardar Historial</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>