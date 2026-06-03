<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EduCard — Solicitud de Carnet' }}</title>
    <meta name="description" content="Sistema de carnets estudiantiles EduCard">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@yield('content')
</body>
</html>
