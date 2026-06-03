<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    public function publicPages()
    {
        $pages = [
            [
                'title' => 'Formulario de Solicitud de Carnet',
                'description' => 'Enlace público para que los estudiantes inicien el trámite de solicitud de carnet.',
                'url' => route('public.student-request.create'),
                'icon' => 'form-input' // o similar en lucide
            ],
            [
                'title' => 'Seguimiento de Trámite',
                'description' => 'Página donde los estudiantes pueden consultar el estado de su carnet.',
                'url' => route('public.student-request.track'),
                'icon' => 'search'
            ],
            [
                'title' => 'Verificación de Carnets',
                'description' => 'Portal público para verificar la autenticidad y estado de carnets de estudiantes.',
                'url' => route('verificar.carnet.index'),
                'icon' => 'check-circle'
            ],
            [
                'title' => 'Verificación de Certificados',
                'description' => 'Portal público para validar la autenticidad de los certificados emitidos.',
                'url' => route('verificar.certificado.index'),
                'icon' => 'award'
            ],
            [
                'title' => 'Login de Administrador',
                'description' => 'Acceso al sistema para el personal administrativo de Educard.',
                'url' => route('login'),
                'icon' => 'shield'
            ],
        ];

        return view('admin.public-pages', compact('pages'));
    }
}
