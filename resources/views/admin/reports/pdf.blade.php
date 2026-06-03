<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte General — EduCard</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; margin: 30px; }
        h1 { font-size: 18px; border-bottom: 2px solid #6366f1; padding-bottom: 8px; }
        h2 { font-size: 14px; margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f3f4f6; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat { padding: 12px 16px; background: #f9fafb; border-radius: 8px; }
        .stat .n { font-size: 22px; font-weight: 800; color: #6366f1; }
        .stat .l { font-size: 10px; text-transform: uppercase; color: #6b7280; }
        .footer { margin-top: 30px; font-size: 10px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Reporte General — EduCard Modular</h1>
    <p>Generado el {{ $generatedAt->format('d/m/Y H:i') }}</p>

    <div class="stats">
        <div class="stat"><div class="n">{{ $requests }}</div><div class="l">Solicitudes</div></div>
        <div class="stat"><div class="n">{{ $students }}</div><div class="l">Estudiantes</div></div>
        <div class="stat"><div class="n">{{ $generatedCards }}</div><div class="l">Carnets</div></div>
    </div>

    <h2>Estudiantes por carrera</h2>
    <table>
        <thead><tr><th>Carrera</th><th>Estudiantes</th></tr></thead>
        <tbody>
            @foreach($studentsByCareer as $c)
                <tr><td>{{ $c->name }}</td><td>{{ $c->students_count }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Estudiantes por semestre</h2>
    <table>
        <thead><tr><th>Semestre</th><th>Estudiantes</th></tr></thead>
        <tbody>
            @foreach($studentsBySemester as $s)
                <tr><td>{{ $s->name }}</td><td>{{ $s->students_count }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">EduCard Modular — Sistema de carnets estudiantiles</div>
</body>
</html>
