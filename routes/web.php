<?php

use App\Http\Controllers\Admin\CardDerivedController;
use App\Http\Controllers\Admin\CardGenerationController;
use App\Http\Controllers\Admin\CardTemplateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PluginController;
use App\Http\Controllers\Admin\FontController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\StudentRequestAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Public\StudentRequestController;
use App\Http\Controllers\Public\VerificarCarnetController;
use App\Http\Controllers\Public\VerificarCredencialController;
use App\Http\Controllers\Public\ActualizarFotoController;
use App\Http\Controllers\Admin\CarnetController;
use App\Http\Controllers\Admin\AdminPhotoUpdateController;
use App\Http\Controllers\Admin\CredencialController;
use App\Services\PluginManager;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('public.student-request.create'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/solicitud-carnet', [StudentRequestController::class, 'create'])->name('public.student-request.create');
Route::get('/api/tramites/estudiantes/{ci}', [StudentRequestController::class, 'validateCi'])->name('api.tramites.validate-ci');
Route::post('/solicitud-carnet', [StudentRequestController::class, 'store'])->name('public.student-request.store');
Route::post('/api/validate-photo-advanced', [StudentRequestController::class, 'validatePhotoAdvanced'])->name('api.validate-photo-advanced');
Route::get('/consultar', [StudentRequestController::class, 'track'])->name('public.student-request.track');
Route::get('/tramite/{procedure_number}', [StudentRequestController::class, 'show'])->name('public.student-request.show');
Route::get('/tramite/{procedure_number}/corregir', [StudentRequestController::class, 'edit'])->name('public.student-request.edit');
Route::post('/tramite/{procedure_number}/corregir', [StudentRequestController::class, 'resubmit'])->name('public.student-request.resubmit');
Route::get('/tramite/{procedure_number}/constancia', [StudentRequestController::class, 'downloadCertificate'])->name('public.student-request.constancy');

Route::get('/carnet', [VerificarCarnetController::class, 'index'])->name('verificar.carnet.index');
Route::get('/credencial', [VerificarCredencialController::class, 'index'])->name('verificar.credencial.index');

Route::get('/actualizar-foto', [ActualizarFotoController::class, 'create'])->name('public.actualizar-foto.create');
Route::post('/actualizar-foto', [ActualizarFotoController::class, 'store'])->name('public.actualizar-foto.store');

// VERIFICACION DE CERTIFICADOS
Route::get('/certificado', [\App\Http\Controllers\Admin\Certificados\VerificarCertificadoController::class, 'index'])->name('verificar.certificado.index');
Route::get('/descargar-certificado/{codigo}', [\App\Http\Controllers\Admin\Certificados\VerificarCertificadoController::class, 'descargarView'])->name('verificar.certificado.descargar');
Route::post('/descargar-certificado/{codigo}', [\App\Http\Controllers\Admin\Certificados\VerificarCertificadoController::class, 'descargarCertificado']);

Route::middleware(['auth', 'permission'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('fonts', FontController::class)->only(['index', 'store', 'destroy']);

    Route::post('/estudiantes/{student}/toggle-print', [StudentController::class, 'togglePrintStatus'])->name('students.toggle-print');

    Route::get('/carnets-derivados', [CardDerivedController::class, 'index'])->name('cards.derived.index');
    Route::post('/carnets-derivados/pdf', [CardDerivedController::class, 'generatePdf'])->name('cards.derived.pdf');

    Route::get('/solicitudes', [StudentRequestAdminController::class, 'index'])->name('requests.index');
    Route::get('/solicitudes/{request}', [StudentRequestAdminController::class, 'show'])->name('requests.show');
    Route::post('/solicitudes/{request}/aprobar', [StudentRequestAdminController::class, 'approve'])->name('requests.approve');
    Route::post('/solicitudes/{request}/rechazar', [StudentRequestAdminController::class, 'reject'])->name('requests.reject');
    Route::post('/solicitudes/{request}/observar', [StudentRequestAdminController::class, 'observe'])->name('requests.observe');
    Route::delete('/solicitudes/{request}', [StudentRequestAdminController::class, 'destroy'])->name('requests.destroy');

    Route::resource('/estudiantes', StudentController::class)->parameters(['estudiantes' => 'student'])->names('students')->except(['togglePrintStatus']);
    Route::get('/registro-masivo', [StudentImportController::class, 'create'])->name('students.import.create');
    Route::post('/registro-masivo', [StudentImportController::class, 'store'])->name('students.import.store');

    Route::resource('/plantillas-carnet', CardTemplateController::class)->names('card-templates')->parameters(['plantillas-carnet' => 'cardTemplate']);
    // System Settings
    Route::get('/configuraciones', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/configuraciones', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::get('/reportes/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');

    Route::resource('/usuarios', \App\Http\Controllers\Admin\UserController::class)->names('users')->parameters(['usuarios' => 'user']);

    Route::prefix('carnets')->name('carnets.')->group(function () {
        Route::get('index', [CarnetController::class, 'index'])->name('index');
        Route::get('create', [CarnetController::class, 'create'])->name('create');
        Route::post('store', [CarnetController::class, 'store'])->name('store');
        Route::get('data', [CarnetController::class, 'data'])->name('data');
        Route::get('{carnet}/edit', [CarnetController::class, 'edit'])->name('edit');
        Route::put('{carnet}', [CarnetController::class, 'update'])->name('update');
        Route::delete('{carnet}', [CarnetController::class, 'destroy'])->name('destroy');
        Route::get('subir', [CarnetController::class, 'cargar'])->name('subir');
        Route::post('subir', [CarnetController::class, 'subir'])->name('subir');
        Route::get('descargarPlantilla', [CarnetController::class,'descargarPlantilla'])->name('descargar.plantilla');
    });

    Route::prefix('carnets/photo-updates')->name('photo-updates.')->group(function () {
        Route::get('/', [AdminPhotoUpdateController::class, 'index'])->name('index');
        Route::post('{update}/approve', [AdminPhotoUpdateController::class, 'approve'])->name('approve');
        Route::post('{update}/reject', [AdminPhotoUpdateController::class, 'reject'])->name('reject');
    });

    Route::prefix('credenciales')->name('credenciales.')->group(function () {
        Route::get('index', [CredencialController::class, 'index'])->name('index');
        Route::get('create', [CredencialController::class, 'create'])->name('create');
        Route::post('store', [CredencialController::class, 'store'])->name('store');
        Route::get('data', [CredencialController::class, 'data'])->name('data');
        Route::get('{credencial}/edit', [CredencialController::class, 'edit'])->name('edit');
        Route::put('{credencial}', [CredencialController::class, 'update'])->name('update');
        Route::delete('{credencial}', [CredencialController::class, 'destroy'])->name('destroy');
        Route::get('subir', [CredencialController::class, 'cargar'])->name('subir');
        Route::post('subir', [CredencialController::class, 'subir'])->name('subir');
        Route::get('descargarPlantilla', [CredencialController::class,'descargarPlantilla'])->name('descargar.plantilla');
    });

    Route::prefix('certificados')->name('certificados.')->group(function () {
        Route::post('upload-csv', [\App\Http\Controllers\Admin\Certificados\CsvController::class, 'store'])->name('upload-csv');
        Route::post('change-image', [\App\Http\Controllers\Admin\Certificados\GeneradorCertificadoController::class, 'changeImage'])->name('change-image');
        Route::get('generate', [\App\Http\Controllers\Admin\Certificados\GeneradorCertificadoController::class, 'generate'])->name('generate');
        Route::get('download', [\App\Http\Controllers\Admin\Certificados\GeneradorCertificadoController::class, 'downloadZip'])->name('download');

        // Configuración SMTP
        Route::get('smtp', [\App\Http\Controllers\Admin\Certificados\EmailController::class, 'index'])->name('smtp.index');
        Route::get('smtp/show', [\App\Http\Controllers\Admin\Certificados\EmailController::class, 'show'])->name('smtp.show');
        Route::post('smtp/update', [\App\Http\Controllers\Admin\Certificados\EmailController::class, 'update'])->name('smtp.update');
        Route::get('smtp/email-test', [\App\Http\Controllers\Admin\Certificados\EmailController::class, 'emailTestView'])->name('smtp.email-test');
        Route::post('smtp/email-test', [\App\Http\Controllers\Admin\Certificados\EmailController::class, 'emailTest'])->name('smtp.email-test');
        Route::get('smtp/sendemails', [\App\Http\Controllers\Admin\Certificados\EmailController::class, 'sendEmails'])->name('smtp.sendEmails');
        Route::post('smtp/sendemails', [\App\Http\Controllers\Admin\Certificados\EmailController::class, 'sendEmails'])->name('smtp.sendEmails');
        Route::post('smtp/sendemail/{certificado}', [\App\Http\Controllers\Admin\Certificados\EmailController::class, 'sendEmail'])->name('smtp.sendEmail');

        // Gestión de plantillas
        Route::get('plantillas', [\App\Http\Controllers\Admin\Certificados\PlantillaCertificadoController::class, 'index'])->name('plantilla.index');
        Route::get('plantillas/create', [\App\Http\Controllers\Admin\Certificados\PlantillaCertificadoController::class, 'create'])->name('plantilla.create');
        Route::post('plantillas/store', [\App\Http\Controllers\Admin\Certificados\PlantillaCertificadoController::class, 'store'])->name('plantilla.store');
        Route::get('plantillas/{plantilla}', [\App\Http\Controllers\Admin\Certificados\PlantillaCertificadoController::class, 'show'])->name('plantilla.show');
        Route::get('plantillas/{plantilla}/edit', [\App\Http\Controllers\Admin\Certificados\PlantillaCertificadoController::class, 'edit'])->name('plantilla.edit');
        Route::get('plantillas/{plantilla}/designer', [\App\Http\Controllers\Admin\Certificados\PlantillaCertificadoController::class, 'designer'])->name('plantilla.designer');
        Route::put('plantillas/{plantilla}', [\App\Http\Controllers\Admin\Certificados\PlantillaCertificadoController::class, 'update'])->name('plantilla.update');
        Route::delete('plantillas/{plantilla}', [\App\Http\Controllers\Admin\Certificados\PlantillaCertificadoController::class, 'destroy'])->name('plantilla.destroy');

        // Gestión de Cursos
        Route::get('cursos', [\App\Http\Controllers\Admin\Certificados\CursoCertificadoController::class, 'index'])->name('curso.index');
        Route::get('cursos/data', [\App\Http\Controllers\Admin\Certificados\CursoCertificadoController::class, 'data'])->name('curso.data');
        Route::get('cursos/create', [\App\Http\Controllers\Admin\Certificados\CursoCertificadoController::class, 'create'])->name('curso.create');
        Route::post('cursos/store', [\App\Http\Controllers\Admin\Certificados\CursoCertificadoController::class, 'store'])->name('curso.store');
        Route::get('cursos/{curso}', [\App\Http\Controllers\Admin\Certificados\CursoCertificadoController::class, 'show'])->name('curso.show');
        Route::get('cursos/{curso}/edit', [\App\Http\Controllers\Admin\Certificados\CursoCertificadoController::class, 'edit'])->name('curso.edit');
        Route::put('cursos/{curso}', [\App\Http\Controllers\Admin\Certificados\CursoCertificadoController::class, 'update'])->name('curso.update');
        Route::delete('cursos/{curso}', [\App\Http\Controllers\Admin\Certificados\CursoCertificadoController::class, 'destroy'])->name('curso.destroy');

        // Generación masiva de certificados
        Route::get('/test/{plantilla}', [\App\Http\Controllers\Admin\Certificados\GeneradorCertificadoController::class, 'test'])->name('test');
        Route::get('subir', [\App\Http\Controllers\Admin\Certificados\GeneradorCertificadoController::class, 'cargar'])->name('subir');

        // CRUD de certificados
        Route::get('index', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class, 'create'])->name('create');
        Route::post('store', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class, 'store'])->name('store');
        Route::get('data', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class, 'data'])->name('data');
        Route::get('descargarPlantilla', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class,'descargarPlantilla'])->name('descargar.plantilla');
        Route::get('{certificado}', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class, 'show'])->name('show');
        Route::get('{certificado}/edit', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class, 'edit'])->name('edit');
        Route::put('{certificado}', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class, 'update'])->name('update');
        Route::delete('{certificado}', [\App\Http\Controllers\Admin\Certificados\CertificadoController::class, 'destroy'])->name('destroy');
    });

    Route::get('/generar-carnets', [CardGenerationController::class, 'index'])->name('cards.generate.index');
    Route::post('/generar-carnets/pdf', [CardGenerationController::class, 'generatePdf'])->name('cards.generate.pdf');
    Route::post('/generar-carnets/derivar', [CardGenerationController::class, 'derive'])->name('cards.generate.derive');

    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/plugins', [PluginController::class, 'index'])->name('plugins.index');
    Route::post('/plugins', [PluginController::class, 'store'])->name('plugins.store');
    Route::post('/plugins/{plugin}/activar', [PluginController::class, 'activate'])->name('plugins.activate');
    Route::post('/plugins/{plugin}/desactivar', [PluginController::class, 'deactivate'])->name('plugins.deactivate');
    Route::delete('/plugins/{plugin}', [PluginController::class, 'destroy'])->name('plugins.destroy');

    Route::get('/conexion-api', [\App\Http\Controllers\Admin\ApiConnectionController::class, 'index'])->name('api-connection.index');
    Route::post('/conexion-api', [\App\Http\Controllers\Admin\ApiConnectionController::class, 'store'])->name('api-connection.store');

    Route::get('/paginas-publicas', [\App\Http\Controllers\Admin\SystemController::class, 'publicPages'])->name('public-pages');

    Route::resource('/asignaciones', \App\Http\Controllers\Admin\AcademicAssignmentController::class)
        ->names('assignments')
        ->parameters(['asignaciones' => 'assignment'])
        ->except(['show']);

    Route::resource('/carreras', \App\Http\Controllers\Admin\CareerController::class)
        ->names('careers')
        ->parameters(['carreras' => 'career'])
        ->except(['show']);

    Route::resource('/semestres', \App\Http\Controllers\Admin\SemesterController::class)
        ->names('semesters')
        ->parameters(['semestres' => 'semester'])
        ->except(['show']);
});

// Rutas API para Dropdowns
Route::prefix('api/dropdown')->name('api.dropdown.')->group(function () {
    Route::get('/semesters', [\App\Http\Controllers\DropdownController::class, 'getSemesters'])->name('semesters');
    Route::get('/gestions', [\App\Http\Controllers\DropdownController::class, 'getGestions'])->name('gestions');
    Route::get('/turnos', [\App\Http\Controllers\DropdownController::class, 'getTurnos'])->name('turnos');
    Route::get('/grupos', [\App\Http\Controllers\DropdownController::class, 'getGrupos'])->name('grupos');
});

app(PluginManager::class)->loadActiveRoutes();
