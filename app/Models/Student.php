<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Student extends Model
{
    protected $fillable = [
        'student_code', 'names', 'lastnames', 'ci_number', 'career_id', 'semester_id',
        'gestion', 'turno', 'grupo',
        'photo_path', 'status', 'approved_request_id', 'is_printed', 'printed_at', 'is_derived', 'derived_at'
    ];

    protected function casts(): array
    {
        return [
            'is_printed' => 'boolean',
            'printed_at' => 'datetime',
            'is_derived' => 'boolean',
            'derived_at' => 'datetime',
        ];
    }

    public function career(): BelongsTo { return $this->belongsTo(Career::class); }
    public function semester(): BelongsTo { return $this->belongsTo(Semester::class); }
    public function approvedRequest(): BelongsTo { return $this->belongsTo(StudentRequest::class, 'approved_request_id'); }

    public function fullName(): string
    {
        return trim($this->names.' '.$this->lastnames);
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
