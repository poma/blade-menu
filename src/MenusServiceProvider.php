<?php

namespace Poma\BladeMenu;

use Illuminate\Support\ServiceProvider;

class MenusServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerNamespaces();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['menus'] = $this->app->share(function ($app) {
            return new Menu($app['view'], $app['config']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('menus');
    }

    /**
     * Register package's namespaces.
     *
     * @return void
     */
    protected function registerNamespaces()
    {
        #$this->loadViewsFrom(__DIR__ . '/src/views', 'menus');

        #$this->publishes([
        #    __DIR__ . '/src/config/config.php' => config_path('menus.php'),
        #], 'config');

        #$this->publishes([
        #    __DIR__ . '/src/views' => base_path('resources/views/vendor/pingpong/menus'),
        #], 'views');
    }
}
