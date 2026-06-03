<?php

namespace App\Http\Controllers\Admin\Certificados;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Certificados\Certificado;

use Yajra\DataTables\Facades\DataTables;
use App\Models\Certificados\PlantillaCertificado;


class CertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
    {
        $query = Certificado::query()->with(['plantilla', 'curso']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_estudiante', 'like', "%{$search}%")
                  ->orWhere('carnet', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        $certificados = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.certificados.index', compact('certificados'));
    }

    public function data(Request $request)
    {
          $query = Certificado::query()
          ->select([
              'id',
              'nombre_estudiante',
              'nombre_curso_id',
              'carnet',
              'email',
              'codigo',
              'plantilla_id',
              'created_at'
          ])
          ->with(['plantilla:id,imagen']);

      // Filtros personalizados
      if ($request->filled('filtro_nombre')) {
          $query->where('nombre_estudiante', 'like', '%'.$request->filtro_nombre.'%');
      }

      if ($request->filled('filtro_carnet')) {
          $query->where('carnet', 'like', '%'.$request->filtro_carnet.'%');
      }

      if ($request->filled('filtro_email')) {
          $query->where('email', 'like', '%'.$request->filtro_email.'%');
      }

      if ($request->filled('filtro_codigo')) {
          $query->where('codigo', 'like', '%'.$request->filtro_codigo.'%');
      }

      // Construcción con Yajra
      return DataTables::of($query)
          ->addColumn('thumb_url', function ($c) {
              return $c->plantilla?->imagen
                  ? Storage::url($c->plantilla->imagen)
                  : null;
          })
          ->addColumn('plantilla_show_url', function ($c) {
              return $c->plantilla
                  ? route('admin.certificados.test', ['plantilla' => $c->plantilla->id])
                  : null;
          })
          ->editColumn('nombre_curso_id', function ($c) {
            return $c->curso->nombre;
            })
          ->editColumn('created_at', fn($c) => optional($c->created_at)->toIso8601String())
          ->addColumn('send_url', fn($c) => route('admin.certificados.smtp.sendEmail', $c))
          ->addColumn('edit_url', fn($c) => route('admin.certificados.edit', $c))
          ->addColumn('delete_url', fn($c) => route('admin.certificados.destroy', $c))
          ->toJson(1); // sin rawColumns → JSON puro
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plantillas = PlantillaCertificado::select('id as value', 'nombre', 'imagen')->get();
        $cursos = \App\Models\Certificados\Curso::all();
        return view('admin.certificados.create', compact('plantillas', 'cursos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_estudiante' => 'required|string',
            'nombre_curso' => 'required|exists:certificados_cursos,id',
            'carnet' => 'required|string',
            'email' => 'required|email',
            'codigo' => 'required|string',
            'plantilla_id' => 'required|exists:certificados_plantillas,id',
        ]);

        $certificado = new Certificado();
        $certificado->nombre_estudiante = $request->nombre_estudiante;
        $certificado->nombre_curso_id = $request->nombre_curso;
        $certificado->carnet = $request->carnet;
        $certificado->email = $request->email;
        $certificado->codigo = $request->codigo;
        $certificado->plantilla_id = $request->plantilla_id;
        $certificado->save();

        return redirect()->route('admin.certificados.index')->with('success', 'Certificado creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificado $certificado)
    {
        $certificado->load(['plantilla', 'curso']);
        return view('admin.certificados.show', compact('certificado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificado $certificado)
    {
        $plantillas = PlantillaCertificado::select('id as value', 'nombre', 'imagen')->get();
        $cursos = \App\Models\Certificados\Curso::all();
        return view('admin.certificados.edit', compact('certificado', 'plantillas', 'cursos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificado $certificado)
    {
        $request->validate([
            'nombre_estudiante' => 'required|string',
            'nombre_curso' => 'required|exists:certificados_cursos,id',
            'carnet' => 'required|string',
            'email' => 'required|email',
            'codigo' => 'required|string',
            'plantilla_id' => 'required|exists:certificados_plantillas,id',
        ]);

        $certificado->nombre_estudiante = $request->nombre_estudiante;
        $certificado->nombre_curso_id = $request->nombre_curso;
        $certificado->carnet = $request->carnet;
        $certificado->email = $request->email;
        $certificado->codigo = $request->codigo;
        $certificado->plantilla_id = $request->plantilla_id;
        $certificado->save();

        return redirect()->route('admin.certificados.index')->with('success', 'Certificado actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificado $certificado)
    {
        $certificado->delete();
        return redirect()->route('admin.certificados.index')->with('success', 'Certificado eliminado correctamente');
    }

    public function descargarPlantilla(){
        return Storage::download('verificar/certificados/lista.csv');
    }
}
