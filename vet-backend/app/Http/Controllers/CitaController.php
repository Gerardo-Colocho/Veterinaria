<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        // Admin ve todas las citas globales, el cliente ve solo sus citas
        if ($request->user()->rol === 'admin') {
            return response()->json(Cita::with(['cliente', 'mascota'])->get(), 200);
        }
        return response()->json(Cita::where('user_id', $request->user()->id)->with('mascota')->get(), 200);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'fecha_hora' => 'required|date',
            'motivo' => 'required|string'
        ]);

        $cita = Cita::create([
            'user_id' => $request->user()->id,
            'mascota_id' => $fields['mascota_id'],
            'fecha_hora' => $fields['fecha_hora'],
            'motivo' => $fields['motivo'],
            'estado' => 'pendiente'
        ]);

        return response()->json([
            'message' => 'Cita solicitada con éxito',
            'cita' => $cita
        ], 201);
    }

    public function update(Request $request, Cita $cita)
    {
        // El admin cambia estados: pendiente, confirmada, atendida, cancelada
        $fields = $request->validate([
            'estado' => 'required|in:pendiente,confirmada,atendida,cancelada',
            'fecha_hora' => 'nullable|date',
            'motivo' => 'nullable|string'
        ]);

        $cita->update($fields);

        return response()->json([
            'message' => 'Cita actualizada con éxito',
            'cita' => $cita
        ], 200);
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();
        return response()->json(['message' => 'Cita eliminada'], 200);
    }
}