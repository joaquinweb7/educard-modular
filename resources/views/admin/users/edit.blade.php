@extends('layouts.admin')

@section('title', 'Editar Usuario')
@section('heading', 'Editar Usuario: ' . $user->name)

@section('content')
<div class="panel">
    <p style="margin-bottom: 20px; color: #666; font-size: 14px;">Actualiza la información del usuario y sus permisos de acceso.</p>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @method('PUT')
        @include('admin.users.form')
    </form>
</div>
@endsection
