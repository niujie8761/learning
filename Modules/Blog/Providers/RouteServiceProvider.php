<?php

namespace Modules\Blog\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Modules\Blog\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the module.
     *
     * @return void
     */
    public function map()
    {
        $api = app('Dingo\Api\Routing\Router');
        $this->mapWebRoutes($api);

        $this->mapApiRoutes($api);

        //
    }

    /**
     * Define the "web" routes for the module.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes($api)
    {
        Route::group([
            'middleware' => 'web',
            'namespace'  => $this->namespace,
        ], function ($router) {
            require module_path('blog', 'Routes/web.php', 'app');
        });
    }

    /**
     * Define the "api" routes for the module.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes($api)
    {
        $api->version('v1', function ($api) {
            $api->group([
                'namespace' => $this->namespace,
            ], function ($api) {
                $routerPath = module_path('blog', 'Routes/api.php', 'app');
                if (file_exists($routerPath)) {
                    require $routerPath;
                }
            });
        });
       // Route::group([
       //     'middleware' => 'auth:api',
       //     'namespace'  => $this->namespace,
       //     'prefix'     => 'api',
       // ], function ($router) {
       //     require module_path('blog', 'Routes/api.php', 'app');
       // });
    }
}
