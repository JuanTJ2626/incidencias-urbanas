<?php

namespace App\Http\Controllers;

use App\Services\TrabajadorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrabajadorController extends Controller
{
    public function __construct(private TrabajadorService $trabajadorService) {}

    public function dashboard()
    {
        $tareas = $this->trabajadorService->misIncidencias(auth()->id());
        $nuevas = $tareas->where('estatus', 'en proceso')->count();

        return Inertia::render('trabajador/Dashboard', [
            'user'   => auth()->user(),
            'tareas' => $tareas,
            'nuevas' => $nuevas,
        ]);
    }

    public function tareas()
    {
        $tareas = $this->trabajadorService->misIncidencias(auth()->id());
        $nuevas = $tareas->where('estatus', 'en proceso')->count();

        return Inertia::render('trabajador/Tareas', [
            'tareas' => $tareas,
            'nuevas' => $nuevas,
        ]);
    }

    public function reportes()
    {
        return Inertia::render('trabajador/Reportes', [
            'cerradas' => $this->trabajadorService->cerradas(auth()->id()),
        ]);
    }

    /**
     * Cierra una orden: sube evidencia fotográfica + coordenadas.
     * Llamado vía axios desde Tareas.vue → debe retornar JSON.
     */
    public function cerrarOrden(Request $request, $id)
    {
        $request->validate([
            'foto_despues' => 'required|image|max:5120',
            'lat_cierre'   => 'required|numeric',
            'lng_cierre'   => 'required|numeric',
            'notas_cierre' => 'nullable|string|max:500',
        ]);

        try {
            $result = $this->trabajadorService->cerrarOrden(
                (int) $id,
                auth()->id(),
                $request->file('foto_despues'),
                (float) $request->lat_cierre,
                (float) $request->lng_cierre,
                $request->notas_cierre
            );
            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }
}

