<?php

namespace App\Models\Certificados;


use Illuminate\Database\Eloquent\Model;

class PlantillaCertificado extends Model
{
    protected $table = 'certificados_plantillas';

    protected $fillable = [
        'nombre', 
        'imagen',
        'nombre_estudiante_x',
        'nombre_estudiante_y',
        'nombre_curso_x',
        'nombre_curso_y',
        'qr_x',
        'qr_y',
        'codigo_x',
        'codigo_y',
        'design_json',
        'width',
        'height'
    ];


    public function certificados()
    {
        return $this->hasMany(Certificado::class);
    }
}
