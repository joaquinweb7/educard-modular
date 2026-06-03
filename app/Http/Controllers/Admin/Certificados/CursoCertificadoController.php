<?php

namespace App\Http\Controllers\Admin\Certificados;

use App\Http\Controllers\Controller;
use App\Models\Certificados\Curso;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CursoCertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Certificados\Curso::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nombre', 'like', "%{$search}%");
        }
        $cursos = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        return view('admin.certificados.curso.index', compact('cursos'));
    }

    public function data(Request $request)
    {
        $query = Curso::query()
            ->select([
                'id',
                'nombre',
                'descripcion',
                'duracion',
                'estado',
                'created_at'
            ]);

        // Filtros personalizados
        if ($request->filled('filtro_nombre')) {
            $query->where('nombre', 'like', '%'.$request->filtro_nombre.'%');
        }

        if ($request->filled('filtro_estado')) {
            $query->where('estado', $request->filtro_estado);
        }

        // Construcción con Yajra
        return DataTables::of($query)
            ->editColumn('created_at', fn($c) => optional($c->created_at)->toIso8601String())
            ->addColumn('edit_url', fn($c) => route('admin.certificados.curso.edit', $c))
            ->addColumn('delete_url', fn($c) => route('admin.certificados.curso.destroy', $c))
            ->toJson(1);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.certificados.curso.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:certificados_cursos,nombre',
            'descripcion' => 'nullable|string|max:1000',
            'duracion' => 'nullable|string|max:255',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $curso = new Curso();
        $curso->nombre = $request->nombre;
        $curso->descripcion = $request->descripcion;
        $curso->duracion = $request->duracion;
        $curso->estado = $request->estado;

        if ($curso->save()) {
            return redirect()->route('admin.certificados.curso.index')->with('success', 'Curso creado correctamente');
        }

        return back()->with('error', 'Error al crear el curso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Curso $curso)
    {
        return view('admin.certificados.curso.show', compact('curso'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Curso $curso)
    {
        return view('admin.certificados.curso.edit', compact('curso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Curso $curso)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:certificados_cursos,nombre,' . $curso->id,
            'descripcion' => 'nullable|string|max:1000',
            'duracion' => 'nullable|string|max:255',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $curso->nombre = $request->nombre;
        $curso->descripcion = $request->descripcion;
        $curso->duracion = $request->duracion;
        $curso->estado = $request->estado;

        if ($curso->save()) {
            return redirect()->route('admin.certificados.curso.index')->with('success', 'Curso actualizado correctamente');
        }

        return back()->with('error', 'Error al actualizar el curso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curso $curso)
    {
        // Verificar si el curso tiene certificados asociados
        if ($curso->certificados()->count() > 0) {
            return redirect()->route('admin.certificados.curso.index')->with('error', 'No se puede eliminar el curso porque tiene certificados asociados');
        }

        if ($curso->delete()) {
            return redirect()->route('admin.certificados.curso.index')->with('success', 'Curso eliminado correctamente');
        }

        return back()->with('error', 'Error al eliminar el curso');
    }
}
