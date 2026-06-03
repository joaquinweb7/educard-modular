<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión — EduCard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="logo">⬡ EduCard</div>
        <div class="tagline">Sistema modular de carnets estudiantiles</div>

        @if($errors->any())
            <div class="alert error"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field" style="margin-bottom:14px">
                <label for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       placeholder="admin@educard.test" required autofocus>
            </div>
            <div class="field" style="margin-bottom:24px">
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password"
                       placeholder="••••••••" required>
            </div>
            <button id="btn-login" type="submit" class="btn primary w-100">
                Iniciar sesión
            </button>
        </form>
    </div>
</div>
</body>
</html>
