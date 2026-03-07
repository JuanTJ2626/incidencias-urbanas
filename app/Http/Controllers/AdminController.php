<?php

namespace App\Http\Controllers;

use App\Models\Incidencias;
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
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'rol'        => $user->rol,
                'activo'     => (bool) $user->activo,
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
     * Activar / desactivar un usuario (toggle)
     */
    public function toggleActivo($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes desactivarte a ti mismo.']);
        }
        $user->update(['activo' => !$user->activo]);
        $estado = $user->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$estado} correctamente.");
    }

    /**
     * Renderiza la vista de Reportes (simple)
     */
    public function reportesView()
    {
        return Inertia::render('admin/Reportes');
    }

    /**
     * Vista de gestión de incidencias (validar, rechazar, asignar)
     */
    public function gestionIncidencias()
    {
        $incidencias = Incidencias::with('trabajador')->orderBy('created_at', 'desc')->get()->map(function ($inc) {
            return [
                'id'               => $inc->id,
                'nombre_ciudadano' => $inc->nombre_ciudadano,
                'email'            => $inc->email,
                'direccion'        => $inc->direccion,
                'tipo_incidencia'  => $inc->tipo_incidencia,
                'descripcion'      => $inc->descripcion,
                'estatus'          => $inc->estatus,
                'foto'             => $inc->foto,
                'latitud'          => $inc->latitud  ? (float) $inc->latitud  : null,
                'longitud'         => $inc->longitud ? (float) $inc->longitud : null,
                'asignado_a'       => $inc->asignado_a,
                'trabajador_nombre'=> $inc->trabajador?->name,
                'created_at'       => $inc->created_at?->format('d/m/Y H:i'),
            ];
        });

        $trabajadores = User::whereIn('rol', ['trabajador', 'contratista', 'worker'])
            ->where('activo', true)
            ->select('id', 'name', 'email', 'rol')
            ->get();

        return Inertia::render('admin/GestionIncidencias', [
            'incidencias'  => $incidencias,
            'trabajadores' => $trabajadores,
        ]);
    }

    /**
     * Crear una incidencia manualmente desde el panel admin
     */
    public function storeIncidencia(Request $request)
    {
        $data = $request->validate([
            'nombre_ciudadano' => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'direccion'        => 'required|string|max:500',
            'tipo_incidencia'  => 'required|string|max:255',
            'descripcion'      => 'nullable|string|max:1000',
            'estatus'          => 'required|string',
            'latitud'          => 'nullable|numeric',
            'longitud'         => 'nullable|numeric',
            'foto'             => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('incidencias', 'public');
        }

        Incidencias::create($data);
        return back()->with('success', 'Incidencia creada correctamente.');
    }

    /**
     * Editar datos de una incidencia
     */
    public function updateIncidencia(Request $request, $id)
    {
        $inc = Incidencias::findOrFail($id);

        $data = $request->validate([
            'nombre_ciudadano' => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'direccion'        => 'required|string|max:500',
            'tipo_incidencia'  => 'required|string|max:255',
            'descripcion'      => 'nullable|string|max:1000',
            'estatus'          => 'required|string',
            'latitud'          => 'nullable|numeric',
            'longitud'         => 'nullable|numeric',
            'foto'             => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('incidencias', 'public');
        } else {
            unset($data['foto']);
        }

        $inc->update($data);
        return back()->with('success', 'Incidencia actualizada correctamente.');
    }

    /**
     * Eliminar una incidencia
     */
    public function destroyIncidencia($id)
    {
        Incidencias::findOrFail($id)->delete();
        return back()->with('success', 'Incidencia eliminada.');
    }

    /**
     * Cambiar el estatus de una incidencia (no permite poner 'resuelto' directamente;
     * ese estado solo lo genera revisarCierre).
     */
    public function cambiarEstatus(Request $request, $id)
    {
        $request->validate(['estatus' => 'required|string']);
        $incidencia = Incidencias::findOrFail($id);
        $incidencia->update(['estatus' => $request->estatus]);
        return back()->with('success', 'Estatus actualizado.');
    }

    /**
     * Revisar el cierre enviado por el trabajador.
     * accion = 'aprobar'  → estatus resuelto
     * accion = 'rechazar' → estatus en proceso + guarda motivo_rechazo
     */
    public function revisarCierre(Request $request, $id)
    {
        $request->validate([
            'accion'         => 'required|in:aprobar,rechazar',
            'motivo_rechazo' => 'required_if:accion,rechazar|nullable|string|max:500',
        ]);

        $inc = Incidencias::findOrFail($id);

        if ($request->accion === 'aprobar') {
            $inc->update([
                'estatus'        => 'resuelto',
                'motivo_rechazo' => null,
            ]);
            return back()->with('success', 'Orden aprobada como resuelta.');
        }

        // rechazar → vuelve a en proceso para que el trabajador corrija
        $inc->update([
            'estatus'        => 'en proceso',
            'motivo_rechazo' => $request->motivo_rechazo,
            'foto_despues'   => null,
            'cerrado_en'     => null,
        ]);
        return back()->with('success', 'Orden rechazada. El trabajador deberá corregir.');;
    }

    /**
     * Asignar un trabajador a una incidencia (genera la orden de trabajo)
     */
    public function asignarTrabajador(Request $request, $id)
    {
        $request->validate([
            'asignado_a' => 'required|exists:users,id',
        ]);
        $incidencia = Incidencias::findOrFail($id);
        $incidencia->update([
            'asignado_a' => $request->asignado_a,
            'estatus'    => 'en proceso',
        ]);
        return back()->with('success', 'Trabajador asignado correctamente.');
    }

    /**
    /**
     * Mapa de calor: todas las incidencias con coords para Google Maps
     */
    public function mapaCalor()
    {
        $incidencias = Incidencias::orderBy('created_at', 'desc')
            ->get()
            ->map(fn($inc) => [
                'id'               => $inc->id,
                'tipo_incidencia'  => $inc->tipo_incidencia,
                'descripcion'      => $inc->descripcion,
                'direccion'        => $inc->direccion,
                'estatus'          => $inc->estatus,
                'foto'             => $inc->foto,
                'latitud'          => $inc->latitud  ? (float) $inc->latitud  : null,
                'longitud'         => $inc->longitud ? (float) $inc->longitud : null,
                'nombre_ciudadano' => $inc->nombre_ciudadano,
                'created_at'       => $inc->created_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('admin/Mapa', [
            'incidencias' => $incidencias,
        ]);
    }

    /**
     * Monitoreo: listado de incidencias con evidencias (fotos) de trabajadores
     */
    public function monitoreo()
    {
        $incidencias = Incidencias::with('trabajador')
            ->whereNotNull('asignado_a')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($inc) {
                return [
                    'id'               => $inc->id,
                    'tipo_incidencia'  => $inc->tipo_incidencia,
                    'direccion'        => $inc->direccion,
                    'descripcion'      => $inc->descripcion,
                    'estatus'          => $inc->estatus,
                    'foto'             => $inc->foto,
                    'foto_despues'     => $inc->foto_despues,
                    'lat_cierre'       => $inc->lat_cierre,
                    'lng_cierre'       => $inc->lng_cierre,
                    'notas_cierre'     => $inc->notas_cierre,
                    'cerrado_en'       => $inc->cerrado_en?->format('d/m/Y H:i'),
                    'motivo_rechazo'   => $inc->motivo_rechazo,
                    'trabajador_nombre'=> $inc->trabajador?->name,
                    'trabajador_email' => $inc->trabajador?->email,
                    'updated_at'       => $inc->updated_at?->format('d/m/Y H:i'),
                ];
            });

        return Inertia::render('admin/Monitoreo', [
            'incidencias' => $incidencias,
        ]);
    }
}
