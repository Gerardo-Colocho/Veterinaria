<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\HistorialMedico;
use App\Models\User;
use Illuminate\Http\Request;

class AdminWebController extends Controller
{
    public function index()
    {
        // Cargar citas y mascotas con sus relaciones
        $citas = Cita::with(['mascota.dueno'])->latest()->get();
        $mascotas = Mascota::with('dueno')->get();
        
        return view('admin.dashboard', compact('citas', 'mascotas'));
    }

    // Vista para la administración de usuarios y roles
    public function usuarios()
    {
        return view('admin.usuarios');
    }

    public function updateCitaStatus(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);
        $cita->estado = $request->estado;
        $cita->save();

        return redirect()->back()->with('success', 'Estado de cita actualizado.');
    }

    public function storeHistorial(Request $request)
{
    $request->validate([
        'mascota_id'  => 'required|exists:mascotas,id',
        'diagnostico' => 'required|string',
        'tratamiento' => 'nullable|string',
        'vacuna'      => 'nullable|string',
    ]);

    // Si enviaron una vacuna, la anexamos al tratamiento para no romper la estructura de la BD
    $tratamientoFinal = $request->tratamiento;
    if ($request->filled('vacuna')) {
        $tratamientoFinal = ($tratamientoFinal ? $tratamientoFinal . " | " : "") . "Vacuna: " . $request->vacuna;
    }

    // Datos a guardar compatibles con la tabla historial_medicos
    $data = [
        'mascota_id'     => $request->mascota_id,
        'diagnostico'    => $request->diagnostico,
        'tratamiento'    => $tratamientoFinal,
        'fecha_atencion' => now(),
    ];

    // Si la columna veterinario_id o user_id existe en la tabla, asociamos al usuario actual
    if (\Illuminate\Support\Facades\Schema::hasColumn('historial_medicos', 'veterinario_id')) {
        $data['veterinario_id'] = auth()->id() ?? 1;
    } elseif (\Illuminate\Support\Facades\Schema::hasColumn('historial_medicos', 'user_id')) {
        $data['user_id'] = auth()->id() ?? 1;
    }

    \App\Models\HistorialMedico::create($data);

    return redirect()->back()->with('success', '¡Historial médico registrado con éxito!');
}
}