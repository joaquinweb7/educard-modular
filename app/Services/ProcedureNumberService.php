<?php

namespace App\Services;

use App\Models\StudentRequest;
use Illuminate\Support\Facades\DB;

class ProcedureNumberService
{
    public function generate(): string
    {
        return DB::transaction(function () {
            do {
                $number = 'TRM-'.date('Y').'-'.strtoupper(\Illuminate\Support\Str::random(8));
            } while (StudentRequest::where('procedure_number', $number)->exists());
            
            return $number;
        });
    }
}
