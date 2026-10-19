<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\HistorialMedico;
use Illuminate\Http\Request;

class AdminWebController extends Controller
{
    public function index()
    {
        // Se carga la mascota y su relación con el dueño (dueno)
        $citas = Cita::with(['mascota.dueno'])->latest()->get();
        $mascotas = Mascota::with('dueno')->get();
        
        return view('admin.dashboard', compact('citas', 'mascotas'));
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
        'mascota_id' => 'required',
        'diagnostico' => 'required',
    ]);

    HistorialMedico::create([
        'mascota_id' => $request->mascota_id,
        'diagnostico' => $request->diagnostico,
        'tratamiento' => $request->tratamiento,
        'vacuna' => $request->vacuna,
        'fecha_atencion' => now(), // <--- Asigna la fecha/hora actual
    ]);

    return redirect()->back()->with('success', 'Historial médico registrado con éxito.');
}
}