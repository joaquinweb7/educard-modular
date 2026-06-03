<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StudentRequest extends Model
{
    protected $fillable = [
        'procedure_number', 'student_code', 'names', 'lastnames', 'ci_number', 'career_id', 'semester_id',
        'gestion', 'turno', 'grupo',
        'photo_path', 'status', 'observation', 'submitted_at', 'reviewed_by', 'reviewed_at',
        'photo_validation_status', 'photo_validation_details'
    ];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function career(): BelongsTo { return $this->belongsTo(Career::class); }
    public function semester(): BelongsTo { return $this->belongsTo(Semester::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pendiente',
            'resubmitted' => 'Reenviada',
            'reviewing' => 'En revisión',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'observed' => 'Observada',
            default => ucfirst((string) $this->status),
        };
    }

    protected function names(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper($value, 'UTF-8'),
        );
    }

    protected function lastnames(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_strtoupper($value, 'UTF-8'),
        );
    }
}
