<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('educard:info', function () {
    $this->info('EduCard Modular instalado correctamente.');
})->purpose('Muestra información básica del sistema');
