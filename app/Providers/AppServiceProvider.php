<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->removeTelescopeSentinelMiddleware();
    }

    /**
     * Telescope 5.18+ prepends Sentinel, which returns 401 for local apps
     * accessed via ngrok or other trusted reverse proxies.
     */
    protected function removeTelescopeSentinelMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        $middleware = $router->getMiddlewareGroups()['telescope'] ?? [];

        $router->middlewareGroup('telescope', array_values(array_filter(
            $middleware,
            fn (string $name): bool => ! str_contains($name, 'SentinelMiddleware')
        )));
    }
}
