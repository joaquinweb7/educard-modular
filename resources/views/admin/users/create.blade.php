@extends('layouts.admin')

@section('title', 'Nuevo Usuario')
@section('heading', 'Crear Nuevo Usuario')

@section('content')
<div class="panel">
    <p style="margin-bottom: 20px; color: #666; font-size: 14px;">Agrega un usuario y asígnale los permisos necesarios.</p>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @include('admin.users.form')
    </form>
</div>
@endsection
