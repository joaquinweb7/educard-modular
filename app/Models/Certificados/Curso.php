<?php

namespace App\Models\Certificados;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'certificados_cursos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion',
        'estado'
    ];

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'nombre_curso_id');
    }
}
