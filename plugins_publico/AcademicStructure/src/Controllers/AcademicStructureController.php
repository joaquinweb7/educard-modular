<?php

namespace Plugins\AcademicStructure\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Plugins\AcademicStructure\Models\AcademicGestion;
use Plugins\AcademicStructure\Models\AcademicGroup;

class AcademicStructureController extends Controller
{
    public function index()
    {
        return view('AcademicStructure::index');
    }

    // --- Gestiones ---
    public function gestionsIndex()
    {
        $gestions = AcademicGestion::orderBy('name')->get();
        return view('AcademicStructure::gestions', compact('gestions'));
    }

    public function gestionsStore(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        AcademicGestion::create($data);
        return back()->with('success', 'Gestión creada correctamente.');
    }

    public function gestionsUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|in:active,inactive'
        ]);
        AcademicGestion::findOrFail($id)->update($data);
        return back()->with('success', 'Gestión actualizada correctamente.');
    }

    public function gestionsDestroy($id)
    {
        AcademicGestion::findOrFail($id)->delete();
        return back()->with('success', 'Gestión eliminada correctamente.');
    }

    // --- Grupos ---
    public function groupsIndex()
    {
        $groups = AcademicGroup::orderBy('name')->get();
        return view('AcademicStructure::groups', compact('groups'));
    }

    public function groupsStore(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        AcademicGroup::create($data);
        return back()->with('success', 'Grupo creado correctamente.');
    }

    public function groupsUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|in:active,inactive'
        ]);
        AcademicGroup::findOrFail($id)->update($data);
        return back()->with('success', 'Grupo actualizado correctamente.');
    }

    public function groupsDestroy($id)
    {
        AcademicGroup::findOrFail($id)->delete();
        return back()->with('success', 'Grupo eliminado correctamente.');
    }

    // --- Semestres (del Core) ---
    public function semestersIndex()
    {
        $semesters = Semester::orderBy('number')->get();
        return view('AcademicStructure::semesters', compact('semesters'));
    }

    public function semestersStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'number' => 'required|integer',
            'status' => 'required|in:active,inactive'
        ]);
        Semester::create($data);
        return back()->with('success', 'Semestre creado correctamente.');
    }

    public function semestersUpdate(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'number' => 'required|integer',
            'status' => 'required|in:active,inactive'
        ]);
        Semester::findOrFail($id)->update($data);
        return back()->with('success', 'Semestre actualizado correctamente.');
    }

    public function semestersDestroy($id)
    {
        Semester::findOrFail($id)->delete();
        return back()->with('success', 'Semestre eliminado correctamente.');
    }
}
