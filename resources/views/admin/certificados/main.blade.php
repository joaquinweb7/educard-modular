@extends('layouts.admin')
@section('heading', 'Carga Masiva y Generación de Certificados')
@section('content')

@if (session('success'))
    <div style="background-color: var(--success); color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        <strong>Éxito:</strong> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div style="background-color: var(--danger); color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
        <strong>Error:</strong> {{ session('error') }}
    </div>
@endif

<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <!-- 1. Cargar Archivo -->
    <div class="panel" style="display: flex; flex-direction: column;">
        <div class="panel-heading d-flex justify-content-between align-items-center">
            <h3 class="panel-title">1. Cargar Archivo</h3>
            <i data-lucide="upload-cloud"></i>
        </div>
        <div class="panel-body mt-3" style="flex: 1; display: flex; flex-direction: column;">
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
                Carga un archivo CSV con los datos. <a href="{{ route('admin.certificados.descargar.plantilla') }}" style="color: var(--primary); text-decoration: underline;">Descargar plantilla CSV</a>.
            </p>
            <div class="field mb-3">
                <input type="file" id="csv_file" accept=".csv, .xlsx, .xls" class="input" name="csv_file">
            </div>
            <div class="field mb-3">
                <label for="certificado">Plantilla Base</label>
                <select id="certificado" name="certificado" class="input">
                    <option value="" selected disabled>Seleccione una plantilla</option>
                    @foreach($plantillas as $plantilla)
                        <option value="{{ $plantilla->value }}">{{ $plantilla->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field mb-3">
                <label for="curso">Curso Asociado</label>
                <select id="curso" name="curso" class="input">
                    <option value="" selected disabled>Seleccione un curso</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary w-100 mt-auto" type="button" id="btnSendCSV">
                <i data-lucide="database"></i> Subir a la Base de Datos
            </button>
        </div>
    </div>

    <!-- 2. Generar PDFs -->
    <div class="panel" style="display: flex; flex-direction: column;">
        <div class="panel-heading d-flex justify-content-between align-items-center">
            <h3 class="panel-title">2. Generar Certificados</h3>
            <i data-lucide="file-text"></i>
        </div>
        <div class="panel-body mt-3" style="flex: 1; display: flex; flex-direction: column;">
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
                Genera los certificados en formato PDF listos para impresión.
            </p>
            <button class="btn btn-success w-100 mt-auto" id="generate">
                <i data-lucide="printer"></i> Visualizar/Imprimir PDF
            </button>
        </div>
    </div>

    <!-- 3. Descargar ZIP -->
    <div class="panel" style="display: flex; flex-direction: column;">
        <div class="panel-heading d-flex justify-content-between align-items-center">
            <h3 class="panel-title">3. Descargar Archivo</h3>
            <i data-lucide="download"></i>
        </div>
        <div class="panel-body mt-3" style="flex: 1; display: flex; flex-direction: column;">
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
                Descarga todos los certificados generados a tu dispositivo.
            </p>
            <button class="btn btn-secondary w-100 mt-auto" type="button" id="download">
                <i data-lucide="download-cloud"></i> Descargar PDF Global
            </button>
        </div>
    </div>

    <!-- 4. Enviar Correos -->
    <div class="panel" style="display: flex; flex-direction: column;">
        <div class="panel-heading d-flex justify-content-between align-items-center">
            <h3 class="panel-title">4. Enviar Correos</h3>
            <i data-lucide="mail"></i>
        </div>
        <div class="panel-body mt-3" style="flex: 1; display: flex; flex-direction: column;">
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
                Envía los certificados masivamente por correo electrónico a los estudiantes.
            </p>
            <button class="btn btn-danger w-100 mt-auto" type="button" id="sendEmailBtn">
                <span id="btnText"><i data-lucide="send"></i> Procesar Envío SMTP</span>
                <span id="loader" style="display:none;"><i data-lucide="loader"></i> Enviando...</span>
            </button>
        </div>
    </div>
</div>

<!-- Panel de Resultados -->
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Registro de Estudiantes Cargados</h3>
    </div>
    <div class="panel-body mt-3">
        <div style="background-color: var(--bg-hover); padding: 1rem; border-radius: 8px; font-family: monospace; min-height: 150px; max-height: 400px; overflow-y: auto;" id="info-section">
            --- Esperando carga de datos ---
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('plugins/jquery.3.7.1.js') }}"></script>
<script>
    function showToast($message, $name, $type) {
        const isDarkMode = localStorage.getItem("theme") ? JSON.parse(localStorage.getItem("theme")).settings.layout.darkMode : false;
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: $type,
            title: $name ? $message + ": " + $name : $message,
            showConfirmButton: false,
            showCloseButton: true,
            timer: 3000,
            timerProgressBar: true,
            background: isDarkMode ? "#333" : "#fff",
            color: isDarkMode ? "#fff" : "#000",
        });
    }

    let currentBatchId = null;

    $('#sendEmailBtn').on('click', function(){
        let $button = $(this);
        $.ajax({
            url: '{{ route('admin.certificados.smtp.sendEmails') }}',
            type:'POST',
            data:{
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $button.find('#btnText').hide();
                $button.find('#loader').show();
                $button.attr('disabled', true);
            },
            success: function(data) {
                showToast(data.message, '', data.type);
            },
            error: function() {
                showToast('Revisa tu configuración SMTP', '', 'error');
            },
            complete: function() {
                $button.find('#btnText').show();
                $button.find('#loader').hide();
                $button.removeAttr('disabled');
            }
        });
    });

    $('#btnSendCSV').on('click', function(e) {
        e.preventDefault();

        let fileInput = $('#csv_file')[0].files[0];
        if (!fileInput) {
            showToast('Seleccione un archivo CSV', '', 'error');
            return;
        }

        var formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('csv_file', fileInput);
        formData.append('certificado', $('#certificado').val() || '');
        formData.append('curso', $('#curso').val() || '');

        $.ajax({
            url: "{{ route('admin.certificados.upload-csv') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                currentBatchId = data.batch_id;
                showToast(data.message, fileInput.name, data.type);
                $('#csv_file').val('');
                
                $('#info-section').empty();
                $('#info-section').append('<div style="color:var(--success);">--- Base de datos actualizada con éxito ---</div>');

                data.students.forEach((student, index) => {
                    setTimeout(() => {
                        var text = `[OK] Añadido: ${student[1]} - Curso ID: ${student[2]} - Código: ${student[5]}`;
                        $('#info-section').append(`<div style="margin-top:4px;">${text}</div>`);
                        // Auto scroll
                        var elem = document.getElementById('info-section');
                        elem.scrollTop = elem.scrollHeight;
                    }, index * 100);
                });
            },
            error: function(jqXHR) {
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    showToast(jqXHR.responseJSON.message, '', 'error');
                } else {
                    showToast('Error con el servidor', '', 'error');
                }
            }
        });
    });

    $('#generate').on('click', function(){
        if (!currentBatchId) {
            showToast('Primero debes cargar un archivo válido', '', 'error');
            return;
        }
        window.open("{{ route('admin.certificados.generate') }}?batch_id=" + currentBatchId, '_blank');
    });
    
    $('#download').on('click', function (e) {
        if (!currentBatchId) {
            showToast('No hay documentos generados en esta sesión', '', 'error');
            return;
        }
        window.location.href = "{{ route('admin.certificados.generate') }}?batch_id=" + currentBatchId;
    });

    let error = '{{ session('error') }}';
    if (error) {
        showToast(error, '', 'error');
    }
</script>
@endpush
