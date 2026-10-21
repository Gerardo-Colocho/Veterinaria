<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use Illuminate\Http\Request;

class MascotaController extends Controller
{
    // Listar mascotas del usuario autenticado
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $mascotas = Mascota::where('user_id', $userId)->get();

        return response()->json($mascotas, 200);
    }

    // Registrar una nueva mascota
    public function store(Request $request)
    {
        $fields = $request->validate([
            'nombre'  => 'required|string|max:255',
            'especie' => 'required|string|max:100',
            'raza'    => 'nullable|string|max:100',
            'edad'    => 'nullable|numeric',
            'peso'    => 'nullable|numeric',
        ]);

        $mascota = Mascota::create([
            'user_id'    => $request->user()->id,
            'nombre'     => $fields['nombre'],
            'especie'    => $fields['especie'],
            'raza'       => $fields['raza'] ?? null,
            'edad_meses' => $fields['edad'] ?? null,
            'peso'       => $fields['peso'] ?? null,
        ]);

        return response()->json([
            'message' => 'Mascota registrada con éxito',
            'mascota' => $mascota
        ], 201);
    }

    // Eliminar mascota
    public function destroy(Request $request, $id)
    {
        $mascota = Mascota::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$mascota) {
            return response()->json(['message' => 'Mascota no encontrada o no autorizada'], 404);
        }

        $mascota->delete();
        return response()->json(['message' => 'Mascota eliminada con éxito'], 200);
    }
}