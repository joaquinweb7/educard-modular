<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Credencial;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class CredencialController extends Controller
{
    public function index(Request $request){
        $query = Credencial::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombres', 'like', "%$search%")
                  ->orWhere('apellidos', 'like', "%$search%")
                  ->orWhere('cedula_identidad', 'like', "%$search%")
                  ->orWhere('codigo_credencial', 'like', "%$search%");
            });
        }
        $credenciales = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.credenciales.index', compact('credenciales'));
    }

    public function data(Request $request)
    {
        $query = Credencial::query()
        ->select([
            'id',
            'nombres',
            'apellidos',
            'cedula_identidad',
            'codigo_credencial',
            'fecha_emision',
            'fecha_caducidad',
            'cargo_principal',
            'cargo_secundario',
            'departamento',
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

        if ($request->filled('filtro_codigo_credencial')) {
            $query->where('codigo_credencial', 'like', '%'.$request->filtro_codigo_credencial.'%');
        }

        return DataTables::of($query)
            ->editColumn('created_at', fn($c) => optional($c->created_at)->toIso8601String())
            ->addColumn('edit_url', fn($c) => route('credenciales.edit', $c))
            ->addColumn('delete_url', fn($c) => route('credenciales.destroy', $c))
            ->toJson(1);
    }


    public function create(){
        return view('admin.credenciales.create');
    }

    public function store(Request $request){
        
        $request->validate([
            'nombres'=> 'required|string|max:255',
            'apellidos'=>'required|string|max:255',
            'cedula_identidad'=> 'required|string|max:255|unique:credenciales,cedula_identidad',
            'codigo_credencial' => 'required|string|max:255|unique:credenciales,codigo_credencial',
            'fecha_emision'=> 'required|string|max:255',
            'fecha_caducidad'=> 'required|string|max:255',
            'cargo_principal'=> 'required|string|max:255',
            'cargo_secundario'=> 'nullable|string|max:255',
            'departamento'=> 'nullable|string|max:255',
            'observacion'=> 'nullable|string|max:255',
        ]);
        $credencial = new Credencial();
        $credencial->nombres = $request->nombres;
        $credencial->apellidos = $request->apellidos;
        $credencial->cedula_identidad = $request->cedula_identidad;
        $credencial->codigo_credencial = $request->codigo_credencial;
        $credencial->fecha_emision = $request->fecha_emision;
        $credencial->fecha_caducidad = $request->fecha_caducidad;
        $credencial->cargo_principal = $request->cargo_principal ;
        $credencial->cargo_secundario = $request->cargo_secundario;
        $credencial->departamento = $request->departamento;
        $credencial->observacion = $request->observacion;
        $credencial->estado = $request->estado;
        if ( $credencial->save()){
            return redirect()->route('credenciales.index')->with('success','Creado correctamente');
        }
    }

    public function edit(Credencial $credencial){
        return view('admin.credenciales.edit', compact('credencial'));
    }

    public function update(Request $request, Credencial $credencial){
        
        $request->validate([
            'nombres'=> 'required|string|max:255',
            'apellidos'=>'required|string|max:255',
            'cedula_identidad'=> 'required|string|max:255|unique:credenciales,cedula_identidad,' . $credencial->id,
            'codigo_credencial' => 'required|string|max:255|unique:credenciales,codigo_credencial,' . $credencial->id,
            'fecha_emision'=> 'required|string|max:255',
            'fecha_caducidad'=> 'required|string|max:255',
            'cargo_principal'=> 'required|string|max:255',
            'cargo_secundario'=> 'nullable|string|max:255',
            'departamento'=> 'nullable|string|max:255',
            'observacion'=> 'nullable|string|max:255',
        ]);
        $credencial->nombres = $request->nombres;
        $credencial->apellidos = $request->apellidos;
        $credencial->cedula_identidad = $request->cedula_identidad;
        $credencial->codigo_credencial = $request->codigo_credencial;
        $credencial->fecha_emision = $request->fecha_emision;
        $credencial->fecha_caducidad = $request->fecha_caducidad;
        $credencial->cargo_principal = $request->cargo_principal ;
        $credencial->cargo_secundario = $request->cargo_secundario;
        $credencial->departamento = $request->departamento;
        $credencial->observacion = $request->observacion;
        $credencial->estado = $request->estado;
        if ( $credencial->save()){
            return redirect()->route('credenciales.index')->with('success','Actualizado correctamente');
        }
    }

    public function destroy(Credencial $credencial){
        $credencial->delete();
        return redirect()->route('credenciales.index')->with('success','Eliminado correctamente');
    }



    public function cargar(){

        return view('admin.credenciales.cargar');
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
            $raw = array_slice($data[$i] ?? [], 0, 9); // Now 9 columns

            // Saltar filas totalmente vacías
            if (count(array_filter($raw, fn($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            foreach ([4, 5] as $col) {
                if (isset($raw[$col]) && $raw[$col] !== '' && is_numeric($raw[$col])) {
                    $raw[$col] = ExcelDate::excelToDateTimeObject($raw[$col])->format('Y/m/d');
                }
            }
            $row = [
                'nombres'           => trim((string)($raw[0] ?? '')),
                'apellidos'         => trim((string)($raw[1] ?? '')),
                'cedula_identidad'  => trim((string)($raw[2] ?? '')),
                'codigo_credencial' => trim((string)($raw[3] ?? '')),
                'fecha_emision'     => (string)($raw[4] ?? ''),
                'fecha_caducidad'   => (string)($raw[5] ?? ''),
                'cargo_principal'   => trim((string)($raw[6] ?? '')),
                'cargo_secundario'  => trim((string)($raw[7] ?? '')),
                'departamento'      => trim((string)($raw[8] ?? '')),
            ];
 
            // Validar esta fila
            $validator = Validator::make($row, [
                'nombres'           => 'required|string|max:255',
                'apellidos'         => 'required|string|max:255',
                'cedula_identidad'  => 'required|string|max:255|unique:credenciales,cedula_identidad',
                'codigo_credencial' => 'required|string|max:255|unique:credenciales,codigo_credencial',
                'fecha_emision'     => 'required|date_format:Y/m/d',
                'fecha_caducidad'   => 'required|date_format:Y/m/d|after:fecha_emision',
                'cargo_principal'   => 'required|string|max:255',
                'cargo_secundario'  => 'nullable|string|max:255',
                'departamento'      => 'nullable|string|max:255',
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
                $credencial = new Credencial();
                $credencial->fill($filas[$i]);
                $credencial->save();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar las credenciales.',
                'error'   => $th->getMessage(),
            ], 500);
        }
        
        return redirect()->route('credenciales.subir')->with([
            'success' => 'Se añadieron correctamente los registros',
            'total'   => 'Total añadidos: '.count($filas),
        ]);
    }

    public function descargarPlantilla(){
        return Storage::download('verificar/credenciales/plantilla_credenciales.xlsx');
    }
}
