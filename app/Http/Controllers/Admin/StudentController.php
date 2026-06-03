<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\Semester;
use App\Models\Student;
use App\Services\StudentCodeService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['career', 'semester'])->latest();

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
        
        if ($request->filled('print_status')) {
            $query->where('is_printed', $request->print_status === 'printed');
        }

        return view('admin.students.index', [
            'students' => $query->paginate(20)->withQueryString(),
            'careers' => Career::orderBy('name')->get(),
            'semesters' => Semester::orderBy('number')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.students.create', [
            'student' => new Student(),
            'careers' => Career::where('status', 'active')->orderBy('name')->get(),
            'semesters' => Semester::where('status', 'active')->orderBy('number')->get(),
        ]);
    }

    public function store(Request $request, StudentCodeService $codeService)
    {
        $data = $request->validate([
            'names' => ['required', 'string', 'max:150'],
            'lastnames' => ['required', 'string', 'max:150'],
            'ci_number' => ['required', 'string', 'max:50', 'unique:students,ci_number'],
            'career_name' => ['required', 'string', 'max:150'],
            'semester_name' => ['required', 'string', 'max:150'],
            'gestion' => ['required', 'string', 'max:50'],
            'turno' => ['required', 'string', 'max:50'],
            'grupo' => ['required', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'student_code' => ['nullable', 'string', 'max:50', 'unique:students,student_code'],
            'auto_code' => ['nullable'],
        ]);

        $career = \App\Models\Career::firstOrCreate(
            ['name' => $data['career_name']],
            ['status' => 'active']
        );

        preg_match('/\d+/', $data['semester_name'], $matches);
        $semesterNumber = !empty($matches) ? (int)$matches[0] : null;

        if ($semesterNumber) {
            $semester = \App\Models\Semester::where('number', $semesterNumber)->first();
            if (!$semester) {
                $semester = \App\Models\Semester::create([
                    'name' => $data['semester_name'],
                    'number' => $semesterNumber,
                    'status' => 'active'
                ]);
            }
        } else {
            $semester = \App\Models\Semester::where('name', $data['semester_name'])->first();
            if (!$semester) {
                $maxNumber = \App\Models\Semester::max('number') ?? 0;
                $semester = \App\Models\Semester::create([
                    'name' => $data['semester_name'],
                    'number' => $maxNumber + 1,
                    'status' => 'active'
                ]);
            }
        }

        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('students/photos', 'public') : null;

        Student::create([
            'student_code' => $request->boolean('auto_code') || empty($data['student_code']) ? $codeService->generate() : $data['student_code'],
            'names' => $data['names'],
            'lastnames' => $data['lastnames'],
            'ci_number' => $data['ci_number'],
            'career_id' => $career->id,
            'semester_id' => $semester->id,
            'gestion' => $data['gestion'],
            'turno' => $data['turno'],
            'grupo' => $data['grupo'],
            'photo_path' => $photoPath,
            'status' => 'active',
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Estudiante registrado correctamente.');
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', [
            'student' => $student,
            'careers' => Career::where('status', 'active')->orderBy('name')->get(),
            'semesters' => Semester::where('status', 'active')->orderBy('number')->get(),
        ]);
    }

    public function update(Request $request, Student $student, StudentCodeService $codeService)
    {
        $data = $request->validate([
            'names' => ['required', 'string', 'max:150'],
            'lastnames' => ['required', 'string', 'max:150'],
            'ci_number' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('students', 'ci_number')->ignore($student->id)],
            'career_id' => ['required', 'exists:careers,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'gestion' => ['required', 'string', 'max:50'],
            'turno' => ['required', 'string', 'max:50'],
            'grupo' => ['required', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'student_code' => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('students', 'student_code')->ignore($student->id)],
            'auto_code' => ['nullable'],
        ]);

        if ($request->hasFile('photo')) {
            $student->photo_path = $request->file('photo')->store('students/photos', 'public');
        }

        $newCode = $student->student_code;
        if ($request->boolean('auto_code')) {
            $newCode = $codeService->generate();
        } elseif (!empty($data['student_code'])) {
            $newCode = $data['student_code'];
        }

        $student->update([
            'student_code' => $newCode,
            'names' => $data['names'],
            'lastnames' => $data['lastnames'],
            'ci_number' => $data['ci_number'],
            'career_id' => $data['career_id'],
            'semester_id' => $data['semester_id'],
            'gestion' => $data['gestion'],
            'turno' => $data['turno'],
            'grupo' => $data['grupo'],
        ]);

        return redirect()->route('admin.students.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Estudiante eliminado correctamente.');
    }

    public function togglePrintStatus(Student $student)
    {
        $student->update([
            'is_printed' => !$student->is_printed,
            'printed_at' => !$student->is_printed ? now() : null,
        ]);
        
        $status = $student->is_printed ? 'impreso' : 'no impreso';
        return back()->with('success', "Estado actualizado a: {$status}.");
    }
}
