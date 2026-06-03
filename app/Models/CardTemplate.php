<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardTemplate extends Model
{
    protected $fillable = ['name', 'background_path', 'width', 'height', 'design_json', 'is_default', 'status', 'created_by'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }
}
