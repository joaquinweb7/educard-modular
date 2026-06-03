<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Font;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FontController extends Controller
{
    public function index()
    {
        $fonts = Font::all();
        return view('admin.fonts.index', compact('fonts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fonts',
            'file' => 'required|file|max:5120',
        ]);

        $extension = $request->file('file')->getClientOriginalExtension();
        if (!in_array(strtolower($extension), ['ttf', 'woff', 'woff2'])) {
            return back()->with('error', 'El archivo debe ser una fuente válida (.ttf, .woff, .woff2).');
        }

        $path = $request->file('file')->storeAs('fonts', \Illuminate\Support\Str::uuid() . '.' . $extension, 'public');

        Font::create([
            'name' => $request->name,
            'file_path' => $path,
        ]);

        return redirect()->route('admin.fonts.index')->with('success', 'Fuente instalada correctamente.');
    }

    public function destroy(Font $font)
    {
        Storage::disk('public')->delete($font->file_path);
        $font->delete();

        return redirect()->route('admin.fonts.index')->with('success', 'Fuente eliminada correctamente.');
    }
}
