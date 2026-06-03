<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardTemplate;
use Illuminate\Http\Request;
use App\Models\Font;

class CardTemplateController extends Controller
{
    public function index()
    {
        return view('admin.card-templates.index', ['templates' => CardTemplate::latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.card-templates.form', ['template' => new CardTemplate()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'background' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'width' => ['required', 'numeric', 'min:3', 'max:100'],
            'height' => ['required', 'numeric', 'min:3', 'max:100'],
        ]);

        $backgroundPath = $request->hasFile('background') ? $request->file('background')->store('card-templates/backgrounds', 'public') : null;

        $template = CardTemplate::create([
            'name' => $data['name'],
            'background_path' => $backgroundPath,
            'width' => round($data['width'] * 37.795276),
            'height' => round($data['height'] * 37.795276),
            'design_json' => null,
            'is_default' => false,
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.card-templates.edit', $template)->with('success', 'Plantilla creada. Ahora puedes diseñarla.');
    }

    public function edit(CardTemplate $cardTemplate)
    {
        $fonts = Font::all();
        return view('admin.card-templates.designer', ['template' => $cardTemplate, 'fonts' => $fonts]);
    }

    public function update(Request $request, CardTemplate $cardTemplate)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'design_json' => ['nullable', 'string'],
            'background' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'width' => ['nullable', 'numeric', 'min:3', 'max:100'],
            'height' => ['nullable', 'numeric', 'min:3', 'max:100'],
        ]);

        if ($request->hasFile('background')) {
            $cardTemplate->background_path = $request->file('background')->store('card-templates/backgrounds', 'public');
        }

        $cardTemplate->fill(array_filter([
            'name' => $data['name'] ?? null,
            'design_json' => $data['design_json'] ?? null,
            'width' => isset($data['width']) ? round($data['width'] * 37.795276) : null,
            'height' => isset($data['height']) ? round($data['height'] * 37.795276) : null,
        ], fn ($v) => ! is_null($v)));

        $cardTemplate->save();

        return back()->with('success', 'Plantilla guardada correctamente.');
    }

    public function destroy(CardTemplate $cardTemplate)
    {
        $cardTemplate->delete();
        return redirect()->route('admin.card-templates.index')->with('success', 'Plantilla eliminada.');
    }
}
