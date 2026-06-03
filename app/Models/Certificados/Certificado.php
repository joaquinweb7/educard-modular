<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = 'certificados';

    protected $fillable = [
        'nombre_estudiante',
        'nombre_curso_id',
        'carnet',
        'email',
        'codigo',
        'plantilla_id',
        'batch_id'
    ];

    public function plantilla(){
        return $this->belongsTo(PlantillaCertificado::class, 'plantilla_id');
    }

    public function curso(){
        return $this->belongsTo(Curso::class,'nombre_curso_id');
    }

}
