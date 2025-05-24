<?php

namespace Modules\Apps\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Modules\Apps\Console\SetupNewUpdate;
use Modules\Area\Entities\Country;
use Setting;
use Modules\Area\Entities\CurrencyCode;

class AppsServiceProvider extends ServiceProvider
{
    protected $middleware = [
        'Apps' => [
            'site.activation'     => 'SiteActivationChecker',
        ],
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMiddleware($this->app['router']);
        $this->commands([
            SetupNewUpdate::class
        ]);
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Apps', 'Database/Migrations'));
        view()->composer('*', function ($view) {
            $view->with([
                // 'supported_countries' => Country::suportedCountries()->get(),
                'new_supported_currencies' => CurrencyCode::whereIn('id', Setting::get('supported_currencies'))->pluck('name','id')->toArray(),
            ]);
        });

    }



    /**
     * Register the filters.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\\Middleware\\{$middleware}";

                $router->aliasMiddleware($name, $class);
            }
        }
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
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Apps', 'Config/config.php') => config_path('apps.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Apps', 'Config/config.php'), 'apps'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/apps');

        $sourcePath = module_path('Apps', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/apps';
        }, \Config::get('view.paths')), [$sourcePath]), 'apps');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/apps');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'apps');
        } else {
            $this->loadTranslationsFrom(module_path('Apps', 'Resources/lang'), 'apps');
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
            $this->loadFactoriesFrom(module_path('Apps', 'Database/factories'));
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
