<?php

namespace App\Models\Certificados;


use Illuminate\Database\Eloquent\Model;

class Smtp extends Model
{
    protected $table = 'smtp';

    protected $fillable = [
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from',
        'from_name',
    ];
}
