<?php

namespace Modules\Chat\Providers;

use Caffeinated\Modules\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(module_path('chat', 'Resources/Lang', 'app'), 'chat');
        $this->loadViewsFrom(module_path('chat', 'Resources/Views', 'app'), 'chat');
        $this->loadMigrationsFrom(module_path('chat', 'Database/Migrations', 'app'), 'chat');
        $this->loadConfigsFrom(module_path('chat', 'Config', 'app'));
        $this->loadFactoriesFrom(module_path('chat', 'Database/Factories', 'app'));
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
