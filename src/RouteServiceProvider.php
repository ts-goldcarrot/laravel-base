<?php

namespace GoldcarrotLaravel;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace;
    protected array $baseMiddleware;
    protected string $routesDirectory = 'routes';
    protected string $basePath;

    public function __construct($app)
    {
        $this->namespace = config('routes.baseNamespace', 'App\Http\Controllers');
        $this->baseMiddleware = config('routes.middleware', ['web']);
        $this->basePath = config('routes.basePath');

        parent::__construct($app);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/routes.php' => config_path('routes.php'),
        ], 'config');

    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/routes.php',
            'routes'
        );
    }

    private function mapRoutes($path, $namespace, $prefix): void
    {
        Route::namespace($namespace)
            ->middleware($this->baseMiddleware)
            ->prefix($prefix)
            ->group(base_path($path));
    }

    private function normalizePath(string $path): string
    {
        $path = str_replace($this->basePath, null, $path);
        return trim(preg_replace('/[\/|\\\]+/', '/', $path), '/');
    }

    private function explodePath(string $path): Collection
    {
        return collect(explode('/', $this->normalizePath($path)));
    }

    private function normalizeNamespace(string $dirname): string
    {
        return $this
            ->explodePath($this->namespace . Str::replaceFirst($this->basePath, null, $dirname))
            ->map(fn($namespace) => Str::ucfirst($namespace))
            ->join('\\');
    }

    private function normalizePrefix(string $dirname): string
    {
        return $this->normalizePath($dirname);
    }

    public function map(): void
    {
        $files = File::allFiles(base_path($this->routesDirectory . '\\' . $this->basePath));

        foreach ($files as $file) {
            $path = Str::replaceFirst(base_path(), null, $file->getRealPath());

            $dirname = Str::replaceFirst($this->routesDirectory, null, File::dirname($path));

            $this->mapRoutes(
                $path,
                $this->normalizeNamespace($dirname),
                $this->normalizePrefix($dirname)
            );
        }
    }
}
