<?php

namespace Plugins\AcademicStructure\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicGroup extends Model
{
    protected $table = 'academic_groups';
    
    protected $fillable = [
        'name',
        'status',
    ];
}
