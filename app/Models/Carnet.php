<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Carnet extends Model
{
    protected $table = 'carnets';

    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula_identidad',
        'codigo_estudiante',
        'fecha_emision',
        'fecha_caducidad',
        'carrera',
        'semestre',
        'observacion',
        'estado',
        'foto'
    ];
    /**
     * Get the estado attribute with automatic calculation
     */
    public function getEstadoAttribute($value)
    {
        if ($this->fecha_caducidad) {
            return Carbon::now()->gt($this->fecha_caducidad) ? 'CADUCADO' : 'VIGENTE';
        }
        return $value;
    }
}
