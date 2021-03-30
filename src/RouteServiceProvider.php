<?php

namespace GoldcarrotLaravel;

use Arr;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    public function __construct($app)
    {
        $this->namespace = config('routes.namespace');
        parent::__construct($app);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/routes.php' => config_path('routes.php'),
        ], 'config');

        parent::boot();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/routes.php',
            'routes'
        );

        parent::register();
    }

    private function mapRoutes($path, $namespace, $prefix): void
    {
        Route::namespace($namespace)
            ->middleware(config('routes.middleware'))
            ->prefix($prefix)
            ->group(base_path($path));
    }

    private function normalizePath(string $path): string
    {
        return trim(preg_replace('/[\/|\\\]+/', '/', $path), '/');
    }

    private function explodePath(string $path): Collection
    {
        return collect(explode('/', $this->normalizePath($path)));
    }

    private function normalizeNamespace(string $dirname): string
    {
        return $this
            ->explodePath($this->namespace . '\\' . $dirname)
            ->map(fn($namespace) => Str::ucfirst($namespace))
            ->join('\\');
    }

    private function normalizePrefix(string $dirname): string
    {
        return $this->normalizePath($dirname);
    }

    public function map(): void
    {
        $directories = Arr::wrap(config('routes.directories'));

        foreach ($directories as $directory) {
            $files = File::allFiles(base_path("routes\\$directory"));

            foreach ($files as $file) {
                $path = Str::replaceFirst(base_path(), null, $file->getRealPath());

                $dirname = Str::replaceFirst('routes\\', null, File::dirname($path));

                $this->mapRoutes(
                    $path,
                    $this->normalizeNamespace($dirname),
                    $this->normalizePrefix($dirname)
                );
            }
        }
    }
}
