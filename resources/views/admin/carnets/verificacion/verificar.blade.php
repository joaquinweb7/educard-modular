@extends('main-1.verificar.layouts.app')
@section('content') 
<style>
@media (max-width: 768px) {
  .table-vertical thead {
    display: none; /* Ocultar encabezado SOLO en móviles */
  }
  .table-vertical tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 8px;
  }
  .table-vertical td {
    display: flex;
    justify-content: space-between;
    padding: 8px;
    border: none;
    border-bottom: 1px solid #eee;
  }
  .table-vertical td:last-child {
    border-bottom: none;
  }
  .table-vertical td::before {
    content: attr(data-label);
    font-weight: bold;
    margin-right: 8px;
    color: #444;
  }
}

</style>
<style>
    .logo-verificacion {
    max-width: 500; /* tamaño en pantallas grandes */
    height: auto;
}

@media (max-width: 768px) {
  .logo-verificacion {
    max-width: 300; /* tamaño reducido en móviles */
  }
}

</style>
    <div class="col-12 d-flex justify-content-center align-items-center">
        <div class="col-12 col-sm-12 col-md-10 col-lg-10">
            @if (request()->has('code'))
                @if ($carnet)
                <div class="alert alert-arrow-right alert-icon-right alert-light-success alert-dismissible fade show mb-4"
                    role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-alert-circle">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12" y2="16"></line>
                    </svg>
                    <strong>CARNET VERIFICADO!</strong>
                </div>
                @else
                <div class="alert alert-arrow-right alert-icon-right alert-light-danger alert-dismissible fade show mb-4"
                    role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-alert-circle">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12" y2="16"></line>
                    </svg>
                    <strong>EL CARNET NO COINCIDE CON NINGUNO DE NUESTROS REGISTROS</strong>
                </div>
                @endif
            @endif
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12 text-center align-items-center">
                            <h4>Verificar Carnet</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-md-6 mx-auto">
                            <center>
                                <form method="get">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Introduce
                                            el
                                            Código</span>
                                        <input required  type="text" class="form-control" placeholder="Código" name="code"
                                            aria-label="Código" aria-describedby="basic-addon1">
                                    </div>
                                    <button class="btn btn-secondary mb-4 me-4" type="submit">VERIFICAR</button>
                                </form>
                            </center>
                        </div>
                    </div>
                    
                   
                   <div class="table-responsive">
                        <table class="table table-bordered table-striped table-vertical">
                            <thead>
                                <tr>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Cédula Identidad</th>
                                    <th>Código Estudiante</th>
                                    <th>Fecha Emisión</th>
                                    <th>Fecha Caducidad</th>
                                    <th>Carrera</th>
                                    <th>Semestre</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if ($carnet)
                                    <tr>
                                        <td data-label="Nombres">{{ $carnet->nombres }}</td>
                                        <td data-label="Apellidos">{{ $carnet->apellidos }}</td>
                                        <td data-label="Cédula Identidad">{{ $carnet->cedula_identidad }}</td>
                                        <td data-label="Código Estudiante">{{ $carnet->codigo_estudiante }}</td>
                                        <td data-label="Fecha Emisión">{{ $carnet->fecha_emision }}</td>
                                        <td data-label="Fecha Caducidad">{{ $carnet->fecha_caducidad }}</td>
                                        <td data-label="Carrera">{{ $carnet->carrera }}</td>
                                        <td data-label="Semestre">{{ $carnet->semestre }}</td>
                                        <td data-label="Estado">
                                            @if(strtolower($carnet->estado) == 'vigente')
                                                <span class="badge badge-light-success">VIGENTE</span>
                                            @else
                                                <span class="badge badge-light-danger">CADUCADO</span>
                                            @endif
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="9">No se encontró ningún resultado</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{-- Logo debajo de la tabla --}}
                    <div class="text-center justify-content-center align-items-center mt-4">
                        <img src="{{ asset('logo_itecnoba.png') }}" 
                            alt="Logo ITECNOVA" 
                            class="img-fluid logo-verificacion">
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection