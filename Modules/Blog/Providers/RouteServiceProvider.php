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
    protected $namespace = 'Modules\Blog\Controllers\Http';

    protected $adminNamespace = 'Modules\Blog\Controllers\Admin';

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

        $this->mapHttpRoutes($api);

        $this->mapAdminRoutes($api);
        //
    }

    /**
     * Brief:http
     *
     * @param $api
     */
    protected function mapHttpRoutes($api)
    {
        $api->version('v1', function($api) {
            $api->group([
                'namespace'  => $this->namespace,
            ], function($api) {
                require module_path('blog', 'Routes/Http/route.php', 'app');
            });
        });
    }

    /**
     * Brief:admin
     *
     * @param $api
     */
    protected function mapAdminRoutes($api)
    {
        $api->version('v1', function($api) {
           $api->group([
               'namespace' => $this->adminNamespace,
               ], function($api) {
                    require module_path('blog', 'Routes/Admin/route.php', 'app');
           });
        });
    }

    /**
     * Define the "web" routes for the module.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @params $api
     *
     * @return void
     */
    protected function mapWebRoutes($api)
    {
        $api->version('v1', function($api) {
            $api->group([
                'middleware' => 'web',
                'namespace'  => $this->namespace,
            ], function ($api) {
                require module_path('blog', 'Routes/web.php', 'app');
            });
        });
    }

    /**
     * Define the "api" routes for the module.
     *
     * These routes are typically stateless.
     *
     * @param $api
     *
     * @return void
     */
    protected function mapApiRoutes($api)
    {
        $api->version('v1', function($api) {
            $api->group([
                'middleware' => 'auth:api',
                'namespace'  => $this->namespace,
                'prefix'     => 'api',
            ], function ($api) {
                require module_path('blog', 'Routes/api.php', 'app');
            });
        });
    }
}
