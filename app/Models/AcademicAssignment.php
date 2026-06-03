<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicAssignment extends Model
{
    protected $fillable = [
        'career_id',
        'semester_id',
        'gestion',
        'turno',
        'grupo',
        'status',
    ];

    public function career()
    {
        return $this->belongsTo(Career::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
