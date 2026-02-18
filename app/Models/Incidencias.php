<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencias extends Model
{
    protected $table = 'incidencias';

    protected $fillable = [
        'nombre_ciudadano',
        'email',
        'direccion',
        'latitud',
        'longitud',
        'tipo_incidencia',
        'descripción',
        'estatus',
        'foto',
    ];
}
