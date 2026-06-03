<?php

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use RuntimeException;
use ZipArchive;

class PluginManager
{
    public function activePlugins()
    {
        try {
            if (! DB::getSchemaBuilder()->hasTable('plugins')) {
                return collect();
            }

            return Plugin::where('status', 'active')->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function manifestPath(Plugin $plugin): string
    {
        return base_path(trim($plugin->path, '/').'/plugin.json');
    }

    public function readManifestFromPath(string $pluginPath): array
    {
        $manifestPath = rtrim($pluginPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'plugin.json';
        if (! File::exists($manifestPath)) {
            throw new RuntimeException('El plugin no tiene archivo plugin.json.');
        }

        $json = json_decode(File::get($manifestPath), true);
        if (! is_array($json) || empty($json['name'])) {
            throw new RuntimeException('El archivo plugin.json no es válido.');
        }

        return $json;
    }

    public function menuItems(): array
    {
        $items = [];

        foreach ($this->activePlugins() as $plugin) {
            try {
                $manifest = $this->readManifestFromPath(base_path(trim($plugin->path, '/')));
                foreach (($manifest['menu'] ?? []) as $menu) {
                    $items[] = [
                        'title' => $menu['title'] ?? $plugin->display_name,
                        'route' => $menu['route'] ?? null,
                        'icon' => $menu['icon'] ?? 'plugin',
                        'plugin' => $plugin->name,
                    ];
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $items;
    }

    public function loadActiveRoutes(): void
    {
        foreach ($this->activePlugins() as $plugin) {
            $routeFile = base_path(trim($plugin->path, '/').'/routes/admin.php');
            if (File::exists(base_path(trim($plugin->path, '/').'/resources/views'))) {
                View::addNamespace($plugin->name, base_path(trim($plugin->path, '/').'/resources/views'));
            }

            if (File::exists($routeFile)) {
                Route::middleware(['web', 'auth'])
                    ->prefix('admin/plugins')
                    ->name('admin.plugins.')
                    ->group($routeFile);
            }
        }
    }

    public function installFromZip(string $zipPath): Plugin
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('La extensión ZipArchive de PHP no está instalada.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('No se pudo abrir el archivo ZIP.');
        }

        $tempPath = storage_path('app/temp/plugin_'.uniqid());
        File::ensureDirectoryExists($tempPath);
        $zip->extractTo($tempPath);
        $zip->close();

        $pluginRoot = $this->detectPluginRoot($tempPath);
        $manifest = $this->readManifestFromPath($pluginRoot);
        $name = preg_replace('/[^A-Za-z0-9_\-]/', '', $manifest['name']);
        $targetPath = base_path('plugins/'.$name);

        if (File::exists($targetPath)) {
            File::deleteDirectory($targetPath);
        }

        File::ensureDirectoryExists(base_path('plugins'));
        File::copyDirectory($pluginRoot, $targetPath);
        File::deleteDirectory($tempPath);

        return Plugin::updateOrCreate(
            ['name' => $name],
            [
                'display_name' => $manifest['display_name'] ?? $name,
                'description' => $manifest['description'] ?? null,
                'version' => $manifest['version'] ?? '1.0.0',
                'author' => $manifest['author'] ?? null,
                'provider' => $manifest['provider'] ?? null,
                'path' => 'plugins/'.$name,
                'status' => 'installed',
                'installed_at' => now(),
            ]
        );
    }

    protected function detectPluginRoot(string $tempPath): string
    {
        if (File::exists($tempPath.'/plugin.json')) {
            return $tempPath;
        }

        foreach (File::directories($tempPath) as $directory) {
            if (File::exists($directory.'/plugin.json')) {
                return $directory;
            }
        }

        throw new RuntimeException('El ZIP no contiene un plugin válido con plugin.json.');
    }
}
