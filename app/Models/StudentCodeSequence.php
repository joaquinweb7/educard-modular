<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCodeSequence extends Model
{
    protected $fillable = ['prefix', 'last_number'];
}
