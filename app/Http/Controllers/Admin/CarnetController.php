<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Carnet;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class CarnetController extends Controller
{
    public function index(Request $request){
        $query = Carnet::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%$search%")
                  ->orWhere('apellidos', 'like', "%$search%")
                  ->orWhere('cedula_identidad', 'like', "%$search%")
                  ->orWhere('codigo_estudiante', 'like', "%$search%");
            });
        }
        $carnets = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.carnets.index', compact('carnets'));
    }

    public function data(Request $request)
    {
        $query = Carnet::query()
        ->select([
            'id',
            'nombres',
            'apellidos',
            'cedula_identidad',
            'codigo_estudiante',
            'fecha_emision',
            'fecha_caducidad',
            'carrera',
            'semestre',
            'estado',
            'created_at'
        ]);
        // Filtros personalizados
        if ($request->filled('filtro_nombres')) {
            $query->where('nombres', 'like', '%'.$request->filtro_nombres.'%');
        }

        if ($request->filled('filtro_apellidos')) {
            $query->where('apellidos', 'like', '%'.$request->filtro_apellidos.'%');
        }

        if ($request->filled('filtro_cedula_identidad')) {
            $query->where('cedula_identidad', 'like', '%'.$request->filtro_cedula_identidad.'%');
        }

        if ($request->filled('filtor_codigo_estudiante')) {
            $query->where('codigo_estudiante', 'like', '%'.$request->filtor_codigo_estudiante.'%');
        }

                // Construcción con Yajra
        return DataTables::of($query)
            ->editColumn('created_at', fn($c) => optional($c->created_at)->toIso8601String())
            ->addColumn('edit_url', fn($c) => route('carnets.edit', $c))
            ->addColumn('delete_url', fn($c) => route('carnets.destroy', $c))
            ->toJson(1); // sin rawColumns → JSON puro
    }


    public function create(){

        return view('admin.carnets.create');
    }

    public function store(Request $request){
        
        $request->validate([
            'nombres'=> 'required|string|max:255',
            'apellidos'=>'required|string|max:255',
            'cedula_identidad'=> 'required|string|max:255|unique:carnets,cedula_identidad',
            'codigo_estudiante' => 'required|string|max:255|unique:carnets,codigo_estudiante',
            'fecha_emision'=> 'required|string|max:255',
            'fecha_caducidad'=> 'required|string|max:255',
            'carrera'=> 'required|string|max:255',
            'semestre'=> 'required|string|max:255',
            'observacion'=> 'nullable|string|max:255',
        ]);
        $carnet = new Carnet();
        $carnet->nombres = $request->nombres;
        $carnet->apellidos = $request->apellidos;
        $carnet->cedula_identidad = $request->cedula_identidad;
        $carnet->codigo_estudiante = $request->codigo_estudiante;
        $carnet->fecha_emision = $request->fecha_emision;
        $carnet->fecha_caducidad = $request->fecha_caducidad;
        $carnet->carrera = $request->carrera ;
        $carnet->semestre = $request->semestre;
        $carnet->observacion = $request->observacion;
        $carnet->estado = $request->estado;
        if ( $carnet->save()){
            return redirect()->route('carnets.index')->with('success','Creado correctamente');
        }
    }

    public function edit(Carnet $carnet){
        return view('admin.carnets.edit', compact('carnet'));
    }

    public function update(Request $request, Carnet $carnet){
        
        $request->validate([
            'nombres'=> 'required|string|max:255',
            'apellidos'=>'required|string|max:255',
            'cedula_identidad'=> 'required|string|max:255|unique:carnets,cedula_identidad,' . $carnet->id,
            'codigo_estudiante' => 'required|string|max:255|unique:carnets,codigo_estudiante,' . $carnet->id,
            'fecha_emision'=> 'required|string|max:255',
            'fecha_caducidad'=> 'required|string|max:255',
            'carrera'=> 'required|string|max:255',
            'semestre'=> 'required|string|max:255',
            'observacion'=> 'nullable|string|max:255',
        ]);
        $carnet->nombres = $request->nombres;
        $carnet->apellidos = $request->apellidos;
        $carnet->cedula_identidad = $request->cedula_identidad;
        $carnet->codigo_estudiante = $request->codigo_estudiante;
        $carnet->fecha_emision = $request->fecha_emision;
        $carnet->fecha_caducidad = $request->fecha_caducidad;
        $carnet->carrera = $request->carrera ;
        $carnet->semestre = $request->semestre;
        $carnet->observacion = $request->observacion;
        $carnet->estado = $request->estado;
        if ( $carnet->save()){
            return redirect()->route('carnets.index')->with('success','Actualizado correctamente');
        }
    }

    public function destroy(Carnet $carnet){
        $carnet->delete();
        return redirect()->route('carnets.index')->with('success','Eliminado correctamente');
    }



    public function cargar(){

        return view('admin.carnets.cargar');
    }


    public function subir(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB
        ]);

        $file = $request->file('excel_file');

        // Importa primera hoja como array
        $import = new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public array $data = [];
            public function array(array $array) { $this->data = $array; }
        };

        // Deja que detecte .xls o .xlsx
        Excel::import($import, $file);
        $data = $import->data ?? [];

        $filas = [];

        // Recorre desde la fila 2 del Excel (índice 1)
        for ($i = 1; $i < count($data); $i++) {
            $raw = array_slice($data[$i] ?? [], 0, 8);

            // Saltar filas totalmente vacías
            if (count(array_filter($raw, fn($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            // // Exigir 8 columnas no vacías (A..H)
            // if (count($raw) < 8 || count(array_filter($raw, fn($v) => $v !== null && $v !== '')) < 8) {
            //     $excelRow = $i + 1; // i=1 -> fila 2 en Excel
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'La fila '.$excelRow.' está incompleta.',
            //     ], 422);
            // }
            // Normalizar fechas si vienen como número Excel (E y F)
            foreach ([4, 5] as $col) {
                if (isset($raw[$col]) && $raw[$col] !== '' && is_numeric($raw[$col])) {
                    $raw[$col] = ExcelDate::excelToDateTimeObject($raw[$col])->format('Y/m/d');
                }
            }
            $row = [
                'nombres'           => trim((string)($raw[0] ?? '')),
                'apellidos'         => trim((string)($raw[1] ?? '')),
                'cedula_identidad'  => trim((string)($raw[2] ?? '')),
                'codigo_estudiante' => trim((string)($raw[3] ?? '')),
                'fecha_emision'     => (string)($raw[4] ?? ''),
                'fecha_caducidad'   => (string)($raw[5] ?? ''),
                'carrera'           => trim((string)($raw[6] ?? '')),
                'semestre'          => is_numeric($raw[7] ?? null) ? (int)$raw[7] : trim((string)($raw[7] ?? '')),
            ];
 
            // Validar esta fila
            $validator = Validator::make($row, [
                'nombres'           => 'required|string|max:255',
                'apellidos'         => 'required|string|max:255',
                'cedula_identidad'  => 'required|string|max:255|unique:carnets,cedula_identidad',
                'codigo_estudiante' => 'required|string|max:255|unique:carnets,codigo_estudiante',
                'fecha_emision'     => 'required|date_format:Y/m/d',
                'fecha_caducidad'   => 'required|date_format:Y/m/d|after:fecha_emision',
                'carrera'           => 'required|string|max:100',
                'semestre'          => 'required|integer|min:1|max:10',
            ],
                [
                    'fecha_emision.date_format' => 'Tu fecha tiene que estar en MM/DD/YYYY',
                    'fecha_caducidad.date_format' => 'Tu fecha tiene que estar en MM/DD/YYYY'
                ]
            );

            if ($validator->fails()) {
                $excelRow = $i + 1;
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la fila '.$excelRow.': '.$validator->errors()->first(),
                    'errors'  => $validator->errors(),
                    'fila'    => $excelRow,
                ], 422);
            }

            $filas[] = $row;
        }
        try {
            DB::beginTransaction();
            for ($i = 0; $i < count($filas); $i++) {
                $carnet = new Carnet();

                $carnet->fill($filas[$i]);
                $carnet->save();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar los carnets.',
                'error'   => $th->getMessage(),
            ], 500);
        }
        

        // return response()->json([
        //     'success' => true,
        //     'total'   => count($filas),
        //     'datos'   => $filas,
        // ]);
        return redirect()->route('carnets.subir')->with([
            'success' => 'Se añadieron correctamente los registros',
            'total'   => 'Total añadidos: '.count($filas),
        ]);
    }

    public function descargarPlantilla(){
        return Storage::download('verificar/carnets/plantilla_carnets.xlsx');
    }
}
