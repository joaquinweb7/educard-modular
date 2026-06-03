<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $fillable = ['name', 'number', 'status'];

    public function students(): HasMany { return $this->hasMany(Student::class); }
}
