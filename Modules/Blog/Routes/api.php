<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function($api) {
   $api->get('/blog', 'TestController@info');
});
//Route::get('/blog', function (Request $request) {
//    // return $request->blog();
//})->middleware('auth:api');
