<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Dashboard principal del administrador
     * Muestra lista de usuarios con sus roles
     */
    public function dashboard()
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,
                'created_at' => $user->created_at->format('d/m/Y'),
            ];
        });

        $roles = Role::all();

        return Inertia::render('admin/Dashboard', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Crear un nuevo usuario desde el panel de admin
     */
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'rol' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'rol' => $data['rol'],
        ]);

        // Attach role
        $role = Role::firstOrCreate(['nombre' => $data['rol']]);
        $user->roles()->attach($role->id);

        return redirect()->back()->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Eliminar un usuario
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // No permitir eliminarse a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->back()->withErrors(['error' => 'No puedes eliminarte a ti mismo.']);
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->back()->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Actualizar un usuario existente
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'rol' => 'required|string|max:100',
        ];

        // Solo validar password si se envió
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $data = $validator->validated();

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->rol = $data['rol'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Update role relationship
        $role = Role::firstOrCreate(['nombre' => $data['rol']]);
        $user->roles()->sync([$role->id]);

        return redirect()->back()->with('success', 'Usuario actualizado exitosamente.');
    }

    public function testing()
    {
        return Inertia::render('admin/Test');
    }

    /**
     * Renderiza la vista de Gestión de Usuarios
     */
    public function usersView()
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol' => $user->rol,
                'created_at' => $user->created_at->format('d/m/Y'),
            ];
        });

        $roles = Role::all();

        return Inertia::render('admin/GestionUsuarios', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Renderiza la vista de Reportes (simple)
     */
    public function reportesView()
    {
        return Inertia::render('admin/Reportes');
    }
}
