<?php

namespace App\Http\Controllers\Admin\Certificados;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

use App\Models\Certificados\Smtp;
use App\Models\Certificados\Certificado;
use App\Models\Certificados\PlantillaCertificado;
class EmailController extends Controller
{
    public function __construct()
    {
        $smtp = Smtp::first();
        if (request()->route()->getName() !== 'certificados.smtp.index' ) {
            if ($smtp !== null) {
                Config::set('mail.mailers.smtp', [
                    'transport' => 'smtp',
                    'host' => $smtp->host,
                    'port' => $smtp->port,
                    'encryption' => $smtp->encryption,
                    'username' => $smtp->username,
                    'password' => $smtp->password,
                ]);
                Config::set('mail.default', 'smtp');

                // Configurar el "from" address y name
                Config::set('mail.from', [
                    'address' => $smtp->from_address,  // Cambia la dirección de correo
                    'name' => $smtp->from_name     // Cambia el nombre
                ]);
            } else {
                session()->flash('error', 'No se ha configurado un servidor SMTP. Por favor, configúralo.');
            }
        }
        
    }

    public function index(){
        $smtp = Smtp::first();
        return view('admin.certificados.email.smtp-detalle', compact('smtp'));
    }

    public function show()
    {
        $smtp = Smtp::first();
        // Si no hay un objeto SMTP, cargamos uno vacío
        if (!$smtp) {
            $smtp = new Smtp();
        }
        return response()->json($smtp);  // Retorna los datos del SMTP en formato JSON
    }

    public function update(Request $request)
    {
        // Validar los datos del formulario
        $smtp = Smtp::first();
        $request->validate([
            'host' => 'required|string|max:100',
            'port' => 'required|string|max:100',
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:100',
            'encryption' => 'required|string|max:100',
            'from_address' => 'required|email',
            'from_name' => 'required|string|max:100',
        ]);

        // Si el objeto SMTP no existe, lo creamos
        if (!$smtp) {
            $smtp = new Smtp();
        }
        // Asignar los datos del formulario al objeto
        $smtp->host = $request->host;
        $smtp->port = $request->port;
        $smtp->username = $request->username;
        $smtp->password = $request->password;
        $smtp->encryption = $request->encryption;
        $smtp->from_address = $request->from_address;
        $smtp->from_name = $request->from_name;
        $smtp->save();

        return response()->json([
            'message' => 'Datos SMTP actualizados correctamente',
            'smtp' => Config::get('mail.mailers.smtp'),
            'from' => Config::get('mail.from')


        ]);  // Retornamos los datos actualizados
    }

    public function emailTestView(){
        // Verifica si hay un mensaje de error en la sesión (redirección desde el constructor)
        if (session()->has('error')) {
            return redirect()->route('admin.certificados.smtp.index')->with('error', session('error'));
        }

        $plantillas = PlantillaCertificado::select('id as value', 'nombre', 'imagen')->get();
        return view('admin.certificados.email.email-test', compact('plantillas'));
    }

    public function emailTest(Request $request){

        $request->validate([
            'nombre' => 'required|string|max:100',
            'curso' => 'required|string|max:100',
            'codigo' => 'required|string|max:100',
            'email' => 'required|email',
            'certificado' => 'required|max:255'
        ]);

        if (Smtp::first() === null) {
            return response()->json([
                'message' => 'No se ha configurado un servidor SMTP. Por favor, configúralo.',
                'type' => 'error'
            ]);
        }

        $nombre = $request->nombre;
        $curso = $request->curso;
        $codigo = $request->codigo;
        $email = $request->email;
        $data = [
            '1',
            $nombre,
            $curso,
            'carnet',
            $email,
            $codigo,
        ];

        $idPlantilla = $request->input('certificado');
        $plantilla = PlantillaCertificado::where('id', $idPlantilla)->first();
        $pdf = new GeneradorCertificadoController();
        $mipdf = $pdf->genCert($nombre,$curso,$codigo, $plantilla);
        $pdfContent = $mipdf->Output($codigo . ".pdf", "S"); // 'S'
        // $path = Storage::put('pdf_files/' . 'codigo' . '.pdf', $pdfContent);
        
        try {
            Mail::to($email)->send(new \App\Mail\SendPDFMail($pdfContent, $data[1], $data[2], $data[5]));
        } catch (\Throwable $th) {
           return response()->json([
            'message' => 'Ocurrió un error, revisa tus credenciales SMTP',
            'type' => 'error'
           ]);
        }

        return response()->json([
            'message' => 'Correo de prueba correctamente a ' . $email,
            'type' => 'success'
        ]);
    }


    public function send(Certificado $certificado){

    }

    public function sendEmail(Certificado $certificado){

        // return $certificado;
        $plantilla = $certificado->plantilla;
        if($certificado){
            $pdf = new GeneradorCertificadoController();
            $mipdf = $pdf->genCert(nombre: $certificado->nombre_estudiante, curso: $certificado->curso->nombre, codigo: $certificado->codigo, plantilla: $plantilla);
            $pdfContent = $mipdf->Output($certificado->codigo . ".pdf", "S"); // 'S'
            // $path = Storage::put('pdf_files/' . 'codigo' . '.pdf', $pdfContent);
            if ($certificado->email) {
                try {
                    Mail::to($certificado->email)->send(new \App\Mail\SendPDFMail($pdfContent, $certificado->nombre_estudiante, $certificado->curso->nombre, $certificado->codigo));
                } catch (\Throwable $th) {
                    return response()->json([
                        'message' => 'Ocurrió un error, revisa tus credenciales SMTP',
                        'type' => 'error',
                        'error' => $th->getMessage()
                    ]);
                }
                return redirect()->back()->with('success','Se envió correctamente el correo a'. $certificado->email);
            }else{
                return redirect()->back()->with('error', 'No se pudo enviar al correo, parece que el correo está mal: '. $certificado->email);
            }
            
            
        }

    }

    public function sendEmails( )
    {

        $csvFilePath = storage_path("app/private/csv-files/lista.csv"); // Ruta del archivo CSV

        if (!File::exists($csvFilePath)) {
            return response()->json([
                'message' => 'No ha cargado un archivo CSV aún',
                'type' => 'error'
            ]);
        }

        if (Smtp::first() === null) {
            return response()->json([
                'message' => 'No se ha configurado un servidor SMTP. Por favor, configúralo.',
                'type' => 'error'
            ]);
        }
        
        $csvController = new CsvController(); // Instancia del controlador
        $csvData = $csvController->parseData($csvFilePath); // Llama al método parseData con la ruta del archivo

        $plantilla = Certificado::where('nombre_estudiante', $csvData[0][1])->where('nombre_curso_id', $csvData[0][2])->first()->plantilla;

        $count = 0;
        foreach ($csvData as $certificado) {
           if($certificado){
               $pdf = new GeneradorCertificadoController();
               $mipdf = $pdf->genCert($certificado[1], $certificado[2], $certificado[5], $plantilla);
               $pdfContent = $mipdf->Output($certificado[5] . ".pdf", "S"); // 'S'
               // $path = Storage::put('pdf_files/' . 'codigo' . '.pdf', $pdfContent);
               if ($certificado[4]) {
                    try {
                        Mail::to($certificado[4])->send(new \App\Mail\SendPDFMail($pdfContent, $certificado[1], $certificado[2], $certificado[5]));
                    } catch (\Throwable $th) {
                        return response()->json([
                            'message' => 'Ocurrió un error, revisa tus credenciales SMTP',
                            'type' => 'error',
                            'error' => $th->getMessage()
                        ]);
                    }
                    $count++;
               }
               
           }
        }

        return response()->json([
            'message' => 'Correos enviados correctamente a ' . $count,
            'type' => 'error'
        ]);
    }
}
