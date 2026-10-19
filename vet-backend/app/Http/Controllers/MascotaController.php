<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use Illuminate\Http\Request;

class MascotaController extends Controller
{
    public function index(Request $request)
    {
        // El admin ve todas las mascotas, el cliente solo las suyas
        if ($request->user()->rol === 'admin') {
            return response()->json(Mascota::with('dueno')->get(), 200);
        }
        return response()->json($request->user()->mascotas, 200);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'nombre' => 'required|string',
            'especie' => 'required|string',
            'raza' => 'nullable|string',
            'edad_meses' => 'nullable|integer',
            'peso' => 'nullable|numeric',
            'foto_url' => 'nullable|string'
        ]);

        $mascota = $request->user()->mascotas()->create($fields);

        return response()->json([
            'message' => 'Mascota registrada con éxito',
            'mascota' => $mascota
        ], 201);
    }

    public function show(Mascota $mascota)
    {
        return response()->json($mascota->load('historiales', 'citas'), 200);
    }

    public function update(Request $request, Mascota $mascota)
    {
        $mascota->update($request->all());
        return response()->json([
            'message' => 'Mascota actualizada correctamente',
            'mascota' => $mascota
        ], 200);
    }

    public function destroy(Mascota $mascota)
    {
        $mascota->delete();
        return response()->json(['message' => 'Mascota eliminada'], 200);
    }
}