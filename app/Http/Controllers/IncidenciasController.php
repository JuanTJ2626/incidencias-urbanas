<?php

namespace App\Http\Controllers;

use App\Models\Incidencias;
use Illuminate\Http\Request;

class IncidenciasController extends Controller
{

    public function index()
    {
        $incidencias = Incidencias::all();
        return response()->json($incidencias);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_ciudadano' => 'nullable|string|max:100',
            'email' => 'nullable|string|max:100',
            'direccion' => 'required|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'tipo_incidencia' => 'required|string',
            'descripción' => 'nullable|string',
            'estatus' => 'required|string',
            'foto' => 'nullable|string',
        ]);

        $incidencia = Incidencias::create($data);
        return response()->json($incidencia, 201);
    }

    public function show($id)
    {
        $incidencia = Incidencias::findOrFail($id);
        return response()->json($incidencia);
    }

    public function update(Request $request, $id)
    {
        $incidencia = Incidencias::findOrFail($id);

        $data = $request->validate([
            'nombre_ciudadano' => 'nullable|string|max:100',
            'email' => 'nullable|string|max:100',
            'direccion' => 'sometimes|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'tipo_incidencia' => 'sometimes|string',
            'descripción' => 'nullable|string',
            'estatus' => 'sometimes|string',
            'foto' => 'nullable|string',
        ]);

        $incidencia->update($data);
        return response()->json($incidencia);
    }

    public function destroy($id)
    {
        $incidencia = Incidencias::findOrFail($id);
        $incidencia->delete();

        return response()->json(['message' => 'Incidencia eliminada']);
    }
}
