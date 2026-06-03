<?php

namespace App\Services;

use App\Models\StudentCodeSequence;
use Illuminate\Support\Facades\DB;

class StudentCodeService
{
    public function generate(string $prefix = '202602'): string
    {
        return DB::transaction(function () use ($prefix) {
            $sequence = StudentCodeSequence::where('prefix', $prefix)->lockForUpdate()->first();

            if (! $sequence) {
                $sequence = StudentCodeSequence::create(['prefix' => $prefix, 'last_number' => 99]);
                $sequence->refresh();
            }

            $sequence->last_number = $sequence->last_number + 1;
            $sequence->save();

            return $sequence->prefix . str_pad((string) $sequence->last_number, 3, '0', STR_PAD_LEFT);
        });
    }
}
