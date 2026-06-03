<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Semester;
use App\Models\Student;
use App\Services\StudentCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentImportController extends Controller
{
    public function create()
    {
        return view('admin.students.import');
    }

    public function store(Request $request, StudentCodeService $codeService)
    {
        $request->validate(['csv' => ['required', 'file', 'mimes:csv,txt', 'max:4096']]);

        $handle = fopen($request->file('csv')->getRealPath(), 'r');
        
        // Skip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 1000, ';');
        if (!$header || count($header) < 5) {
            // Fallback to comma if semicolon didn't work (just in case)
            rewind($handle);
            if ($bom === "\xEF\xBB\xBF") fread($handle, 3);
            $header = fgetcsv($handle, 1000, ',');
        }
        
        $created = 0; $skipped = 0;

        // Limpiar headers (quitar espacios y poner a minúsculas)
        if ($header) {
            $header = array_map(function($item) {
                return strtolower(trim($item));
            }, $header);
        }

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            if (count($header) !== count($row)) continue;
            
            $data = array_combine($header, $row);
            if (! $data) { $skipped++; continue; }

            $career = Career::firstOrCreate(['name' => trim($data['carrera'] ?? '')], ['status' => 'active']);
            $semesterNumber = (int) trim($data['semestre'] ?? 1);
            $semester = Semester::firstOrCreate(['number' => $semesterNumber], ['name' => $semesterNumber.'° Semestre', 'status' => 'active']);
            $ci = trim($data['carnet'] ?? '');

            if ($ci === '' || Student::where('ci_number', $ci)->exists()) { $skipped++; continue; }

            $studentCode = trim($data['codigo'] ?? '');
            if (empty($studentCode)) {
                $studentCode = $codeService->generate();
            }

            Student::create([
                'student_code' => $studentCode,
                'names' => Str::title(trim($data['nombres'] ?? '')),
                'lastnames' => Str::title(trim($data['apellidos'] ?? '')),
                'ci_number' => $ci,
                'career_id' => $career->id,
                'semester_id' => $semester->id,
                'gestion' => Str::upper(trim($data['gestion'] ?? '')),
                'turno' => Str::upper(trim($data['turno'] ?? '')),
                'grupo' => Str::upper(trim($data['grupo'] ?? '')),
                'status' => 'active',
            ]);
            $created++;
        }

        fclose($handle);

        return back()->with('success', "Importación finalizada. Creados: {$created}. Omitidos: {$skipped}.");
    }
}
