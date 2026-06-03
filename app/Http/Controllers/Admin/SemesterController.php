<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::orderBy('number')->paginate(15);
        return view('admin.semesters.index', compact('semesters'));
    }

    public function create()
    {
        return view('admin.semesters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:semesters,name',
            'number' => 'required|integer|min:1|max:20|unique:semesters,number',
            'status' => 'required|in:active,inactive',
        ]);

        Semester::create($validated);

        return redirect()->route('admin.semesters.index')->with('success', 'Semestre creado correctamente.');
    }

    public function edit(Semester $semester)
    {
        return view('admin.semesters.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:semesters,name,' . $semester->id,
            'number' => 'required|integer|min:1|max:20|unique:semesters,number,' . $semester->id,
            'status' => 'required|in:active,inactive',
        ]);

        $semester->update($validated);

        return redirect()->route('admin.semesters.index')->with('success', 'Semestre actualizado correctamente.');
    }

    public function destroy(Semester $semester)
    {
        $semester->update(['status' => 'inactive']);
        return redirect()->route('admin.semesters.index')->with('success', 'Semestre desactivado.');
    }
}
