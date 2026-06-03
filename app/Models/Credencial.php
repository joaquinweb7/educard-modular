<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Credencial extends Model
{
    protected $table = 'credenciales';

    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula_identidad',
        'codigo_credencial',
        'cargo_principal',
        'cargo_secundario',
        'departamento',
        'fecha_emision',
        'fecha_caducidad',
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
