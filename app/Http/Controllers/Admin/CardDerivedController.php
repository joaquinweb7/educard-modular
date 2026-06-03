<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardTemplate;
use App\Models\Career;
use App\Models\GeneratedCard;
use App\Models\Semester;
use App\Models\Student;
use App\Services\CardRenderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CardDerivedController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['career', 'semester'])
            ->where('status', 'active')
            ->where('is_derived', true)
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
        if ($request->filled('grupo')) $query->where('grupo', $request->grupo);
        if ($request->filled('print_status')) {
            $query->where('is_printed', $request->print_status === 'printed');
        }

        return view('admin.cards.derived', [
            'students' => $query->paginate(20)->withQueryString(),
            'templates' => CardTemplate::where('status', 'active')->orderBy('name')->get(),
            'careers' => Career::orderBy('name')->get(),
            'semesters' => Semester::orderBy('number')->get(),
            'gestiones' => Student::select('gestion')->distinct()->whereNotNull('gestion')->orderBy('gestion')->pluck('gestion'),
            'grupos' => Student::select('grupo')->distinct()->whereNotNull('grupo')->orderBy('grupo')->pluck('grupo'),
        ]);
    }

    public function generatePdf(Request $request, CardRenderService $renderer)
    {
        $data = $request->validate([
            'card_template_id' => ['required', 'exists:card_templates,id'],
            'career_id' => ['nullable', 'exists:careers,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'gestion' => ['nullable', 'string'],
            'grupo' => ['nullable', 'string'],
            'student_ids' => ['nullable', 'string'],
        ]);

        $template = CardTemplate::findOrFail($data['card_template_id']);
        $query = Student::with(['career', 'semester'])
            ->where('status', 'active')
            ->where('is_derived', true)
            ->orderBy('lastnames');
        
        if (! empty($data['career_id'])) $query->where('career_id', $data['career_id']);
        if (! empty($data['semester_id'])) $query->where('semester_id', $data['semester_id']);
        if (! empty($data['gestion'])) $query->where('gestion', $data['gestion']);
        if (! empty($data['grupo'])) $query->where('grupo', $data['grupo']);
        if (! empty($data['student_ids'])) {
            $ids = collect(explode(',', $data['student_ids']))->map(fn($v) => (int) trim($v))->filter()->all();
            if ($ids) $query->whereIn('id', $ids);
        }

        $students = $query->get();

        foreach ($students as $student) {
            GeneratedCard::create([
                'student_id' => $student->id,
                'card_template_id' => $template->id,
                'generated_by' => auth()->id(),
                'generated_at' => now(),
            ]);
            
            $student->update([
                'is_printed' => true,
                'printed_at' => now(),
            ]);
        }

        $fonts = \App\Models\Font::all();
        $pdf = Pdf::loadView('admin.cards.pdf', compact('students', 'template', 'renderer', 'fonts'))
            ->setPaper([0, 0, $template->width * 0.75, $template->height * 0.75], 'portrait');
        $filename = 'carnets-derivados.pdf';
        if ($students->count() === 1) {
            $student = $students->first();
            $filename = trim($student->names . ' ' . $student->lastnames);
            if ($student->student_code) {
                $filename .= ' - ' . $student->student_code;
            }
            $filename = preg_replace('/[<>:"\/\\|?*]+/', '', $filename) . '.pdf';
        }

        return $pdf->download($filename);
    }
}
