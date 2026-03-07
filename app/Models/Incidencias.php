<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencias extends Model
{
    protected $table = 'incidencias';

    protected $casts = [
        'cerrado_en' => 'datetime',
        'latitud'    => 'float',
        'longitud'   => 'float',
        'lat_cierre' => 'float',
        'lng_cierre' => 'float',
    ];

    protected $fillable = [
        'nombre_ciudadano',
        'email',
        'direccion',
        'latitud',
        'longitud',
        'tipo_incidencia',
        'descripcion',
        'estatus',
        'foto',
        'asignado_a',
        'foto_despues',
        'lat_cierre',
        'lng_cierre',
        'notas_cierre',
        'cerrado_en',
        'motivo_rechazo',
    ];

    public function trabajador()
    {
        return $this->belongsTo(\App\Models\User::class, 'asignado_a');
    }
}
