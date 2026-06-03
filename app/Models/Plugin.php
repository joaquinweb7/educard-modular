<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = ['name', 'display_name', 'description', 'version', 'author', 'provider', 'path', 'status', 'installed_at', 'activated_at'];

    protected function casts(): array
    {
        return ['installed_at' => 'datetime', 'activated_at' => 'datetime'];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
