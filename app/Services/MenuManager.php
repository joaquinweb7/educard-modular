<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class MenuManager
{
    public function items(): array
    {
        $allItems = $this->allSections();

        if (!auth()->check()) {
            return $allItems;
        }

        $user = auth()->user();

        return array_values(array_filter($allItems, function ($item) use ($user) {
            if (isset($item['route']) && $item['route'] === 'admin.dashboard') {
                return true; // Todos ven el inicio
            }
            return isset($item['route']) ? $user->hasPermission($item['route']) : false;
        }));
    }

    public function allSections(): array
    {
        $core = [
            ['title' => 'Inicio', 'route' => 'admin.dashboard', 'icon' => 'home', 'group' => 'Principal'],
            
            ['title' => 'Carreras', 'route' => 'admin.careers.index', 'icon' => 'book-open', 'group' => 'Gestión Académica'],
            ['title' => 'Semestres', 'route' => 'admin.semesters.index', 'icon' => 'calendar', 'group' => 'Gestión Académica'],
            
            ['title' => 'Solicitudes', 'route' => 'admin.requests.index', 'icon' => 'inbox', 'group' => 'Estudiantes'],
            ['title' => 'Estudiantes', 'route' => 'admin.students.index', 'icon' => 'users', 'group' => 'Estudiantes'],
            ['title' => 'Registro manual', 'route' => 'admin.students.create', 'icon' => 'user-plus', 'group' => 'Estudiantes'],
            ['title' => 'Registro masivo', 'route' => 'admin.students.import.create', 'icon' => 'upload', 'group' => 'Estudiantes'],

            ['title' => 'Lista', 'route' => 'admin.carnets.index', 'icon' => 'list', 'group' => 'Carnets Estudiantes'],
            ['title' => 'Crear', 'route' => 'admin.carnets.create', 'icon' => 'plus-circle', 'group' => 'Carnets Estudiantes'],
            ['title' => 'Actualizar fotos', 'route' => 'admin.photo-updates.index', 'icon' => 'camera', 'group' => 'Carnets Estudiantes'],
            ['title' => 'Plantillas', 'route' => 'admin.card-templates.index', 'icon' => 'layout-template', 'group' => 'Carnets Estudiantes'],

            ['title' => 'Lista', 'route' => 'admin.credenciales.index', 'icon' => 'list', 'group' => 'Credenciales Administrativas'],
            ['title' => 'Crear', 'route' => 'admin.credenciales.create', 'icon' => 'plus-circle', 'group' => 'Credenciales Administrativas'],

            ['title' => 'Lista de Certificados', 'route' => 'admin.certificados.index', 'icon' => 'award', 'group' => 'Certificados'],
            ['title' => 'Emitir Certificado', 'route' => 'admin.certificados.create', 'icon' => 'plus-circle', 'group' => 'Certificados'],
            ['title' => 'Carga Masiva', 'route' => 'admin.certificados.subir', 'icon' => 'upload', 'group' => 'Certificados'],
            ['title' => 'Cursos', 'route' => 'admin.certificados.curso.index', 'icon' => 'book', 'group' => 'Certificados'],
            ['title' => 'Plantillas', 'route' => 'admin.certificados.plantilla.index', 'icon' => 'layout-template', 'group' => 'Certificados'],
            ['title' => 'E-mails (SMTP)', 'route' => 'admin.certificados.smtp.index', 'icon' => 'mail', 'group' => 'Certificados'],
            ['title' => 'Probar Email', 'route' => 'admin.certificados.smtp.email-test', 'icon' => 'send', 'group' => 'Certificados'],
            
            ['title' => 'Asignaciones', 'route' => 'admin.assignments.index', 'icon' => 'list', 'group' => 'Producción de Carnets'],
            ['title' => 'Fuentes Tipográficas', 'route' => 'admin.fonts.index', 'icon' => 'type', 'group' => 'Producción de Carnets'],
            ['title' => 'Generar carnets', 'route' => 'admin.cards.generate.index', 'icon' => 'id-card', 'group' => 'Producción de Carnets'],
            ['title' => 'Carnets derivados', 'route' => 'admin.cards.derived.index', 'icon' => 'printer', 'group' => 'Producción de Carnets'],
            ['title' => 'Reportes', 'route' => 'admin.reports.index', 'icon' => 'bar-chart', 'group' => 'Producción de Carnets'],
            ['title' => 'Lista de usuarios', 'route' => 'admin.users.index', 'icon' => 'settings', 'group' => 'Usuarios'],
            ['title' => 'Crear Usuario', 'route' => 'admin.users.create', 'icon' => 'user-plus', 'group' => 'Usuarios'],
            
            ['title' => 'Páginas Públicas', 'route' => 'admin.public-pages', 'icon' => 'globe', 'group' => 'Sistema'],
            ['title' => 'Configuraciones', 'route' => 'admin.settings.index', 'icon' => 'settings', 'group' => 'Sistema'],
            ['title' => 'Conexión API REST', 'route' => 'admin.api-connection.index', 'icon' => 'link', 'group' => 'Sistema'],
            ['title' => 'Plugins', 'route' => 'admin.plugins.index', 'icon' => 'blocks', 'group' => 'Sistema'],
        ];

        $plugins = app(PluginManager::class)->menuItems();
        return array_merge($core, $plugins);
    }

    public function urlFor(array $item): string
    {
        return isset($item['route']) && Route::has($item['route']) ? route($item['route']) : '#';
    }
}
