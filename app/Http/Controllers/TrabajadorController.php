<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Incidencias;
use Illuminate\Support\Facades\Storage;

class TrabajadorController extends Controller
{
    /** Solo las incidencias asignadas a este trabajador */
    private function misIncidencias()
    {
        return Incidencias::where('asignado_a', auth()->id())
            ->orderByRaw("CASE estatus
                WHEN 'en proceso' THEN 1
                WHEN 'pendiente' THEN 2
                WHEN 'resuelto' THEN 3
                ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($inc) {
                return array_merge($inc->toArray(), [
                    'foto_url'         => $inc->foto         ? Storage::url($inc->foto)         : null,
                    'foto_despues_url' => $inc->foto_despues ? Storage::url($inc->foto_despues) : null,
                ]);
            });
    }

    public function dashboard()
    {
        $tareas   = $this->misIncidencias();
        $nuevas   = $tareas->where('estatus', 'en proceso')->count();

        return Inertia::render('trabajador/Dashboard', [
            'user'    => auth()->user(),
            'tareas'  => $tareas,
            'nuevas'  => $nuevas,
        ]);
    }

    public function tareas()
    {
        $tareas  = $this->misIncidencias();
        $nuevas  = $tareas->where('estatus', 'en proceso')->count();

        return Inertia::render('trabajador/Tareas', [
            'tareas' => $tareas,
            'nuevas' => $nuevas,
        ]);
    }

    public function reportes()
    {
        $cerradas = Incidencias::where('asignado_a', auth()->id())
            ->where('estatus', 'resuelto')
            ->orderBy('cerrado_en', 'desc')
            ->get()
            ->map(fn($inc) => array_merge($inc->toArray(), [
                'foto_url'         => $inc->foto         ? Storage::url($inc->foto)         : null,
                'foto_despues_url' => $inc->foto_despues ? Storage::url($inc->foto_despues) : null,
            ]));

        return Inertia::render('trabajador/Reportes', [
            'cerradas' => $cerradas,
        ]);
    }

    /**
     * Cierra una orden: guarda foto_despues + coordenadas y marca como resuelto.
     */
    public function cerrarOrden(Request $request, $id)
    {
        $inc = Incidencias::where('id', $id)
            ->where('asignado_a', auth()->id())
            ->firstOrFail();

        $request->validate([
            'foto_despues' => 'required|image|max:5120',
            'lat_cierre'   => 'required|numeric',
            'lng_cierre'   => 'required|numeric',
            'notas_cierre' => 'nullable|string|max:500',
        ]);

        $path = $request->file('foto_despues')->store('incidencias/cierres', 'public');

        $inc->update([
            'foto_despues'   => $path,
            'lat_cierre'     => $request->lat_cierre,
            'lng_cierre'     => $request->lng_cierre,
            'notas_cierre'   => $request->notas_cierre,
            'estatus'        => 'en revisión',   // el admin es quien aprueba como resuelto
            'cerrado_en'     => now(),
            'motivo_rechazo' => null,            // limpia rechazo anterior si había
        ]);

        return back()->with('success', 'Evidencia enviada. Esperando revisión del administrador.');
    }
}

