<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Routing\Router;

class UserServiceProvider extends ServiceProvider
{

    protected $middleware = [
        'tocaan.user'     => 'TocaanUserMiddleware',
    ];
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMiddleware($this->app['router']);
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('User', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register the filters.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $name => $middleware) {
            $class = "Modules\\User\\Http\\Middleware\\{$middleware}";
            $router->aliasMiddleware($name, $class);
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('User', 'Config/config.php') => config_path('user.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('User', 'Config/config.php'), 'user'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/user');

        $sourcePath = module_path('User', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/user';
        }, \Config::get('view.paths')), [$sourcePath]), 'user');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/user');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'user');
        } else {
            $this->loadTranslationsFrom(module_path('User', 'Resources/lang'), 'user');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            $this->loadFactoriesFrom(module_path('User', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
