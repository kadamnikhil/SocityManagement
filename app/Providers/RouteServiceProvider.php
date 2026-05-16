<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/admin/dashboard';
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            $this->registerRouteBinding(app_path() . DS . 'Models', 'App\\Models\\');

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }


    /**
     * @return mixed
     */
    private function registerRouteBinding($path, $namespace)
    {

        $classes = scandir($path);

        foreach ($classes as $idx => $class) {
            if (strpos($class, '.') === 0) {
                continue;
            }
            if (is_dir($path . DS . $class)) {
                $this->registerRouteBinding($path . DS . $class, $namespace . $class . '\\');
                continue;
            }
            if (!ends_with($class, '.php')) {
                continue;
            }

            $key = snake_case(basename($class, '.php'));

            $classname = $namespace . basename($class, '.php');

            try {
                Route::bind($key, function ($value, $route = null) use ($classname, $key) {
                    return $this->getModel($classname, $value);
                });
            } catch (\Exception $e) {
                // Do nothing
            }
        }
    }

    /**
     * @param $model
     * @param $routeKey
     * @return mixed
     */
    private function getModel($model, $routeKey)
    {
        if (!is_numeric($routeKey)) {
            $routeKey = \Hashids::connection($model)->decode($routeKey)[0] ?? null;
        }
        $modelInstance = resolve($model);
        try {
            return $modelInstance->withTrashed()->findOrFail($routeKey);
        } catch (\BadMethodCallException $e) {
            return $modelInstance->findOrFail($routeKey);
        }
    }
}