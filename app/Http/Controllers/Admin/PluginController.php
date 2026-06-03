<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Services\PluginManager;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function index()
    {
        return view('admin.plugins.index', ['plugins' => Plugin::orderBy('display_name')->get()]);
    }

    public function store(Request $request, PluginManager $manager)
    {
        if (!config('app.allow_plugin_upload', false)) {
            return back()->withErrors(['plugin_zip' => 'La subida de plugins está deshabilitada por razones de seguridad en producción. Activa ALLOW_PLUGIN_UPLOAD=true en el archivo .env si necesitas subir un plugin manualmente.']);
        }

        $request->validate(['plugin_zip' => ['required', 'file', 'mimes:zip', 'max:10240']]);

        try {
            $plugin = $manager->installFromZip($request->file('plugin_zip')->getRealPath());
            return back()->with('success', 'Plugin instalado: '.$plugin->display_name);
        } catch (\Throwable $e) {
            return back()->withErrors(['plugin_zip' => $e->getMessage()]);
        }
    }

    public function activate(Plugin $plugin)
    {
        $plugin->update(['status' => 'active', 'activated_at' => now()]);
        return back()->with('success', 'Plugin activado.');
    }

    public function deactivate(Plugin $plugin)
    {
        $plugin->update(['status' => 'inactive']);
        return back()->with('success', 'Plugin desactivado.');
    }

    public function destroy(Plugin $plugin)
    {
        $plugin->delete();
        return back()->with('success', 'Plugin eliminado del registro. Los archivos físicos no se eliminan automáticamente por seguridad.');
    }
}
