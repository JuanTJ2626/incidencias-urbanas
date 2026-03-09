<?php

namespace App\Http\Controllers;

use App\Services\IncidenciaService;
use App\Models\Incidencias;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncidenciasController extends Controller
{
    public function __construct(private IncidenciaService $incidenciaService) {}

    public function index()
    {
        return response()->json(Incidencias::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_ciudadano' => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:100',
            'direccion'        => 'required|string',
            'latitud'          => 'nullable|numeric',
            'longitud'         => 'nullable|numeric',
            'tipo_incidencia'  => 'required|string',
            'descripcion'      => 'nullable|string',
            'estatus'          => 'required|string',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $incidencia = $this->incidenciaService->crear($data, $request->file('foto'));

        return response()->json($incidencia, 201);
    }

    public function show($id)
    {
        return response()->json(Incidencias::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre_ciudadano' => 'nullable|string|max:100',
            'email'            => 'nullable|email|max:100',
            'direccion'        => 'sometimes|string',
            'latitud'          => 'nullable|numeric',
            'longitud'         => 'nullable|numeric',
            'tipo_incidencia'  => 'sometimes|string',
            'descripcion'      => 'nullable|string',
            'estatus'          => 'sometimes|string',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $incidencia = $this->incidenciaService->actualizar((int) $id, $data, $request->file('foto'));

        return response()->json($incidencia);
    }

    public function destroy($id)
    {
        $this->incidenciaService->eliminar((int) $id);

        return response()->json(['message' => 'Incidencia eliminada']);
    }

    public function showIncidencias()
    {
        return Inertia::render('GestionReporte', [
            'data' => Incidencias::latest()->get(),
        ]);
    }
}
