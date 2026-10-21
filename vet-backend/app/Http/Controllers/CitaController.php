<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->rol === 'admin') {
            return response()->json(Cita::with(['cliente', 'mascota'])->get(), 200);
        }
        return response()->json(Cita::where('user_id', $request->user()->id)->with('mascota')->get(), 200);
    }

    // Nuevo método para consultar qué horas están libres en un día específico
    public function consultarDisponibilidad(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date_format:Y-m-d'
        ]);

        // Obtener las citas agendadas que no estén canceladas
        $horasOcupadas = Cita::whereDate('fecha_hora', $request->fecha)
            ->whereNotIn('estado', ['Cancelada', 'cancelada'])
            ->pluck('fecha_hora')
            ->map(function ($dateTime) {
                return date('H:i', strtotime($dateTime));
            });

        // Horarios de atención definidos por la veterinaria (8 AM a 4 PM)
        $horariosLaborales = [
            '08:00', '09:00', '10:00', '11:00', 
            '13:00', '14:00', '15:00', '16:00'
        ];

        // Retornar solo los horarios que no están tomados
        $horariosDisponibles = array_values(array_diff($horariosLaborales, $horasOcupadas->toArray()));

        return response()->json([
            'fecha' => $request->fecha,
            'horarios_disponibles' => $horariosDisponibles
        ], 200);
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
            'estado' => 'Pendiente'
        ]);

        return response()->json([
            'message' => 'Cita solicitada con éxito',
            'cita' => $cita
        ], 201);
    }

    public function update(Request $request, Cita $cita)
    {
        $fields = $request->validate([
            'estado' => 'required|string',
            'fecha_hora' => 'nullable|date',
            'motivo' => 'nullable|string',
            'motivo_cancelacion' => 'nullable|string'
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