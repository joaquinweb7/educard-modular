@extends('layouts.admin')
@section('heading','Registro masivo')
@section('content')
<div class="panel" style="max-width: 800px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="width: 64px; height: 64px; background: rgba(99, 102, 241, 0.1); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
        </div>
        <h2 style="font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 8px;">Importación Masiva CSV</h2>
        <p class="muted" style="font-size: 14.5px;">Sube el archivo CSV exportado desde el sistema de Trámites para registrar a los estudiantes de manera automática.</p>
    </div>

    <form method="POST" action="{{ route('admin.students.import.store') }}" enctype="multipart/form-data" style="margin-bottom: 32px;">
        @csrf
        <div style="background: var(--surface-2); border: 2px dashed var(--border); border-radius: 12px; padding: 40px 20px; text-align: center; transition: all 0.3s; position: relative;" onmouseover="this.style.borderColor='var(--primary)'; this.style.background='rgba(99, 102, 241, 0.05)'" onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--surface-2)'">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-dim)" stroke-width="1.5" style="margin-bottom: 12px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <div style="font-size: 15px; font-weight: 500; color: var(--text); margin-bottom: 8px;">Selecciona tu archivo CSV</div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Formatos soportados: .csv, .txt (Delimitado por punto y coma)</div>
            <input type="file" name="csv" accept=".csv,.txt" required style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
            <div class="btn light" style="pointer-events: none;">Explorar archivos</div>
        </div>

        <div style="text-align: center; margin-top: 24px;">
            <button class="btn primary" style="padding: 12px 32px; font-size: 15px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> Comenzar Importación
            </button>
        </div>
    </form>

    <div style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 20px;">
        <h3 style="font-size: 15px; font-weight: 600; color: #10b981; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg> Estructura del Archivo
        </h3>
        <p style="font-size: 13.5px; color: var(--text-dim); margin-bottom: 12px;">El archivo CSV debe estar delimitado por punto y coma (<code>;</code>) y contener la siguiente cabecera en la primera línea. El código de estudiante es opcional (se generará uno si está vacío).</p>
        <pre style="background: var(--bg); color: var(--text); border: 1px solid var(--border); padding: 16px; border-radius: 8px; overflow: auto; font-size: 13px; line-height: 1.6; white-space: pre-wrap; font-family: 'JetBrains Mono', monospace;">codigo;nombres;apellidos;carnet;carrera;semestre;gestion;turno;grupo
202601100;JUAN CARLOS;CALLE;1234567;CONTADURÍA GENERAL;1;GESTIÓN I-2027;MAÑANA;MAÑANA A</pre>
    </div>
</div>
@endsection
