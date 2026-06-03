<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class ApiConnectionController extends Controller
{
    public function index()
    {
        $apiUrl = Setting::get('tramites_api_url', 'http://127.0.0.1:8000/api/v1');
        $apiKey = Setting::get('tramites_api_key', '');
        return view('admin.api-connection', compact('apiUrl', 'apiKey'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tramites_api_url' => 'required|url',
            'tramites_api_key' => 'required|string',
        ]);

        Setting::set('tramites_api_url', rtrim($request->tramites_api_url, '/'));
        Setting::set('tramites_api_key', $request->tramites_api_key);

        if ($request->action === 'test') {
            return $this->testConnection();
        }

        return back()->with('success', 'Configuración de conexión guardada correctamente.');
    }

    private function testConnection()
    {
        $apiUrl = Setting::get('tramites_api_url');
        $apiKey = Setting::get('tramites_api_key');

        try {
            // We ping an invalid CI just to test if the API rejects us due to auth or if it responds properly.
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept' => 'application/json',
            ])->get("{$apiUrl}/estudiantes/ci/test_connection_ping_000");

            if ($response->status() === 401) {
                return back()->with('error', 'Prueba Fallida: API Key incorrecta (No autorizado).');
            }
            if ($response->status() === 503) {
                return back()->with('error', 'Prueba Fallida: La API está apagada en el sistema de Trámites.');
            }
            if ($response->status() === 404 || $response->successful()) {
                // 404 means the student was not found, which means the API Key was valid and processed correctly!
                return back()->with('success', 'Configuración guardada y Prueba Exitosa: Conexión establecida correctamente con el sistema de Trámites.');
            }

            return back()->with('error', 'Prueba Fallida: El servidor respondió con estado HTTP ' . $response->status());

        } catch (\Exception $e) {
            return back()->with('error', 'Prueba Fallida: No se pudo conectar al servidor. Verifica la URL. (' . $e->getMessage() . ')');
        }
    }
}
