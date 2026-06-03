<?php

namespace Plugins\AcademicStructure\Providers;

use Illuminate\Support\ServiceProvider;

class AcademicServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Cargar migraciones del plugin
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}
