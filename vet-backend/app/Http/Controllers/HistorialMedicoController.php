<?php

namespace App\Http\Controllers;

use App\Models\HistorialMedico;
use Illuminate\Http\Request;

class HistorialMedicoController extends Controller
{
    public function index()
    {
        return response()->json(HistorialMedico::with('mascota')->get(), 200);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'diagnostico' => 'required|string',
            'tratamiento' => 'required|string',
            'fecha_atencion' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        $historial = HistorialMedico::create($fields);

        return response()->json([
            'message' => 'Control médico registrado con éxito',
            'historial' => $historial
        ], 201);
    }

    public function show($mascota_id)
    {
        $historiales = HistorialMedico::where('mascota_id', $mascota_id)->get();
        return response()->json($historiales, 200);
    }
}