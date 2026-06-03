<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        // If the checkbox is unchecked, it won't be sent in the request
        $publicFormEnabled = $request->has('public_form_enabled') ? '1' : '0';
        
        Setting::set('public_form_enabled', $publicFormEnabled);
        
        return redirect()->back()->with('success', 'Configuraciones guardadas correctamente.');
    }
}
