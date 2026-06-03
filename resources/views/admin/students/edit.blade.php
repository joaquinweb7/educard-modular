@extends('layouts.admin')
@section('heading','Editar estudiante')
@section('content')
<div class="panel">
<form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="form-grid">
    <div class="field"><label>Nombres</label><input name="names" value="{{ old('names', $student->names) }}" required></div>
    <div class="field"><label>Apellidos</label><input name="lastnames" value="{{ old('lastnames', $student->lastnames) }}" required></div>
    <div class="field"><label>C.I.</label><input name="ci_number" value="{{ old('ci_number', $student->ci_number) }}" required></div>
    <div class="field"><label>Código estudiantil manual</label><input name="student_code" value="{{ old('student_code', $student->student_code) }}" placeholder="Déjalo vacío para automático"></div>
    <div class="field">
        <label>Carrera</label>
        <select name="career_id" class="dependent-career" required>
            <option value="">Seleccione...</option>
            @foreach($careers as $career)
                <option value="{{ $career->id }}" @selected(old('career_id', $student->career_id) == $career->id)>{{ $career->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>Semestre</label>
        <select name="semester_id" required>
            <option value="">Seleccione...</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id }}" @selected(old('semester_id', $student->semester_id) == $semester->id)>{{ $semester->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="field"><label>Gestión</label><select name="gestion" class="dependent-gestion" data-old="{{ old('gestion', $student->gestion) }}" required><option value="">Seleccione...</option></select></div>
    <div class="field"><label>Turno</label><select name="turno" class="dependent-turno" data-old="{{ old('turno', $student->turno) }}" required><option value="">Seleccione...</option></select></div>
    <div class="field"><label>Grupo</label><select name="grupo" class="dependent-grupo" data-old="{{ old('grupo', $student->grupo) }}" required><option value="">Seleccione...</option></select></div>
    <div class="field"><label>Fotografía (Opcional)</label><input type="file" name="photo" accept="image/*" data-photo-input></div>
</div>
<label style="display:block;margin:14px 0"><input type="checkbox" name="auto_code" value="1"> Asignar nuevo código automáticamente</label>

@if($student->photo_path)
    <div style="margin-top:14px">
        <p style="font-size:12px;color:var(--text-muted);margin-bottom:4px">Foto actual:</p>
        <img src="{{ asset('storage/'.$student->photo_path) }}" style="width:100px;height:auto;border-radius:8px">
    </div>
@endif

<img data-photo-preview class="photo-preview" alt="Vista previa">
<div style="margin-top:18px">
    <button class="btn primary">Actualizar estudiante</button>
    <a href="{{ route('admin.students.index') }}" class="btn secondary">Cancelar</a>
</div>
</form>
</div>
<script src="{{ asset('js/photo-preview.js') }}"></script>
<script src="{{ asset('js/academic-dropdowns.js') }}"></script>
@endsection
