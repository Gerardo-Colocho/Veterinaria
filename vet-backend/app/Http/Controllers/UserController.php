<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Listar todos los usuarios
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'rol', 'created_at')->get();
        return response()->json($users, 200);
    }

    // Crear un nuevo usuario con rol específico
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:admin,veterinario,cliente'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'rol' => $fields['rol']
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => $user
        ], 201);
    }

    // Actualizar datos o rol de usuario (Protegiendo Admin)
    public function update(Request $request, User $user)
    {
        if ($user->rol === 'admin') {
            return response()->json(['message' => 'El usuario Administrador está protegido y no se puede modificar'], 403);
        }

        $fields = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'rol' => 'sometimes|in:admin,veterinario,cliente',
            'password' => 'nullable|string|min:6'
        ]);

        if (isset($fields['password'])) {
            $fields['password'] = Hash::make($fields['password']);
        }

        $user->update($fields);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user
        ], 200);
    }

    // Eliminar un usuario (Protegiendo Admin)
    public function destroy(Request $request, User $user)
    {
        if ($user->rol === 'admin') {
            return response()->json(['message' => 'El usuario Administrador está protegido y no puede ser eliminado'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado con éxito'], 200);
    }
}