<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedCard extends Model
{
    protected $fillable = ['student_id', 'card_template_id', 'generated_by', 'pdf_path', 'generated_at'];

    protected function casts(): array
    {
        return ['generated_at' => 'datetime'];
    }

    public function student(): BelongsTo { return $this->belongsTo(Student::class); }
    public function template(): BelongsTo { return $this->belongsTo(CardTemplate::class, 'card_template_id'); }
}
