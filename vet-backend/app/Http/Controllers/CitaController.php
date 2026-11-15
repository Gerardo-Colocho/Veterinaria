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

    public function consultarDisponibilidad(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date_format:Y-m-d'
        ]);

        $horasOcupadas = Cita::whereDate('fecha_hora', $request->fecha)
            ->whereNotIn('estado', ['Cancelada', 'cancelada'])
            ->pluck('fecha_hora')
            ->map(function ($dateTime) {
                return date('H:i', strtotime($dateTime));
            });

        $horariosLaborales = [
            '08:00', '09:00', '10:00', '11:00', 
            '13:00', '14:00', '15:00', '16:00'
        ];

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

    // Actualización / Reagendamiento desde la App Móvil (Cliente)
    public function update(Request $request, Cita $cita)
    {
        $fields = $request->validate([
            'estado' => 'required|string',
            'fecha_hora' => 'nullable|date',
            'motivo' => 'nullable|string',
            'motivo_cancelacion' => 'nullable|string',
            'justificacion_cancelacion' => 'nullable|string'
        ]);

        if ($request->input('estado') === 'Cancelada') {
            $fields['cancelado_por'] = 'cliente';
            $justificacion = $request->input('justificacion_cancelacion') ?? $request->input('motivo_cancelacion') ?? 'Cancelada por el cliente desde la App';
            $fields['justificacion_cancelacion'] = $justificacion;
            $fields['motivo_cancelacion'] = $justificacion;
        } elseif ($request->input('estado') === 'Pendiente') {
            // Caso Reagendamiento: se resetean los campos de cancelación
            $fields['justificacion_cancelacion'] = null;
            $fields['motivo_cancelacion'] = null;
            $fields['cancelado_por'] = null;
        }

        $cita->update($fields);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cita actualizada con éxito',
                'cita' => $cita
            ], 200);
        }

        return back()->with('success', 'Estado de la cita actualizado con éxito.');
    }

    // Actualización desde el Panel Web (Administrador)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string',
            'justificacion_cancelacion' => 'nullable|string',
            'motivo_cancelacion' => 'nullable|string'
        ]);

        $cita = Cita::findOrFail($id);
        $cita->estado = $request->input('estado');

        if ($request->input('estado') === 'Cancelada') {
            $cita->cancelado_por = 'admin';
            $justificacion = $request->input('justificacion_cancelacion') ?? $request->input('motivo_cancelacion') ?? 'Cancelada por la administración de VetCare';
            $cita->justificacion_cancelacion = $justificacion;
            $cita->motivo_cancelacion = $justificacion;
        }

        $cita->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Estado actualizado con éxito',
                'cita' => $cita
            ], 200);
        }

        return back()->with('success', 'Estado de la cita actualizado correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();
        return response()->json(['message' => 'Cita eliminada'], 200);
    }
}