<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\GeneratedCard;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['career', 'semester'])
            ->where('status', 'active')
            ->orderByDesc('printed_at')
            ->orderBy('lastnames');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('names', 'like', '%'.$request->search.'%')
                    ->orWhere('lastnames', 'like', '%'.$request->search.'%')
                    ->orWhere('ci_number', 'like', '%'.$request->search.'%')
                    ->orWhere('student_code', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('career_id')) $query->where('career_id', $request->career_id);
        if ($request->filled('semester_id')) $query->where('semester_id', $request->semester_id);
        if ($request->filled('gestion')) $query->where('gestion', $request->gestion);
        if ($request->filled('turno')) $query->where('turno', $request->turno);
        if ($request->filled('grupo')) $query->where('grupo', $request->grupo);
        
        // Filter by print status. Defaults to 'printed' for archiving, unless 'all' or 'pending' is selected.
        if ($request->filled('print_status') && $request->print_status !== 'all') {
            $query->where('is_printed', $request->print_status === 'printed');
        } elseif (!$request->filled('print_status')) {
            $query->where('is_printed', true);
        }

        if ($request->export === 'pdf') {
            $studentsList = $query->get();
            $pdf = Pdf::loadView('admin.reports.list-pdf', compact('studentsList'))->setPaper('letter', 'landscape');
            return $pdf->download('reporte-impresiones.pdf');
        }

        if ($request->export === 'excel') {
            $studentsList = $query->get();
            $csvFileName = 'reporte-impresiones.csv';
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$csvFileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use($studentsList) {
                // Use UTF-8 BOM for Excel compatibility
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");
                
                fputcsv($file, ['Nombres', 'Apellidos', 'CI', 'Codigo', 'Carrera', 'Semestre', 'Gestion', 'Turno', 'Grupo', 'Estado de Impresion', 'Fecha de Impresion']);

                foreach ($studentsList as $s) {
                    fputcsv($file, [
                        $s->names,
                        $s->lastnames,
                        $s->ci_number,
                        $s->student_code,
                        $s->career->name ?? '',
                        $s->semester->name ?? '',
                        $s->gestion,
                        $s->turno,
                        $s->grupo,
                        $s->is_printed ? 'Impreso' : 'Pendiente',
                        $s->is_printed && $s->printed_at ? $s->printed_at->format('d/m/Y H:i') : ''
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        $studentsList = $query->paginate(20)->withQueryString();

        $studentsByCareer = Career::withCount('students')->orderBy('name')->get();
        $studentsBySemester = Semester::withCount('students')->orderBy('number')->get();

        return view('admin.reports.index', [
            'studentsList' => $studentsList,
            'studentsByCareer' => $studentsByCareer,
            'studentsBySemester' => $studentsBySemester,
            'requests' => StudentRequest::count(),
            'students' => Student::count(),
            'generatedCards' => GeneratedCard::count(),
            'careers' => Career::orderBy('name')->get(),
            'semesters' => Semester::orderBy('number')->get(),
            'gestiones' => Student::select('gestion')->distinct()->whereNotNull('gestion')->whereRaw("gestion != ''")->orderBy('gestion')->pluck('gestion'),
            'turnos'    => Student::select('turno')->distinct()->whereNotNull('turno')->whereRaw("turno != ''")->orderBy('turno')->pluck('turno'),
            'grupos'    => Student::select('grupo')->distinct()->whereNotNull('grupo')->whereRaw("grupo != ''")->orderBy('grupo')->pluck('grupo'),
        ]);
    }

    public function pdf()
    {
        $data = [
            'studentsByCareer' => Career::withCount('students')->orderBy('name')->get(),
            'studentsBySemester' => Semester::withCount('students')->orderBy('number')->get(),
            'requests' => StudentRequest::count(),
            'students' => Student::count(),
            'generatedCards' => GeneratedCard::count(),
            'generatedAt' => now(),
        ];
        return Pdf::loadView('admin.reports.pdf', $data)->download('reporte-general.pdf');
    }
}
