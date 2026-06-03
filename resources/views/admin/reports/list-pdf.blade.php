<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Impresiones</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .title { text-align: center; margin-bottom: 20px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; color: #fff; }
        .badge-success { background-color: #10b981; }
        .badge-warning { background-color: #f59e0b; }
    </style>
</head>
<body>
    <div class="title">
        <h2>Reporte Detallado de Impresiones de Carnets</h2>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>C.I.</th>
                <th>Código</th>
                <th>Carrera</th>
                <th>Semestre</th>
                <th>Gestión</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentsList as $student)
            <tr>
                <td>{{ $student->names }} {{ $student->lastnames }}</td>
                <td>{{ $student->ci_number }}</td>
                <td>{{ $student->student_code ?? 'S/C' }}</td>
                <td>{{ $student->career->name ?? '-' }}</td>
                <td>{{ $student->semester->name ?? '-' }}</td>
                <td>{{ $student->gestion }}</td>
                <td>
                    @if($student->is_printed)
                        <span class="badge badge-success">Impreso {{ $student->printed_at ? $student->printed_at->format('d/m/Y') : '' }}</span>
                    @else
                        <span class="badge badge-warning">Pendiente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
