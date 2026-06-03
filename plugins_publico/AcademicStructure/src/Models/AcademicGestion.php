<?php

namespace Plugins\AcademicStructure\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicGestion extends Model
{
    protected $table = 'academic_gestions';
    
    protected $fillable = [
        'name',
        'status',
    ];
}
