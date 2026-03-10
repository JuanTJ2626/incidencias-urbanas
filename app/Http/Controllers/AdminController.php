<?php

namespace App\Http\Controllers;

use App\Models\Incidencias;
use App\Models\Role;
use App\Models\User;
use App\Services\IncidenciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(private IncidenciaService $incidenciaService) {}

    /**
     * Dashboard principal del administrador
     */
    public function dashboard()
    {
        $users = User::all()->map(fn($u) => [
            'id'         => $u->id,
            'name'       => $u->name,
            'email'      => $u->email,
            'rol'        => $u->rol,
            'created_at' => $u->created_at->format('d/m/Y'),
        ]);

        return Inertia::render('admin/Dashboard', [
            'users'       => $users,
            'roles'       => Role::all(),
            'incidencias' => $this->incidenciaService->listar(),
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
     * Eliminar múltiples usuarios a la vez
     */
    public function bulkDeleteUsers(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        $ids = $request->ids;
        // Evitar que el admin se borre a sí mismo en lote si por error se seleccionó
        $ids = array_filter($ids, fn($id) => $id != auth()->id());

        User::whereIn('id', $ids)->delete();

        return back()->with('success', 'Usuarios eliminados correctamente.');
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
        return Inertia::render('admin/GestionIncidencias', [
            'incidencias'  => $this->incidenciaService->listar(),
            'trabajadores' => $this->incidenciaService->trabajadoresDisponibles(),
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

        $this->incidenciaService->crear($data, $request->file('foto'));

        return back()->with('success', 'Incidencia creada correctamente.');
    }

    /**
     * Editar datos de una incidencia
     */
    public function updateIncidencia(Request $request, $id)
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

        $this->incidenciaService->actualizar((int) $id, $data, $request->file('foto'));

        return back()->with('success', 'Incidencia actualizada correctamente.');
    }

    /**
     * Eliminar múltiples incidencias a la vez
     */
    public function bulkDeleteIncidencias(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:incidencias,id'
        ]);

        Incidencias::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Incidencias eliminadas correctamente.']);
    }

    /**
     * Eliminar una incidencia
     */
    public function destroyIncidencia($id)
    {
        $this->incidenciaService->eliminar((int) $id);

        return response()->json(['message' => 'Incidencia eliminada correctamente.']);
    }

    /**
     * Cambia el estatus de una incidencia según el flujo de negocio.
     * Delega toda la lógica a IncidenciaService.
     */
    public function cambiarEstatus(Request $request, $id)
    {
        $request->validate(['estatus' => 'required|string']);

        try {
            $result = $this->incidenciaService->cambiarEstatus((int) $id, $request->estatus);
            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    /**
     * Revisa la evidencia de cierre enviada por el trabajador.
     * Delega toda la lógica a IncidenciaService.
     */
    public function revisarCierre(Request $request, $id)
    {
        $request->validate([
            'accion'         => 'required|in:aprobar,rechazar',
            'motivo_rechazo' => 'required_if:accion,rechazar|nullable|string|max:500',
        ]);

        try {
            $result = $this->incidenciaService->revisarCierre(
                (int) $id,
                $request->accion,
                $request->motivo_rechazo
            );
            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    /**
     * Asigna un trabajador a una incidencia.
     * Delega toda la lógica a IncidenciaService.
     */
    public function asignarTrabajador(Request $request, $id)
    {
        $request->validate([
            'asignado_a' => 'required|exists:users,id',
        ]);

        try {
            $result = $this->incidenciaService->asignarTrabajador((int) $id, (int) $request->asignado_a);
            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    /**
     * Mapa de calor: todas las incidencias con coords para Google Maps
     */
    public function mapaCalor()
    {
        $incidencias = Incidencias::with('trabajador')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($inc) => [
                'id'               => $inc->id,
                'tipo_incidencia'  => $inc->tipo_incidencia,
                'descripcion'      => $inc->descripcion,
                'direccion'        => $inc->direccion,
                'estatus'          => $inc->estatus,
                'email'            => $inc->email,
                'foto'             => $inc->foto,
                'foto_despues'     => $inc->foto_despues,
                'latitud'          => $inc->latitud  ? (float) $inc->latitud  : null,
                'longitud'         => $inc->longitud ? (float) $inc->longitud : null,
                'lat_cierre'       => $inc->lat_cierre ? (float) $inc->lat_cierre : null,
                'lng_cierre'       => $inc->lng_cierre ? (float) $inc->lng_cierre : null,
                'nombre_ciudadano' => $inc->nombre_ciudadano,
                'asignado_a'       => $inc->asignado_a,
                'trabajador_nombre'=> $inc->trabajador?->name,
                'trabajador_email' => $inc->trabajador?->email,
                'notas_cierre'     => $inc->notas_cierre,
                'motivo_rechazo'   => $inc->motivo_rechazo,
                'cerrado_en'       => $inc->cerrado_en?->format('d/m/Y H:i'),
                'created_at'       => $inc->created_at?->format('d/m/Y H:i'),
            ]);

        $trabajadores = $this->incidenciaService->trabajadoresDisponibles();

        return Inertia::render('admin/Mapa', [
            'incidencias'  => $incidencias,
            'trabajadores' => $trabajadores,
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

    /**
     * Dashboard de Gráficas: Estadísticas avanzadas y reportes descargables
     */
    public function graficas()
    {
        return Inertia::render('admin/DashboardGraficas', [
            'incidencias' => $this->incidenciaService->listar(),
            'totalUsers'  => User::count(),
            'userStats'   => User::selectRaw('rol, count(*) as total')->groupBy('rol')->get(),
        ]);
    }
}
