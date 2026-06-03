<?php

namespace App\Http\Controllers\Admin\Certificados;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use App\Models\Certificados\Certificado;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CsvController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'certificado' => 'required|string',
            'curso'=> 'required|integer|exists:certificados_cursos,id'
        ]);

        $idPlantilla = $request->input('certificado'); 
        if (!$idPlantilla) {
             return response()->json(['message' => 'Plantilla no seleccionada', 'type' => 'error'], 422);
        }

        $file = $request->file('csv_file');
        $batchId = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $batchId . '.' . $extension;

        $isStored = Storage::putFileAs('csv-files', $file, $filename);

        if ($isStored) {
            $path_file = Storage::path('csv-files/' . $filename);
            $csvData = $this->parseData($path_file, $extension);
            if (empty($csvData)) {
                return response()->json(['message' => 'El archivo está vacío o no se pudo leer', 'type' => 'error'], 422);
            }

            try {
                DB::beginTransaction();

                foreach ($csvData as $index => $student) {
                    // Saltar filas vacías
                    if (empty(array_filter($student))) continue;
                    
                    // Mapeo seguro con null coalescing por si faltan columnas en el excel
                    $row = [
                        'nombre_estudiante' => $student[1] ?? '',
                        'nombre_curso_id' => $student[2] ?? '',
                        'carnet' => $student[3] ?? '',
                        'email'=> $student[4] ?? '',
                        'codigo' => $student[5] ?? '',
                    ];
                    
                    $validator = Validator::make($row, [
                        'nombre_estudiante' => [
                            'required',
                            'string',
                            'max:255',
                        ],
                        'nombre_curso_id' => 'required|integer|exists:certificados_cursos,id',
                        'carnet' => 'required|string|max:255',
                        'email'=> 'required|string|email|max:255',
                        'codigo' => 'required|string|max:255', 
                    ]);
                    if ($validator->fails()) {
                        $fila = $index + 2; // +1 por base 0, +1 por el header saltado
                        DB::rollBack();
                        return response()->json([
                            'type' => 'error',
                            'message' => 'Error en la fila '.$fila.': '.$validator->errors()->first(),
                            'errors'  => $validator->errors(),
                            'fila'    => $fila,
                        ], 422);
                    }

                    $certificado = new Certificado();
                    $certificado->nombre_estudiante = $row['nombre_estudiante'];
                    $certificado->nombre_curso_id = $row['nombre_curso_id'];
                    $certificado->carnet = $row['carnet'];
                    $certificado->email = $row['email'];
                    $certificado->codigo = $row['codigo'];
                    $certificado->plantilla_id = $idPlantilla;
                    $certificado->batch_id = $batchId;
                    $certificado->save();
                }

                DB::commit();

                return response()->json([
                    'message' => 'Archivo subido y procesado correctamente',
                    'students' => $csvData,
                    'batch_id' => $batchId,
                    'type' => 'success'
                ], 201);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'message' => $e->getMessage(),
                    'type' => 'error'
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Error al guardar el archivo en el servidor',
                'type' =>'error'
            ], 500);
        }
    }

    public function parseData($filePath, $extension){
        $csvData = [];
        if ($extension === 'xlsx') {
            if ( $xlsx = \Shuchkin\SimpleXLSX::parse($filePath) ) {
                $rows = $xlsx->rows();
                if (!empty($rows)) {
                    array_shift($rows); // saltar headers
                    foreach ($rows as $row) {
                        $csvData[] = $row;
                    }
                }
            }
        } else {
            if (($handle = fopen($filePath, "r")) !== false) {
                $isFirstRow = true;
                while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                    if (count($row) === 1 && strpos($row[0], ';') !== false) {
                        $row = explode(';', $row[0]);
                    }
                    if ($isFirstRow) {
                        $isFirstRow = false;
                        continue; 
                    }
                    $csvData[] = $row; 
                }
                fclose($handle);
            }
        }
        return $csvData;
    }

    public function genCode(){
        return "RE-MO".date('Y')."-".substr(md5(rand()), 0, 5);
    }
}
