<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicAssignment;
use App\Models\Career;
use Illuminate\Http\Request;

class AcademicAssignmentController extends Controller
{
    public function index()
    {
        $assignments = AcademicAssignment::with(['career', 'semester'])
            ->orderBy('career_id')
            ->orderBy('semester_id')
            ->orderBy('gestion')
            ->orderBy('turno')
            ->orderBy('grupo')
            ->get();
        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $careers = Career::where('status', 'active')->orderBy('name')->get();
        $semesters = \App\Models\Semester::where('status', 'active')->orderBy('number')->get();
        return view('admin.assignments.create', compact('careers', 'semesters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'career_id' => 'required|exists:careers,id',
            'semester_id' => 'required|exists:semesters,id',
            'gestion' => 'required|string|max:50',
            'turno' => 'required|string|max:50',
            'grupo' => 'required|string|max:50',
        ]);

        // Check if combination already exists
        $exists = AcademicAssignment::where('career_id', $data['career_id'])
            ->where('semester_id', $data['semester_id'])
            ->where('gestion', $data['gestion'])
            ->where('turno', $data['turno'])
            ->where('grupo', $data['grupo'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Esta asignación académica ya existe.');
        }

        AcademicAssignment::create($data);

        return redirect()->route('admin.assignments.index')->with('success', 'Asignación creada exitosamente.');
    }

    public function edit(AcademicAssignment $assignment)
    {
        $careers = Career::where('status', 'active')->orderBy('name')->get();
        $semesters = \App\Models\Semester::where('status', 'active')->orderBy('number')->get();
        return view('admin.assignments.edit', compact('assignment', 'careers', 'semesters'));
    }

    public function update(Request $request, AcademicAssignment $assignment)
    {
        $data = $request->validate([
            'career_id' => 'required|exists:careers,id',
            'semester_id' => 'required|exists:semesters,id',
            'gestion' => 'required|string|max:50',
            'turno' => 'required|string|max:50',
            'grupo' => 'required|string|max:50',
            'status' => 'required|string|in:active,inactive',
        ]);

        // Check if combination already exists for a DIFFERENT assignment
        $exists = AcademicAssignment::where('career_id', $data['career_id'])
            ->where('semester_id', $data['semester_id'])
            ->where('gestion', $data['gestion'])
            ->where('turno', $data['turno'])
            ->where('grupo', $data['grupo'])
            ->where('id', '!=', $assignment->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Esta asignación académica ya existe.');
        }

        $assignment->update($data);

        return redirect()->route('admin.assignments.index')->with('success', 'Asignación actualizada exitosamente.');
    }

    public function destroy(AcademicAssignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('admin.assignments.index')->with('success', 'Asignación eliminada exitosamente.');
    }
}
