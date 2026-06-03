@extends('layouts.admin')

@section('title', 'Ajustes del Sitio')

@section('content')
<div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Ajustes del Sitio</h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label for="site_name" class="form-label">Nombre del Sitio</label>
                        <input type="text" 
                               class="form-control @error('site_name') is-invalid @enderror" 
                               id="site_name" 
                               name="site_name" 
                               value="{{ old('site_name', $siteName) }}" 
                               placeholder="Ingrese el nombre del sitio">
                        @error('site_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-4">
                        <label for="logo" class="form-label">Logo del Sitio</label>
                        <input type="file" 
                               class="form-control @error('logo') is-invalid @enderror" 
                               id="logo" 
                               name="logo" 
                               accept="image/*">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group mb-4">
                        <label class="form-label">Logo Actual</label>
                        <div class="d-flex align-items-center">
                            <img src="{{ $currentLogo }}" 
                                 alt="Logo actual" 
                                 class="img-fluid" 
                                 style="max-height: 100px; max-width: 200px; object-fit: contain;">
                            <div class="ms-3">
                                <small class="text-muted">Ruta: {{ $currentLogo }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-save">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17,21 17,13 7,13 7,21"></polyline>
                                <polyline points="7,3 7,8 15,8"></polyline>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview logo before upload
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.style.maxHeight = '100px';
                preview.style.maxWidth = '200px';
                preview.style.objectFit = 'contain';
                preview.style.marginTop = '10px';
                
                const container = document.querySelector('.form-group:has(#logo)');
                const existingPreview = container.querySelector('img[src^="data:"]');
                if (existingPreview) {
                    existingPreview.remove();
                }
                container.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
