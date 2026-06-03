<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index()
    {
        $careers = Career::orderBy('name')->paginate(15);
        return view('admin.careers.index', compact('careers'));
    }

    public function create()
    {
        return view('admin.careers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:careers,name',
            'status' => 'required|in:active,inactive',
        ]);

        Career::create($validated);

        return redirect()->route('admin.careers.index')->with('success', 'Carrera creada correctamente.');
    }

    public function edit(Career $career)
    {
        return view('admin.careers.edit', compact('career'));
    }

    public function update(Request $request, Career $career)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:careers,name,' . $career->id,
            'status' => 'required|in:active,inactive',
        ]);

        $career->update($validated);

        return redirect()->route('admin.careers.index')->with('success', 'Carrera actualizada correctamente.');
    }

    public function destroy(Career $career)
    {
        // En lugar de borrar, cambiamos a inactivo
        $career->update(['status' => 'inactive']);
        return redirect()->route('admin.careers.index')->with('success', 'Carrera desactivada.');
    }
}
