<?php

namespace App\Providers;

use App\Services\PluginManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PluginManager::class, fn () => new PluginManager());
    }

    public function boot(): void
    {
        Blade::directive('money', fn ($expression) => "<?php echo number_format($expression, 2); ?>");
    }
}
