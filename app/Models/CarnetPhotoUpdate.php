<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarnetPhotoUpdate extends Model
{
    protected $fillable = [
        'carnet_id',
        'codigo_estudiante',
        'photo_path',
        'status',
        'observation'
    ];

    public function carnet(): BelongsTo
    {
        return $this->belongsTo(Carnet::class);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            default => ucfirst((string) $this->status),
        };
    }
}
